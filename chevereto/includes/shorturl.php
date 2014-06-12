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
  
/**
 * This File declarates and proccess the Short URL
 */

require_once(__CHV_PATH_CLASSES__.'class.shorturl.php');
$ShortURL = new ShortURL();

if(!check_value(chevereto_config('short_url_service'))) {
	$config['short_url_service'] = 'tinyurl';
}

$ShortURL->service = chevereto_config('short_url_service');

if(check_value(chevereto_config('short_url_user')) or check_value(chevereto_config('short_url_keypass'))) {
	$ShortURL->user = chevereto_config('short_url_user');
	$ShortURL->pass = chevereto_config('short_url_keypass');
}

if(chevereto_config('short_url_service')=="google" && !check_value(chevereto_config('short_url_keypass'))) {
	$ShortURL->pass = 'AIzaSyBT9Qm10Skr502kL4bUMqoC7GHiaOBFz-g'; // For demo / new installations only
}

if(chevereto_config('short_url_service')=='custom' and check_value(chevereto_config('custom_short_url_api'))) {
	$ShortURL->custom_service_api = chevereto_config('custom_short_url_api');
}

?>