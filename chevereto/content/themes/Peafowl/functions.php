<?php
/* --------------------------------------------------------------------

  Chevereto
  http://chevereto.com/

  @version	2.6.0
  @author	Rodolfo Berríos A. <http://rodolfoberrios.com/>
			<inbox@rodolfoberrios.com>

  Copyright (C) 2013 Rodolfo Berríos A. All rights reserved.
  
  BY USING THIS SOFTWARE YOU DECLARE TO ACCEPT THE CHEVERETO EULA
  http://chevereto.com/license

  --------------------------------------------------------------------

  Peafowl Theme Functions
  http://www.chevereto.com/

  @version	1.0
  @author	Rodolfo Berríos A. <http://rodolfoberrios.com/>
			<inbox@rodolfoberrios.com>

  --------------------------------------------------------------------- */

if(!defined('access') or !access) die('This file cannot be directly accessed.');


/**
 * show_social_links
 * echoes the social share links
 * TODO: Set this by db...
 */
function show_social_links($tag, $url, $url2) {
	$html_tag = is_html_tag($tag) ? $tag : '';
	$html_closing_tag = html_closing_tag($html_tag);
	$url = urlencode($url);
	$social_services = array(
	
		"Facebook"		=> array('url' => 'http://www.facebook.com/share.php?u='.$url,
			'target'	=> 'pop-up',
			'pop_h'		=> 350,
			'img'		=> 'ico-facebook.png'
		),
		"Twitter"		=> array('url' => 'http://twitter.com/share?url='.$url.'&via=chevereto', // Change "via" to match your twitter account via.
			'target'	=> 'pop-up',
			'pop_h'		=> 350,
			'img'		=> 'ico-twitter.png'
		),
		"Pinterest"		=> array('url' => 'http://pinterest.com/pin/create/bookmarklet/?media='.$url2.'&url='.$url.'&is_video=false&description=&title=',
			'target'	=> 'pop-up',
			'pop_h'		=> 300,
			'img'		=> 'ico-pinterest.png'
		),
		"StumbleUpon"	=> array('url' => 'http://www.stumbleupon.com/submit?url='.$url,
			'target'	=> '_blank',
			'img'		=> 'ico-stumbleupon.png'
		),
		"Tumblr"		=> array('url' => 'http://www.tumblr.com/share?v=3&u='.$url,
			'target'	=> 'pop-up',
			'pop_h'		=> 450,
			'img'		=> 'ico-tumblr.png'
		),
		"Delicious"		=> array('url' => 'http://www.delicious.com/save?v=5&noui&jump=close&url='.$url,
			'target'	=> 'pop-up',
			'pop_h'		=> 450,
			'img'		=> 'ico-delicious.png'
		),
		"reddit"		=> array('url' => 'http://reddit.com/submit?url='.$url,
			'target'	=> '_blank',
			'img'		=> 'ico-reddit.png'
		),
		"Myspace"		=> array('url' => 'http://www.myspace.com/Modules/PostTo/Pages/?u='.$url,
			'target'	=> 'pop-up',
			'pop_h'		=> 400,
			'img'		=> 'ico-myspace.png'
		)
	);
	foreach($social_services as $key => $value) {
		if($value['target']=='pop-up') {
			$target = 'rel="pop-up" data-height="'.$value['pop_h'].'"';
		} else {
			$target = 'target="_blank"';
		}
		echo $html_tag.'<a href="'.$value['url'].'" '.$target.'><img src="'.get_theme_imgdir().$value['img'].'" alt="'.$key.'" title="'.$key.'" /></a>'.$html_closing_tag."\n";
	}
}


?>