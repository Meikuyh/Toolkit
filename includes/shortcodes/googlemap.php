<?php

if (!class_exists('Worldmart_Toolkit_Shortcode_Googlemap')){
    class Worldmart_Toolkit_Shortcode_Googlemap extends  Worldmart_Toolkit_Shortcode{
        /**
         * Shortcode name.
         *
         * @var  string
         */
        public $shortcode = 'googlemap';


        /**
         * Default $atts .
         *
         * @var  array
         */
        public  $default_atts  = array(
        );


        public static  function generate_css( $atts ){
            // Extract shortcode parameters.
            extract($atts);

            $css = '';
            if( $atts['map_height'] > 0 ){
                $css .= '.'.$atts['googlemap_custom_id'] .'{ min-height:'.$atts['map_height'].'px;} ';
            }
            $css = '';

            return $css;
        }


        public function output_html( $atts, $content = null ){
            $atts = function_exists('vc_map_get_attributes') ? vc_map_get_attributes('worldmart_googlemap', $atts) : $atts;

            // Extract shortcode parameters.
            extract($atts);

            $css_class = array('worldmart-google-maps');
            $css_class[] = $atts['el_class'];
            $css_class[] =  $atts['googlemap_custom_id'];

            if ( function_exists( 'vc_shortcode_custom_css_class' ) ){
                $css_class[] = ' ' . apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $css, ' ' ), '', $atts );
            }

            ob_start();
            ?>
            <div  class="<?php echo esc_attr( implode(' ', $css_class) );?>"
                  id=                   "az-google-maps<?php echo uniqid(); ?>"
                 data-hue			    =""
                 data-lightness		    ="1"
                 data-map-style		    ="<?php echo esc_attr($info_content)?>"
                 data-saturation	    ="-100"
                 data-modify-coloring   ="false"
                 data-title_maps	    ="<?php echo esc_html($title) ?>"
                 data-phone			    ="<?php echo esc_html($phone) ?>"
                 data-email			    ="<?php echo esc_html($email) ?>"
                 data-address		    ="<?php echo esc_html($address) ?>"
                 data-longitude		    ="<?php echo esc_html($longitude) ?>"
                 data-latitude		    ="<?php echo esc_html($latitude) ?>"
                 data-pin-icon		    =""
                 data-zoom			    ="<?php echo esc_html($zoom) ?>"
                 data-map-type		    ="<?php echo esc_attr($map_type) ?>"
                 data-map-height        = "<?php echo esc_attr($map_height) ?>"
            >
            </div>
            <?php
            $html = ob_get_clean();
            return apply_filters( 'worldmart_toolkit_shortcode_googlemap', force_balance_tags( $html ), $atts ,$content );
        }
    }
}