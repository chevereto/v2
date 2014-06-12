<?php if(!defined('access') or !access) die('This file cannot be directly accessed.'); ?>
<?php include_theme_header(); ?>
    <div id="content">
        <form id="upload" action="">
        	<div id="error-box-container"></div>
            <div id="selector">
                <ul>
                    <li><a id="select-local" class="active"><?php show_lang_txt('button_local_upload'); ?></a></li>
                    <li><a id="select-remote"><?php show_lang_txt('button_remote_upload'); ?></a></li>
                </ul>
            </div>
            <div id="upload-tools">
                <a id="preferences"><?php show_lang_txt('button_upload_preferences'); ?></a>
                <div id="upload-params">JPG PNG BMP GIF <span>MAX. <?php show_max_upload_size(); ?></span></div>
            </div>
            <div id="upload-container">
                <div id="preferences-box">
                    <div><input type="checkbox" id="pref-shorturl" <?php if(isset($_COOKIE['doShort'])) echo 'checked="checked"'; ?> /> <label for="pref-shorturl"><?php show_lang_txt('txt_create_short_url'); ?> <?php show_tinyurl_service(); ?></label></div>
                </div>
                <div id="input-container">
                    <div class="upload show_upload" id="upload-local">
                        <h1><?php show_lang_txt('txt_local_upload'); ?></h1>
                        <div id="fileQueue"></div>
                        <div><input style="display: none;" id="uploadify" name="uploadify" type="file" /></div>
                    </div>
                    <div class="upload hide_upload" id="upload-remote">
                        <h1><?php show_lang_txt('txt_remote_upload'); ?></h1>
                        <div id="remote-parser"><input type="text" id="url" name="url" /></div>
                        <div id="remoteQueue"></div>
                    </div>
                </div>
                <div id="resizing">
                    <div id="resizing-switch"><div><a><span><?php show_lang_txt('txt_resizing'); ?></span></a> <?php show_lang_txt('txt_resizing_explained'); ?></div></div>
                    <div id="resizing-box">
                        <div id="resizing-it">
                            <div id="resize-width"><?php show_lang_txt('txt_resizing_width'); ?> <span><?php show_lang_txt('txt_resizing_pixels'); ?></span></div>
                            <input type="text" id="resize" name="resize" maxlength="4"/><div id="resize-keep">*<?php show_lang_txt('txt_resizing_keep'); ?></div>
                        </div>
                    </div>
                </div>
                <div id="upload-action"><a id="upload-button"><span><?php show_lang_txt('button_upload'); ?></span></a><a id="cancel-upload"><?php show_lang_txt('txt_cancel'); ?></a></div>
            </div>
        </form>
        
    </div>

<?php include_theme_footer(); ?>