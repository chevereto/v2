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
  
/**
 * class adminhandler
 * This class handles all the request to the admin area of the script. It does:
 * 		- Makes canonical redirection
 * 		- Do the ajaxed admin features
 * 		- Fetch all the admin non static access to Chevereto
 *
 * @author		Rodolfo Berríos <http://rodolfoberrios.com/>
 * @url			<http://chevereto.com>
 * @package		Chevereto
 *
 * @copyright	Rodolfo Berríos <inbox@rodolfoberrios.com>
 *
 */

global $lang;

class AdminHandler extends Handler {
	private $handled_request;
	private $request_array;
	
	// Let's construct a valid admin request!
	function __construct($valid_request)
	{
		global $lang, $Login, $dB;
		@session_start();
		
		// Redirect plain /admin/index.php access
		if(preg_match('/index\.php/', $_SERVER['REQUEST_URI'])) {
			$this->redirect($this->base_redirection($this->root_url), 301);
		}
		
		$admin_request = sanitize_path(str_replace(sanitize_path(__CHV_FOLDER_ADMIN__), "", str_replace(sanitize_path(__CHV_RELATIVE_ROOT__).'/', "", $valid_request))); // json?blabla instead of (folder?)/admin/json?blabla
		$this->request_array = explode('/', $admin_request);
		
		$request_file = str_replace('//', '/', __CHV_ROOT_DIR__.str_replace((__CHV_RELATIVE_ROOT__=='/') ? '' : __CHV_RELATIVE_ROOT__, '', $_SERVER['REQUEST_URI']));
		
		// Serve the static file or call the handler?
		if(file_exists($request_file) and !is_dir($request_file) and !preg_match('/php/', get_mime($request_file)) and trim($_SERVER['REQUEST_URI'], '/')!==trim(dirname($_SERVER['SCRIPT_NAME']), '/')) {
			error_reporting(0);
			header('Content-Type: ' . get_mime($request_file).'; Cache-Control: no-cache; Pragma: no-cache');
			die(readfile($request_file));
		}
		
		// Now, deny all direct access to the other resources
		if((file_exists($request_file) or is_dir($request_file)) and trim($_SERVER['REQUEST_URI'], '/')!==trim(dirname($_SERVER['SCRIPT_NAME']), '/') and !$Login->is_admin()) {
			status_header(403);
			die('Forbidden');
		}		
		
		// Organize the source request
		$request_array_explode = explode('?', $this->request_array[0]);
		$request_base = $request_array_explode[0];
		
		// Now, lets do sub request according to the base request
		switch($request_base) {
			
			case '': break; // admin main
			
			case 'json':
				json_prepare();
				// Do a special trick for the json action=login
				if($_REQUEST['action']!=='login' and !is_admin()) {					
					$json_array = array('status_code' => 401, 'status_txt'=>'unauthorized');
				} elseif($_REQUEST['action']=='login') {
				
					// Check for admin match...
					$login_user = login_user($_REQUEST['password'], $_REQUEST['keep']);
					if($login_user=='admin') {
						$json_array = array('status_code' => 200, 'status_txt'=>'logged in');
					} else {
						$json_array = array('status_code' => 403, 'status_txt'=>'invalid login');
					}
					
				} elseif($_REQUEST['action']=='logout') {
					
					do_logout();
					$json_array = array('status_code' => 200, 'status_txt'=>'logged out');
					
				} elseif($_REQUEST['action']=='filelist') {
					
					require_once(__CHV_PATH_CLASSES__.'class.filelist.php');
					$filelist = new FileList($_REQUEST['type'], $_REQUEST['sort'], $_REQUEST['limit'], $_REQUEST['keyword']);
					$json_array = $filelist->filelist;
					
				} elseif($_REQUEST['action']=='uploaded') {
					
					// In some point there will be a stats class that will help us to output all the stats. This is just the number of uploaded files now.
					$json_array = array('total' => total_images_uploaded());
				
				// The rest of the actions are for the manage class (delete|rename|resize)
				} else {
					require_once(__CHV_PATH_ADMIN_CLASSES__.'class.manage.php');
					$manage = new Manage($_REQUEST);
					if($manage->dead) {
						$json_array = array('status_code' => 403,'status_txt'=>$manage->error);
					} else {
						$json_array = $manage->process();
					}
					
				}
				$json_array = check_value($json_array) ? $json_array : array('status_code'=>403, 'status_txt'=>'empty json');
				die(json_output($json_array));
			break; // json
			
			default:
				if(is_admin()) {
					status_header(404);
					die('Not found');
				} else {
					status_header(403);
					die('Forbidden');
				}
			break;
		}
		
		// Send the OK status header
		status_header(200);

		if(!is_admin()) {
			$doctitle = get_lang_txt('txt_enter_password').' - Chevereto File Manager';
			require_once(__CHV_PATH_SYSTEM__.'login.php');
		} else {
			require_once(__CHV_PATH_ADMIN_SYSTEM__.'header.php');
			require_once(__CHV_PATH_ADMIN_SYSTEM__.'filemanager.php');
		}
		
	}
	
}

?>