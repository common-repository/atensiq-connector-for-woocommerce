<?php
namespace WCAT;

class Helpers {
    
    static function getReq($key, $san_type='text'){
        global $_ac_req;
        
        if(isset($_ac_req) && isset($_ac_req[$key])) return $_ac_req[$key];
        
        $val = isset($_REQUEST[$key]) ? $_REQUEST[$key] : false;
        
        if($val !== false){
            if(is_array($val)){
                array_walk_recursive($val, 'self::sanitizeReqArr', $san_type);
            }else{
                $val = self::sanitizeReq(urldecode($val), $san_type);
            }
        }
        
        $_ac_req[$key] = $val;
        
        return $val;
    }
    
    static function sanitizeReq($val_item, $san_type='text'){
        
        $val_item = trim($val_item);
        
        switch($san_type){
            case 'text':
                $val_item = sanitize_text_field($val_item);
                break;
            case 'textarea':
                $val_item = sanitize_textarea_field($val_item);
                break;
            case 'integer':
                $val_item = intval($val_item);
                break;
            case 'float':
                $val_item = floatval($val_item);
                break;
        }
        
        return $val_item;
    }
    
    static function sanitizeReqArr(&$val_item, $san_type='text', $udec=false){
        if($udec) $val_item = urldecode($val_item);
        $val_item = self::sanitizeReq($val_item, $san_type);
    }
    
    static function respondJson($status=false, $args=array()){
        $args = wp_parse_args($args, array(
            'error_fields' => array(),
            'error_messages' => '',
            'ok_messages' => '',
            'values' => array(),
            'redirect' => '',
            'reload' => false,
            'reset_form' => false
            ));
        
        
        header('Content-Type: application/json');
        $resp = array(
            'status' => (int)$status,
            'errorFields' => $args['error_fields'],
            'errorMessages' => $args['error_messages'],
            'okMessages' => $args['ok_messages'],
            'values' => $args['values'],
            'redirect' => $args['redirect'],
            'reload' => (int)$args['reload'],
            'resetForm' => (int)$args['reset_form']
        );
        
        echo json_encode($resp);
        die();
    }
    
    static function doRequest($endpoint, $req_fields){
        
        $resp = wp_remote_post(
            $endpoint, 
            array(
                'timeout' => 20,
                'body' => $req_fields
            )
        );
        
        $code = wp_remote_retrieve_response_code($resp);
        $body = wp_remote_retrieve_body($resp);
        
        return ($code == 200 && !empty($body)) ? json_decode($body, true) : false;
    }
    
    static function getTermLabel($term, $tax){
        $term_data = is_int($term) ? get_term($term, $tax) : get_term_by('slug', $term, $tax);
        if(isset($term_data->name)) return $term_data->name;
        return '';
    }
    
    static function getTermData($term, $tax, $data_key=null){
        $term_data = is_int($term) ? get_term($term, $tax) : get_term_by('slug', $term, $tax);
        if($term_data && !is_wp_error($term_data)){
            if(isset($data_key) && isset($term_data->$data_key)) return $term_data->$data_key;
            return $term_data;
        }
        return false;
    }
    
    static function getTermId($term, $tax){
        return self::getTermData($term, $tax, 'term_id');
    }
    
    static function getTermSlug($term, $tax){
        return self::getTermData($term, $tax, 'slug');
    }
    
    static function getTaxLabel($tax){
        return ($tax_obj = get_taxonomy($tax)) ? $tax_obj->labels->singular_name : $tax;
    }
    
    static function getTermChildren($taxonomy, $parent=0, $hide_empty=false){
        $parent = is_int($parent) ? $parent : self::getTermId($parent, $taxonomy);
        $terms = get_terms(array(
            'taxonomy' => $taxonomy,
            'parent' => $parent,
            'hide_empty' => $hide_empty,
            'orderby' => 'name'
            ));
        return ($terms && !is_wp_error($terms)) ? $terms : false;
    }
    
    static function hasShortcode($shortcode){
        global $post;
        
        return (isset($post) && strpos($post->post_content, $shortcode) !== false);
    }
    
    static function isAdminScreen($screen_ids){
        $screen = get_current_screen();
        if(!is_array($screen_ids)){
            $screen_ids = array($screen_ids);
        }
        return (isset($screen) && in_array($screen->id, $screen_ids));
    }
    
    static function isQuickcartShortcodePage(){
        return self::hasShortcode('[wcat-quickcart');
    }
    
    static function getOption($key, $default=null){
        
        $opt = wcat_get_option($key);
        
        return (empty($opt) && isset($default)) ? $default : false;
    }
    
    static function getMessageOptionHtml($key, $type, $default=null){
        
        return self::getMessageHtml(self::getOption($key, $default), $type);
    }
    
    static function getMessageHtml($msg, $type){
        
        return empty($msg) ? '' : '<p class="msg msg-' . $type . '">' . $msg . '</p>';
    }
    
    static function getTableNum(){
        
        $table_num = self::getReq('atq_table', 'integer');
        if(!$table_num && isset($_COOKIE['wcat_atq_table'])){
            $table_num = intval($_COOKIE['wcat_atq_table']);
        }
        
        return $table_num;
    }
    
    static function getLang(){
        
        return substr(get_locale(), 0, 2);
    }
    
}
