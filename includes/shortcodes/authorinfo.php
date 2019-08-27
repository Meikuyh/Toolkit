<?php

if (!class_exists('Worldmart_Toolkit_Shortcode_Authorinfo')){
    class Worldmart_Toolkit_Shortcode_Authorinfo extends  Worldmart_Toolkit_Shortcode{
        /**
         * Shortcode name.
         *
         * @var  string
         */
        public $shortcode = 'authorinfo';


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
            $atts = function_exists('vc_map_get_attributes') ? vc_map_get_attributes('worldmart_authorinfo', $atts) : $atts;

            // Extract shortcode parameters.
            extract($atts);


            ob_start();
            ?>
            <div class="wrap-meet-team ">
                <?php if($atts['picture']){ ?>
                    <?php $image = wp_get_attachment_url( $atts['picture'] ); ?>
                    <div class="media">
                        <figure>
                            <img src="<?php  echo esc_url($image) ;?>" alt="meet our team worldmart" >
                        </figure>
                    </div>
                <?php } ?>
                    <div class="desc-group">
                        <b class="author-name"><?php esc_html_e($name,'worldmart-toolkit'); ?></b>
                        <span class="author-title"><?php esc_html_e($title,'worldmart-toolkit'); ?></span>
                        <p class="desc"><?php esc_html_e($desc,'worldmart-toolkit')?></p>
                    </div>
            </div>
            <?php
            $html = ob_get_clean();

            return apply_filters( 'Worldmart_Toolkit_Shortcode_authorinfo', force_balance_tags( $html ), $atts ,$content );
        }
    }
}