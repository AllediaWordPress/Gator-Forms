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
    pwebcontact_admin.counter = 0;
    pwebcontact_admin.confirm = true;
    
    
    // save
    $("#pweb_form").on("submit", function(e){
        
        e.preventDefault();
        
        $("#pweb-save-button").get(0).disabled = true;
        
        // close options
        $("#pweb_fields_options_close").click();
        
        // change index in names of fields to match current order
        var counter = 0, last = 0;
        $("#pweb_fields_rows").find("input,textarea").not(":disabled").each(function(){
            if (typeof $(this).data("index") !== "undefined" && $(this).data("index") !== last) {
                last = $(this).data("index");
                counter++;
            }
            $(this).attr( "name", $(this).attr("name").replace("["+last+"]", "["+counter+"]") );
            
            // generate alias for email template
            if ($(this).hasClass("pweb-custom-field-alias") && !$(this).val()) {
                var $alias = $(this).closest(".pweb-custom-field-options").find("input.pweb-custom-field-label-input");
                if ($alias.length) {
                    var alias = $alias.val().replace(/[^a-z0-9\_]+/gi, '').toLowerCase();
                    $(this).val( alias ? alias : "field_"+counter );
                }
            }
        });
        
        // save with ajax
        $.ajax({
			url: $(this).attr("action")+"&ajax=1",
			type: "post", 
			dataType: "json",
            data: $(this).serialize(),
            beforeSend: function() {
                $("#pweb-save-status").addClass("pweb-saving").text("Saving..."); //TODO translation
            }
		}).always(function(){
            $("#pweb-save-button").get(0).disabled = false;
            $("#pweb-save-status").removeClass("pweb-saving")
            
        }).done(function(response, textStatus, jqXHR) {
			if (response && typeof response.success === "boolean") 
			{
                $("#pweb-save-status").text(
                        response.success === true ? "Saved on "+(new Date()).toLocaleTimeString() : "Error"); //TODO translation
			}
		}).fail(function(jqXHR, textStatus, errorThrown) {
            $("#pweb-save-status").text("Request error");
            alert("Request error. "+ jqXHR.status +" "+ errorThrown); //TODO translation
		});
        
        return false;
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
                        +'<div class="pweb-fields-sort-row pweb-has-tooltip" title="Drag to change order of rows">&varr;</div>' //TODO translation
                        +'<div class="pweb-fields-cols"></div>'
                        +'<div class="pweb-fields-add-col pweb-has-tooltip" title="Add column"><i class="icomoon-plus"></i></div>' //TODO translation
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
                                +'<div class="pweb-fields-remove-col pweb-has-tooltip" title="Remove"><i class="icomoon-close"></i></div>' //TODO translation
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
                        if (pwebcontact_admin.confirm === false || confirm("Are you sure you want to remove this field?")) { //TODO translation
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
    
    $("#pweb_params_upload_label").change(function(){
        $("#pweb_fields_rows").find(".pweb-custom-field-type-upload")
            .find(".pweb-custom-field-label span")
            .text( $(this).val() );
    });
    
    $("#pweb_params_button_send").change(function(){
        $("#pweb_fields_rows").find(".pweb-custom-field-type-button_send")
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
    
    function dropField(source, target, show_options) {
        
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
            $(this).attr("id", $(this).attr("id").replace(/_X_/g, "_"+index+"_") )
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
        if (typeof show_options === "undefined" || show_options !== false) {
            $field.find(".pweb-custom-field-show-options").click();
        }
        
        // Hide field on fields types list if only one instance is allowed
        if (source.hasClass("pweb-custom-fields-single")) {
            source.hide();
        }
        
        return $field;
    }
    
    function loadFields(fields, parse) {
        
        var $row = null, $cols = null, $addCol = null, rowCreated = false;
        
        if (typeof parse === "undefined" || parse !== false) {
            fields = $.parseJSON( fields );
        }
        
        $.each(fields, function(i, field) {

            if (field.type === "row") {
                // create new row
                $("#pweb_fields_add_row").trigger("click");
                $row = $("#pweb_fields_rows").children().last();
                $cols = $row.find(".pweb-fields-cols");
                $addCol = $row.find(".pweb-fields-add-col");
                rowCreated = true;
            }
            else {
                if (rowCreated === false) {
                    // add new column if not already created with new row
                    $addCol.trigger("click");
                }
                if (field.type !== "column") {
                    var $target = $cols.children().last();
                    // add field
                    dropField( $("#pweb_field_type_"+field.type), $target, false );
                    
                    // load field options
                    var index = $target.find(".pweb-custom-field-options input:first").data("index");
                    $.each(field, function(key, value) {
                        if (key === "required") {
                            if (value !== "0" && value !== "1") {
                                value = "0";
                            }
                            $target.find("#pweb_fields_"+index+"_"+key+"_"+value).get(0).checked = true;
                        }
                        else if (key !== "type") {
                            
                            $target.find("#pweb_fields_"+index+"_"+key).val( value.replace(/\\+/g, "\\") );
                            
                            if (key === "label") {
                                $target.find(".pweb-custom-field-label span").text(value);
                            }
                            else if (key === "upload") {
                                $("#pweb_params_upload_label").trigger("change");
                            }
                            else if (key === "button_send") {
                                $("#pweb_params_button_send").trigger("change");
                            }
                        }
                    });
                }
                rowCreated = false;
            }
        });
    }
    
    
    // load sample fields
    $("#pweb_load_fields").change(function(){
        if (this.selectedIndex) {
            var $rows = $("#pweb_fields_rows").children();
            // confirm old fields removal
            if ($rows.length === 0 || confirm("All current fields will be removed!")) { //TODO translation
                // hide options to remove them
                $("#pweb_fields_options_close").click();
                // remove rows with columns and fields without confirmation
                $rows.remove();
                // load sample fields
                loadFields( $(this).val() );
            }
            else {
                this.selectedIndex = 0;
            }
        }
    });
    
    
    // load fields
    loadFields( $("#pweb_params_fields").val() );
    $("#pweb_params_fields").get(0).disabled = true;
    
    //console.log($("#pweb_params_fields").val());
    
    $("body").css("overflow-y", "scroll");
});