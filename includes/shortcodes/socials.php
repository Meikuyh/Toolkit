<?php
if (!class_exists('Worldmart_Toolkit_Shortcode_Socials')){
    class Worldmart_Toolkit_Shortcode_Socials extends  Worldmart_Toolkit_Shortcode{

        public $shortcode = 'socials';

        public static  function generate_css( $atts ){
            $css = '';
            return $css;
        }

        public function output_html( $atts, $content = null ){
            $atts = function_exists('vc_map_get_attributes') ? vc_map_get_attributes('worldmart_socials', $atts) : $atts;

            $title                  = '';
            $text_align             = '';
            $style                  = '';
            $el_class               = '';
            $socials_custom_id      = '';
            $use_socials            = '';

            /* Extract shortcode parameters.*/
            extract($atts);

            $css_class      = array( 'worldmart-socials widget ');
            $css_class[]    =  $style;

            if( $el_class ){
                $css_class[] =  $el_class;
            }
            if( $socials_custom_id ){
                $css_class[] =  $socials_custom_id;
            }

            if ( function_exists( 'vc_shortcode_custom_css_class' ) ){
                $css_class[] = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $css, ' ' ), '', $atts );
            }

            $socials = explode(',', $use_socials );

            if( ! function_exists( 'worldmart_social' ) ) return '';

            ob_start(); ?>
            <div class="<?php echo esc_attr( implode(' ', $css_class) ); ?>">
                <?php if( $title ): ?>
                    <h2 class="widgettitle"><?php echo esc_html( $title ); ?></h2>
                <?php endif; ?>
                <div class="socials <?php echo esc_attr( $text_align); ?>">
                    <?php if( count( $socials ) > 0 ) :
                        $include_name = ( $style == 'style3' ) ? true : false;
                        foreach ( $socials as $social ){
                            worldmart_social( $social, $include_name);
                        }
                    endif; ?>
                </div>
            </div>
            <?php
            $html = ob_get_clean();
            return apply_filters( 'worldmart_toolkit_shortcode_socials', force_balance_tags( $html ), $atts ,$content );
        }
    }
}