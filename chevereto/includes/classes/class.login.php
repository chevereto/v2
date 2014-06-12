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
 * class.login.php
 * This class is used to handle the login/logout (admin or user)
 *
 * @author		Rodolfo Berríos <http://rodolfoberrios.com/>
 * @url			<http://chevereto.com>
 * @package		Chevereto
 *
 * @copyright	Rodolfo Berríos <inbox@rodolfoberrios.com>
 *
 */

class Login {
	
	public $error;
	public $ok;
	
	public static $user;
	
	// Let's construct the login class!
	function __construct()
	{
		if(array_key_exists('sID', $_REQUEST)) session_id($_REQUEST['sID']); // Uploadify thing
		@session_start();
		self::$user = self::login_user();
	}

	/**
	 * login_user
	 * Checks for the admin/user password
	 *
	 * @return	mixed
	 */		
	public static function login_user($password='', $keep='') {
		
		// Always clean the cookie and session on new login request
		if(check_value($password)) {
			unset($_SESSION["login_password"]);
			setcookie('login_password', '', time() - 3600, __CHV_RELATIVE_ROOT__, $_SERVER['SERVER_NAME']);
		}
		
		if(isset($_COOKIE['login_password'])) $_SESSION['login_password'] = $_COOKIE['login_password'];
		
		$admin_password = md5(chevereto_config('admin_password'));
		$user_password = md5(chevereto_config('user_password'));
		
		$permission = NULL;
		
		if($password == $admin_password || (isset($_SESSION['login_password']) && $_SESSION['login_password'] == $admin_password)) {
			$permission = 'admin';
		} elseif(check_value(chevereto_config('user_password')) && ($password == $user_password || $_SESSION['login_password'] == $user_password)) {
			$permission = 'user';
		}
		
		if(!is_null($permission) && check_value($password) && $keep==1) {
			setcookie('login_password', $password, time()+60*60*24*30, __CHV_RELATIVE_ROOT__, $_SERVER['SERVER_NAME']);
		}
		if(!is_null($permission) && check_value($password)) {
			$_SESSION['login_password'] = $password;
		}
		if(is_null($permission)) {
			self::do_logout();
		} else {
			self::$user = $permission;
		}
		
		return (!is_null($permission)) ? $permission : false;
	}
	
	/**
	 * is_user
	 * Checks for the user password combo
	 *
	 * @return	bool
	 */	
	public static function is_user() {
		return (self::$user=='user') ? true : false;
	}
	
	/**
	 * is_admin
	 * Checks for the admin password combo
	 *
	 * @return	bool
	 */	
	public static function is_admin() {
		return (self::$user=='admin') ? true : false;
	}

	/**
	 * do_logout
	 * Logout the user
	 */	
	public static function do_logout() {
		setcookie('login_password', '', time() - 3600, __CHV_RELATIVE_ROOT__, $_SERVER['SERVER_NAME']);
		unset($_SESSION['login_password']);
	}

}

/**
 * Procedural methods->functions
 * This functions are made to be used as procedural versions of the class login methods.
 */

function login_user($password='', $keep='') {
	return Login::login_user($password, $keep);
}

function is_logged_user() {
	return (is_admin() || is_user()) ? true : false;
}

function is_admin() {
	return Login::is_admin();
}

function is_user() {
	return Login::is_user();
}

function do_logout() {
	return Login::do_logout();
}
 

?>