<?php

/*
Plugin Name: Wemake Chat
Plugin URI: https://wordpress.org/plugins/wemake-chat/
Description: WhatsApp chat button
Version: 1.1
Author: Wemake
Author URI: http://wemake.co.il
License: GPL-2.0+
License URI: http://www.gnu.org/licenses/gpl-2.0.txt
Text Domain: wemake-chat

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

// Constants

define("WMCH_PLUGIN_NAME", 'Wemake Chat');
define("WMCH_PLUGIN_SLUG", 'wemake-chat');
define("WMCH_PLUGIN_VERSION", '1.1');
define("WMCH_ABSPATH", dirname( __FILE__ ));
define("WMCH_URI", plugins_url().'/'.WMCH_PLUGIN_SLUG);

define('WMCH_DOC_ROOT', preg_replace('/\/$/', '', ABSPATH));
define('WMCH_HTTP_HOST', get_site_url());

define('WMCH_JPG_QUALITY', 85);

$upload_dir = wp_upload_dir();

define('WMCH_FILES_DIR', $upload_dir['basedir'] . '/' . WMCH_PLUGIN_SLUG);

// PHP version

if(version_compare(phpversion(), '5.6.40', '<')){
    add_action('admin_notices', function(){
        $message = 'Your server is running PHP version '.phpversion().' but '.WMCH_PLUGIN_NAME.' requires at least 5.6.40. The plugin does not work.';
        printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr('notice notice-error'), esc_html( $message ) );
    });
    return false;
}

// Functions

require_once(WMCH_ABSPATH . '/inc/functions.php');

// AJAX actions

if(isset($_GET['action']) && (function_exists('wp_doing_ajax') &&  wp_doing_ajax() || defined('DOING_AJAX'))){
    require_once(WMCH_ABSPATH . '/inc/action.php');
}

// Languages

add_action('init', function(){
    if((is_admin() || is_multisite() && is_network_admin()) && function_exists('get_user_locale')){
        $locale = get_user_locale();
    }elseif(function_exists('get_locale')){
        $locale = get_locale();
    }else{
        $locale = 'en_US';
    }
    load_textdomain(WMCH_PLUGIN_SLUG, WMCH_ABSPATH.'/languages/'.$locale.'.mo');
});

// Run controllers

add_action("wp_loaded", function(){
    if(is_admin() || is_multisite() && is_network_admin()){
        require_once(WMCH_ABSPATH . '/inc/admin.php');
    }else{
        require_once(WMCH_ABSPATH . '/inc/frontend.php');
    }
});

?>
