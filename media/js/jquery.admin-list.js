/**
* @version 3.2.0
* @package PWebContact
* @copyright © 2014 Perfect Web sp. z o.o., All rights reserved. http://www.perfect-web.co
* @license GNU General Public License http://www.gnu.org/licenses/gpl-3.0.html
* @author Piotr Moćko
*/

var pwebcontact_l10n = pwebcontact_l10n || {},
    pwebcontact_admin = {};

if (typeof jQuery !== "undefined") jQuery(document).ready(function($){
    
    // Toogle state action
    $(".pweb-action-toggle-state").click(function(e){
        
        var that = this;
        e.preventDefault();
        $(this).blur();
        
        $.ajax({
			url: $(this).data("action") + ($(this).data("state") ? 0 : 1),
			type: "get", 
			dataType: "json",
            beforeSend: function() {
                $(that).removeClass("pweb-text-success pweb-text-danger").find("i").get(0).className = "icomoon-spinner";
            }
		}).done(function(response, textStatus, jqXHR) {
			
			if (response && typeof response.success === "boolean") 
			{
				if (response.success === true) 
				{
                    // change state icon and color
                    $(that).data("state", response.state)
                            .addClass(response.state ? "pweb-text-success" : "pweb-text-danger")
                            .find("i").get(0).className = response.state ? "icomoon-checkmark-circle" : "icomoon-cancel-circle";
				}
				else 
                {
                    // restore state icon and color and alert response message
                    $(that).addClass(!response.state ? "pweb-text-success" : "pweb-text-danger")
                            .find("i").get(0).className = !response.state ? "icomoon-checkmark-circle" : "icomoon-cancel-circle";
                    alert(response.message);
                }
			}
		}).fail(function(jqXHR, textStatus, errorThrown) {
			
            // restore state icon and color
            $(that).addClass($(that).data("state") ? "pweb-text-success" : "pweb-text-danger")
                    .find("i").get(0).className = ( $(that).data("state") ? "icomoon-checkmark-circle" : "icomoon-cancel-circle" );
			alert('Request error. '+ jqXHR.status +' '+ errorThrown);
		});
    });
    
    // Delete action
    $(".pweb-action-delete").click(function(e){
        
        e.preventDefault();
        $(this).blur();
        
        $("#pweb-dialog-delete")
                .data("element", $(this)) // pass delete button handler to dialog
                .dialog("open")
                .find(".pweb-dialog-form-title").text( $(this).data("form-title") ) // set form title in dialog
                ;
    });
    
    // Delete dialog box
    $("#pweb-dialog-delete").dialog({
        autoOpen: false,
        resizable: false,
        modal: true,
        buttons: [
            { 
                text: pwebcontact_l10n.delete,
                click: function() {
                    
                    $(this).dialog("close");
                    var $element = $(this).data("element");
                    
                    $.ajax({
                        url: $element.data("action"),
                        type: "get", 
                        dataType: "json",
                        beforeSend: function() {
                            $element.find("i").get(0).className = "icomoon-spinner";
                        }
                    }).done(function(response, textStatus, jqXHR) {

                        if (response && typeof response.success === "boolean") 
                        {
                            if (response.success === true) 
                            {
                                // fade out form and remove it
                                $element.closest(".pweb-panel-box").fadeOut("slow", function() {
                                    $(this).remove();
                                });
                            }
                            else 
                            {
                                // restore delete button icon and alert response message
                                $element.find("i").get(0).className = "icomoon-remove2";
                                alert(response.message);
                            }
                        }
                    }).fail(function(jqXHR, textStatus, errorThrown) {

                        // restore delete button icon
                        $element.find("i").get(0).className = "icomoon-remove2";
                        alert('Request error. '+ jqXHR.status +' '+ errorThrown);
                    });
                }
            },
            {
                text: pwebcontact_l10n.cancel,
                click: function() {
                    $(this).dialog("close");
                }
            }
        ]
    });
    
    
    $("input.pweb-shortcode").click(function(e){
        e.preventDefault();
        $(this).select();
    }).on("keydown", function(e){
        e.preventDefault();
        $(this).select();
    });
});