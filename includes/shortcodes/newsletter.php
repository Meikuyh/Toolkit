<?php
if (!class_exists('Worldmart_Toolkit_Shortcode_Newsletter')){
    class Worldmart_Toolkit_Shortcode_Newsletter extends  Worldmart_Toolkit_Shortcode{

        public $shortcode = 'newsletter';

        public  static function generate_css( $atts ){
            $css = '';
            return $css;
        }

        public function output_html( $atts, $content = null ){
            $atts = function_exists('vc_map_get_attributes') ? vc_map_get_attributes('worldmart_newsletter', $atts) : $atts;

            $title                  = '';
            $el_class               = '';
            $newsletter_custom_id   = '';
            $style                  = '';
            $button_name            = '';
            $placeholder_text       = '';
            $css                    = '';

            /* Extract shortcode parameters.*/
            extract($atts);

            $css_class   = array('worldmart-newsletter widget');
            $css_class[] = $style;

            if( $el_class )
                $css_class[] = $el_class;

            if( $newsletter_custom_id )
                $css_class[] = $newsletter_custom_id;

            if ( function_exists( 'vc_shortcode_custom_css_class' ) ){
                $css_class[] = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $css, ' ' ), '', $atts );
            }

            ob_start(); ?>
            <div class="new-letter-block <?php echo esc_attr( implode(' ', $css_class) );?>">
                <?php if( $title ): ?>
                    <h3 class="widgettitle"><?php echo esc_html( $title ); ?></h3>
                <?php endif;?>
                <div class="block-content">
                    <div class="newsletter-form-wrap">
                        <input class="email" type="email" name="email" placeholder="<?php echo esc_attr( $placeholder_text ); ?>">
                        <button type="submit" name="submit_button" class="btn-submit submit-newsletter"><?php echo esc_html( $button_name )?></button>
                    </div>
                </div>
            </div>
            <?php
            $html = ob_get_clean();
            return apply_filters( 'worldmart_toolkit_shortcode_newsletter', wp_specialchars_decode( $html ), $atts , $content );
        }
    }
}