<?php
/* --------------------------------------------------------------------

  Chevereto
  http://chevereto.com/

  @version	2.6.0
  @author	Rodolfo Berríos A. <http://rodolfoberrios.com/>
			<inbox@rodolfoberrios.com>

  Copyright (c) Rodolfo Berrios <inbox@rodolfoberrios.com>
  
  Licensed under the MIT license
  http://opensource.org/licenses/MIT

  --------------------------------------------------------------------- */

if(!defined('access') or !access) die('This file cannot be directly accessed.');

@session_start();
$_SESSION['last_login_request'] = time();

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php show_language_html_tags(); ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo $doctitle;?></title>
<meta name="generator" content="Chevereto <?php show_chevereto_version(); ?>" />
<link rel="stylesheet" type="text/css" href="<?php echo absolute_to_relative(__CHV_PATH_ADMIN_SYSTEM__).conditional_minify('style.css'); ?>" />
<link href="<?php echo __CHV_URL_THEME__; ?>favicon.ico" rel="shortcut icon" type="image/x-icon" />
<meta name="description" content="<?php echo chevereto_config('site_name'); ?> File Manager - Powered by Chevereto Image Hosting script" />
<meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0; user-scalable=0;"/>
<script type="text/javascript" language="javascript" src="<?php echo absolute_to_relative(__CHV_PATH_SYSTEM_JS__).conditional_minify('jquery.js'); ?>"></script>
<script type="text/javascript" language="javascript" src="<?php echo absolute_to_relative(__CHV_PATH_SYSTEM_JS__).conditional_minify('functions.js'); ?>"></script>
<script type="text/javascript" language="javascript">
	var config = {
		error_reporting : true
	}
	var lang = <?php echo get_chevereto_safe_lang(); ?>;
	var admin_url = "<?php echo __CHV_ADMIN_URL__; ?>";
	var admin_json = admin_url+"json";
	var site_url = "<?php echo __CHV_BASE_URL__; ?>";
	var site_json = site_url+"json";
	var update_script = "<?php echo __CHV_URL_UPDATE_SCRIPT__; ?>"
	
	var input_login = "input#login-password";
	var placeholder = ".placeholder";
	var placeholders = input_login;
	
	var active_class = "active";
	var loading_class = "loading";
	var checked_class = "checked";
	var checkbox_class = "checkbox";
	var selected_class = "selected";
	var disabled_class = "disabled";
	var no_change_class = "no-change";
	
	var checkbox = "."+checkbox_class;
	
	$(function (){
		
		$.ajaxSetup({
			cache: false,
			dataType: "json",
			contentType: "application/json",
			url: <?php if(access=='update' || access=='admin') : ?>admin_json<?php else : ?>site_json<?php endif; ?>
		});
		
		
		/*************************************************************************************************************************************/
		/*** Global helper functions ***/
		
		// Disabler/enabler target selector
		function disable(selector) {
			$(selector).addClass(disabled_class);
		}
		function renable(selector) {
			$(selector).removeClass(disabled_class);
		}
		function is_disabled(selector) {
			return ($(selector).hasClass(disabled_class)) ? true : false;
		}
		
		
		/*************************************************************************************************************************************/
		/*** Login functions ***/
		
		$(input_login).focus(); // Focus loading on load
		$("img#logo", "body#login").click(function() {
			$(input_login).focus();
		});
		$(placeholders).bind("change focus blur keyup", function(event) {
			$target = $(this).parent().children(placeholder)
			if($(this).val().length!==0 || $(this).val()!=="") {
				$target.hide();
			} else {
				$target.fadeIn("fast");
			}
		})
		$(placeholder).click(function() {
			$(this).parent().find("input").trigger("focus");
		})
		function toggle_checkbox($checkbox) {
			if(is_disabled("body")) return false;
			$checkbox.toggleClass(checked_class);
		}
		$(checkbox).live("click", function(event) {
			if(is_disabled("body")) return false;
			$(this).toggleClass(checked_class);
		})
		$("i#login-submit:not(."+loading_class+")").click(function() {
			$this = $(this);
			$input_login_password = $(input_login);
			if($input_login_password.val().length==0 || $.trim($input_login_password.val()).length==0) {
				$input_login_password.val("").focus();
				return false;
			}
			$this.addClass(active_class + " " + loading_class);
			$input_login_password.trigger("focus").attr(disabled_class, disabled_class);
			$("body").addClass(disabled_class);
			keep_login = $("#keep-session-login").find("."+checked_class).exists() ? "&keep=1" : "";
			$.ajax({data: "action=login&password="+hex_md5($(input_login).val())+keep_login,
				success: function(response) {				
					$this.removeClass(active_class + " " + loading_class);
					$input_login_password.removeAttr(disabled_class);
					if(response.status_code!==200) {
						$this.hide();
						$input_login_password.blur().val("").focus();
						$(".input-login").shake();
						$this.fadeIn();
						$("body").removeClass(disabled_class);
					} else {
						$input_login_password.trigger("blur");
						$this.addClass(loading_class);
						$("#login-box").fadeOut(function() {
							$("body").append($('<div id="shade"></div>').css("background-color", "#FFFFFF").fadeIn("fast", function() {
								window.location = <?php switch(access) {
									case 'admin':
										echo 'admin_url';
									break;
									case 'update':
										echo 'update_script';
									break;
									case 'index': default:
										echo 'site_url';
									break;
								} ?>;
								return false;
							}));
						});
					}
				}
			});
		});
		$(input_login).keyup(function(event) {
			if(event.keyCode==13) {
				$("i#login-submit").trigger("click");
			}
		});
		// Keep session login 
		$("#keep-session-login").click(function(event) {
			event.stopPropagation();
			toggle_checkbox($(this).find(checkbox));
		});
	})
</script>

<?php show_google_analytics(); ?>

</head>

<body id="login">

	<div id="login-box">
    	<div id="login-box-content">
        	<img id="logo" src="<?php echo absolute_to_relative(__CHV_PATH_SYSTEM_IMG__); ?>chevereto.png" alt="" />
            <div class="input-login"><span class="placeholder"><?php show_lang_txt('txt_enter_password'); ?></span><input type="password" id="login-password" /><i id="login-submit"></i></div>
            <div id="keep-session-login"><span class="checkbox"></span> <?php show_lang_txt('txt_keep_me_login'); ?></div>
        </div>
    </div>
    
    <div id="login-copyright">Powered by <a href="http://chevereto.com/">Chevereto image hosting script</a></div>

</body>
</html>