<?php if(!defined('access') or !access) die('This file cannot be directly accessed.'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html id="shortURL" xmlns="http://www.w3.org/1999/xhtml" <?php show_language_html_tags(); ?> xmlns:fb="http://ogp.me/ns/fb#">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php show_doctitle(); ?></title>
<meta name="description" content="<?php echo chevereto_config('meta_description'); ?>" />
<meta name="generator" content="Chevereto <?php show_chevereto_version(); ?>" />
<meta content="width = device-width, initial-scale = 1.0, maximum-scale = 1.0, minimum-scale = 1.0, user-scalable = no" name="viewport">
<link href="<?php show_theme_url(); echo conditional_minify('style.css'); ?>" rel="stylesheet" type="text/css" />
<link href="<?php show_theme_url(); ?>favicon.ico" rel="shortcut icon" type="image/x-icon" />

<?php show_google_analytics(); ?>

</head>

<body id="maintenance">
	<div>
        <h1>We are under maintenance.</h1>
        <p><?php show_chevereto_config('site_name'); ?> will be down while we fine tune the system, don’t worry we will back soon.</p>
        <p>Sorry for the inconvenience.</p>
    </div>
</body>

</html>