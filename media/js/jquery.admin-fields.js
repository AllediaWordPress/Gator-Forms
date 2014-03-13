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
    
    //new pwebFields($("#pweb_params_fields"), {});
    
    pwebcontact_admin.item_index = 0;
    pwebcontact_admin.counter = 0; //TODO remove
    
    $("#pweb-tabs .nav-tab").get(1).click(); //TODO remove
    
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
        }
    });
    
    
    // add new row
    $("#pweb_fields_add_row").click(function(){
        var $row = $('<div class="pweb-fields-row pweb-clearfix">'
                        +'<div class="pweb-fields-sort-row pweb-has-tooltip" title="Drag to change order of rows">&varr;</div>'
                        +'<div class="pweb-fields-cols"></div>'
                        +'<div class="pweb-fields-add-col pweb-has-tooltip" title="Add column"><i class="icomoon-plus"></i></div>'
                    +'</div>');
        
        $row.data("cols", 0).find(".pweb-fields-cols").sortable({
            connectWith: ".pweb-fields-cols",
            cursor: "move",
            placeholder: "pweb-sortable-placeholder",
            tolerance: "pointer",
            receive: function( event, ui ) {
                
                ui.sender.removeClass("pweb-placeholder");
                
                if (ui.item.parent().children().length > 3) {
                    var $replacedItem = ui.item.next();
                    if ($replacedItem.length === 0) $replacedItem = ui.item.prev();
                    if (pwebcontact_admin.item_index === 0) {
                        $replacedItem.prependTo( ui.sender );
                    }
                    else {
                        $replacedItem.insertAfter( ui.sender.children().get(pwebcontact_admin.item_index-1) );
                    }
                }
                else if (ui.sender !== ui.item.parent()) {
                    var $row = ui.item.closest(".pweb-fields-row"),
                        cols = $row.data("cols");
                    $row.removeClass("pweb-fields-cols-" + cols.toString());
                    cols++;
                    $row.addClass("pweb-fields-cols-" + cols.toString()).data("cols", cols);
                    
                    
                    $row = ui.sender.closest(".pweb-fields-row");
                    cols = $row.data("cols");
                    if (cols > 1) {
                        $row.removeClass("pweb-fields-cols-" + cols.toString());
                        cols--;
                        $row.addClass("pweb-fields-cols-" + cols.toString()).data("cols", cols);
                    }
                    else {
                        $row.remove();
                    }
                }
            },
            start: function( event, ui ) {
                event.stopPropagation();
                pwebcontact_admin.item_index = ui.item.index();
                ui.item.addClass("pweb-dragged");
                ui.item.parent().addClass("pweb-placeholder");
            },
            stop: function( event, ui ) {
                ui.item.removeClass("pweb-dragged");
                ui.item.parent().removeClass("pweb-placeholder");
            }
        });
        
        //$row.find(".pweb-fields-sort-row").tooltip();
        
        // add new column button
        $row.find(".pweb-fields-add-col").click(function(){
            var cols = $row.data("cols");
            // add column only if not exited limit
            if (cols < 3) {
                // change row class
                $row.removeClass("pweb-fields-cols-" + cols.toString());
                cols++;
                $row.addClass("pweb-fields-cols-" + cols.toString()).data("cols", cols);
                
                // create new column
                var $col = $('<div class="pweb-fields-col pweb-field-empty">'
                                +'<div class="pweb-fields-field"></div>'
                                +'<div class="pweb-fields-remove-col pweb-has-tooltip" title="Remove"><i class="icomoon-close"></i></div>'
                            +'</div>');
                
                pwebcontact_admin.counter++; //TODO remove
                $col.find(".pweb-fields-field").text(pwebcontact_admin.counter); //TODO remove
                
                // remove button
                $col.find(".pweb-fields-remove-col").click(function(){
                    
                    if (!$col.hasClass("pweb-field-empty")) {
                        // remove field
                        
                        //TODO confirmation
                    }
                    else {
                        var cols = $row.data("cols");
                        if (cols > 1) {
                            // remove single column
                            $row.removeClass("pweb-fields-cols-" + cols.toString());
                            cols--;
                            $row.addClass("pweb-fields-cols-" + cols.toString()).data("cols", cols);
                            $col.remove();
                        }
                        else {
                            // remove whole row
                            $row.remove();
                            //$("#pweb_fields_rows").sortable("refresh");
                        }
                    }
                }).tooltip();
                
                $col.appendTo( $row.find(".pweb-fields-cols") );
                
                //$row.find(".pweb-fields-cols").sortable("refresh");
            }
        }).trigger("click").tooltip();
        
        $("#pweb_fields_rows").append($row);//.sortable("refresh");
    });
    
    
    $("#pweb_fields_add_row").trigger("click"); //TODO remove
    $("#pweb_fields_add_row").trigger("click"); //TODO remove
    $("#pweb_fields_add_row").trigger("click"); //TODO remove
});