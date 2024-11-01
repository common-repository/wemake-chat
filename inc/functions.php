<?php

if(!defined('WMCH_ABSPATH')) exit;

function wmch_reg_esc($c){
    $patterns = array('/\//', '/\^/', '/\./', '/\$/', '/\|/',
        '/\(/', '/\)/', '/\[/', '/\]/', '/\*/', '/\+/',
        '/\?/', '/\{/', '/\}/', '/\,/');
    $replace = array('\/', '\^', '\.', '\$', '\|', '\(', '\)',
        '\[', '\]', '\*', '\+', '\?', '\{', '\}', '\,');
    return preg_replace($patterns,$replace, $c);
}

function wmch_editor_perm(){
    if(current_user_can('administrator') || current_user_can('editor')){
        return true;
    }
    return false;
}

function wmch_ajax_return_esc($arr){
    if(!empty($arr)){
        foreach($arr as $k=>$item){
            if(is_array($item)){
                $arr[$k] = wmch_ajax_return_esc($item);
            }else{
                $arr[$k] = esc_html($item);
            }
        }
    }
    return $arr;
}

function wmch_ajax_return($data){
    echo json_encode(wmch_ajax_return_esc($data));
    exit;
}

function wmch_get_ajax_action_url($action, $parameters = array()){

    $action_url = admin_url('/admin-ajax.php?action='.$action.'&_wpnonce='.wp_create_nonce($action));

    if(count($parameters)){
        foreach($parameters as $par_k=>$par){
            $action_url .= '&'.$par_k.'='.$par;
        }
    }

    return esc_url($action_url);

}

function wmch_upload_image($temp_file_path, $file_name){

    if(empty($temp_file_path) || empty($file_name)){
        return false;
    }

    $file_dir = WMCH_FILES_DIR;
    $file_path = $file_dir . '/' . $file_name;
    $file_ext = pathinfo($file_path, PATHINFO_EXTENSION);

    // Security

    if(!is_uploaded_file($temp_file_path) || !in_array($file_ext, array('jpg', 'jpeg', 'png', 'gif'))){
        return false;
    }

    // Create directory

    if(!file_exists($file_dir)){
        mkdir($file_dir, 0755);
    }

    // Create image from

    if($file_ext=='jpg' || $file_ext=='jpeg'){
        $image = imagecreatefromjpeg($temp_file_path);
    }elseif($file_ext=='png'){
        $image = imagecreatefrompng($temp_file_path);
    }elseif($file_ext=='gif'){
        $image = imagecreatefromgif($temp_file_path);
    }

    // Change file name

    if(file_exists($file_path)){
        $ii = 1;
        $base_file_name = preg_replace('/\.([^\.]*)$/', '', $file_path);
        while(file_exists($file_path)){
            $file_path = $base_file_name . '-' . $ii . '.' . $file_ext;
            $ii++;
        }
    }

    // Save file

    imagejpeg($image, $file_path, WMCH_JPG_QUALITY);
    imagedestroy($image);

    // Check the type of file. We'll use this as the 'post_mime_type'.

    $file_type = wp_check_filetype(basename($file_path), null);

    // Prepare an array of post data for the attachment.

    $attachment = array(
        'guid'           => wmch_path_to_url($file_path),
        'post_mime_type' => $file_type['type'],
        'post_title'     => preg_replace( '/\.[^.]+$/', '', basename($file_path)),
        'post_content'   => '',
        'post_status'    => 'inherit',
        'comment_status' => 'closed'
    );

    // Insert the attachment.

    $attach_id = wp_insert_attachment($attachment, wmch_path_to_url($file_path), 0);

    // Make sure that this file is included, as wp_generate_attachment_metadata() depends on it.

    require_once(ABSPATH.'wp-admin/includes/image.php');

    // Generate the metadata for the attachment, and update the database record.

    $attach_data = wp_generate_attachment_metadata($attach_id, $file_path);
    wp_update_attachment_metadata($attach_id, $attach_data);

    return $attach_id;

}

function wmch_replace_smiles($content, $length = 18){
    if(!empty(trim($content))){
        for($ii=1;$ii<=$length;$ii++){
            $image_c = '<img src="'.WMCH_URI.'/assets/img/smiles/smile'.$ii.'.png" class="wmch-smile">';
            $content = preg_replace('/\[smile'.$ii.'\]/', $image_c, $content);
        }
    }
    return $content;
}

function wmch_path_to_url($path){
    return preg_replace('/^'.wmch_reg_esc(WMCH_DOC_ROOT).'/', WMCH_HTTP_HOST, $path);
}

function wmch_field_has_rtl_class($text){
    if(!empty(preg_match("/\p{Hebrew}/u", $text))){
        esc_attr_e(' wmch-has-rtl');
    }
}

?>