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

// Always test the current installation
check_install();

/**
 * Returns the current script version
 */
function get_chevereto_version($full=true) {
	if($full) {
		return __CHV_VERSION__;
	} else {
		preg_match('/\d\.\d/', __CHV_VERSION__, $return);
		return $return[0];
	}
}
register_show_function('get_chevereto_version', '$full=true');

/**
 * debug
 * Outputs a well fromated HTML output of anything
 */
function debug($var) {
	echo '<pre>',print_r($var,1),'</pre>';
}

/**
 * chevereto_config($config_key)
 * Returns the $config value or FALSE in empty
 */
function chevereto_config($config_key) {
	global $config;
	return $config[$config_key];
}
function get_chevereto_config($config_key) {
	return chevereto_config($config_key);
}
register_show_function('get_chevereto_config', '$config_key');

/**
 * conditional_config($config_key)
 * Returns true/false according to the state of the config value
 */
function conditional_config($config_key) {
	if(!chevereto_config($config_key)) {
		return false;
	} else {
		return true;
	}
}

/**
 * check_value
 * Looks for valid value and setted ones.
 */
function check_value($value) {
	if((@count($value)>0 and !@empty($value) and @isset($value)) || $value=='0') {
		return true;
	}
}

/**
 * Register a show_ function from a get_ function
 */
function clean_array_arguments($argument) {
	return preg_replace('/\s*=\s*[\'|"](.*)[\'|"]\s*/', '', $argument);
}
function register_show_function($function, $args='') {
	$function = preg_replace('/^get_/', '', $function);
	$args = array_map('trim', explode(',', $args));
	$parsed_args = implode(',', array_map('clean_array_arguments', $args));
	$args = (check_value($args) ? implode(',', $args) : '');	
	eval("function show_".$function."(".$args.") { echo get_".$function."(".$parsed_args."); }");
}


/**
 * is_integer_val
 * Check if the value is an integer, whatever is his source
 */
function is_integer_val($value) {
	return (!preg_match("/\D/", $value)) ? true : false;
}

/**
 * clean_spaces
 * Removes all the spaces on a given string
 */
function clean_spaces($var) {
	return str_replace(' ', '', $var);
}

/**
 * sanitize_path
 * Sanitizes ///ugly//path/// to clean/path
 */
function sanitize_path($path) {
	return rtrim(ltrim(stripslashes($path), '/'), '/');
}

/**
 * Function: sanitize
 * Returns a sanitized string, typically for URLs.
 *
 * Parameters:
 *     $string - The string to sanitize.
 *     $force_lowercase - Force the string to lowercase?
 *     $anal - If set to *true*, will remove all non-alphanumeric characters.
 *     $trunc - Number of characters to truncate to (default 100, 0 to disable).
 *
 * This function was borrowed from chyrp.net (MIT License)
 */
function sanitize($string, $force_lowercase = true, $anal = false, $trunc = 100) {
	$strip = array("~", "`", "!", "@", "#", "$", "%", "^", "&", "*", "(", ")", "=", "+", "{",
				   "}", "\\", "|", ";", ":", "\"", "'", "&#8216;", "&#8217;", "&#8220;", "&#8221;", "&#8211;", "&#8212;",
				   "—", "–", ",", "<", ".", ">", "/", "?");
	$clean = trim(str_replace($strip, "", strip_tags($string)));
	$clean = preg_replace('/\s+/', "-", $clean);
	$clean = ($anal ? preg_replace("/[^a-zA-Z0-9]/", "", $clean) : $clean);
	$clean = ($trunc ? substr($clean, 0, $trunc) : $clean);
	return ($force_lowercase) ?
		(function_exists('mb_strtolower')) ?
			mb_strtolower($clean, 'UTF-8') :
			strtolower($clean) :
		$clean;
}

/**
 * format_bytes
 * Converts bytes to whathever
 */
function format_bytes($bytes, $round=1) {
	switch($bytes) {
		case $bytes < 1024:
			return $bytes .' B';
		break;
		case $bytes < 1048576:
			 return round($bytes / 1024, $round) .' KB';
		break;
		case $bytes < 1073741824:
			return round($bytes / 1048576, $round) . ' MB';
		break;		
	}
}

/**
 * mb_to_bytes
 * Converts MB to bytes
 */
function mb_to_bytes($mb) {
	return $mb*1048576;
}

/**
 * return_bytes
 * Returns bytes for SIZE + Suffix format
 */
function return_bytes($size){
	switch(strtolower(substr ($size, -2))) {
		case 'kb': return (int)$size * 1024;
		case 'mb': return (int)$size * 1048576;
		case 'gb': return (int)$size * 1073741824;
		default: return $size;
	}
}

/**
 * return_ini_bytes
 * Returns bytes (used for the ini_get functions)
 */
function return_ini_bytes($size){
	switch(strtolower(substr ($size, -1))) {
		case 'k': return (int)$size * 1024;
		case 'm': return (int)$size * 1048576;
		case 'g': return (int)$size * 1073741824;
		default: return $size;
	}
}


/**
 * get_image_target
 * Get the target image based on the storage
 */
function get_image_target($filearray) {

	switch($filearray['storage_id']) {
		case NULL:
			$folder = __CHV_PATH_IMAGES__.date('Y/m/d/', strtotime($filearray['image_date'])); // sqlite no strtotime!
		break;
		case '1':
			$folder = __CHV_PATH_IMAGES__.'old/';
		break;
		case '2':
			$folder = __CHV_PATH_IMAGES__;
		break;
	}
	
	$return = array(
		'image_path' => $folder.$filearray['image_name'].'.'.$filearray['image_type'],
		'image_thumb_path' => $folder.$filearray['image_name'].'.th.'.$filearray['image_type']
	);
	
	return $return;
}

/**
 * recreate_thumb
 * If the thumb doesn't exits it recreate it
 */
function recreate_thumb($image_array) {
	if(!file_exists($image_array["image_thumb_path"])) {
		@require_once(__CHV_PATH_CLASSES__.'class.imageresize.php');
		$thumb = new ImageResize($image_array["image_path"], $image_array["image_thumb_path"], chevereto_config('thumb_width'), chevereto_config('thumb_height'), true);
	}
}

/**
 * must_delete_image_record
 * Delete the image from the dB and the thumb if the image doesn't exists in the filesystem
 */
function must_delete_image_record($id, $image, $thumb, $db) {
	if(!file_exists($image)) {
		@unlink($thumb);
		$db->delete_file($id);
		return true;
	} else {
		return false;
	}
}

/**
 * relative_to_absolute
 * Converts relative path to absolute path
 */
function relative_to_absolute($filepath) {
	return str_replace(__CHV_RELATIVE_ROOT__, __CHV_ROOT_DIR__, str_replace('\\', '/', $filepath));
}

/**
 * relative_to_url
 * Converts relative path to url
 */
function relative_to_url($filepath) {
	return str_replace(__CHV_RELATIVE_ROOT__, __CHV_BASE_URL__, str_replace('\\', '/', $filepath));
}

/**
 * absolute_to_relative
 * Converts absolute path to relative path
 */
function absolute_to_relative($filepath) {
	return str_replace(__CHV_ROOT_DIR__, __CHV_RELATIVE_ROOT__, str_replace('\\', '/', $filepath));
}

/**
 * absolute_to_url
 * Converts absolute path to URL
 */
function absolute_to_url($filepath) {
	if(__CHV_ROOT_DIR__===__CHV_RELATIVE_ROOT__) {
		return __CHV_BASE_URL__.ltrim($filepath, '/');
	}
	return str_replace(__CHV_ROOT_DIR__, __CHV_BASE_URL__, str_replace('\\', '/', $filepath));
}

/**
 * url_to_relative
 * Converts full chevereto URL to relative path
 */
function url_to_relative($url) {
	return str_replace(__CHV_BASE_URL__, __CHV_RELATIVE_ROOT__, $url);
}

/**
 * url_to_absolute
 * Converts full chevereto URL to absolute path
 */
function url_to_absolute($url) {
	return str_replace(__CHV_BASE_URL__, __CHV_ROOT_DIR__, $url);
}

/*
 * chevereto_id
 * This function is a mixture of 2 methods for having a conversion between integers and alphanumerics
 * It converts any integer into a alphanumeric representation (like a youtube id)
 * This also uses a __CHV_CRYPT_SALT__ in order to generate unique conversions
 * Please notice that this is not limited by the 2^31-1 number
 *
 * @inspiration
 *	http://kevin.vanzonneveld.net/techblog/article/create_short_ids_with_php_like_youtube_or_tinyurl/
 *	http://yourls.org/
 *
 */
function chevereto_id($var, $action='encode') {
	$base_chars = "abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ"; // DON'T REPEAT A SINGLE CHAR!
	
	for ($n = 0; $n<strlen($base_chars); $n++) {
		$i[] = substr( $base_chars,$n ,1);
    }
 
    $passhash = hash('sha256',__CHV_CRYPT_SALT__);
    $passhash = (strlen($passhash) < strlen($base_chars)) ? hash('sha512',__CHV_CRYPT_SALT__) : $passhash;
 
    for ($n=0; $n < strlen($base_chars); $n++) {
		$p[] =  substr($passhash, $n ,1);
    }
 
    array_multisort($p, SORT_DESC, $i);
    $base_chars = implode($i);
	
	switch($action) {
		case 'encode':
			$string = '';
			$len = strlen($base_chars);
			while($var >= $len) {
				$mod = bcmod($var, $len);
				$var = bcdiv($var, $len);
				$string = $base_chars[$mod].$string;
			}
			return $base_chars[$var] . $string;
		break;
		case 'decode':
			$integer = 0;
			$var = strrev($var );
			$baselen = strlen( $base_chars );
			$inputlen = strlen( $var );
			for ($i = 0; $i < $inputlen; $i++) {
				$index = strpos($base_chars, $var[$i] );
				$integer = bcadd($integer, bcmul($index, bcpow($baselen, $i)));
			}
			return $integer;
		break;
	}
}

/**
 * encodeID
 * Shorthand fot chevereto_id encode
 */
function encodeID($var) {
	return chevereto_id($var, "encode");
}

/**
 * decodeID
 * Shorthand fot chevereto_id decode
 */
function decodeID($var) {
	return chevereto_id($var, "decode");
}

/**
 * get_mime
 * Gets the mimetype accorginf to your php version
 */
function get_mime($file) {
	// Since in php 5.3 this is a mess...
	/*if(function_exists('finfo_open')) {
		return finfo_file(finfo_open(FILEINFO_MIME_TYPE), $file); 
	} else {
		if(function_exists('mime_content_type')) {
			return mime_content_type($file);
		} else {
			return "application/force-download";
		}
	}*/
	$mimetypes = array(
		"php"	=> "application/x-php",
		"js"	=> "application/x-javascript",
		
		"css"	=> "text/css",
		"html"	=> "text/html",
		"htm"	=> "text/html",
		"txt"	=> "text/plain",
		"xml"	=> "text/xml",
		
		"bmp"	=> "image/bmp",
		"gif"	=> "image/gif",
		"jpg"	=> "image/jpeg",
		"png"	=> "image/png",
		"tiff"	=> "image/tiff",
		"tif"	=> "image/tif",
	);
	$file_mime = $mimetypes[pathinfo($file, PATHINFO_EXTENSION)];
	if(check_value($file_mime)) {
		return $file_mime;
	} else {
		return "application/force-download";
	}
}

/**
 * get_info
 * Retrieves info about the current image file
 */
function get_info($file) {
	$info = getimagesize($file);
	$filesize = filesize($file);
	return array(
		'width'		=> intval($info[0]),
		'height'	=> intval($info[1]),
		'bytes'		=> intval($filesize),
		'size'		=> format_bytes($filesize),
		'mime'		=> strtolower($info['mime']),
		'html'		=> 'width="'.$info[0].'" height="'.$info[1].'"'
		//'bits' => $info['bits'],
		//'channels' => $info['channels'],
	);
}

/**
 * is_local
 * Returns true|false if the server is localhost or not
 */
function is_localhost() {
	return ($_SERVER['SERVER_NAME']=='localhost') ? true : false;
}


/**
 * chevereto_die
 * This function is an alias of php die() but with html error display :1313:
 */
function chevereto_die($error_msg, $title='', $explain=array()) {
	if(!is_array($error_msg) && check_value($error_msg)) $error_msg = array($error_msg);
	$what_happend = $explain[0];
	$solution = $explain[1];
	$doctitle = (check_value($title)) ? $title : 'Error';
	$doctitle .= ' - Chevereto '.get_chevereto_version();
	require_once(__CHV_PATH_CONTENT__.'system/error.php'); // $error_msg are handled here
	die();
}

/**
 * fetch_url
 * uses cURL to get the contents from a url
 */
function fetch_url($url) {
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_TIMEOUT, 120);
	$file_get_contents = curl_exec($ch);
	curl_close($ch);
	return $file_get_contents;
}

/**
 * xml_output
 * Outputs the REST_API array to xml
 */
function xml_output($array=array()) {
	error_reporting(0);
	header("Last-Modified: ".gmdate("D, d M Y H:i:s")."GMT");
	header("Cache-Control: no-cache, must-revalidate");
	header("Pragma: no-cache");
	header("Content-Type:text/xml; charset=UTF-8");
	$out = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
	$out .= "<response>\n";
	$out .= "	<status_code>$array[status_code]</status_code>\n";
	$out .= "	<status_txt>$array[status_txt]</status_txt>\n";
	if(count($array['data'])>0) {
		$out .= "	<data>\n";
		foreach($array['data'] as $key => $value) {
			$out .= "		<$key>$value</$key>\n";
		}
		$out .= "	</data>\n";
	}
	$out .= '</response>';
	echo $out;
}

/**
 * json_output
 * Outputs an array to json
 */
function json_output($array=array(), $callback='') {
	error_reporting(0);
	header("Last-Modified: ".gmdate("D, d M Y H:i:s")."GMT");
	header("Cache-Control: no-cache, must-revalidate");
	header("Pragma: no-cache");
	header("Content-type: application/json; charset=UTF-8");
	if(check_value($callback) and preg_match('/\W/', $callback)) {
		header('HTTP/1.1 400 Bad Request');
		$json_fail = array('status_code' => '400', 'status_txt' => 'Bad Request');
		exit(json_encode($json_fail));
	}
	if(check_value($callback)) {
		print sprintf('%s(%s);', $callback, json_encode($array));
	} else {
		print json_encode($array);
	}
	exit(); // Terminate any further operation
}

/**
 * json_prepare
 * Hide the display errors and blocks the non XMLHttpRequest
 */
function json_prepare() {
	error_reporting(0);
	@ini_set('display_errors', false);
	$http_referer = (isset($_SERVER['HTTP_REFERER'])) ? parse_url($_SERVER['HTTP_REFERER']) : NULL;
	if($_SERVER['HTTP_X_REQUESTED_WITH']!=='XMLHttpRequest' && !preg_match('/'.HTTP_HOST.'/', $http_referer['host']) && !preg_match('/127\.0\.0\.1/', $_SERVER['SERVER_ADDR'])) {
		die(json_output(array('status_code' => 400, 'status_txt'=>'bad request')));
	}
}

/**
 * get_backup_lang
 * Who you gonna call?
 */
function get_backup_lang() {
	global $backup_lang; return $backup_lang;
}

/**
 * get_lang_txt
 * Returns the current lang string
 */
function get_lang_txt($lang_string) {
	global $lang, $backup_lang;
	return (check_value($lang[$lang_string])) ? $lang[$lang_string] : $backup_lang[$lang_string];
}
register_show_function('get_lang_txt', '$lang_string');

/**
 * get_lang_locale
 * Returns the current lang locale string
 */
function get_lang_locale() {
	global $lang_code;
	$localeMap = array(
		'ca' => 'ca_ES',
		'cs' => 'cs_CZ',
		'cy' => 'cy_GB',
		'da' => 'da_DK',
		'de' => 'de_DE',
		'eu' => 'eu_ES',
		'en' => 'en_US',
		'es' => 'es_ES',
		'fi' => 'fi_FI',
		'fr' => 'fr_FR',
		'gl' => 'gl_ES',
		'hu' => 'hu_HU',
		'it' => 'it_IT',
		'ja' => 'ja_JP',
		'ko' => 'ko_KR',
		'nb' => 'nb_NO',
		'nl' => 'nl_NL',
		'pl' => 'pl_PL',
		'pt' => 'pt_BR',
		'ro' => 'ro_RO',
		'ru' => 'ru_RU',
		'sk' => 'sk_SK',
		'sl' => 'sl_SI',
		'sv' => 'sv_SE',
		'th' => 'th_TH',
		'tr' => 'tr_TR',
		'ku' => 'ku_TR',
		'zh_CN' => 'zh_CN',
		'zh_TW' => 'zh_TW',
		'af' => 'af_ZA',
		'sq' => 'sq_AL',
		'hy' => 'hy_AM',
		'az' => 'az_AZ',
		'be' => 'be_BY',
		'bs' => 'bs_BA',
		'bg' => 'bg_BG',
		'hr' => 'hr_HR',
		'eo' => 'eo_EO',
		'et' => 'et_EE',
		'fo' => 'fo_FO',
		'ka' => 'ka_GE',
		'el' => 'el_GR',
		'hi' => 'hi_IN',
		'is' => 'is_IS',
		'id' => 'id_ID',
		'ga' => 'ga_IE',
		'jv' => 'jv_ID',
		'kk' => 'kk_KZ',
		'la' => 'la_VA',
		'lv' => 'lv_LV',
		'lt' => 'lt_LT',
		'mk' => 'mk_MK',
		'mg' => 'mg_MG',
		'ms' => 'ms_MY',
		'mt' => 'mt_MT',
		'mn' => 'mn_MN',
		'ne' => 'ne_NP',
		'rm' => 'rm_CH',
		'sr' => 'sr_RS',
		'so' => 'so_SO',
		'sw' => 'sw_KE',
		'tl' => 'tl_PH',
		'uk' => 'uk_UA',
		'uz' => 'uz_UZ',
		'vi' => 'vi_VN',
		'zu' => 'zu_ZA',
		'ar' => 'ar_AR',
		'he' => 'he_IL',
		'ur' => 'ur_PK',
		'fa' => 'fa_IR',
		'sy' => 'sy_SY',
		'gn' => 'gn_PY'
	);
	return (check_value($localeMap[$lang_code])) ? $localeMap[$lang_code] : 'en_US';
}
register_show_function('get_lang_locale');

/**
 * htaccess
 * This creates .htaccess file on the target dir using the param rules
 * This can be also ussed to add rules to a existing .htaccess file
 */
function htaccess($rules, $directory, $before='') {
	$htaccess = $directory.'.htaccess';
	switch($rules) {
		case 'static':
			$rules = '<Files .*>'."\n".
					 'order allow,deny'."\n".
					 'deny from all'."\n".
					 '</Files>'."\n\n". 
					 'AddHandler cgi-script .php .php3 .phtml .pl .py .jsp .asp .htm .shtml .sh .cgi .fcgi'."\n".
					 'Options -ExecCGI';
		break;
		case 'deny':
			$rules = 'deny from all';
		break;
	}
	
	if(file_exists($htaccess)) {
		$fgc = file_get_contents($htaccess);
		if(strpos($fgc, $rules)) {
			$done = true;
		} else {
			if(check_value($before)) {
				$rules = str_replace($before, $rules."\n".$before, $fgc);
				$f = 'w';
			} else {
				$rules = "\n\n".$rules;
				$f = 'a';
			}
		}
	} else {
		$f = 'w';
	}
	
	if(!$done) {
		$fh = @fopen($htaccess, $f);
		if(!$fh) return false;
		if(fwrite($fh, $rules)) {
			@fclose($fh); return true;
		} else {
			@fclose($fh); return false;
		}
	} else {
		return true;
	}
}

/**
 * check_install
 * This checks folders + permissions, .php files and settings.
 */
function check_install(){
	global $config, $install_errors;
	
	// Error friendly messages
	$requirements_error = array(
		'There is a problem regarding server requirements. This means that Chevereto can\'t run because of the following:',
		'Please notice that this issue is because your server setup. If you want to run Chevereto please contact your hosting company or system admin regarding this report.'
	);
	$folder_error = array(
		'There is a problem regarding folders. This means that Chevereto can\'t run because one or more folders required doesn\'t exists. The missing folders are:',
		'Please double-check your current setup for the missing files.'
	);
	$admin_folder_error = array(
		'There is a problem regarding the admin folder and Chevereto won\'t run because the admin folder doesn\'t exists.',
		'Please double-check <code>$config[\'admin_folder\']</code> in the <code>includes/config.php</code> file.'
	);	
	$htaccess = 'In some operating systems this files are hidden, therefore you can\'t upload them. You need to <a href="http://www.google.com/search?q=show+hidden+htaccess" target="_blank">show this file</a> and then upload it.';
	$htaccess_error = array(
		'Some <code>.htaccess</code> file(s) doesn\'t exists and the system can\'t create this files.', $htaccess
	);
	$root_htaccess_error = array(
		'The <code>.htaccess</code> file doesn\'t exists in the Chevereto root directory. This file must be uploaded to run Chevereto.', $htaccess
	);
	$file_error = array(
		str_replace('folders', 'files', $folder_error[0]),
		str_replace('folders', 'files', $folder_error[1])
	);
	$permission_error = array(
		'There is a problem regarding permissions. This means that Chevereto can\'t upload files because of the following:',
		'Chevereto needs a way to write in this folders. You can do this by doing <a href="http://www.google.com/search?q=chmod+777" target="_blank">chmod 0777</a> on the above folders or use <a href="http://www.suphp.org/" target="_blank">suPHP</a> or <a href="http://httpd.apache.org/docs/current/suexec.html" target="_blank">suEXEC</a> on your server setup.'
	);
	$config_error = array(
		'There is a problem regarding your config setup. This means that Chevereto won\'t run because the config is not valid:',
		'Please double-check your settings in the <code>includes/config.php</code> file.'
	);
	$definitions_error = array(
		'Please take note that you must edit the <code>definitions.php</code> file the first time that you install Chevereto.',
		'Please double-check your definitions in the <code>includes/definitions.php</code> file.'
	);
	$theme_error = array(
		'There is a problem regarding your current theme. This means that Chevereto won\'t run because <code>'.$config['theme'].'</code> theme has missing files:',
		'Please double-check the theme hierarchy.'
	);
	$theme_data_error = array(
		'There is a problem regarding your current theme data. This means that Chevereto won\'t run because <code>'.$config['theme'].'</code> theme has not valid theme data in <code>style.css</code> header comments:',
		'Please double-check the <code>style.css</code> header comments and refer to the <a href="http://chevereto.com/docs#themes">theme documentation</a> page.'
	);
	$virtual_folder_error = array(
		'There is a problem regarding your virtual folders setup:',
		'Either you set different values for virtual folders in <code>includes/config.php</code> or you delete this directories.'
	);
	
	// Check for the server requirements
	if(!check_requirements()) {
		chevereto_die($install_errors, 'System error', $requirements_error);
	}
	
	// Check for the image folders
	$image_folders = array(__CHV_PATH_IMAGES__);
	if(!check_files_folders($image_folders, 'Directory')) {
		chevereto_die($install_errors, 'Folder error', $folder_error);
	}
	if(!check_permissions($image_folders)) {
		chevereto_die($install_errors, 'Permissions error', $permission_error);
	}
	
	// Check for virtual folders
	$virtual_folders = array(sanitize_path($config['virtual_folder_image']), sanitize_path($config['virtual_folder_uploaded']));
	foreach($virtual_folders as $folder) {
		if(file_exists(__CHV_ROOT_DIR__.$folder)) {
			$install_errors[] = 'The directory <code>'.__CHV_RELATIVE_ROOT__.$folder.'</code> must not exists';
		}
	}
	if(count($install_errors)>0) chevereto_die($install_errors, 'Virtual folders error', $virtual_folder_error);
	
	// Check for upload.php
	if(!file_exists(__CHV_ROOT_DIR__.'upload.php')) {
		chevereto_die('Can\'t find <code>upload.php</code>', 'Missing upload.php', $file_error);
	}
	
	// Check for the root .htaccess file
	if(!file_exists(__CHV_ROOT_DIR__.'.htaccess')) {
		chevereto_die('', '.htaccess error', $root_htaccess_error);
	}
	
	// Admin folder
	if(!check_value($config["admin_folder"])) $config["admin_folder"] = "admin";
	if(!file_exists(__CHV_PATH_ADMIN__)) {
		chevereto_die('', 'Admin folder doesn\'t exists', $admin_folder_error);
	}
	
	// Check for the other .htaccess files
	$htaccess_files = array(__CHV_PATH_IMAGES__, __CHV_PATH_INCLUDES__, __CHV_PATH_ADMIN_INCLUDES__);
	foreach($htaccess_files as $dir) {
		if(!file_exists($dir.'.htaccess')) {
			switch($dir) {
				case __CHV_PATH_IMAGES__:
					$rules = 'static';
				break;
				case __CHV_PATH_INCLUDES__:
				case __CHV_PATH_ADMIN_INCLUDES__:
					$rules = 'deny';
				break;
			}
			if(!htaccess($rules, $dir)) {
				$install_errors[] = 'Can\'t create <code>'.$dir.'.htaccess</code> file. Please upload the <code>.htaccess</code> file to the target dir';
			}
		}
	}
	if(count($install_errors)>0) chevereto_die($install_errors, '.htaccess error', $htaccess_error);
	
	// Files check
	$include_files = array(
		'chevereto.php',
		'uploader.php',
		'shorturl.php',
		'definitions.php',
		'template.functions.php'
	);
	$classes_files = array(
		'class.handler.php',
		'class.db.php',
		'class.upload.php',
		'class.filelist.php',
		'class.imageresize.php',
		'class.imageconvert.php',
		'class.minify.php',
		'class.shorturl.php'
	);
	
	$system_files = array(
		'login.php',
		'error.php',
		'style.css',
		'img/chevereto.png',
		'img/logo.png',
		'img/ico-warn.png',
		'img/background.png',
		'img/bkg-content.png'
	);
	$system_files_minify = array('style.css');
	$system_files = array_merge_minified($system_files, $system_files_minify);
	
	$system_js_files = array(
		'uploadify.swf',
		'ZeroClipboard.swf',
		'pref.php'
	);
	$system_js_files_minify = array('jquery.js', 'chevereto.js', 'functions.js', 'jquery.uploadify-3.1_chevereto.js');
	$system_js_files = array_merge_minified($system_js_files, $system_js_files_minify);
	
	// Admin files
	$admin_classes_files = array(
		'class.adminhandler.php',
		'class.manage.php'
	);
	$admin_system_files = array(
		'header.php',
		'filemanager.php'
	);
	$admin_system_files_minify = array('style.css', 'js/admin.js');
	$admin_system_files = array_merge_minified($admin_system_files, $admin_system_files_minify);
	
	foreach($include_files as $key => $value) $include_files[$key] = __CHV_PATH_INCLUDES__.$value;
	foreach($classes_files as $key => $value) $classes_files[$key] = __CHV_PATH_CLASSES__.$value;
	foreach($system_files as $key => $value) $system_files[$key] = __CHV_PATH_SYSTEM__.$value;
	foreach($system_js_files as $key => $value) $system_js_files[$key] = __CHV_PATH_SYSTEM_JS__.$value;
	foreach($admin_classes_files as $key => $value) $admin_classes_files[$key] = __CHV_PATH_ADMIN_CLASSES__.$value;
	foreach($admin_system_files as $key => $value) $admin_system_files[$key] = __CHV_PATH_ADMIN_SYSTEM__.$value;
	
	/*** The complete file check array ***/
	$check_files = array(
		'Includes'			=> $include_files,
		'Classes'			=> $classes_files,
		'System Files'		=> array_merge($system_files, $system_js_files),
		'Admin .htaccess'	=> array(__CHV_PATH_ADMIN_INCLUDES__.'.htaccess'),
		'Admin Classes'		=> $admin_classes_files,
		'Admin System'		=> $admin_system_files
	);
	
	foreach($check_files as $key => $value) {
		check_files_folders($value, 'File');
	}
	if((count($install_errors)>0)) {
		chevereto_die($install_errors, 'Setup error', $file_error);
	}
		
	if(!check_config()) {
		chevereto_die($install_errors, 'Config error', $config_error);
	}
	
	if(!check_definitions()) {
		chevereto_die($install_errors, 'Please change definitions.php', $definitions_error);
	}
	
	if(!check_theme()) {
		chevereto_die($install_errors, 'Theme error', $theme_error);
	}
	
	if(!check_theme_data()) {
		chevereto_die($install_errors, 'Theme data error', $theme_data_error);
	}

}

/**
 * check_requirements
 * This checks upload limit, GD library, Domain and cURL. 
 */
function check_requirements() {
	global $install_errors;
	
	// Try to fix the sessions in crap setups (OVH)
	@ini_set('session.gc_divisor', 100);
	@ini_set('session.gc_probability', true);
	@ini_set('session.use_trans_sid', false);
	@ini_set('session.use_only_cookies', true);
	@ini_set('session.hash_bits_per_character', 4);
	
	$mod_rw_error = 'Apache <a href="http://httpd.apache.org/docs/2.1/rewrite/rewrite_intro.html" target="_blank">mod_rewrite</a> is not enabled.';
	
	if(version_compare(PHP_VERSION, '5.2.0', '<'))
		$install_errors[] = 'Your server is currently running PHP version '.PHP_VERSION.' and Chevereto needs atleast PHP 5.2.0';
	
	if(!extension_loaded('curl') && !function_exists('curl_init'))
		$install_errors[] = '<a href="http://curl.haxx.se/" target="_blank">cURL</a> is not enabled on your current server setup.';
	
	if(!function_exists('curl_exec'))
		$install_errors[] = '<b>curl_exec()</b> function is disabled, you have to enable this function on your php.ini';
	
	if(function_exists('apache_get_modules')) {
		if(!in_array('mod_rewrite', apache_get_modules()))
			$install_errors[] = $mod_rw_error;
	} else {
		// As today (Jun 11, 2012) i haven't found a better way to test mod_rewrite in CGI setups. The phpinfo() method is not fail safe either.
	}
	
	if (!extension_loaded('gd') and !function_exists('gd_info')) {
		$install_errors[] = '<a href="http://www.libgd.org" target="_blank">GD Library</a> is not enabled.';
	} else {
		$imagetype_fail = 'image support is not enabled in your current PHP setup (GD Library).';
		if(!imagetypes() & IMG_PNG)  $install_errors[] = 'PNG '.$imagetype_fail;
		if(!imagetypes() & IMG_GIF)  $install_errors[] = 'GIF '.$imagetype_fail;
		if(!imagetypes() & IMG_JPG)  $install_errors[] = 'JPG '.$imagetype_fail;
		if(!imagetypes() & IMG_WBMP) $install_errors[] = 'BMP '.$imagetype_fail;
	}
	
	/*
	$test_session_file = session_save_path().'/'.time();
	if(!@fopen($test_session_file, 'w+')) {
		$install_errors[] = 'PHP can\'t write/read in the session path <code>'.session_save_path().'</code>. Your server setup doesn\'t have the right PHP/Apache permissions over this folder.';
		$install_errors[] = 'Please repair the permissions on this folder or specify a new one on <code>php.ini</code>';
	} else {
		@unlink($test_session_file);
	}
	*/
	
	$bcmath_functions = array('bcadd', 'bcmul', 'bcpow', 'bcmod', 'bcdiv');
	foreach($bcmath_functions as $bcmath_function) {
		if(!function_exists($bcmath_function)) {
			$install_errors[] = '<a href="http://php.net/manual/function.'.$bcmath_function.'.php" target="_blank">'.$bcmath_function.'</a> function is not defined. You need to re-install the BC Math functions.';
		}
	}
	
	if(!extension_loaded('pdo')) {
		$install_errors[] = 'PHP Data Objects (<a href="http://www.php.net/manual/book.pdo.php">PDO</a>) is not loaded.';
	}
	
	if(!extension_loaded('pdo_mysql')) {
		$install_errors[] = 'MySQL Functions (<a href="http://www.php.net/manual/ref.pdo-mysql.php" target="_blank">PDO_MYSQL</a>) is not loaded.';
	}

	if(count($install_errors)==0) return true;
}

/**
 * check_files_folders
 * This checks for filer or folders existence
 * If the folder doesn't exists the script will attempt to create it.
 */
function check_files_folders($elements, $type) {
	global $install_errors;
	$type = strtolower($type);
	foreach ($elements as $element) {
		if($type=='directory') {
			@mkdir($element);
		}
		if(preg_match('/.+\.min\.(js|css)$/i', $element)) {
			if(conditional_config('minify') && !file_exists($element)) {
				require_once(__CHV_PATH_CLASSES__.'class.minify.php');
				$minify = new Minify();
				try {
					$minify->addSource(preg_replace('/(.+)\.min(\.(js|css))$/i', '$1$2', $element));
					$minify->exec();
				} catch (MinifyException $e) {
					if(conditional_config('error_reporting')) debug($e->getMessage());
				}
			
			}
		}
		if(!file_exists($element)) {
			$install_errors[] = '<code>'.absolute_to_relative($element).'</code>';			
		}
	}
	if(count($install_errors)==0) return true;
}


/**
 * check_permissions
 * Check the current permission on a array directory
 */
function check_permissions($dirs) {
	global $install_errors;
	foreach ($dirs as $dir) {
		if(!is_writable($dir)) {
			$install_errors[] = 'No write permission in <code>'.absolute_to_relative($dir).'</code> directory.';
		}
	}
	if(count($install_errors)==0) return true;
}

/**
 * check_config
 * This checks the script configuration... Like upload limit, thumbs, etc. 
 */
function check_config() {
	global $config, $install_errors;
	
	if(!defined('HTTP_HOST')) {
		$install_errors[] = 'Can\'t resolve <code>HTTP_HOST</code>. Please check at the bottom of <code>config.php</code>';
	}
	
	// Upload limit vs php.ini value -> http://php.net/manual/ini.php
	$ini_upload_bytes = return_bytes(trim(ini_get('upload_max_filesize')).'B');
	$max_size_bytes =  return_bytes($config['max_filesize']);
	if(!is_numeric($max_size_bytes)) {
		$install_errors[] = 'Invalid numeric value in <code>$config[\'max_filesize\']</code>';
	} else {
		if($ini_upload_bytes<$max_size_bytes) {
			$install_errors[] = 'Max. image size ('.$config['max_filesize'].') is greater than the value in <code>php.ini</code> ('.format_bytes($ini_upload_bytes).')';
		}
	}
	
	if(!is_int($config['thumb_width'])) {
		$install_errors[] = 'Invalid thumb size width in <code>$config[\'thumb_width\']</code>';
	}
	if(!is_int($config['thumb_height'])) {
		$install_errors[] = 'Invalid thumb size height in <code>$config[\'thumb_height\']</code>';
	}
	
	if(!is_int($config['min_resize_size']) || $config['min_resize_size'] < 0) {
		$install_errors[] = 'Invalid minimum resize size in <code>$config[\'min_resize_size\']</code>';
	}
	if(!is_int($config['max_resize_size']) || $config['max_resize_size'] < 0) {
		$install_errors[] = 'Invalid maximum resize size in <code>$config[\'max_resize_size\']</code>';
	}
	if(is_int($config['min_resize_size']) && is_int($config['max_resize_size']) && $config['min_resize_size'] > $config['max_resize_size']) {
		$install_errors[] = 'Minimum resize size can\'t be larger than maximum resize size. Please check <code>$config[\'min_resize_size\']</code> and <code>$config[\'max_resize_size\']</code>';
	}
	
	if(!conditional_config('multiupload')) {
		$config['multiupload_limit'] = 1;
	} else {
		if($config['multiupload_limit']<=0 || $config['multiupload_limit']=='') {
			$config['multiupload_limit'] = 0;
		}
	}
	
	if(!check_value(chevereto_config('file_naming')) || !in_array(chevereto_config('file_naming'), array('original', 'random', 'mixed'))) {
		$config['file_naming'] = 'original';
	}

	if(!is_numeric($config['multiupload_limit']) && !is_bool($config['multiupload_limit'])) {
		$install_errors[] = 'Invalid multiupload limit value in <code>$config[\'multiupload_limit\']</code>';
	}
	
	if($config['multiupload_limit']>100) {
		$install_errors[] = 'Multiupload limit value can\'t be higher than 100 in <code>$config[\'multiupload_limit\']</code>';
	}
	
	if($config['short_url_service']=='bitly') {
		$bitly_status = fetch_url('http://api.bit.ly/v3/validate?x_login='.$config['short_url_user'].'&x_apiKey='.$config['short_url_keypass'].'&apiKey='.$config['short_url_keypass'].'&login='.$config['short_url_user'].'&format=json');
		$bitly_json = json_decode($bitly_status);
		
		if($bitly_json->data->valid!==1) {
			$install_errors[] = 'The <a href="http://bit.ly/" target="_blank">bit.ly</a> user/api is invalid. bitly server says <code>'.$bitly_json->status_txt.'</code>. Please double check your data.';
		}
	}

	// Facebook comments
	if(use_facebook_comments() && !check_value($config['facebook_app_id'])) {
		$install_errors[] = 'You are are trying to use Facebook comments but <code>$config[\'facebook_app_id\']</code> is not setted.';
	}
	
	// Virtual folders
	foreach(array('virtual_folder_image', 'virtual_folder_uploaded') as $value) {
		if(!check_value($config[$value])) {
			$install_errors[] = '<code>$config[\''.$value.'\']</code> is not setted.';
		}
	}
	
	// Passwords
	if($config['user_password']==$config['admin_password']) {
		$install_errors[] = 'Admin and user passwords must be different. Please check <code>$config[\'admin_password\']</code> and <code>$config[\'user_password\']</code>';
	}
	
	// Flood report email?
	if(check_value($config['flood_report_email']) && !check_email_address($config['flood_report_email'])) {
		$install_errors[] = 'It appears that <code>$config[\'flood_report_email\']</code> has a invalid email address';
	}
	
	// Watermark
	if(conditional_config('watermark_enable')) {
		define('__CHV_WATERMARK_FILE__', __CHV_ROOT_DIR__.ltrim($config['watermark_image'], '/'));
		
		if(!is_int($config['watermark_margin'])) {
			$install_errors[] = 'Watermark margin must be integer in <code>$config[\'watermark_margin\']</code>';
		}
		if(!is_int($config['watermark_opacity'])) {
			$install_errors[] = 'Watermark opacity must be integer in <code>$config[\'watermark_opacity\']</code>';
		}
		if($config['watermark_opacity'] > 100 or $config['watermark_opacity'] < 0) {
			$install_errors[] = 'Watermark opacity value out of limis ('.$config['watermark_opacity'].'). <code>$config[\'watermark_opacity\']</code> must be in the range 0 to 100';
		}
		
		// Watermark position
		if(!check_value($config['watermark_position'])) {
			$config['watermark_position'] = 'center center';
		}
		
		$watermark_position = explode(' ', strtolower($config['watermark_position']));
		if(!isset($watermark_position[1])) $watermark_position[1] = 'center';
			
		if(preg_match('/^left|center|right$/', $watermark_position[0])) {
			$config['watermark_x_position'] = $watermark_position[0];
		} else {
			$install_errors[] = 'Invalid watermark horizontal position in <code>$config[\'watermark_position\']</code>';
		}
		
		if(preg_match('/^top|center|bottom$/', $watermark_position[1])) {
			$config['watermark_y_position'] = $watermark_position[1];
		} else {
			$install_errors[] = 'Invalid watermark vertical position in <code>$config[\'watermark_position\']</code>';
		}
		
		if(!file_exists(__CHV_WATERMARK_FILE__)) {
			$install_errors[] = 'Watermark image file doesn\'t exists. Please check the path in <code>$config[\'watermark_image\']</code>';
		} else {
			$watermark_image_info = get_info(__CHV_WATERMARK_FILE__);
			if($watermark_image_info['mime']!=='image/png') {
				$install_errors[] = 'Watermark image file must be a PNG image in <code>$config[\'watermark_image\']</code>';
			}
		}
	}
	
	// Flood limits
	$flood_limits = array('minute','hour','day','week','month');
	$flood_value_error = false;
	foreach($flood_limits as $value) {
		if(!check_value($config['max_uploads_per_'.$value]) || !is_numeric($config['max_uploads_per_'.$value])) {
			$install_errors[] = 'Invalid config value in <code>$config[\''.$value.'\']</code>';
			$flood_value_error = true;
		}
	}
	if($flood_value_error==false) {
		$flood_lower_than = array(
			'minute' => array('hour','day','week','month'),
			'hour'	 => array('day','week','month'),
			'day'	 => array('week','month'),
			'week'	 => array('month')
		);
		foreach($flood_lower_than as $period => $lower_than) {
			foreach($lower_than as $value) {
				if($config['max_uploads_per_'.$period] >= $config['max_uploads_per_'.$value]) {
					$install_errors[] = '<code>max_uploads_per_'.$period.'</code> must be lower than <code>max_uploads_per_'.$value.'</code>';
				}
			}
		}
	}
	
	// dB settings
	foreach(array('db_host', 'db_name', 'db_user') as $value) {
		if(!check_value($config[$value])) {
			$install_errors[] = '<code>$config[\''.$value.'\']</code>';
		}
	}
	
	if(count($install_errors)==0) {
		require_once(__CHV_PATH_CLASSES__.'class.db.php');
		$dB = new dB();
		if($dB->dead) {
			chevereto_die('<code>'.$dB->error.'</code>', 'Database error', array('The system has encountered a error when it try to connect to the database server.', 'Please note this error and if you need help go to <a href="http://chevereto.com/support/">Chevereto support</a>.'));
		} else { // Check maintenance mode
			if($dB->get_option('maintenance') && !defined('SKIP_MAINTENANCE')) {
				$config['maintenance'] = true;
			}
		}
	}
	
	return (count($install_errors)==0) ? true : false;
}

/**
 * check_definitions
 * This checks the user constants.
 */
function check_definitions() {
	global $config, $install_errors;
	if(is_localhost()) return true;
	
	// Crypt salt
	if(!check_value(__CHV_CRYPT_SALT__)) {
		$install_errors[] = 'You need to set <code>__CHV_CRYPT_SALT__</code> in <code>includes/definitions.php</code>. You only need to change this only the first time you install Chevereto';
	}
	if(__CHV_CRYPT_SALT__=='changeme') {
		$install_errors[] = 'You haven\'t changed the default <code>__CHV_CRYPT_SALT__</code>. Please set this value in <code>includes/definitions.php</code>';
	}
	if(count($install_errors)==0) return true;
}

/**
 * check_theme
 * This cheks the consistency of the current theme... First the folder and then the files.
 */
function check_theme() {
	global $config, $install_errors;
	$theme_files = array(
		'index.php',
		'header.php',
		'footer.php',
		'uploaded.php',
		'view.php',
		'404.php',
		'delete.php'
	);
	$theme_files_minify = array('theme.js', 'style.css', 'uploadify.css');
	$theme_files = array_merge_minified($theme_files, $theme_files_minify);
	
	foreach($theme_files as $key => $value) $theme_files[$key] = __CHV_PATH_THEME__.$value;
	
	if(!file_exists(__CHV_PATH_THEME__)) {
		$install_errors[] = 'Theme directory <code>'.absolute_to_relative(__CHV_PATH_THEME__).'</code> doesn\'t exists.';
	} else {
		check_files_folders($theme_files, 'File');
	}
	
	if(count($install_errors)==0) return true;
}

/**
 * check_theme_data
 * This cheks the consistency of the current theme data.
 */
function check_theme_data() {
	global $install_errors;
	
	$theme_data = get_theme_data();
	
	if(!check_value($theme_data['Name'])) {
		$install_errors[] = '<code>Theme Name</code> has no value.';
	}
	if(!check_value($theme_data['Chevereto'])) {
		$install_errors[] = 'There is no value on <code>@Chevereto</code> wich indicates the version compatibility.';
	}
	
	if(count($install_errors)==0) return true;
}

/**
 * get_theme_data
 * This gets the data of the current theme.
 * @Inspired from WordPress
 */
function get_theme_data() {
	$theme_file = __CHV_PATH_THEME__.'style.css';
	$theme_data = implode('', file($theme_file));
	$theme_data = str_replace ('\r', '\n',$theme_data );
	
	if (preg_match('|Theme Name:(.*)$|mi', $theme_data, $theme_name))
		$name = clean_header_comment($theme_name[1]);

	if (preg_match('|Theme URL:(.*)$|mi', $theme_data, $theme_url))
		$url = clean_header_comment($theme_url[1]);
		
	if (preg_match('|Version:(.*)$|mi', $theme_data, $theme_version))
		$version = clean_header_comment($theme_version[1]);
	
	if (preg_match('|Author:(.*)$|mi', $theme_data, $theme_author))
		$author = clean_header_comment($theme_author[1]);
	
	if (preg_match('|@Chevereto:(.*)$|mi', $theme_data, $theme_chevereto))
		$chevereto = clean_header_comment($theme_chevereto[1]);
		
	return array(
		'Name' => $name,
		'URL' => $url,
		'Version' => $version,
		'Author' => $author,
		'Chevereto' => $chevereto
	);
}

/**
 * get execution time in microseconds
 * Returns float Execution time at this point of call
 */
function get_execution_time() {
	return microtime(true) - __CHV_TIME_EXECUTION_STARTS__;
}
register_show_function('get_execution_time');

/**
 * is_upload_flood
 * Returns true or false if the script spot flood upload
 */
function is_upload_flood() {
	if(is_localhost() || is_admin() || !conditional_config('flood_protection')) return false;
	
	global $dB;

	$flood = $dB->query_fetch_single("
		SELECT
			COUNT(IF(image_date >= DATE_SUB(NOW(), INTERVAL 1 MINUTE), 1, NULL)) AS minute,
			COUNT(IF(image_date >= DATE_SUB(NOW(), INTERVAL 1 HOUR), 1, NULL)) AS hour,
			COUNT(IF(image_date >= DATE_SUB(NOW(), INTERVAL 1 DAY), 1, NULL)) AS day,
			COUNT(IF(image_date >= DATE_SUB(NOW(), INTERVAL 1 WEEK), 1, NULL)) AS week,
			COUNT(IF(image_date >= DATE_SUB(NOW(), INTERVAL 1 MONTH), 1, NULL)) AS month
		FROM chv_images WHERE uploader_ip=? AND image_date >= DATE_SUB(NOW(), INTERVAL 1 MONTH)",
		$_SERVER['REMOTE_ADDR']);
	
	if( chevereto_config('max_uploads_per_minute') > 0 && $flood['minute'] >= chevereto_config('max_uploads_per_minute') ||
		chevereto_config('max_uploads_per_hour')   > 0 && $flood['hour']   >= chevereto_config('max_uploads_per_hour') ||
		chevereto_config('max_uploads_per_day')	   > 0 && $flood['day']	   >= chevereto_config('max_uploads_per_day') ||
		chevereto_config('max_uploads_per_week')   > 0 && $flood['week']   >= chevereto_config('max_uploads_per_week') ||
		chevereto_config('max_uploads_per_month')  > 0 && $flood['month']  >= chevereto_config('max_uploads_per_month')) {
			$email_report = chevereto_config('flood_report_email');
			if(check_value($email_report)) {
				$message_report .= 'User IP '.$_SERVER['REMOTE_ADDR']."\n\n";
				$message_report .= 'Uploads per time period'."\n";
				$message_report .= 'Minute: '.$flood['minute']."\n";
				$message_report .= 'Hour: '.$flood['hour']."\n";
				$message_report .= 'Week: '.$flood['day']."\n";
				$message_report .= 'Month: '.$flood['week']."\n";
				@mail($email_report, chevereto_config('site_name').' Flood report ('.$_SERVER['REMOTE_ADDR'].')', $message_report, "From: Chevereto Report <report@".HTTP_HOST.">");
			}		
			return true;
	}
}

/**
 * is_invalid_request
 * Tells if the request comes from a external site
 */
function is_invalid_request() {
	$http_referer = (isset($_SERVER['HTTP_REFERER'])) ? parse_url($_SERVER['HTTP_REFERER']) : NULL;	
	if(!check_value($http_referer)) {
		$referal = HTTP_HOST;
	} else {
		$referal = (isset($http_referer['port']) && !empty($http_referer['port'])) ? $http_referer['host'].':'.$http_referer['port'] : $http_referer['host'];
	}
	return ($referal !== HTTP_HOST) ? true : false;	
}

/**
 * status_header
 * Set HTTP status header from status code
 * @Inspired from WordPress
 */
function status_header($code) {
	$desc = get_status_header_desc($code);
	if (empty($desc)) return false;
	$protocol = $_SERVER['SERVER_PROTOCOL'];
	if('HTTP/1.1' != $protocol && 'HTTP/1.0' != $protocol) $protocol = 'HTTP/1.0';
	$status_header = "$protocol $code $desc";
	return @header($status_header, true, $code);
}

/**
 * get_status_header_desc
 * Gets header desc according to it's code
 * @Inspired from WordPress
 */
function get_status_header_desc($code) {
	$codes_to_desc = array(
			100 => 'Continue',
			101 => 'Switching Protocols',
			102 => 'Processing',

			200 => 'OK',
			201 => 'Created',
			202 => 'Accepted',
			203 => 'Non-Authoritative Information',
			204 => 'No Content',
			205 => 'Reset Content',
			206 => 'Partial Content',
			207 => 'Multi-Status',
			226 => 'IM Used',

			300 => 'Multiple Choices',
			301 => 'Moved Permanently',
			302 => 'Found',
			303 => 'See Other',
			304 => 'Not Modified',
			305 => 'Use Proxy',
			306 => 'Reserved',
			307 => 'Temporary Redirect',

			400 => 'Bad Request',
			401 => 'Unauthorized',
			402 => 'Payment Required',
			403 => 'Forbidden',
			404 => 'Not Found',
			405 => 'Method Not Allowed',
			406 => 'Not Acceptable',
			407 => 'Proxy Authentication Required',
			408 => 'Request Timeout',
			409 => 'Conflict',
			410 => 'Gone',
			411 => 'Length Required',
			412 => 'Precondition Failed',
			413 => 'Request Entity Too Large',
			414 => 'Request-URI Too Long',
			415 => 'Unsupported Media Type',
			416 => 'Requested Range Not Satisfiable',
			417 => 'Expectation Failed',
			422 => 'Unprocessable Entity',
			423 => 'Locked',
			424 => 'Failed Dependency',
			426 => 'Upgrade Required',

			500 => 'Internal Server Error',
			501 => 'Not Implemented',
			502 => 'Bad Gateway',
			503 => 'Service Unavailable',
			504 => 'Gateway Timeout',
			505 => 'HTTP Version Not Supported',
			506 => 'Variant Also Negotiates',
			507 => 'Insufficient Storage',
			510 => 'Not Extended'
	);
	
	if(check_value($codes_to_desc[$code])) {
		return $codes_to_desc[$code];	
	}
	
}

/**
 * clean_header_comment
 * @Inspired from WordPress
 */
function clean_header_comment($str) {
	return trim(preg_replace("/\s*(?:\*\/|\?>).*/", '', $str));
}

/**
 * check_email_address
 * This cheks for real email adress.. Uses by now for the contact page demo.
 */
function check_email_address($email) {
	$isValid = true;
	$atIndex = strrpos($email, "@");
	if (is_bool($atIndex) && !$atIndex) {
		$isValid = false;
	}
	else {
		$domain = substr($email, $atIndex+1);
		$local = substr($email, 0, $atIndex);
		$localLen = strlen($local);
		$domainLen = strlen($domain);
		if ($localLen < 1 || $localLen > 64) {
			// local part length exceeded
			$isValid = false;
		}
		else if ($domainLen < 1 || $domainLen > 255) {
			// domain part length exceeded
			$isValid = false;
		}
		else if ($local[0] == '.' || $local[$localLen-1] == '.') {
			// local part starts or ends with '.'
			$isValid = false;
		}
		else if (preg_match('/\\.\\./', $local)) {
			// local part has two consecutive dots
			$isValid = false;
		}
		else if (!preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain)) {
			// character not valid in domain part
			$isValid = false;
		}
		else if (preg_match('/\\.\\./', $domain)) {
			// domain part has two consecutive dots
			$isValid = false;
		}
		else if (!preg_match('/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/',
		str_replace("\\\\","",$local)))
		{
		 // character not valid in local part unless 
		 // local part is quoted
		 if (!preg_match('/^"(\\\\"|[^"])+"$/', str_replace("\\\\","",$local))) {
			$isValid = false;
		 }
		}
		if ($isValid && !(checkdnsrr($domain,"MX") || checkdnsrr($domain,"A"))) {
			// domain not found in DNS
			$isValid = false;
		}
	}
	return $isValid;
}

/**
 * is_url
 * This will tell if the string is an URL.
 */
function is_url($string) {
	return filter_var($string, FILTER_VALIDATE_URL);	
}

/**
 * is_image_url
 * This will tell if the string is an image URL.
 */
function is_image_url($string) {
	return (preg_match("/(?:ftp|https?):\/\/(?:[-\w])+([-\w\.])*\.[a-z]{2,6}(?:\/[^\/#\?]+)+\.(?:jpe?g|gif|png|bmp)/i", $string)) ? true : false;
}


/**
 * is_valid_url
 * This will tell if the string is an url or not AND if is a valid URL
 */
function is_valid_url($string) {
	if(is_url($string)){
		$ch = curl_init();
		curl_setopt ($ch, CURLOPT_URL, str_replace('https://', 'http://', $string));
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt ($ch, CURLOPT_NOBODY, true);
		curl_setopt ($ch, CURLOPT_FAILONERROR, false);
		curl_setopt ($ch, CURLOPT_AUTOREFERER, true);
		curl_setopt ($ch, CURLOPT_TIMEOUT, 120);
		$result = @curl_exec($ch);
		curl_close($ch);
		return ($result!==false) ? true : false;
	} else {
		return false;
	}
}

/**
 * generateRandomString
 * Generates the random string used in randomFile
 *
 * @autor	fabin dot gnu at gmail dot com
 * @url		http://www.php.net/manual/es/function.file-exists.php#88607
 */
function generateRandomString($length = 8) {   
	$string = "";
	$possible = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
	for($i=0;$i < $length;$i++) {
		$char = substr($possible, rand(0, strlen($possible)-1), 1);
		if (!strstr($string, $char)) {
			$string .= $char;
		}
	}
	return $string;
}

/**
 * api_key
 * Returns the API key setted in config.php
 */
function api_key() {
	if(check_value(chevereto_config('api_key'))) return chevereto_config('api_key');
}

/**
 * api_mode
 * Returns the boolean according to the $ask_mode and API mode setted in config.php
 */
function api_mode($ask_mode) {
	if(trim(strtolower(chevereto_config('api_mode'))) == $ask_mode) return true;
}

/**
 * Pure PHP json_encode
 * json_enconde implementation for PHP < 5.2.0
 * http://www.php.net/manual/en/function.json-encode.php#100835
 */
if(!function_exists('json_encode')) {
	function json_encode($data) {           
		if(is_array($data) || is_object($data)) {
			$islist = is_array($data) && ( empty($data) || array_keys($data) === range(0,count($data)-1) );
		   
			if( $islist ) {
				$json = '[' . implode(',', array_map('__json_encode', $data) ) . ']';
			} else {
				$items = Array();
				foreach( $data as $key => $value ) {
					$items[] = json_encode("$key") . ':' . json_encode($value);
				}
				$json = '{' . implode(',', $items) . '}';
			}
		} elseif( is_string($data) ) {
			# Escape non-printable or Non-ASCII characters.
			# I also put the \\ character first, as suggested in comments on the 'addclashes' page.
			$string = '"' . addcslashes($data, "\\\"\n\r\t/" . chr(8) . chr(12)) . '"';
			$json    = '';
			$len    = strlen($string);
			# Convert UTF-8 to Hexadecimal Codepoints.
			for( $i = 0; $i < $len; $i++ ) {
			   
				$char = $string[$i];
				$c1 = ord($char);
			   
				# Single byte;
				if( $c1 <128 ) {
					$json .= ($c1 > 31) ? $char : sprintf("\\u%04x", $c1);
					continue;
				}
			   
				# Double byte
				$c2 = ord($string[++$i]);
				if ( ($c1 & 32) === 0 ) {
					$json .= sprintf("\\u%04x", ($c1 - 192) * 64 + $c2 - 128);
					continue;
				}
			   
				# Triple
				$c3 = ord($string[++$i]);
				if( ($c1 & 16) === 0 ) {
					$json .= sprintf("\\u%04x", (($c1 - 224) <<12) + (($c2 - 128) << 6) + ($c3 - 128));
					continue;
				}
				   
				# Quadruple
				$c4 = ord($string[++$i]);
				if( ($c1 & 8 ) === 0 ) {
					$u = (($c1 & 15) << 2) + (($c2>>4) & 3) - 1;
			   
					$w1 = (54<<10) + ($u<<6) + (($c2 & 15) << 2) + (($c3>>4) & 3);
					$w2 = (55<<10) + (($c3 & 15)<<6) + ($c4-128);
					$json .= sprintf("\\u%04x\\u%04x", $w1, $w2);
				}
			}
		} else {
			# int, floats, bools, null
			$json = strtolower(var_export( $data, true ));
		}
		return $json;
	}
}

/**
 * Pure PHP json_decode
 * json_decode implementation for PHP < 5.2.0
 * http://www.php.net/manual/en/function.json-decode.php#100740
 */
if(!function_exists('json_decode')) {
	function json_decode($data) {
		$comment = false;
		$out = '$x=';
		for ($i=0; $i<strlen($data); $i++) {
			if (!$comment) {
				if (($data[$i] == '{') || ($data[$i] == '['))       $out .= ' array(';
				else if (($data[$i] == '}') || ($data[$i] == ']'))   $out .= ')';
				else if ($data[$i] == ':')    $out .= '=>';
				else $out .= $data[$i];         
			}
			else $out .= $data[$i];
			if ($data[$i] == '"' && $data[($i-1)]!="\\")    $comment = !$comment;
		}
		eval($out . ';');
		return $x;
	}
}

/**
 * remove_temp_files
 * This function scan the image folder and removes the unwanted temp files
 */
function remove_temp_files() {
	
	if(!class_exists(FilterIterator) && !class_exists(RecursiveDirectoryIterator)) return;
	class TempFilesFilterIterator extends FilterIterator {
		public function accept() {
			$fileinfo = $this->getInnerIterator()->current();
			if(preg_match('/temp_.*/', $fileinfo)){
				return true;
			}
			return false;
		}
	}
	$iterator = new RecursiveDirectoryIterator(__CHV_FOLDER_IMAGES__); // PHP > 5.3 flag: FilesystemIterator::SKIP_DOTS
	$iterator = new TempFilesFilterIterator($iterator);
	foreach ($iterator as $file) {
		unlink($file->getPathname());
	}
}

/********************************************************
 * CONFIG OBSERVATION FUNCTIONS
 * This functions are quick methods to read config values
 */

/**
 * is_config_short_url()
 * Returns true if $config['short_url']==true
 */
function is_config_short_url() {
	return conditional_config('short_url');
}

/**
 * is_user_preference_short_url()
 * Returns true if the user wants to short the urls
 */
function is_user_preference_short_url() {
	return (isset($_COOKIE['doShort'])) ? true : false;
}

/**
 * is_config_auto_lang()
 * Returns true if the autolang is enabled
 */
function is_config_auto_lang() {
	return conditional_config('auto_lang');
}

/**
 * is_config_multiupload()
 * Returns true if the multiupload is enabled
 */
function is_config_multiupload() {
	return conditional_config('multiupload');
}

/**
 * is_config_over_resize()
 * Returns true if the over_resize is enabled
 */
function is_config_over_resize() {
	return conditional_config('over_resize');
}

/**
 * is_config_error_reporting()
 * Returns true if the error_reporting is enabled
 */
function is_config_error_reporting() {
	return conditional_config('error_reporting');
}

/**
 * is_config_private_mode()
 * Returns true if the private_mode is enabled
 */
function is_config_private_mode() {
	return conditional_config('private_mode');
}

/**
 * use_facebook_comments()
 * Returns true if the facebook_comments is enabled
 */
function use_facebook_comments() {
	return conditional_config('facebook_comments');
}

/**
 * total_images_uploaded
 * returns the total number of images uploaded
 */
function total_images_uploaded() {
	global $dB;
	$uploaded_qry = $dB->query_fetch_single("SELECT count(*) as total FROM chv_images;");
	return $uploaded_qry["total"];
}

/**
 * PNG ALPHA CHANNEL SUPPORT for imagecopymerge();
 * by Sina Salek
 *
 * Bugfix by Ralph Voigt (bug which causes it
 * to work only for $src_x = $src_y = 0.
 * Also, inverting opacity is not necessary.)
 * 08-JAN-2011
 *
 **/
function imagecopymerge_alpha($dst_im, $src_im, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h, $pct, $dst_im_ext) {
	if($dst_im_ext=='jpg' && $pct==100) {
		imagealphablending($dst_im, true);
		imagealphablending($src_im, true);
		imagecopy($dst_im, $src_im, $dst_x, $dst_y, 0, 0, $src_w, $src_h);
	} else {
		$transparent_index = imagecolortransparent($dst_im);
		$colors_total = imagecolorstotal($dst_im);
		$cut = imagecreatetruecolor($src_w, $src_h);
		
		if($transparent_index >= 0) {
			$transparent_color = imagecolorsforindex($dst_im, $transparent_index);
			$transparent_index = imagecolorallocatealpha($cut, $transparent_color['red'], $transparent_color['green'], $transparent_color['blue'], 127);
			imagefill($cut, 0, 0, $transparent_index);
			imagecolortransparent($cut, $transparent_index);
		} else {
			$color = imagecolorallocatealpha($cut, 0, 0, 0, 127);
			imagefill($cut, 0, 0, $color);
		}
		
		if($dst_im_ext=='png') {
			imagealphablending($dst_im, false);
			imagesavealpha($dst_im, true);
		} else {
			if($dst_im_ext!=='jpg') {
				imagetruecolortopalette($dst_im, true, 255);
				imagesavealpha($dst_im, false);
			}	
		}
		
		if($dst_im_ext=='png' && $colors_total==0) {
			if($pct<100) filter_opacity($src_im, $pct);
			imagealphablending($dst_im, true);
			imagesavealpha($dst_im, true);
			imagecopy($dst_im, $src_im, $dst_x, $dst_y, 0, 0, $src_w, $src_h);
			imagealphablending($dst_im, false);
		} else {
			imagecopy($cut, $dst_im, 0, 0, $dst_x, $dst_y, $src_w, $src_h);
			imagecopy($cut, $src_im, 0, 0, $src_x, $src_y, $src_w, $src_h);
			imagecopymerge($dst_im, $cut, $dst_x, $dst_y, 0, 0, $src_w, $src_h, $pct);
		}
	}
	
	imagedestroy($cut);
	imagedestroy($src_im);

}

// Taken from http://it.php.net/manual/en/function.imagecreatefromgif.php#59787
function is_animated_image($filename){
	$filecontents = file_get_contents($filename);
	$str_loc = 0; $count = 0;
	while($count<2) # There is no point in continuing after we find a 2nd frame
	{
		$where1=strpos($filecontents,"\x00\x21\xF9\x04",$str_loc);
		if ($where1 === FALSE) {
			break;
		} else {
			$str_loc=$where1+1;
			$where2=strpos($filecontents,"\x00\x2C",$str_loc);
			if($where2 === FALSE) {
				break;
			} else {
				if($where1+8 == $where2) {
					$count++;
				}
				$str_loc=$where2+1;
			}
		}
	}
	return ($count>1) ? true : false;
}

// Taken from http://www.php.net/manual/en/function.imagefilter.php#82162
function filter_opacity(&$img, $opacity) {
	if( !isset( $opacity ) )
		{ return false; }
	$opacity /= 100;
   
	//get image width and height
	$w = imagesx( $img );
	$h = imagesy( $img );
   
	//turn alpha blending off
	imagealphablending( $img, false );
   
	//find the most opaque pixel in the image (the one with the smallest alpha value)
	$minalpha = 127;
	for( $x = 0; $x < $w; $x++ )
		for( $y = 0; $y < $h; $y++ )
			{
				$alpha = ( imagecolorat( $img, $x, $y ) >> 24 ) & 0xFF;
				if( $alpha < $minalpha )
					{ $minalpha = $alpha; }
			}
   
	//loop through image pixels and modify alpha for each
	for( $x = 0; $x < $w; $x++ )
		{
			for( $y = 0; $y < $h; $y++ )
				{
					//get current alpha value (represents the TANSPARENCY!)
					$colorxy = imagecolorat( $img, $x, $y );
					$alpha = ( $colorxy >> 24 ) & 0xFF;
					//calculate new alpha
					if( $minalpha !== 127 )
						{ $alpha = 127 + 127 * $opacity * ( $alpha - 127 ) / ( 127 - $minalpha ); }
					else
						{ $alpha += 127 * $opacity; }
					//get the color index with new alpha
					$alphacolorxy = imagecolorallocatealpha( $img, ( $colorxy >> 16 ) & 0xFF, ( $colorxy >> 8 ) & 0xFF, $colorxy & 0xFF, $alpha );
					//set pixel with the new color + opacity
					if( !imagesetpixel( $img, $x, $y, $alphacolorxy ) )
						{ return false; }
				}
		}
	return true;
}

// Filter some nasty lang values
function get_chevereto_safe_lang() {
	global $backup_lang;
	foreach($backup_lang as $key=>$value) {
		$safe_lang[$key] = get_lang_txt($key);
	}
	unset($safe_lang['critical_js']);
	unset($safe_lang['critical_js_step_1']);
	unset($safe_lang['critical_js_step_2']);
	return $safe_lang;
}

function is_windows() {
	return (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN' ? true : false);
}

// http://kuikie.com/snippets/snippet.php/90-17/php-function-to-convert-bbcode-to-html
function convert_html_to_bbcode($text) {
	
    $htmltags = array(
        '/\<b\>(.*?)\<\/b\>/is',
        '/\<i\>(.*?)\<\/i\>/is',
        '/\<u\>(.*?)\<\/u\>/is',
        '/\<ul.*?\>(.*?)\<\/ul\>/is',
        '/\<li\>(.*?)\<\/li\>/is',
        '/\<img(.*?) src=\"(.*?)\" alt=\"(.*?)\" title=\"Smile(y?)\" \/\>/is',        // some smiley
        '/\<img(.*?) src=\"http:\/\/(.*?)\" (.*?)\>/is',
        '/\<img(.*?) src=\"(.*?)\" alt=\":(.*?)\" .*? \/\>/is',                       // some smiley
        '/\<div class=\"quotecontent\"\>(.*?)\<\/div\>/is',
        '/\<div class=\"codecontent\"\>(.*?)\<\/div\>/is', 
        '/\<div class=\"quotetitle\"\>(.*?)\<\/div\>/is',  
        '/\<div class=\"codetitle\"\>(.*?)\<\/div\>/is',
        '/\<cite.*?\>(.*?)\<\/cite\>/is',
        '/\<blockquote.*?\>(.*?)\<\/blockquote\>/is',
        '/\<div\>(.*?)\<\/div\>/is',
        '/\<code\>(.*?)\<\/code\>/is',
        '/\<br(.*?)\>/is',
        '/\<strong\>(.*?)\<\/strong\>/is',
        '/\<em\>(.*?)\<\/em\>/is',
        '/\<a href=\"mailto:(.*?)\"(.*?)\>(.*?)\<\/a\>/is',
        '/\<a .*?href=\"(.*?)\"(.*?)\>http:\/\/(.*?)\<\/a\>/is',
        '/\<a .*?href=\"(.*?)\"(.*?)\>(.*?)\<\/a\>/is'
    );
 
    $bbtags = array(
        '[b]$1[/b]',
        '[i]$1[/i]',
        '[u]$1[/u]',
        '[list]$1[/list]',
        '[*]$1',
        '$3',
        '[img]http://$2[/img]',
        ':$3',
        '\[quote\]$1\[/quote\]',
        '\[code\]$1\[/code\]',
        '',
        '',
        '',
        '\[quote\]$1\[/quote\]',
        '$1',
        '\[code\]$1\[/code\]',
        "\n",
        '[b]$1[/b]',
        '[i]$1[/i]',
        '[email=$1]$3[/email]',
        '[url]$1[/url]',
        '[url=$1]$3[/url]'
    );
 
    $text = str_replace ("\n", ' ', $text);
    $ntext = preg_replace ($htmltags, $bbtags, $text);
    $ntext = preg_replace ($htmltags, $bbtags, $ntext);
 
    // for too large text and cannot handle by str_replace
    if (!$ntext) {
        $ntext = str_replace(array('<br>', '<br />'), "\n", $text);
        $ntext = str_replace(array('<strong>', '</strong>'), array('[b]', '[/b]'), $ntext);
        $ntext = str_replace(array('<em>', '</em>'), array('[i]', '[/i]'), $ntext);
    }
 
    $ntext = strip_tags($ntext);
    $ntext = trim(html_entity_decode($ntext,ENT_QUOTES,'UTF-8'));
    return $ntext;
}

function minify_name($filepath) {
	return preg_replace('/(.+)\.(js|css)$/i', '$1.min.$2', $filepath);
}

function conditional_minify($filepath) {
	return (conditional_config('minify') && preg_match('/\.(js|css)$/', $filepath) ? minify_name($filepath) : $filepath);
}

function array_merge_minified($array, $minified, $create_missing_files=true) {
	if(conditional_config('minify')) {
		$minified = array_map('minify_name', $minified);	
	}
	return array_merge($array, $minified);
}

function get_language_direction($lang_iso_code='') {
	global $lang_code;
	$lang_iso_code = (!check_value($lang_iso_code) ? $lang_code : $lang_iso_code);
	$rtl_langs = array('ar','az','fa','jv','ks', 'kk','ku','ms','ml','ps','pa','sd','so','tk','ug','he','yi','dv','ur');
	return (in_array($lang_iso_code, $rtl_langs) ? 'rtl' : 'ltr');
}
register_show_function('get_language_direction', '$lang_iso_code=\'\'');

function get_language_html_tags($lang_iso_code='') {
	$lang_iso_code = (!check_value($lang_iso_code) ? $lang_code : $lang_iso_code);
	return 'xml:lang="'.get_lang_used().'" lang="'.get_lang_used().'" dir="'.get_language_direction($lang_iso_code).'"';
}
register_show_function('get_language_html_tags', '$lang_iso_code=\'\'');

function is_rtl_lang($lang_iso_code='') {
	return get_language_direction($lang_iso_code)=='rtl';
}

function is_ltr_lang($lang_iso_code='') {
	return get_language_direction($lang_iso_code)=='ltr';
}

// Thanks to Alix Axel
// http://stackoverflow.com/a/5860054
function unaccent_string($string) {
    if(strpos($string = htmlentities($string, ENT_QUOTES, 'UTF-8'), '&') !== false) {
        $string = html_entity_decode(preg_replace('~&([a-z]{1,2})(?:acute|cedil|circ|grave|lig|orn|ring|slash|tilde|uml);~i', '$1', $string), ENT_QUOTES, 'UTF-8');
    }
    return $string;
}

?>