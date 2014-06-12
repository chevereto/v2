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

define('access', 'upload');
require_once('includes/chevereto.php');

if(isset($_FILES['ImageUp'])) {
	$to_upload = $_FILES['ImageUp'];
	$is_remote = false;
} else {
	if(isset($_POST['url'])) {
		$to_upload = $_POST['url'];
		$is_remote = true;
	}
}

if(isset($_POST['resize'])) $to_resize = $_POST['resize'];

if(check_value($to_upload)) {
	require_once('includes/uploader.php');
} else {
	print_r($_REQUEST);
	die();
}

?>