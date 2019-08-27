<?php

if( !class_exists('Worldmart_Mailchimp') ){
	class Worldmart_Mailchimp{
		public $plugin_uri;
		private $options;

		public function __construct() {
			$this->options = get_option( 'worldmart_mailchimp_option' );

			$this->plugin_uri = trailingslashit(plugin_dir_url(__FILE__) );
			add_action( 'wp_enqueue_scripts',  array(&$this,'scripts') );

			add_action( 'wp_ajax_submit_mailchimp_via_ajax', array($this,'submit_mailchimp_via_ajax') );
			add_action( 'wp_ajax_nopriv_submit_mailchimp_via_ajax', array($this,'submit_mailchimp_via_ajax') );

			if ( !$this->options['api_key'] ) {
	            add_action( 'admin_notices', array( $this, 'admin_notice' ));
	        }
		}


		function admin_notice() {
        ?>
        <div class="updated">
            <p><?php 
                printf( 
                    __('Please enter Mail Chimp API Key in <a href="%s">here</a>', 'worldmart-toolkit' ),
                    admin_url( 'admin.php?page=mailchimp-settings')
                ); 
            ?></p>
        </div>
        <?php
	    }

		public function scripts(){
			wp_enqueue_script( 'worldmart-mailchimp', WORLDMART_TOOLKIT_URL. '/includes/classes/mailchimp/js/mailchimp.min.js', array( 'jquery' ), '1.0', true );
			wp_localize_script( 'worldmart-mailchimp', 'worldmart_mailchimp', array(
				'ajaxurl'  => admin_url('admin-ajax.php'),
				'security' => wp_create_nonce( 'worldmart_mailchimp' ),
	        ) );
		}

		public function submit_mailchimp_via_ajax() {
			if ( !class_exists( 'MCAPI' ) ) {
				include_once( 'MCAPI.class.php' );
			}
			$response        = array(
				'html'    => '',
				'message' => '',
				'success' => 'no',
			);
			$email           = isset( $_POST['email'] ) ? $_POST['email'] : '';
			$list_id         = isset( $_POST['list_id'] ) ? $_POST['list_id'] : '';
			$fname           = isset( $_POST['fname'] ) ? $_POST['fname'] : '';
			$lname           = isset( $_POST['lname'] ) ? $_POST['lname'] : '';
			$api_key         = "";
			$success_message = esc_html__( 'Your email added...', 'ovic-toolkit' );
			if ( $this->options ) {
				$api_key = isset( $this->options['api_key'] ) ? $this->options['api_key'] : '';
				$list_id = ($list_id =='' ) ? $this->options['list'] : $list_id;
				if ( isset( $this->options['success_message'] ) && $this->options['success_message'] != "" ) {
					$success_message = $this->options['success_message'];
				}
			}
			$response['message'] = esc_html__( 'Failed', 'ovic-toolkit' );
			$response['list_id'] = $list_id;
			$merge_vars          = array(
				'FNAME' => $fname,
				'LNAME' => $lname,
			);
			if ( class_exists( 'MCAPI' ) ) {
				$api = new MCAPI( $api_key );
				if ( $api->subscribe( $list_id, $email, $merge_vars ) === true ) {
					$response['message'] = sanitize_text_field( $success_message );
					$response['success'] = 'yes';
				} else {
					// Sending failed
					$response['message'] = $api->get_error_message();
				}
			}
			wp_send_json( $response );
			die();
		}
	}
}
new Worldmart_Mailchimp();
