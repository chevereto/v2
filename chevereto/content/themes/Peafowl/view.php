<?php if(!defined('access') or !access) die('This file cannot be directly accessed.'); ?>
<?php include_theme_header(); ?>
    <div id="content">
        
        <h2 class="viewing"><?php show_lang_txt('txt_viewing'); ?> <a href="<?php show_image_shorturl(); ?>" target="_blank"><?php show_image_filename(); ?></a> (<?php show_image_size(); ?> - <?php show_image_dimentions(); ?>)</h2>
        
        <div class="view-full-image"><a href="<?php show_image_shorturl(); ?>" target="_blank"><img src="<?php show_image_url(); ?>" alt="<?php show_image_filename(); ?>" id="full_image" /></a></div>
        
        <div class="image_tools">
        
        	<?php if(is_viewer() && use_facebook_comments()) : ?>
            <div id="fb-root"></div>
            <div class="image-tools-section">
                <fb:comments href="<?php show_image_viewer(); ?>" num_posts="10" width="670"></fb:comments>
            </div>        
            <?php endif; ?>   
             
            <div class="image-tools-section socialize">
                <h3><?php show_lang_txt('txt_socialize'); ?> <span><?php show_lang_txt('txt_socialize_desc'); ?></span></h3>
                <div class="input-item">
                    <label><?php show_lang_txt('txt_socialize_label'); ?>:</label>
                    <ul class="input-element">
                        <?php show_social_links('<li>', get_image_viewer(), get_image_url()); ?>              
                    </ul>
                </div>
            </div>

            <div class="image-tools-section show_directly">
                    <h3><?php show_lang_txt('txt_show_directly'); ?> <span><?php show_lang_txt('txt_show_directly_desc'); ?></span></h3>
                    <div class="input-item"><label for="short-url-internal-<?php show_image_id(); ?>"><a href="<?php show_image_shorturl(); ?>"><?php show_lang_txt('txt_show_directly_shorturl'); ?></a>:</label> <input type="text" id="short-url-internal-<?php show_image_id(); ?>" value="<?php show_image_shorturl(); ?>" /></div>
                    <div class="input-item"><label for="viewer-link-<?php show_image_id(); ?>"><a href="<?php show_image_viewer(); ?>"><?php show_lang_txt('txt_show_viewer_link'); ?></a>:</label> <input type="text" id="viewer-link-<?php show_image_id(); ?>" value="<?php show_image_viewer(); ?>" /></div>
                    <div class="input-item"><label for="direct-link-<?php show_image_id(); ?>"><a href="<?php show_image_url(); ?>"><?php show_lang_txt('txt_show_directly_link'); ?></a>:</label> <input type="text" id="direct-link-<?php show_image_id(); ?>" value="<?php show_image_url(); ?>" /></div>
                    
                    <?php if(is_config_short_url() && is_user_preference_short_url()) : ?>
                    <div class="input-item"><label for="short-url-service-<?php show_image_id(); ?>"><a id="short-url-service-link" href="<?php //show_short_url('service'); ?>"><?php show_tinyurl_service(); ?></a>:</label> <input type="text" id="short-url-service-<?php show_image_id(); ?>" value="<?php show_image_shorturl_service(); ?>" /></div>
                    <?php endif; ?>
                    
                    <div class="input-item"><label for="html-image-<?php show_image_id(); ?>"><?php show_lang_txt('txt_show_directly_html'); ?>:</label> <input type="text" id="html-image-<?php show_image_id(); ?>" value="<?php show_image_html(); ?>" /></div>
                    <div class="input-item"><label for="bb-code-<?php show_image_id(); ?>"><?php show_lang_txt('txt_thumb_plus_link_bbcode'); ?>:</label> <input type="text" id="bb-code-<?php show_image_id(); ?>" value="<?php show_image_bbcode(); ?>" /></div>
                </div>
                
                <div class="image-tools-section thumb_plus_link">
                    <h3><?php show_lang_txt('txt_thumb_plus_link'); ?> <span><?php show_lang_txt('txt_thumb_plus_link_desc'); ?></span></h3>
                    <div class="input-item"><label for="html-code-<?php show_image_id(); ?>"><?php show_lang_txt('txt_thumb_plus_link_html'); ?>:</label> <input type="text" id="html-code-<?php show_image_id(); ?>" value="<?php show_image_thumb_linked_html(); ?>" /></div>
                    <div class="input-item"><label for="bb-code-thumb-<?php show_image_id(); ?>"><?php show_lang_txt('txt_thumb_plus_link_bbcode'); ?>:</label> <input type="text" id="bb-code-thumb-<?php show_image_id(); ?>" value="<?php show_image_thumb_linked_bbcode(); ?>" /></div>
                    <div class="input-item"><label for="thumb-url-<?php show_image_id(); ?>"><?php show_lang_txt('txt_thumb_url'); ?>:</label> <input type="text" id="thumb-url-<?php show_image_id(); ?>" value="<?php show_image_thumb_url(); ?>" /></div>
                </div>

        </div>
    </div>
<?php include_theme_footer(); ?>