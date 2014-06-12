/**
 * @version 1.0.0
 * @package Perfect Easy & Powerful Contact Form
 * @copyright © 2014 Perfect Web sp. z o.o., All rights reserved. http://www.perfect-web.co
 * @license GNU General Public License http://www.gnu.org/licenses/gpl-3.0.html
 * @author Piotr Moćko
 *
 * jQuery 1.8+
 */

var pwebBoxes = pwebBoxes || [],
    pwebcontact_l10n = pwebcontact_l10n || {};

(function($){
	
	pwebContact = function(options)
	{
		this.init(options);
	};
	
	pwebContact.prototype = (function() {
		
		// private members
		
		// protect captcha from disabling
		var captcha = {};
		
		// public members
		
    	return {
		
		constructor: pwebContact,
		
		defaults: 
		{
			id: '',
			selector: '#pwebcontact',
			selectorClass: '.pwebcontact',
			
			debug: false,
			basePath: '',
			baseUrl: '',
			ajaxUrl: '',
			reloadToken: false,
			
			openAuto: false,
			openDelay: 0,
			maxAutoOpen: 0,
			closeAuto: false,
			closeDelay: 0,
			closeOther: true,
			
			reset: 1,
			redirectURL: '',
			redirectDelay: 3,
			
			layout: 'slidebox',
			position: 'left',
			offsetPosition: 'top',
			tooltips: 3,
			msgPosition: 'after',
			msgCloseDelay: 10,
			rulesModal: false,
			
			togglerNameOpen: '',
			togglerNameClose: '',
			
			slideWidth: 0,
			slideDuration: 400,
			slideTransition: 'swing',

			accordionDuration: 500,
			accordionEaseIn: 'easeInBack',
			accordionEaseOut: 'easeOutBounce',
			
			modalBackdrop: true,
			modalClose: true,
			modalStyle: 'default',
			modalEffect: 'square',	//animation style: rotate, smooth, square, default (turn off genie effect)
			modalEffectDuration: 400,	//animation duration
			modalEaseIn: 'easeInCubic',
			modalEaseOut: 'easeOutCubic',
			
			cookieLifetime: 30,
			cookiePath: '/',
			cookieDomain: '',

			onLoad: function(){},
			onOpen: function(){},
			onClose: function(){},
			onComplete: function(data){},
			onError: function(data){},
			
			uploadAcceptFileTypes: /(\.|\/)(gif|jpe?g|png|doc?x|odt|txt|pdf|zip)$/i,
	        uploadMaxSize: 1048576, // 1 MB
			uploadFilesLimit: 5,
			uploadAutoStart: true,
			
			validatorRules: [],
			
			calendars: [],
			calendarFirstDay: 0
		},
		
		status: 	0, // 0=ready, 1=sending, 2=uploading, 3=sent, 4=reseted, 5=error
		hidden: 	true,
		timer: 		false,
		validator: 	false,
		tooltip: 	false,
		uploader: 	false,
		uploadQueue: 0,
		files:		[],

		
		init: function(options)
		{
			var that = this;
			
			this.options = $.extend({}, this.defaults, options);
			
			this.options.baseUrl = document.location.protocol +'//'+ document.location.host + this.options.basePath + '/';
			this.options.ajaxUrl = this.options.baseUrl + this.options.ajaxUrl;
			
			this.options.selector = this.options.selector + this.options.id;
			this.options.selectorClass = this.options.selectorClass + this.options.id;
			
			this.element 		= $(this.options.selector);
			this.Msg 			= $(this.options.selector+'_msg');
			this.Form 			= $(this.options.selector+'_form');
			this.ButtonSend		= $(this.options.selector+'_send');
			this.Toggler		= $(this.options.selector+'_toggler');
			this.Box 			= $(this.options.selector+'_box');
			this.Container 		= $(this.options.selector+'_container');
			this.Token 			= $(this.options.selector+'_token');
			
			// reset fields
			this.Form[0].reset();
			
			// enable captcha
			captcha[this.options.id] = { enabled: 0, status: -1 };
			if (captcha[this.options.id].enabled === 0) { 
				captcha[this.options.id].enabled = this.Form.find('.pweb-captcha').length > 0;
			}
			
			// disable submitting of form
			this.Form.submit(function(e){
				e.preventDefault();
			});
			
			if (this.options.layout == 'slidebox')
			{
				// move box
				this.element.appendTo(document.body);
				
				// init slide box options
				this.options.togglerSlide 	= this.Box.hasClass('pweb-toggler-slide');
				this.options.togglerHidden 	= this.Box.hasClass('pweb-toggler-hidden');
				
				// initialize box position and size
				this.options.slidePos 		= this.element.css('position');
				if (!this.options.slideWidth)
					this.options.slideWidth = parseInt(this.Box.css('max-width'));
				
				this.Box.css('width', this.options.slideWidth);
				if (this.options.position == 'left' || this.options.position == 'right')
				{
					this.Box.css(this.options.position, -this.options.slideWidth);
					
					this.options.togglerWidth 	= this.options.togglerSlide ? this.Toggler.outerWidth() : 0;
					this.options.togglerHeight 	= this.options.togglerSlide ? parseInt(this.Box.css('top')) : 0;
					this.options.slideOffset 	= parseInt(this.element.css('top'));
					
					// set toggler position
					if (this.options.togglerSlide) 
						this.Toggler.css(this.options.position == 'left' ? 'right' : 'left', -this.Toggler.outerWidth());
				}
				else
				{
					this.Box.css(this.options.position, -this.Box.height());
					
					this.options.togglerWidth 	= 0;
					this.options.togglerHeight 	= this.options.togglerSlide ? this.Toggler.outerHeight() : 0;
					this.options.slideOffset 	= 0;
					
					// set toggler position
					if (this.options.togglerSlide) 
						this.Toggler.css(this.options.position == 'top' ? 'bottom' : 'top', -this.Toggler.outerHeight());
				}
				this.Box.addClass('pweb-closed').removeClass('pweb-init');
				
				// hidden toggler
				if (this.options.togglerHidden) {
					this.Toggler.fadeOut(0).removeClass('pweb-closed').addClass('pweb-opened');
					if (this.options.togglerNameClose) {
						this.Toggler.find('.pweb-text').text(this.options.togglerNameClose);
					}
				}
				
				// close other boxes
				if (this.options.closeOther) pwebBoxes.push(this);
				
				// is Firefox
				this.isFF = navigator.userAgent.indexOf('Gecko') != -1;
				
				// change top offset to fit form into page
				$(window).scroll(function(e){
					
					if (!that.hidden && that.element.css('position') == 'absolute' && that.options.slidePos == 'fixed')
					{
						var offset = $(this).scrollTop();
						if (that.options.position == 'bottom')
						{
							offset = offset + $(this).height();
							if (offset > that.element.offset().top) {
								if (that.isFF)
									that.element.css('top', offset);
								else
									that.element.stop().animate({top: offset}, 250, 'linear');
							}
						}
						else
						{
							offset = offset + that.options.slideOffset;
							if (offset < that.element.offset().top) {
								if (that.isFF)
									that.element.css('top', offset);
								else
									that.element.stop().animate({top: offset}, 250, 'linear');
							}
						}
					}
				});
			}
			else if (this.options.layout == 'modal')
			{
				// move box
				this.element.appendTo(document.body);
				
				if (!this.initModal()) return false;
			}
			else if (this.options.layout == 'accordion') 
			{
				// accordion
				this.initAccordion();
			}
			
			// load event
			this.options.onLoad.apply(this);
			
			var openOnLoad = false;
			
			// load fields values from URL and open
			if (document.location.hash.indexOf(this.options.selector+':') !== -1) 
			{
				var data = document.location.hash.replace(this.options.selector+':', '');
				// enable open on page load
				if (data.indexOf('open') === 0 && (typeof data[4] === 'undefined' || data[4] == ':')) {
					data = data.replace(/open(:)?/i, '');
					openOnLoad = true;
				}
				// load fields values
				if (data) this.preloadFields(data);
			}
			
			// initialize optional addons
			if (this.options.tooltips)
				this.initTooltips();
			
			if (this.Form.find('.pweb-uploader').length) 
				this.initUploader();
			
			this.initTextareaCounters();
			this.initChosen();
			this.initCalendar();
			this.initHiddenFields();
			
			this.initValidator();
			
			if (this.Box.hasClass('pweb-labels-over')) 
				this.initLabelsOverFields();
			
			if (this.options.rulesModal)
				this.initModalRules();
			
			// assign buttons events
			this.ButtonSend.click(function(){
				that.submitForm();
			});
			if (this.options.reset == 3) {
				this.ButtonReset = $(this.options.selector+'_reset');
				this.ButtonReset.click(function(){
					$(this).hide();
					that.resetForm();
				});
			}
			
			if (this.options.layout != 'static')
			{
				// clear toggler name if is hidden
				if (this.options.togglerNameClose)
					this.options.togglerNameOpen = this.Toggler.find('.pweb-text').text();
				
				// apply events for opening form with custom links			
				$(this.options.selectorClass+'_toggler').click(function(e){
					e.preventDefault();
					that.toggleForm(-1, -1, this, e);
				});
				
				if (openOnLoad)
				{
					this.autoPopupOnPageLoad();
				}
				else if (this.options.openAuto) 
				{
					// auto-popup limit
					if (this.options.maxAutoOpen > 0) {
						if (!this.initAutoPopupCookie()) this.options.openAuto = false;
					}
					
					// auto-popup
					switch (this.options.openAuto) {
						case 1: 
							this.autoPopupOnPageLoad();
							break;
						case 2: 
							this.autoPopupOnPageScroll();
							break;
						case 3: 
							this.autoPopupOnPageExit();
					}
				}
			}
			
			// check if cookies are enabled
			if (typeof navigator.cookieEnabled !== 'undefined' && navigator.cookieEnabled === false)
				this.displayMsg(pwebcontact_l10n.form.COOKIES_ERR, 'error');
			else
				this.displayMsg('', '');
			
			return this;
		},
		
		initModal: function()
		{
			var that = this;
			
			if (typeof $.fn.modal === 'function')
			{
				this.Modal = $(this.options.selector+'_modal');
				
				// body class for opened modal
				this.options.modalClass = 'pwebcontact'+this.options.id+'_modal-open pweb-modal-open pweb-modal-' + this.options.modalStyle;
				
				// initailize Bootstrap Modal
				this.Modal.appendTo(document.body).modal({
					show: false, 
					//disable closing with esc key
					keyboard: false,
					// to disable closing backdrop set: static
					backdrop: !this.options.modalClose && this.options.modalBackdrop ? 'static' : this.options.modalBackdrop
				}).on('hidden', function(e) {
					// do not close if clicked on form
					e.stopPropagation();
					if (e.target !== e.currentTarget) return;
					
					// close form
					that.toggleForm(0);
					// remove opened class from body
					$(document.body).removeClass(that.options.modalClass);
				}).on('show', function(e) {
					// do not trigger if tooltip is the target
					e.stopPropagation();
					if (e.target !== e.currentTarget) return;
					
					// add opened class to body
					$(document.body).addClass(that.options.modalClass);
				}).click(function(e) {
					// disable click on backdrop
					if (e.target !== e.currentTarget || !that.options.modalClose) return;
					// close form
					that.toggleForm(0);
				});
                
                // apply effect for animation if there is chosen one
				if (this.options.modalEffect !== 'default'){
					this.initGenie();
				}
				
				return true;
			}
			else if (this.options.debug) this.debug('Bootstrap Modal Plugin is not loaded');
			
			return false;
		},
		
		initModalRules: function()
		{
			var that = this,
				links = this.Form.find('.pweb-field-checkbox a');
			
			if (!links.length) return;
			
			if (typeof $.fn.modal === 'function')
			{
				links.click(function(e){
					var href = $(this).attr('href');
					if (href.indexOf('//') !== -1 && href.indexOf('//') <= 6 && href.indexOf(document.location.host) === -1) return;
					
					e.preventDefault();
					
					var opt = {};
					if (that.options.layout == 'modal') 
						opt.backdrop = 0;
					
					$('<div class="pweb-modal modal hide fade'+ (that.Box.hasClass('pweb-rtl') ? ' pweb-rtl' : '') +'">'
						+'<button type="button" class="pweb-button-close" data-dismiss="modal" aria-hidden="true">&times;</button>'
						+'<div class="modal-body pweb-progress"></div>'
					 +'</div>'
					)
					.appendTo(document.body)
					.modal(opt)
					.on('hidden', function() {
						$(this).remove();
					})
					.find('.modal-body').load(href, function(){
						$(this).removeClass('pweb-progress');
					});
				});
				
				return true;
			}
			else if (this.options.debug) this.debug('Bootstrap Modal Plugin is not loaded');
			
			return false;
		},
		
		initGenie: function()
		{
		    var that = this,
				bgColorClass = this.Box.attr('class').match(/pweb-bg-[a-z]+/i);
			
			// transfer effect classes
			this.options.modalGenieClass = 'pweb-genie pweb-'+this.options.modalEffect+'-'+(this.options.position !== 'static' ? this.options.position : 'bottom')
				+' pwebcontact'+this.options.id+'-genie'
				+(this.Box.hasClass('pweb-radius') ? ' pweb-radius' : '')
				+(this.Box.hasClass('pweb-shadow') ? ' pweb-shadow' : '')
				+(bgColorClass ? ' '+bgColorClass[0] : '')
			
			// effect easing
		    if (this.options.modalEffect === 'smooth'){
		    	this.options.modalEaseIn = 'easeInQuart';
				this.options.modalEaseOut = 'easeOutQuart';
		    }
		    else if (this.options.modalEffect === 'rotate'){
				this.options.modalEaseIn = 'easeInQuint';
				this.options.modalEaseOut = 'easeOutQuint';
		    }
			
			// modal window events
			this.Modal.on('show', function(e) {
				// do not trigger if tooltip is the target
				e.stopPropagation();
				if (e.target !== e.currentTarget) return;
				
				if (typeof that.eventSource !== 'undefined' && $(that.eventSource).length) {
					// hide container
					that.Container.css({ visibility: 'hidden' });
				}
			}).on('shown', function(e) {
				// do not trigger if tooltip is the target
				e.stopPropagation();
				if (e.target !== e.currentTarget) return;
				
				if (typeof that.eventSource !== 'undefined' && $(that.eventSource).length) {
					// run transfer effect
					$(that.eventSource).trigger('modalOpen');
				}
			});
		    
		    // show window
		    $(this.options.selectorClass+'_toggler').on('modalOpen', function() {
		    	$(this).effect({
					effect: 'transfer',
		            to: that.Container,
		            duration: that.options.modalEffectDuration,
		            easing: that.options.modalEaseIn,
		            className: 'pweb-genie-show '+that.options.modalGenieClass,
					complete: function() {
		                that.Container.css({ visibility: 'visible' });
		            }
		        });
		    });
			
		    // hide window
		    this.Container.on('modalClose', function() {
		        $(this).css({ visibility: 'hidden' }).effect({
					effect: 'transfer',
		            to: $(that.eventSource),
		            duration: that.options.modalEffectDuration,
		            easing: that.options.modalEaseOut,
		            className: 'pweb-genie-hide '+that.options.modalGenieClass,
					complete: function() {
		                that.Modal.modal('hide');
		            }
		        });
		    });
		},
		
		initAccordion: function()
		{
	        var that = this;
	        
	        $(this.options.selectorClass+'_toggler').on('openAccordion', function(){
	            that.Box.slideDown({
	                duration: that.options.accordionDuration,
	                easing: that.options.accordionEaseOut,
					complete: function() {
						// scroll page that toggler and top of form would be visible
						var winHeight = $(window).height(),
							eleHeight = that.element.outerHeight(),
							eleTop = that.element.offset().top;
						if (eleTop + eleHeight > $(window).scrollTop() + winHeight) {
							if (eleHeight < winHeight) {
								eleTop = eleTop + eleHeight - winHeight;
							}
							$('html,body').animate({ scrollTop: parseInt(eleTop) }, 500);
						}
					}
	            });
	        });  
	        
	        $(this.options.selectorClass+'_toggler').on('closeAccordion', function(){
	            that.Box.slideUp({
	                duration: that.options.accordionDuration,
	                easing: that.options.accordionEaseIn
	            });
	        });
			
			this.Box.css('display', 'none').removeClass('pweb-init');
	    },
		
		initTooltips: function()
		{
			if (typeof $.fn.tooltip === 'function')
			{
				// init tooltips
				this.tooltip = true;
				// input fields with focus
				this.Form.find('input.pweb-input.pweb-tooltip,select.pweb-tooltip,textarea.pweb-tooltip').tooltip({
					trigger: this.options.tooltips == 2 ? 'manual' : 'focus',
					html: false
				});
				// fields without focus, only hover
				this.Form.find('fieldset.pweb-tooltip,.pweb-field-checkbox label.pweb-tooltip,.fileinput-button.pweb-tooltip').tooltip({
					trigger: this.options.tooltips == 2 ? 'manual' : 'hover',
					html: false
				});
				// do not trigger if modal is the target
				this.Form.find('.pweb-tooltip')
				.on('show', function(e){
					e.stopPropagation();
					if (e.target !== e.currentTarget) return;
				}).on('shown', function(e) {
					e.stopPropagation();
					if (e.target !== e.currentTarget) return;
				}).on('hide', function(e) {
					e.stopPropagation();
					if (e.target !== e.currentTarget) return;
				}).on('hidden', function(e) {
					e.stopPropagation();
					if (e.target !== e.currentTarget) return;
				});
				
				return true;
			}
			else if (this.options.debug) this.debug('Bootstrap Tooltip Plugin is not loaded');
			
			return false;
		},
		
		initCalendar: function() 
		{
            if (typeof $.fn.datepicker !== 'function')
				return false;
			
			if (this.options.calendars.length)
			{
				var that = this;
				
				// init calendars
				$.each(this.options.calendars, function(key, calendar) {
					
					$(that.options.selector+'_field-'+calendar.id).datepicker({
                        dateFormat : typeof calendar.format !== 'undefined' ? calendar.format : null,
                        constrainInput: true,
                        firstDay: 1,
                        isRTL: $(that.options.selector).hasClass('pweb-rtl'),
                        onSelect: function(dateText, inst) {
                            this.focus();
                        }
                    });
				});
				
				// calendar open button
				this.Form.find('.pweb-calendar-btn').click(function(){
					// change calendar position when opening
					//if (that.element.css('position') == 'fixed' || that.options.layout == 'modal')
						//$('div.calendar:last').css('position', 'fixed');
					// focus calendar field to show tooltip
					$(this).prev().focus();
				});
			}
			
			return true;
		},
		
		initChosen: function()
		{
			return; //TODO chosen
			
			if (typeof $.fn.chosen === 'function')
			{
				this.Form.find('select').chosen({
					disable_search_threshold: 50,
					allow_single_deselect: true
				});
				
				return true;
			}
			else if (this.options.debug) this.debug('jQuery Chosen Plugin is not loaded');
			
			return false;
		},
		
		initLabelsOverFields: function()
		{
			// text fields
			this.Form.find('input.pweb-input,textarea')
			.focus(function(){
				// hide label on focus
				$('#'+$(this).attr('id')+'-lbl').parent().hide();
			}).blur(function(){
				// show label if field empty and lost focus
				if (!this.value) 
					$('#'+$(this).attr('id')+'-lbl').parent().show();
			}).each(function(){
				// hide label if field not empty
				if (this.value) 
					$('#'+$(this).attr('id')+'-lbl').parent().hide();
			});
			
			// select fields
			this.Form.find('select')
			.focus(function(){
				// hide label on focus
				$('#'+$(this).attr('id')+'-lbl').parent().hide();
				// show select list options
				$(this).removeClass('pweb-blank').children(':first').text($(this).data('default-option'));
			}).blur(function(){
				// if field empty and lost focus
				if ($(this).val() == '' || $(this).val() == null) {
					// hide select list options
					$(this).addClass('pweb-blank').children(':first').text(' ');
					// show label
					$('#'+$(this).attr('id')+'-lbl').parent().show();
				}
			}).each(function(){
				var option = $(this).children(':first');
				$(this).data('default-option', option.text());
				// if field empty
				if ($(this).val() == '' || $(this).val() == null) {
					// hide select list options
					$(this).addClass('pweb-blank');
					option.text(' ');
				}
				else
					// hide label
					$('#'+$(this).attr('id')+'-lbl').parent().hide();
			});
			
			return true;
		},
		
		initTextareaCounters: function()
		{
			// textarea fields characters limit
			this.Form.find('textarea[maxlength]').keyup(function(){
				var limit = parseInt($(this).attr('maxlength'));
				var text = $(this).val();
				if (text.length >= limit) {
					text = text.substring(0, limit);
					$(this).val(text);
				}
				$('#'+$(this).attr('id')+'-limit').text(limit - text.length);
			}).trigger('keyup');
			
			return true;
		},
		
		initHiddenFields: function()
		{
			// Page title
			$('<input/>', {
				type: 'hidden',
				name: 'title',
				value: document.title
			}).appendTo(this.Form);
			
			// Page URL
			$('<input/>', {
				type: 'hidden',
				name: 'url',
				value: document.location.href
			}).appendTo(this.Form);
			
			// Screen resolution
			$('<input/>', {
				type: 'hidden',
				name: 'screen_resolution',
				value: screen.width +'x'+ screen.height
			}).appendTo(this.Form);
			
			// Debug on server side
			if (this.options.debug) {
				$('<input/>', {
					type: 'hidden',
					name: 'debug',
					value: 1
				}).appendTo(this.Form);
			}
			
			return true;
		},
		
		initUploader: function()
		{
			var that = this;
			
			if (typeof $.fn.fileupload !== 'function')
			{
				if (this.options.debug) 
					this.debug('jQuery File Upload Plugin is not loaded');
				return false;
			}
						
			this.uploader = $(this.options.selector+'_uploader_container').fileupload({
				url: this.options.ajaxUrl + 'uploader',
				type: 'POST',
				dataType: 'json',
				dataFilter: this.ajaxResponseDataFilter,
				accepts: "application/json; charset=utf-8",
				headers: { Accept: "application/json; charset=utf-8" },
				converters: {
					'text json': function(result) {
						result = $.parseJSON(result);
						
						if (typeof result.data !== 'undefined') 
							result = result.data;
						
						return result;
					},
					'iframe json': function(iframe) {
						iframe = $.parseJSON(iframe);
						
						if (typeof iframe.data !== 'undefined') 
							iframe = iframe.data;
						
						return iframe;
					}
				},
				formData: function (form) {
					return $.merge(form.serializeArray(), [
						{ name: 'format', 	value: 'json' },
						{ name: 'mid', 		value: that.options.id },
						{ name: 'ignoreMessages', value: true }
					]);
				},
				autoUpload: this.options.uploadAutoStart === true,
				acceptFileTypes: this.options.uploadAcceptFileTypes,
				maxFileSize: this.options.uploadMaxSize,
				maxNumberOfFiles: this.options.uploadFilesLimit,
				getNumberOfFiles: function() {
					return this.filesContainer.children('.pweb-upload-success, .pweb-upload-ready').length;
				},
				messages: {
					uploadedBytes: pwebcontact_l10n.upload.BYTES_ERR,
					maxNumberOfFiles: pwebcontact_l10n.upload.LIMIT_ERR,
					acceptFileTypes: pwebcontact_l10n.upload.TYPE_ERR,
					maxFileSize: pwebcontact_l10n.upload.SIZE_ERR
				},
				dropZone: this.Box,
				pasteZone: null,
				uploadTemplateId: null,
				downloadTemplateId: null,
				uploadTemplate: function(o) {
					var rows = $();
					$.each(o.files, function(index, file) {
						
						var row = that.uploader.find('.templates .template-upload').clone();
						row.find('.name').text(file.name);
						row.find('.cancel').bind('click', function(){
							if (row.hasClass('pweb-upload-ready'))
								that.uploadQueue--;
							// clear msg if there was displayed an error msg
							if (that.status == 0) that.displayMsg('', '');
						});
						
						rows = rows.add(row);
					});
					return rows;
				},
				downloadTemplate: function(o) {
					var rows = $();
					$.each(o.files, function(index, file) {
						
						var row = that.uploader.find('.templates .template-download').clone();
						row.find('.size').text(o.formatFileSize(file.size));
						row.find('.name').text(file.name);
						
						var btn = row.find('.delete').bind('click', function(){
							// remove uploaded file from attachments list
							var index = $.inArray(file.name, that.files);
							if (index > -1) that.files.splice(index, 1);
							// clear msg if there was displayed an error msg
							if (that.status == 0) that.displayMsg('', '');
						});
						
						if (file.name) 
						{
							// bind delete action
							var query = {
								file: file.name,
								mid: that.options.id,
								format: 'json',
								ignoreMessages: true
							};
							if (file.deleteType == 'POST') {
								query._method = 'DELETE';
								btn.data('type', 'POST');
							}
							query[that.Token.attr('name')] = 1;
							btn.data('url', that.options.ajaxUrl + 'uploader' +'&'+ decodeURIComponent($.param(query)) );
						}
						
						if (file.error) {
							// upload error
							row.find('.success').remove();
							row.find('.error').text(file.error);
						}
						else {
							// uploaded successfully
							row.find('.warning, .error').remove();
							row.addClass('pweb-upload-success');
							// add file to uploaded list
							that.files.push(file.name);
						}
						
						rows = rows.add(row);
					});
					return rows;
				}
				
			}).on('fileuploaddragover', function(e) {
				// show dropzone
				that.Box.addClass('pweb-dragged');
				that.Box.find('.pweb-dropzone').css({width: that.Box.width(), height: that.Box.height()});
			
			}).on('fileuploaddrop', function(e, data) {
				// hide dropzone
				that.Box.removeClass('pweb-dragged');

			// adding file to upload list
			}).on('fileuploadadd', function (e, data) {
				if (that.options.reloadToken && that.status == 0 && that.options.uploadAutoStart) 
					// reload token if cache is enabled and not uploading already
					that.ajaxCall('getToken', false)
			
			// file is correct, added to upload list
			}).on('fileuploadprocessdone', function (e, data) {
				that.uploadQueue++;
				data.context.addClass('pweb-upload-ready').find('.warning,.error').remove();
				
			// file is invalid, added to upload list with error
			}).on('fileuploadprocessfail', function (e, data) {
				data.context.each(function (index) {
					$(this).find('.ready,.progress').remove();
					var cancelBtn = $(this).find('.cancel');
					setTimeout(function(){
						cancelBtn.click();
					}, 3000);
				});
			
			// starting file upload
			}).on('fileuploadstart', function(e) {
				if (that.options.uploadAutoStart) {
					// clear msg before upload if not uploading already
					if (that.status == 0)
						that.displayMsg('', '');
					that.status = 2; // uploading
				}
				
			// adding file to download list
			}).on('fileuploaddone', function(e, data) {
				that.uploadQueue--;
				if (!(data.getFilesFromResponse(data)).length) {
					// empty server response
					that.displayMsg(pwebcontact_l10n.upload.ERR, 'error');
					if (data.autoUpload === false)
						that.status = 5; // error
				}
			
			// file is invalid or upload failed, added to list with error
			}).on('fileuploadfail', function(e, data) {
				that.displayMsg(pwebcontact_l10n.upload.ERR, 'error');
				if (data.autoUpload === false)
					that.status = 5; // error
			
			// after upload
			}).on('fileuploadalways', function(e, data) {
				// server-side PHP error
				if (data.textStatus == 'parsererror')
					that.debug(data.jqXHR.responseText);
				// debug response
				else if (that.options.debug && data.result && typeof data.result.debug !== 'undefined' && data.result.debug)
					that.debug(data.result.debug, data.result.status);
				
			// after upload or adding to any list
			}).on('fileuploadfinished', function(e, data) {
				// all files has been uploaded
				if (that.uploadQueue == 0) 
				{
					if (data.autoUpload || that.status == 5)
						that.status = 0; // ready
					else if (that.status == 1)
						that.ajaxCall('sendEmail');
				}
			});

			// hide dropzone
			this.Box.bind('dragleave', function (e) {
				$(this).removeClass('pweb-dragged');
			});

			// hide tooltip after selecting file
			if (this.tooltip) {
				this.uploader.find('.fileinput-button').click(function(){
					$(this).tooltip('hide');
				});
			}
			
			// deleted uploaded files if email was not sent
			$(window).on('unload', function(){
				if (that.files.length) {
					that.uploader.find('.files .delete').click();
				}
			});

			return true;
		},
		
		initValidator: function()
		{
			var that = this;
			
			if (typeof $.fn.validate === 'function')
			{
				// add captcha validation rule
				if (captcha[this.options.id].enabled === true) {
					setTimeout(function(){
						that.Form.find('.pweb-captcha input[type=text]').addClass('pweb-input required');
					}, 1000);
				}
				
				// add custom validation rules
				$.each(this.options.validatorRules, function(key, rule) {
					$.validator.addMethod('pweb'+that.options.id+'-validate-'+rule.name, function(value, element) {
						return this.optional(element) || (rule.regexp).test(value);
					}, '');
				});
				
				// add uploader validation rule
				if (this.uploader) {
					$.validator.addMethod('pweb-validate-uploader', function(value, element) {
						return that.Container.find('.files .pweb-upload-success,.files .pweb-upload-ready').length;
					}, '');
				}
				
				// init validator
				this.validator = this.Form.validate({
					debug: this.options.debug,
					onsubmit: false,
					errorClass: 'invalid',
					showErrors: function(errorMap, errorList) {
						var i, elements;
						for (i = 0; errorList[i]; i++) 
						{
							// highlight invalid field and label
							var element = errorList[i].element;
							this.settings.highlight.call(this, element, this.settings.errorClass, this.settings.validClass);
							
							// get element
							element = $(element);
							if (element.hasClass('pweb-fieldset'))
								element = element.parents('fieldset');
							$('#'+element[0].id+'-lbl').addClass(this.settings.errorClass);
							
							// scroll to first error
							if (i == 0 && that.element.css('position') != 'fixed')
							{
								if (that.options.layout == 'modal') {
									var maxPos = that.Box.outerHeight() - $(window).height();
									if (maxPos > 0) {
										var pos = element.parent().parent().offset().top - that.Box.offset().top;
										if (pos > maxPos) pos = maxPos;
										that.Modal.animate({ scrollTop: parseInt(pos) }, 500);
									}
								}
								else if (that.element.css('position') != 'fixed') {
									var pos = element.parent().parent().offset().top - 10,
										winTop = $(window).scrollTop(),
										winHeight = $(window).height();
									if (pos < winTop + 50 || pos > winTop + winHeight - 50) {
										pos = pos - winHeight/2;
										$('html,body').animate({ scrollTop: parseInt(pos) }, 500);
									}
								}
							}
							
							// show tooltip
							if (that.tooltip && that.options.tooltips >= 2) 
							{
								if (element.hasClass('pweb-single-checkbox'))
									element.next().tooltip('show');
								else 
									element.tooltip('show');
							}
						}
						for (i = 0, elements = this.validElements(); elements[i]; i++) 
						{
							// unhighlight invalid field and label
							var element = elements[i];
							this.settings.unhighlight.call(this, element, this.settings.errorClass, this.settings.validClass);
							
							// get element
							element = $(element);
							if (element.hasClass('pweb-fieldset'))
								element = element.parents('fieldset');
							$('#'+element[0].id+'-lbl').removeClass(this.settings.errorClass);
							
							// hide tooltip
							if (that.tooltip && that.options.tooltips >= 2) 
							{
								if (element.hasClass('pweb-single-checkbox'))
									element.next().tooltip('hide');
								else 
									element.tooltip('hide');
							}
						}
					}
				});
				
				return true;
			}
			else if (this.options.debug) this.debug('jQuery Validate Plugin is not loaded');
			
			return false;
		},
		
		preloadFields: function(data)
		{
			var that = this;
			
			try {
				var fields = data.split('/');
				$.each(fields, function(key, field) {
					field = field.split('=');
					if (field.length >= 2) 
					{
						field[1] = unescape(field[1]);
						var element = $(that.options.selector+'_field-'+field[0]),
							tag = element.prop('tagName').toLowerCase();
						// radio and checkboxes group
						if (tag == 'fieldset') {
							element.find('input').val( field[1].split(';') );
						}
						else if (tag == 'select' && element.attr('multiple')) {
							element.val( field[1].split(';') ).trigger('focus').trigger('blur');
						}
						else if (tag == 'input' && element.attr('type') == 'checkbox') {
							element.attr('checked', field[1] != 0 ? true : false);
						}
						else {
							element.val( field[1] ).trigger('focus').trigger('blur');
							if (tag == 'textarea') 
								element.trigger('keyup');
						}							
					}
				});
			} catch(e) {
				console.log(e);
			}
		},
		
		close: function()
		{
			this.toggleForm(0);
		},
		
		toggleForm: function(state, recipient, bind, event)
		{
			var that = this;
			
			if (typeof state === 'undefined') 
				state = -1;
			if (typeof bind === 'undefined') 
				bind = this.Toggler;
			
			if (this.timer) clearTimeout(this.timer);
		
			// close
			if (!this.hidden && (state === -1 || state === 0)) 
			{
				this.hidden = true;
				if (this.Toggler.length && !this.options.togglerHidden) {
					// add toggler closed class
					this.Toggler.removeClass('pweb-opened').addClass('pweb-closed');
					// change toggler name
					if (this.options.togglerNameClose) 
						this.Toggler.find('.pweb-text').text(this.options.togglerNameOpen);
				}
				
				if (this.options.layout == 'slidebox') 
				{
					var css = {};
					css[this.options.position] = (this.options.position == 'left' || this.options.position == 'right') ? -this.Box.width() : -this.Box.height();
					
					// slide out the box
					this.Box.stop(true, false).animate(css, this.options.slideDuration, this.options.slideTransition, function(){
						
						// restore fixed position
						if (that.element.css('position') == 'absolute' && that.options.slidePos == 'fixed')
						{
							var css = { position: 'fixed' };
							if (that.options.position == 'left' || that.options.position == 'right') {
								css.top = that.options.slideOffset;
							}
							else {
								css[that.options.position] = 0;
								if (that.options.position == 'bottom') css.top = 'auto';
							}
							that.element.css(css);
						}
					}).addClass('pweb-closed');
					
					// hide toggler if disabled
					if (this.options.togglerHidden) this.Toggler.fadeOut(this.options.slideDuration);
				}
				else if (this.options.layout == 'modal') 
				{
					if (this.options.modalEffect != 'default' && typeof this.eventSource !== 'undefined') {
						
						this.Container.trigger('modalClose');
					}
					else {
						this.Modal.modal('hide');
					}
				}
				else if (this.options.layout == 'accordion') 
				{
					$(bind).trigger('closeAccordion');
				}
				
				// close event
				this.options.onClose.apply(this);
				
				// reset form on close
				if (this.options.reset == 2) 
					this.resetForm();
			}
			
			// open
			else if (this.hidden && (state === -1 || state === 1)) 
			{
				// select recipient
				if (typeof recipient === 'number' && recipient >= 0) {
					var select = $(this.options.selector+'_mailto');
					if (select.length) {
						select[0].selectedIndex = parseInt(recipient);
						if (this.Box.hasClass('pweb-labels-over')) select.trigger('focus').trigger('blur');
					}
				}
				
				// load fields values from data attribute
				if ($(bind).length) 
				{
					if ($(bind).data('pwebcontact-fields')) {
						this.preloadFields( $(bind).data('pwebcontact-fields') );
					}
					if ($(bind).data('pwebcontact-fields-once')) {
						this.preloadFields( $(bind).data('pwebcontact-fields-once') );
						$(bind).data('pwebcontact-fields-once', '');
					}
				}
				
				this.hidden = false;
				if (this.Toggler.length && !this.options.togglerHidden) {
					// add toggler opened class
					this.Toggler.removeClass('pweb-closed').addClass('pweb-opened');
					// change toggler name
					if (this.options.togglerNameClose) 
						this.Toggler.find('.pweb-text').text(this.options.togglerNameClose);
				}
				
				if (this.options.layout == 'slidebox') 
				{
					// close other Perfect Web Boxes
					if (this.options.closeOther) {
						$.each(pwebBoxes, function(){
							if (this.options.id != that.options.id && typeof this.close === 'function') this.close();
						});
					}
					
					var css = { width: this.options.slideWidth };
					css[this.options.position] = 0;
					
					var maxWidth = $(window).width();
					
					// calculate width of box
					if (this.options.position == 'left' || this.options.position == 'right')
					{
						if (this.options.slideWidth + this.options.togglerWidth > maxWidth) {
							css.width = maxWidth - this.options.togglerWidth;
						}
					}
					else
					{
						var offset = this.element.offset().left;
						if (this.options.offsetPosition == 'right') {
							offset = $(document).width() - offset;
						}
						if (this.options.slideWidth + offset > maxWidth) {
							css.width = maxWidth - offset;
						}
					}
					
					// slide in the box
					this.Box.stop(true, false).animate(css, this.options.slideDuration, this.options.slideTransition, function(){
						
						// change position to absolute if box is bigger than viewport
						if (that.options.slidePos == 'fixed')
						{
							var css = { position: 'absolute' };
							
							if (that.options.position == 'left' || that.options.position == 'right')
							{
								css.top = that.element.offset().top;
								if (that.Box.height() + that.options.togglerHeight + css.top - $(window).scrollTop() > $(window).height()) {
									css.top = that.element.offset().top;
									that.element.css(css);
								}
							}
							else if (that.Box.height() + that.options.togglerHeight > $(window).height()) 
							{
								css.top = that.element.offset().top;
								if (that.options.position == 'bottom') {
									css.bottom = 'auto';
								}
								that.element.css(css);
							}
						}
					}).css('overflow', 'visible').removeClass('pweb-closed');
					
					// show toggler if disabled
					if (this.options.togglerHidden) this.Toggler.fadeIn(this.options.slideDuration);
				}
				else if (this.options.layout == 'modal') 
				{
					if (this.options.modalEffect != 'default' && $(bind).length) {
                        this.eventSource = bind;
                    }
					this.Modal.modal('show');
				}
				else if (this.options.layout == 'accordion') 
				{
					$(bind).trigger('openAccordion');
				}
				
				// open event
				this.options.onOpen.apply(this);
			}
		},
		
		resetForm: function()
		{
			if (this.status == 3) // sent
			{
				var that = this;
				
				// allow to send next email after a few seconds
				setTimeout(function(){ that.status = 0; }, 5000);
				
				this.status = 4; // reseted
				captcha[this.options.id].status = -1;
				this.displayMsg('', '');
				
				// get new token
				if (!this.options.redirectURL) this.ajaxCall('getToken');
				// show send button
				this.ButtonSend.show();
				
				// reset textarea chars counter
				this.Form.find('textarea.pweb-charslimit').each(function(){
					$('#'+$(this).attr('id')+'-charsleft').text( parseInt($(this).attr('maxlength')) );
				});
				
				// reset form and validator
				if (this.validator)
					this.validator.resetForm();
				this.Form[0].reset();
				
				if (this.Box.hasClass('pweb-labels-over'))
					this.Form.find('input.pweb-input,textarea,select').trigger('focus').trigger('blur');
				
				// clear uploader
				if (this.uploader) {
					var files = this.uploader.find('.files');
					files.find('.cancel,.delete').click();
					files.empty();
					this.files = [];
					this.uploadQueue = 0;
				}
				
				if (captcha[this.options.id].enabled === true && typeof Recaptcha !== 'undefined' && !this.options.redirectURL)
					Recaptcha.reload();
			}
		},
		
		submitForm: function()
		{
			var that = this;
			
			// form already submitted or error occured
			if (this.status == 1 || this.status == 2 || this.status == 3 || this.status == 5) 
				return false;
			
			// check if form is valid
			if (this.validator && !this.validator.form())
				return false;
				
			// form was not reseted after sending last message
			if (this.status == 4) 
			{
				var msg = pwebcontact_l10n.form.SEND_ERR;
				if (this.options.msgPosition == 'popup') {
					this.displayAlert(msg, 'error');
				} 
				else {
					this.scrollToMsg();
					this.displayMsg(msg, 'error');
				}
				return false;
			}
			
			this.status = 1; // sending
			this.scrollToMsg();
			
			// reload token if cache is enabled
			if (this.options.reloadToken) this.ajaxCall('getToken', false);
			
			// pre check captcha
			if (captcha[this.options.id].enabled === true && captcha[this.options.id].status === -1)
				this.ajaxCall('checkCaptcha', false);
			if (captcha[this.options.id].status === false) {
				if (typeof Recaptcha !== 'undefined')
					Recaptcha.reload();
				
				captcha[this.options.id].status = -1;
				return false;
			}
			// upload files
			if (this.uploader && !this.options.uploadAutoStart)
			{
				var uploads = this.uploader.find('.files .pweb-upload-ready');
				if (uploads.length) 
				{
					this.displayMsg(pwebcontact_l10n.upload.UPLOADING, 'progress');
					// start manual upload
					uploads.each(function(index){
						var data = $(this).data('data');
						if (typeof data.submit === 'function') {
							data.submit();
						}
					});
					return true;
				}
			}
			
			if (this.status == 1) this.ajaxCall('sendEmail');
		},
		
		ajaxCall: function(method, async)
		{
			var that = this, 
				query = {
					mid: this.options.id,
					format: 'json',
					ignoreMessages: true
				};
						
			if (method == 'sendEmail' || method == 'checkCaptcha') 
			{
				// prepare query				
				if (this.uploader && this.files.length && method == 'sendEmail') {
					// add attachments files names
					query.attachments = this.files;
				}
				query = decodeURIComponent($.param(query)) +'&'+ this.Form.serialize();
			}
			
			if (typeof async === 'undefined') async = true;
			
			$.ajax({
				url: this.options.ajaxUrl + method,
				type: 'POST', 
				cache: false,
				async: async,
				data: query,
				dataType: 'json',
				dataFilter: this.ajaxResponseDataFilter,
				beforeSend: function() {
		        	if (method == 'sendEmail' || !async) 
		        		that.displayMsg(pwebcontact_l10n.form.SENDING, 'progress');
		        }
			}).done(function(response, textStatus, jqXHR) {
				
				if (typeof response.data !== 'undefined' && typeof response.data.status !== 'undefined') 
				{
					var data = response.data;
					
					if (!async) that.displayMsg('', '');
					
					if (method == 'getToken') 
					{
						that.options.reloadToken = false;
						that.Token.attr('name', data.token);
					}
					else 
					{
						// display debug
						if (data.debug) {
							that.debug(data.debug, data.status);
						}
						
						if (typeof data.deleted !== 'undefined' && !that.options.reset && data.deleted && that.uploader) 
						{
							// remove files if deleted
							that.uploader.find('.files').empty();
							that.files = [];
							that.uploadQueue = 0;
						}
						
						// correct response
						if (data.status >= 1 && data.status <= 199) 
						{
							if (method == 'sendEmail') 
							{
								that.status = 3; // sent and allow reset
								
								// on complete event
								that.options.onComplete.apply(that, [data]);
								
								// remove delete buttons for uploaded files
								if (that.uploader) 
									that.uploader.find('.files .pweb-upload-success .delete').remove();
								
								// reset form
								if (that.options.reset == 1) 
									that.resetForm();
								else if (that.options.reset == 2) 
									that.ButtonSend.hide();
								else if (that.options.reset == 3) {
									that.ButtonSend.hide();
									that.ButtonReset.show();
								}
								else {
									that.status = 4; // reseted
									// allow to send next email after a few seconds
									setTimeout(function(){ that.status = 0; }, 5000);
								}
								
								// display message
								if (that.options.msgPosition == 'popup') {
									that.displayAlert(data.msg, 'success');
									data.msg = '';
								}
								that.displayMsg(data.msg, 'success');
								
								// auto-close form
								if (that.options.closeAuto) 
									that.autoCloseOnSuccess();
								
								// redirect
								if (that.options.redirectURL) 
									that.redirectOnSuccess();
							}
							else if (method == 'checkCaptcha') {
								captcha[that.options.id].status = true;
							}
						}
						// error response
						else 
						{
							if (method == 'checkCaptcha') {
								captcha[that.options.id].status = false;
								// highlight invalid captcha field
								that.Form.find('.pweb-captcha input').removeClass('valid').addClass('invalid');
							}
							
							if (typeof data.invalid !== 'undefined') {
								// unhighlight all previouslly invalid fields
								that.Form.find('.invalid').removeClass('invalid');
								// highlight invalid fields
								$.each(data.invalid, function(key, field) {
									var el = $(that.options.selector+'_'+field);
									if (el.is('input,textarea,select')) el.addClass('invalid');
									$(that.options.selector+'_'+field+'-lbl').addClass('invalid');
								});
							}
							
							// on error event
							that.options.onError.apply(that, [data]);
							
							that.status = 0; // ready
							if (data.status >= 300 && !that.options.debug) {
								that.status = 5; // error
								that.ButtonSend.hide();
							}
							
							// display message
							if (that.options.msgPosition == 'popup') {
								that.displayAlert(data.msg, 'error');
								data.msg = '';
							}
							that.displayMsg(data.msg, 'error');
						}
					}
				}
				else that.displayError(textStatus, jqXHR.responseText.indexOf('<html') === -1 ? jqXHR.responseText : '');
				
			}).fail(function(jqXHR, textStatus, errorThrown) {
				
				that.displayError(jqXHR.status +' '+ errorThrown, jqXHR.responseText.indexOf('<html') === -1 ? jqXHR.responseText : '');
			});
		},
		
		ajaxResponseDataFilter: function(data, type) 
		{
			if (type === 'iframe json' && data && typeof data === 'object') {
				data = $(data[0].body).text();
			}
			if (type.indexOf('json') !== -1 && typeof data === 'string' && (data.indexOf('{') !== 0 || data[data.length-1] !== '}')) {
        		var json = data.match(/{"success":.+{"status":[0-9]+(,"[a-z]+":.+)+}}/i);
        		if (json) {
					if (typeof JSON.stringify === 'function') {
						var result = $.parseJSON(json[0]);
						if (typeof result.data.debug === 'undefined' || !result.data.debug) 
							result.data.debug = [];
						result.data.debug.push( data.replace(json[0], ' ') );
						data = JSON.stringify(result);
					}
					else data = json[0];
        		}
        	}
        	return data;
		},
		
		scrollToMsg: function()
		{
			if (this.options.msgPosition == 'before' || this.options.msgPosition == 'after')
			{
				if (this.options.layout == 'modal') {
					// scroll modal container
					var pos = this.options.msgPosition == 'after' ? this.Box.outerHeight() - $(window).height() : 0;
					if (pos >= 0) this.Modal.animate({ scrollTop: pos }, 500);
				}
				else if (this.element.css('position') != 'fixed') {
					// scroll document body
					var pos = this.Msg.offset().top - 20;
					if (this.options.msgPosition == 'after')
						pos = pos + 60 + this.Msg.height() - $(window).height();
					$('html,body').animate({ scrollTop: parseInt(pos) }, 500);
				}
			}
		},
		
		initAutoPopupCookie: function()
		{
			if (typeof $.cookie === 'function')
			{
				var counter = parseInt($.cookie('pwebcontact'+this.options.id+'_openauto'));
				counter = isNaN(counter) ? 1 : counter+1;
				if (counter <= this.options.maxAutoOpen) 
				{
					$.cookie('pwebcontact'+this.options.id+'_openauto', counter, {
						domain: 	this.options.cookieDomain, 
						path: 		this.options.cookiePath, 
						expires: 	this.options.cookieLifetime
					});
					return true;
				}
			}
			else if (this.options.debug) this.debug('jQuery Cookie Plugin is not loaded');
			
			return false;
		},
		
		autoPopupOnPageLoad: function()
		{
			if (this.options.openDelay)
				this.timer = this.delay(this.toggleForm, this.options.openDelay, this, [1]);
			else
				this.toggleForm(1);
		},
		
		autoPopupOnPageScroll: function()
		{
			var that = this;
			
			this.autoOpen = true;
			$(window).scroll(function(){
				if (that.autoOpen) {
					that.autoOpen = false;
					if (that.options.openDelay)
						that.timer = that.delay(that.toggleForm, that.options.openDelay, that, [1]);
					else
						that.toggleForm(1);
				}
			});
		},
		
		autoPopupOnPageExit: function()
		{
			var that = this;
			
			this.autoOpen = -1;
			$(window).mousemove(function(e){
				if (that.autoOpen == -1 && e.clientY > 70) {
					that.autoOpen = 1;
				} else if (that.autoOpen == 1 && e.clientY < 30) {
					that.autoOpen = 0;
					if (that.options.openDelay)
						that.timer = that.delay(that.toggleForm, that.options.openDelay, that, [1]);
					else
						that.toggleForm(1);
				}
			});
		},
		
		autoCloseOnSuccess: function()
		{
			if (this.options.closeDelay)
				this.timer = this.delay(this.toggleForm, this.options.closeDelay, this, [0]);
			else
				this.toggleForm(0);
		},
		
		redirectOnSuccess: function()
		{
			var that = this;
			
			setTimeout(function(){ document.location = that.options.redirectURL; }, this.options.redirectDelay * 1000);
		},
		
		delay: function(element, delay, bind, args)
		{
			//var that = this;
			return setTimeout(function(){
				return element.apply(bind, args || arguments);
			}, delay);
		},
		
		displayError: function(text, debugHtml)
		{
			if (!this.options.debug) {
				this.status = 5;
				this.ButtonSend.hide();
			}
			else this.status = 0;
			
			var msg = pwebcontact_l10n.form.REQUEST_ERR + text;
			if (this.options.msgPosition == 'popup')
				this.displayAlert(msg, 'error');
			else
				this.displayMsg(msg, 'error');
			
			if (debugHtml) this.debug(debugHtml);
		},
		
		displayMsg: function(html, type)
		{
			// Display message inside form
			this.Msg.html(html ? html : '&nbsp;').attr('class', type ? 'pweb-'+type : '');
		},
		
		displayAlert: function(html, type, heading, close)
		{
			if (typeof $.fn.alert === 'function')
			{
				// Display popup with alert
				var alert = 
				$('<div class="pweb-alert alert alert-block ' + (type ? 'alert-'+type : '') + ' fade in">'
				+ '<button data-dismiss="alert" class="close" type="button">&times;</button>'
				+ (typeof heading !== 'undefined' ? '<h4 class="alert-heading">' + heading + '</h4>' : '')
				+ '<p>' + html + '</p>'
				+ '</div>').alert().appendTo(document.body);
				
				if ((typeof close === 'undefined' || close) && this.options.msgCloseDelay)
					setTimeout(function(){ alert.alert('close'); }, this.options.msgCloseDelay * 1000);
			}
			else alert(html.replace('<br>', '\r\n'));
		},
		
		debug: function(html, code)
		{
			if ($.isArray(html))
				html = html.join('<br>');
			if (typeof code !== 'undefined')
				html = html+'<br>Response code: '+code;
			this.displayAlert(html, 'info', 'Perfect Easy & Powerful Contact Form Debug', false);
		}
	};
	
	})();

	pwebContact.options = pwebContact.prototype.options;
	
})(window.jQuery);