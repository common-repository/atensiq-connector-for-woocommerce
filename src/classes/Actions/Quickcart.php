<?php
namespace WCAT\Actions;
use WCAT\Helpers;

class Quickcart {
    
    public function __construct(){
        
        add_action('wp_ajax_wcat_qcart_add', [$this, 'addProductAjax']);
        add_action('wp_ajax_nopriv_wcat_qcart_add', [$this, 'addProductAjax']);
        
        add_action('wp_ajax_wcat_qcart_del', [$this, 'delProductAjax']);
        add_action('wp_ajax_nopriv_wcat_qcart_del', [$this, 'delProductAjax']);
        
        add_action('wp_ajax_wcat_qcart_get_product', [$this, 'getProductAjax']);
        add_action('wp_ajax_nopriv_wcat_qcart_get_product', [$this, 'getProductAjax']);
        
    }
    
    public function addProductAjax(){
        
        $product_id = Helpers::getReq('product_id', 'integer');
        $variation_id = Helpers::getReq('variation_id', 'integer');
        
        if(!$product_id){
            Helpers::respondJson(false);
        }
        
        $product_qty = Helpers::getReq('qty', 'integer');
        
        if(!$product_qty){
            $product_qty = 1;
        }
        
        $product_id_cart = $variation_id ? $variation_id : $product_id;
        $item_key = WC()->cart->add_to_cart($product_id_cart, $product_qty);
        
        if(!$item_key){
            Helpers::respondJson(false);
        }

        $redirect = '';
        if(get_option('woocommerce_cart_redirect_after_add') == 'yes'){
            $redirect = wc_get_cart_url();
        }
        
        Helpers::respondJson(true, array(
            'values' => array(
                'product_html' => \WCAT\View\Quickcart::getProductItem($product_id),
                'cart_empty' => intval(WC()->cart->is_empty())
            ),
            'redirect' => $redirect
        ));
        
    }
    
    public function delProductAjax(){
        
        $product_id = Helpers::getReq('product_id', 'integer');
        $cart_key = Helpers::getReq('cart_key');
        
        if(!($product_id && !empty($cart_key))){
            Helpers::respondJson(false);
        }
        
        if(WC()->cart->remove_cart_item($cart_key)){
            Helpers::respondJson(true, array(
                'values' => array(
                    'product_html' => \WCAT\View\Quickcart::getProductItem($product_id),
                    'cart_empty' => intval(WC()->cart->is_empty())
                )
            ));
        }

        Helpers::respondJson(false);
        
    }
    
    public function getProductAjax(){
        
        $product_id = Helpers::getReq('product_id', 'integer');
        
        if($product_id){
            Helpers::respondJson(true, array(
                'values' => array(
                    'product_html' => \WCAT\View\Quickcart::getProductItem($product_id),
                    'cart_empty' => intval(WC()->cart->is_empty())
                )
            ));
        }

        Helpers::respondJson(false);
        
    }
    
}
