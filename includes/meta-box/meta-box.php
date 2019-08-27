<?php
if( !class_exists('Worldmart_Meta_Box') && class_exists('RW_Meta_Box')){
    class Worldmart_Meta_Box extends RW_Meta_Box {
        /**
         * Variable to hold the initialization state.
         *
         * @var  boolean
         */
        protected static $initialized = false;

        /**
         * Initialize pluggable functions.
         *
         * @return  void
         */
        public static function initialize() {
            // Do nothing if pluggable functions already initialized.
            if ( self::$initialized ) {
                return;
            }

            // Disable page options in WooCommerce shop page.
            global $pagenow;

            // Remove original RW Meta Box init action.
            remove_action( 'admin_init', 'rwmb_register_meta_boxes' );

            foreach ( $GLOBALS['wp_filter']['admin_init'] as $p => $handles ) {
                foreach ( $handles as $k => $handle ) {
                    if ( is_array( $handle['function'] ) ) {
                        if ( is_object( $handle['function'][0] ) && 'RWMB_Core' == get_class( $handle['function'][0] ) ) {
                            if ( 'register_meta_boxes' == $handle['function'][1] ) {
                                unset( $GLOBALS['wp_filter']['admin_init'][ $p ][ $k ] );
                            }
                        }
                    }
                }
            }

            // Add action to init RW Meta Box.
           // add_action( 'admin_init', array( __CLASS__, 'register_meta_boxes' ) );

            // Register necessary actions / filters to hook WR Nitro meta boxes into WordPress.
            add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_admin_assets' ) );

            add_filter( 'rwmb_html', array( __CLASS__, 'field_html' ), 10, 3 );

            // State that initialization completed.
            self::$initialized = true;
        }

        /**
         * Initialize RW Meta Box.
         *
         * @return  void
         */
        public static function register_meta_boxes() {
            // Get meta boxes to register.
            $meta_boxes = apply_filters( 'rwmb_meta_boxes', array() );


            if ( is_array( $meta_boxes ) ) {
                // Load all custom fields.
                static $loaded;

                if ( ! isset( $loaded ) ) {
                    foreach ( glob( implode( '/', array_slice( explode( '/', str_replace( '\\', '/', __FILE__ ) ), 0, -1 ) ) . '/fields/*.php' ) as $file ) {
                        include_once $file;
                    }

                    $loaded = true;
                }

                // Instantiate all meta boxes.
                foreach ( $meta_boxes as $meta_box ) {
                    $meta_box = new self( $meta_box );
                }
            }
        }

        /**
         * Enqueue required admin assets.
         *
         * @return  void
         */
        public static function enqueue_admin_assets() {
            global $post;
            wp_enqueue_style( 'worldmart-metabox', WORLDMART_TOOLKIT_URL . '/includes/meta-box/assets/css/meta-box.css' );
            wp_enqueue_script( 'worldmart-metabox', WORLDMART_TOOLKIT_URL. '/includes/meta-box/assets/js/meta-box.js', array( 'jquery' ), '2.4', true );
        }
        /**
         * Callback function to show fields in meta box.
         *
         * @return  void
         */
        function show() {
            global $post;

            $saved = $this->is_saved();

            // Container
            printf(
                '<div class="rwmb-meta-box" data-autosave="%s">',
                $this->meta_box['autosave'] ? 'true' : 'false'
            );

            wp_nonce_field( "rwmb-save-{$this->meta_box['id']}", "nonce_{$this->meta_box['id']}" );

            // Allow users to add custom code before meta box content
            // 1st action applies to all meta boxes
            // 2nd action applies to only current meta box
            do_action( 'rwmb_before', $this );
            do_action( "rwmb_before_{$this->meta_box['id']}", $this );

            // Print HTML code for all fields
            $current_tab = null;
            $tab_heading = $tab_body = '';

            foreach ( $this->fields as $field ) {
                if ( isset( $field['tab'] ) && $current_tab != $field['tab'] ) {
                    $tab_id = sanitize_key( $field['tab'] );

                    // Update tab heading.
                    $tab_heading .= '
					<li class="meta-box-tab-' . $tab_id . ( empty( $current_tab ) ? ' active' : '' ) . '">
						<a href="#' . $tab_id . '">' . $field['tab'] . '</a>
					</li>';

                    // Update tab body.
                    $tab_body .= ( empty( $current_tab ) ? '' : '</div>' ) . '
					<div id="' . $tab_id . '" class="meta-box-tabs-content ' . ( empty( $current_tab ) ? '' : 'hidden' ) . '">';

                    $current_tab = $field['tab'];
                }

                // Start output buffering to hold field output.
                ob_start();

                if ( method_exists( __CLASS__, 'get_class_name' ) ) {
                    call_user_func( array( self::get_class_name( $field ), 'show' ), $field, $saved );
                } elseif ( class_exists( 'RWMB_Field' ) && method_exists( 'RWMB_Field', 'call' ) ) {
                    RWMB_Field::call( 'show', $field, $saved );
                }

                $tab_body .= ob_get_contents();

                ob_end_clean();
            }

            if ( ! empty( $tab_heading ) ) {
                echo '
				<div class="meta-box-tabs" id="' . $this->meta_box['id'] . '">
					<ul class="meta-box-tabs-nav">' . $tab_heading . '</ul>
					' . $tab_body . '</div>
				</div>
				<scr' . 'ipt>
					(function($) {
						$("#' . $this->meta_box['id'] . '").on("click", ".meta-box-tabs-nav a", function(e) {
							e.preventDefault();
							$("#' . $this->meta_box['id'] . ' .meta-box-tabs-nav li").removeClass("active");
							$(this).parent().addClass("active");
							$("#' . $this->meta_box['id'] . ' .meta-box-tabs-content").addClass("hidden").filter($(this).attr("href")).removeClass("hidden");
						});
					})(jQuery);
				</scr' . 'ipt>';
            } else {
                echo worldmart_output('' . $tab_body) ;
            }

            // Include validation settings for this meta-box
            if ( isset( $this->validation ) && $this->validation ) {
                echo '
				<scr' . 'ipt>
				if ( typeof rwmb == "undefined" )
				{
					var rwmb = {
						validationOptions : jQuery.parseJSON( \'' , json_encode( $this->validation ) , '\' ),
						summaryMessage : "' , esc_js( esc_html__( 'Please correct the errors highlighted below and try again.', 'worldmart' ) ) , '"
					};
				}
				else
				{
					var tempOptions = jQuery.parseJSON( \'' , json_encode( $this->validation ) . '\' );
					jQuery.extend( true, rwmb.validationOptions, tempOptions );
				}
				</scr' . 'ipt>
			';
            }

            // Allow users to add custom code after meta box content
            // 1st action applies to all meta boxes
            // 2nd action applies to only current meta box
            do_action( 'rwmb_after', $this );
            do_action( "rwmb_after_{$this->meta_box['id']}", $this );

            // End container
            echo '</div>';
        }

        public static function field_html( $field_html, $field, $meta ){
            $attributes = '';
            if( isset($field['dependency']) && $field['dependency'] && count( $field['dependency'] ) > 0 ){
                $arg = $field['dependency'];

                $attrs['data-did'] = $arg['id'];
                if( is_array( $arg['value'] ) ){
                    $attrs['data-dval'] = implode( ',', $arg['value'] );
                }else{
                    $attrs['data-dval'] = $arg['value'];
                }
                if( isset( $arg['operator'] ) && $arg['operator'] ){
                    $attrs['data-operator'] = $arg['operator'];
                }else{
                    $attrs['data-operator'] = 'equal';
                }
                $attrs['data-dependency'] = 'on';

                if( isset($attrs) && is_array($attrs) && count($attrs) > 0 ){
                    foreach ( $attrs as $key => $value ){
                        $attributes .= $key.'='.$value.' ';
                    }
                }
                $field_html = '<div class="dependency" '.$attributes.'>'.$field_html;
                $field_html .= '</div>';
            }

            return $field_html;
        }
    }
}

if ( is_admin() ) {
    // Initialize meta boxes.
    if ( class_exists( 'RW_Meta_Box' ) ) {
        Worldmart_Meta_Box::initialize();
    }
}
