<?php

if(!defined('WMCH_ABSPATH')) exit;

// Scripts and styles

add_action('admin_enqueue_scripts', function(){

    wp_enqueue_style('wmch-admin-style',        WMCH_URI.'/assets/css/admin.css', array(), WMCH_PLUGIN_VERSION, 'all');
    wp_enqueue_script('wmch-admin-js', 	        WMCH_URI.'/assets/js/admin.js', array(), null, true);
    wp_enqueue_script('wmch-admin-sfi-js', 	    WMCH_URI.'/lib/js/jquery.simplefileinput.min.js', array(), null, true);
    wp_enqueue_script('jquery-form');

});

// Plugin page

function wmch_add_settings_page() {
    add_options_page(
        __('Wemake Chat', WMCH_PLUGIN_SLUG),
        __('Wemake Chat', WMCH_PLUGIN_SLUG),
        'manage_options',
        'wm-chat',
        'wmch_plugin_settings_page'
    );
}

add_action('admin_menu', 'wmch_add_settings_page');

function wmch_plugin_settings_page(){

    $whatsapp_number = get_option('wmch_whatsapp_number');
    $tooltip_text = get_option('wmch_tooltip_text');
    $popup_avatar = get_option('wmch_avatar');
    $popup_title = get_option('wmch_popup_title');
    $popup_first_message = get_option('wmch_popup_first_message');
    $popup_second_message = get_option('wmch_popup_second_message');
    $popup_button_text = get_option('wmch_popup_button_text');
    $show_for_mobile = get_option('wmch_show_for_mobile');

    ?>
    <form method="POST" action="<?php esc_attr_e(wmch_get_ajax_action_url('wmch_change_settings')); ?>" class="wmch-form" enctype="multipart/form-data">
        <input type="hidden" name="MAX_FILE_SIZE" value="5242880" />
        <div class="wmch-result"></div>
        <div class="wmch-form-field<?php wmch_field_has_rtl_class($whatsapp_number); ?>">
            <label for="whatsapp_number" class="wmch-form-label">
                <?php _e('Whatsapp number', WMCH_PLUGIN_SLUG); ?>
            </label><br>
            <input type="number" name="whatsapp_number" value="<?php esc_attr_e($whatsapp_number); ?>" id="whatsapp_number">
        </div>
        <div class="wmch-form-field wmch-allow-smiles<?php wmch_field_has_rtl_class($tooltip_text); ?>">
            <label for="tooltip_text" class="wmch-form-label">
                <?php _e('Tooltip text', WMCH_PLUGIN_SLUG); ?>
                <span class="wmch-form-tooltip">
                    <span class="wmch-form-tooltip-q">i</span>
                    <span class="wmch-form-tooltip-c">
                        <img src="<?php esc_attr_e(WMCH_URI); ?>/assets/img/scr/tooltip-text.jpg">
                    </span>
                </span>
            </label><br>
            <input type="text" name="tooltip_text" value="<?php esc_attr_e($tooltip_text); ?>" id="tooltip_text">
            <div class="wmch-show-smiles"></div>
        </div>
        <div class="wmch-form-field">
            <label for="popup_avatar" class="wmch-form-label">
                <?php _e('Avatar', WMCH_PLUGIN_SLUG); ?>
            </label><br>
            <?php
            $popup_avatar = wp_get_attachment_thumb_url($popup_avatar);
            ?>
            <div class="wmch-form-pic<?php if(!empty($popup_avatar)) esc_attr_e(' selected'); ?>">
                <input type="hidden" name="remove_avatar" value="0">
                <input type="file" name="popup_avatar" id="popup_avatar" class="custom-input-file">
                <button class="wmch-form-pic-bt">
                    <?php _e('Select', WMCH_PLUGIN_SLUG); ?>
                </button>
                <div class="wmch-form-pic-img">
                    <?php if(!empty($popup_avatar)){ ?>
                        <img src="<?php esc_attr_e($popup_avatar); ?>">
                    <?php } ?>
                </div>
                <div class="wmch-form-pic-sel" data-empty-text="<?php _e('No file selected', WMCH_PLUGIN_SLUG); ?>">
                    <?php
                    if(empty($popup_avatar)){
                        _e('No file selected', WMCH_PLUGIN_SLUG);
                    }else{
                        echo esc_html_e(basename($popup_avatar));
                    }
                    ?>
                </div>
                <i class="wmch-form-pic-rem"></i>
            </div>
        </div>
        <div class="wmch-form-field<?php wmch_field_has_rtl_class($popup_title); ?>">
            <label for="popup_title" class="wmch-form-label">
                <?php _e('Popup title', WMCH_PLUGIN_SLUG); ?>
                <span class="wmch-form-tooltip">
                    <span class="wmch-form-tooltip-q">i</span>
                    <span class="wmch-form-tooltip-c">
                        <img src="<?php esc_attr_e(WMCH_URI); ?>/assets/img/scr/popup-title.jpg">
                    </span>
                </span>
            </label><br>
            <input type="text" name="popup_title" value="<?php esc_attr_e($popup_title); ?>" id="popup_title">
        </div>
        <div class="wmch-form-field wmch-allow-smiles<?php wmch_field_has_rtl_class($popup_first_message); ?>">
            <label for="popup_first_message" class="wmch-form-label">
                <?php _e('First message text', WMCH_PLUGIN_SLUG); ?>
                <span class="wmch-form-tooltip">
                    <span class="wmch-form-tooltip-q">i</span>
                    <span class="wmch-form-tooltip-c">
                        <img src="<?php esc_attr_e(WMCH_URI); ?>/assets/img/scr/popup-first-message.jpg">
                    </span>
                </span>
            </label><br>
            <input type="text" name="popup_first_message" value="<?php esc_attr_e($popup_first_message); ?>" id="popup_first_message">
            <div class="wmch-show-smiles"></div>
        </div>
        <div class="wmch-form-field wmch-allow-smiles<?php wmch_field_has_rtl_class($popup_second_message); ?>">
            <label for="popup_second_message" class="wmch-form-label">
                <?php _e('Second message text', WMCH_PLUGIN_SLUG); ?>
                <span class="wmch-form-tooltip">
                    <span class="wmch-form-tooltip-q">i</span>
                    <span class="wmch-form-tooltip-c">
                        <img src="<?php esc_attr_e(WMCH_URI); ?>/assets/img/scr/popup-second-message.jpg">
                    </span>
                </span>
            </label><br>
            <input type="text" name="popup_second_message" value="<?php esc_attr_e($popup_second_message); ?>" id="popup_second_message">
            <div class="wmch-show-smiles"></div>
        </div>
        <div class="wmch-form-field<?php wmch_field_has_rtl_class($popup_button_text); ?>">
            <label for="popup_button_text" class="wmch-form-label">
                <?php _e('Button text', WMCH_PLUGIN_SLUG); ?>
                <span class="wmch-form-tooltip">
                    <span class="wmch-form-tooltip-q">i</span>
                    <span class="wmch-form-tooltip-c">
                        <img src="<?php esc_attr_e(WMCH_URI); ?>/assets/img/scr/popup-button-text.jpg">
                    </span>
                </span>
            </label><br>
            <input type="text" name="popup_button_text" value="<?php esc_attr_e($popup_button_text); ?>" id="popup_button_text">
        </div>
        <div class="wmch-form-field wmch-checkbox">
            <input type="checkbox" name="show_for_mobile" value="1" id="wmch_show_for_mobile"<?php if(!empty($show_for_mobile)) esc_attr_e(' checked'); ?>>
            <label for="wmch_show_for_mobile" class="wmch-form-label">
                <?php _e('Show for mobile devices', WMCH_PLUGIN_SLUG); ?>
            </label>
        </div>
        <div class="wmch-smile-popup">
            <?php for($ii=128512;$ii<=128577;$ii++){ ?>
                <span class="smile">&#<?php echo $ii; ?></span>
            <?php } ?>
        </div>
        <button type="submit" class="wmch-form-submit">
            <?php _e('Save settings', WMCH_PLUGIN_SLUG); ?>
        </button>
    </form>
    <?php
}

// Admin footer

add_action('admin_footer', function(){
    ?>
    <script>
        <?php if(isset($_GET['page']) && $_GET['page']=='wm-chat'){ ?>
            var wmch_language = {
                "unsaved_changes": "<?php esc_attr_e('You have unsaved changes', WMCH_PLUGIN_SLUG); ?>",
                "request_error": "<?php esc_attr_e('Request error!', WMCH_PLUGIN_SLUG); ?>",
                "success": "<?php esc_attr_e('Settings successfully changed', WMCH_PLUGIN_SLUG); ?>",
                "upload_error1" : "<?php esc_attr_e('File size error. Maximum file size - 5mb.', WMCH_PLUGIN_SLUG); ?>",
                "upload_error2" : "<?php esc_attr_e('Uploading error. Please select other file.', WMCH_PLUGIN_SLUG); ?>",
                "upload_error3" : "<?php esc_attr_e('Bad file format. Supported formats: jpg, jpeg, png, gif.', WMCH_PLUGIN_SLUG); ?>",
            };
        <?php } ?>
    </script>
    <?php
});

// "Settings" link

add_filter('plugin_action_links_wemake-chat/wemake-chat.php', function($links){
    $url = get_admin_url() . 'options-general.php?page=wm-chat';
    $settings_link = array('settings' => '<a href="' . $url . '">' . __('Settings', WMCH_PLUGIN_SLUG) . '</a>');
    $links = array_merge($settings_link, $links);
    return $links;
});

?>