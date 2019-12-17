<?php
if( !class_exists('Worldmart_Toolkit_Shortcode')){
    class Worldmart_Toolkit_Shortcode{
        /**
         * Shortcode name.
         *
         * @var  string
         */
        public $shortcode = '';
        /**
         * Register shortcode with WordPress.
         *
         * @return  void
         */
        /**
         * Meta key.
         *
         * @var  string
         */
        protected $metakey = '_worldmart_shortcode_custom_css';

        public function __construct() {
            if ( ! empty( $this->shortcode ) ) {
                /* Add shortcode.*/
                add_shortcode( "worldmart_{$this->shortcode}", array( &$this, 'output_html' ) );

                /* Hook into post saving.*/
                add_action( 'save_post', array( &$this, 'update_post' ) );

            }
        }
        /**
         * Replace and save custom css to post meta.
         *
         * @param   int  $post_id
         *
         * @return  void
         */
        public function update_post( $post_id ) {
            if ( ! isset( $_POST['post_ID'] ) || $_POST['post_ID'] != $post_id ) {
                return;
            }

            /* Set and replace content.*/
            $post = $this->replace_post( $post_id );
            if ( $post ) {
                /* Generate custom CSS.*/
                $css = $this->get_css( $post->post_content );
                /* Update post and save CSS to post meta.*/
                $this->save_post( $post );
                $this->save_postmeta( $post_id, $css );
            } else {
                $this->save_postmeta( $post_id, '' );
            }
        }

        /**
         * Replace shortcode used in a post with real content.
         *
         * @param   int  $post_id  Post ID.
         *
         * @return  WP_Post object or null.
         */
        public function replace_post( $post_id ) {
            /* Get post.*/
            $post = get_post( $post_id );
            if ( $post ) {
                if ( has_shortcode( $post->post_content, "worldmart_{$this->shortcode}" ) ) {
                    $post->post_content = preg_replace_callback(
                        '/(' . $this->shortcode . '_custom_id)="[^"]+"/',
                        'worldmart_toolkit_shortcode_replace_post_callback',
                        $post->post_content
                    );
                }
            }
            return $post;
        }

        /**
         * Parse shortcode custom css string.
         *
         * @param   string  $content
         * @param   string  $shortcode
         *
         * @return  string
         */
        public function get_css( $content ) {
            $css = '';
            if ( preg_match_all( '/' . get_shortcode_regex() . '/', $content, $shortcodes ) ) {
                foreach ( $shortcodes[2] as $index => $tag ) {
                    if (  strpos($tag, 'worldmart_') !== false ) {
                        $atts = shortcode_parse_atts( trim( $shortcodes[3][ $index ] ) );
                        $shortcode = explode('_',$tag);
                        $shortcode = end($shortcode);

                        $class = 'Worldmart_Toolkit_Shortcode_' . implode( '_', array_map( 'ucfirst', explode( '-', $shortcode ) ) );
                        if ( class_exists( $class ) ) {
                            $css .= $class::generate_css($atts);
                        }
                    }
                }

                foreach ( $shortcodes[5] as $shortcode_content ) {
                    $css .= $this->get_css( $shortcode_content  );
                }
            }

            return $css;
        }

        /**
         * Update post data content.
         *
         * @param   int  $post  WP_Post object.
         *
         * @return  void
         */
        public function save_post( $post ) {
            /* Sanitize post data for inserting into database.*/
            $data = sanitize_post( $post, 'db' );

            /* Update post content.*/
            global $wpdb;

            $wpdb->query( "UPDATE {$wpdb->posts} SET post_content = '" . esc_sql( $data->post_content ) . "' WHERE ID = {$data->ID};" );

            /* Update post cache.*/
            $data = sanitize_post( $post, 'raw' );

            wp_cache_replace( $data->ID, $data, 'posts' );
        }

        /**
         * Update extra post meta.
         *
         * @param   int     $post_id  Post ID.
         * @param   string  $css      Custom CSS.
         *
         * @return  void
         */
        public function save_postmeta( $post_id, $css ) {
            if ( $post_id && $this->metakey ) {
                if ( empty( $css ) ) {
                    delete_post_meta( $post_id, $this->metakey );
                } else {
                    update_post_meta( $post_id, $this->metakey, preg_replace( '/[\t\r\n]/', '', $css ) );
                }
            }
        }

        /**
         * Generate custom CSS.
         *
         * @param   array  $atts  Shortcode parameters.
         *
         * @return  string
         */
        public static function generate_css( $atts ) {
            return '';
        }


        public function output_html( $atts, $content = null ){
            return '';
        }

        function get_all_attributes( $tag, $text ) {
            preg_match_all( '/' . get_shortcode_regex() . '/s', $text, $matches );
            $out = array();
            $shortcode_content = array();
            if( isset( $matches[5] ) ){
                $shortcode_content = $matches[5];
            }

            if( isset( $matches[2] ) ){
                $i = 0;
                foreach( (array) $matches[2] as $key => $value ){
                    if( $tag === $value ){
                        $out[$i] = shortcode_parse_atts( $matches[3][$key] );
                        $out[$i]['content'] = $matches[5][$key];
                    }
                    $i++;
                }
            }
            return $out;
        }

        public function generate_carousel_data_attributes($prefix = '', $atts){
            $result = '';
            if(isset($atts[$prefix.'autoplay']))
                $result .= 'data-autoplay="'.$atts[$prefix.'autoplay'].'" ';
            if(isset($atts[$prefix.'navigation']))
                $result .= 'data-nav="'.$atts[$prefix.'navigation'].'" ';
            if(isset($atts[$prefix.'dots'])){
                $result .= 'data-dots="'.$atts[$prefix.'dots'].'" ';
            }else{
                $result .= 'data-dots="false"';
            }
            if(isset($atts[$prefix.'loop']))
                $result .= 'data-loop="'.$atts[$prefix.'loop'].'" ';
            if(isset($atts[$prefix.'slidespeed']))
                $result .= 'data-slidespeed="'.$atts[$prefix.'slidespeed'].'" ';
            if(isset($atts[$prefix.'items']))
                $result .= 'data-items="'.$atts[$prefix.'items'].'" ';
            if(isset($atts[$prefix.'margin']))
                $result .= 'data-margin="'.$atts[$prefix.'margin'].'" ';

            $responsive = '';
            if(isset($atts[$prefix.'ts_items'])){
                $responsive .= '"0":{"items":'.$atts[$prefix.'ts_items'].', ';
                if(isset($atts[$prefix.'ts_margin']))
                    $responsive .= '"margin":'.$atts[$prefix.'ts_margin'].'}, ';
                else
                    $responsive .= '"margin":'.$atts[$prefix.'margin'].'}, ';
            }
            if(isset($atts[$prefix.'xs_items'])){
                $responsive .= '"480":{"items":'.$atts[$prefix.'xs_items'].', ';
                if(isset($atts[$prefix.'xs_margin']))
                    $responsive .= '"margin":'.$atts[$prefix.'xs_margin'].'}, ';
                else
                    $responsive .= '"margin":'.$atts[$prefix.'margin'].'}, ';
            }
            if(isset($atts[$prefix.'sm_items'])){
                $responsive .= '"768":{"items":'.$atts[$prefix.'sm_items'].', ';
                if(isset($atts[$prefix.'sm_margin']))
                    $responsive .= '"margin":'.$atts[$prefix.'sm_margin'].'}, ';
                else
                    $responsive .= '"margin":'.$atts[$prefix.'margin'].'}, ';
            }
            if(isset($atts[$prefix.'md_items'])){
                $responsive .= '"992":{"items":'.$atts[$prefix.'md_items'].', ';
                if(isset($atts[$prefix.'md_margin']))
                    $responsive .= '"margin":'.$atts[$prefix.'md_margin'].'}, ';
                else
                    $responsive .= '"margin":'.$atts[$prefix.'margin'].'}, ';
            }
            if(isset($atts[$prefix.'lg_items'])){
                $responsive .= '"1200":{"items":'.$atts[$prefix.'lg_items'].', ';
                if(isset($atts[$prefix.'lg_margin']))
                    $responsive .= '"margin":'.$atts[$prefix.'lg_margin'].'}, ';
                else
                    $responsive .= '"margin":'.$atts[$prefix.'margin'].'}, ';
            }
            if($responsive){
                $responsive = substr($responsive, 0, strlen($responsive)-2);
                $result .= ' data-responsive = \'{'.$responsive.'}\'';
            }
            return $result;
        }

        /**
         * Get Products
         *
         * @return  $products
         */
        public function getProducts( $atts, $args = array(), $ignore_sticky_posts = 1 ){
            extract( $atts );
            $target = isset( $target ) ? $target : 'recent-product';
            $meta_query = WC()->query->get_meta_query();
            $args['meta_query'] = $meta_query;
            $args['post_type']  = 'product';
            if( isset( $taxonomy ) and $taxonomy ){
                $args['tax_query'] =
                    array(
                        array(
                            'taxonomy' => 'product_cat',
                            'field'    => 'slug',
                            'terms'    => array_map( 'sanitize_title', explode( ',', $taxonomy )
                            )
                        )
                    );
            }
            $args['post_status']         = 'publish';
            $args['ignore_sticky_posts'] = $ignore_sticky_posts;
            $args['suppress_filter']     = true;

            if( isset( $atts['per_page'] )  && $atts['per_page']){

                $args['posts_per_page'] = $atts['per_page'];
            }

            if( ! isset( $orderby ) ){
                $ordering_args = WC()->query->get_catalog_ordering_args();
                $orderby = $ordering_args['orderby'];
                $order   = $ordering_args['order'];
            }

            switch( $target ):
                case 'best-selling' :
                    $args[ 'meta_key' ] = 'total_sales';
                    $args[ 'orderby' ]  = 'meta_value_num';
                    break;
                case 'top-rated' :
                    $args[ 'orderby' ] = $orderby;
                    $args[ 'order' ]   = $order;
                    break;
                case 'product-category' :
                    $ordering_args = WC()->query->get_catalog_ordering_args( $atts['orderby'], $atts['order'] );
                    $args[ 'orderby' ] = $ordering_args['orderby'];
                    $args[ 'order' ]   = $ordering_args['order'];
                    break;
                case 'products' :
                    $args['posts_per_page'] = -1;
                    if ( ! empty( $ids ) ) {
                        $args['post__in'] = array_map( 'trim', explode( ',', $ids ) );
                        $args['orderby'] = 'post__in';
                    }
                    if ( ! empty( $skus ) ) {
                        $args['meta_query'][] = array(
                            'key'     => '_sku',
                            'value'   => array_map( 'trim', explode( ',', $skus ) ),
                            'compare' => 'IN'
                        );
                    }
                    break;
                case 'featured_products' :
                    $args[ 'tax_query' ][] =  array(
                        array(
                            'taxonomy' => 'product_visibility',
                            'field'    => 'name',
                            'terms'    => 'featured',
                        )
                    );
                    break;
                case 'product_attribute' :
                    /*'recent-product'*/
                    $args[ 'tax_query' ] =  array(
                        array(
                            'taxonomy' => strstr( $atts['attribute'], 'pa_' ) ? sanitize_title( $atts['attribute'] ) : 'pa_' . sanitize_title( $atts['attribute'] ),
                            'terms'    => array_map( 'sanitize_title', explode( ',', $atts['filter'] ) ),
                            'field'    => 'slug'
                        )
                    );
                    break;
                case 'on_sale' :
                    $product_ids_on_sale = wc_get_product_ids_on_sale();
                    $args['post__in'] = array_merge( array( 0 ), $product_ids_on_sale );
                    if( $orderby == '_sale_price' ){
                        $orderby = 'date';
                        $order   = 'DESC';
                    }
                    $args['orderby'] = $orderby;
                    $args['order'] 	= $order;
                    break;
                default :
                    /*'recent-product'*/
                    $args[ 'orderby' ] = $orderby;
                    $args[ 'order' ]   = $order;
                    if ( isset( $ordering_args['meta_key'] ) ) {
                        $args['meta_key'] = $ordering_args['meta_key'];
                    }
                    /* Remove ordering query arguments*/
                    WC()->query->remove_ordering_args();
                    break;
            endswitch;
            return $products = new WP_Query( apply_filters( 'woocommerce_shortcode_products_query', $args, $atts, null ) );
        }
    }
}

if ( ! function_exists( 'worldmart_toolkit_shortcode_replace_post_callback' ) ) {

    function worldmart_toolkit_shortcode_replace_post_callback( $matches ) {
        /* Generate a random string to use as element ID.*/
        $id = 'worldmart_custom_css_' . mt_rand();
        return $matches[1] . '="' . $id . '"';
    }
}

/* Check plugin wc is activate*/
$active_plugin_wc = is_plugin_active( 'woocommerce/woocommerce.php' );
$shortcodes = array(
    'tabs',
    'iconbox',
    'slider',
    'custommenu',
    'title',
    'googlemap',
    'banner',
    'aboutus',
    'authorinfo',
    'newsletter',
    'socials',
);

if( $active_plugin_wc ){
    $shortcode_woo = array(
        'products'
    );
    $shortcodes = array_merge( $shortcodes, $shortcode_woo );
}

foreach ( $shortcodes as $shortcode ) {
    /* Include shortcode class declaration file.*/
    $shortcode = str_replace( '_', '-', $shortcode );
    if ( is_file( WORLDMART_TOOLKIT_PATH . '/includes/shortcodes/' . $shortcode . '.php' ) ) {
        include_once WORLDMART_TOOLKIT_PATH . '/includes/shortcodes/' . $shortcode . '.php';
    }

    /* Generate shortcode class name.*/
   $class = 'Worldmart_Toolkit_Shortcode_' . implode( '_', array_map( 'ucfirst', explode( '-', $shortcode ) ) );
    if ( class_exists( $class ) ) {
        $shortcode = new $class();
    }
}

if( !function_exists('worldmart_shortcode_print_inline_css') ){
    function worldmart_shortcode_print_inline_css() {
        /* Get all custom inline CSS.*/
        if ( is_singular()  ) {
            $post_custom_css = get_post_meta( get_the_ID(), '_worldmart_shortcode_custom_css', true );
            $inline_css[] = $post_custom_css;
            $inline_css = apply_filters( 'worldmart-shortcode-inline-css', $inline_css );
            if ( count( $inline_css ) ) {
                echo '<style id="worldmart-toolkit-inline" type="text/css">' . trim( implode( ' ', $inline_css ) ) . "</style>\n";
            }
        }
    }
}
add_action( 'wp_head', 'worldmart_shortcode_print_inline_css', 99999 );