/**
 * @version 1.0.0
 * @package Perfect Ajax Popup Contact Form
* @copyright © 2014 Perfect Web sp. z o.o., All rights reserved. http://www.perfect-web.co
* @license GNU General Public License http://www.gnu.org/licenses/gpl-3.0.html
* @author Piotr Moćko
*/

var pwebcontact_l10n = pwebcontact_l10n || {},
    pwebcontact_admin = pwebcontact_admin || {};

if (typeof jQuery !== "undefined") jQuery(document).ready(function($){
    
    pwebcontact_admin.item_index = 0;
    pwebcontact_admin.counter = 0; //TODO load counter when loading fields
    pwebcontact_admin.confirm = true;
    
    $("#pweb-tabs .nav-tab").get(1).click(); //TODO remove
    
    // prepare fields before save
    $("#pweb_form").on("submit", function(){
        
        // close options
        $("#pweb_fields_options_close").click();
        
        // change index in names of fields to match current order
        var counter = 0, last = 0;
        $("#pweb_fields_rows").find("input,textarea").not(":disabled").each(function(){
            if (typeof $(this).data("index") !== "undefined" && $(this).data("index") !== last) {
                last = $(this).data("index");
                counter++;
            }
            //console.log($(this).attr("name"), $(this).attr("name").replace("["+last+"]", "["+counter+"]"), $(this).val()); //TODO remove
            $(this).attr( "name", $(this).attr("name").replace("["+last+"]", "["+counter+"]") );
            
        });
        //return false;
    });
    
    
    // allow rows sorting
    $("#pweb_fields_rows").sortable({
        axis: "y",
        opacity: 0.7,
        handle: ".pweb-fields-sort-row",
        cancel: ".pweb-fields-cols",
        start: function( event, ui ) {
            ui.item.addClass("pweb-dragged");
        },
        stop: function( event, ui ) {
            ui.item.removeClass("pweb-dragged");
            // change order of sortable items in DOM
            $(this).sortable("refresh");
        }
    });
    
    
    // add new row
    $("#pweb_fields_add_row").click(function(){
        
        pwebcontact_admin.counter++;
        
        var $row = $('<div class="pweb-fields-row pweb-clearfix">'
                        +'<input type="hidden" name="fields['+pwebcontact_admin.counter+'][type]" value="row" data-index="'+pwebcontact_admin.counter+'">'
                        +'<div class="pweb-fields-sort-row pweb-has-tooltip" title="Drag to change order of rows">&varr;</div>'
                        +'<div class="pweb-fields-cols"></div>'
                        +'<div class="pweb-fields-add-col pweb-has-tooltip" title="Add column"><i class="icomoon-plus"></i></div>'
                    +'</div>');
        
        $row.data("cols", 0)
            .find(".pweb-fields-cols")
            .sortable({
            
            connectWith: ".pweb-fields-cols",
            cursor: "move",
            placeholder: "pweb-sortable-placeholder",
            tolerance: "pointer",
            receive: function( event, ui ) {
                
                ui.sender.removeClass("pweb-placeholder");
                
                if (ui.item.parent().children().length > 3) {
                    // replace dragged column in current row
                    var $replacedItem = ui.item.next();
                    if ($replacedItem.length === 0) $replacedItem = ui.item.prev();
                    // move column from current row to previous place
                    if (pwebcontact_admin.item_index === 0) {
                        $replacedItem.prependTo( ui.sender );
                    }
                    else {
                        $replacedItem.insertAfter( ui.sender.children().get(pwebcontact_admin.item_index-1) );
                    }
                }
                else if (ui.sender !== ui.item.parent()) {
                    // increase columns count in current row
                    var $row = ui.item.closest(".pweb-fields-row"),
                        cols = $row.data("cols");
                    $row.removeClass("pweb-fields-cols-" + cols.toString());
                    cols++;
                    $row.addClass("pweb-fields-cols-" + cols.toString()).data("cols", cols);
                    if (cols === 3) {
                        // disable droping of field types on add column button
                        $row.find(".pweb-fields-add-col").droppable("disable");
                    }
                    
                    // previous row
                    $row = ui.sender.closest(".pweb-fields-row");
                    cols = $row.data("cols");
                    if (cols > 1) {
                        // decrease columns count in previous row
                        $row.removeClass("pweb-fields-cols-" + cols.toString());
                        cols--;
                        $row.addClass("pweb-fields-cols-" + cols.toString()).data("cols", cols);
                        
                        if (cols < 3) {
                            // enable droping of field types on add column button
                            $row.find(".pweb-fields-add-col").droppable("enable");
                        }
                    }
                    else {
                        // remove row
                        $row.remove();
                    }
                }
            },
            start: function( event, ui ) {
                event.stopPropagation();
                // remember position of current column
                pwebcontact_admin.item_index = ui.item.index();
                ui.item.addClass("pweb-dragged");
                ui.item.parent().addClass("pweb-placeholder");
            },
            stop: function( event, ui ) {
                ui.item.removeClass("pweb-dragged");
                ui.item.parent().removeClass("pweb-placeholder");
                // change order of sortable items in DOM
                //$("#pweb_fields_rows").find(".pweb-fields-cols").sortable("refresh");
            }
        });
        
        // add new column button
        $row.find(".pweb-fields-add-col").click(function(){
            var cols = $row.data("cols");
            // add column only if not exited limit
            if (cols < 3) {
                
                // increase columns count
                $row.removeClass("pweb-fields-cols-" + cols.toString());
                cols++;
                $row.addClass("pweb-fields-cols-" + cols.toString()).data("cols", cols);
                
                pwebcontact_admin.counter++;
                
                // create new column
                var $col = $('<div class="pweb-fields-col">'
                                +'<input type="hidden" name="fields['+pwebcontact_admin.counter+'][type]" value="column" data-index="'+pwebcontact_admin.counter+'">'
                                +'<div class="pweb-fields-remove-col pweb-has-tooltip" title="Remove"><i class="icomoon-close"></i></div>'
                            +'</div>');
                
                // insert field by droping field type on column
                $col.droppable({
                    scope: "pweb_field_type",
                    activeClass: "pweb-droppable",
                    hoverClass: "pweb-droppable-hover",
                    drop: function(event, ui) {
                        // drop field type on column slot
                        dropField( ui.draggable, $(this) );
                    }
                });
                
                if (cols === 3) {
                    // disable droping of field types on add column button
                    $(this).droppable("disable");
                }
                
                // remove button
                $col.find(".pweb-fields-remove-col").click(function(){
                    if ($col.hasClass("pweb-has-field")) {
                        // remove field
                        if (pwebcontact_admin.confirm === false || confirm("Are you sure you want to remove this field?")) {
                            // check if field options are opened
                            var $field = $col.find(".pweb-custom-field-container");
                            if ($("#pweb_fields_options").data("parent") === $field.attr("id")) {
                                // close field options if opened
                                $("#pweb_fields_options_close").click();
                            }
                            // show field type if only one instance is allowed
                            if ($field.hasClass("pweb-custom-fields-single")) {
                                $("#pweb_field_type_" + $field.data("type")).show("slow");
                            }
                            // destroy DOM element
                            $field.remove();
                            // enable droping of field types on add column button
                            $col.removeClass("pweb-has-field").removeClass("pweb-custom-field-active").droppable("enable");
                            // enable field type of column
                            $col.find("input").get(0).disabled = false;
                        }
                    }
                    else {
                        
                        var $row = $col.closest(".pweb-fields-row"),
                            cols = $row.data("cols");
                        if (cols > 1) {
                            // decrease columns count in current row
                            $row.removeClass("pweb-fields-cols-" + cols.toString());
                            cols--;
                            $row.addClass("pweb-fields-cols-" + cols.toString()).data("cols", cols);
                            // destroy DOM element
                            $col.droppable("destroy").remove();
                            
                            if (cols < 3) {
                                // enable droping of field types on add column button
                                $row.find(".pweb-fields-add-col").droppable("enable");
                            }
                        }
                        else {
                            // remove whole row
                            $row.remove();
                        }
                    }
                }).tooltip();
                
                // Insert new column into row
                $col.appendTo( $row.find(".pweb-fields-cols") );
                // Refresh DOM elements
                $row.find(".pweb-fields-cols").sortable("refresh");
            }
        }).droppable({
            scope: "pweb_field_type",
            activeClass: "pweb-droppable",
            hoverClass: "pweb-droppable-hover",
            drop: function(event, ui) {
                $(this).click();
                // drop field type on add column button
                dropField( ui.draggable, $(this).prev().children().last() );
            }
        }).trigger("click").tooltip();
        
        // Insert new row and refresh DOM elements
        $("#pweb_fields_rows").append($row).sortable("refresh");
        
    }).droppable({
        scope: "pweb_field_type",
        activeClass: "pweb-droppable",
        hoverClass: "pweb-droppable-hover",
        drop: function(event, ui) {
            $(this).click();
            // drop field type on add row button
            dropField( ui.draggable, $(this).prev().children().last().find(".pweb-fields-cols").children().first() );
        }
    });
    
    
    $("#pweb_fields_add_row").trigger("click"); //TODO remove
    $("#pweb_fields_add_row").trigger("click"); //TODO remove
    $("#pweb_fields_add_row").trigger("click"); //TODO remove
    
    
    // Drag field types to insert field into column
    $("#pweb_fields_types .pweb-custom-fields-type").draggable({
        scope: "pweb_field_type",
        revert: true
    });
    
    // Display field label on sort list
    $("#pweb_fields_types .pweb-custom-field-label-input").change(function(){
        $( "#"+ $(this).attr("id").replace("_label", "_container") )
            .find(".pweb-custom-field-label span")
            .text( $(this).val() );
    });
    
    // Display option of single field
    $("#pweb_fields_types .pweb-custom-field-show-options").click(function(e){
        e.preventDefault();
        // Close previous field options
        $("#pweb_fields_options_close").click();
        // Hide field types
        $("#pweb_fields_types").hide();
        // Move fields option to modal
        var $parent = $(this).closest(".pweb-custom-field-container");
        $("#pweb_fields_options_content").append( $parent.find(".pweb-custom-field-options") );
        // Activate field slot
        $parent.closest(".pweb-fields-col").addClass("pweb-custom-field-active");
        // Hide all system fields options
        $("#pweb_fields_options .pweb-fields-options-content").hide();
        // Show constant options for single system fields
        $("#pweb_fields_options_content_" + $parent.data("type")).show();
        // Remeber feild options parent
        $("#pweb_fields_options").data("parent", $parent.attr("id") ).show();
    });
    
    // Hide options of single field
    $("#pweb_fields_options_close").click(function(e){
        e.preventDefault();
        var parent = $("#pweb_fields_options").data("parent");
        if (parent) {
            // Move fields options back to parent and deactivate field slot
            $("#"+parent)
                    .append( $("#pweb_fields_options_content").children() )
                    .closest(".pweb-fields-col").removeClass("pweb-custom-field-active");;
            // Forget parent and hide options
            $("#pweb_fields_options").data("parent", "").hide();
            // Display field types
            $("#pweb_fields_types").show();
        }
        $(this).blur();
    });
    
    //TODO generate alias for email template
    
    function dropField(source, target) {
        
        // get index of current column
        var inputIndex = target.find("input"),
            index = inputIndex.data("index");
        
        // disable field type of column
        inputIndex.get(0).disabled = true;
        
        // Change options IDs and names
        var $field = source.find(".pweb-custom-field-container").clone(true);
        $field.find("input,textarea").each(function(){
            this.disabled = false;
            $(this)
                .data("index", index)
                .attr("id", $(this).attr("id").replace(/_X_/g, "_"+index+"_") )
                .attr("name", $(this).attr("name").replace(/\[X\]/g, "["+index+"]") );
        });
        $field.find("fieldset").each(function(){
            this.disabled = false;
        });
        $field.find("label").each(function(){
            $(this)
                .attr("id", $(this).attr("id").replace(/_X_/g, "_"+index+"_") )
                .attr("for", $(this).attr("for").replace(/_X_/g, "_"+index+"_") );
        });
        $field.attr("id", "pweb_fields_"+index+"_container");

        // Disable adding new fields into this column and insert field details
        target.droppable("disable").addClass("pweb-has-field").prepend($field);

        // Display field options
        $field.find(".pweb-custom-field-show-options").click();
        
        // Hide field on fields types list if only one instance is allowed
        if (source.hasClass("pweb-custom-fields-single")) {
            source.hide();
        }
    }
    
    
    $("body").css("overflow-y", "scroll");
});