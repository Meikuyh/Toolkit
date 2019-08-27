<?php
if( !class_exists('Worldmart_Wellcome') ){
    class Worldmart_Wellcome{

        public $tabs = array();

        public function __construct() {
            $this->set_tabs();
            /* Add action to enqueue scripts.*/
            add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
            add_action( 'admin_menu', array( $this, 'admin_menu' ),9 );
        }

        public  function admin_menu(){
            if ( current_user_can( 'edit_theme_options' ) ) {
                add_menu_page( 'Worldmart', 'Worldmart', 'manage_options', 'worldmart', array( $this, 'wellcome' ),WORLDMART_TOOLKIT_URL . '/assets/images/icon-menu.png', 2 );
                add_submenu_page( 'worldmart', 'Worldmart Dashboard', 'Dashboard', 'manage_options', 'worldmart',  array( $this, 'wellcome' ) );
            }
        }

        public  function  enqueue_scripts(){
            if( defined('WORLDMART_THEME_URI' ) ){
                wp_enqueue_style( 'chosen' );
                wp_enqueue_script( 'chosen');
            }else{
                wp_enqueue_style( 'chosen', WORLDMART_TOOLKIT_URL . '/assets/css/chosen.min.css', array(),'1.0.0');
                wp_enqueue_script( 'chosen', WORLDMART_TOOLKIT_URL . '/assets/js/chosen.min.js', array( 'jquery' ), '1.0.0', true );
            }
            wp_enqueue_style( 'worldmart-admin', WORLDMART_TOOLKIT_URL . '/assets/css/admin.css', array(), WORLDMART_TOOLKIT_VERSION );
            wp_enqueue_script( 'worldmart-admin', WORLDMART_TOOLKIT_URL . '/assets/js/admin.js', array( 'jquery' ), WORLDMART_TOOLKIT_VERSION, true );
        }

        public function set_tabs(){
            $this->tabs = array(
                'demos' => esc_html__('Sample Data','worldmart-toolkit'),
                'plugins' => esc_html__('Plugins','worldmart-toolkit'),
                'support' => esc_html__('Support','worldmart-toolkit')
            );

        }
        public  function active_plugin(){
            if (empty($_GET['magic_token']) || wp_verify_nonce($_GET['magic_token'], 'panel-plugins') === false) {
                esc_html_e('Permission denied','worldmart-toolkit');
                die;
            }

            if( isset($_GET['plugin_slug']) && $_GET['plugin_slug']!=""){
                $plugin_slug = $_GET['plugin_slug'];
                $plugins = TGM_Plugin_Activation::$instance->plugins;
                foreach ($plugins as $plugin) {
                    if ($plugin['slug'] == $plugin_slug) {
                        activate_plugins($plugin['file_path']);
                        ?>
                        <script type="text/javascript">
                            window.location = "admin.php?page=worldmart&tab=plugins";
                        </script>
                        <?php
                        break;
                    }
                }
            }

        }

        public function deactivate_plugin(){
            if (empty($_GET['magic_token']) || wp_verify_nonce($_GET['magic_token'], 'panel-plugins') === false) {
                esc_html_e('Permission denied','worldmart-toolkit');
                die;
            }

            if( isset($_GET['plugin_slug']) && $_GET['plugin_slug']!=""){
                $plugin_slug = $_GET['plugin_slug'];
                $plugins = TGM_Plugin_Activation::$instance->plugins;
                foreach ($plugins as $plugin) {
                    if ($plugin['slug'] == $plugin_slug) {
                        deactivate_plugins($plugin['file_path']);
                        ?>
                        <script type="text/javascript">
                            window.location = "admin.php?page=worldmart&tab=plugins";
                        </script>
                        <?php
                        break;
                    }
                }
            }

        }
        public  function intall_plugin(){

        }
        /**
         * Render HTML of intro tab.
         *
         * @return  string
         */

        public function wellcome(){

            /* deactivate_plugin */
            if( isset($_GET['action']) && $_GET['action'] == 'deactivate_plugin'){
                $this->deactivate_plugin();
            }
            /* deactivate_plugin */
            if( isset($_GET['action']) && $_GET['action'] == 'active_plugin'){
                $this->active_plugin();
            }

            $tab = 'demos';
            if( isset($_GET['tab'])){
                $tab = $_GET['tab'];
            }
            ?>
            <div class="wrap kuthemes-wrap">
                <div class="welcome-panel">
                    <div class="welcome-panel-content">
                        <h2><?php esc_html_e('Welcome to Worldmart!','worldmart-toolkit');?></h2>
                        <p class="about-description"><?php esc_html_e('We\'ve assembled some links to get you started','worldmart-toolkit');?></p>
                        <div class="welcome-panel-column-container">
                            <div class="welcome-panel-column">
                                <h3><?php esc_html_e('Get Started','worldmart-toolkit');?></h3>
                                <a target="_blank" href="https://worldmart.kutethemes.net" class="button button-primary button-hero trigger-tab"><?php esc_html_e('View All Demos','worldmart-toolkit');?></a>
                            </div>
                            <div class="welcome-panel-column">
                                <h3><?php esc_html_e('Next Steps','worldmart-toolkit');?></h3>
                                <ul>
                                    <li><a target="_blank" href="#" class="welcome-icon dashicons-media-document"><?php esc_html_e('Read Documentation','worldmart-toolkit')?></a></li>
                                    <li><a target="_blank" href="https://support.kutethemes.net/support-system" class="welcome-icon dashicons-editor-help"><?php esc_html_e('Request Support','worldmart-toolkit');?></a></li>
                                    <li><a target="_blank" href="https://worldmart.kutethemes.net/changelog.txt" class="welcome-icon dashicons-backup"><?php esc_html_e('View Changelog Details','worldmart-toolkit');?></a></li>
                                </ul>
                            </div>
                            <div class="welcome-panel-column">
                                <h3><?php esc_html_e('Keep in Touch','worldmart-toolkit');?></h3>
                                <ul>
                                    <li><a target="_blank" href="#" class="welcome-icon dashicons-email-alt"><?php esc_html_e('Newsletter','worldmart-toolkit');?></a></li>
                                    <li><a target="_blank" href="#" class="welcome-icon dashicons-twitter"><?php esc_html_e('Twitter','worldmart-toolkit');?></a></li>
                                    <li><a target="_blank" href="https://www.facebook.com/kutethemes" class="welcome-icon dashicons-facebook"><?php esc_html_e('Facebook','worldmart-toolkit');?></a></li>
                                </ul>
                            </div>
                        </div>
                    </div><!-- .welcome-panel-content -->
                </div>
                <div id="tabs-container" role="tabpanel">
                    <div class="nav-tab-wrapper">
                        <?php foreach ($this->tabs as $key => $value ):?>
                            <a class="nav-tab worldmart-nav <?php if( $tab == $key ):?> active<?php endif;?>" href="admin.php?page=worldmart&tab=<?php echo esc_attr($key);?>"><?php echo esc_html($value);?></a>
                        <?php endforeach;?>
                    </div>
                    <div class="tab-content">
                        <?php $this->$tab();?>
                    </div>
                </div>
            </div>
            <?php
        }
        public static function demos(){
            if( class_exists('WORLDMART_IMPORTER')){
                $worldmart_importer = new WORLDMART_IMPORTER();
                $worldmart_importer->importer_page_content();
            }
        }
        public static function plugins(){
            $worldmart_tgm_theme_plugins = TGM_Plugin_Activation::$instance->plugins;
            $tgm =   TGM_Plugin_Activation::$instance;

            $status_class = "";
            ?>
            <div class="plugins rp-row">
                <?php
                $wp_plugin_list = get_plugins();
                foreach ($worldmart_tgm_theme_plugins as $worldmart_tgm_theme_plugin ){
                    if( $tgm->is_plugin_active($worldmart_tgm_theme_plugin['slug'])){
                        $status_class = 'is-active';
                        if( $tgm->does_plugin_have_update($worldmart_tgm_theme_plugin['slug'])){
                            $status_class = 'plugin-update';
                        }
                    }else if (isset($wp_plugin_list[$worldmart_tgm_theme_plugin['file_path']])) {
                        $status_class = 'plugin-inactive';
                    }else{
                        $status_class ='no-intall';
                    }
                    ?>
                    <div class="rp-col">
                        <div class="plugin <?php echo esc_attr($status_class);?>">
                            <div class="preview">
                                <?php if( isset($worldmart_tgm_theme_plugin['image']) && $worldmart_tgm_theme_plugin['image'] != "" ):?>
                                    <img src="<?php echo esc_url($worldmart_tgm_theme_plugin['image']);?>" alt="">
                                <?php else:?>
                                    <img src="<?php echo esc_url(get_template_directory_uri().'/framework/assets/images/no-image.jpg');?>" alt="">
                                <?php endif;?>
                            </div>
                            <div class="plugin-name">
                                <h3 class="theme-name"><?php echo $worldmart_tgm_theme_plugin['name'] ?></h3>
                            </div>
                            <div class="actions">
                                <a class="button button-primary button-install-plugin" href="<?php
                                echo esc_url( wp_nonce_url(
                                    add_query_arg(
                                        array(
                                            'page'		  	=> urlencode(TGM_Plugin_Activation::$instance->menu),
                                            'plugin'		=> urlencode($worldmart_tgm_theme_plugin['slug']),
                                            'tgmpa-install' => 'install-plugin',
                                        ),
                                        admin_url('themes.php')
                                    ),
                                    'tgmpa-install',
                                    'tgmpa-nonce'
                                ));
                                ?>"><?php esc_html_e('Install','worldmart-toolkit');?></a>

                                <a class="button button-primary button-update-plugin" href="<?php
                                echo esc_url( wp_nonce_url(
                                    add_query_arg(
                                        array(
                                            'page'		  	=> urlencode(TGM_Plugin_Activation::$instance->menu),
                                            'plugin'		=> urlencode($worldmart_tgm_theme_plugin['slug']),
                                            'tgmpa-update' => 'update-plugin',
                                        ),
                                        admin_url('themes.php')
                                    ),
                                    'tgmpa-install',
                                    'tgmpa-nonce'
                                ));
                                ?>"><?php esc_html_e('Update','worldmart-toolkit');?></a>

                                <a class="button button-primary button-activate-plugin" href="<?php
                                echo esc_url(
                                    add_query_arg(
                                        array(
                                            'page'                   => urlencode('worldmart'),
                                            'plugin_slug' => urlencode($worldmart_tgm_theme_plugin['slug']),
                                            'action'                 => 'active_plugin',
                                            'magic_token'         => wp_create_nonce('panel-plugins')
                                        ),
                                        admin_url('admin.php')
                                    ));
                                ?>""><?php esc_html_e('Activate','worldmart-toolkit');?></a>
                                <a class="button button-secondary button-uninstall-plugin" href="<?php
                                echo esc_url(
                                    add_query_arg(
                                        array(
                                            'page'                   => urlencode('worldmart'),
                                            'plugin_slug' => urlencode($worldmart_tgm_theme_plugin['slug']),
                                            'action'                 => 'deactivate_plugin',
                                            'magic_token'         => wp_create_nonce('panel-plugins')
                                        ),
                                        admin_url('admin.php')
                                    ));
                                ?>""><?php esc_html_e('Deactivate','worldmart-toolkit');?></a>
                            </div>
                        </div>
                    </div>
                    <?php
                }
                ?>
            </div>
            <?php
        }
        public static function support(){
            ?>
            <div class="rp-row">
                <div class="rp-col">
                    <div class="suport-item">
                        <h3><?php esc_html_e('Documentation','worldmart-toolkit');?></h3>
                        <p><?php esc_html_e('Here is our user guide for Worldmart, including basic setup steps, as well as Worldmart features and elements for your reference.','worldmart-toolkit');?></p>
                        <a target="_blank" href="#" class="button button-primary"><?php esc_html_e('Read Documentation','worldmart-toolkit');?></a>
                    </div>
                </div>
                <div class="rp-col closed">
                    <div class="suport-item">
                        <h3><?php esc_html_e('Video Tutorials','worldmart-toolkit');?></h3>
                        <p class="coming-soon"><?php esc_html_e('Video tutorials is the great way to show you how to setup Worldmart theme, make sure that the feature works as it\'s designed.','worldmart-toolkit');?></p>
                        <a href="#" class="button button-primary disabled"><?php esc_html_e('See Video','worldmart-toolkit');?></a>
                    </div>
                </div>
                <div class="rp-col">
                    <div class="suport-item">
                        <h3><?php esc_html_e('Forum','worldmart-toolkit');?></h3>
                        <p><?php esc_html_e('Can\'t find the solution on documentation? We\'re here to help, even on weekend. Just click here to start 1on1 chatting with us!','worldmart-toolkit');?></p>
                        <a target="_blank" href="https://support.kutethemes.net/support-system" class="button button-primary"><?php esc_html_e('Request Support','worldmart-toolkit');?></a>
                    </div>
                </div>
            </div>

            <?php
        }
    }

    new Worldmart_Wellcome();
}
