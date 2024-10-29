<?php
namespace WCAT\View;
use WCAT\Helpers;

class Quickcart {
    
    static function getCart($products){
        
        $product_variations_data = [];
        
        ob_start();
        
        echo '<div class="wcat-qcart">';
            
            do_action('quickcart_top');
            
            if($products){
                foreach($products as $products_group){ 
                    
                    $cat_slug = isset($products_group['cat']['slug']) ? $products_group['cat']['slug'] : '';
                    $cat_name = isset($products_group['cat']['name']) ? $products_group['cat']['name'] : '';
                    
                    echo '<div id="category-' . $cat_slug . '" class="prods-group">';
                        
                        if(!empty($cat_name)){
                            echo '<h2 class="cat-title">';
                                echo $cat_name;
                            echo '</h2>';
                        }
                        
                        echo '<div class="cat-prods">';
                            
                            $group_rows = array_chunk($products_group['prods'], 2);
                            
                            foreach($group_rows as $group_row){
                                echo '<div class="prods-row">';
                                    foreach($group_row as $wc_product){ 
                                        
                                        $product_id = $wc_product->get_id();
                                        
                                        if($wc_product->get_type() === 'variable'){
                                            $product_variations_data[$product_id] = $wc_product->get_available_variations();
                                        }
                                        
                                        echo self::getProductItem($wc_product);
                                    }
                                echo '</div>';
                            }
                            
                        echo '</div>';
                    echo '</div>';
                    
                } 
            }
            
            do_action('quickcart_bottom');
            
            if(wcat_get_option('local_orders_enabled') && Helpers::getTableNum()){
                echo '<div class="cart-summary';
                if(isset(WC()->cart) && !WC()->cart->is_empty()) echo ' has-items';
                echo '">';
                    echo '<div class="woocommerce widget_shopping_cart">';
                        echo '<a href="#" class="open-btn"><span></span></a>';
                        echo '<div class="widget_shopping_cart_content">';
                            woocommerce_mini_cart();
                        echo '</div>';
                        echo '<div class="messages"></div>';
                    echo '</div>';
                echo '</div>';
            }
            
        echo '</div>';
        
        $html = ob_get_contents();
        ob_end_clean();
        
        wp_localize_script(
            'wcat-front', 
            'wcatData', 
            apply_filters('wcat_data', [
                'productVariations' => $product_variations_data
            ])
        );
        
        return $html;
    }
    
    static function getProductItem($wc_product){
        
        if(is_int($wc_product)){
            $wc_product = wc_get_product($wc_product);
        }
        
        $product_id = $wc_product->get_id();
        $product_type = $wc_product->get_type();
        $product_added = \WCAT\Quickcart::getCartItem($product_id);
        $btn_disabled = ($product_type == 'variable') ? ' disabled' : '';
        $show_images = wcat_get_option('show_images');
        
        $product_classes = [
            'product',
            'product-' . $product_type,
            'product-' . $product_id
        ];
        if($product_added){
            $product_classes[] = 'product-added';
        }
        if($show_images){
            $product_classes[] = 'has-image';
        }
        
        ob_start();
        ?>
        
        <div class="<?php echo implode(' ', $product_classes); ?>">
            <div class="product-inner">
                
                <?php if($show_images){ ?>
                <div class="product-image">

                    <?php echo $wc_product->get_image(); ?>
                    <span class="added-icon"></span>

                </div>
                <?php } ?>
            
                <div class="product-details">
                    
                    <div class="product-info">
                        <h3 class="product-title">
                            <?php echo $wc_product->get_title(); ?>
                        </h3>

                        <!--<form action="" class="product-form" method="POST">-->

                            <?php
                            
                            $attributes = $wc_product->get_attributes();

                            if(!empty($attributes)){
                                echo '<div class="product-atts">';
                                    foreach($attributes as $att_key => $att){

                                        $att_tax = $att->is_taxonomy() ? $att->get_taxonomy() : false;
                                        $att_name = $att->get_name();
                                        $att_label = $att_tax ? Helpers::getTaxLabel($att_tax) : $att_name;

                                        if($att_opts = $att->get_options()){
                                            echo '<div class="product-att">';
                                                echo '<h4 class="att-title">' . $att_label . '</h4>';
                                                echo '<ul class="att-opts">';
                                                    foreach($att_opts as $att_opt){
                                                        if($att_tax){
                                                            $att_opt = Helpers::getTermSlug($att_opt, $att_tax);
                                                        }
                                                        echo '<li class="opt"';
                                                        echo ' data-attkey="attribute_' . $att_key . '"';
                                                        echo ' data-attopt="' . $att_opt . '"';
                                                        echo '>';
                                                            echo $att_tax ? Helpers::getTermLabel($att_opt, $att_tax) : $att_opt;
                                                        echo '</li>';
                                                    }
                                                echo '</ul>';
                                            echo '</div>';
                                        }
                                    }
                                echo '</div>';
                            }
                            ?>

                            <div class="product-price">
                                <div class="price-orig wcat-d-none">
                                    <?php echo $wc_product->get_price_html(); ?>
                                </div>
                                <div class="price-real">
                                    <?php echo $wc_product->get_price_html(); ?>
                                </div>
                            </div>

                            <div class="product-qty">
                                <input class="qty" type="number" min="1" step="1" name="qty" value="1" />
                                
                                <button type="button" class="a-btn qty-btn btn-minus"<?php echo $btn_disabled; ?>></button>
                                <button type="button" class="a-btn qty-btn btn-plus"<?php echo $btn_disabled; ?>></button>
                                
                                <button type="button" class="a-btn add-btn"<?php echo $btn_disabled; ?> data-product_id="<?php echo $product_id; ?>" data-variation_id="0">
                                    <?php _e('Add', 'wcat'); ?>
                                </button>
                                
                            </div>
                            
                            <!--<input type="hidden" name="product_id" value="<?php echo $product_id; ?>" />-->
                            <!--<input type="hidden" name="variation_id" value="0" />-->
                        
                        <!--</form>-->
                    </div>
                    <div class="cart-info">
                        <?php 
                        if(!empty($product_added)){ 
                            foreach($product_added as $item){
                                echo '<div class="cart-item product-' . $item['data']->get_type() . '">';
                                    if($item['variation_id']){
                                        echo '<span class="item-title">' . $item['data']->get_name() . '</span>';
                                    }
                                    echo '<span class="item-qty">';
                                        if($item['quantity'] > 1){
                                            echo sprintf(__('%d items in cart', 'wcat'), $item['quantity']);
                                        }else{
                                            echo sprintf(__('%d item in cart', 'wcat'), $item['quantity']);
                                        }
                                    echo '</span>';

                                    echo '<button type="button" class="del-btn"';
                                    echo ' data-product_id="' . $item['product_id'] . '"';
                                    echo ' data-variation_id="' . $item['variation_id'] . '"';
                                    echo ' data-cart_key="' . $item['key'] . '"';
                                        echo '&nbsp;';
                                    echo '</button>';

                                echo '</div>';
                            }
                        }
                        ?>
                    </div>
                    
                </div>
                
                <div class="product-loader"></div>
            </div>
        </div>
        
        <?php
        $html = ob_get_contents();
        ob_end_clean();
        
        return $html;
    }
    
}
