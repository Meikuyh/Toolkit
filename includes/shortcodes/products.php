<?php

if (!class_exists('Worldmart_Toolkit_Shortcode_Products')){
    class Worldmart_Toolkit_Shortcode_Products extends  Worldmart_Toolkit_Shortcode{
        /**
         * Shortcode name.
         *
         * @var  string
         */
        public $shortcode = 'products';

        /**
         * Default $atts .
         *
         * @var  array
         */
        public  $default_atts  = array(
            'owl_navigation_position_top' => '-60'
        );


        public static function generate_css( $atts ){
            extract( $atts );
            $css = '';
            if( $atts['owl_navigation_position'] == 'nav-top-left' || $atts['owl_navigation_position'] == 'nav-top-right'){
                $css .= '.'.$atts['products_custom_id'] .' .owl-nav{ top:'.$atts['owl_navigation_position_top'].'px;} ';
            }

            return $css;
        }

        public function output_html( $atts, $content = null ){
            $atts = function_exists('vc_map_get_attributes') ? vc_map_get_attributes('worldmart_products', $atts) : $atts;

            extract( $atts );
            $css_class = array('worldmart-products');
            $css_class[] = $atts['el_class'];
            $css_class[] =  $atts['products_custom_id'];

            if ( function_exists( 'vc_shortcode_custom_css_class' ) ){
                $css_class[]= apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $css, ' ' ), '', $atts );
            }


            /* Product Size */
            if ( $product_image_size ){
                if( $product_image_size == 'custom'){
                    $thumb_width = $product_custom_thumb_width;
                    $thumb_height = $product_custom_thumb_height;
                }else{
                    $product_image_size = explode("x",$product_image_size);
                    $thumb_width = $product_image_size[0];
                    $thumb_height = $product_image_size[1];
                }
                if($thumb_width > 0){
                    add_filter( 'worldmart_shop_pruduct_thumb_width', function() use ($thumb_width){ return $thumb_width; });
                }
                if($thumb_height > 0){
                    add_filter( 'worldmart_shop_pruduct_thumb_height', function() use($thumb_height){ return $thumb_height; });
                }
            }
            $products = $this->getProducts($atts);
            $product_item_class = array(' ',$target);
            $product_list_class = array();
            $owl_settings = '';
            if( $productsliststyle  == 'grid' ){
                $product_item_class[] ='style-'.$product_style;
                $product_list_class[] = 'product-list-grid row auto-clear equal-container';

                $product_item_class[] = $boostrap_rows_space;
                $product_item_class[] = 'col-lg-'.$boostrap_lg_items;
                $product_item_class[] = 'col-md-'.$boostrap_md_items;
                $product_item_class[] = 'col-sm-'.$boostrap_sm_items;
                $product_item_class[] = 'col-xs-'.$boostrap_xs_items;
                $product_item_class[] = 'col-ts-'.$boostrap_ts_items;
            }
            if( $productsliststyle  == 'owl' ){
                $product_item_class[] ='style-'.$product_style;
                $product_list_class[] = 'product-list-owl owl-carousel equal-container '.$owl_navigation_position;

                $product_item_class[] = $owl_rows_space;

                $owl_settings = $this->generate_carousel_data_attributes('owl_', $atts);
            }
            $hot_deals ='';
            if($is_hot_deals){
                $date_of = array(
                    'seconds' => '',
                    'minutes' => '',
                    'hours' => '',
                    'mday' => '',
                    'mon' => '',
                    'year' => '',
                );
                if($exp_date){
                    $date_format = get_option( 'date_format' );
                    $exp_day = date_create_from_format($date_format,$exp_date);
                    if($exp_day){
                        $date_of['year'] = $exp_day->format('Y');
                        $date_of['mon'] = $exp_day->format('m');
                        $date_of['mday'] = $exp_day->format('d');
                        $date_of['hours'] = $exp_day->format('H');
                        $date_of['minutes'] = $exp_day->format('i');
                        $date_of['seconds'] = $exp_day->format('s');
                        $hot_deals = '<div class="kt-countdown" data-y="'.esc_attr($date_of['year']).'" data-m="'.esc_attr($date_of['mon']).'" data-d="'.esc_attr($date_of['mday']).'" data-h="'.esc_attr($date_of['hours']).'" data-i="'.esc_attr($date_of['minutes']).'" data-s="'.esc_attr($date_of['seconds']).'"></div>';
                    }    
                }

            }
            ob_start();
            if( $productsliststyle == 'wgt') { ?>
                <div class="shortcode-products <?php echo esc_attr( implode(' ', $css_class) ); ?>">
                    <?php if($title){ ?>
                        <div class="title-box">
                            <h3 class="title" <?php if($title_color){ ?> style="background-color: <?php echo esc_attr($title_color); ?>;" <?php } ?>><?php echo esc_html($title); ?></h3>
                        </div>
                    <?php } ?>
                    <?php if( $products->have_posts()){ ?>
                        <ul class="product_list_widget">
                            <?php 
                                while ( $products->have_posts() ){
                                    $products->the_post();
                                    wc_get_template( 'content-widget-product.php', array( 'show_rating' => true ) ); 
                                } 
                            ?>
                            <?php if($customlink){ ?>
                                <li><a class="link-to" href="<?php echo esc_url($customlink); ?>"><?php esc_html_e('View all','worldmart'); ?></a></li>
                            <?php } ?>
                            
                        </ul>
                    <?php } ?>
                </div>
            <?php
            } else { ?>
                <div class="<?php echo esc_attr( implode(' ', $css_class) );?>">
                    <?php if($title){ ?>
                        <div class="title-box">
                            <h3 class="title"><?php echo esc_html($title); ?></h3>
                        </div>
                    <?php } ?>
                    <?php echo worldmart_output( $hot_deals); ?>
                    <?php if( $products->have_posts()): ?>
                        <?php if( $productsliststyle == 'grid'):?>
                            <ul class="<?php echo esc_attr( implode(' ', $product_list_class) );?>" >
                                <?php while ( $products->have_posts() ) : $products->the_post();  ?>
                                    <li <?php post_class( $product_item_class );?>>
                                        <?php wc_get_template_part('product-styles/content-product-style', $product_style); ?>
                                    </li>
                                <?php endwhile;?>
                            </ul>
                        <?php endif;?>
                        <!-- OWL Products -->
                        <?php if( $productsliststyle == 'owl'):?>
                            <?php
                            $i = 1;
                            $toal_product = $products->post_count;
                            ?>
                            <div class="<?php echo esc_attr( implode(' ', $product_list_class) );?>" <?php echo force_balance_tags($owl_settings);?>>
                                <div class="owl-one-row">
                                    <?php while ( $products->have_posts() ) : $products->the_post();  ?>
                                        <div <?php post_class( $product_item_class );?>>
                                            <?php wc_get_template_part('product-styles/content-product-style', $product_style); ?>
                                        </div>
                                        <?php
                                        if( $i % $owl_number_row == 0 && $i < $toal_product ){
                                            echo '</div><div class="owl-one-row">';
                                        }
                                        $i++;
                                        ?>
                                    <?php endwhile;?>
                                </div>
                            </div>
                        <?php endif;?>
                    <?php else: ?>
                        <p>
                            <strong><?php esc_html_e( 'No Product', 'worldmart' ); ?></strong>
                        </p>
                    <?php endif; ?>
                </div>
            <?php }
            wp_reset_postdata();
            $html = ob_get_clean();
            return apply_filters( 'worldmart_toolkit_shortcode_products', force_balance_tags( $html ), $atts ,$content );
        }
    }
}