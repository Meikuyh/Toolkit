<?php

if (!class_exists('Worldmart_Toolkit_Shortcode_Aboutus')){
    class Worldmart_Toolkit_Shortcode_Aboutus extends  Worldmart_Toolkit_Shortcode{
        /**
         * Shortcode name.
         *
         * @var  string
         */
        public $shortcode = 'aboutus';


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
            $atts = function_exists('vc_map_get_attributes') ? vc_map_get_attributes('worldmart_aboutus', $atts) : $atts;
            extract($atts);
            $css_class = 'worldmart-aboutus '. $atts['el_class'] .' '.$atts['style'];
            $css_class .=' '.$atts['aboutus_custom_id'];
            if ( function_exists( 'vc_shortcode_custom_css_class' ) ){
                $css_class .= ' ' . apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $css, ' ' ), '', $atts );
            }
            // Extract shortcode parameters.

            $add_more = vc_param_group_parse_atts( $atts['add_more'] );
            ob_start();
            ?>
            <div class="about-us-info <?php echo esc_attr($css_class); ?>">
                <?php if($atts['style'] == 'layout3' && !empty($atts['index_code'])){ ?>
                    <b class="index-code"><?php echo esc_html($atts['index_code']); ?></b>
                <?php }?>
                <?php if($atts['title']){ ?>
                    <h3 class="title-box"><?php esc_html_e($atts['title'],'worldmart-toolkit')?></h3>
                <?php }?>
                <?php if($atts['desc']){ ?>
                    <p class="desc"><?php esc_html_e($atts['desc'],'worldmart-toolkit'); ?></p>
                <?php } ?>
                <?php if ($atts['style'] == 'layout4' && !empty($add_more) && is_array($add_more)){ ?>
                    <ul class="toggle-box">
                    <?php foreach ($add_more as $obj => $value) : ?>
                        <li class="toggle-item item<?php echo esc_attr(++$obj); ?>">
                            <span class="icon"></span>
                            <h4 class="title-box toggle-control"><?php esc_html_e($value['content_title'],'worldmart-toolkit'); ?></h4>
                            <div class="content-box">
                                <p class="desc"><?php esc_html_e($value['content_desc'],'worldmart-toolkit')?></p>
                            </div>
                        </li>
                    <?php endforeach; ?>
                    </ul>
                <?php } ?>
            </div>
            <?php
            $html = ob_get_clean();
            return apply_filters( 'Worldmart_Toolkit_Shortcode_aboutus', force_balance_tags( $html ), $atts ,$content );
        }
    }
}