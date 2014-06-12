<?php if(!defined('access') or !access) die('This file cannot be directly accessed.'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php show_language_html_tags(); ?> xmlns:fb="http://ogp.me/ns/fb#">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<?php show_chevereto_header(); ?>

<?php if(is_viewer()) : ?>
<meta name="twitter:card" content="photo">
<meta name="twitter:image" content="<?php show_img_url(); ?>">
<?php endif; ?>

</head>

<body>

<div id="wrap">
    <div id="top">
    	<a href="<?php show_base_url(); ?>"><img src="<?php show_theme_imgdir(); ?>logo.png" alt="<?php echo chevereto_config('site_name'); ?>" /></a>
    	<?php if(is_logged_user()) :?><div id="logged">You are logged in <span class="sep">&middot;</span> <a rel="logout"><?php show_lang_txt('txt_logout'); ?></a></div><?php endif; ?>
    </div>
    