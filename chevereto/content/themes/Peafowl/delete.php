<?php if(!defined('access') or !access) die('This file cannot be directly accessed.'); ?>
<?php include_theme_header(); ?>
    <div id="content">
    	<div id="error-box-container"></div>
        <div id="delete-deleted-container"></div>
    	<h2 id="delete-confirm-msg"><?php show_lang_txt('txt_delete_confirm_single'); ?></h2>
        <h2 class="viewing"><a href="<?php show_image_shorturl(); ?>" target="_blank"><?php show_image_filename(); ?></a> (<?php show_image_size(); ?> - <?php show_image_dimentions(); ?>)</h2>
        <div id="delete-confirm-cancel"><a id="delete-image-confirm" href="<?php show_delete_image_confirm_url(); ?>"><?php show_lang_txt('button_delete'); ?></a> <a id="delete-cancel" href="<?php show_image_viewer(); ?>"><?php show_lang_txt('txt_cancel'); ?></a></div>
        <div class="view-full-image"><a href="<?php show_image_shorturl(); ?>" target="_blank"><img src="<?php show_image_url(); ?>" alt="<?php show_image_filename(); ?>" class="full_image" /></a></div>
    </div>
<?php include_theme_footer(); ?>