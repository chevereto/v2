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

define('access', 'pref');

error_reporting(0);
@ini_set('display_errors', false);

$doShort = $_GET['doShort'];
if(!@include_once('../../../includes/config.php')) die('Can\'t find includes/config.php');
if(!@include_once('../../../includes/chevereto.php')) die('Can\'t find includes/chevereto.php');

if ($doShort=='0') setcookie('doShort', '', time() - 3600, __CHV_RELATIVE_ROOT__, $_SERVER['SERVER_NAME']);
if ($doShort=='1') setcookie('doShort', 1, time()+60*60*24*30, __CHV_RELATIVE_ROOT__, $_SERVER['SERVER_NAME']);

?>