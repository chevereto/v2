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

define('access', 'API');
require_once('includes/chevereto.php');

/*** Die, die, die my darling ***/
if(chevereto_config('api_key')=='my_api_key' and chevereto_config('api_mode')=='private' and !is_localhost()) {
	chevereto_die(array('Open <code>includes/config.php</code>','Edit <code>$config[\'api_key\'] = \'my_api_key\';</code> with a different key.'), 'API key', array('You haven\'t changed the default api key, the API won\'t work until you fix this.'));
}

$key		= $_REQUEST['key'];
$to_upload	= $_REQUEST['upload'];
$to_resize	= $_REQUEST['resize_width'];
$format		= $_REQUEST['format'];
$callback	= $_REQUEST['callback'];

/*** Checks the auth ***/
if(api_mode('private') and api_key()!==$key and !is_localhost()) {
	$error_key_msg = 'Invalid API key';
	$ERROR_AUTH_API = array(
		'status_code' 	=> 403,
		'status_txt' 	=> $error_key_msg
	);
	switch($format) {
		default:
		case 'json':
		default:
			json_output($ERROR_AUTH_API, $callback);
		break;
		case 'xml':
			xml_output($ERROR_AUTH_API);
		break;
		case 'txt':
			echo $error_key_msg;
		break;
	}
	exit; // Shout the door
}

/*** Observe the image request ***/
if(is_image_url($to_upload)) {
	$api_remote_upload = true;
} else {
	if(check_value($to_upload) or check_value($_FILES['upload'])) {
		// Creates the temp image
		$api_temp_name = __CHV_PATH_IMAGES__.generateRandomString(16).'.temp';
		while(file_exists($api_temp_name)) {
			$api_temp_name = __CHV_PATH_IMAGES__.generateRandomString(16).'.temp';
		}
		if(check_value($_FILES['upload']['tmp_name'])) {
			$to_upload = $_FILES['upload'];	
		} else {
			// The base64 comes from POST?
			if ($_SERVER['REQUEST_METHOD'] == 'POST') {
				// Handles the stream
				$fh = fopen($api_temp_name,'w');
				stream_filter_append($fh,'convert.base64-decode',STREAM_FILTER_WRITE);
				if(!@fwrite($fh, $to_upload)) {
					$error = 'invalid base64 byte sequence';
				} else {
					// Since all the validations works with $_FILES, we're going to emulate it.
					$to_upload = array(
						'name'		=> generateRandomString(rand(5,10)).'.jpg',
						'type'		=> 'image/jpeg',
						'tmp_name'	=> $api_temp_name,
						'error'		=> 'UPLOAD_ERR_OK',
						'size'		=> '1'
					);
				}
				fclose($fh);
				
			} else {
				$error = "image base64 string must be sent using POST method";
			}
			
		}
		
	}
}

// No errors, attemp to do the upload process
if(!$error) {
	require_once(__CHV_PATH_CLASSES__.'class.upload.php');
	$api_upload = new Upload($to_upload);
	if($api_remote_upload) $api_upload->is_remote = true;
	
	$api_upload->img_upload_path = __CHV_PATH_IMAGES__;
	$api_upload->storage = chevereto_config('storage');
	$api_upload->resize_width = $to_resize;
	$api_upload->thumb_width = chevereto_config('thumb_width');
	$api_upload->thumb_height = chevereto_config('thumb_height');
	
	$api_upload->max_size = return_bytes(chevereto_config('max_filesize'));
	
	/*** Do the thing? ***/
	if($api_upload->process()) {
		
		$api_status_code = 200;
		$api_status_txt = 'OK';
		// Build the data array
		$api_data_array = $api_upload->image_info;
		if($api_upload->is_remote) {
			$api_data_array['source'] = $to_upload;
		} else {
			$api_data_array['source'] = 'base64 image string';
		}
		$api_data_array['resized'] = check_value($to_resize) ? '1' : '0';
		$api_txt_output = $api_upload->image_info['image_url'];
		
		// Short URL generation
		if(is_config_short_url()) {
			require(__CHV_PATH_INCLUDES__.'shorturl.php');
			$api_data_array['shorturl'] = $ShortURL->get_ShortURL($api_upload->image_info['image_url']);
		}
		
	} else {
		$api_status_code = 403;
		$api_status_txt = $api_upload->error;
	}
} else {
	$api_status_code = 403;
	$api_status_txt = $error;
}

$REST_API = array(
		'status_code' 	=> $api_status_code,
		'status_txt' 	=> $api_status_txt,
		'data'			=> $api_data_array
);

$OUTPUT_REST_API = array_filter($REST_API);

switch($format) {
	default:
	case 'json':
	default:
		json_output($OUTPUT_REST_API, $callback);
	break;
	case 'xml':
		xml_output($OUTPUT_REST_API);
	break;
	case 'txt':
		echo $api_txt_output;
	break;
	case 'redirect':
		if($OUTPUT_REST_API['status_code']==200) {
			$redirect_url = __CHV_BASE_URL__.__CHV_VIRTUALFOLDER_IMAGE__.'/'.$api_upload->image_info['image_id_public'];
			header("Location: $redirect_url");
		} else {
			die($OUTPUT_REST_API['status_txt']);
		}
	break;
}

?>