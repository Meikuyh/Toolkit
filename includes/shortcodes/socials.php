<?php

if (!class_exists('Worldmart_Toolkit_Shortcode_Socials')){
    class Worldmart_Toolkit_Shortcode_Socials extends  Worldmart_Toolkit_Shortcode{
        /**
         * Shortcode name.
         *
         * @var  string
         */
        public $shortcode = 'socials';


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
            return $css;
        }


        public function output_html( $atts, $content = null ){
            $atts = function_exists('vc_map_get_attributes') ? vc_map_get_attributes('worldmart_socials', $atts) : $atts;

            // Extract shortcode parameters.
            extract($atts);

            $css_class = array('worldmart-socials widget');
            $css_class[] = $atts['el_class'];
            $css_class[] =  $atts['socials_custom_id'];
            $css_class[] =  $atts['style'];

            if ( function_exists( 'vc_shortcode_custom_css_class' ) ){
                $css_class[] = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $css, ' ' ), '', $atts );
            }
            $socials = explode(',', $atts['use_socials']);
            ob_start();
            ?>
            <div class="<?php echo esc_attr( implode(' ', $css_class) );?>">
                <?php if( $atts['title'] ):?>
                    <h2 class="widgettitle"><?php esc_html_e( $atts['title'], 'worldmart-toolkit' );?></h2>
                <?php endif;?>
                <?php if( function_exists( 'worldmart_social' ) && $socials && is_array($socials) && count( $socials ) > 0 ):?>
                    <div class="socials <?php echo esc_attr( $atts['text_align'] );?>">
                        <?php
                        foreach ($socials as $social){
                            if($style == 'style3'){
                                worldmart_social($social, true);
                            }else {
                                worldmart_social($social);
                            }

                        }
                        ?>
                    </div>
                <?php endif;?>
            </div>
            <?php
            $html = ob_get_clean();

            return apply_filters( 'worldmart_toolkit_shortcode_socials', force_balance_tags( $html ), $atts ,$content );
        }
    }
}