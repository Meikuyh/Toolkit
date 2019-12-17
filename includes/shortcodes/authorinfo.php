<?php
if (!class_exists('Worldmart_Toolkit_Shortcode_Authorinfo')){
    class Worldmart_Toolkit_Shortcode_Authorinfo extends  Worldmart_Toolkit_Shortcode{

        public $shortcode = 'authorinfo';

        public  static function generate_css( $atts ){
            $css = '';
            return $css;
        }

        public function output_html( $atts, $content = null ){
            $atts = function_exists('vc_map_get_attributes') ? vc_map_get_attributes('worldmart_authorinfo', $atts) : $atts;

            $title = '';
            $desc = '';
            $name = '';
            $picture = '';

            /* Extract shortcode parameters.*/
            extract($atts);

            ob_start(); ?>
            <div class="wrap-meet-team ">
                <?php if( $picture ){ ?>
                    <div class="media">
                        <figure>
                            <img src="<?php echo wp_get_attachment_url( $picture ); ?>" alt="meet our team worldmart" >
                        </figure>
                    </div>
                <?php } ?>
                <div class="desc-group">
                    <b class="author-name"><?php echo esc_html( $name ); ?></b>
                    <span class="author-title"><?php echo esc_html( $title ); ?></span>
                    <p class="desc"><?php echo esc_html( $desc )?></p>
                </div>
            </div>
            <?php
            $html = ob_get_clean();

            return apply_filters( 'Worldmart_Toolkit_Shortcode_authorinfo', wp_specialchars_decode( $html ), $atts ,$content );
        }
    }
}