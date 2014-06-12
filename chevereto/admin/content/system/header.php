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

// Incomplete lang files? Then load the backup lang...
if(!check_value($lang['txt_filesize_large'])) {
	unset($lang);
	$lang = get_backup_lang();	
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php show_language_html_tags(); ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Chevereto File Manager</title>
<link rel="stylesheet" type="text/css" href="<?php echo absolute_to_relative(__CHV_PATH_ADMIN_SYSTEM__); ?>style.css" />
<link href="<?php echo __CHV_URL_THEME__; ?>favicon.ico" rel="shortcut icon" type="image/x-icon" />

<meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0; user-scalable=0;"/>

<script type="text/javascript" language="javascript" src="<?php show_jquery(); ?>"></script>
<script type="text/javascript" language="javascript" src="<?php echo absolute_to_relative(__CHV_PATH_SYSTEM_JS__); echo conditional_minify('functions.js'); ?>"></script>
<script type="text/javascript" language="javascript" src="<?php echo absolute_to_relative(__CHV_PATH_ADMIN_SYSTEM_JS__); echo conditional_minify('admin.js'); ?>"></script>

<script type="text/javascript" language="javascript">
	var admin_url = "<?php echo __CHV_ADMIN_URL__; ?>";
	var admin_json = admin_url+"json";

	var config = {
		thumb_width : "<?php echo chevereto_config('thumb_width'); ?>",
		thumb_height : "<?php echo chevereto_config('thumb_height'); ?>",
		error_reporting : <?php echo (chevereto_config('error_reporting') ? "true" : "false" ) ?>,
		over_resize : <?php echo (chevereto_config('over_resize') ? "true" : "false" ) ?>
	}
	
	var lang = <?php echo json_encode($lang); ?>;
</script>

</head>