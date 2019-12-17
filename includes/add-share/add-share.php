<?php
if ( !function_exists( 'worldmart_single_product_share' ) ) {
    function worldmart_single_product_share() {
        global $post;
        $enable_share_product = worldmart_option( 'worldmart_enable_share_product', 0 );
        $thum_image           = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'full' );
        $url                  = get_permalink( $post->ID );

        add_action( 'wp_footer', 'worldmart_print_scripts' );
        if ( !$enable_share_product) {
            return false;
        }
        ?>

        <div class="worldmart-single-product-socials">

            <!-- Facebook -->
            <div class="fb-like" data-href="<?php echo esc_url( $url ); ?>" data-layout="button_count"
                 data-action="like" data-size="small" data-show-faces="false" data-share="false"></div>

            <!-- Twitter -->
            <a class="twitter-share-button"
               href="<?php echo esc_url( add_query_arg( array( 'text' => urlencode( get_the_title( $post->ID ) ), 'url' => $url ), 'https://twitter.com/intent/tweet' ) ); ?>"
               data-size="small">
                <?php esc_html_e( 'Tweet', 'worldmart' ); ?></a>
            <!-- Pinit -->

            <a href="<?php echo esc_url( add_query_arg( array( 'url' => $url, 'media' => $thum_image[ 0 ], 'description' => urlencode( get_the_title( $post->ID ) ) ), 'http://pinterest.com/pin/create/button/' ) ); ?>"
               class="pin-it-button" count-layout="hozizontal"><?php esc_html_e( 'Pin It', 'worldmart' ); ?></a>

            <!-- G+ -->
            <!--<div class="g-plus" data-action="share" data-annotation="bubble" data-href="<?php /*echo esc_url( $url ); */?>"></div>-->

        </div>
        <?php
    }
}
add_action( 'woocommerce_single_product_summary', 'worldmart_single_product_share', 15);
if ( !function_exists( 'worldmart_ssl' ) ) {
    function worldmart_ssl( $echo = false )
    {
        $ssl = '';
        if ( is_ssl() ) $ssl = 's';
        if ( $echo ) {
            echo esc_attr( $ssl );
        }
        return $ssl;
    }
}

if ( !function_exists( 'worldmart_print_scripts' ) ) {
    function worldmart_print_scripts() { ?>
        <!-- Facebook scripts -->
        <div id="fb-root"></div>
        <script>
            (function (d, s, id) {
                var js, fjs = d.getElementsByTagName(s)[ 0 ];
                if ( d.getElementById(id) ) return;
                js     = d.createElement(s);
                js.id  = id;
                js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.8&appId=1115604095124213";
                fjs.parentNode.insertBefore(js, fjs);
            }(document, 'script', 'facebook-jssdk'));
        </script>

        <!-- Twitter -->
        <script>
            window.twttr = (function (d, s, id) {
                var js, fjs = d.getElementsByTagName(s)[ 0 ],
                    t       = window.twttr || {};
                if ( d.getElementById(id) ) return t;
                js     = d.createElement(s);
                js.id  = id;
                js.src = "https://platform.twitter.com/widgets.js";
                fjs.parentNode.insertBefore(js, fjs);

                t._e    = [];
                t.ready = function (f) {
                    t._e.push(f);
                };

                return t;
            }
            (document, "script", "twitter-wjs"));
        </script>

        <!-- Pinterest -->
        <script type="text/javascript">
            (function () {
                window.PinIt = window.PinIt || {loaded: false};
                if ( window.PinIt.loaded ) return;
                window.PinIt.loaded = true;
                function async_load() {
                    var s   = document.createElement("script");
                    s.type  = "text/javascript";
                    s.async = true;
                    s.src   = "http<?php worldmart_ssl( true ); ?>://assets.pinterest.com/js/pinit.js";
                    var x   = document.getElementsByTagName("script")[ 0 ];
                    x.parentNode.insertBefore(s, x);
                }

                if ( window.attachEvent )
                    window.attachEvent("onload", async_load);
                else
                    window.addEventListener("load", async_load, false);
            })();
        </script>

        <!-- G+ -->
        <!--<script src="https://apis.google.com/js/platform.js" async defer></script>-->
        <?php

    }
}