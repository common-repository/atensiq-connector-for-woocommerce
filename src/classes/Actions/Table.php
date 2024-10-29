<?php
namespace WCAT\Actions;
use WCAT\Helpers;

class Table {
    
    public function __construct(){
        
        add_action('wp', [$this, 'setTableNumCookie']);
    }
    
    public function setTableNumCookie(){
        
        $atq_table = Helpers::getReq('atq_table', 'integer');
        
        if($atq_table){
            setcookie('wcat_atq_table', $atq_table, 0, '/', COOKIE_DOMAIN);
        }
    }
    
    static function detTableNumCookie(){
        setcookie('wcat_atq_table', '', time()-(60*60*24), '/', COOKIE_DOMAIN);
    }
    
}
