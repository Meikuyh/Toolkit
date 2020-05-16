<?php
/**
 * Plugin Name: Worldmart Toolkit
 * Plugin URI:  https://kutethemes.com
 * Description: Worldmart toolkit for Worldmart theme. Currently supports the following theme functionality: shortcodes, CPT.
 * Version:     1.2.0
 * Author:      Kutethemes Team
 * Author URI:  https://kutethemes.com
 * License:     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: worldmart-toolkit
 */

/*Include function plugins if not include.*/
if ( ! function_exists( 'is_plugin_active' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
}

/*Define url to this plugin file.*/
define( 'WORLDMART_TOOLKIT_URL', plugin_dir_url( __FILE__ ) );

/*Define path to this plugin file.*/
define( 'WORLDMART_TOOLKIT_PATH', plugin_dir_path( __FILE__ ) );

/*Define WORLDMART_TOOLKIT_PLUGIN_FILE.*/
define( 'WORLDMART_TOOLKIT_PLUGIN_FILE', __FILE__ );

if( !class_exists('Worldmart_Toolkit')) {
    class Worldmart_Toolkit{

        public $version = '1.2.0';

        public function __construct(){
            $this->define_constants();
            $this->includes();
            $this->init_hooks();
        }

        private function define_constants(){
            $this->define('WORLDMART_TOOLKIT_VERSION', $this->version);
            $this->define('WORLDMART_TOOLKIT_ABSPATH', dirname(WORLDMART_TOOLKIT_PLUGIN_FILE) . '/');
            $wm_options = get_option('worldmart');
            if ( ! empty( $wm_options ) && is_array( $wm_options ) ) {
                $this->define( 'WORLDMART_OPTIONS' , $wm_options );
            }
        }

        private function define($name, $value){
            if (!defined($name)) {
                define($name, $value);
            }
        }

        private function init_hooks(){
            add_action('init', array($this, 'load_plugin_textdomain'));
            add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
            add_filter('ovic_import_key_redux_options', function(){ return 'worldmart'; } , 1 );
            add_action('wp_head', function (){ echo '<link rel="stylesheet" href="' . WORLDMART_TOOLKIT_URL . '/assets/css/custom.css' . '">'; }, 99999);
        }

        public function load_plugin_textdomain(){
            load_plugin_textdomain('worldmart-toolkit', false, plugin_basename(dirname(WORLDMART_TOOLKIT_PLUGIN_FILE)) . '/languages');
        }

        public function enqueue_scripts(){
            wp_enqueue_script('jquery');
            wp_enqueue_script('jquery-ui-datepicker');
            wp_enqueue_style('jquery-ui-datepicker');
            wp_enqueue_style('type-admin', WORLDMART_TOOLKIT_URL . '/assets/css/admin-redux.css');
        }

        public function includes(){
            include_once WORLDMART_TOOLKIT_PATH . 'includes/classes/wellcome.php';
            include_once WORLDMART_TOOLKIT_PATH . 'includes/classes/mailchimp/MCAPI.class.php';
            include_once WORLDMART_TOOLKIT_PATH . 'includes/classes/mailchimp/mailchimp-settings.php';
            include_once WORLDMART_TOOLKIT_PATH . 'includes/classes/mailchimp/mailchimp.php';
            include_once WORLDMART_TOOLKIT_PATH . 'includes/shortcode.php';
            include_once WORLDMART_TOOLKIT_PATH . 'includes/post-types.php';
            include_once WORLDMART_TOOLKIT_PATH . 'includes/vc_templates.php';
            include_once WORLDMART_TOOLKIT_PATH . 'includes/widgets/widget-latest-posts.php';
            include_once WORLDMART_TOOLKIT_PATH . 'includes/widgets/quote-box-widget/widget-quote-box.php';
            include_once WORLDMART_TOOLKIT_PATH . 'includes/meta-box/meta-box.php';
            include_once WORLDMART_TOOLKIT_PATH . 'includes/live-search/live-search.php';
            include_once WORLDMART_TOOLKIT_PATH . 'includes/live-search/widget.php';

            if (class_exists('WooCommerce')){
                include_once WORLDMART_TOOLKIT_PATH . 'includes/widgets/widget-woo-layered-nav/widget-woo-layered-nav.php';
                include_once WORLDMART_TOOLKIT_PATH . 'includes/widgets/widget-products.php';
                include_once WORLDMART_TOOLKIT_PATH . 'includes/add-share/add-share.php';
                include_once WORLDMART_TOOLKIT_PATH . 'includes/widgets/widget-icon-box/widget-icon-box.php';
                include_once WORLDMART_TOOLKIT_PATH . 'includes/woo-attributes-swatches/woo-term.php';
                include_once WORLDMART_TOOLKIT_PATH . 'includes/woo-attributes-swatches/woo-product-attribute-meta.php';
                include_once WORLDMART_TOOLKIT_PATH . 'includes/woo-category-gallery/woo-category-gallery.php';
            }
            if( is_admin() ){
                require WORLDMART_TOOLKIT_PATH .'integration/plugin-update-checker.php';
                $version_checking = Puc_v4_Factory::buildUpdateChecker(
                    'https://github.com/Meikuyh/Toolkit',
                    __FILE__,
                    'worldmart-toolkit'
                );
                $version_checking->setAuthentication('41ad7d2414b4faa0b03243283dbf54206e2e201e');
            }
        }
    }
}

if( !function_exists( 'Worldmart_Toolkit')){
    function Worldmart_Toolkit(){
        new Worldmart_Toolkit();
    }
    add_action( 'plugins_loaded', 'Worldmart_Toolkit', 99 );
}