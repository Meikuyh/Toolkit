<?php
/* fix compare form disable scroll on ipad*/
if( !function_exists('worldmart_add_custom_css') ){
    function worldmart_add_custom_css() {
        echo '<link rel="stylesheet" href="'.get_template_directory_uri().'/css/custom.css'.'">';
    }
}
add_action( 'wp_head', 'worldmart_add_custom_css', 99999 );