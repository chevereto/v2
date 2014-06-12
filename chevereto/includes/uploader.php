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

/*** Session ***/
if (array_key_exists('sID', $_REQUEST)) session_id($_REQUEST['sID']);
@session_start();

/** Block the invalid request ***/
if(is_invalid_request()) {
	json_output(array('error'=>'true','errorMsg'=>'bad request'));
}

/*** Detect Flood ***/
if(is_upload_flood()) {
	json_output(array('error'=>'true','errorMsg'=>'flood detected'));
}

/*** Ask for credentials ***/
if(is_config_private_mode() && (!is_logged_user())) {
	json_output(array('error'=>'true','errorMsg'=>'login needed'));
}

/** Call the upload class ***/
require_once(__CHV_PATH_CLASSES__.'class.upload.php');
$upload = new Upload($to_upload);
if($is_remote) $upload->is_remote = true;
$upload->img_upload_path = __CHV_PATH_IMAGES__;
if(isset($to_resize)) $upload->resize_width = $to_resize;
$upload->thumb_width = chevereto_config('thumb_width');
$upload->thumb_height = chevereto_config('thumb_height');
$upload->max_size = return_bytes(chevereto_config('max_filesize'));
$upload->storage = chevereto_config('storage');

/*** Do the thing? ***/
if($upload->process()) {
	$imageInfo = $upload->image_info;
	if(is_config_short_url() && (is_user_preference_short_url() or $_REQUEST['doShort']=='true')) {
		require(__CHV_PATH_INCLUDES__.'shorturl.php');
		switch(chevereto_config('short_url_image')) {
			default: case 'shorturl':
				$short_url = 'image_shorturl';
			break;
			case 'direct':
				$short_url = 'image_url';
			break;
			case 'viewer':
				$short_url = 'image_viewer';
			break;
		}
		$short_url = $upload->image_info[$short_url];
		$imageInfo['image_shorturl_service'] = $ShortURL->get_ShortURL($short_url);
	}
	$_SESSION['ImagesUp'][$upload->image_info['image_id_public']] = $imageInfo;
	if(chevereto_config("error_reporting")==true) {
		$output = $upload->image_info;
   	} else {
		$output = array("image_id_public"=>$upload->image_info['image_id_public']);
   	}
	json_output($output);
} else { 
	// Translate the upload errors from the class to Chevereto's lang
	switch($upload->error) {
		case 'error uploading':
			$error_msg = $lang['error_uploading'];
		break;
		case 'empty source':
			$error_msg = $lang['error_empty'];
		break;
		case 'invalid source':
			$error_msg = $lang['error_source'];
		break;
		case 'invalid image url':
			$error_msg = $lang['error_url'];
		break;
		case 'invalid thumb size':
			$error_msg = $lang['error_thumb_size'];
		break;
		case 'invalid resize size':
			$error_msg = $lang['error_resize_sise'];
		break;
		case 'invalid image':
		case 'invalid mime':
		case 'invalid extension':
			$error_msg = $lang['error_image_invalid'];
		break;
		case 'too big':
			$error_msg = $lang['error_image_weight'];
		break;
	}
	if(!check_value($error_msg)) $error_msg = $upload->error;
	json_output(array("error"=>"true","errorMsg"=>"$error_msg"));
}

?>