<?php

if (!class_exists('Worldmart_Toolkit_Shortcode_Custommenu')){
    class Worldmart_Toolkit_Shortcode_Custommenu extends  Worldmart_Toolkit_Shortcode{
        /**
         * Shortcode name.
         *
         * @var  string
         */
        public $shortcode = 'custommenu';


        /**
         * Default $atts .
         *
         * @var  array
         */
        public  $default_atts  = array(

        );


        public static function generate_css( $atts ){
            // Extract shortcode parameters.
            extract($atts);
            $css = '';
            return $css;
        }


        public function output_html( $atts, $content = null ){
            $atts = function_exists('vc_map_get_attributes') ? vc_map_get_attributes('worldmart_custommenu', $atts) : $atts;

            // Extract shortcode parameters.
            extract($atts);

            $css_class = array('worldmart-custommenu');
            $css_class[] = isset($atts['menu_style']) ? $atts['menu_style'] : '';
            $css_class[] = isset($atts['el_class']) ? $atts['el_class'] : '';
            $css_class[] = isset($atts['custommenu_custom_id']) ? $atts['custommenu_custom_id'] : '';

            if ( function_exists( 'vc_shortcode_custom_css_class' ) ){
                $css_class[] = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $css, ' ' ), '', $atts );
            }
            ob_start();
            if($menu_style =='megamenu') {
                $nav_menu = get_term_by('slug', $atts['menu'], 'nav_menu'); ?>
                <div class="megamenu-item worldmart-menu-item">
                    <?php if ($title) { ?>
                        <h3 class="title menu-title"><?php esc_html_e($title,'worldmart-toolkit'); ?></h3>
                    <?php } ?>
                    <?php
                    if ($nav_menu->term_id) { $instance['nav_menu'] = $nav_menu->term_id; 
                        wp_nav_menu(array(
                            'menu' => $nav_menu->term_id,
                            'fallback_cb' => 'Worldmart_navwalker::fallback',
                            'walker' => new Worldmart_navwalker(),
                        ));
                    } 
                    ?>
                </div>
                <?php
            }elseif ($menu_style == 'vertical'){
                $nav_menu = get_term_by('slug', $atts['menu'],'nav_menu');
                $css_class[] = 'vertical-menu-style';
                ?>
                <div class="<?php echo esc_attr( implode(' ', $css_class) );?>">
                    <div class="wrap-menu">
                        <?php
                        if( !is_wp_error($nav_menu)):
                            $instance = array();
                            if( $atts['title'] ){
                                $instance['title'] = $atts['title'];
                            }
                            if( $nav_menu->term_id ){
                                $instance['nav_menu'] = $nav_menu->term_id;
                            }
                            the_widget('WP_Nav_Menu_Widget',$instance);
                        endif;
                        ?>
                    </div>

                </div>
                <?php
            }else {
                $nav_menu = get_term_by('slug', $atts['menu'],'nav_menu'); ?>
                <div class="<?php echo esc_attr( implode(' ', $css_class) ); ?>">
                    <?php if( !is_wp_error($nav_menu)):
                        $instance = array();
                        if( $atts['title'] ){
                            $instance['title'] = $atts['title'];
                        }
                        if( $nav_menu->term_id ){
                            $instance['nav_menu'] = $nav_menu->term_id;
                        }
                        the_widget('WP_Nav_Menu_Widget',$instance);
                    endif; ?>
                </div>
                <?php
            }
            $html = ob_get_clean();
            return apply_filters( 'worldmart_toolkit_shortcode_custommenu', force_balance_tags( $html ), $atts ,$content );
        }
    }
}