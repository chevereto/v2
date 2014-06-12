<?php if(!defined('access') or !access) die('This file cannot be directly accessed.'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html id="shortURL" xmlns="http://www.w3.org/1999/xhtml" <?php show_language_html_tags(); ?> xmlns:fb="http://ogp.me/ns/fb#">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php show_image_filename(); ?> (<?php show_image_dimentions(); ?>)</title>
<meta name="description" content="<?php echo chevereto_config('meta_description'); ?>" />
<meta name="generator" content="Chevereto <?php show_chevereto_version(); ?>" />
<meta content="width = device-width, initial-scale = 1.0, maximum-scale = 1.0, minimum-scale = 1.0, user-scalable = no" name="viewport">
<link href="<?php show_theme_url(); echo conditional_minify('style.css'); ?>" rel="stylesheet" type="text/css" />
<link href="<?php show_theme_url(); ?>favicon.ico" rel="shortcut icon" type="image/x-icon" />

<meta name="twitter:card" content="photo">
<meta name="twitter:image" content="<?php show_img_url(); ?>">

<script type="text/javascript" src="<?php show_jquery(); ?>"></script>

<script type="text/javascript">
$(function() {
	var $shortURL_content = $("#shortURL-content");
	var image_height = $shortURL_content.find("img").height();
	var image_max_height = image_height;
	var $contentMargin = {"top": parseInt($shortURL_content.css("margin-top")), "bottom": parseInt($shortURL_content.css("margin-bottom"))};
	var header_height = $("#header").outerHeight(true);
	
	$shortURL_content.find("img").removeAttr("height").removeAttr("width");
	
	function img_resize() {		
		image_max_height = $(window).outerHeight(true) - header_height - $contentMargin.top - $contentMargin.bottom;
		image_max_height_padding = image_max_height;
		if(image_max_height >= image_height) {
			image_max_height = image_height;
		}
		
		$shortURL_content.find("img").css("max-height", image_max_height);
		
		padding_top = (image_max_height_padding - $shortURL_content.find("img").height())/2;
		$shortURL_content.css("padding-top", padding_top);
		
	}
	$(window).load(img_resize).resize(img_resize);
});
</script>

<?php show_google_analytics(); ?>

</head>

<body id="shortURL">
	<div id="header">
    	<h1><a href="<?php show_base_url(); ?>"><?php show_domain(); ?></a></h1>
        <h2><?php show_image_filename(); ?></h2>
        <div id="shortURL-links">
        	<a id="viewer-link" href="<?php show_image_viewer(); ?>"><?php show_lang_txt('txt_show_viewer_link'); ?></a>
        	<a id="direct-link" href="<?php show_image_url(); ?>"><?php show_lang_txt('txt_show_directly_link'); ?></a>
        </div>
    </div>
    <div id="shortURL-content"><a href="<?php show_image_url(); ?>"><img src="<?php show_image_url(); ?>" alt="<?php show_image_filename(); ?>" width="<?php show_image_width(); ?>" height="<?php show_image_height(); ?>" /></a></div>
</body>

</html>