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
  
if(!defined('access') or !access) die('This file cannot be directly accessed.'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="<?php echo absolute_to_relative(__CHV_PATH_SYSTEM__).conditional_minify('style.css'); ?>" rel="stylesheet" type="text/css" />
<link href="<?php echo absolute_to_relative(__CHV_PATH_SYSTEM__); ?>favicon.ico" rel="shortcut icon" type="image/x-icon" />

<title><?php echo $doctitle; ?></title>

<?php if($title=='JavaScript') { ?><script languaje="javascript">window.location="<?php echo __CHV_BASE_URL__;?>";</script><?php } ?>

</head>

<body>

<div id="main">
	<div id="top"><a href="http://chevereto.com/" title="Chevereto image hosting script"><img src="<?php echo absolute_to_relative(__CHV_PATH_SYSTEM__); ?>img/logo.png" id="logo" alt="Chevereto" /></a> <img src="<?php echo absolute_to_relative(__CHV_PATH_SYSTEM__); ?>img/ico-warn.png" id="icon" alt="" /></div>
	<p><?php echo $what_happend; ?></p>
	<?php if(is_array($error_msg)) : ?>
    <ul>
    	<?php foreach($error_msg as $error) echo '<li>'.$error.'</li>'."\n"; ?>
    </ul>
    <?php endif; ?>
    <p><?php echo $solution; ?></p>
</div>

</body>
</html>