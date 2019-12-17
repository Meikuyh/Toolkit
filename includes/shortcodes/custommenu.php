<?php
if (!class_exists('Worldmart_Toolkit_Shortcode_Custommenu')){
    class Worldmart_Toolkit_Shortcode_Custommenu extends  Worldmart_Toolkit_Shortcode{

        public $shortcode = 'custommenu';

        public static function generate_css( $atts ){
            $css = '';
            return $css;
        }


        public function output_html( $atts, $content = null ){
            $atts = function_exists('vc_map_get_attributes') ? vc_map_get_attributes('worldmart_custommenu', $atts) : $atts;

            $menu_style             ='megamenu';
            $title                  = '';
            $menu_style             = '';
            $el_class               = '';
            $custommenu_custom_id   = '';
            $menu                   = '';
            $css                    = '';

            /* Extract shortcode parameters.*/
            extract($atts);

            $css_class      = array('worldmart-custommenu');
            $css_class[]    = $menu_style ? $menu_style : '';
            $css_class[]    = $el_class ? $el_class : '';
            $css_class[]    = $custommenu_custom_id ? $custommenu_custom_id : '';

            if ( function_exists( 'vc_shortcode_custom_css_class' ) ){
                $css_class[] = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $css, ' ' ), '', $atts );
            }

            if( ! term_exists( $menu, 'nav_menu') ) return false;

            ob_start();
            if( $menu_style =='megamenu' ) { ?>
                <div class="megamenu-item worldmart-menu-item">
                    <?php if ($title) { ?>
                        <h3 class="title menu-title"><?php echo esc_html( $title ); ?></h3>
                    <?php } ?>
                    <?php
                        wp_nav_menu( array(
                            'menu'          => $menu,
                            'fallback_cb'   => 'Worldmart_navwalker::fallback',
                            'walker'        => new Worldmart_navwalker(),
                        ));
                    ?>
                </div>
            <?php }elseif ( $menu_style == 'vertical' ) { ?>
                <div class="vertical-menu-style <?php echo esc_attr( implode(' ', $css_class) );?>">
                    <div class="wrap-menu">
                    <?php
                        the_widget('WP_Nav_Menu_Widget', array(
                            'nav_menu' => $menu,
                            'title'    => $title ? $title : ''
                        ));
                    ?>
                    </div>
                </div>
            <?php }else { ?>
                <div class="<?php echo esc_attr( implode(' ', $css_class) ); ?>">
                <?php
                    the_widget('WP_Nav_Menu_Widget', array(
                        'nav_menu' => $menu,
                        'title'    => $title ? $title : ''
                    ));
                ?>
                </div>
            <?php }
            $html = ob_get_clean();
            return apply_filters( 'worldmart_toolkit_shortcode_custommenu', force_balance_tags( $html ), $atts ,$content );
        }
    }
}