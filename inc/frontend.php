<?php

if(!defined( 'WMCH_ABSPATH')) exit;

// Scripts and styles

add_action('wp_enqueue_scripts', function(){
    wp_enqueue_style('wmch-frontend-style',      WMCH_URI.'/assets/css/frontend.css', array(), WMCH_PLUGIN_VERSION, 'all');
    wp_enqueue_script('wmch-frontend-js',        WMCH_URI.'/assets/js/frontend.js', array(), WMCH_PLUGIN_VERSION, true);
    wp_enqueue_script('jquery');
});

// Popup

add_action('wp_footer', function(){

    $whatsapp_number = trim(get_option('wmch_whatsapp_number'));
    $tooltip_text = wmch_replace_smiles(get_option('wmch_tooltip_text'));
    $popup_title = get_option('wmch_popup_title');
    $popup_first_message = wmch_replace_smiles(get_option('wmch_popup_first_message'));
    $popup_second_message = wmch_replace_smiles(get_option('wmch_popup_second_message'));
    $popup_button_text = get_option('wmch_popup_button_text');
    $show_for_mobile = get_option('wmch_show_for_mobile');

    if(empty($tooltip_text)){
        $tooltip_text = __('Hey, need help? We are here', WMCH_PLUGIN_SLUG);
    }
    if(empty($popup_title)){
        $popup_title = __('Help', WMCH_PLUGIN_SLUG);
    }
    if(empty($popup_first_message)){
        $popup_first_message = __('Hey 😊', WMCH_PLUGIN_SLUG);
    }
    if(empty($popup_second_message)){
        $popup_second_message = __('How can we help you?', WMCH_PLUGIN_SLUG);
    }
    if(empty($popup_button_text)){
        $popup_button_text = __('Message', WMCH_PLUGIN_SLUG);
    }

    ?>
    <div class="wmch-popup<?php if(empty($show_for_mobile)) esc_attr_e(' hide-for-mobile'); ?>">
        <div class="wmch-popup-tooltip">
            <img src="<?php esc_attr_e(WMCH_URI); ?>/assets/img/flags.png" alt="wemake chat">
            <?php esc_html_e($tooltip_text); ?>
        </div>
        <div class="wmch-popup-bt"><i class="wmch-icon-whatsapp"><i class="path1"></i><i class="path2"></i><i class="path3"></i><i class="path4"></i></i></div>
        <div class="wmch-popup-window">
            <div class="wmch-popup-head">
                <div class="wmch-popup-close"></div>
                <div class="wmch-popup-devil">
                    <span>
                        <?php esc_html_e($popup_title); ?>
                    </span>
                    <?php
                    if(empty($avatar = get_option('wmch_avatar'))){
                        $avatar = WMCH_URI.'/assets/img/devil.png';
                    }else{
                        $avatar = wp_get_attachment_thumb_url($avatar);
                    }
                    ?>
                    <img src="<?php echo esc_url($avatar); ?>" alt="wemake chat">
                </div>
            </div>
            <div class="wmch-popup-body">
                <div class="wmch-popup-msg wmch-first">
                    <img src="<?php esc_attr_e(WMCH_URI); ?>/assets/img/flags.png" alt="wemake chat">
                    <span>
                        <?php esc_html_e($popup_first_message); ?>
                    </span>
                </div><br>
                <div class="wmch-popup-msg">
                    <img src="<?php esc_attr_e(WMCH_URI); ?>/assets/img/flags.png" alt="wemake chat">
                    <span>
                        <?php esc_html_e($popup_second_message); ?>
                    </span>
                </div>
                <?php
                $whatsapp_url = 'https://api.whatsapp.com/send?phone=' . $whatsapp_number;
                ?>
                <a href="<?php esc_attr_e(esc_url($whatsapp_url)); ?>" class="wmch-popup-bottom" target="_blank" rel="nofollow">
                    <span>
                        <?php esc_html_e($popup_button_text); ?>
                    </span>
                </a>
            </div>
        </div>
    </div>
    <?php
});

?>