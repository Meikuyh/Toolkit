<?php

if (!class_exists('Worldmart_Toolkit_Shortcode_Newsletter')){

    class Worldmart_Toolkit_Shortcode_Newsletter extends  Worldmart_Toolkit_Shortcode{
        /**
         * Shortcode name.
         *
         * @var  string
         */
        public $shortcode = 'newsletter';


        /**
         * Default $atts .
         *
         * @var  array
         */
        public  $default_atts  = array(

        );


        public  static function generate_css( $atts ){
            // Extract shortcode parameters.
            extract($atts);
            $css = '';

            return $css;
        }


        public function output_html( $atts, $content = null ){
            $atts = function_exists('vc_map_get_attributes') ? vc_map_get_attributes('worldmart_newsletter', $atts) : $atts;
            // Extract shortcode parameters.
            extract($atts);

            $css_class = array('worldmart-newsletter widget');
            $css_class[] = $atts['el_class'];
            $css_class[] = $atts['style'];
            $css_class[] =  $atts['newsletter_custom_id'];

            if ( function_exists( 'vc_shortcode_custom_css_class' ) ){
                $css_class[] = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $css, ' ' ), '', $atts );
            }
            $btn_name = __('Subscribe','worldmart');
            if($atts['button_name']){
                $btn_name = $atts['button_name'];
            }
            ob_start();
            ?>
            <div class="new-letter-block <?php echo esc_attr( implode(' ', $css_class) );?>">
                <?php if( $atts['title'] ):?>
                    <h3 class="widgettitle"><?php esc_html_e( $atts['title'] ,'worldmart-toolkit' ); ?></h3>
                <?php endif;?>
                <div class="block-content">
                    <div class="newsletter-form-wrap">
                        <input class="email" type="email" name="email" placeholder="<?php esc_attr_e($atts['placeholder_text'], 'worldmart-toolkit');?>">
                        <button type="submit" name="submit_button" class="btn-submit submit-newsletter"><?php esc_html_e($btn_name, 'worldmart-toolkit')?></button>
                    </div>
                </div>
            </div>
            <?php
            $html = ob_get_clean();
            return apply_filters( 'worldmart_toolkit_shortcode_newsletter', force_balance_tags( $html ), $atts ,$content );
        }
    }
}