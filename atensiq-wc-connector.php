<?php
/*
 * Plugin Name: Atensiq Restaurant menu & Connector for WooCommerce
 * Description: Instant order notifications from WooCommerce in your restaurant´s Atensiq panel. See how Atensiq works on https://atensiq.com
 * Author: Atensiq
 * Author URI: https://atensiq.com
 * Version: 3.0.1
 * Requires at least: 4.6
 * Tested up to: 5.5.3
 * WC tested up to: 4.7.1
 * Text Domain: wcat
 * Domain Path: langs
 */

define('WCAT_VER', '3.0.1');
define('WCAT_ROOT', dirname(__FILE__));
define('WCAT_ROOT_URI', plugin_dir_url(__FILE__));

add_action('plugins_loaded', function(){
    
    if(!class_exists('WooCommerce')){
        add_action('admin_notices', function(){
            echo '<div class="notice notice-warning is-dismissible">';
                echo '<p>' . __('Atensiq Restaurant menu & Connector for WooCommerce requires WooCommerce to work correctly!', 'wcat') . '</p>';
            echo '</div>';
        });
        return;
    }
    
    require WCAT_ROOT . '/src/autoload.php';
    require WCAT_ROOT . '/src/setup.php';
    require WCAT_ROOT . '/src/scripts.php';
    require WCAT_ROOT . '/src/settings.php';
    
    (new WCAT\Actions\Api());
    (new WCAT\Actions\Table());
    (new WCAT\Actions\Quickcart());
    (new WCAT\View\Actions\Quickcart());
    
}, 100);
