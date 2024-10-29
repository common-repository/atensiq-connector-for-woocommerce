<?php
namespace WCAT;

class Quickcart {
    
    static function getCartItem($product_id, $cart_items=null){
        
        $items = [];
        
        if(!isset($cart_items)){
            $cart_items = isset(WC()->cart) ? WC()->cart->get_cart_contents() : [];
        }
        
        if(!empty($cart_items)){
            foreach($cart_items as $item){
                if($product_id == $item['product_id']){
                    $items[] = $item;
                }
            }
        }
        
        return $items;
    }
    
    static function getProducts($cats){
        
        $prods = array();
        
        $cat_slugs = ($cats !== '') ? explode(',', $cats) : false;
        $cat_terms = array();
        
        if($cat_slugs){
            foreach($cat_slugs as $cat_slug){
                $cat_slug = trim($cat_slug);
                $cat_terms[] = get_term_by('slug', $cat_slug, 'product_cat');
            }
        }else{
            $cat_terms = Helpers::getTermChildren('product_cat', 0, true);
        }
        
        $q_args = array(
            'post_type' => 'product',
            'posts_per_page' => 100,
            'post_status' => 'publish'
        );
        
        if($cat_terms){
            foreach($cat_terms as $cat_term){
                $q_args['tax_query'] = array(
                    array(
                        'taxonomy' => 'product_cat', 
                        'field' => 'slug',
                        'terms' => array($cat_term->slug)
                    )
                );
                $prods_q = new \WP_Query($q_args);
                if($prods_q->posts){
                    $prods[] = array(
                        'cat' => [
                            'slug' => $cat_term->slug,
                            'name' => $cat_term->name
                        ],
                        'prods' => self::postsToProducts($prods_q->posts)
                    );
                }
            }
        }else{
            $prods_q = new \WP_Query($q_args);
            if($prods_q->posts){
                $prods[] = array(
                    'cat' => [],
                    'prods' => self::postsToProducts($prods_q->posts)
                );
            }
            
        }
        
        return $prods;
    }
    
    static function postsToProducts($posts){
        
        $products = [];
        
        if(!empty($posts)){
            foreach($posts as $post){
                $products[] = wc_get_product($post->ID);
            }
        }
        
        return $products;
    }
    
}
