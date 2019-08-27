<?php
if (!class_exists('Worldmart_Toolkit_Shortcode_Banner')){
    class Worldmart_Toolkit_Shortcode_Banner extends  Worldmart_Toolkit_Shortcode{

        public $shortcode = 'banner';

        public  $default_atts  = array(
            'style'             => '',
            'ids'               => '',
            'demension'         => '200x200',
            'layout'            => 'default',
            'max_width'         => '150',
            'title'             => '',
            'btn_label'         => '',
            'subtitle'          => '',
            'link'              => '',
            'el_class'          => '',
            'css'               => '',
            'banner_custom_id'  => '',
        );

        public  static function generate_css( $atts ){
            // Extract shortcode parameters.
            extract($atts);
            $css = '';
            return $css;
        }

        public function output_html( $atts, $content = null ){
            $atts = function_exists('vc_map_get_attributes') ? vc_map_get_attributes('worldmart_banner', $atts) : $atts;

            // Extract shortcode parameters.

            extract(
                shortcode_atts(
                    $this->default_atts,
                    $atts
                )
            );

            $css_class = array('worldmart-banner');
            $css_class [] = $atts['el_class'];
            $css_class [] = ' banner-style'.$atts['style'];
            if($style == '2'){
                $css_class [] = $layout;
            }
            $css_class [] = $banner_custom_id;
            if ( function_exists( 'vc_shortcode_custom_css_class' ) ){
                $css_class []= apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $css, ' ' ), '', $atts );
            }
            $pr_width = '200';
            $pr_height = '200';
            $arr_demension = array();
            if($demension) {
                $arr_demension = explode('x',strtolower($demension));
                if(isset($arr_demension[0]) && is_numeric($arr_demension[0])){
                    $pr_width = (int)$arr_demension[0];
                }
                if(isset($arr_demension[1]) && is_numeric($arr_demension[1])){
                    $pr_height = (int)$arr_demension[1];
                }
            }
            $img = false;
            $product_is_valid = false;
            if($ids){
                $product = wc_get_product($ids);
            }
            if(!empty($product)){
                $product_is_valid = true;
                $img_product = worldmart_resize_image( $product->get_image_id(), null, $pr_width, $pr_height, false, true, false );
                if(!empty($img_product["url"])){
                    $img_lazy = "data:image/svg+xml;charset=utf-8,%3Csvg%20xmlns%3D%27http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%27%20viewBox%3D%270%200%20" . $img_product['width'] . "%20" . $img_product['height'] . "%27%2F%3E";
                    $img ='<img class="product-banner lazy" src="'.$img_lazy.'" data-src="'.esc_url($img_product["url"]).'" alt="'.esc_attr($product->get_name()).'" width="'.esc_attr($img_product['width']).'" height="'.esc_attr($img_product['height']).'" />';
                }
            }
            if($style != '1'){
                if($max_width){
                    $pr_width = $max_width;
                }else{
                    $pr_width = '';
                }
            }
            $buton = __('shop now','worldmart-toolkit');
            if($btn_label ){
                $buton = $btn_label;
            }
            ob_start(); ?>
            <div class="<?php echo esc_attr( implode(' ', $css_class) );?>" style="<?php if($pr_width){ echo 'max-width:'.esc_attr($pr_width).'px;'; } ?>" >
                <?php if ($style == '1'){ ?>
                    <?php if($product_is_valid){ ?>
                    <div class="img-banner">
                        <?php if($img) { ?> <figure><?php echo worldmart_output($img); ?></figure><?php } ?>
                        <h4 class="product-title">
                            <a href="<?php echo esc_url($product->get_permalink()); ?>"> <?php echo esc_html($product->get_name()) ;?> </a>
                        </h4>
                        <a href="<?php echo esc_url($product->get_permalink()); ?>" class="link-to-product">
                            <?php esc_html_e( 'view more','worldmart-toolkit')?>
                        </a>
                    </div>
                    <?php } else { ?>
                    <div class="alert alert-warning">
                        <strong>Warning!</strong><?php esc_html_e('product is not valid','worldmart-toolkit'); ?>
                    </div>
                    <?php } ?>
                <?php } else { ?>
                    <div class="text-banner">
                        <?php if($title){ ?>
                            <h3 class="title"><?php echo  esc_html( $title); ?></h3>
                        <?php } ?>
                        <?php if($subtitle){ ?>
                            <span class="subtitle"><?php echo esc_html($subtitle)?></span>
                        <?php } ?>
                        <a href="<?php echo esc_url($link)?>" class="custom-link"><?php echo esc_html($buton); ?></a>
                    </div>
                <?php } ?>
            </div>
            <?php
            $html = ob_get_clean();
            return apply_filters( 'worldmart_toolkit_shortcode_banner', force_balance_tags( $html ), $atts ,$content );
        }
    }
}
