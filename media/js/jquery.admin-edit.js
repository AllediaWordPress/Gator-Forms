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
	
    pwebcontact_admin.running_related = false;
    pwebcontact_admin.duration = 0;
    
    var $tabs = $("#pweb-tabs-content"),
        $adminBar = $("#pweb-adminbar");
    
    $(window).resize(function(){
        $tabs.css("padding-top", $(this).width() < 768 ? 0 : $adminBar.height());
    }).trigger("resize");
    
    // Initialize tooltips
    $(".pweb-has-tooltip").tooltip();
    
    // Tabs
    $("#pweb-tabs").find(".nav-tab").click(function(e){
        e.preventDefault();
        document.location.hash = $(this).attr("href");

        $("#pweb-tabs").find(".nav-tab-active").removeClass("nav-tab-active");
        $(this).addClass("nav-tab-active");

        $tabs.find(".nav-tab-content-active").removeClass("nav-tab-content-active");
        $($(this).attr("href")+"-content").addClass("nav-tab-content-active");
    });
    
    // Open last active tab
    if (document.location.hash) {
        $(document.location.hash).click();
    }
    
    // Location tabs
    $("#pweb-location-steps .pweb-location-step-tab").click(function(e){
        e.preventDefault();

        $("#pweb-location-steps .pweb-tab-active").removeClass("pweb-tab-active");
        $(this).addClass("pweb-tab-active");

        $("#pweb-location-options .pweb-location-options").removeClass("pweb-options-active");
        $("#"+$(this).attr("id")+"-options").addClass("pweb-options-active");

        var winWidth = $(window).width(),
            topOffset = $tabs.offset().top;
        
        if (winWidth > 600) {
            topOffset = topOffset - $("#wpadminbar").height();
            if (winWidth > 768) {
                topOffset = 0;
            }
        }
        
        if ($(window).scrollTop() > topOffset) {
            $("html,body").animate({ scrollTop: topOffset }, 500);
        }
    });
    
    // Show related options
    var $relatedFields = $tabs.find("fieldset.pweb-related input");
    $relatedFields.each(function(){
        $(this).data("relations", $(this).parent().attr("class").match(/pweb-related-[a-z\-]+/g) );
    }).change(function(e){
        
        if (pwebcontact_admin.running_related === true) return;
        
        pwebcontact_admin.running_related = true;
                
        var current_relations = $(this).data("relations"),
            relations = {
                name: [],
                strength: []
            };
        
        // find combinations of relations for checked options
        $relatedFields.filter(":checked").each(function(){
            var related = $.grep( $(this).data("relations"), function( n, i ){
                return $.inArray(n, current_relations) > -1;
            });
            
            if (related.length) {
                $.each(related, function( i, related_name ){
                    var index = $.inArray(related_name, relations.name);
                    if (index === -1) {
                        relations.name.push(related_name);
                        relations.strength.push(1);
                    } 
                    else {
                        relations.strength[index]++;
                    }
                });
            }
        });
        
        // find most relevant combination
        var strength = 0;
        $.each(relations.strength, function( i, s ){
            if (s > relations.strength[strength]) {
                strength = i;
            }
        });
        
        var selected = relations.name[strength];
        
        // change options which do not meet most relevan combination
        $relatedFields.filter(":checked").each(function(){
            if ($.inArray(selected, $(this).data("relations")) === -1) {
                $(this).closest("fieldset").find("."+selected+" input:not(:disabled)").first().click();
            }
        });
        
        // mark not related options
        $relatedFields.each(function(){
            var $option = $(this).parent();
            if ($option.hasClass(selected)) {
                // unmark related options
                $option.removeClass("pweb-not-related");
            }
            else {
                // mark not related options
                $option.addClass("pweb-not-related");
            }
        });
        
        pwebcontact_admin.running_related = false;
    }); 
    
    
    // display selected option in main settings of Location tab
    $("#pweb_params_handler input").change(function(e){
        if (this.checked) {
            var text = $("#"+$(this).attr("id")+"-lbl").text();
            if (this.value === "tab") {
                text = text +" - "+ $("#"+$("#pweb_params_toggler_position input:checked").attr("id")+"-lbl").text();
            }
            $("#pweb-location-before .pweb-step-option").text(text);
        }
    });
    
    $("#pweb_params_toggler_position input").change(function(e){
        if (this.checked) {
            var $field = $("#pweb_params_handler input:checked");
            if ($field.val() === "tab") {
                var text = $("#"+$field.attr("id")+"-lbl").text() 
                        + " - "
                        + $("#"+$(this).attr("id")+"-lbl").text();
                $("#pweb-location-before .pweb-step-option").text(text);
            }
        }
    });
    
    $("#pweb_params_effect input").change(function(e){
        if (this.checked) {
            $("#pweb-location-effect .pweb-step-option").text( $("#"+$(this).attr("id")+"-lbl").text() );
        }
    }).filter(":checked").each(function(){
        $("#pweb-location-effect .pweb-step-option").text( $("#"+$(this).attr("id")+"-lbl").text() );
    });
    
    $("#pweb_params_layout_type input").change(function(e){
        if (this.checked) {
            $("#pweb-location-after .pweb-step-option").text( $("#"+$(this).attr("id")+"-lbl").text() );
        }
    }).filter(":checked").each(function(){
        $("#pweb-location-after .pweb-step-option").text( $("#"+$(this).attr("id")+"-lbl").text() );
    });
    
    $("#pweb_params_position input").change(function(e){
        if (this.checked) {
            $("#pweb-location-place .pweb-step-option").text( $("#"+$(this).attr("id")+"-lbl").text() );
        }
    }).filter(":checked").each(function(){
        $("#pweb-location-place .pweb-step-option").text( $("#"+$(this).attr("id")+"-lbl").text() );
    });
    
    
    
    
    function hideChildOptions(parent_id, current_id) {
        
        // Find child elements of given parent
        var $elements = $tabs.find( "."+ parent_id );
        if (typeof current_id !== "undefined") {
            // Exclude elements that will be shown by current option
            $elements = $elements.filter(":not(."+current_id+")");
        }
        $elements.each(function(){
            var show = false,
                // Get all parents IDs of child element
                ids = $(this).data("parents");
            for (var id in ids) {
                // Skip id if it has parent id from beginning of function
                if (parent_id !== id) { //TODO maybe not required statement
                    var $field = $("#"+id);
                    if ($field.length && $field.is(":checked") && $field.closest(".pweb-field").css("dsiaply") !== "none") {
                        // Do not hide field if parent is checked and visible
                        show = true;
                        break;
                    }
                }
            }
            if (show === false && $(this).css("dsiaply") !== "none") {
                // hide element if it is not already hidden
                $(this).hide(pwebcontact_admin.duration);
                // propagate hiding of options that were shown by this element
                hideChildOptions( $(this).find("input.pweb-parent:checked").attr("id") );
            }
        });
    }
    
    // store array of parents for each child
    $tabs.find(".pweb-child").each(function(){
        $(this).data("parents", $(this).attr("class").match(/pweb_params_[a-z_]+/g) );
    })
    // hide all childs on page load
    .filter(".pweb-field").hide();
    
    // Show options for checked parent
    $tabs.find("fieldset.pweb-parent input").change(function(e){
        var current_id = $(this).attr("id");
            $options = $(this).closest("fieldset").find("input.pweb-parent");
        
        // Hide child options of unchecked options
        $options.filter(":not(:checked)").each(function(){
            hideChildOptions( $(this).attr("id"), current_id );
        });
        
        // Show child options for checked option (current)
        $options.filter(":checked").each(function(){
            var $elements = $tabs.find( "."+ $(this).attr("id") );
            $elements.show(pwebcontact_admin.duration);
            // Propagate displaly of child options
            $elements.find("input.pweb-parent:checked").trigger("change");
        });
    });
    
    var $inputFields = $tabs.find(".pweb-field-text input.pweb-parent, .pweb-field-color input.pweb-parent").change(function(e){
        $tabs.find( "."+ $(this).attr("id") )[ $(this).val() ? "show" : "hide" ](pwebcontact_admin.duration);
    });
    
    
    // Init fields
    $relatedFields.filter(":checked").first().trigger("change");
	// Init parent options for fields not dependend on releated fields
    $tabs.find("fieldset.pweb-parent").find("input:first").trigger("change");
    $inputFields.trigger("change");
    
    
    
    // Advanced options toggler
    $(".pweb-advanced-options-toggler").click(function(e){
        e.preventDefault();
        
        var $box = $(this).parent(),
            $icon = $(this).find("i");
        if ($box.hasClass("pweb-advanced-options-active")) {
            $box.removeClass("pweb-advanced-options-active");
            $(this).next().slideUp(400, function(){
                $icon.removeClass("dashicons-arrow-up").addClass("dashicons-arrow-down");
            });
        }
        else {
            $box.addClass("pweb-advanced-options-active");
            $(this).next().slideDown(400);
            $icon.removeClass("dashicons-arrow-down").addClass("dashicons-arrow-up");
        }
        
        $(this).blur();
    });
    
	
	// validate single email
	$('.pweb-filter-email').on('change', function() {
		if (this.value) {
			var regex=/^[a-zA-Z0-9.!#$%&‚Äô*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/;
			if (regex.test(this.value)) {
				$(this).removeClass('invalid');
			} else {
				$(this).addClass('invalid');
			}
		}
	});
	
	// validate coma separated emails
	$('.pweb-filter-emails').on('change', function() {
		if (this.value) {
			var regex=/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*\.\w{2,4}(,[ ]*\w+([\.-]?\w+)*@\w+([\.-]?\w+)*\.\w{2,4})*$/;
			if (regex.test(this.value)) {
				$(this).removeClass('invalid');
			} else {
				$(this).addClass('invalid');
			}
		}
	});
	
	// validate list of email recipients
	$('.pweb-filter-emailRecipients').on('change', function() {
		if (this.value) {
			var regex=/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*\.\w{2,4}[\|]{1}[^\r\n\|]+([\r]?\n\w+([\.-]?\w+)*@\w+([\.-]?\w+)*\.\w{2,4}[\|]{1}[^\r\n\|]+)*$/;
			if (regex.test(this.value)) {
				$(this).removeClass('invalid');
			} else {
				$(this).addClass('invalid');
			}
		}
	});
	
	// validate int
	$('.pweb-filter-int').on('change', function() {
		if (this.value && this.value !== 'auto') {
			var value = parseInt(this.value);
			this.value = isNaN(value) ? '' : value;
		}
	});
	
	// validate float
	$('.pweb-filter-float').on('change', function() {
		if (this.value && this.value !== 'auto') {
			var value = parseFloat(this.value);
			this.value = isNaN(value) ? '' : value;
		}
	});
	
	// validate unit
	$('.pweb-filter-unit').on('change', function() {
		var regex = /^\d+(px|em|ex|cm|mm|in|pt|pc|%){1}$/i;
		if (!this.value || this.value === 'auto' || regex.test(this.value)) {
			$(this).removeClass('invalid');
		} else {
			var value = parseInt(this.value);
			if (!isNaN(value)) {
				this.value = value+'px';
				$(this).removeClass('invalid');
			} else {
				$(this).addClass('invalid');
			}
		}
	});
	
	// validate color
	$('.pweb-filter-color').on('change', function() {
		var regex = /^(\w|#[0-9a-f]{3}|#[0-9a-f]{6}|rgb\(\d{1,3},[ ]?\d{1,3},[ ]?\d{1,3}\)|rgba\(\d{1,3},[ ]?\d{1,3},[ ]?\d{1,3},[ ]?[0]?\.\d{1}\))$/i;
		if (!this.value || regex.test(this.value)) {
			$(this).removeClass('invalid');
		} else {
			$(this).addClass('invalid');
		}
	});
	
	// validate url
	$('.pweb-filter-url').on('change', function() {
		var regexp = /^((http|https):){0,1}\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?$/i;
		if (!this.value || regex.test(this.value)) {
			$(this).removeClass('invalid');
		} else {
			$(this).addClass('invalid');
		}
	});
	
	// validate upload file size
	$('.pweb-filter-upload-max-size').on('change', function() {
		if (this.value) {
			var maxSize = pwebUploadMaxSize || 0,
				value = parseFloat(this.value);
			value = isNaN(value) ? 1 : value;
			if (value > maxSize) value = maxSize;
			this.value = value;
		}
	});
	
	// Validate upload files extensions
	$('.pweb-filter-ext').on('change', function(){
		this.value = this.value.toLowerCase().replace(/[^a-z0-9|?]+/g, '');
	});
    
    // Set duration of showing/hiding options
    setTimeout(function(){ pwebcontact_admin.duration = 400; }, 600);
    
    setTimeout(function(){ $("#wpbody").find(".updated, .error").hide(); }, 3000);
    
    
    // AdWords paste button
	$("#pweb_params_adwords_url_btn").click(function(e){
		e.preventDefault();
		var s = prompt("Paste Google AdWords/Goal Conversion tracking script"); //TODO translation
		if (s) {
			var u = s.match(/<img[^>]* src=["]([^"]+)"/i);
			if (u && typeof u[1] !== "undefined") {
                $("#pweb_params_adwords_url").val( u[1].replace(/&amp;/gi, "&") );
            }
		}
	});

    // AdCenter paste button
    $("#pweb_params_adcenter_url_btn").click(function(e){
		e.preventDefault();
		var s = prompt("Paste Microsoft adCenter conversion tracking script"); //TODO translation
		if (s) {
			var u = s.match(/<iframe[^>]* src=["]([^"]+)"/i);
			if (u && typeof u[1] !== "undefined") {
                $("#pweb_params_adcenter_url").val( u[1].replace(/&amp;/gi, "&") );
            }
		}
	});
    
    $("#pweb_params_bg_color").closest(".pweb-field-control").append( $("#pweb_params_bg_opacity") );
    
    //TODO select background image
});