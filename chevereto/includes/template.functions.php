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

/* ---------------------------------------------------------------------------------------------------------------------------------------- */
/*** INCLUDE TAGS ***/

function include_theme_header() {
	include(__CHV_PATH_THEME__.'header.php');
}

function include_theme_footer() {
	include(__CHV_PATH_THEME__.'footer.php');
}

/* ---------------------------------------------------------------------------------------------------------------------------------------- */
/*** ASSETS ***/

function get_jquery() {
	return __CHV_URL_SYSTEM_JS__.conditional_minify('jquery.js');
}
register_show_function('get_jquery');


/* ---------------------------------------------------------------------------------------------------------------------------------------- */
/*** CURRENT SETUP INFO TAGS ***/

function get_domain() {
	return HTTP_HOST;
}
register_show_function('get_domain');

function get_base_url() {
	return __CHV_BASE_URL__;
}
register_show_function('get_base_url');

function get_default_lang() {
	return chevereto_config('lang');
}
register_show_function('get_default_lang');

function get_max_upload_size() {
	return chevereto_config('max_filesize');
}
register_show_function('get_max_upload_size');

function get_tinyurl_service() {
	switch(chevereto_config('short_url_service')) {
		case 'tinyurl':
		default:
			$tiny_service = 'TinyURL';
		break;
		case 'google':
			$tiny_service = 'Google';
		break;
		case 'isgd':
			$tiny_service = 'is.gd';
		break;
		case 'bitly':
			$tiny_service = 'bit.ly';
		break;
		case 'custom':
			$tiny_service = check_value(chevereto_config('custom_short_url_service')) ? chevereto_config('custom_short_url_service') : 'custom service';
		break;
	}
	echo $tiny_service;
}
register_show_function('get_tinyurl_service');


/* ---------------------------------------------------------------------------------------------------------------------------------------- */
/*** THEME INFO FUNCTIONS ***/

function is_html_tag($tag) {
	return (strlen($tag) == strlen(strip_tags($tag))) ? false : true;
}

function get_theme_name() {
	return chevereto_config('theme');
}
register_show_function('get_theme_name');
 
function get_theme_url() {
	return __CHV_URL_THEME__;
}
register_show_function('get_theme_url');

function get_theme_imgdir() {
	return __CHV_URL_THEME__.'theme-img/';
}
register_show_function('get_theme_imgdir');


/* ---------------------------------------------------------------------------------------------------------------------------------------- */
/*** LANGUAGE TAGS ***/
 
function get_lang_used() {
	return basename(dirname(__CHV_LANGUAGE_FILE__));
}
register_show_function('get_lang_used');


/* ---------------------------------------------------------------------------------------------------------------------------------------- */
/*** HTML TAGS ***/

register_show_function('get_doctitle');

function show_chevereto_header() {
	$doctitle = check_value(get_doctitle()) ? get_doctitle().' - ' : '';
	$html =  '<script type="text/javascript" src="'.__CHV_URL_SYSTEM_JS__.conditional_minify('jquery.js').'"></script>
<script type="text/javascript" src="'.__CHV_URL_SYSTEM_JS__.conditional_minify('jquery.uploadify-3.1_chevereto.js').'"></script>
<script type="text/javascript" src="'.__CHV_URL_SYSTEM_JS__.conditional_minify('functions.js').'"></script>
<script type="text/javascript" src="'.__CHV_URL_SYSTEM_JS__.conditional_minify('chevereto.js').'"></script>
<script type="text/javascript" src="'.__CHV_URL_THEME__.conditional_minify('theme.js').'"></script>

<link type="text/css" href="'.__CHV_URL_THEME__.conditional_minify('style.css').'" rel="stylesheet" />
<link type="text/css" href="'.__CHV_URL_THEME__.conditional_minify('uploadify.css').'" rel="stylesheet" />
<link type="image/x-icon" href="'.__CHV_URL_THEME__.'favicon.ico" rel="shortcut icon"  />

<meta name="generator" content="Chevereto '.get_chevereto_version().'" />

<meta name="description" content="'.chevereto_config('meta_description').'" />
<meta name="keywords" content="'.chevereto_config('meta_keywords').'" />

<title>'.$doctitle.chevereto_config('doctitle').'</title>

<script type="text/javascript">
	var base_url = "'.__CHV_BASE_URL__.'";
	var base_url_js = "'.__CHV_URL_SYSTEM_JS__.'";
	var uploadify_swf = "'.absolute_to_relative(__CHV_PATH_SYSTEM_JS__).'uploadify.swf";
	var uploader_file = "'.__CHV_RELATIVE_ROOT__.'upload.php";
	var zeroclip_swf = "'.__CHV_URL_SYSTEM_JS__.'ZeroClipboard.swf";
	var session_id = "'.session_id().'";
	var virtual_url_image = "'.__CHV_BASE_URL__.__CHV_VIRTUALFOLDER_IMAGE__.'/";
	var virtual_url_uploaded = "'.__CHV_BASE_URL__.__CHV_VIRTUALFOLDER_UPLOADED__.'/";
	var config = {
		doctitle : "'.chevereto_config('doctitle').'",
		virtual_folder_image : "'.chevereto_config('virtual_folder_image').'",
		virtual_folder_uploaded : "'.chevereto_config('virtual_folder_uploaded').'",
		max_filesize : "'.chevereto_config('max_filesize').'",
		min_resize_size : '.chevereto_config('min_resize_size').',
		max_resize_size : '.chevereto_config('max_resize_size').',
		multiupload : '.(chevereto_config('multiupload') ? "true" : "false").',
		multiupload_limit : '.chevereto_config('multiupload_limit').',
		error_reporting : '.(chevereto_config('error_reporting') ? "true" : "false").'
	}
	var ImagesUp = '.json_encode(get_uploaded_images()).';
	var lang = '.json_encode(get_chevereto_safe_lang()).';
</script>';

	if(check_value(chevereto_config('facebook_app_id'))) {
		$html .= "\n\n";
		$html .= '<meta property="fb:app_id" content="'.chevereto_config('facebook_app_id').'" />'."\n";
		$html .= '<script type="text/javascript">
	(function(d, s, id) {
		  var js, fjs = d.getElementsByTagName(s)[0];
		  if (d.getElementById(id)) return;
		  js = d.createElement(s); js.id = id;
		  js.src = "//connect.facebook.net/'.get_lang_locale().'/all.js#xfbml=1&appId='.chevereto_config('facebook_app_id').'";
		  fjs.parentNode.insertBefore(js, fjs);
		}(document, "script", "facebook-jssdk"));'."\n";
		$html .= '</script>'."\n";
	}
	
	$ga = get_google_analytics();
	$html .= $ga ? "\n".$ga."\n" : '';
	
	$html .= "\n".'<noscript><meta http-equiv="refresh" content="0;url='.__CHV_BASE_URL__.'error-javascript" /></noscript>'."\n";;
	
	echo $html;

}

/**
 * show_page_links
 * echoes the setted pages
 */
function show_page_links($tag) {
	$html_tag = is_html_tag($tag) ? $tag : '';
	$html_closing_tag = html_closing_tag($html_tag);
	require(__CHV_PATH_THEME__.'pages/pages_config.php');
	foreach($pages_config as $key => $value) {
		if($value['live']) echo $html_tag.'<a href="'.__CHV_RELATIVE_ROOT__.$key.'">'.$value['title'].'</a>'.$html_closing_tag;
	}
}

/**
 * html_closing_tag
 * returns the closing </tag> for a given <tag>
 */
function html_closing_tag($html_tag) {
	return str_replace('<', '</', $html_tag);
} 

/* 2.4.2 */

/* ---------------------------------------------------------------------------------------------------------------------------------------- */
/*** THE CHEVERETO LOOP ***/

/*function set_images() {
	global $images;
	$images = [];
}*/

$imageloop = -1;
function have_images() {
	global $images, $imageloop;
	if(!check_value($images)) {
		if(is_uploaded()) {
			$images = get_uploaded_images();
		} else {
			return false;
		}
	}
	return ($imageloop + 1 < count($images) ? true : false);
}
function the_image() {
	global $images, $image, $imageloop;
	$image = $images[$imageloop+1];
	$imageloop++;
}
function get_current_loop() {
	global $imageloop;
	return $imageloop;
}
function is_first_loop() {
	return (get_current_loop()==0 ? true : false);
}

function get_images_count() {
	global $images;
	$images_count = 0;
	if(!check_value($images)) {
		if(is_uploaded()) {
			$images_count = count(get_uploaded_images());
		}
	}
	return $images_count;
}
register_show_function('get_images_count');


/* ---------------------------------------------------------------------------------------------------------------------------------------- */
/*** AUX FUNCTIONS ***/

function get_newline_by_char($newlinechar) {
	if($newlinechar=='default') $newlinechar = "\n";
	if(!preg_match('/^hr|br|\s+|\\[nrt]$/', $newlinechar) && $newlinechar!=='') {
		$newlinechar = "\n";
	}
	if(preg_match("/^hr|br$/", $newlinechar)) {
		$newline = '<'.$newlinechar.' />';
	} else {
		$newline = $newlinechar;
	}
	return $newline;
}

function trim_lastline_newlinechar($string) {
	return preg_replace('/<(br|hr) \/>$/', '', rtrim($string));
}

function get_image_link($link='') {
	switch($link) {
		default: case 'shorturl':
			$link = get_image_shorturl();
		break;
		case 'direct':
			$link = get_image_url();
		break;
		case 'viewer':
			$link = get_image_viewer();
		break;
		case 'thumb_url':
			$link = get_image_thumb_url();
		break;
	}
	return $link;
}

function get_image_template_link($link='') {
	switch($link) {
		default: case 'shorturl':
			$link = 'IMAGE_SHORTURL';
		break;
		case 'direct':
			$link = 'IMAGE_URL';
		break;
		case 'viewer':
			$link = 'IMAGE_VIEWER';
		break;
		case 'thumb_url':
			$link = 'IMAGE_THUMB_URL';
		break;
	}
	return '%'.$link.'%';
}


/* ---------------------------------------------------------------------------------------------------------------------------------------- */
/*** GET IMAGE FUNCTIONS ***/

function get_image() {
	global $image;
	if(!check_value($image)) {
		$image = get_image_by_handler();
	} else {
		return;
	}
}
function get_image_by_id($id, $id_public=true) {
	global $dB;
	$id = ($id_public ? decodeID($id) : $id);
	if($dB->dead) {
		return 'database error ('.$dB->error.')';
	} else {
		$image_info = $dB->image_info($id);
		return (is_array($image_info) ? $image_info : 'image id not found');
	}
}

function get_image_value($key) {
	global $image; get_image();
	return $image[$key];
}

function get_image_filename() {
	return get_image_value('image_filename');
}
register_show_function('get_image_filename');

function get_image_id() {
	return get_image_value('image_id_public');
}
register_show_function('get_image_id');

function get_image_real_id() {
	return get_image_value('image_id');
}
register_show_function('get_image_real_id');

function get_image_viewer() {
	return get_image_value('image_viewer');
}
register_show_function('get_image_viewer');

function get_image_shorturl() {
	return get_image_value('image_shorturl');
}
register_show_function('get_image_shorturl');

function get_image_shorturl_service($link='') {
	$image_value = get_image_value('image_shorturl_service');
	if(!check_value($image_value)) {
		require(__CHV_PATH_INCLUDES__.'shorturl.php');
		$image_value = $ShortURL->get_ShortURL(get_image_link($link));
	}
	return $image_value;
}
register_show_function('get_image_shorturl_service', '$link=\'\'');

function get_image_width() {
	return get_image_value('image_width');
}
register_show_function('get_image_width');

function get_image_height() {
	return get_image_value('image_height');
}
register_show_function('get_image_height');

function get_image_size() {
	return get_image_value('image_size');
}
register_show_function('get_image_size');

function get_image_size_bytes() {
	return get_image_value('image_bytes');
}
register_show_function('get_image_size_bytes');

function get_image_url() {
	return get_image_value('image_url');
}
register_show_function('get_image_url');

function get_image_thumb_width() {
	return get_image_value('image_thumb_width');
}
register_show_function('get_image_thumb_width');

function get_image_thumb_height() {
	return get_image_value('image_thumb_height');
}
register_show_function('get_image_thumb_height');

function get_image_thumb_url() {
	return get_image_value('image_thumb_url');
}
register_show_function('get_image_thumb_url');

function get_image_delete_hash() {
	return get_image_value('image_delete_hash');
}
register_show_function('get_image_delete_hash');

function get_image_delete_url() {
	return get_image_value('image_delete_url');
}
register_show_function('get_image_delete_url');

function get_image_delete_confirm_url() {
	return get_image_value('image_delete_confirm_url');
}
register_show_function('get_image_delete_confirm_url');

function get_image_dimentions() {
	return get_image_width().' x '.get_image_height().' pixels';
}
register_show_function('get_image_dimentions');

function get_image_attr_dimentions() {
	return get_image_value('image_attr');
}
register_show_function('get_image_attr_dimentions');

function get_image_raw_html($template='') {
	if($template=='' || $template=='default') $template = '<img src="%IMAGE_URL%" alt="%IMAGE_FILENAME%" border="0" />';
	return get_image_by_template($template);
}
register_show_function('get_image_raw_html', '$template=\'\'');

function get_image_html($template='') {
	return htmlentities(get_image_raw_html($template));
}
register_show_function('get_image_html', '$template=\'\'');

function get_image_thumb_raw_html($template='') {
	if($template=='' || $template=='default') $template = '<img src="%IMAGE_THUMB_URL%" alt="%IMAGE_FILENAME%" border="0" />';
	return get_image_by_template($template);
}
register_show_function('get_image_thumb_raw_html', '$template=\'\'');

function get_image_thumb_html($template='') {
	return htmlentities(get_image_thumb_raw_html($template));
}
register_show_function('get_image_thumb_html', '$template=\'\'');

function get_image_bbcode($template='') {
	return convert_html_to_bbcode(get_image_raw_html($template));
}
register_show_function('get_image_bbcode', '$template=\'\'');

function get_image_thumb_bbcode($template='') {
	return convert_html_to_bbcode(get_image_thumb_raw_html($template));
}
register_show_function('get_image_thumb_bbcode', '$template=\'\'');

function set_image_link_target($target) {
	$target = ltrim($target, '_');
	return (preg_match('/^blank|self|parent|top$/', $target) ? ' target="_'.$target.'"' : '');
}

function get_image_linked_raw_html($link='', $target='', $template='') {
	if($template=='' || $template=='default') $template = '<img src="%IMAGE_URL%" alt="%IMAGE_FILENAME%" border="0" />';
	return '<a href="'.get_image_link($link).'"'.set_image_link_target($target).'>'.get_image_by_template($template).'</a>';
}
register_show_function('get_image_linked_raw_html', '$link=\'\', $target=\'\', $template=\'\'');

function get_image_linked_html($link='', $target='', $template='') {
	return htmlentities(get_image_linked_raw_html($link, $target, $template));
}
register_show_function('get_image_linked_html', '$link=\'\', $target=\'\', $template=\'\'');

function get_image_linked_bbcode($link='', $target='', $template='') {
	return convert_html_to_bbcode(get_image_linked_raw_html($link, $target, $template));
}
register_show_function('get_image_linked_bbcode', '$link=\'\', $target=\'\', $template=\'\'');

function get_image_thumb_linked_raw_html($link='', $target='', $template='') {
	if($template=='' || $template=='default') $template = '<img src="%IMAGE_THUMB_URL%" alt="%IMAGE_FILENAME%" border="0" />';
	return '<a href="'.get_image_link($link).'"'.set_image_link_target($target).'>'.get_image_by_template($template).'</a>';
}
register_show_function('get_image_thumb_linked_raw_html', '$link=\'\', $target=\'\', $template=\'\'');

function get_image_thumb_linked_html($link='', $target='', $template='') {
	return htmlentities(get_image_thumb_linked_raw_html($link, $target, $template));
}
register_show_function('get_image_thumb_linked_html', '$link=\'\', $target=\'\', $template=\'\'');

function get_image_thumb_linked_bbcode($link='', $target='', $template='') {
	return convert_html_to_bbcode(get_image_thumb_linked_raw_html($link, $target, $template));
}
register_show_function('get_image_thumb_linked_bbcode', '$link=\'\', $target=\'\', $template=\'\'');

function get_image_by_template($template='', $before='', $after='') {
	if($template=='' || $template=='default') $template = '<img width="%IMAGE_WIDTH%" height="%IMAGE_HEIGHT%" id="%IMAGE_ID%" src="%IMAGE_URL%" alt="%IMAGE_FILENAME%" />';
	$template = $before.$template.$after;	
	$patterns = array(
		'IMAGE_ID'				=> get_image_id(),
		'IMAGE_REAL_ID'			=> get_image_real_id(),
		'IMAGE_FILENAME'		=> get_image_filename(),
		'IMAGE_URL'				=> get_image_url(),		
		'IMAGE_VIEWER'			=> get_image_viewer(),
		'IMAGE_SHORTURL'		=> get_image_shorturl(),
		'IMAGE_WIDTH'			=> get_image_width(),
		'IMAGE_HEIGHT'			=> get_image_height(),
		'IMAGE_SIZE'			=> get_image_size(),
		'IMAGE_SIZE_BYTES'		=> get_image_size_bytes(),		
		'IMAGE_THUMB_WIDTH'		=> get_image_thumb_width(),
		'IMAGE_THUMB_HEIGHT'	=> get_image_thumb_height(),
		'IMAGE_THUMB_URL'		=> get_image_thumb_url(),	
	);
	return preg_replace('/%([a-z_]+)%/ie', '$patterns["$1"]', $template);
}


/* ---------------------------------------------------------------------------------------------------------------------------------------- */
/*** GET IMAGES ARRAY ***/

function get_images() {
	global $images;
	if(!check_value($images)) {
		if(is_uploaded()) {
			$images = get_uploaded_images();
		} else {
			return;
		}
	}
	return $images;
}


/* ---------------------------------------------------------------------------------------------------------------------------------------- */
/*** IMAGES DIRECT VALUE FUNCTIONS ***/

function get_images_value($key) {
	foreach(get_uploaded_images() as $image) {
		if(!check_value($image[$key])) continue;
		$array[] = $image[$key];
	}
	return $array;
}

function show_images_value($key, $before='', $after='', $newlinechar='default') {
	$images = get_images();
	foreach($images as $image) {
		if(!check_value($image[$key])) continue;
		$echo .= $before.$image[$key].$after.get_newline_by_char($newlinechar);
	}
	echo trim_lastline_newlinechar($echo);
}

function get_images_shorturls() {
	return get_images_value('image_shorturl');
}
function show_images_shorturls($newlinechar='default', $before='', $after='') {
	show_images_value('image_shorturl', $before, $after, $newlinechar);
}

function get_images_shorturls_service() {
	return get_images_value('image_shorturl_service');
}
function show_images_shorturls_service($newlinechar='default', $before='', $after='') {
	show_images_value('image_shorturl_service', $before, $after, $newlinechar);
}

function has_images_shorturls_service() {
	return count(get_images_shorturls_service())>0;
}

function get_images_urls() {
	return get_images_value('image_url');
}
function show_images_urls($newlinechar='default', $before='', $after='') {
	show_images_value('image_url', $before, $after, $newlinechar);
}

function get_images_thumbs_urls() {
	return get_images_value('image_thumb_url');
}
function show_images_thumbs_urls($newlinechar='default', $before='', $after='') {
	show_images_value('image_thumb_url', $before, $after, $newlinechar);
}

function get_images_viewer() {
	return get_images_value('image_viewer');
}
function show_images_viewer($newlinechar='default', $before='', $after='') {
	show_images_value('image_viewer', $before, $after, $newlinechar);
}

function get_images_delete_urls() {
	return get_images_value('image_delete_url');
}
function show_images_delete_urls($newlinechar='default', $before='', $after='') {
	show_images_value('image_delete_url', $before, $after, $newlinechar);
}


/* ---------------------------------------------------------------------------------------------------------------------------------------- */
/*** IMAGES ARRAY FUNCTIONS ***/

function get_images_html_bysize($template='', $raw=true, $code='html', $before='', $after='', $size='') {
	$images = get_images();
	if($template=='default') $template = '';
	global $image;
	foreach($images as $image) {
		switch($size) {
			default: case 'full':
				$array_value = get_image_raw_html($template, $before, $after);
			break;
			case 'thumb':
				$array_value = get_image_thumb_raw_html($template, $before, $after);
			break;
		}
		if($code=='bbcode') {
			$array[] = convert_html_to_bbcode($array_value);
		} else {
			$array[] = ($raw ? $array_value : htmlentities($array_value));
		}
	}
	return $array;
}
function show_images_html_bysize($template='', $raw=true, $code='html', $newlinechar='default', $before='', $after='', $size='') {
	foreach(get_images_html_bysize($template, $raw, $code, $before, $after, $size) as $image) {
		if($code=='bbcode') {
			$image = convert_html_to_bbcode($image);
		} else {
			$image = $image;
		}
		$echo .= $image.get_newline_by_char($newlinechar);
	}
	echo trim_lastline_newlinechar($echo);
}

function get_images_raw_html($template='', $before='', $after='') {
	return get_images_html_bysize($template, true, 'html', $before, $after);
}
function show_images_raw_html($template='', $newlinechar='default', $before='', $after='') {
	show_images_html_bysize($template, true, 'html', $newlinechar, $before, $after);
}

function get_images_html($template='', $before='', $after='') {
	return get_images_html_bysize($template, false, 'html', $before, $after);
}
function show_images_html($template='', $newlinechar='default', $before='', $after='') {
	show_images_html_bysize($template, false, 'html', $newlinechar, $before, $after);
}

function get_images_bbcode($template='', $before='', $after='') {
	return get_images_html_bysize($template, true, 'bbcode', $before, $after);
}
function show_images_bbcode($template='', $newlinechar='default', $before='', $after='') {
	show_images_html_bysize($template, true, 'bbcode', $newlinechar, $before, $after);
}

function get_images_thumbs_raw_html($template='', $before='', $after='') {
	return get_images_html_bysize($template, true, 'html', $before, $after, 'thumb');
}
function show_images_thumbs_raw_html($template='', $newlinechar='default', $before='', $after='') {
	show_images_html_bysize($template, true, 'html', $newlinechar, $before, $after, 'thumb');
}

function get_images_thumbs_html($template='', $before='', $after='') {
	return get_images_html_bysize($template, false, 'html', $before, $after, 'thumb');
}
function show_images_thumbs_html($template='', $newlinechar='default', $before='', $after='') {
	show_images_html_bysize($template, false, 'html', $newlinechar, $before, $after, 'thumb');
}

function get_images_thumbs_bbcode($template='', $before='', $after='') {
	return get_images_html_bysize($template, true, 'bbcode', $before, $after, 'thumb');
}
function show_images_thumbs_bbcode($template='', $newlinechar='default', $before='', $after='') {
	show_images_html_bysize($template, true, 'bbcode', $newlinechar, $before, $after, 'thumb');
}


/* ---------------------------------------------------------------------------------------------------------------------------------------- */
/*** LINKED IMAGES ARRAY FUNCTIONS ***/

function get_images_linked_html_bysize($link='', $target='', $template='', $raw=true, $code='html', $before='', $after='', $size='') {
	$images = get_images();
	if($template=='default') $template = '';
	global $image;
	foreach($images as $image) {
		$array_value = '<a href="'.get_image_link($link).'"'.set_image_link_target($target).'>';
		switch($size) {
			default: case 'full':
				$array_value .= get_image_raw_html($template, $before, $after);
			break;
			case 'thumb':
				$array_value .= get_image_thumb_raw_html($template, $before, $after);
			break;
		}
		$array_value .= '</a>';
		if($code=='bbcode') {
			$array[] = convert_html_to_bbcode($array_value);
		} else {
			$array[] = ($raw ? $array_value : htmlentities($array_value));
		}
	}
	return $array;
}
function show_images_linked_html_bysize($link='', $target='', $template='', $raw=true, $code='html', $newlinechar='default', $before='', $after='', $size='') {
	foreach(get_images_linked_html_bysize($link, $target, $template, $raw, $code, $before, $after, $size) as $image) {
		if($code=='bbcode') {
			$image = convert_html_to_bbcode($image);
		} else {
			$image = $image;
		}
		$echo .= $image.get_newline_by_char($newlinechar);
	}
	echo trim_lastline_newlinechar($echo);
}

function get_images_linked_raw_html($link='', $target='', $template='', $before='', $after='') {
	return get_images_linked_html_bysize($link, $target, $template, true, 'html', $before, $after);
}
function show_images_linked_raw_html($link='', $target='', $template='', $newlinechar='default', $before='', $after='') {
	show_images_linked_html_bysize($link, $target, $template, true, 'html', $newlinechar, $before, $after);
}

function get_images_linked_html($link='', $target='', $template='', $before='', $after='') {
	return get_images_linked_html_bysize($link, $target, $template, false, 'html', $before, $after);
}
function show_images_linked_html($link='', $target='', $template='', $newlinechar='default', $before='', $after='') {
	show_images_linked_html_bysize($link, $target, $template, false, 'html', $newlinechar, $before, $after);
}

function get_images_linked_bbcode($link='', $target='', $template='', $before='', $after='') {
	return get_images_linked_html_bysize($link, $target, $template, true, 'bbcode', $before, $after);
}
function show_images_linked_bbcode($link='', $target='', $template='', $newlinechar='default', $before='', $after='') {
	show_images_linked_html_bysize($link, $target, $template, true, 'bbcode', $newlinechar, $before, $after);
}

function get_images_thumbs_linked_raw_html($link='', $target='', $template='', $before='', $after='') {
	return get_images_linked_html_bysize($link, $target, $template, true, 'html', $before, $after, 'thumb');
}
function show_images_thumbs_linked_raw_html($link='', $target='', $template='', $newlinechar='default', $before='', $after='') {
	show_images_linked_html_bysize($link, $target, $template, true, 'html', $newlinechar, $before, $after, 'thumb');
}

function get_images_thumbs_linked_html($link='', $target='', $template='', $before='', $after='') {
	return get_images_linked_html_bysize($link, $target, $template, false, 'html', $newlinechar, $before, $after, 'thumb');
}
function show_images_thumbs_linked_html($link='', $target='', $template='', $newlinechar='default', $before='', $after='') {
	show_images_linked_html_bysize($link, $target, $template, false, 'html', $newlinechar, $before, $after, 'thumb');
}

function get_images_thumbs_linked_bbcode($link='', $target='', $template='', $before='', $after='') {
	return get_images_linked_html_bysize($link, $target, $template, true, 'bbcode', $newlinechar, $before, $after, 'thumb');
}
function show_images_thumbs_linked_bbcode($link='', $target='', $template='', $newlinechar='default', $before='', $after='') {
	show_images_linked_html_bysize($link, $target, $template, true, 'bbcode', $newlinechar, $before, $after, 'thumb');
}

/* ---------------------------------------------------------------------------------------------------------------------------------------- */
/*** LEGACY FUNCTIONS ***/

$legacy_functions = array(
	'version'				=> 'get_chevereto_version',
	'show_version'			=> 'show_chevereto_version',
	'get_img_filename'		=> 'get_image_filename',
	'show_img_filename'		=> 'show_image_filename',
	'get_img_weight'		=> 'get_image_size',
	'show_img_weight'		=> 'show_image_size',
	'get_img_url'			=> 'get_image_url',
	'show_img_url'			=> 'show_image_url',
	'get_img_viewer'		=> 'get_image_viewer',
	'show_img_viewer'		=> 'show_image_viewer',
	'get_short_url'			=> 'get_image_shorturl',
	'get_img_shorturl'		=> 'get_image_shorturl',
	'show_short_url'		=> 'show_image_shorturl',
	'show_img_shorturl'		=> 'show_image_shorturl',
	'get_img_html'			=> 'get_image_html',
	'show_img_html'			=> 'show_image_html',
	'get_img_bbcode'		=> 'get_image_bbcode',
	'show_img_bbcode'		=> 'show_image_bbcode',
	'get_thumb_url'			=> 'get_image_thumb_url',
	'show_thumb_url'		=> 'show_image_thumb_url',
	'get_thumb_html'		=> 'get_image_thumb_linked_html',
	'show_thumb_html'		=> 'show_image_thumb_linked_html',
	'get_thumb_bbcode'		=> 'get_image_thumb_linked_bbcode', //linked
	'show_thumb_bbcode'		=> 'show_image_thumb_linked_bbcode', //linked
	'get_img_dimentions'	=> 'get_image_dimentions',
	'show_img_dimentions'	=> 'show_image_dimentions',
	'get_img_html_size' 	=> 'get_image_attr_dimentions',
	'show_img_html_size'	=> 'show_image_attr_dimentions',
	'get_delete_image_url'	=> 'get_image_delete_url',
	'show_delete_image_url'	=> 'show_image_delete_url',
	'get_delete_image_confirm_url'	=> 'get_image_delete_confirm_url',
	'show_delete_image_confirm_url' => 'show_image_delete_confirm_url'
);
foreach($legacy_functions as $old_function=>$new_function) {
	eval('function '.$old_function.'() { return '.$new_function.'(); }');
}

function get_google_analytics() {
	if(!check_value(chevereto_config('google_analytics_tracking_id'))) return false;
	return '<script type="text/javascript">
  var _gaq = _gaq || [];
  _gaq.push(["_setAccount", "'.chevereto_config('google_analytics_tracking_id').'"]);
  _gaq.push(["_trackPageview"]);
  (function() {
    var ga = document.createElement("script"); ga.type = "text/javascript"; ga.async = true;
    ga.src = ("https:" == document.location.protocol ? "https://ssl" : "http://www") + ".google-analytics.com/ga.js";
    var s = document.getElementsByTagName("script")[0]; s.parentNode.insertBefore(ga, s);
  })();
</script>';
}
register_show_function('get_google_analytics');

?>