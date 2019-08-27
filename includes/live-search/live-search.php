<?php
if( !class_exists('Ovic_Live_Search')){
    class Ovic_Live_Search{
        public $key = 'worldmart';
        public $options = array();
        private static $instance;
        public static function instance(){
            if ( !isset( self::$instance ) && !( self::$instance instanceof Ovic_Live_Search ) ) {
                self::$instance = new Ovic_Live_Search;
            }
            self::includes();

            add_action( 'wp_enqueue_scripts', array(  self::$instance , 'scripts' ),1 );
            add_action( 'wp_ajax_ovic_live_search', array(  self::$instance,'get_results') );
            add_action( 'wp_ajax_nopriv_ovic_live_search', array(  self::$instance,'get_results')  );
            add_shortcode( "ovic_live_search_form", array(  self::$instance, 'live_search_form' ) );
            return self::$instance;
        }
        public function __construct(){
            self::get_option_seting();
            $this->options = self::get_option_seting();
            $enable_live_search = isset($this->options['enable_live_search']) ? $this->options['enable_live_search'] : false;
            if( $enable_live_search == false ) return;
        }
        public static function includes(){
            include_once( 'widget.php' );
        }

        private function get_option_seting(){
            $default_info = get_option( $this->key , false );
            $option['max_results']        = isset( $default_info['max_results']) ? $default_info['max_results'] : 3;
            $option['enable_live_search'] = isset( $default_info['enable_live_search']) ? $default_info['enable_live_search'] : 0;
            $options['min_characters']    = isset( $default_info['min_characters']) ? $default_info['min_characters'] : 3;
            $option['show_suggestion']    = isset( $default_info['show_suggestion'] ) ? $default_info['show_suggestion']:'';
            if(!empty($default_info['search_in']) && is_array($default_info['search_in'])){
                $search_in = array();
                foreach ($default_info['search_in'] as $key => $value) {
                    if(!empty($value)){
                        $search_in[] = $key;
                    }                
                }
            } 
            $option['search_in'] = isset( $search_in) ? $search_in : 0;
            return $option;
        }

        public function scripts(){
            wp_enqueue_style( 'live-search', trailingslashit ( plugin_dir_url( __FILE__ ) ). '/css/live-search.css' );
            wp_enqueue_script( 'ovic-live-search', trailingslashit ( plugin_dir_url( __FILE__ ) ). '/js/live-search.js', array( 'jquery' ), '1.0', true );
            wp_localize_script( 'ovic-live-search', 'ovic_ajax_live_search', array(
                'ajaxurl'                         => admin_url('admin-ajax.php'),
                'security'                        => wp_create_nonce('ovic_ajax_live_search'),
                'view_all_text'                   => esc_html__('View All', 'worldmart-toolkit'),
                'product_matches_text'            => esc_html__('Product Matches', 'worldmart-toolkit'),
                'results_text'                    => esc_html__('Results', 'worldmart-toolkit'),
                'ovic_enable_live_search'         => isset($this->options['enable_live_search']) ? $this->options['enable_live_search'] : 0,
                'ovic_live_search_min_characters' => isset( $this->options['min_characters']) ? $this->options['min_characters'] : 3
            ) );
        }

        public function get_results(){
            $keyword = $_POST['keyword'];
            $product_cat = $_POST['product_cat'];
            if( !isset( $keyword ) || $keyword == ''){
                exit;
            }
            $data = array();
            $options = $this->options;

            $data['max_results']       = isset( $options['max_results']) ? $options['max_results'] : 3;
            $data['show_suggestion']   = isset( $options['show_suggestion'] ) ? $options['show_suggestion']:'';
            $data['search_in']         = isset( $options['search_in']) ? $options['search_in'] : array('title');
            $data['keyword']           = $keyword;
            $data['product_cat']       =  $product_cat;

            $args = array(
                'post_type'      => 'product',
                'post_status'    => 'publish',
                'orderby'        => 'post_title',
                'order'          => 'ASC',
                'posts_per_page' => ( int ) $data['max_results'],
            );
            if (  $product_cat !="" ) {
                $args['tax_query'] = array(
                    array(
                        'taxonomy' => 'product_cat',
                        'field'    => 'slug',
                        'terms'    => array_map( 'sanitize_title', explode( ',', $product_cat )),
                    ),
                );
            }

            // Prepare suggestion setting.
            if ( $data['show_suggestion'] != 'on' ) {
                $args['fields'] = 'ids';
            }

            // Globalize Live Search settings.
            $GLOBALS['ovic_live_search_settings'] = $data;

            // Register where filter.
            add_filter( 'posts_where', array( __CLASS__, 'posts_where' ) );
            add_filter( 'posts_groupby', array( __CLASS__, 'posts_groupby' ) );

            // Register join filter.
            if( in_array('sku',$data['search_in']) ) {
                add_filter( 'posts_join', array( __CLASS__, 'posts_join' ) );
            }
            // Prepare return data.
            $return_data = array();

            // Query for results.
            $products = new WP_Query();
            $products = $products->query( $args );
            $return_data['result_count']= count($products);

            if( $products ){
                foreach ( $products as $key => $product ) {
                    $product = wc_get_product( $product );
                    // Add property sku to products
                    if( $data['show_suggestion'] == 'on' && in_array('sku',$data['search_in']) ) {

                        $products[ $key ]->ovic_sku = $product->get_sku();

                    }

                    if ( $product ) {
                        $return_data['list_product'][] = array(
                            'title' => $product->get_title(),
                            'url'   => $product->get_permalink(),
                            'image' => $product->get_image( array( 100, 100 ) ),
                            'price' => $product->get_price_html(),
                        );
                    }
                }
                if ( $data['show_suggestion'] == 'on' ) {
                    foreach ( $products as $product ) {
                        // Find keyword in title.
                        if ( in_array('title',$data['search_in']) ) {
                            // Convert HTML tag and shortcode to space.
                            $content_search = preg_replace( '/\\[[^\\]]*\\]|<[^>]*>/', ' ', $product->post_title );

                            // Find keyword.
                            $position_keyword = stripos( $content_search, $data['keyword'] );

                            if ( $position_keyword !== false && $position_keyword + strlen( $data['keyword'] ) < strlen( $content_search ) ) {
                                // Get suggestion of keyword in content.
                                $return_data['suggestion'] = self::get_suggestion( $content_search, $data['keyword'] );

                                break;
                            }
                        }

                        // Find keyword in description.
                        if ( in_array('description',$data['search_in']) && ! isset( $return_data['suggestion'] ) ) {
                            // Convert HTML tag and shortcode to space.
                            $content_search = preg_replace( '/\\[[^\\]]*\\]|<[^>]*>/', ' ', $product->post_excerpt );

                            // Find keyword.
                            $position_keyword = stripos( $content_search, $data['keyword'] );

                            if ( $position_keyword !== false && $position_keyword + strlen( $data['keyword'] ) < strlen( $content_search ) ) {
                                // Get suggestion of keyword in content.
                                $return_data['suggestion'] = self::get_suggestion( $content_search, $data['keyword'] );

                                break;
                            }
                        }

                        // Find keyword in content.
                        if ( in_array('content',$data['search_in']) && ! isset( $return_data['suggestion'] ) ) {
                            // Convert HTML tag and shortcode to space.
                            $content_search = preg_replace( '/\\[[^\\]]*\\]|<[^>]*>/', ' ', $product->post_content );

                            // Find keyword.
                            $position_keyword = stripos( $content_search, $data['keyword'] );

                            if ( $position_keyword !== false && $position_keyword + strlen( $data['keyword'] ) < strlen( $content_search ) ) {
                                // Get suggestion of keyword in content.
                                $return_data['suggestion'] = self::get_suggestion( $content_search, $data['keyword'] );

                                break;
                            }
                        }

                        // Find keyword in sku.
                        if (  in_array('sku',$data['search_in'])  && ! isset( $return_data['suggestion'] ) ) {
                            // Convert HTML tag and shortcode to space.
                            $content_search = preg_replace( '/\\[[^\\]]*\\]|<[^>]*>/', ' ', $product->ovic_sku );

                            // Find keyword.
                            $position_keyword = stripos( $content_search, $data['keyword'] );

                            if ( $position_keyword !== false && $position_keyword + strlen( $data['keyword'] ) < strlen( $content_search ) ) {
                                // Get suggestion of keyword in content.
                                $return_data['suggestion'] = self::get_suggestion( $content_search, $data['keyword'] );

                                break;
                            }
                        }
                    }
                }
                wp_send_json( $return_data );
            }

            wp_send_json( array( 'message' => esc_html__( 'No results.', 'worldmart-toolkit' ) ) );

            wp_die();
        }

        /**
         * Prepare where clause for query statement.
         *
         * @param   string  $where  Current where clause.
         *
         * @return  string
         */
        public static function posts_where( $where ) {
            global $wpdb, $ovic_live_search_settings;

            // Prepare search coverages.
            $columns = array();

            if ( in_array('title',$ovic_live_search_settings['search_in']) ) {
                $columns[] = ' ' . $wpdb->posts . '.post_title LIKE "%' . sanitize_text_field( $ovic_live_search_settings['keyword'] ) . '%" ';
            }

            if ( in_array('description',$ovic_live_search_settings['search_in']) ) {
                $columns[] = ' ' . $wpdb->posts . '.post_excerpt LIKE "%' . sanitize_text_field( $ovic_live_search_settings['keyword'] ) . '%" ';
            }

            if ( in_array('content',$ovic_live_search_settings['search_in']) ) {
                $columns[] = ' ' . $wpdb->posts . '.post_content LIKE "%' . sanitize_text_field( $ovic_live_search_settings['keyword'] ) . '%" ';
            }

            if ( in_array('sku',$ovic_live_search_settings['search_in']) ) {
                $columns[] = '( ' . $wpdb->postmeta . '.meta_key = "_sku" AND '  . $wpdb->postmeta . '.meta_value LIKE "%' . sanitize_text_field( $ovic_live_search_settings['keyword'] ) . '%" )';
            }

            if ( count( $columns ) ) {
                $where .= ' AND ( ' . implode( ' OR ', $columns ) . ' ) ';
            }

            return $where;
        }

        /**
         * Prepare groupby clause for query statement.
         *
         * @param   string  $groupby  Current groupby clause.
         *
         * @return  string
         */
        public static function posts_groupby( $groupby ) {
            global $wpdb;

            $groupby = "{$wpdb->posts}.ID";

            return $groupby;
        }
        /**
         * Prepare join clause for query statement.
         *
         * @param   string  $join  Current join clause.
         *
         * @return  string
         */
        public static function posts_join( $join ) {

            global $wpdb;
            if( strpos( $join, $wpdb->postmeta ) === false ) {
                $join .= ' INNER JOIN ' . $wpdb->postmeta . ' ON ( ' . $wpdb->posts . '.ID = ' . $wpdb->postmeta . '.post_id ) ';

            }

            return $join;
        }

        /**
         * Get suggestion of keyword in content.
         *
         * @param   string  $content  Content.
         * @param   string  $keyword  Keyword.
         *
         * @return  string
         */
        public static function get_suggestion( $content, $keyword ) {
            // Get the postion of the first keyword in content.
            $index_keyword = stripos( $content, $keyword );

            // Strip the content from that keyword postion.
            $post_title = substr( $content, ( $index_keyword + strlen( $keyword ) ), 40 );

            // Get the postion of the last keyword in content.
            $index_keyword = stripos( $content, $post_title ) + strlen( $post_title );

            // Prepare the title.
            for ( $i = 0; $i < 30; $i++ ) {
                $post_title_add = substr( $content, $index_keyword + $i, 1 );

                if ( $post_title_add == ' ' ) {
                    break;
                } else {
                    $post_title .= $post_title_add;
                }
            }

            return $keyword . $post_title;
        }

        public function live_search_form( $atts, $content =''){
            $default = array(
                'placeholder' => __('Search products','worldmart-toolkit'),
            );
            $atts = shortcode_atts( $default, $atts );
            $selected = '';
            if( isset( $_GET['product_cat']) && $_GET['product_cat'] ){
                $selected = $_GET['product_cat'];
            }
            $args_cat = array(
                'show_option_none' => __( 'All Categories', 'worldmart-toolkit' ),
                'taxonomy'          => 'product_cat',
                'class'             => 'categori-search-option',
                'hide_empty'        => 1,
                'orderby'           => 'name',
                'order'             => "asc",
                'tab_index'         => true,
                'hierarchical'      => true,
                'id'                => rand(),
                'name'              => 'product_cat',
                'value_field'       => 'slug',
                'selected'          => $selected,
                'option_none_value' => '0',
            );
            ob_start();
            if( class_exists( 'WooCommerce' ) ){ ?>
                <form method="get" action="<?php echo esc_url( home_url( '/' ) ) ?>" class="block-search ovic-live-search-form">
                    <div class="form-content">
                        <input type="hidden" name="post_type" value="product" />
                        <input type="hidden" name="taxonomy" value="product_cat">
                        <div class="serach-box results-search">
                            <input autocomplete="off" type="text" class="serchfield txt-livesearch input"  name="s" value ="<?php echo esc_attr( get_search_query() );?>"  placeholder="<?php echo esc_html( $atts['placeholder']);?>">
                            <div class="category">
                                <?php wp_dropdown_categories( $args_cat ); ?>
                            </div>
                            <button class="btn-search"><?php esc_html_e('Search','worldmart');?></button>
                        </div>
                    </div>
                </form>
            <?php
            }else{
                ?>
                <form method="get" action="<?php echo esc_url( home_url( '/' ) ) ?>" class="block-search ovic-live-search-form">
                    <div class="serach-box results-search">
                        <input autocomplete="off" type="text" class="serchfield txt-livesearch"  name="s" value ="<?php echo esc_attr( get_search_query() );?>"  placeholder="<?php echo esc_html( $atts['placeholder']);?>">
                    </div>
                </form>
                <?php
            }
            $html = ob_get_clean();
            return apply_filters('ovic_output_live_search_form',$html,$atts);
        }
    }
}

$ovic_live_search = new Ovic_Live_Search();
$ovic_live_search::instance();
