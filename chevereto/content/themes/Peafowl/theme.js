/* --------------------------------------------------------------------

  Chevereto
  http://chevereto.com/

  @version	2.6.0
  @author	Rodolfo Berríos A. <http://rodolfoberrios.com/>
			<inbox@rodolfoberrios.com>
  
  Copyright (C) 2013 Rodolfo Berríos A. All rights reserved.
  
  BY USING THIS SOFTWARE YOU DECLARE TO ACCEPT THE CHEVERETO EULA
  http://chevereto.com/license

  --------------------------------------------------------------------

  theme.js
  This file contains the peafowl theme js functions 

  --------------------------------------------------------------------- */

/* ---------------------------------------------------------------------------------------------------------------------------------------- */
/*** CONSTANTS ***/

/**
 * CSS Classes object
 * This contains the CSS classes handled by JavaScript. If you want to change a CSS class make sure that you update this object
 */
var css_classes = {
	active						: "active",
	valid						: "valid",
	error						: "error",
	uploading					: "uploading",
	completed					: "completed",
	cancelled					: "cancelled",
	loading						: "loading",
	copy						: "copy",
	empty_upload				: "empty-upload",
	show_upload					: "show_upload",
	hide_upload					: "hide_upload"
};

/**
 * Selectors object
 * This contains the selectors handled by JavaScript. If you want to change a HTML selector make sure that you update this object
 */
var selectors = {
	
	// Content
	content						: "#content",
	
	// Upload
	upload						: "#upload",
	upload_local				: "#upload-local",
	upload_remote				: "#upload-remote",
	upload_button_container		: "#upload-action",
	
	// Queues
	queue_local					: "#fileQueue",
	queue_remote				: "#remoteQueue",
	queue_element				: ".uploadify-queue-item",
	queue_element_filename		: ".fileName",
	queue_element_status		: ".status",
	queue_element_cancel		: ".cancel",

	// Inputs
	input_url					: "input#url",
	input_resize				: "input#resize",
	input_item					: ".input-item",

	// Buttons
	button_upload				: "#upload-button",
	button_upload_cancel		: "#cancel-upload",
	button_delete_image			: "#delete-confirm",
	button_copy					: "span.copy",
	
	// Switch
	select_upload_local			: "#select-local",
	select_upload_remote		: "#select-remote",
	
	// Resize
	resize_switch_button		: "#resizing-switch a",
	resize_container			: "#resizing-box",
	
	// Preferences
	preferences_switch_button	: "#preferences",
	preferences_container		: "#preferences-box",
	preferences_shorturl		: "#pref-shorturl",	
	
	// Uploadify
	uploadify					: "#uploadify",
	uploadify_error				: ".uploadify-error",
	uploadify_error_text		: ".error_txt",
	uploadify_cancel_button		: ".status a",
	uploadify_percentage		: ".percentage",
	uploadify_progress			: ".uploadify-progress",
	uploadify_progress_bar		: ".uploadify-progress-bar",
	uploadify_button_text		: ".uploadify-button-text",
	
	// Uploaded view
	multicodes					: "#multi-codes",
	
	// Error box
	errorbox					: "#error-box",
	errorbox_container			: "#error-box-container",
	errorbox_message			: "#error-msg",
	
	// Uploaded
	uploaded_list				: "#uploaded_list",
	uploaded_image				: ".uploaded_image",
	
	// View
	viewing						: ".viewing",
	view_full_image				: ".view-full-image",
	
	// Delete image
	delete_image_confirm_button	: "#delete-image-confirm",
	delete_confirm_msg			: "#delete-confirm-msg",
	delete_confirm_cancel		: "#delete-confirm-cancel",
	delete_deleted_container	: "#delete-deleted-container"
	
};

/**
 * Settings object
 * This is the easiest way to customize the default behaviours
 * Refer to easing methods on http://gsgd.co.uk/sandbox/jquery/easing/
 */
var settings = {
	errorbox_open : {
		effect			: "slideDown",					// slideDown | FadeIn | show
		speed			: "fast",						// slow | normal | fast | [time:ms]
		easing			: "easeOutSine",				// easing function
		where			: "prepend"						// prepend | append
	},
	errorbox_close : {
		effect			: "slideUp",					// slideUp | fadeOut | hide
		speed			: "fast",						// slow | normal | fast | [time:ms] 
		easing			: "easeOutSine"					// easing function
	},
	// Use this if you want to change the onUploadError default function
	onUploadError : {
		fn				: "show_error_box",				// The function that you want to call (Must be jQuery function)
		selector		: selectors.errorbox_container,	// The $(selector) to pass the onUploadError.fn
		message			: "error_local_upload",			// The lang string to use
		extra_args		: {}							// The aditional arguments to pass to the settings.onUploadError.fn function (leave empty to use the errorbox_open object)
	}
};

/**
 * HTML Templates
 * This contains the HTML templates used by the JS. Here you can easily tweak the HTML of all the HTML edited by JavaScript
 */
var templates = {
	queue_local_element		: '<div id="%file_id%" class="%queue_element%">\
								<div class="%queue_element_status% %queue_element_cancel%">\
									<a href="javascript:jQuery(\'#%settings_id%\').uploadify(\'cancel\', \'%file_id%\')"></a>\
								</div>\
								<span class="%queue_element_filename%">%filename% <span class="byteSize">%filesize%</span> </span> <span class="%uploadify_percentage%"></span>\
								<div class="%uploadify_progress%">\
									<div class="%uploadify_progress_bar%"></div>\
								</div>\
							  </div>',
	queue_remote_element	: '<div class="%queue_element%">\
								<div class="%queue_element_status% %queue_element_cancel%"><a></a></div>\
								<span class="%queue_element_filename%">%display_url%</span>\
							  </div>',
	errorbox				: '<div id="chv-error-box-wrap" style="display: none;"><div id="%errorbox%" class="%error%"><div id="%errorbox_message%">%error_text%</div></div></div>',
	copy					: '<span class="%copy_class%">%copy_text%</span>'
};


/* ---------------------------------------------------------------------------------------------------------------------------------------- */
/*** THEME FUNCTIONS ***/

$(function (){

	/* This functions are theme scpecific meaning that you can use it as a base or you can totally make your own functions.
   	   NOTICE: This file uses functions declarated in functions.js */
	
	
	/* ---------------------------------------------------------------------------------------------------------------------------------------- */
	/*** LEGACY FUNCTIONS ***/
	
	/* Try to fetch old theme html and update it on the fly */
	$.each(["#viewing", "#view-full-image", "#full_image", "#image_tools", "#socialize", "#show_directly", "#thumb_plus_link"], function(index, value) {
		$(value).addClass(value.toAttr());
	});
	
	
	/* ---------------------------------------------------------------------------------------------------------------------------------------- */
	/*** CALLBACKS ***/
	
	/* This is a easy way to run functions over the default methods. They are optional.
	
	/**
	 * reactivate_upload_callback: Will be called as a callback on reactivate_upload() and in cancel_upload()
	 */	
	reactivate_upload_callback = function() {
		$(selectors.button_upload).html("<span>"+lang.button_upload+"</span>").removeClass(css_classes.uploading);
		$(selectors.button_upload_cancel).css('display', 'none').removeClass(css_classes.cancelled);
		$(selectors.preferences_container + " input, " + selectors.input_resize).removeAttr('disabled');
		$(selectors.queue_remote).hide();
	};
		
	
	/* ---------------------------------------------------------------------------------------------------------------------------------------- */
	/*** ALTERATION ***/
	
	/**
	 * Peafowl's sticky footer
	 */
	footer_height = $("#foot").outerHeight(true);
	$("#wrap").css("margin-bottom", -footer_height).append('<div id="push" />');
	$("#push").css("height", footer_height);
	$(footer_height).css("height", footer_height);
	
	/**
	 * jQuery.fn.show_error_box(error_text, where="", effect="", speed="", easing="")
	 * Shows the error box with the desired message in the object (target) with prepend or append
	 * Preserve error_text when editing
	 */
	jQuery.fn.show_error_box = function(error_text, where, effect, speed, easing) {
		
		errorbox_selector		= get_safe_selector("errorbox");
		errorbox_container		= get_safe_selector("errorbox_container");
		errorbox_msg_selector	= get_safe_selector("errorbox_message");
		
		if(!this.exists()) {
			console.log("target error box object doesn't exists! using default declaration");
			$(get_safe_selector("content")).prepend('<div id="'+get_safe_selector("errorbox_container").toAttr()+'"></div>');
			target = errorbox_container;
		} else {
			target = this;
		};
		
		if(typeof where == "undefined" || where!=="prepend"|| where!=="append") where = get_safe_var("settings.errorbox_open.where");
		if(typeof effect == "undefined") effect = get_safe_var("settings.errorbox_open.effect");
		if(typeof speed == "undefined" || !speed.isSpeedval()) speed = get_safe_var("settings.errorbox_open.speed");
		if(typeof easing == "undefined") easing = get_safe_var("settings.errorbox_open.easing");
		
		if($(errorbox_msg_selector, target).exists()) {
			$(errorbox_msg_selector, target).find(selectors.errorbox_message).html(error_text);
		} else {
			ERROR_HTML = templates.errorbox.replace(/%errorbox%/g, errorbox_selector.toAttr()).replace(/%error%/g, get_safe_class("error")).replace(/%errorbox_message%/g, errorbox_msg_selector.toAttr()).replace(/%error_text%/g, error_text);
			if(where == "prepend") {
				$(target).prepend(ERROR_HTML);
			} else if(where == "append") {
				$(target).append(ERROR_HTML);
			};
			target = $("#chv-error-box-wrap");
			if(speed.isNumeric()) speed = Number(speed);
			switch(effect.toLowerCase()) {
				case 'fadein':
					target.fadeIn(speed, easing);
				break;
				case 'show':
					target.show(speed, easing);
				break;
				default: case "slidedown": 
					target.slideDown(speed, easing);
				break;
			};
		};
		
		if(typeof reactivate_upload_callback == "function") {
			reactivate_upload_callback();
		}
		
	};
	
	/**
	 * jQuery.fn.close_error_box(effect="", speed="", easing="")
	 * Closes the error_box with the desired effect, speed and easing (all optional)
	 */
	jQuery.fn.close_error_box = function(effect, speed, easing) {
		
		errorbox_selector		= get_safe_selector("errorbox");
		errorbox_container		= get_safe_selector("errorbox_container");
		errorbox_msg_selector	= get_safe_selector("errorbox_message");
		
		if(!$(errorbox_selector).exists()) return;
		
		if(!this.exists()) {
			console.log("target error box object doesn't exists! using default declaration");
			target = $(errorbox_container);
		} else {
			target = this;
		};
		
		if(typeof effect == "undefined") effect = get_safe_var("settings.errorbox_close.effect");
		if(typeof speed == "undefined" || !speed.isSpeedval()) speed = get_safe_var("settings.errorbox_close.speed");
		if(typeof easing == "undefined") easing = get_safe_var("settings.errorbox_close.easing");
		
		close_wrap = function() {
			$(errorbox_selector+", #chv-error-box-wrap").remove();
		};
		target = $(errorbox_selector);
		if(speed.isNumeric()) speed = Number(speed);
		
		switch(effect.toLowerCase()) {
			case 'fadeout':
				target.fadeOut(speed, easing, function() {
					close_wrap();
				});
			break;
			case 'hide':
				target.hide(speed, easing, function() {
					close_wrap();
				});
			break;
			default: case "slideup": 
				target.slideUp(speed, easing, function() {
					close_wrap();
				});
			break;
		};
		
	};
	
	
	/* ---------------------------------------------------------------------------------------------------------------------------------------- */
	/*** EVENTS ***/
	
	/**
	 * Error box live click listener
	 */
	$(document).on("click", selectors.errorbox, function() {
		$(selectors.errorbox_container).close_error_box();
		reactivate_upload();
	});
	
	/**
	 * Upload selector -> show/hide upload container
	 */
	$(selectors.select_upload_remote+", "+selectors.select_upload_local).click(function() {
		if(is_uploading()) return false;
		if(!$(this).hasClass(css_classes.active)) {
			if($(this).is(selectors.select_upload_remote)) {
				focus_upload_source("remote");
			} else if($(this).is(selectors.select_upload_local)) {
				focus_upload_source("local");
			};
		};
	});
	
	/**
	 * Preferences -> show/hide preferences box
	 */
	$(selectors.preferences_switch_button).click(function() {
		if(is_uploading()) {
			return false;
		};
		$(selectors.preferences_container).slideToggle("fast", "easeOutSine");
		$(this).toggleClass(css_classes.active);
	});
	
	/**
	 * Preferences -> short url on/off
	 */
	$(selectors.preferences_shorturl).change(function(){
		$(this).ajax_short_url();
	});
	
	/**
	 * Resize -> show/hide resize box
	 */
	$(selectors.resize_switch_button).click(function() {
		if(is_uploading()) return false;
		$(selectors.resize_container).slideToggle("fast", "easeOutSine");
		$(this).toggleClass(css_classes.active);
	});
	
	/**
	 * Logout
	 */
	$("a[rel='logout']").click(function() {
    	$.ajax({url: base_url+"json", data: "action=logout",
			success: function(response) {
				window.location = base_url;
			}
		});
    });
	
	/**
	 * Validate the resize input field -> deny non numeric input and paste
	 * Bind in keydown, paste(input) and in change
	 */
	$(selectors.input_resize).bind("keydown input change", function(event) {
		if($(this).isValidresize(event, config.min_resize_size, config.max_resize_size)) {
			$(this).setValid();
		} else {
			$(this).setError();
		};
		if($(this).val()=='') $(this).resetStatus();
	});
	
	/**
	 * URL input parser
	 * This will parse the contents of the URL input as URLs on the remote queue
	 */ 
	$(selectors.button_add_url).hide();
	$(selectors.input_url).resetStatus();
	$(selectors.input_url).bind("change keyup input", function(event) { // Event listener
		$(this).add_to_remote_queue(event);
	}).click(function() {
		reactivate_upload();
	});

	/**
	 * Upload button -> Trigger the upload action
	 * This click event will trigger the upload with whatever you may tweak on it, like validation, pre/post actions, etc.
	 * This means that you can remove/replace any prior validation like the url validation or the resize validation.
	 * You can also change when you trigger the upload, you can change the $(selector), the .function, etc.
	 */
	$(selectors.button_upload).click(function () {
		
		if(is_uploading()) return false;
		
		// Autoclose the resize input when his visible but with no value
		if($(selectors.input_resize).val()=="" && $(selectors.input_resize).isVisible()) $(selectors.resize_switch_button).click();
		
		// Fix the remote queue only when it's is visible
		if($(selectors.upload_remote).hasClass(css_classes.show_upload)) {
			$(selectors.upload_remote).fix_remote_queue();
		};
		
		// Autofix the tab selection (local/remote)
		if(must_focus_local_queue()) {
			focus_upload_source("local");
		} else {
			focus_upload_source("remote");
			// Is remote... Check if it has errors.
			if($(selectors.input_url).hasError()) {
				$(selectors.input_url).highlightError();
				return false;
			};
		};
		
		// Do something...
		if(perform_upload_request()) {
			
			uploadingON();
			
			$(selectors.errorbox_container).close_error_box(); // Close any error_box instance
			
			$(this).addClass(css_classes.uploading).html('<span class="'+css_classes.uploading+'"><b>Uploading</b></span>');
			$(selectors.button_upload_cancel).fadeIn();
			$(selectors.preferences_container + " input, " + selectors.input_resize).attr('disabled', 'disabled');
			
			// Do the upload on the active tab
			if(must_focus_local_queue()) {
				$(selectors.uploadify).uploadify("upload", "*");
			} else { // Remote
				/* This theme has both single URL upload as <INPUT> and multiple remote upload (queue)
				   that's why there are a single method and a multiple method. If you will handle all in queue then you should use just the
				   $(selectors.queue_remote).remoteupload(); method without the special single remote upload thing.
				*/
				$(selectors.queue_remote).remoteupload(); // Note that you can handle a callback function here with a data/response parameter
			};
			
		} else { // Don't do anything
			// Resize error
			if($(selectors.input_resize).hasError()) {
				$(selectors.input_resize).highlightError();
			};			
			// Trigger the error box or update its contents
			if($(selectors.errorbox).exists()) {
				$(selectors.errorbox).shake();
			} else {
				$(selectors.errorbox_container).show_error_box(lang.error_empty_form); 
			};
		};
	});
	
	/**
	 * Cancel a element from the remote queue
	 */
	$(document).on("click", selectors.queue_remote+" .cancel a", function(event) {
    	$(this).closest(selectors.queue_element).slideUp(250, function() {
        	$(this).remove();
            if($(selectors.queue_element, selectors.queue_remote).size()==0) {
                $(selectors.queue_remote).hide();
            };
        });
    });
	
	/**
	 * Cancel the upload (once has already started)
	 * Note: This event is accepting live request :)
	 */	
	$(document).on("click", selectors.button_upload_cancel, function() {
		if(confirm(lang.msg_cancel_upload)) {
        	$(this).addClass(css_classes.cancelled);
        	cancel_upload();
		};
	});
	
	/**
	 * Multicodes switcher
	 */
	$("select", "#multi-codes").change(function() {
		$("textarea", "#multi-codes").hide();
		$("textarea#"+$(this).attr("value")).show();
	});
	
	/**
	 * Thumbs switcher (for uploaded.php)
	 */
	$("a:first-child", selectors.uploaded_list).addClass(css_classes.active);
	$("a", selectors.uploaded_list).click(function(event) {
		event.preventDefault();
		if($(this).hasClass(css_classes.active)) return;
		$("a", selectors.uploaded_list).removeClass(css_classes.active);	
		$(this).addClass(css_classes.active);
		$(selectors.uploaded_image).hide();
		$("#uploaded_image-"+$(this).attr("rel")).show();
	});
	// Append loading indicator for each image
	$("img.full_image").each(function() {
		$(this).bind("load", function() {
			$(this).closest(".uploaded_image").find(".loading-image").fadeOut();
		})
	});
	// Wait for first thumb to load and then calculate the rows of #uploaded_list
	$("img:first-child", selectors.uploaded_list).bind("load", function() {
		if($(selectors.uploaded_list).attr("visible-rows").isNumeric() && parseInt($(selectors.uploaded_list).attr("visible-rows"))>0) {
			uploaded_list_max_height = $("a", selectors.uploaded_list).outerHeight(true) * parseInt($(selectors.uploaded_list).attr("visible-rows"));
			$(selectors.uploaded_list).css("max-height", uploaded_list_max_height);
		};
	});
	
	/**
	 * Social links pop-up
	 */
	$("a[rel='pop-up']", ".image-tools-section.socialize").click(function() {
    	if($(this).attr("target")!=="_blank") {
        	var pop_w = 650;
            var pop_h = (typeof $(this).attr("data-height") !== "undefined") ? $(this).attr("data-height") : 350;
            var pop_left = (screen.width/2)-(pop_w/2);
            var pop_top = (screen.height/2)-(pop_h/2);
            var settings = "height="+pop_h+", width="+pop_w+", scrollTo, resizable=0, scrollbars=0, location=0, top="+pop_top+", left="+pop_left;  
            window.open(this.href, 'Popup', settings);  
            return false; 	
       	};
    });
	
	/**
	 * Input view/uploaded copy button
	 */
	$(selectors.input_item).mouseover(function() {
		if(!$(this).find(selectors.button_copy).exists() && $("input", this).exists()) {
			copy_value = $("input", this).val();
			$(this).generate_copy_button(copy_value); // Declarate the new copy instance
		};
		$(selectors.button_copy, this).css("visibility", "visible");
	}).mouseleave(function() {
		$(selectors.button_copy, this).css("visibility", "hidden");
	});
	
	/**
	 * Select all text on input double click (parent .input-item)
	 */
	$("input", selectors.input_item).dblclick(function() {
		$(this).select();
	});
	
	/**
	 * Delete image function
	 */
    $(selectors.delete_image_confirm_button).click(function(event) {
    	event.preventDefault(); // Prevent default link behaviour
		
		$(selectors.delete_confirm_cancel)
			.height($(selectors.delete_confirm_cancel).height()) // Keep div height (for loading class)
			.addClass(css_classes.loading) // Add loading class
			.find("a").hide(); // Hide the <a>
        
		$(selectors.view_full_image).fadeOut();
		
		/* In this case the function is called on the <a> which contains the delete-confirm URL
		 * You can also call it like this: $.ajax_delete_image('imageid', 'imagedeletehash', function(response) {}) 
		 * or this $('#selector').ajax_delete_image('imageid', 'imagedeletehash', function(response) {}) 
		 */
		$(this).ajax_delete_image(function(response) {
			if(response.status_code==200) {
				$(selectors.viewing+", "+selectors.delete_confirm_msg).remove();
				$(selectors.delete_confirm_cancel).fadeOut();
				$(selectors.delete_deleted_container).text(response.status_txt).fadeIn();
				document.title = response.status_txt + " - " + config.doctitle;
			} else {            	
				$(selectors.delete_confirm_cancel).removeClass(css_classes.loading).find("a").fadeIn();
				$(selectors.view_full_image).fadeIn();
				$(selectors.errorbox_container).show_error_box(response.status_txt);
			};
		});

    });
	
});