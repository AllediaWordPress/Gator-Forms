/**
 * @version 1.0.0
 * @package Perfect Ajax Popup Contact Form
* @copyright © 2014 Perfect Web sp. z o.o., All rights reserved. http://www.perfect-web.co
* @license GNU General Public License http://www.gnu.org/licenses/gpl-3.0.html
* @author Piotr Moćko
*/

var pwebcontact_l10n = pwebcontact_l10n || {},
    pwebcontact_admin = {};

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

        $("#pweb-tabs").find(".nav-tab-active").removeClass("nav-tab-active");
        $(this).addClass("nav-tab-active");

        $tabs.find(".nav-tab-content-active").removeClass("nav-tab-content-active");
        $($(this).attr("href")).addClass("nav-tab-content-active");
    });
    
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
    });
    
    $("#pweb_params_layout input").change(function(e){
        if (this.checked) {
            $("#pweb-location-after .pweb-step-option").text( $("#"+$(this).attr("id")+"-lbl").text() );
        }
    });
    
    $("#pweb_options_position input").change(function(e){
        if (this.checked) {
            $("#pweb-location-place .pweb-step-option").text( $("#"+$(this).attr("id")+"-lbl").text() );
        }
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
    
    
    // Init fields
    $relatedFields.filter(":checked").trigger("change");
	//TODO init parent options for fields not dependend on releated fields
    //$tabs.find("fieldset.pweb-parent input.pweb-parent:checked").trigger("change");
    
    
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
    });
    
    
    
    
	// show/hide description
	$('.pweb-colapse a').click(function(e) {
		e.preventDefault();
		$(this).parent().find('.pweb-content').toggleClass('hide');
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
	
	// Warn about limited display of contact form
	$('.pweb-component-view').click(function(){
		if (this.value == 2)
			alert(Joomla.JText._('MOD_PWEBCONTACT_INTEGRATION_COMPONENT_VIEW'));
	});
	
	// Email HTML template preview 
	$('.pweb-email-preview').each(function(){
		$('<a>', {
			href: '#',
			html: '<i class="icon-eye"></i> '+Joomla.JText._('MOD_PWEBCONTACT_PREVIEW_BUTTON'),
			'class': 'pweb-email-preview-button'
		}).click(function(e) {
			e.preventDefault();
			
			var tmpl = $(this).parent().find('select').val();
			Joomla.popupWindow('../media/mod_pwebcontact/email_tmpl/'+tmpl+'.html', 'Preview', 700, 500, 1);
			
		}).appendTo($(this).parent());
	});
	
	// Add copy button to Analytics sample codes
	$('.pweb-analytics-code code').each(function(){
		$('<a>', {
			href: '#',
			html: '<i class="icon-copy"></i> '+Joomla.JText._('MOD_PWEBCONTACT_COPY_BUTTON'),
			'class': 'pweb-copy-code-button'
		}).click(function(e) {
			e.preventDefault();
			
			var code = $(this).prev().html(),
				field = $('#jform_params_oncomplete'),
				old_code = field.val(),
				pos = field.offset().top,
				maxPos = pos + field.outerHeight() - $(window).height();
			field.val((old_code ? old_code+'\r\n' : '') + code);
			if (pos > maxPos) pos = maxPos;
			$('html, body').animate({ scrollTop: pos }, 500);
		
		}).insertAfter($(this));
	});
	
	// Set module position
	$('.pweb-set-position').click(function(e){
		e.preventDefault();
		
		var $jform_position = $('#jform_position'),
			value = $(this).data('position');
		if ($jform_position.val() != value)
		{
			$jform_position.val(value);
			if ($jform_position.val() != value
				&& typeof $.fn.chosen === 'function' 
				&& $jform_position.prop('tagName').toLowerCase() == 'select' 
			) {
				var Chosen = $jform_position.data('chosen'),
					group = Chosen.add_unique_custom_group(),
	        		option = $('<option value="' + value + '">' + value + '</option>');
	        	$jform_position.append( group.append(option) )
	        		.val(value)
	        		.trigger('chosen:updated')
	        		.trigger('liszt:updated');
			}
		}
		
		alert(Joomla.JText._('MOD_PWEBCONTACT_POSITION_SET'));
	});
	
	// Assign module to all menu items
	$('.pweb-menuitems-all').click(function(e){
		e.preventDefault();
		
		$('#jform_assignment').val(0).trigger('change');
		if (typeof document.id === 'function')
			document.id('jform_assignment').fireEvent('change');
		
		if (typeof $.fn.chosen === 'function') {
			$('#jform_assignment').trigger('chosen:updated').trigger('liszt:updated');
		}
		
		alert(Joomla.JText._('MOD_PWEBCONTACT_ASSIGNED_TO_ALL_MENU_ITEMS'));
	});
    
    // Set duration of showing/hiding options
    setTimeout(function(){ pwebcontact_admin.duration = 400; }, 600);
});