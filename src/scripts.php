<?php

add_action('admin_enqueue_scripts', 'wcat_register_scripts');
add_action('wp_enqueue_scripts', 'wcat_register_scripts');

function wcat_register_scripts(){
    
    wp_register_style('wcat-admin', WCAT_ROOT_URI . 'css/admin.css', WCAT_VER);
    wp_register_style('wcat-front', WCAT_ROOT_URI . 'css/front.css', WCAT_VER);
    wp_register_script('wcat-front', WCAT_ROOT_URI . 'js/front.js', array('jquery'), WCAT_VER, true);
}


add_action('admin_enqueue_scripts', 'wcat_enqueue_scripts_admin', 20);

function wcat_enqueue_scripts_admin(){
    
    if(WCAT\Helpers::isAdminScreen('woocommerce_page_wcat_page')){
        wp_enqueue_style('wcat-admin');
    }
}

add_action('wp_enqueue_scripts', 'wcat_enqueue_scripts_front', 20);

function wcat_enqueue_scripts_front(){
    
    //if(WCAT\Helpers::isQuickcartShortcodePage()){
        wp_enqueue_script('wcat-front');
        wp_enqueue_style('wcat-front');
    //}
}

add_filter('wcat_data', 'wcat_data_add_vars');

function wcat_data_add_vars($vars){
    
    $vars['ajaxurl'] = admin_url('admin-ajax.php');
    
    return $vars;
}
