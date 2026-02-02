<?php //@phpcs:ignore WordPress.Files.FileName.InvalidClassFileName -- can not change main file name for existing plugin
/**
 * Plugin Name: Login with Cognito
 * Plugin URI: miniorange-login-with-cognito
 * Description: WordPress Login with Cognito plugin allows Login(Single Sign-On) to WordPress using AWS Cognito account credentials. You can SSO(Single Sign-on) to your WordPress site with Cognito using this plugin. This plugin uses OAuth protocol to achieve Single Sign-on.
 * Version: 1.5.3
 * Author: miniOrange
 * Author URI: https://www.miniorange.com
 * License: Expat
 * License URI: https://plugins.miniorange.com/mit-license
 */

require 'handler/class-mocognito-oauth-handler.php';
require_once dirname( __FILE__ ) . '/class-mocognito-widget.php';
require 'class-mocognito-customer.php';
require plugin_dir_path( __FILE__ ) . 'includes/class-mocognito-oauth-client.php';
require 'views/feedback-form.php';
require 'constants.php';

/**
 * MOCognito OAuth class
 */
class MoCognito_OAuth {

	/**
	 * Constructor
	 */
	public function __construct() {

		add_action( 'admin_init', array( $this, 'miniorange_oauth_save_settings' ) );
		add_action( 'plugins_loaded', array( $this, 'mo_login_widget_text_domain' ) );
		register_deactivation_hook( __FILE__, array( $this, 'mo_oauth_deactivate' ) );
		remove_action( 'admin_notices', array( $this, 'mo_oauth_success_message' ) );
		remove_action( 'admin_notices', array( $this, 'mo_oauth_error_message' ) );
		add_shortcode( 'mo_oauth_login', array( $this, 'mo_oauth_shortcode_login' ) );
		add_action( 'admin_footer', array( $this, 'mo_oauth_client_feedback_request' ) );

	}

	/**
	 * Success Message
	 */
	public function mo_oauth_success_message() {
		$class   = 'error';
		$message = get_option( 'message' );
		echo "<div class='" . esc_attr( $class ) . "'> <p>" . esc_attr( $message ) . '</p></div>';
	}

	/**
	 * Feedback Request
	 */
	public function mo_oauth_client_feedback_request() {
		mocognito_display_feedback_form();
	}

	/**
	 * Error message
	 */
	public function mo_oauth_error_message() {
		$class   = 'updated';
		$message = get_option( 'message' );
		echo "<div class='" . esc_attr( $class ) . "'><p>" . esc_attr( $message ) . '</p></div>';
	}

	/**
	 * Deactivate
	 */
	public function mo_oauth_deactivate() {
		delete_option( 'host_name' );
		delete_option( 'new_registration' );
		delete_option( 'mo_oauth_admin_phone' );
		delete_option( 'verify_customer' );
		delete_option( 'mo_oauth_admin_customer_key' );
		delete_option( 'mo_oauth_admin_api_key' );
		delete_option( 'mo_oauth_new_customer' );
		delete_option( 'customer_token' );
		delete_option( 'message' );
		delete_option( 'mo_oauth_registration_status' );
		delete_option( 'mo_oauth_client_show_mo_server_message' );
	}

	/**
	 * Login Widget text domain
	 */
	public function mo_login_widget_text_domain() {
		load_plugin_textdomain( 'flw', false, basename( dirname( __FILE__ ) ) . '/languages' );
	}

	/**
	 * Show Success Message
	 */
	private function mo_oauth_show_success_message() {
		remove_action( 'admin_notices', array( $this, 'mo_oauth_success_message' ) );
		add_action( 'admin_notices', array( $this, 'mo_oauth_error_message' ) );
	}

	/**
	 * Show Error Message
	 */
	private function mo_oauth_show_error_message() {
		remove_action( 'admin_notices', array( $this, 'mo_oauth_error_message' ) );
		add_action( 'admin_notices', array( $this, 'mo_oauth_success_message' ) );
	}

	/**
	 * Check Empty or Null
	 *
	 * @param Value $value for value.
	 */
	public function mo_oauth_check_empty_or_null( $value ) {
		if ( ! isset( $value ) || empty( $value ) ) {
			return true;
		}
		return false;
	}

	/**
	 * Save settings
	 */
	public function miniorange_oauth_save_settings() {

		if ( isset( $_POST['option'] ) && 'mo_oauth_client_mo_server_message' === sanitize_text_field( wp_unslash( $_POST['option'] ) ) && isset( $_REQUEST['mo_oauth_mo_server_message_form_field'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['mo_oauth_mo_server_message_form_field'] ) ), 'mo_oauth_mo_server_message_form' ) ) {
			update_option( 'mo_oauth_client_show_mo_server_message', 1 );
			return;
		}

		if ( isset( $_POST['option'] ) && 'clear_pointers' === sanitize_text_field( wp_unslash( $_POST['option'] ) ) && isset( $_REQUEST['mo_oauth_clear_pointers_form_field'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['mo_oauth_clear_pointers_form_field'] ) ), 'mo_oauth_clear_pointers_form' ) ) {
			update_user_meta( get_current_user_id(), 'dismissed_wp_pointers', '' );
			return;
		}

		if ( isset( $_POST['option'] ) && 'change_miniorange' === sanitize_text_field( wp_unslash( $_POST['option'] ) ) && isset( $_REQUEST['mo_oauth_goto_login_form_field'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['mo_oauth_goto_login_form_field'] ) ), 'mo_oauth_goto_login_form' ) ) {

			if ( current_user_can( 'administrator' ) ) {
				$this->mo_oauth_deactivate();
				return;
			}
		}

		if ( isset( $_POST['option'] ) && 'mo_oauth_register_customer' === sanitize_text_field( wp_unslash( $_POST['option'] ) ) && isset( $_REQUEST['mo_oauth_register_form_field'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['mo_oauth_register_form_field'] ) ), 'mo_oauth_register_form' ) ) {

			if ( current_user_can( 'administrator' ) ) {
				$email            = '';
				$phone            = '';
				$password         = '';
				$confirm_password = '';
				$fname            = '';
				$lname            = '';
				$company          = '';
				if ( $this->mo_oauth_check_empty_or_null( sanitize_text_field( wp_unslash( isset( $_POST['email'] ) ? $_POST['email'] : '' ) ) ) || $this->mo_oauth_check_empty_or_null( sanitize_text_field( wp_unslash( isset( $_POST['password'] ) ? $_POST['password'] : '' ) ) ) || $this->mo_oauth_check_empty_or_null( sanitize_text_field( wp_unslash( isset( $_POST['confirmPassword'] ) ? $_POST['confirmPassword'] : '' ) ) ) ) {
					update_option( 'message', 'All the fields are required. Please enter valid entries.' );
					$this->mo_oauth_show_error_message();
					return;
				} elseif ( strlen( $_POST['password'] ) < 8 || strlen( $_POST['confirmPassword'] ) < 8 ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.ValidatedSanitizedInput.MissingUnslash -- can not sanitize passwords
					update_option( 'message', 'Choose a password with minimum length 8.' );
					$this->mo_oauth_show_error_message();
					return;
				} else {
					$email            = sanitize_email( wp_unslash( $_POST['email'] ) );
					$phone            = stripslashes( sanitize_text_field( wp_unslash( isset( $_POST['phone'] ) ? $_POST['phone'] : '' ) ) );
					$password         = stripslashes( $_POST['password'] ); // @phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.ValidatedSanitizedInput.MissingUnslash -- can not sanitize passwords
					$confirm_password = stripslashes( $_POST['confirmPassword'] );  // @phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.ValidatedSanitizedInput.MissingUnslash -- can not sanitize passwords
					$fname            = stripslashes( sanitize_text_field( wp_unslash( isset( $_POST['fname'] ) ? $_POST['fname'] : '' ) ) );
					$lname            = stripslashes( sanitize_text_field( wp_unslash( isset( $_POST['lname'] ) ? $_POST['lname'] : '' ) ) );
					$company          = stripslashes( sanitize_text_field( wp_unslash( isset( $_POST['company'] ) ? $_POST['company'] : '' ) ) );
				}

				update_option( 'mo_oauth_admin_email', $email );
				update_option( 'mo_oauth_admin_phone', $phone );
				update_option( 'mo_oauth_admin_fname', $fname );
				update_option( 'mo_oauth_admin_lname', $lname );
				update_option( 'mo_oauth_admin_company', $company );

				if ( 0 === mocognito_is_curl_installed() ) {
					return $this->mo_oauth_show_curl_error();
				}

				if ( 0 === strcmp( $password, $confirm_password ) ) {
					update_option( 'password', $password );
					$customer = new MoCognito_Customer();
					$email    = get_option( 'mo_oauth_admin_email' );
					$content  = json_decode( $customer->check_customer(), true );
					if ( 0 === strcasecmp( $content['status'], 'CUSTOMER_NOT_FOUND' ) ) {
						$response = json_decode( $customer->create_customer(), true );
						if ( 0 === strcasecmp( $response['status'], 'SUCCESS' ) ) {
							$this->mo_oauth_get_current_customer();
							wp_safe_redirect( admin_url( '/admin.php?page=mo_oauth_settings&tab=licensing' ), 301 );
							exit;
						} else {
							update_option( 'message', 'Failed to create customer. Try again.' );
						}
						$this->mo_oauth_show_success_message();
					} else {
						$this->mo_oauth_get_current_customer();
					}
				} else {
					update_option( 'message', 'Passwords do not match.' );
					delete_option( 'verify_customer' );
					$this->mo_oauth_show_error_message();
				}
			}
		}

		if ( isset( $_POST['option'] ) && 'mo_oauth_client_goto_login' === sanitize_text_field( wp_unslash( $_POST['option'] ) ) && isset( $_REQUEST['mo_oauth_goto_login_form_field'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['mo_oauth_goto_login_form_field'] ) ), 'mo_oauth_goto_login_form' ) ) {

			delete_option( 'new_registration' );
			update_option( 'verify_customer', 'true' );
		}

		if ( isset( $_POST['option'] ) && 'mo_oauth_verify_customer' === sanitize_text_field( wp_unslash( $_POST['option'] ) ) && isset( $_REQUEST['mo_oauth_verify_password_form_field'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['mo_oauth_verify_password_form_field'] ) ), 'mo_oauth_verify_password_form' ) ) {

			if ( current_user_can( 'administrator' ) ) {
				if ( 0 === mocognito_is_curl_installed() ) {
					return $this->mo_oauth_show_curl_error();
				}
				$email    = '';
				$password = '';
				if ( $this->mo_oauth_check_empty_or_null( sanitize_text_field( wp_unslash( $_POST['email'] ) ) ) || $this->mo_oauth_check_empty_or_null( sanitize_text_field( wp_unslash( $_POST['password'] ) ) ) ) {
					update_option( 'message', 'All the fields are required. Please enter valid entries.' );
					$this->mo_oauth_show_error_message();
					return;
				} else {
					$email    = sanitize_email( wp_unslash( $_POST['email'] ) );
					$password = stripslashes( $_POST['password'] ); // @phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.ValidatedSanitizedInput.MissingUnslash -- can not sanitize passwords
				}

				update_option( 'mo_oauth_admin_email', $email );
				update_option( 'password', $password );
				$customer     = new MoCognito_Customer();
				$content      = $customer->get_customer_key();
				$customer_key = json_decode( $content, true );
				if ( json_last_error() === JSON_ERROR_NONE ) {
					update_option( 'mo_oauth_admin_customer_key', $customer_key['id'] );
					update_option( 'mo_oauth_admin_api_key', $customer_key['apiKey'] );
					update_option( 'customer_token', $customer_key['token'] );
					if ( isset( $customer_key['phone'] ) ) {
						update_option( 'mo_oauth_admin_phone', $customer_key['phone'] );
					}
					delete_option( 'password' );
					update_option( 'message', 'Customer retrieved successfully' );
					delete_option( 'verify_customer' );
					delete_option( 'new_registration' );
					$this->mo_oauth_show_success_message();
				} else {
					update_option( 'message', 'Invalid username or password. Please try again.' );
					$this->mo_oauth_show_error_message();
				}
			}
		} elseif ( isset( $_POST['option'] ) && 'mo_oauth_add_app' === sanitize_text_field( wp_unslash( $_POST['option'] ) ) && isset( $_REQUEST['mo_oauth_add_app_form_field'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['mo_oauth_add_app_form_field'] ) ), 'mo_oauth_add_app_form' ) ) {
			if ( current_user_can( 'administrator' ) ) {
				$scope        = '';
				$clientid     = '';
				$clientsecret = '';
				if ( $this->mo_oauth_check_empty_or_null( sanitize_text_field( wp_unslash( isset( $_POST['mo_oauth_client_id'] ) ? $_POST['mo_oauth_client_id'] : '' ) ) ) || $this->mo_oauth_check_empty_or_null( sanitize_text_field( wp_unslash( isset( $_POST['mo_oauth_client_secret'] ) ? $_POST['mo_oauth_client_secret'] : '' ) ) ) ) {
					update_option( 'message', 'Please enter valid Client ID and Client Secret.' );
					$this->mo_oauth_show_error_message();
					return;
				} else {
					$scope              = stripslashes( sanitize_text_field( wp_unslash( isset( $_POST['mo_oauth_scope'] ) ? $_POST['mo_oauth_scope'] : '' ) ) );
					$clientid           = stripslashes( trim( sanitize_text_field( wp_unslash( $_POST['mo_oauth_client_id'] ) ) ) );
					$clientsecret       = stripslashes( trim( $_POST['mo_oauth_client_secret'] ) ); // @phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.ValidatedSanitizedInput.MissingUnslash -- can not sanitize secrets containing special chars
					$appname            = rtrim( stripslashes( sanitize_text_field( wp_unslash( isset( $_POST['mo_oauth_custom_app_name'] ) ? $_POST['mo_oauth_custom_app_name'] : '' ) ) ), ' ' );
					$ssoprotocol        = stripslashes( sanitize_text_field( wp_unslash( isset( $_POST['mo_oauth_app_type'] ) ? $_POST['mo_oauth_app_type'] : '' ) ) );
					$selectedapp        = stripslashes( sanitize_text_field( wp_unslash( isset( $_POST['mo_oauth_app_name'] ) ? $_POST['mo_oauth_app_name'] : '' ) ) );
					$send_headers       = isset( $_POST['mo_oauth_authorization_header'] ) ? sanitize_text_field( wp_unslash( $_POST['mo_oauth_authorization_header'] ) ) : '0';
					$send_body          = isset( $_POST['mo_oauth_body'] ) ? sanitize_text_field( wp_unslash( $_POST['mo_oauth_body'] ) ) : '0';
					$show_on_login_page = isset( $_POST['mo_oauth_show_on_login_page'] ) ? (int) filter_var( sanitize_text_field( wp_unslash( $_POST['mo_oauth_show_on_login_page'] ) ), FILTER_SANITIZE_NUMBER_INT ) : 0;
					if ( 'wso2' === $selectedapp ) {
						update_option( 'mo_oauth_client_custom_token_endpoint_no_csecret', true );
					}

					if ( get_option( 'mo_oauth_apps_list' ) ) {
						$appslist = get_option( 'mo_oauth_apps_list' );
					} else {
						$appslist = array();
					}

					$email_attr = '';
					$name_attr  = '';
					$newapp     = array();

					$isupdate = false;
					foreach ( $appslist as $key => $currentapp ) {
						if ( $appname === $key ) {
							$newapp   = $currentapp;
							$isupdate = true;
							break;
						}
					}

					if ( ! $isupdate && count( $appslist ) > 0 ) {
						update_option( 'message', 'You can only add 1 application with free version. Upgrade to enterprise version if you want to add more applications.' );
						$this->mo_oauth_show_error_message();
						return;
					}

					$newapp['clientid']           = $clientid;
					$newapp['clientsecret']       = $clientsecret;
					$newapp['scope']              = $scope;
					$newapp['redirecturi']        = site_url();
					$newapp['ssoprotocol']        = $ssoprotocol;
					$newapp['send_headers']       = $send_headers;
					$newapp['send_body']          = $send_body;
					$newapp['show_on_login_page'] = $show_on_login_page;

					$authorizeurl   = stripslashes( sanitize_text_field( wp_unslash( isset( $_POST['mo_oauth_authorizeurl'] ) ? $_POST['mo_oauth_authorizeurl'] : '' ) ) );
					$accesstokenurl = stripslashes( sanitize_text_field( wp_unslash( isset( $_POST['mo_oauth_accesstokenurl'] ) ? $_POST['mo_oauth_accesstokenurl'] : '' ) ) );
					$appname        = rtrim( stripslashes( sanitize_text_field( wp_unslash( isset( $_POST['mo_oauth_custom_app_name'] ) ? $_POST['mo_oauth_custom_app_name'] : '' ) ) ), ' ' );

					$newapp['authorizeurl']   = $authorizeurl;
					$newapp['accesstokenurl'] = $accesstokenurl;
					if ( isset( $_POST['mo_oauth_app_type'] ) ) {
						$newapp['apptype'] = stripslashes( sanitize_text_field( wp_unslash( $_POST['mo_oauth_app_type'] ) ) );
					} else {
						$newapp['apptype'] = stripslashes( 'oauth' );
					}

					if ( 'oauth' === $newapp['apptype'] || isset( $_POST['mo_oauth_resourceownerdetailsurl'] ) ) {
						$resourceownerdetailsurl = stripslashes( sanitize_text_field( wp_unslash( $_POST['mo_oauth_resourceownerdetailsurl'] ) ) );
						if ( '' !== $resourceownerdetailsurl ) {
							$newapp['resourceownerdetailsurl'] = $resourceownerdetailsurl;
						}
					}
					if ( isset( $_POST['mo_oauth_app_name'] ) ) {
						$newapp['appId'] = stripslashes( sanitize_text_field( wp_unslash( $_POST['mo_oauth_app_name'] ) ) );
					}
					$appslist[ $appname ] = $newapp;
					update_option( 'mo_oauth_apps_list', $appslist );
					wp_safe_redirect( 'admin.php?page=mo_oauth_settings&tab=config&action=update&app=' . rawurlencode( $appname ) );
				}
			}
		} elseif ( isset( $_POST['option'] ) && 'mo_oauth_app_customization' === sanitize_text_field( wp_unslash( $_POST['option'] ) ) && isset( $_REQUEST['mo_oauth_app_customization_form_field'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['mo_oauth_app_customization_form_field'] ) ), 'mo_oauth_app_customization_form' ) ) {

			if ( current_user_can( 'administrator' ) ) {
				update_option( 'mo_oauth_icon_width', stripslashes( sanitize_text_field( wp_unslash( isset( $_POST['mo_oauth_icon_width'] ) ? $_POST['mo_oauth_icon_width'] : '' ) ) ) );
				update_option( 'mo_oauth_icon_height', stripslashes( sanitize_text_field( wp_unslash( isset( $_POST['mo_oauth_icon_height'] ) ? $_POST['mo_oauth_icon_height'] : '' ) ) ) );
				update_option( 'mo_oauth_icon_margin', stripslashes( sanitize_text_field( wp_unslash( isset( $_POST['mo_oauth_icon_margin'] ) ? $_POST['mo_oauth_icon_margin'] : '' ) ) ) );
				update_option( 'mo_oauth_icon_configure_css', stripcslashes( sanitize_text_field( wp_unslash( isset( $_POST['mo_oauth_icon_configure_css'] ) ? $_POST['mo_oauth_icon_configure_css'] : '' ) ) ) );
				update_option( 'message', 'Your settings were saved' );
				$this->mo_oauth_show_success_message();
			}
		} elseif ( isset( $_POST['option'] ) && 'mo_oauth_attribute_mapping' === sanitize_text_field( wp_unslash( $_POST['option'] ) ) && isset( $_REQUEST['mo_oauth_attr_role_mapping_form_field'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['mo_oauth_attr_role_mapping_form_field'] ) ), 'mo_oauth_attr_role_mapping_form' ) ) {

			if ( current_user_can( 'administrator' ) ) {
				$appname       = stripslashes( sanitize_text_field( wp_unslash( $_POST['mo_oauth_app_name'] ) ) );
				$username_attr = stripslashes( sanitize_text_field( wp_unslash( isset( $_POST['mo_oauth_username_attr'] ) ? $_POST['mo_oauth_username_attr'] : '' ) ) );
				$attr_option   = stripslashes( sanitize_text_field( wp_unslash( isset( $_POST['mo_attr_option'] ) ? $_POST['mo_attr_option'] : '' ) ) );
				if ( empty( $appname ) ) {
					update_option( 'message', 'You MUST configure an application before you map attributes.' );
					$this->mo_oauth_show_error_message();
					return;
				}
				$appslist = get_option( 'mo_oauth_apps_list' );
				foreach ( $appslist as $key => $currentapp ) {
					if ( $appname === $key ) {
						$currentapp['username_attr'] = $username_attr;
						$appslist[ $key ]            = $currentapp;
						break;
					}
				}

				update_option( 'mo_oauth_apps_list', $appslist );
				update_option( 'message', 'Your settings are saved successfully.' );
				update_option( 'mo_attr_option', $attr_option );
				$this->mo_oauth_show_success_message();
				wp_safe_redirect( 'admin.php?page=mo_oauth_settings&tab=attributemapping' );
			}
		} elseif ( isset( $_POST['option'] ) && 'mo_oauth_contact_us_query_option' === sanitize_text_field( wp_unslash( $_POST['option'] ) ) && isset( $_REQUEST['mo_oauth_support_form_field'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['mo_oauth_support_form_field'] ) ), 'mo_oauth_support_form' ) ) {

			if ( current_user_can( 'administrator' ) ) {
				if ( 0 === mocognito_is_curl_installed() ) {
					return $this->mo_oauth_show_curl_error();
				}
				$email       = sanitize_email( wp_unslash( isset( $_POST['mo_oauth_contact_us_email'] ) ? $_POST['mo_oauth_contact_us_email'] : '' ) );
				$phone       = stripslashes( sanitize_text_field( wp_unslash( isset( $_POST['mo_oauth_contact_us_phone'] ) ? $_POST['mo_oauth_contact_us_phone'] : '' ) ) );
				$query       = stripslashes( sanitize_text_field( wp_unslash( isset( $_POST['mo_oauth_contact_us_query'] ) ? $_POST['mo_oauth_contact_us_query'] : '' ) ) );
				$send_config = isset( $_POST['mo_oauth_send_plugin_config'] ) ? sanitize_text_field( wp_unslash( $_POST['mo_oauth_send_plugin_config'] ) ) : '0';
				$customer    = new MoCognito_Customer();
				if ( $this->mo_oauth_check_empty_or_null( $email ) || $this->mo_oauth_check_empty_or_null( $query ) ) {
					update_option( 'message', 'Please fill up Email and Query fields to submit your query.' );
					$this->mo_oauth_show_error_message();
				} else {
					$submited = $customer->submit_contact_us( $email, $phone, $query, $send_config );
					if ( false === $submited ) {
						update_option( 'message', 'Your query could not be submitted. Please try again.' );
						$this->mo_oauth_show_error_message();
					} else {
						update_option( 'message', 'Thanks for getting in touch! We shall get back to you shortly.' );
						$this->mo_oauth_show_success_message();
					}
				}
			}
		} elseif ( isset( $_POST['option'] ) && 'mo_oauth_client_demo_request_form' === sanitize_text_field( wp_unslash( $_POST['option'] ) ) && isset( $_REQUEST['mo_oauth_client_demo_request_field'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['mo_oauth_client_demo_request_field'] ) ), 'mo_oauth_client_demo_request_form' ) ) {

			if ( current_user_can( 'administrator' ) ) {
				if ( 0 === mocognito_is_curl_installed() ) {
					return $this->mo_oauth_show_curl_error();
				}
				$email     = sanitize_email( wp_unslash( isset( $_POST['mo_auto_create_demosite_email'] ) ? $_POST['mo_auto_create_demosite_email'] : '' ) );
				$demo_plan = stripslashes( sanitize_text_field( wp_unslash( isset( $_POST['mo_auto_create_demosite_demo_plan'] ) ? $_POST['mo_auto_create_demosite_demo_plan'] : '' ) ) );
				$query     = stripslashes( sanitize_text_field( wp_unslash( isset( $_POST['mo_auto_create_demosite_usecase'] ) ? $_POST['mo_auto_create_demosite_usecase'] : '' ) ) );

				if ( $this->mo_oauth_check_empty_or_null( $email ) || $this->mo_oauth_check_empty_or_null( $demo_plan ) || $this->mo_oauth_check_empty_or_null( $query ) ) {
					update_option( 'message', 'Please fill up Usecase, Email field and Requested demo plan to submit your query.' );
					$this->mo_oauth_show_error_message();
				} else {
					$url = 'http://demo.miniorange.com/wordpress-oauth/';

					$headers = array(
						'Content-Type' => 'application/x-www-form-urlencoded',
						'charset'      => 'UTF - 8',
					);
					$args    = array(
						'method'      => 'POST',
						'body'        => array(
							'option' => 'mo_auto_create_demosite',
							'mo_auto_create_demosite_email' => $email,
							'mo_auto_create_demosite_usecase' => $query,
							'mo_auto_create_demosite_demo_plan' => $demo_plan,
							'mo_auto_create_demosite_plugin_name' => MO_OAUTH_PLUGIN_SLUG,
						),
						'timeout'     => '20',
						'redirection' => '5',
						'httpversion' => '1.0',
						'blocking'    => true,
						'headers'     => $headers,

					);

					$response = wp_remote_post( $url, $args );

					if ( is_wp_error( $response ) ) {
						$error_message = $response->get_error_message();
						echo 'Something went wrong: ' . esc_attr( $error_message );
						exit();
					}
					$output = wp_remote_retrieve_body( $response );
					$output = json_decode( $output );

					if ( is_null( $output ) ) {
						update_option( 'message', 'Something went wrong! contact to your administrator' );
						$this->mo_oauth_show_success_message();
					}

					if ( 'SUCCESS' === $output->status ) {
						update_option( 'message', $output->message );
						$this->mo_oauth_show_success_message();
					} else {
						update_option( 'message', $output->message );
						$this->mo_oauth_show_error_message();
					}
				}
			}
		} elseif ( isset( $_POST ['option'] ) && 'mo_oauth_forgot_password_form_option' === sanitize_text_field( wp_unslash( $_POST['option'] ) ) && isset( $_REQUEST['mo_oauth_forgotpassword_form_field'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['mo_oauth_forgotpassword_form_field'] ) ), 'mo_oauth_forgotpassword_form' ) ) {

			if ( current_user_can( 'administrator' ) ) {
				if ( ! mocognito_is_curl_installed() ) {
					update_option( 'mo_oauth_message', 'ERROR: <a href="http://php.net/manual/en/curl.installation.php" target="_blank">PHP cURL extension</a> is not installed or disabled. Resend OTP failed.' );
					$this->mo_oauth_show_error_message();
					return;
				}

				$email = get_option( 'mo_oauth_admin_email' );

				$customer = new MoCognito_Customer();
				$content  = json_decode( $customer->mo_oauth_forgot_password( $email ), true );

				if ( 0 === strcasecmp( $content ['status'], 'SUCCESS' ) ) {
					update_option( 'message', 'Your password has been reset successfully. Please enter the new password sent to ' . $email . '.' );
					$this->mo_oauth_show_success_message();
				}
			}
		} elseif ( isset( $_POST['option'] ) && 'mo_oauth_change_email' === sanitize_text_field( wp_unslash( $_POST['option'] ) ) && isset( $_REQUEST['mo_oauth_change_email_form_field'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['mo_oauth_change_email_form_field'] ) ), 'mo_oauth_change_email_form' ) ) {
			update_option( 'verify_customer', '' );
			update_option( 'mo_oauth_registration_status', '' );
			update_option( 'new_registration', 'true' );
		} elseif ( isset( $_POST['option'] ) && 'mo_oauth_register_with_phone_option' === sanitize_text_field( wp_unslash( $_POST['option'] ) ) && isset( $_REQUEST['mo_oauth_register_with_phone_form_field'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['mo_oauth_register_with_phone_form_field'] ) ), 'mo_oauth_register_with_phone_form' ) ) {

			if ( current_user_can( 'administrator' ) ) {
				if ( ! mocognito_is_curl_installed() ) {
					return $this->mo_oauth_show_curl_error();
				}
				$phone = stripslashes( sanitize_text_field( wp_unslash( $_POST['phone'] ) ) );
				$phone = str_replace( ' ', '', $phone );
				$phone = str_replace( '-', '', $phone );
				update_option( 'mo_oauth_admin_phone', $phone );
				$customer = new MoCognito_Customer();
				$content  = json_decode( $customer->send_otp_token( '', $phone, false, true ), true );
				if ( $content ) {
					update_option( 'message', ' A one time passcode is sent to ' . get_site_option( 'mo_oauth_admin_phone' ) . '. Please enter the otp here to verify your email.' );
					$_SESSION['mo_oauth_transactionId'] = $content['txId'];
					update_option( 'mo_oauth_registration_status', 'MO_OTP_DELIVERED_SUCCESS_PHONE' );
					$this->mo_oauth_show_success_message();
				} else {
					update_option( 'message', 'There was an error in sending SMS. Please click on Resend OTP to try again.' );
					update_option( 'mo_oauth_registration_status', 'MO_OTP_DELIVERED_FAILURE_PHONE' );
					$this->mo_oauth_show_error_message();
				}
			}
		} elseif ( isset( $_POST['option'] ) && 'mo_oauth_client_skip_feedback' === sanitize_text_field( wp_unslash( $_POST['option'] ) ) && isset( $_REQUEST['mo_oauth_skip_feedback_form_field'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['mo_oauth_skip_feedback_form_field'] ) ), 'mo_oauth_skip_feedback_form' ) ) {

			deactivate_plugins( __FILE__ );
			update_option( 'message', 'Plugin deactivated successfully' );
			$this->mo_oauth_show_success_message();
			wp_safe_redirect( self_admin_url( 'plugins.php?deactivate=true' ) );
		} elseif ( isset( $_POST['mo_oauth_client_feedback'] ) && 'true' === sanitize_text_field( wp_unslash( $_POST['mo_oauth_client_feedback'] ) ) && isset( $_REQUEST['mo_oauth_feedback_form_field'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['mo_oauth_feedback_form_field'] ) ), 'mo_oauth_feedback_form' ) ) {

			if ( current_user_can( 'administrator' ) ) {
				$user                      = wp_get_current_user();
				$message                   = 'Plugin Deactivated:';
				$deactivate_reason         = array_key_exists( 'deactivate_reason_radio', $_POST ) ? sanitize_text_field( wp_unslash( $_POST['deactivate_reason_radio'] ) ) : false;
				$deactivate_reason_message = array_key_exists( 'query_feedback', $_POST ) ? sanitize_text_field( wp_unslash( $_POST['query_feedback'] ) ) : false;
				if ( $deactivate_reason ) {
					$message .= $deactivate_reason;
					if ( isset( $deactivate_reason_message ) ) {
						$message .= ':' . $deactivate_reason_message;
					}
					$email = get_option( 'mo_oauth_admin_email' );
					if ( empty( $email ) ) {
						$email = $user->user_email;
					}
					$phone            = get_option( 'mo_oauth_admin_phone' );
					$feedback_reasons = new MoCognito_Customer();
					$submited         = json_decode( $feedback_reasons->mo_oauth_send_email_alert( $email, $phone, $message, 'Feedback: WordPress ' . MO_OAUTH_PLUGIN_NAME ), true );
					deactivate_plugins( __FILE__ );
					update_option( 'message', 'Thank you for the feedback.' );
					$this->mo_oauth_show_success_message();
					wp_safe_redirect( self_admin_url( 'plugins.php?deactivate=true' ) );
				} else {
					update_option( 'message', 'Please Select one of the reasons ,if your reason is not mentioned please select Other Reasons' );
					$this->mo_oauth_show_error_message();
				}
			}
		}

	}

	/**
	 * Get Current Customer
	 */
	public function mo_oauth_get_current_customer() {
		$customer     = new MoCognito_Customer();
		$content      = $customer->get_customer_key();
		$customer_key = json_decode( $content, true );
		if ( json_last_error() === JSON_ERROR_NONE ) {
			update_option( 'mo_oauth_admin_customer_key', $customer_key['id'] );
			update_option( 'mo_oauth_admin_api_key', $customer_key['apiKey'] );
			update_option( 'customer_token', $customer_key['token'] );
			update_option( 'password', '' );
			update_option( 'message', 'Customer retrieved successfully' );
			delete_option( 'verify_customer' );
			delete_option( 'new_registration' );
			$this->mo_oauth_show_success_message();
		} else {
			update_option( 'message', 'You already have an account with miniOrange. Please enter a valid password.' );
			update_option( 'verify_customer', 'true' );
			$this->mo_oauth_show_error_message();

		}
	}

	/**
	 * Create customer
	 */
	public function create_customer() {
		$customer     = new MoCognito_Customer();
		$customer_key = json_decode( $customer->create_customer(), true );
		if ( 0 === strcasecmp( $customer_key['status'], 'CUSTOMER_USERNAME_ALREADY_EXISTS' ) ) {
			$this->mo_oauth_get_current_customer();
			delete_option( 'mo_oauth_new_customer' );
		} elseif ( 0 === strcasecmp( $customer_key['status'], 'SUCCESS' ) ) {
			update_option( 'mo_oauth_admin_customer_key', $customer_key['id'] );
			update_option( 'mo_oauth_admin_api_key', $customer_key['apiKey'] );
			update_option( 'customer_token', $customer_key['token'] );
			update_option( 'password', '' );
			update_option( 'message', 'Registered successfully.' );
			update_option( 'mo_oauth_registration_status', 'MO_OAUTH_REGISTRATION_COMPLETE' );
			update_option( 'mo_oauth_new_customer', 1 );
			delete_option( 'verify_customer' );
			delete_option( 'new_registration' );
			$this->mo_oauth_show_success_message();
		}
	}

	/**
	 * Show curl error
	 */
	public function mo_oauth_show_curl_error() {
		if ( 0 === mocognito_is_curl_installed() ) {
			update_option( 'message', '<a href="http://php.net/manual/en/curl.installation.php" target="_blank">PHP CURL extension</a> is not installed or disabled. Please enable it to continue.' );
			$this->mo_oauth_show_error_message();
			return;
		}
	}

	/**
	 * Shortcode login
	 */
	public function mo_oauth_shortcode_login() {
		if ( mocognito_hbca_xyake() || ! mocognito_is_customer_registered() ) {
			return '<div class="mo_oauth_premium_option_text" style="text-align: center;border: 1px solid;margin: 5px;padding-top: 25px;"><p>This feature is supported only in standard and higher versions.</p>
				<p><a href="' . get_site_url( null, '/wp-admin/' ) . 'admin.php?page=mo_oauth_settings&tab=licensing">Click Here</a> to see our full list of Features.</p></div>';
		}
		$mowidget = new MoCognito_Widget();
		return $mowidget->mo_oauth_login_form();
	}

	/**
	 * Show Export plugin config
	 *
	 * @param ShareWith $share_with for share with.
	 */
	public function export_plugin_config( $share_with = false ) {
		$appslist          = get_option( 'mo_oauth_apps_list' );
		$currentapp_config = null;
		if ( is_array( $appslist ) ) {
			foreach ( $appslist as $key => $value ) {
				$currentapp_config = $value;
				break;
			}
		}
		if ( $share_with ) {
			unset( $currentapp_config['clientid'] );
			unset( $currentapp_config['clientsecret'] );
		}
		return $currentapp_config;
	}

}

/**
 * Is customer registered
 */
function mocognito_is_customer_registered() {
	$email        = get_option( 'mo_oauth_admin_email' );
	$customer_key = get_option( 'mo_oauth_admin_customer_key' );
	if ( ! $email || ! $customer_key || ! is_numeric( trim( $customer_key ) ) ) {
		return 0;
	} else {
		return 1;
	}
}

/**
 * Is CURL installed
 */
function mocognito_is_curl_installed() {
	if ( in_array( 'curl', get_loaded_extensions(), true ) ) {
		return 1;
	} else {
		return 0;
	}
}

new MoCognito_OAuth();

/**
 * Run
 */
function run_mo_oauth_client() {
	$plugin = new MoCognito_OAuth_Client();
	$plugin->run();
}
run_mo_oauth_client();
