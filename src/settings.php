<?php

add_action('after_setup_theme', 'wcat_init_settings', 1);

$wcat_settings = null;
function wcat_init_settings(){
    global $wcat_settings;
    $wcat_settings = new WCAT\Settings(
        array(),
        array(
            'api' => array(
                'title' => __('API', 'wcat'),
                'fields' => array(
                    'sbn_id' => array(
                        'type' => 'text',
                        'title' => __('Sbn ID', 'wcat')
                    ),
                    'api_secret' => array(
                        'type' => 'text',
                        'title' => __('Secret', 'wcat')
                    )
                )
            ),
            'online_orders' => array(
                'title' => __('Online orders', 'wcat'),
                'description' => __('Section description', 'wcat'),
                'fields' => array(
                    'online_orders_disabled_msg' => array(
                        'type' => 'textarea',
                        'title' => __('Online orders not available message', 'wcat'),
                        'description' => __('Default: Online orders are not available at this time.', 'wcat')
                    )
                )
            ),
            'local_orders' => array(
                'title' => __('Local orders', 'wcat'),
                'fields' => array(
                    'local_orders_enabled' => array(
                        'type' => 'checkbox',
                        'title' => __('Enable local orders', 'wcat')
                    ),
                    'local_order_received_msg' => array(
                        'type' => 'textarea',
                        'title' => __('Local order received message', 'wcat'),
                        'description' => __('Default: Thank you! Your order has been received.', 'wcat')
                    )
                )
            ),
            'product_list' => array(
                'title' => __('Product list', 'wcat'),
                'fields' => array(
                    'show_images' => array(
                        'type' => 'checkbox',
                        'title' => __('Show product images', 'wcat')
                    )
                )
            )
        )
    );    
}

function wcat_get_option($name, $lang=null){
	global $wcat_settings;
	return isset($wcat_settings) ? $wcat_settings->get_option($name, $lang) : false;
}

add_action('admin_notices', function(){
    
    $sbn_id = wcat_get_option('sbn_id');
    $api_secret = wcat_get_option('api_secret');
    
    if(empty($sbn_id) || empty($api_secret)){
        
        if(\WCAT\Helpers::isAdminScreen('woocommerce_page_wcat_page')){
            return;
        }
        
        echo '<div class="notice notice-warning is-dismissible">';
            echo '<p>' . sprintf(__('Please, set Atensiq API credentials in <a href="%s">WooCommerce -> Atensiq</a>', 'wcat'), admin_url('admin.php?page=wcat_page')) . '</p>';
        echo '</div>';
    }
});

add_action('wcat_before_form', function(){
    ?>
    <div class="atq-sbn-info">
        <p>
            <?php 
            echo __('This plugin connects WooCommerce cart to the Atensiq panel.', 'wcat') . ' ';
            echo sprintf(__('See how Atensiq works %s.', 'wcat'), '<a href="' . __('https://atensiq.com/subscriptions/', 'wcat') . '" target="_blank">' . __('here', 'wcat') . '</a>'); 
            ?>
        </p>
    </div>
    <?php
});

add_action('wcat_section_api_after', function(){
    ?>
    <div class="atq-sbn-info">
        <p>
            <?php
            echo sprintf(__('You will need a free subscription %s to get your API credentials.', 'wcat'), '<a href="' . __('https://atensiq.com/subscriptions/', 'wcat') . '" target="_blank">' . __('here', 'wcat') . '</a>');
            ?>
        </p>
    </div>
    <?php
});

add_action('wcat_section_online_orders_after', function(){
    ?>
    <div class="atq-sbn-info">
        <p>
            <?php
            echo __('Online orders can be anabled from the Atensiq panel.', 'wcat') . ' ';
            echo __('Make sure to enable online orders each time you log in to the panel.', 'wcat');
            ?>
        </p>
    </div>
    <?php
});

add_action('wcat_section_local_orders_after', function(){
    ?>
    <div class="atq-sbn-info">
        <p>
            <?php
            echo __('Local orders allow customers at your restaurant to place orders directly without checkout step involved.', 'wcat') . '<br /> ';
            echo __('Make sure to enter your menu page url in Atensiq panel - Settings - Link to your restaurant menu.', 'wcat');
            ?>
        </p>
    </div>
    <?php
});

add_action('wcat_after_form', function(){
    ?>
    <div class="atq-qcart-info atq-sbn-info">
        <p>
            <strong><?php _e('Use this Quickcart shortcode to list your products', 'wcat'); ?>:</strong>
        </p>
        <p>
            [wcat-quickcart cats=""]
        </p>
        <p>
            <strong><?php _e('Options', 'wcat'); ?>:</strong>
        </p>
            cats - <?php _e('Product category slugs. Separate multiple values by comma.', 'wcat'); ?>
        </p>
    </div>
    <?php
});
