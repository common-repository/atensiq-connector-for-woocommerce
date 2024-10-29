jQuery(function($){
    
    const wcatQcart = {
        
        init: function(){
            const _this = this;
            
            $(".wcat-qcart").on("click", ".product .att-opts .opt", function(){
                _this.selectOpt($(this));
            });
            $(".wcat-qcart").on("click", ".product .qty-btn", function(){
                _this.manageQty($(this));
            });
            $(".wcat-qcart").on("click", ".product .add-btn", function(){
                _this.addProduct($(this));
            });
            $(".wcat-qcart").on("click", ".product .del-btn", function(){
                _this.delProduct($(this));
            });
            
            $(".wcat-qcart").on("click", ".cart-summary .open-btn", function(e){
                e.preventDefault();
                $(this).closest(".cart-summary").toggleClass("opened");
            });
            $(".wcat-qcart").on("click", ".cart-summary .place-order-btn", function(e){
                e.preventDefault();
                _this.placeLocalOrder();
            });
            
            let delProductId = 0;
            $(document.body).on("click", ".woocommerce-mini-cart a.remove_from_cart_button", function(){
                delProductId = $(this).data("product_id");
            });
            $(document.body).on("removed_from_cart", function(){
                if(delProductId){
                    _this.refreshProduct(delProductId);
                    delProductId = 0;
                    
                    _this.updateCartSummaryState();
                }
            });
            $(document.body).on("wc_fragments_refreshed", function(){
                _this.updateCartSummaryState();
            });
            
        },
        selectOpt: function(btn){
            
            btn.siblings(".opt").removeClass("active");
            btn.addClass("active");
            
            const attsCont = btn.closest(".product-atts");
            btn.closest(".product-att").addClass("att-sel");
            
            if(attsCont.children(".product-att").length == attsCont.children(".att-sel").length){
                this.setVariation(attsCont);
            }
        },
        setVariation: function(attsCont){
            
            const productCont = attsCont.closest(".product");
            const priceHtmlOrig = productCont.find(".product-price .price-orig").html();
            const priceCont = productCont.find(".product-price .price-real");
            const addBtn = productCont.find(".add-btn");
            const productId = parseInt(addBtn.data("product_id"));
            
            var attVals = {};
            
            attsCont.children(".att-sel").each(function(){
                const optSelected = $(this).find(".att-opts .active");
                attVals[optSelected.data("attkey")] = optSelected.data("attopt");
            });
            
            const variationProduct = this.getProductVariation(productId, attVals);
            
            if(variationProduct !== false){
                priceCont.html(variationProduct.price_html);
                addBtn.data("variation_id", variationProduct.variation_id);
                productCont.find(".a-btn").prop("disabled", false);
            }else{
                priceCont.html(priceHtmlOrig);
                addBtn.data("variation_id", 0);
                productCont.find(".a-btn").prop("disabled", true);
            }
        },
        getProductVariation: function(productId, atts){
            
            if(typeof wcatData.productVariations !== "undefined" && typeof wcatData.productVariations[productId] !== "undefined"){
                
                for(let i=0; i < wcatData.productVariations[productId].length; i++){
                    const variationAtts = wcatData.productVariations[productId][i].attributes;
                    const variationAttsKeys = Object.keys(variationAtts);
                    
                    let matched = 0;
                    for(let ik=0; ik < variationAttsKeys.length; ik++){
                        const key = variationAttsKeys[ik];
                        if(typeof atts[key] !== "undefined" && atts[key] === variationAtts[key]){
                            matched++;
                        }
                    }
                    if(matched === variationAttsKeys.length){
                        return wcatData.productVariations[productId][i];
                    }
                }
            }
            return false;
        },
        manageQty: function(btn){
            
            const qtyCont = btn.closest(".product-qty");
            const qtyInput = qtyCont.children("[name='qty']");
            const qtyInputVal = parseInt(qtyInput.val());
            
            if(btn.hasClass("btn-plus")){
                qtyInput.val((qtyInputVal+1));
            }else if(btn.hasClass("btn-minus") && qtyInputVal > 1){
                qtyInput.val((qtyInputVal-1));
            }
        },
        addProduct: function(btn){
            const _this = this;
            
            const productCont = btn.closest(".product");
            
            const productId = parseInt(btn.data("product_id"));
            const variationId = parseInt(btn.data("variation_id"));
            //const lang = btn.data("lang");
            const productQty = parseInt(productCont.find('input[name="qty"]').val());
            
            productCont.addClass("loading");
            btn.prop("disabled", true);
            
            $.post(
                wcatData.ajaxurl, 
                {
                    action: "wcat_qcart_add",
                    product_id: productId,
                    variation_id: variationId,
                    qty: productQty
                    //lang: lang
                },
                function(resp){
                    if(resp.status && resp.values.product_html !== ""){
                        _this.refreshProduct(productId, resp.values.product_html);
                    }
                }, 
                "json"
            ).always(function(resp){
                _this.displayCartSummaryMessages(resp.okMessages + resp.errorMessages);
            });
        },
        delProduct: function(btn){
            const _this = this;
            
            const productCont = btn.closest(".product");
            
            const productId = parseInt(btn.data("product_id"));
            const cartKey = btn.data("cart_key");
            
            productCont.addClass("loading");
            btn.prop("disabled", true);
            
            $.post(
                wcatData.ajaxurl, 
                {
                    action: "wcat_qcart_del",
                    product_id: productId,
                    cart_key: cartKey
                },
                function(resp){
                    if(resp.status && resp.values.product_html !== ""){
                        _this.refreshProduct(productId, resp.values.product_html);
                    }
                }, 
                "json"
            ).always(function(resp){
                _this.displayCartSummaryMessages(resp.okMessages + resp.errorMessages);
            });
        },
        placeLocalOrder: function(){
            const _this = this;
            
            $.post(
                wcatData.ajaxurl, 
                {
                    action: "wcat_place_local_order"
                },
                function(resp){
                    if(resp.status){
                        $(".wcat-qcart .cart-summary").addClass("order-placed");
                        $(".wcat-qcart .cart-summary .place-order-btn").prop("disabled", true);
                        //$(document.body).trigger("wc_fragment_refresh");
                    }
                }, 
                "json"
            ).always(function(resp){
                _this.displayCartSummaryMessages(resp.okMessages + resp.errorMessages);
            });
        },
        refreshProduct: function(productId, itemHtml){
            //const _this = this;
            
            const productCont = $(".wcat-qcart .product-" + productId);
            
            if(!productCont.length){
                return;
            }
            
            if(typeof itemHtml !== "undefined"){
                productCont.replaceWith(itemHtml);
                $(document.body).trigger("wc_fragment_refresh");
            }else{
                
                productCont.addClass("loading");
                
                $.post(
                    wcatData.ajaxurl, 
                    {
                        action: "wcat_qcart_get_product",
                        product_id: productId
                    },
                    function(resp){
                        if(resp.status && resp.values.product_html !== ""){
                            productCont.replaceWith(resp.values.product_html);
                        }
                    }, 
                    "json"
                );
            }
        },
        updateCartSummaryState: function(){
            
            const cartSummaryCont = $(".wcat-qcart .cart-summary");
            
            if(cartSummaryCont.find(".woocommerce-mini-cart-item").length){
                cartSummaryCont.addClass("has-items");
            }else{
                cartSummaryCont.removeClass("has-items");
            }
            
            cartSummaryCont.removeClass("order-placed");
        },
        displayCartSummaryMessages: function(msg){
            
            $(".wcat-qcart .cart-summary .messages").html(msg);
        }
    };
    wcatQcart.init();
    
});
