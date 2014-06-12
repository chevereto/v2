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

  chevereto.js
  This file contains all the chevereto core front js functions.

  --------------------------------------------------------------------- */

/* -------------------------------------------------------------------------------------------------------------------------------- */
/*** Ajax Setup ****/

$.ajaxSetup({
	cache: false,
	dataType: "json"
});


/* ---------------------------------------------------------------------------------------------------------------------------------------- */
/*** DEFAULT VALUES ***/

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

/*** Catchers, this states the objects ***/
var ImageIDs = "", remoteXHR = ""; // Captures the remote XHR

/*** default builder ***/
stock_defaults = ["selectors", "css_classes", "settings", "templates"];
var defaults = [];
for(i=0; i<stock_defaults.length; i++) {
	defaults[stock_defaults[i]] = eval(stock_defaults[i]);
};

/*** Available callbacks ***/
reactivate_upload_callback = function() {};


/* ---------------------------------------------------------------------------------------------------------------------------------------- */
/*** HELPERS ***/

/*** Run a callback function if needed. Use it in conditional statements ***/
ask_callback_function = function(callback, response) {
	if(typeof callback == "function") {
		callback(fix_response_object(response));
		return true;
	} else {
		return false;
	}
};

/*** Run a callback function if needed. ***/
run_callback_function = function(callback, response) {		
	if(typeof callback == "function") return callback(fix_response_object(response));
};

/*** Fix the response object ***/
fix_response_object = function(response) {
	if(typeof response == "undefined") return "";
	if(typeof response.error !== "undefined") {
		response.success = (response.error ? false : true);
	}
	return response;
};


/* ---------------------------------------------------------------------------------------------------------------------------------------- */
/*** VALUES FETCH ***/

/**
 * Fetch the value of the element declared in the constants. If no value is found it will use the default value
 */
get_safe_var = function(element) {
	if(typeof element == "undefined") return false;
	element = element.replace(/\]\[/g, ".").replace(/\[/g, ".").replace(/\]/g, "").replace(/\'/g, "");
	objs	= element.split("."); // Fix the element to match as object syntax
	obj		= ""; fail = false;

	for(i=0; i<objs.length; i++) {
		obj += objs[i]+".";
		object = obj.replace(/(\s+)?.$/, "");
		if(typeof eval(object) == "undefined" || eval(object) == "") {
			fail = true;
			break;
		}
	}

	return eval((fail ? "defaults."+object : element));
};

/**
 * Get the target class
 */
get_safe_class = function(classname) {
	return get_safe_var("css_classes."+classname);
};

/**
 * Get the target selector
 * Shorthands for specific selectors
 */
get_safe_selector = function(selector) {
	return get_safe_var("selectors."+selector);
};


/* ---------------------------------------------------------------------------------------------------------------------------------------- */
/*** CONDITIONALS ***/

/*** Analizes the <html> to tell if the uploading process ir running or not. ***/
is_uploading = function() {
	return $('html').hasClass(get_safe_class("uploading"));
};

/*** Analizes the queue contents to tell if the upload request must be done or not. ***/
perform_upload_request = function() {
	return (!$(get_safe_selector("input_resize")).hasError() && $(get_safe_selector("queue_local")).text()!=='' || (($(get_safe_selector("input_url")).val()!=='' && !$(get_safe_selector("input_url")).hasError()) || $(get_safe_selector("queue_element"), get_safe_selector("queue_remote")).size()>0) ? true : false);
};

/*** Tells if the local queue must be focused or not ***/
must_focus_local_queue = function() {
	local_queue_size = $(get_safe_selector("queue_element"), get_safe_selector("queue_local")).size();
	remote_queue_size = $(get_safe_selector("queue_element"), get_safe_selector("queue_remote")).size();
	remote_has_values = ((remote_queue_size > 0 || $(get_safe_selector("input_url")).val()!=="") ? true : false);
	if($(get_safe_selector("upload_local")).hasClass(get_safe_class("show_upload"))) {
		if(local_queue_size > 0) {
			return true;
		} else {
			return (remote_has_values ? false : true);
		}
	} else if($(get_safe_selector("upload_remote")).hasClass(get_safe_class("show_upload"))) {
		if(remote_has_values) {
			return false;
		} else if(local_queue_size > 0) {
			return true;
		}
	}
};


/* ---------------------------------------------------------------------------------------------------------------------------------------- */
/*** ALTERATIONS ***/

/**
 * uploadingSwitch(what)
 * Add/Remove the uplading class on the target selector
 */
uploadingSwitch = function(what) {
	what = what.toLowerCase();
	uploading_class = get_safe_class("uploading");
	if(what=="on") {
		jQuery("html").addClass(uploading_class);
	} else if(what=="off") {
		jQuery("html").removeClass(uploading_class);
	}
};
uploadingON = function() {
	return uploadingSwitch("on");
};
uploadingOFF = function() {
	return uploadingSwitch("off");
};

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
	}
	
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
		}
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
		}
	}
	
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
	}
	
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
	}
	
};

/**
 * jQuery.fn.add_to_remote_queue(event, callback)
 * Adds image URLs from url imput to the queue. Must provide the event.
 * Callback is optional. Use the response object to handle the result
 */
jQuery.fn.add_to_remote_queue = function(event, callback) {
	
	selector = this;
	queue_element = get_safe_selector("queue_element");
	queue_remote = get_safe_selector("queue_remote");
	queueItemHTML_template = get_safe_var("templates.queue_remote_element")
		.replace(/%queue_element%/g, queue_element.toAttr())
		.replace(/%queue_element_status%/g, get_safe_selector("queue_element_status").toAttr())
		.replace(/%queue_element_cancel%/g, get_safe_selector("queue_element_cancel").toAttr())
		.replace(/%queue_element_filename%/g, get_safe_selector("queue_element_filename").toAttr());

	if(this.val()=='') {
		this.resetStatus();
		response = {status_txt:"Empty input URL", status_code:"error", error:true};
		run_callback_function(callback, response);
		return false;
	}
	
	// Return false on > max upload queue
	if(config.multiupload_limit && $(queue_element, queue_remote).size() >= config.multiupload_limit) {
		run_callback_function(callback, response);
		return false;
	}
	
	urls = this.val().match_image_urls();

	if(urls == null) {
		this.setError();
		response = {status_txt:"No URL match", status_code:"error", error:true};
		run_callback_function(callback, response);
		return false;
	}
	urls = urls.array_unique(false);
	
	if(urls.length>0) {

		// We have urls but... Are those images already on the queue?
		queueHTML = "";
		for(i=0; i<urls.length; i++) {
			if(!$(queue_remote).find(queue_element+'[data-url="'+urls[i]+'"]').exists()) {
				queueHTML = $(queueItemHTML_template.replace('%display_url%', urls[i].truncate_url())).attr("data-url", urls[i]);
				$(queue_remote).append(queueHTML);
				if(config.multiupload_limit) {
					if($(queue_element, queue_remote).size() >= config.multiupload_limit || (i+1) >= config.multiupload_limit) {
						break;
					}
				}
			}
		}
		if(queueHTML!=="") {
			this.val("").change();
		}
	} else {
		if($(queue_remote).find(queue_element+'[data-url="'+urls[0]+'"]').exists()) {
			response = {status_txt:"URL already in queue", status_code:"duplicated", error:true};
			run_callback_function(callback, response);
			return false;
		}
		
		queueHTML = $(queueItemHTML_template.replace('%display_url%', urls[0].truncate_url())).attr("data-url", urls[0]);
		response = {status_txt:"URL(s) added", status_code:"ok", error:false};		
		run_callback_function(callback, response);

		this.setValid();
		
		if(event.keyCode==13) {
			$(queue_remote).append(queueHTML);
			this.val("").change();
			run_callback_function(callback, response);
		}
	}
	
	if($(queue_element, queue_remote).size()>0) {
		$(queue_remote).show();
	} else {
		$(queue_remote).hide();
	}
};

/**
 * This prepares the remote queue. Usefull to parse any URL left in the queue or convert the queue on single URL upload.
 */
jQuery.fn.fix_remote_queue = function(callback) {
	
	if(!this.exists()) {
		console.log("target remote queue object doesn't exists! using default declaration");
		target_queue = get_safe_selector("queue_remote");
	} else {
		target_queue = this;
	}

	queue_element_selector = get_safe_selector("queue_element");
	
	// Queue = 1 -> Convert in single url upload
	queue_remote_size = $(queue_element_selector, target_queue).size();
	/*if(queue_remote_size==1) {
		// Invalid url or already on queue
		if(!$(get_safe_selector("input_url")).isValid()) {
			$queue_element = $(queue_element_selector, target_queue);
			single_url = $queue_element.attr("data-url");
			$(get_safe_selector("input_url")).val(single_url).setValid();
			$queue_element.find(".cancel a").click();
		}
	}*/
	if(queue_remote_size>0 && $(get_safe_selector("input_url")).isValid()) {
		var e = jQuery.Event("keyup");
		e.keyCode = 13;
		$(get_safe_selector("input_url")).trigger(e);
	}
	
	run_callback_function(callback);
};

/**
 * This generates the copy button. It will append the button to the object and use the params to set the text to copy and the callback function
 */
jQuery.fn.generate_copy_button = function(copy, callback) {
	// No support for iOS I'm afraid...
	if(navigator.userAgent.match(/(iPad|iPhone|iPod)/i)) {
		return;
	}
	
	if(typeof zeroclip_swf == "undefined" || zeroclip_swf == "") {
		console.log("Undefined or invalid zeroclip_swf value");
		return;
	}
	this.append(get_safe_var('templates.copy').replace(/%copy_class%/g, get_safe_selector('button_copy').toAttr()).replace(/%copy_text%/g, lang.txt_copy));
	$(selectors.button_copy, this).zclip({
		path: zeroclip_swf,
		copy: copy,
		afterCopy: function() {
			if(!ask_callback_function(callback)) {
				$("input", $(this).getParentcontext()).focus().highlight();
			} else {
				run_callback_function(callback);
			}
		}
	});
};

/**
 * This will re-enable the upload form to its default state
 */
reactivate_upload = function() {
	if(!is_uploading()) return;
	uploadingOFF();
	$(get_safe_selector("errorbox")).close_error_box();
	$(get_safe_selector("uploadify")).uploadify("disable", false);
	$(get_safe_selector("uploadify_error")).each(function() { $(this).remove(); });
	$(get_safe_selector("input_url")).val("").change();
	if(typeof reactivate_upload_callback == "function") reactivate_upload_callback();
};

/**
 * This will CANCEL the upload and RESET it status to defaults
 */
cancel_upload = function() {
	$(get_safe_selector("uploadify")).uploadify("cancel", "*");
	if(remoteXHR && remoteXHR.readyState != 4){
		remoteXHR.abort();
		$(get_safe_selector("queue_remote")).empty();
	};
	reactivate_upload();
};

/**
 * focus_upload_source(show, toActive, toDeactivate, toHide, toShow)
 * Used to focus/hide the target upload container. I know that is fast/easy/pretty to use hide()/show() or fade()
 * but that will break the flash queue. It's just a weird flash issue.
 */
function focus_upload_source(show, toActive, toDeactivate, toHide, toShow) {

	uploadswitch_local_selector		= get_safe_selector("select_upload_local");
	uploadswitch_remote_selector	= get_safe_selector("select_upload_remote");
	upload_local_selector			= get_safe_selector("upload_local");
	upload_remote_selector			= get_safe_selector("upload_remote");
	
	switch(show.toLowerCase()) {
		case "local":
			toActive		= uploadswitch_local_selector;
			toDeactivate	= uploadswitch_remote_selector;
			toHide			= upload_remote_selector;
			toShow			= upload_local_selector;
		break;
		case "remote":
			toActive		= uploadswitch_remote_selector;
			toDeactivate	= uploadswitch_local_selector;
			toHide			= upload_local_selector;
			toShow			= upload_remote_selector;
			
		break;
		default:
			if(typeof toActive == "undefined" || typeof toDeactivate == "undefined" || typeof toHide == "undefined" || typeof toShow == "undefined") {
				return false;
			}
		break;
	}
	$(toActive).addClass(get_safe_class("active"));
	$(toDeactivate).removeClass(get_safe_class("active"));
	$(toHide).addClass(get_safe_class("hide_upload")).removeClass(get_safe_class("show_upload"));
	$(toShow).addClass(get_safe_class("show_upload")).removeClass(get_safe_class("hide_upload"));
};


/* ---------------------------------------------------------------------------------------------------------------------------------------- */
/*** AJAX ***/

/**
 * Perform the ajax call for the enable/disable short URL
 */
jQuery.fn.ajax_short_url = function() {
	shorturl_checkbox_selector = this;
	$(get_safe_selector("uploadify")).uploadify('settings', 'formData',{sID: session_id, doShort: shorturl_checkbox_selector.is(':checked')});
	$.ajax({url: base_url_js+"pref.php?doShort="+(shorturl_checkbox_selector.is(':checked')?1:0)});
};

/**
 * The remote upload queue process
 */
var remote_uploaded = 0;
var remote_requests = 0;
jQuery.fn.remoteupload = function(callback) {
	if(!this.find(get_safe_selector("queue_element"))) {
		console.log("empty queue");
		return;
	}
	remote_requests += 1;
	queue_element_selector	= get_safe_selector("queue_element");
	queue_element_class		= get_safe_selector("queue_element").toAttr();
	var queue_element		= $(queue_element_selector, get_safe_selector("queue_remote")).eq(remote_requests-1);	
	
	if(config.multiupload_limit) {
		multiupload_limit = config.multiupload_limit;
		if(remote_requests >= multiupload_limit) { // para la wea po weoncito
			$(queue_element_selector+":gt("+(multiupload_limit-1)+")", queue_element).remove();
		}
	}
	if(!queue_element.exists()) {
		remote_requests = 0;
		
		response = {status_code:403, status_txt:"error", next_url:"", error:true};
		
		if(remote_uploaded>=1) {
			if(remote_uploaded==1) {
				next_url = virtual_url_image+ImageIDs;
			} else {
				next_url = virtual_url_uploaded;
			}
			response = {status_code:200, status_txt:"success", next_url:next_url, error:false};
		} else {
			response = {status_code:403, status_txt:"error", next_url:"", error:true};
		}
		
		if(!ask_callback_function(callback, response)) {
			if(response.status_code==200) {
				window.location = response.next_url;
			} else {
				if(typeof jQuery.fn[get_safe_var("settings.onUploadError.fn")] == "function") {
					$(get_safe_var("settings.onUploadError.selector"))[get_safe_var("settings.onUploadError.fn")](lang[get_safe_var("settings.onUploadError.message")], get_safe_var("settings.onUploadError.extra_args"));
				} else {
					$(get_safe_selector("errorbox_container")).show_error_box(lang.error_local_upload);
				}
				return false;
			}
		}

		return;
			
	}
	
	classes_uploading = get_safe_class("uploading");
	classes_completed = get_safe_class("completed");
	
	queue_element.addClass(classes_uploading);
	
	remoteXHR = $.ajax({
		url: uploader_file,
		data: { url: queue_element.attr("data-url"), resize: $(get_safe_selector("input_resize")).val() },
		type: "POST",
		success: function(response) {
			queue_element.removeClass(classes_uploading).addClass(classes_completed);
			if(response.error == "true") {
				queue_element.addClass(get_safe_selector("uploadify_error").toAttr()).append('<span class="error_txt">'+response.errorMsg+'</span>').find(".status").remove();
			} else {
				queue_element.find(".status").html('<span class="completed"></span>');
				ImageIDs += response.image_id;
				remote_uploaded += 1;
				ImageIDs = response.image_id_public;
			}
			$(get_safe_selector("queue_remote")).remoteupload(callback);
		}
	});
};

/**
 * Deletes the image using a AJAX call. It will try to get the delete URL from the object href.
 * You can also use the arguments "image_id" and "image_delete_hash" to parse this values directly
 * If you use this function within a <a> and with its href, you can set the callback as the
 * unique argument of this function.
 */
jQuery.fn.ajax_delete_image = function(image_id, image_delete_hash, callback) {
	$.each(arguments, function(i, v) {
		if(typeof v == "undefined") {
			arguments[i] = null;
		}
	});
	if(typeof image_id == "function") {
		callback = image_id;
		image_id = null; image_delete_hash = null;
	}
	if(this.is("a") && typeof this.attr("href") !== "undefined" && image_id == null && image_delete_hash == null) {
		regex = /delete-confirm\/image\/([\w]+)\/([\w]+)(?:\/)*/;
		matches = regex.exec(this.attr("href"));
		if(matches == null) {
			console.log("Missing values on the given delete confirm URL (taken by href).");
			return;
		}
		image_id = matches[1];
		image_delete_hash = matches[2];
	}
	if(typeof callback !== "function" && image_id == null || image_delete_hash == null) {
		console.log("Missing image_id/image_delete_hash combo on the function call. You must include this two arguments on the function.");
		return;
	};
	$.ajax({
		url: base_url+'delete-confirm/image/'+image_id+'/'+image_delete_hash,
		type: "POST",
		success: function(response) {
			run_callback_function(callback, response);
		}
	});
};
$.ajax_delete_image = function(image_id, image_delete_hash, callback) {
	return jQuery.fn.ajax_delete_image(image_id, image_delete_hash, callback);
};


/* ---------------------------------------------------------------------------------------------------------------------------------------- */
/*** UPLOADIFY ***/

$(function (){
	
	/*** The Uploadify object ***/
	if($(get_safe_selector("uploadify")).exists()) {
		$(get_safe_selector("uploadify")).uploadify({
			method			: "post",
			swf      		: uploadify_swf,
			uploader		: uploader_file,
			fileObjName		: "ImageUp",
			fileTypeDesc	: "Image Files",
			fileTypeExts	: "*.jpg;*.jpeg;*.png;*.gif;*.bmp",
			formData		: {sID: session_id, doShort: $(get_safe_selector("preferences_shorturl")).is(":checked")},
			fileSizeLimit	: config.max_filesize,
			queueID			: get_safe_selector("queue_local").replace("#", ""),
			queueSizeLimit	: (config.multiupload_limit>0 ? config.multiupload_limit : 999),      
			auto			: false,
			removeCompleted	: false,
			multi			: (config.multiupload ? true : false),
			buttonText		: lang.button_select_files,
			onDialogOpen	: function() {
									reactivate_upload();
									//$(get_safe_selector("errorbox")).click();
			},
			onDialogClose	: function() {
									queue_item_error = $(get_safe_selector("uploadify_error"), get_safe_selector("queue_local"));
									if(queue_item_error.length > 0) {
										queue_item_error.each(function() {
											$(this).remove();
										});
									}
			},
			onUploadStart	: function(file) {
									$(get_safe_selector("uploadify")).uploadify('settings', 'formData', {resize : $(get_safe_selector("input_resize")).val()});
			},
			//onSelect		: function(file) {},
			onUploadError	: function(file, errorCode, errorMsg, errorString) {
									var _onError_explained = errorCode + " : " + errorString;
									console.log(_onError_explained);
			},
			onUploadSuccess	: function(file, data, response) {
									console.log(data);
									console.log("onComplete response: "+data);
									eval("var data="+data.match(/\{.*\}/));
									$queueItem = $("#"+file.id, get_safe_selector("queue_local"));
									if(typeof data !== "undefined" && typeof data.error !== "undefined" && data.error == "true") { // SERVER SIDE ERROR
										$queueItem.addClass(get_safe_selector("uploadify_error").toAttr()).find('.status').remove();
										$queueItem.find(get_safe_selector("uploadify_progress")).remove();
										$queueItem.find(get_safe_selector("uploadify_percentage")).remove();
										$queueItem.append('<span class="error_txt">'+data.errorMsg+'</span>');
										return false;
									} else { // SERVER SIDE OK
										ImageIDs += data.image_id_public;
									}
								  },
			onQueueComplete	: function(queueData) {
									if(ImageIDs!=='') { // One or more images uploaded
										if(queueData.uploadsSuccessful==1) {                           	                                   
											next_url = virtual_url_image+ImageIDs;
										} else {
											next_url = virtual_url_uploaded;
										}
										window.location = next_url;
									} else {
										if($(get_safe_selector("queue_element"), get_safe_selector("queue_local")).size()!==0) {
											console.log(queueData);
											if(typeof jQuery.fn[get_safe_var("settings.onUploadError.fn")] == "function") {
												$(get_safe_var("settings.onUploadError.selector"))[get_safe_var("settings.onUploadError.fn")](lang[get_safe_var("settings.onUploadError.message")], get_safe_var("settings.onUploadError.extra_args"));
											} else {
												$(get_safe_selector("errorbox_container")).show_error_box(lang.error_local_upload);
											}
										}                          	
									}
								  }
		});
	}

});