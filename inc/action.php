<?php

function wmch_change_settings_action(){

    // Security

    if(!wmch_editor_perm() || check_ajax_referer($_REQUEST['action'])!==1 || wp_verify_nonce($_REQUEST['_wpnonce'], $_REQUEST['action'])!==1) {
        return;
    }

    // Header

    header('Content-Type: application/json');

    // Result array

    $result = array('success' => 0, 'error' => 0);

    // Sanitize post data

    $allow_options = array(
        'whatsapp_number',
        'tooltip_text',
        'popup_title',
        'popup_first_message',
        'popup_second_message',
        'popup_button_text',
    );

    $options = array();

    if(!empty($_POST)){
        foreach($_POST as $key=>$value){
            if(in_array($key, $allow_options)){
                $options[sanitize_key($key)] = sanitize_text_field($value);
            }
        }
        $options['show_for_mobile'] = isset($_POST['show_for_mobile']) ? intVal(sanitize_text_field($_POST['show_for_mobile'])) : 0;
        $options['remove_avatar'] = isset($_POST['remove_avatar']) ? intVal(sanitize_text_field($_POST['remove_avatar'])) : 0;
    }else{
        return;
    }

    // Update options

    foreach($allow_options as $option){
        if(isset($options[$option])){
            update_option('wmch_'.$option, $options[$option]);
        }
    }

    // Show for mobile device

    update_option('wmch_show_for_mobile', $options['show_for_mobile']);

    // Remove avatar

    if(!empty($options['remove_avatar'])){
        update_option('wmch_avatar', '');
    }

    // Sanitize files

    $file_input_name = 'popup_avatar';

    if(isset($_FILES) && is_array($_FILES)){
        $file = array(
            'tmp_name'  => isset($_FILES[$file_input_name]['tmp_name']) ? $_FILES[$file_input_name]['tmp_name'] : '',
            'name'      => isset($_FILES[$file_input_name]['name']) ? sanitize_file_name($_FILES[$file_input_name]['name']) : '',
            'size'      => isset($_FILES[$file_input_name]['size']) ? intVal($_FILES[$file_input_name]['size']) : 0,
            'error'     => isset($_FILES[$file_input_name]['error']) ? intVal($_FILES[$file_input_name]['error']) : 0,
        );
        if(!empty($file['tmp_name']) && !is_uploaded_file($_FILES[$file_input_name]['tmp_name'])){
            $file['tmp_name'] = '';
        }
    }

    // Upload avatar

    if(!empty($file['tmp_name']) && !empty($file['name'])){

        // Check file size

        if(empty($file['size']) && !empty($file['error']) || $file['size'] > 5242880){
            wmch_ajax_return(array('error' => 1));
        }

        if(empty($file['size']) || !empty($file['error'])){
            wmch_ajax_return(array('error' => 2));
        }

        // Check file type

        $ext_ex = explode(".", $file['name']);

        if(count($ext_ex) <= 1 || !in_array(strtolower(array_pop($ext_ex)), array('jpg', 'jpeg', 'png', 'gif'))){
            wmch_ajax_return(array('error' => 3));
        }

        // Move image

        $attach_id = wmch_upload_image($file['tmp_name'], $file['name']);

        if(!empty($attach_id) && !empty($file_url = wp_get_attachment_thumb_url($attach_id))){
            update_option('wmch_avatar', intVal($attach_id));
            $result['avatar'] = esc_url($file_url);
        }

    }

    $result['success'] = 1;

    // Return result

    wmch_ajax_return($result);

}
add_action('wp_ajax_wmch_change_settings', 'wmch_change_settings_action');

?>