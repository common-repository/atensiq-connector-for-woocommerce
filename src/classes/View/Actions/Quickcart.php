<?php
namespace WCAT\View\Actions;
use WCAT\Helpers;

class Quickcart {
    
    public function __construct(){
        
        add_shortcode('wcat-quickcart', [$this, 'addQuickcartShortcode']);
        add_action('woocommerce_widget_shopping_cart_buttons', [$this, 'addPlaceOrderButton'], 20);
        add_action('woocommerce_after_mini_cart', [$this, 'addClear']);
    }
    
    public function addQuickcartShortcode($atts){
        
        $atts = shortcode_atts(array(
            'cats' => ''
        ), $atts, 'at-quickcart');
        
        $prods = \WCAT\Quickcart::getProducts($atts['cats']);
        
        return \WCAT\View\Quickcart::getCart($prods);
    }
    
    public function addPlaceOrderButton(){
        
        echo '<button type="button" class="button place-order-btn">';
            echo __('Order', 'wcat');
        echo '</button>';
    }
    
    public function addClear(){
        
        echo '<div class="wcat-clear"></div>';
    }
    
}
