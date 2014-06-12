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
 * class handler
 * This class handles all the request to the script. It does:
 * 		- Makes canonical redirection
 * 		- Sets the propper theme template file
 * 		- Fetch all the non static access to Chevereto
 *
 * @author		Rodolfo Berríos <http://rodolfoberrios.com/>
 * @url			<http://chevereto.com>
 * @package		Chevereto
 *
 * @copyright	Rodolfo Berríos <inbox@rodolfoberrios.com>
 *
 */

class Handler {
	
	private $root_url;
	private $base_url;
	private $path_images;
	private $path_themes;
	
	private $request_uri;
	private $script_name;
	private $valid_request;
	private $canonical_request;
	
	private $template;
	private $pages;
	
	private $handled_request;
	private $request_array;
	private $base_request;
	
	public static $image;
	public static $uploaded;
	public static $doctitle;
	public static $id_public;
	public static $image_info;
	public static $image_filename;
	public static $image_target;
	public static $image_thumb_target;
	public static $image_url;
	public static $image_thumb_url;
	public static $image_viewer;
	public static $delete_image_url;
	public static $delete_image_confirm_url;
	public static $uploaded_images;
	
	public static $is_viewer;
	
	// Let's construct a valid request!
	function __construct()
	{
		global $lang, $dB;
		$this->dB = $dB;
		
		// Parse the definitions to this object.. This nos not necessary but in case of changes...
		$this->root_url = __CHV_RELATIVE_ROOT__;
		$this->base_url = __CHV_BASE_URL__;
		$this->path_images = rtrim(__CHV_PATH_IMAGES__,'/').'/';
		$this->path_theme = __CHV_PATH_THEME__;

		// Parse the params
		$this->request_uri = $_SERVER['REQUEST_URI'];
		$this->script_name = $_SERVER['SCRIPT_NAME'];
		$this->valid_request = sanitize_path($this->request_uri);

		// Build the canonical request
		// All the dirs will have a traling slash no matter in what whe are (Linux, Windows, etc)
		$this->canonical_request = '/'.$this->valid_request;
		if(is_dir(__CHV_ROOT_DIR__.$this->valid_request)) {
			$this->canonical_request .= '/';
		}
		
		$this->handled_request = ($this->root_url=='/') ? $this->valid_request : str_ireplace($this->root_url, '', $this->add_trailing_slashes($this->request_uri));
		$this->request_array = explode('/', rtrim(str_replace("//", "/", str_replace("?", "/",$this->handled_request)), '/'));
		$this->base_request = $this->request_array[0];
		
		// Override this vars just for the admin area
		if($this->base_request==chevereto_config('admin_folder')) {
			$this->root_url = __CHV_RELATIVE_ADMIN__;
			$this->base_url = __CHV_ADMIN_URL__;
		}
		
		// If the request is invalid we make a 301 redirection to the canonical url.
		if($this->root_url!==$this->request_uri and $this->canonical_request!==$this->request_uri) {
			$this->redirect($this->base_redirection($this->canonical_request), 301);
		}
		
		// It's a valid request on admin or index.php?
		if($this->base_request!==chevereto_config('admin_folder')) {
			if($this->is_index()) $this->proccess_request();
		} else {
			// Admin credentials
			if(!check_value(chevereto_config('admin_password'))) {
				$admin_password_errors[] = 'You need to set the admin password in <code>$config[\'admin_password\']</code>';
			}
			if(chevereto_config('admin_password')=='password') {
				$admin_password_errors[] = 'You haven\'t changed the default admin password. Please set this value in <code>$config[\'admin_password\']</code>';
			}
			if(check_value($admin_password_errors) && !is_localhost()) {
				chevereto_die($admin_password_errors, 'Config error', array('You need to fix the configuration related to the admin credentials before use this area.'));
			}
			require_once(__CHV_PATH_ADMIN_CLASSES__.'class.adminhandler.php');
			$handler = new AdminHandler($this->valid_request);
			die();
		}
	}

	/**
	 * proccess_request
	 * Process the request for the public area
	 */
	private function proccess_request()
	{
		global $lang;
		$this->template = 404; // Default template
		$this->pages = $this->get_pages(); // get theme pages	
		
		// Prepare te request array to use the legacy request (?v=file.ext)
		if(check_value($_GET['v']) && preg_match("/^\w*\.jpg|png|gif$/", $_GET['v'])) {
			$this->base_request = '?'.$this->request_array[1];
			unset($this->request_array[1]);
		}
		
		@session_start();
		if(count($_SESSION['ImagesUp'])>0) {
			$_SESSION['ImagesUp'] = array_values($_SESSION['ImagesUp']);
			self::$uploaded = true;
		}
		
		if(chevereto_config('maintenance')) {
			$this->base_request = 'maintenance';
		}		
		
		// Switch according the request
		switch($this->base_request) {
			case '': case 'index.php':
				@session_start();
				$_SESSION['last_upload_request'] = time();
				$this->template = 'index';
			break;
			
			case 'json':
				json_prepare();
				// Do a special trick for the json action=login
				if($_REQUEST['action']=='login') {
					// Check for user match...
					$login_user = login_user($_REQUEST['password'], $_REQUEST['keep']);
					if($login_user!==false) {
						$json_array = array('status_code' => 200, 'status_txt'=>'logged in');
					} else {
						$json_array = array('status_code' => 403, 'status_txt'=>'invalid login');
					}
				} elseif($_REQUEST['action']=='logout') {
					do_logout();
					$json_array = array('status_code' => 200, 'status_txt'=>'logged out');					
				}
				$json_array = check_value($json_array) ? $json_array : array('status'=>403, 'status_txt'=>'unauthorized');
				session_write_close();
				die(json_output($json_array));
			break;
			
			case __CHV_VIRTUALFOLDER_IMAGE__: // View request
				$id_public = $this->request_array[1];
				$this->template = (!is_upload_result() ? 'view' : 'uploaded');
				self::$is_viewer = true;
			break;
			
			case __CHV_VIRTUALFOLDER_UPLOADED__:
				@session_start();
				if(count($_SESSION['ImagesUp'])>0) {
					$this->template = 'uploaded';
					self::$doctitle = $lang['doctitle_upload_complete'];
				} else {
					$this->redirect(__CHV_BASE_URL__, 400);
				}
			break;
			
			case 'error-javascript':
				chevereto_die(array(get_lang_txt('critical_js_step_1'), get_lang_txt('critical_js_step_2')),'JavaScript', array(get_lang_txt('critical_js')));
			break;
			
			case '?chevereto':
				$this->template = 'bool';
			break;
			
			// Legacy viewer
			case '?v='.$_GET['v']: // View request
				$id_public = $_GET['v'];
				$this->legacy_redirect = true;
			break;
			
			case 'delete':
			case 'delete-confirm':
				//$delete_what = $this->request_array[1];
				$id_public = $this->request_array[2];
				$deleteHash = $this->request_array[3];
				$this->template = $this->base_request;
				self::$is_viewer = true;
			break;
			
			case 'maintenance':
				$this->template = 'maintenance';
				self::$doctitle = chevereto_config('doctitle');
			break;
			
			default:
				// Pages request
				require_once($this->path_theme.'pages/pages_config.php'); // We load the special pages config
				if(in_array($this->base_request.'.php', $this->pages) and $this->request_array[1]=='' and $pages_config[$this->base_request]['live']) {
					$this->template = 'pages/'.$this->base_request;
					self::$doctitle = $pages_config[$this->base_request]['title'];
				} else {
					$this->template = 'shorturl';
					$id_public = $this->base_request;
					self::$is_viewer = true;
				}
			break;
		}
		
		// Ask for the login on index and pages
		if($this->template=='index' || $this->template=='pages/'.$this->base_request) {
			if(conditional_config('private_mode')) {					
				if(!is_logged_user()) {
					$doctitle = get_lang_txt('txt_enter_password').' - '.chevereto_config('doctitle');
					include(__CHV_PATH_SYSTEM__.'login.php');
					die();
				}
			}
		}
		
		if($this->template=='uploaded') {
			self::$doctitle = get_lang_txt('doctitle_upload_complete');
			self::$image_info = $_SESSION['ImagesUp'][0];
			self::$uploaded_images = $_SESSION['ImagesUp'];
			$_SESSION['ImagesUp'] = NULL; unset($_SESSION['ImagesUp']);
		}
		
		if(preg_match('/view|shorturl|delete/', $this->template) || $this->legacy_redirect) {

			// Test connection
			if($this->dB->dead) {
				self::$doctitle = 'dB connection error';
				$this->template = 404;
			} else {
				
				// get image info
				$imageID = ($this->legacy_redirect) ? $id_public : decodeID($id_public);
				self::$image_info = $this->dB->image_info($imageID);		
				self::$id_public = $id_public;
				
				if(!is_array(self::$image_info)) { // Record?
					
					if($this->template=='delete-confirm') {
						json_output(array('status_code'=>403, 'status_txt'=>'target image doesn\'t exists'));
					} else {
						$this->template = 404;
					}
				} else {
					if($this->legacy_redirect) {
						$this->redirect(__CHV_BASE_URL__.__CHV_VIRTUALFOLDER_IMAGE__.'/'.encodeID(self::$image_info['image_id']), 301);
					}
					$target = get_image_target(self::$image_info);
					
					self::$image_target = $target['image_path'];
					self::$image_thumb_target = $target['image_thumb_path'];
					self::$image_url = absolute_to_url($target['image_path']);
					self::$image_thumb_url = absolute_to_url($target['image_thumb_path']);					
					self::$image_filename = self::$image_info['image_filename'];
					self::$image_viewer = __CHV_BASE_URL__.__CHV_VIRTUALFOLDER_IMAGE__.'/'.$id_public;
					self::$delete_image_url = __CHV_BASE_URL__.'delete/image/'.self::$id_public.'/'.self::$image_info['image_delete_hash'];
					
					$image_delete_proceed = (!empty(self::$image_info['image_delete_hash']) && $deleteHash===self::$image_info['image_delete_hash']) ? true : false;
					
					switch($this->template) {
						case 'delete':
							if(!$image_delete_proceed) {
								$this->redirect(__CHV_BASE_URL__.__CHV_VIRTUALFOLDER_IMAGE__.'/'.self::$id_public, 301);
							}
							self::$delete_image_confirm_url = __CHV_BASE_URL__.'delete-confirm/image/'.self::$id_public.'/'.self::$image_info['image_delete_hash'];
							self::$doctitle = get_lang_txt('doctitle_delete_confirm').' '.self::$image_info['image_filename'];
						break;
						case 'delete-confirm':
							if(!$image_delete_proceed) {
								json_output(array('status_code'=>403, 'status_txt'=>'invalid delete hash'));
							} else {
								require_once(__CHV_PATH_ADMIN_CLASSES__.'class.manage.php');
								$manage = new Manage(array('id' => self::$image_info['image_id'], 'action' => 'delete'));
								if($manage->dead) {
									$json_array = array('status_code' => 403,'status_txt'=>$manage->error);
								} else {
									$json_array = $manage->process();
								}
							}
							// Make the status_txt more readable...
							switch($json_array['status_code']) {
								case 200:
									$json_array['status_txt'] = get_lang_txt('txt_image_deleted');
								break;
								default:
								case 403:
									$json_array['status_txt'] = get_lang_txt('txt_error_deleting_image');
								break;
							}
							json_output($json_array);
						break;
						default:
							self::$doctitle = get_lang_txt('doctitle_viewing_image').' '.self::$image_info['image_filename'];
						break;
					}			
				}
				
			}

		}

		
		if($this->template == 404) {
			status_header(404);
			self::$doctitle = (check_value(self::$doctitle)) ? self::$doctitle : get_lang_txt('txt_404_title');
		} else {
			status_header(200);
		}
		
		// We load the template
		if($this->template == 'bool'){
			exit(json_encode(true));
		} else {
			$this->load_template();
		}
	}

	/**
	 * add_trailing_slashes
	 * Adds allways trailing slash to string.
	 * @Inspired from WordPress
	 *
	 * @param	string
	 * @return	string
	 */	
	private function add_trailing_slashes($string)
	{
		$string = '/'.ltrim($string, '/');
		return rtrim($string, '/') . '/';
	}
	
	/**
	 * getArrayFirstIndex
	 * Returns the first index of the target array
	 *
	 * @param	array
	 * @return	string
	 */	
	private function getArrayFirstIndex($arr)
	{
      foreach ($arr as $key => $value) return $key;
	}

	/**
	 * redirect
	 * Redirects to another URL
	 * @Inspired from WordPress
	 *
	 * @param	string
	 */	
	private function redirect($to, $status=301)
	{
		$to = preg_replace('|[^a-z0-9-~+_.?#=&;,/:%!]|i', '', $to);
		if(php_sapi_name() != 'cgi-fcgi') status_header($status);
		header("Location: $to");
		die();
	}

	/**
	 * base_redirection
	 * Returns the correct url to handle redirections replacing the extra /path/ from request
	 *
	 * @param	string
	 * @return	string
	 */
	private function base_redirection($request)
	{
		return str_replace($this->root_url, '/', $this->base_url).ltrim($request, '/');
	}

	/**
	 * is_index
	 * We look at the script_name to see if we are requesting index.php
	 *
	 * @return	string
	 */	
	private function is_index()
	{
		return preg_match('{/index.php$}', $this->script_name);
	}

	/**
	 * is_valid_image
	 * We look if the image exists and if is valid
	 *
	 * @param	string
	 * @return	bool
	 */
	private function is_valid_image($image) {
		return (file_exists($image) and @getimagesize($image)) ? true : false;
	}

	/**
	 * get_pages
	 * Scan and return the array of pages
	 *
	 * @return	array
	 */
	private function get_pages()
	{
		$pages = scandir(__CHV_PATH_THEME__.'pages/');
		return array_values(array_diff($pages, array('.', '..', 'pages_config.php', 'index.php')));
	}

	/**
	 * load_template
	 * Invoque the template file!
	 */
	private function load_template()
	{
		if(file_exists($this->path_theme.'functions.php')) require_once($this->path_theme.'functions.php');
		require_once($this->path_theme.$this->template.'.php');	// Content
	}

	/**
	 * get_img_info
	 * Scan and return the array of pages
	 *
	 * @param	string
	 * @return	strig
	 */
	public static function get_img_info($what)
	{
		$get_info = get_info(self::$image_target);
		
		// TODO: Detect info mismatch
		/*if($get_info['width']!==self::$image_info['image_width']) {
			echo "W";
		}
		if($get_info['height']!==self::$image_info['image_height']) {
			echo "H";
		}
		if($get_info['bytes']!==self::$image_info['image_size']) {
			echo "S";
		}*/
		
		switch($what) {
			case 'width':
				return $get_info['width'];
			break;
			case 'height':
				return $get_info['height'];
			break;
			case 'html':
				return $get_info['html'];
			break;
			case 'weight':
				return $get_info['size'];
			break;
		}
	}

}

/**
 * Procedural methods->functions
 * This functions are made to be used as procedural versions of the class handler methods.
 */
function is_uploaded() {
	return Handler::$uploaded;
}

function is_viewer() {
	return (Handler::$is_viewer && !Handler::$uploaded) ? true : false;
}

function get_doctitle() {
	return Handler::$doctitle;
}


function get_img_relative_url() {
	return absolute_to_relative(Handler::$image_target); //wea
}

/*** 2.3 ***/
function is_upload_result() {
	return (is_uploaded()) ? true : false;
}

/*** 2.4.2 ***/
function get_image_by_handler() {
	return (is_viewer() || is_uploaded() ? Handler::$image_info : false);
}

function is_multiupload_result() {
	return (count(Handler::$uploaded_images)>1 ? true : false);
}

function is_singleupload_result() {
	return !is_multiupload_result();
}

function get_uploaded_images() {
	return Handler::$uploaded_images;
}

?>