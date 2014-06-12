<?php if(!defined('access') or !access) die('This file cannot be directly accessed.'); ?>
<?php include_theme_header(); ?>
    <div id="content">

    	<?php if(is_multiupload_result()) : ?>
    	<h2><?php show_lang_txt("txt_just_uploaded"); ?> <?php show_images_count(); ?> <?php show_lang_txt("txt_images"); ?></h2>
        
        <div id="multi-codes">
        	<select>
            	<option value="short-urls-internal" selected="selected"><?php show_lang_txt("txt_show_directly_shorturl"); ?></option>
                <option value="direct-links"><?php show_lang_txt("txt_multicodes_directlink"); ?></option>
                <?php if(has_images_shorturls_service()) : ?><option value="short-urls-service"><?php show_tinyurl_service(); ?></option><?php endif; ?>
                <option value="html-codes"><?php show_lang_txt("txt_multicodes_html"); ?></option>
                <option value="bb-codes"><?php show_lang_txt("txt_multicodes_bbcode"); ?></option>
                <option value="html-thumb-codes"><?php show_lang_txt("txt_multicodes_thumbs_html"); ?></option>
                <option value="thumb-bb-codes"><?php show_lang_txt("txt_multicodes_thumbs_bbcode"); ?></option>
			</select>
            <textarea id="short-urls-internal" readonly="readonly" style="display: block;"><?php show_images_shorturls('\n'); ?></textarea>
            <textarea id="direct-links" readonly="readonly"><?php show_images_urls('\n'); ?></textarea>
            <?php if(has_images_shorturls_service()) : ?><textarea id="short-urls-service" readonly="readonly"><?php show_images_shorturls_service('\n'); ?></textarea><?php endif; ?>
            <textarea id="html-codes" readonly="readonly"><?php show_images_html('<img src="%IMAGE_URL%" alt="%IMAGE_FILENAME%" border="0" />', ' '); ?></textarea>
            <textarea id="bb-codes" readonly="readonly"><?php show_images_bbcode('[img]%IMAGE_URL%[/img]', ' '); ?></textarea>
            <textarea id="html-thumb-codes" readonly="readonly"><?php show_images_thumbs_linked_html('viewer', 'blank', '<img src="%IMAGE_THUMB_URL%" alt="%IMAGE_FILENAME%" border="0" />', ' '); ?></textarea>
            <textarea id="thumb-bb-codes" readonly="readonly"><?php show_images_thumbs_linked_bbcode('viewer', 'blank', '[img]%IMAGE_THUMB_URL%[/img]', ' '); ?></textarea>
		</div>
        
        <div id="uploaded">
        	<div id="uploaded_list" visible-rows="2">
            	<?php show_images_thumbs_raw_html('<a rel="%IMAGE_ID%" href="%IMAGE_VIEWER%"><img src="%IMAGE_THUMB_URL%" /></a>'); ?>
			</div>
		</div>
   		
        <?php endif; ?>
        
        <?php while(have_images()) : the_image(); ?>
        <div id="uploaded_image-<?php show_image_id(); ?>" class="uploaded_image" <?php if(!is_first_loop()) : ?> style="display: none;"<?php endif; ?>>
         
            <h2 class="viewing"><?php show_lang_txt(is_singleupload_result() ? 'txt_just_uploaded' : 'txt_viewing'); ?> <a href="<?php show_image_shorturl(); ?>" target="_blank"><?php show_image_filename(); ?></a> (<?php show_image_size(); ?> - <?php show_image_dimentions(); ?>)<span class="loading-image"></span></h2>
            
            <div class="view-full-image"><a href="<?php show_image_shorturl(); ?>" target="_blank"><img src="<?php show_image_url(); ?>" alt="<?php show_image_filename(); ?>" width="<?php show_image_width(); ?>" height="<?php show_image_height(); ?>" class="full_image" /></a></div>
            
            <div class="image_tools">
            
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
                
                <div class="image-tools-section delete_link">
                    <h3>Delete link <span>save it for later</span></h3>
                    <div class="input-item"><label for="delete-link-input-<?php show_image_id(); ?>"><a id="delete-link" href="<?php show_image_delete_url(); ?>">Delete link</a></label>  <input type="text" id="delete-link-input-<?php show_image_id(); ?>" value="<?php show_image_delete_url(); ?>" /></div>
                </div>
                
            </div>
            
        </div>
		<?php endwhile; ?>
        
    </div>
<?php include_theme_footer(); ?>