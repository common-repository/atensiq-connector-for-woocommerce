<?php
namespace WCAT\Actions;
use WCAT\Helpers;
use WCAT\Table;

class Api extends \WCAT\Api {
    
    public function __construct(){
        
        //add_action('woocommerce_payment_complete', [$this, 'placeOrder']);
        add_action('woocommerce_order_status_processing', [$this, 'placeOrder']);
        
        add_action('wp_ajax_nopriv_wcat_place_local_order', [$this, 'placeLocalOrderAjax']);
        add_action('wp_ajax_wcat_place_local_order', [$this, 'placeLocalOrderAjax']);
        
        add_action('wp', [$this, 'addOrdersDisabledNotice']);
        add_action('quickcart_top', [$this, 'printOrdersDisabledNotice']);
    }
    
    public function placeOrder($order_id){
        
        $wc_order = wc_get_order($order_id);
        
        if(!$wc_order){
            return false;
        }
        
        $req_fields = array_merge($this->getCreds(), array(
            'type' => 'online_order',
            'order_id' => $order_id,
            'order_shipping_name' => $wc_order->get_formatted_shipping_full_name(),
            'order_shipping_addr' => $wc_order->get_formatted_shipping_address(),
            'order_shipping_phone' => $wc_order->get_billing_phone(),
            'order_shipping_method' => $wc_order->get_shipping_method(),
            'order_shipping_note' => $wc_order->get_customer_note(),
            'order_payment_method' => $wc_order->get_payment_method_title(),
            'order_items' => $this->getOrderItems($wc_order),
            'order_total' => wc_price($wc_order->get_total())
        ));
        
        $resp = Helpers::doRequest(
            self::API_ENDPOINT . '/order/create', 
            $req_fields
        );
        
        return $resp;
    }
    
    public function placeLocalOrderAjax(){
        
        if(!wcat_get_option('local_orders_enabled')){
            Helpers::respondJson(false, [
                'error_messages' => Helpers::getMessageHtml(__('Local orders not available.', 'wcat'), 'error')
            ]);
        }
        
        $table_num = Helpers::getTableNum();
        $order_items = $this->getCartOrderItems();
        
        if(!($table_num && !empty($order_items))){
            Helpers::respondJson(false, [
                'error_messages' => Helpers::getMessageHtml(__('Failed to place order.', 'wcat'), 'error')
            ]);
        }
        
        $req_fields = array_merge($this->getCreds(), array(
            'type' => 'local_order',
            'table_num' => $table_num,
            'order_items' => $order_items,
            'order_total' => wc_price(WC()->cart->get_subtotal())
        ));
        
        $resp = Helpers::doRequest(
            self::API_ENDPOINT . '/order/create', 
            $req_fields
        );
        
        if($resp !== false && isset($resp['status']) && intval($resp['status'])){
            WC()->cart->empty_cart();
            Helpers::respondJson(true, [
                'ok_messages' => Helpers::getMessageOptionHtml('local_order_received_msg', 'success', __('Thanky you! Your order has been received.', 'wcat'))
            ]);
        }
        
        Helpers::respondJson(false, [
            'error_messages' => Helpers::getMessageHtml(__('Failed to place order.', 'wcat'), 'error')
        ]);
    }
    
    public function addOrdersDisabledNotice(){
        
        if(!(is_cart() || is_checkout())){
            return;
        }
        
        $disabled_error = $this->getOnlineOrdersDisabledError();
        
        if(!empty($disabled_error)){
            wc_add_notice($disabled_error, 'error');
        }
        
    }
    
    public function printOrdersDisabledNotice(){
        
        if(!Helpers::isQuickcartShortcodePage()){
            return;
        }
        
        $disabled_error = $this->getOnlineOrdersDisabledError();
        
        if(!empty($disabled_error)){
            echo '<div class="wc-notices">';
                wc_print_notice($disabled_error, 'error');
            echo '</div>';
        }
        
    }
    
    protected function getOnlineOrdersDisabledError(){
        
        $wcat_api = new \WCAT\Api();
        if(!$wcat_api->checkOnlineOrdersEnabled()){
            
            return Helpers::getOption('online_orders_disabled_msg', __('Online orders are not available at this time.', 'wcat'));
        }
        
        return '';
    }
    
}
