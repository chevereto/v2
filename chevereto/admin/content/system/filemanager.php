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

?>

<body id="filemanager">

	<div id="top">
    	<a href="<?php echo __CHV_ADMIN_URL__; ?>" id="logo"><img src="<?php echo absolute_to_relative(__CHV_PATH_ADMIN_SYSTEM_IMG__); ?>chevereto.png" alt="" /></a>
        <div id="tagline-total">
            <div id="tagline">File Manager</div>
            <div id="total-files"></div>
        </div>
        <div id="top-links">
        	<a href="<?php echo __CHV_BASE_URL__; ?>" target="_blank"><?php show_lang_txt('txt_homepage'); ?></a>
            <a id="logout"><?php show_lang_txt('txt_logout'); ?><i></i></a>
        </div>
    </div>
    
    <div id="sidebar">
    	<div id="current-selection" class="empty" data-selection="$1 (<span></span>) <a id='clear-current-selection'>$2</a>" data-empty="$3"><?php show_lang_txt('txt_no_image_selection'); ?></div>
        
        <ul class="sidebar-actions" id="sidebar-actions">
        	<li data-action="delete"><i class="icon icon-action delete"></i><?php show_lang_txt('button_delete'); ?></li>
            <li data-action="rename"><i class="icon icon-action rename"></i><?php show_lang_txt('button_rename'); ?></li>
            <li data-action="resize"><i class="icon icon-action resize"></i><?php show_lang_txt('button_resize'); ?></li>
            <li data-action="codes"><i class="icon icon-action codes"></i><?php show_lang_txt('button_codes'); ?></li>
        </ul>
        
        <div id="search-input"><span class="placeholder"><?php show_lang_txt('txt_search_images'); ?></span><i class="loupe"></i><input type="text" id="search" autocomplete="off" searched="" /></div>

        <div id="copyright">
            Chevereto File Manager
            <div>by <a href="http://rodolfoberrios.com" target="_blank">Rodolfo Berrios</a></div>
        </div>
    </div>
    
    <div id="list">
    	<div id="filter-wrap">
            <div id="filter">
                <div id="filter-type" data-value="all">
                    <span class="label"><?php show_lang_txt('txt_type'); ?>:</span>
                    <div class="selectable mimic-select">
                        <span class="select-label"><span><?php show_lang_txt('txt_all'); ?></span><i></i></span>
                        <ul>
                            <li class="active" data-value="jpg"><i></i>jpg</li>
                            <li class="active" data-value="gif"><i></i>gif</li>
                            <li class="active" data-value="png"><i></i>png</li>
                        </ul>
                    </div>
                </div><!-- filter type -->
                <div id="filter-sort" data-value="recent">
                    <span class="label"><?php show_lang_txt('txt_sort_by'); ?>:</span>
                    <ul class="selectable">
                        <li class="active" data-value="date_desc"><?php show_lang_txt('txt_newest'); ?></li>
                        <li data-value="date_asc"><?php show_lang_txt('txt_oldest'); ?></li>
                        <li data-value="size_asc"><?php show_lang_txt('txt_filesize_small'); ?></li>
                        <li data-value="size_desc"><?php show_lang_txt('txt_filesize_large'); ?></li>
                    </ul>
                </div><!-- filter sort -->
            </div><!-- filter -->
        </div><!-- filter wrap -->
        
        <div id="list-items"></div><!-- list items -->
        
    </div><!-- list -->

</body>
</html>