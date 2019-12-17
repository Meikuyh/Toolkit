<?php
if (!class_exists('Worldmart_Toolkit_Shortcode_Slider')){
    class Worldmart_Toolkit_Shortcode_Slider extends  Worldmart_Toolkit_Shortcode{
        /**
         * Shortcode name.
         *
         * @var  string
         */
        public $shortcode = 'slider';

        /**
         * Default $atts .
         *
         * @var  array
         */
        public  $default_atts  = array(
        );


        public static  function generate_css( $atts ){
            $css = '';
            if( $atts['owl_navigation_position'] == 'nav-top-left' || $atts['owl_navigation_position'] == 'nav-top-right'){
                $css .= '.'.$atts['slider_custom_id'] .' .owl-nav{ top:'.$atts['owl_navigation_position_top'].'px;} ';
            }

            return $css;
        }

        public function output_html( $atts, $content = null ){
            $atts = function_exists('vc_map_get_attributes') ? vc_map_get_attributes('worldmart_slider', $atts) : $atts;
            $style = 'default';
            $el_class = '';
            $slider_custom_id = '';
            $owl_navigation_position = '';
            $css = '';
            /* Extract shortcode parameters.*/
            extract($atts);

            $css_class = array('worldmart-slider');
            $css_class[] = 'slider-style-'.$style;
            $css_class[] = $el_class;
            $css_class[] = $slider_custom_id;

            if ( function_exists( 'vc_shortcode_custom_css_class' ) ){
                $css_class[] = ' ' . apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $css, ' ' ), '', $atts );
            }
            $owl_settings = $this->generate_carousel_data_attributes('', $atts);
            ob_start();
            ?>
            <div class="<?php echo esc_attr( implode(' ', $css_class) );?>">
                <div class="owl-carousel <?php echo esc_attr( $owl_navigation_position );?>" <?php echo $owl_settings; ?> >
                    <?php echo wpb_js_remove_wpautop( $content ); ?>
                </div>
            </div>
            <?php
            $html = ob_get_clean();
            return apply_filters( 'worldmart_toolkit_shortcode_slider', force_balance_tags( $html ), $atts ,$content );
        }
    }
}