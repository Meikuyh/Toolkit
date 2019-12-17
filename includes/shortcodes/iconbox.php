<?php
if (!class_exists('Worldmart_Toolkit_Shortcode_Iconbox')){
    class Worldmart_Toolkit_Shortcode_Iconbox extends  Worldmart_Toolkit_Shortcode{

        public $shortcode = 'iconbox';

        public static  function generate_css( $atts ){
            $css = '';
            return $css;
        }


        public function output_html( $atts, $content = null ){
            $atts = function_exists('vc_map_get_attributes') ? vc_map_get_attributes('worldmart_iconbox', $atts) : $atts;

            $title = '';
            $text_content = '';
            $style = '';
            $el_class = '';
            $iconbox_custom_id = '';
            $link = '#';
            $icon_type = 'fontawesome';
            /* Extract shortcode parameters.*/
            extract($atts);

            $css_class      = array('worldmart-iconbox');
            $css_class[]    = $style;

            if( $el_class )
                $css_class[] = $el_class;

            if( $iconbox_custom_id )
                $css_class[] = $iconbox_custom_id;

            if ( function_exists( 'vc_shortcode_custom_css_class' ) ){
                $css_class[] = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $css, ' ' ), '', $atts );
            }
            $icon_class_type = 'icon_'.$icon_type;

            $icon = isset( $$icon_class_type ) ? $$icon_class_type : '' ;

            ob_start(); ?>
            <div class="<?php echo esc_attr( implode(' ', $css_class) ); ?>" onclick="<?php echo sprintf("parent.location='%s'", esc_url($link) ); ?>">
                <?php if( $icon ): ?>
                    <div class="icon"><span class="<?php echo esc_attr( $icon )?>"></span></div>
                <?php endif; ?>
                <div class="content">
                    <?php if( $title ):?>
                        <h3 class="title"><?php echo esc_html( $title );?></h3>
                    <?php endif; ?>
                    <?php if( $text_content ):?>
                        <div class="text"><?php echo esc_html( $text_content ); ?></div>
                    <?php endif; ?>
                </div>
            </div>
            <?php
            $html = ob_get_clean();
            return apply_filters( 'worldmart_toolkit_shortcode_iconbox', wp_specialchars_decode( $html ), $atts , $content );
        }
    }
}