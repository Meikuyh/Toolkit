<?php

if (!class_exists('Worldmart_Toolkit_Shortcode_Tabs')){
    class Worldmart_Toolkit_Shortcode_Tabs extends  Worldmart_Toolkit_Shortcode{
        /**
         * Shortcode name.
         *
         * @var  string
         */
        public $shortcode = 'tabs';


        /**
         * Default $atts .
         *
         * @var  array
         */
        public  $default_atts  = array(
            'style'          => '',
            'tab_animate'    => '',
            'el_class'       => '',
            'css'            => '',
            'ajax_check'     => 'no',
            'tabs_custom_id' => '',
            'tab_title'      => '',
            'custom_url'     => '',
            'banner_image'   => ''
        );


        public static  function generate_css( $atts ){
            /* Extract shortcode parameters.*/
            extract($atts);
            return '';
        }

        public function output_html( $atts, $content = null )
        {
            $style          = 'default';
            $tab_animate    = '';
            $el_class       = '';
            $css            = '';
            $ajax_check     = '0';
            $tabs_custom_id = '';
            $tab_title      = '';
            $custom_url     = '';
            $banner_image   = '';

            $atts           = function_exists('vc_map_get_attributes') ? vc_map_get_attributes('worldmart_tabs', $atts) : $atts;
            /* Extract shortcode parameters.*/
            extract($atts);

            $css_class = 'worldmart-tabs ' . $el_class . ' ' . $style;
            if (function_exists('vc_shortcode_custom_css_class')) {
                $css_class .= ' ' . apply_filters(VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class($css, ' '), '', $atts);
            }
            $html_banner = '';
            if (($style == 'default' || $style == 'style-inbox') && $banner_image) {
                $url                = wp_get_attachment_image_src($banner_image, 'full', false);
                $custom_url_start   = '';
                $custom_url_end     = '';
                if ($custom_url) {
                    $custom_url_start   = '<a class="link-tab-banner" href="' . esc_url( $custom_url ) . '" title="' . esc_attr( $tab_title ) . '">';
                    $custom_url_end     = '</a>';
                }
                $html_banner            = $custom_url_start;
                $img_lazy               = "data:image/svg+xml;charset=utf-8,%3Csvg%20xmlns%3D%27http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%27%20viewBox%3D%270%200%20" . $url[1] . "%20" . $url[2] . "%27%2F%3E";
                $worldmart_enable_lazy  = worldmart_option('worldmart_enable_lazy', '1');
                if ( $worldmart_enable_lazy ) {
                    $html_banner .= '<figure><img class="lazy tab-banner " src="' . esc_attr($img_lazy) . '" alt="' . esc_attr($tab_title) . '"  data-src="' . esc_url($url[0]) . '" ' . image_hwstring($url[1], $url[2]) . ' ></figure>';
                } else {
                    $html_banner .= '<figure><img class="tab-banner " src="' . esc_url($url[0]) . '" alt="' . esc_attr($tab_title) . '"  ' . image_hwstring($url[1], $url[2]) . ' ></figure>';
                }
                $html_banner .= $custom_url_end;
            }

            $sections           = $this->get_all_attributes('vc_tta_section', $content);
            $html_tab_link      = '';
            $html_tab_container = '';

            if ( $sections && is_array( $sections ) && count( $sections ) ){
                $post_id        = get_the_ID();
                $html_tab_link  = '<ul class="tabs-link">';
                foreach ( $sections as $index => $section ) {
                    $active         = '';
                    $tab_content    = '';
                    $btn_class      = '';
                    if( $index == 0 ){
                        $active      = 'active';
                        $btn_class   = 'loaded';
                        $tab_content = do_shortcode($section['content']);
                    }else{
                        if ( $ajax_check !== '1' ) {
                            $tab_content = do_shortcode($section['content']);
                        }
                    }
                    $html_tab_link      .= '<li class="tab-link ' . esc_attr( $active ) . '"><a class="' . esc_attr( $btn_class ) . '" data-ajax="' . esc_attr( $ajax_check ) . '" data-id="' . esc_attr( $post_id ) . '" data-animate="' . esc_attr( $tab_animate ) . '" data-toggle="tab" href="' . esc_attr($section['tab_id']) . '">' . esc_html($section['title']) . '</a></li>';
                    $html_tab_container .= '<div class="tab-panel ' . esc_attr( $active ) . '" id="' . esc_attr($section['tab_id']) . '">' . wp_specialchars_decode( $tab_content ) . '</div>';
                }
                $html_tab_link .= '</ul>';
            }
            ob_start(); ?>
            <div class="<?php echo esc_attr( $css_class );?>">
                 <?php if( $tab_title ) : ?>
                    <h3 class="title"><?php echo esc_html( $tab_title ); ?></h3>
                <?php endif; ?>
                <div class="tab-head">
                    <?php echo $html_banner; ?>
                    <?php echo $html_tab_link; ?>
                </div>
                <div class="tab-container">
                    <?php echo $html_tab_container; ?>
                </div>
            </div> <?php
            $html = ob_get_clean();
            return apply_filters( 'worldmart_toolkit_shortcode_tabs', force_balance_tags( $html ), $atts ,$content );
        }
    }
}