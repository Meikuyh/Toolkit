<?php

/**
 * Color RGB field for RW Meta Box plugin.
 *
 */
class RWMB_Color_rgb_Field extends RWMB_Field {
    /**
     * Enqueue scripts and styles
     *
     * @return void
     */
    static function admin_enqueue_scripts()
    {
        // Enqueue Spectrum library for picking color.
        wp_enqueue_style( 'spectrum-color-picker', get_template_directory_uri() . '/framework/includes/meta-box/assets/css/spectrum.css' );
        wp_enqueue_script( 'spectrum-color-picker', get_template_directory_uri() . '/framework/includes/meta-box/assets/js/spectrum.js' );

    }

    /**
     * Get field HTML
     *
     * @param mixed $meta
     * @param array $field
     *
     * @return string
     */
    static function html( $meta, $field )
    {
        // Prepare meta value
        $meta = $meta == '' ? $field['std'] : $meta;

        // Print HTML
        ob_start();
        ?>
        <div id="<?php echo esc_attr( $field['id'] ); ?>-container">
            <input id="<?php echo esc_attr( $field['id'] ); ?>" name="<?php echo esc_attr( $field['id'] ); ?>" type='text' value="<?php echo esc_attr( $meta );?>"/>
        </div>
        <script>
            jQuery( function ( $ ) {
                'use strict';
                $("#<?php echo esc_attr( $field['id'] ); ?>").spectrum({
                    color: "<?php echo esc_attr( $meta );?>",
                    showAlpha:true,
                    showInput: true,
                    allowEmpty:true,
                    preferredFormat: "rgb",
                    change: function(color) {
                        $("#basic-log").text("change called: " + color);
                    }
                });
            });
        </script>
        <?php
        return ob_get_clean();
    }
}
