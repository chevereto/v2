<?php
/* --------------------------------------------------------------------

  Chevereto
  http://chevereto.com/

  @version	2.6.0
  @author	Rodolfo Berríos A. <http://rodolfoberrios.com/>
			<inbox@rodolfoberrios.com>

  Copyright (C) 2013 Rodolfo Berríos <inbox@rodolfoberrios.com> All rights reserved.
  
  BY USING THIS SOFTWARE YOU DECLARE TO ACCEPT THE CHEVERETO EULA
  http://chevereto.com/license

  --------------------------------------------------------------------- */

if(!defined('access') or !access) die('This file cannot be directly accessed.');

/*** Define execution starts ***/
define('__CHV_TIME_EXECUTION_STARTS__', microtime(true));

/*** Define current version ***/
define('__CHV_VERSION__', '2.6.0');

/*** Define default admin folder ***/
define('__CHV_DEFAULT_ADMIN_FOLDER__', 'admin');

/*** Define user configurable file error ***/
define('__CHV_CONFIGURABLE_FILE_ERROR__', '<br />There are errors in the <strong>%%FILE%%</strong> file. Change the encodig to UTF-8 without bom using Notepad++ or any similar code editor and remove any character before <span style="color: red;">&lt;?php</span> and after <span style="color: red;">?&gt;</span>');

/*** Include the config file ***/
(file_exists(dirname(__FILE__).'/config.php')) ? require_once(dirname(__FILE__).'/config.php') : die('Can\'t find includes/config.php');
if(headers_sent()) die(str_replace('%%FILE%%', 'includes/config.php', __CHV_CONFIGURABLE_FILE_ERROR__));

/*** Set the base definitions ***/
define('HTTP_HOST', $_SERVER['HTTP_HOST']);
define('SERVER_PROTOCOL', (!empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on' ) ? 'https://' : 'http://');

if(strtolower($config['root_dir'])=='auto' || !isset($config['root_dir'])) {
	define('__CHV_ROOT_DIR__', rtrim(str_replace('\\','/',dirname(dirname(__FILE__))),'/').'/'); // /home/user/public_html/chevereto/
} else {
	define('__CHV_ROOT_DIR__', $config['root_dir']);
}
if(strtolower($config['relative_dir'])=='auto' || !isset($config['relative_dir'])) {
	define('__CHV_RELATIVE_ROOT__', str_ireplace(rtrim(str_replace('\\','/', str_ireplace(str_replace('\\','/', $_SERVER['SCRIPT_NAME']), '', str_replace('\\','/', realpath($_SERVER['SCRIPT_FILENAME'])))), '/'), '', __CHV_ROOT_DIR__)); // /chevereto/
} else {
	define('__CHV_RELATIVE_ROOT__', $config['relative_dir']);
}

/*** The base URL ***/
define('__CHV_BASE_URL__', SERVER_PROTOCOL.HTTP_HOST.__CHV_RELATIVE_ROOT__); // http(s)://www.mysite.com/chevereto/

/*** error reporting ***/
@ini_set('log_errors', true); // Always log the errors
if($config['error_reporting']==false || access=='js') { 
	@ini_set('display_errors', false);
	error_reporting(0);
} else {
	@ini_set('display_errors', true);
	error_reporting(E_ALL & ~E_NOTICE);
}

/*** encoding ***/
@ini_set('default_charset', 'utf-8');

/*** Folders Definitions ***/
define('__CHV_FOLDER_IMAGES__', $config['folder_images']);

/*** Paths Definitions ***/
define('__CHV_PATH_IMAGES__', __CHV_ROOT_DIR__.__CHV_FOLDER_IMAGES__.'/');
define('__CHV_PATH_INCLUDES__', __CHV_ROOT_DIR__.'includes/');
define('__CHV_PATH_CLASSES__', __CHV_PATH_INCLUDES__.'classes/');
define('__CHV_FILE_FUNCTIONS__', __CHV_PATH_INCLUDES__.'functions.php');
define('__CHV_FILE_DEFINITIONS__', __CHV_PATH_INCLUDES__.'definitions.php');
define('__CHV_PATH_CONTENT__', __CHV_ROOT_DIR__.'content/');
define('__CHV_PATH_SYSTEM__', __CHV_PATH_CONTENT__.'system/');
define('__CHV_PATH_SYSTEM_JS__', __CHV_PATH_SYSTEM__.'js/');
define('__CHV_PATH_SYSTEM_IMG__', __CHV_PATH_SYSTEM__.'img/');
define('__CHV_PATH_LANGUAGES__', __CHV_PATH_CONTENT__.'languages/');
define('__CHV_PATH_THEMES__', __CHV_PATH_CONTENT__.'themes/');
define('__CHV_PATH_THEME__', __CHV_PATH_THEMES__.$config['theme'].'/');

/*** Admin folder + path ***/
$config['admin_folder'] = (!isset($config['admin_folder']) || empty($config['admin_folder']) ? __CHV_DEFAULT_ADMIN_FOLDER__ : $config['admin_folder']);
define('__CHV_FOLDER_ADMIN__', $config['admin_folder'].'/');
define('__CHV_PATH_ADMIN__', __CHV_ROOT_DIR__.__CHV_FOLDER_ADMIN__);

// Try to fix the admin folder
if(!file_exists(__CHV_PATH_ADMIN__) && file_exists(__CHV_ROOT_DIR__.__CHV_DEFAULT_ADMIN_FOLDER__)) {
	@rename(__CHV_ROOT_DIR__.__CHV_DEFAULT_ADMIN_FOLDER__, __CHV_PATH_ADMIN__);
}

/*** Admin paths ***/
define('__CHV_PATH_ADMIN_INCLUDES__', __CHV_PATH_ADMIN__.'includes/');
define('__CHV_PATH_ADMIN_CLASSES__', __CHV_PATH_ADMIN_INCLUDES__.'classes/');
define('__CHV_PATH_ADMIN_CONTENT__', __CHV_PATH_ADMIN__.'content/');
define('__CHV_PATH_ADMIN_SYSTEM__', __CHV_PATH_ADMIN_CONTENT__.'system/');
define('__CHV_PATH_ADMIN_SYSTEM_JS__', __CHV_PATH_ADMIN_SYSTEM__.'js/');
define('__CHV_PATH_ADMIN_SYSTEM_IMG__', __CHV_PATH_ADMIN_SYSTEM__.'img/');
define('__CHV_RELATIVE_ADMIN__', __CHV_RELATIVE_ROOT__.__CHV_FOLDER_ADMIN__);
define('__CHV_ADMIN_URL__', __CHV_BASE_URL__.__CHV_FOLDER_ADMIN__);

/*** Set the dB constants ***/
define('__CHV_DB_HOST__', $config['db_host']);
define('__CHV_DB_PORT__', $config['db_port']);
define('__CHV_DB_NAME__', $config['db_name']);
define('__CHV_DB_USER__', $config['db_user']);
define('__CHV_DB_PASS__', $config['db_pass']);

/*** Language ***/
$default_lang = __CHV_PATH_LANGUAGES__.$config['lang'].'/chevereto_lang.php';
if(@strlen($config['lang'])>0 and file_exists($default_lang)) {
	require_once(__CHV_PATH_LANGUAGES__.'en/chevereto_lang.php');
	$backup_lang = $lang;
	unset($lang);
	if($config['auto_lang']==true && isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
		$lang_code = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'],0,2);
		if($lang_code=='zh') {
			$lang_code = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'],0,5);
		}
		$auto_lang = __CHV_PATH_LANGUAGES__.$lang_code.'/chevereto_lang.php';
		if(file_exists($auto_lang)) {
			define('__CHV_LANGUAGE_FILE__', $auto_lang);
		} else {
			$lang_code = $config['lang'];
			define('__CHV_LANGUAGE_FILE__', $default_lang);
		}
	} else {
		$lang_code = $config['lang'];
		define('__CHV_LANGUAGE_FILE__', $default_lang);
	}
	if($lang_code!=='en') {
		require_once(__CHV_LANGUAGE_FILE__);
	} else {
		$lang = $backup_lang;
	}
} else {
	die('Can\'t find default language file. Please check your lang in the config.php file.');
}

/*** Include the user definitions ***/
(file_exists(__CHV_FILE_DEFINITIONS__)) ? require_once(__CHV_FILE_DEFINITIONS__) : die('Can\'t find '.__CHV_FILE_DEFINITIONS__);
if(headers_sent()) die(str_replace('%%FILE%%', __CHV_FILE_DEFINITIONS__, __CHV_CONFIGURABLE_FILE_ERROR__));

/*** Workaround the admin request ***/
if(preg_match('/\/admin\//', $_SERVER['REQUEST_URI'])) {
	define('access', 'admin');
	define('SKIP_MAINTENANCE', true);
}

/*** Include the core functions ***/
(file_exists(__CHV_FILE_FUNCTIONS__)) ? require_once(__CHV_FILE_FUNCTIONS__) : die('Can\'t find <strong>'.__CHV_FILE_FUNCTIONS__.'</strong>. Make sure you have uploaded this file.');
require_once(__CHV_PATH_INCLUDES__.'template.functions.php');

/*** Set some url paths ***/
define('__CHV_URL_SYSTEM_JS__', absolute_to_url(__CHV_PATH_SYSTEM_JS__));
define('__CHV_URL_THEME__', absolute_to_url(__CHV_PATH_THEME__));
define('__CHV_URL_UPDATE_SCRIPT__', __CHV_BASE_URL__.'update.php');
// Virtual paths
define('__CHV_VIRTUALFOLDER_IMAGE__', sanitize_path($config['virtual_folder_image']));
define('__CHV_VIRTUALFOLDER_UPLOADED__', sanitize_path($config['virtual_folder_uploaded']));

/*** Call the dB class ***/
require_once(__CHV_PATH_CLASSES__.'class.db.php');
$dB = new dB();

/*** Call the Login class ***/
require_once(__CHV_PATH_CLASSES__.'class.login.php');
$Login = new Login();

/*** Call the ShortURL class ***/
require_once(__CHV_PATH_CLASSES__.'class.shorturl.php');
$ShortURL = new ShortURL();

/*** Flood protection ***/
if(preg_match('/upload/', access)) {
	$flood = is_upload_flood();
}

/*** maintenance ***/
if(preg_match('/upload|API|pref/', access) && chevereto_config('maintenance')) {
	status_header(400);
	die('maintenance');
}

/*** Call the handler ***/
if(check_value(access) && !preg_match("/API|update|pref/", access)) {
	require_once(__CHV_PATH_CLASSES__.'class.handler.php');
	$handler = new Handler();
}

?>