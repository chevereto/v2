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

/*
 * pages_config.php
 * Here you can set up your pages for Chevereto. You can set on/off status, doctitle, etc.
 * By default this comes with 3 pages.
 *
 * NOTES >>
 * - The array order is also the default sort order.
 * - 'live' values can be true/false
 * - 'title' values can use $lang[] values or any value.
 */

$pages_config = array (
	'about' 	=> array('live' => true, 'title' => 'About Us'),
	'tos' 		=> array('live' => true, 'title' => 'Terms of Service'),
	'contact'	=> array('live' => true, 'title' => 'Contact'),
);

?>