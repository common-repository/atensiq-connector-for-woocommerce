<?php
namespace WCAT;

class Api {
    
    const API_ENDPOINT = 'https://atensiq.com/app/a/api';
    
    protected function getCreds(){
        
        return [
            'sbn' => wcat_get_option('sbn_id'),
            'app_id' => 'wcat',
            'secret' => wcat_get_option('api_secret')
        ];
    }
    
    protected function getOrderItems($order_id){
        
        $order_items = array();
        
        $wc_order = wc_get_order($order_id);
        $items = $wc_order->get_items();
        
        if($wc_order && $items){
            foreach($items as $item){
                $order_items[] = array(
                    'name' => $item->get_name(),
                    'qty' => $item->get_quantity(),
                    'total' => $item->get_total()
                );
            }
        }
        return $order_items;
    }
    
    protected function getCartOrderItems(){
        
        $order_items = [];
        
        $cart_items = WC()->cart->get_cart_contents();
        
        if($cart_items){
            foreach($cart_items as $item){
                $order_items[] = array(
                    'name' => $item['data']->get_name(),
                    'qty' => $item['quantity'],
                    'total' => $item['line_total']
                );
            }
        }
        return $order_items;
    }
    
    public function checkOnlineOrdersEnabled() {
        
        $req_fields = $this->getCreds();
        
        $resp = Helpers::doRequest(
            self::API_ENDPOINT . '/setting/onlineorders', 
            $req_fields
        );
        
        $enabled = (
            isset($resp['values']) 
            && ($resp['values']['service_enabled'] == 'on') 
            && ($resp['values']['online_orders_enabled'] == 'on')
        );
        
        return $enabled;
    }
}
