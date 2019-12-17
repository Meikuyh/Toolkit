<?php
if (!class_exists('Worldmart_Toolkit_Shortcode_Aboutus')){
    class Worldmart_Toolkit_Shortcode_Aboutus extends  Worldmart_Toolkit_Shortcode{

        public $shortcode = 'aboutus';

        public  static function generate_css( $atts ){
            $css = '';
            return $css;
        }

        public function output_html( $atts, $content = null ){
            $atts = function_exists('vc_map_get_attributes') ? vc_map_get_attributes('worldmart_aboutus', $atts) : $atts;

            $style = '';
            $index_code = '';
            $title = '';
            $desc = '';
            $add_more = '';
            $el_class = '';
            $aboutus_custom_id = '';
            $css = '';

            /* Extract shortcode parameters.*/
            extract($atts);

            $css_class = array( 'about-us-info worldmart-aboutus', $style);

            if( $el_class )
                $css_class[] = $el_class;

            if( $aboutus_custom_id )
                $css_class[] = $aboutus_custom_id;

            if ( function_exists( 'vc_shortcode_custom_css_class' ) ){
                $css_class[] = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $css, ' ' ), '', $atts );
            }

            $list_more = vc_param_group_parse_atts( $add_more );

            ob_start(); ?>
            <div class="<?php echo implode(' ', $css_class ) ?>">
                <?php if( $style == 'layout3' && $index_code ){ ?>
                    <b class="index-code"><?php echo esc_html($atts['index_code']); ?></b>
                <?php }?>
                <?php if( $title ){ ?>
                    <h3 class="title-box"><?php echo esc_html( $title ) ?></h3>
                <?php }?>
                <?php if( $desc ){ ?>
                    <p class="desc"><?php echo esc_html( $desc ); ?></p>
                <?php } ?>
                <?php if ( $style == 'layout4' && !empty( $list_more ) ){ ?>
                    <ul class="toggle-box">
                        <?php foreach ( $list_more as $obj => $value) : ?>
                            <li class="toggle-item item<?php echo esc_attr(++$obj); ?>">
                                <span class="icon"></span>
                                <h4 class="title-box toggle-control"><?php echo esc_html( $value['content_title'] ); ?></h4>
                                <div class="content-box">
                                    <p class="desc"><?php echo esc_html( $value['content_desc'] ); ?></p>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php } ?>
            </div>
            <?php
            $html = ob_get_clean();
            return apply_filters( 'Worldmart_Toolkit_Shortcode_aboutus', wp_specialchars_decode( $html ), $atts ,$content );
        }
    }
}