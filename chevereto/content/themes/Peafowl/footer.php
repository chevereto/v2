<?php if(!defined('access') or !access) die('This file cannot be directly accessed.'); ?>
</div><!-- wrap -->

<div id="foot">
	<div id="in-foot">
    	<div id="foot-content">
        	<ul>
            	<?php show_page_links('<li>'); ?>
                <?php if(is_logged_user()) :?><li><a rel="logout"><?php show_lang_txt('txt_logout'); ?></a></li><?php endif; ?>
            </ul>
            <p>&copy; <a href="<?php show_base_url(); ?>" id="c_chevereto"><?php echo chevereto_config('site_name'); ?></a></p>
        </div>
		<a href="http://chevereto.com/" target="_blank" id="powered" title="Chevereto Image Hosting Script">Powered by <img src="<?php echo show_theme_imgdir(); ?>chevereto.gif" alt="Chevereto image hosting script" /></a><!-- Support Chevereto, keep it for the pride! -->
    </div>
</div>

</body>

</html>