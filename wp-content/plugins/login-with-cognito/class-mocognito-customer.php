<?php
/**
 * MiniOrange enables user to log in through OAuth to apps such as Cognito, Azure, Google, EVE Online etc.
 *
 * @package      miniOrange OAuth
 *
 * @license      https://plugins.miniorange.com/mit-license Expat
 */

/**
 * This library is miniOrange Authentication Service.
 * Contains Request Calls to Customer service.
 **/
class MoCognito_Customer {

	/**
	 * Email
	 *
	 * @var email $email for email.
	 */
	public $email;
	/**
	 * Phone
	 *
	 * @var phone $phone for phone.
	 */
	public $phone;
	/**
	 * Default Customer Key
	 *
	 * @var default_customer_key $default_customer_key for default_customer_key.
	 */
	private $default_customer_key = '16555';
	/**
	 * Default API Key
	 *
	 * @var default_api_key $default_api_key for default_api_key.
	 */
	private $default_api_key = 'fFd2XcvTGDemZvbw1bcUesNJWEqKbbUq';

	/**
	 * Create customer
	 */
	public function create_customer() {
		$url         = get_option( 'host_name' ) . '/moas/rest/customer/add';
		$this->email = get_option( 'mo_oauth_admin_email' );
		$this->phone = get_option( 'mo_oauth_admin_phone' );
		$password    = get_option( 'password' );
		$first_name  = get_option( 'mo_oauth_admin_fname' );
		$last_name   = get_option( 'mo_oauth_admin_lname' );
		$company     = get_option( 'mo_oauth_admin_company' );

		$fields       = array(
			'companyName'    => $company,
			'areaOfInterest' => MO_OAUTH_AREA_OF_INTEREST,
			'firstname'      => $first_name,
			'lastname'       => $last_name,
			'email'          => $this->email,
			'phone'          => $this->phone,
			'password'       => $password,
		);
		$field_string = wp_json_encode( $fields );
		$headers      = array(
			'Content-Type'  => 'application/json',
			'charset'       => 'UTF - 8',
			'Authorization' => 'Basic',
		);
		$args         = array(
			'method'      => 'POST',
			'body'        => $field_string,
			'timeout'     => '15',
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

		return wp_remote_retrieve_body( $response );
	}

	/**
	 * Customer Key
	 */
	public function get_customer_key() {
		$url   = get_option( 'host_name' ) . '/moas/rest/customer/key';
		$email = get_option( 'mo_oauth_admin_email' );

		$password = get_option( 'password' );

		$fields       = array(
			'email'    => $email,
			'password' => $password,
		);
		$field_string = wp_json_encode( $fields );

		$headers = array(
			'Content-Type'  => 'application/json',
			'charset'       => 'UTF - 8',
			'Authorization' => 'Basic',
		);
		$args    = array(
			'method'      => 'POST',
			'body'        => $field_string,
			'timeout'     => '15',
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

		return wp_remote_retrieve_body( $response );
	}

	/**
	 * Create customer
	 *
	 * @param name    $name for name.
	 *
	 * @param AppName $app_name for application name.
	 */
	public function add_oauth_application( $name, $app_name ) {
		$url           = get_option( 'host_name' ) . '/moas/rest/application/addoauth';
		$customer_key  = get_option( 'mo_oauth_admin_customer_key' );
		$scope         = get_option( 'mo_oauth_' . $name . '_scope' );
		$client_id     = get_option( 'mo_oauth_' . $name . '_client_id' );
		$client_secret = get_option( 'mo_oauth_' . $name . '_client_secret' );
		if ( false !== $scope ) {
			$fields = array(
				'applicationName' => $app_name,
				'scope'           => $scope,
				'customerId'      => $customer_key,
				'clientId'        => $client_id,
				'clientSecret'    => $client_secret,
			);
		} else {
			$fields = array(
				'applicationName' => $app_name,
				'customerId'      => $customer_key,
				'clientId'        => $client_id,
				'clientSecret'    => $client_secret,
			);
		}
		$field_string = wp_json_encode( $fields );

		$headers = array(
			'Content-Type'  => 'application/json',
			'charset'       => 'UTF - 8',
			'Authorization' => 'Basic',
		);
		$args    = array(
			'method'      => 'POST',
			'body'        => $field_string,
			'timeout'     => '15',
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

		return wp_remote_retrieve_body( $response );
	}

	/**
	 * Submit contact us
	 *
	 * @param email $email for email.
	 *
	 * @param phone $phone for phone.
	 *
	 * @param query $query for query.
	 *
	 * @param phone $send_config for send config.
	 */
	public function submit_contact_us( $email, $phone, $query, $send_config = true ) {
		global $current_user;
		wp_get_current_user();

		$mo_oauth       = new MoCognito_OAuth();
		$plugin_config  = $mo_oauth->export_plugin_config( true );
		$config_to_send = wp_json_encode( $plugin_config, JSON_UNESCAPED_SLASHES );
		$plugin_version = get_plugin_data( __DIR__ . DIRECTORY_SEPARATOR . 'mo_oauth_settings.php' )['Version'];

		$query = '[WP ' . MO_OAUTH_PLUGIN_NAME . ' ' . $plugin_version . '] ' . $query;
		if ( $send_config ) {
			$query .= '<br><br>Config String:<br><pre style="border:1px solid #444;padding:10px;"><code>' . $config_to_send . '</code></pre>';
		}
		$fields       = array(
			'firstName' => $current_user->user_firstname,
			'lastName'  => $current_user->user_lastname,
			'company'   => sanitize_text_field( wp_unslash( isset( $_SERVER['SERVER_NAME'] ) ? $_SERVER['SERVER_NAME'] : '' ) ),
			'email'     => $email,
			'ccEmail'   => 'oauthsupport@xecurify.com',
			'phone'     => $phone,
			'query'     => $query,
		);
		$field_string = wp_json_encode( $fields, JSON_UNESCAPED_SLASHES );

		$url = get_option( 'host_name' ) . '/moas/rest/customer/contact-us';

		$headers = array(
			'Content-Type'  => 'application/json',
			'charset'       => 'UTF - 8',
			'Authorization' => 'Basic',
		);
		$args    = array(
			'method'      => 'POST',
			'body'        => $field_string,
			'timeout'     => '15',
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

		return true;
	}

	/**
	 * Send OTP Token
	 *
	 * @param email         $email for email.
	 *
	 * @param phone         $phone for phone.
	 *
	 * @param send_to_email $send_to_email for send_to_email.
	 *
	 * @param send_to_phone $send_to_phone for send_to_phone.
	 */
	public function send_otp_token( $email, $phone, $send_to_email = true, $send_to_phone = false ) {
			$url = get_option( 'host_name' ) . '/moas/api/auth/challenge';

			$customer_key = $this->default_customer_key;
			$api_key      = $this->default_api_key;

			$username = get_option( 'mo_oauth_admin_email' );
			$phone    = get_option( 'mo_oauth_admin_phone' );
			/* Current time in milliseconds since midnight, January 1, 1970 UTC. */
			$current_time_in_millis = self::get_timestamp();

			/* Creating the Hash using SHA-512 algorithm */
			$string_to_hash = $customer_key . $current_time_in_millis . $api_key;
			$hash_value     = hash( 'sha512', $string_to_hash );

			$customer_key_header  = 'Customer-Key: ' . $customer_key;
			$timestamp_header     = 'Timestamp: ' . $current_time_in_millis;
			$authorization_header = 'Authorization: ' . $hash_value;

		if ( $send_to_email ) {
			$fields = array(
				'customerKey' => $customer_key,
				'email'       => $username,
				'authType'    => 'EMAIL',
			);} else {
				$fields = array(
					'customerKey' => $customer_key,
					'phone'       => $phone,
					'authType'    => 'SMS',
				);
			}
			$field_string = wp_json_encode( $fields );

			$headers                  = array( 'Content-Type' => 'application/json' );
			$headers['Customer-Key']  = $customer_key;
			$headers['Timestamp']     = $current_time_in_millis;
			$headers['Authorization'] = $hash_value;
			$args                     = array(
				'method'      => 'POST',
				'body'        => $field_string,
				'timeout'     => '15',
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

			return wp_remote_retrieve_body( $response );
	}

	/**
	 * Get timestamp
	 */
	public function get_timestamp() {
		$url     = get_option( 'host_name' ) . '/moas/rest/mobile/get-timestamp';
		$headers = array(
			'Content-Type'  => 'application/json',
			'charset'       => 'UTF - 8',
			'Authorization' => 'Basic',
		);
		$args    = array(
			'method'      => 'POST',
			'body'        => array(),
			'timeout'     => '15',
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

		return wp_remote_retrieve_body( $response );
	}

	/**
	 * Validate OTP Token
	 *
	 * @param transaction_id $transaction_id for transaction_id.
	 *
	 * @param otp_token      $otp_token for otp_token.
	 */
	public function validate_otp_token( $transaction_id, $otp_token ) {
		$url = get_option( 'host_name' ) . '/moas/api/auth/validate';

		$customer_key = $this->default_customer_key;
		$api_key      = $this->default_api_key;

		$username = get_option( 'mo_oauth_admin_email' );

		/* Current time in milliseconds since midnight, January 1, 1970 UTC. */
		$current_time_in_millis = self::get_timestamp();

		/* Creating the Hash using SHA-512 algorithm */
		$string_to_hash = $customer_key . $current_time_in_millis . $api_key;
		$hash_value     = hash( 'sha512', $string_to_hash );

		$customer_key_header  = 'Customer-Key: ' . $customer_key;
		$timestamp_header     = 'Timestamp: ' . $current_time_in_millis;
		$authorization_header = 'Authorization: ' . $hash_value;

		$fields = '';

			// *check for otp over sms/email
			$fields = array(
				'txId'  => $transaction_id,
				'token' => $otp_token,
			);

			$field_string = wp_json_encode( $fields );

			$headers                  = array( 'Content-Type' => 'application/json' );
			$headers['Customer-Key']  = $customer_key;
			$headers['Timestamp']     = $current_time_in_millis;
			$headers['Authorization'] = $hash_value;
			$args                     = array(
				'method'      => 'POST',
				'body'        => $field_string,
				'timeout'     => '15',
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

			return wp_remote_retrieve_body( $response );
	}

	/**
	 * Validate OTP Token
	 */
	public function check_customer() {
			$url   = get_option( 'host_name' ) . '/moas/rest/customer/check-if-exists';
			$email = get_option( 'mo_oauth_admin_email' );

			$fields       = array(
				'email' => $email,
			);
			$field_string = wp_json_encode( $fields );
			$headers      = array(
				'Content-Type'  => 'application/json',
				'charset'       => 'UTF - 8',
				'Authorization' => 'Basic',
			);
			$args         = array(
				'method'      => 'POST',
				'body'        => $field_string,
				'timeout'     => '15',
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

			return wp_remote_retrieve_body( $response );
	}

	/**
	 * Send email alert
	 *
	 * @param email   $email for email.
	 *
	 * @param phone   $phone for phone.
	 *
	 * @param message $message for message.
	 *
	 * @param subject $subject for subject.
	 */
	public function mo_oauth_send_email_alert( $email, $phone, $message, $subject ) {

		if ( ! $this->check_internet_connection() ) {
			return;
		}
		$url = get_option( 'host_name' ) . '/moas/api/notify/send';

		$plugin_version = get_plugin_data( __DIR__ . DIRECTORY_SEPARATOR . 'mo_oauth_settings.php' )['Version'];

		$customer_key = $this->default_customer_key;
		$api_key      = $this->default_api_key;

		$current_time_in_millis = self::get_timestamp();
		$string_to_hash         = $customer_key . $current_time_in_millis . $api_key;
		$hash_value             = hash( 'sha512', $string_to_hash );
		$customer_key_header    = 'Customer-Key: ' . $customer_key;
		$timestamp_header       = 'Timestamp: ' . $current_time_in_millis;
		$authorization_header   = 'Authorization: ' . $hash_value;
		$from_email             = $email;
		$subject                = $subject . ' ' . $plugin_version;
		$site_url               = site_url();

		global $user;
		$user  = wp_get_current_user();
		$query = '[WP ' . MO_OAUTH_PLUGIN_NAME . ' ' . $plugin_version . '] : ' . $message;

		$content = '<div >Hello, <br><br>First Name :' . $user->user_firstname . '<br><br>Last  Name :' . $user->user_lastname . '   <br><br>Company :<a href="' . esc_attr( sanitize_text_field( wp_unslash( isset( $_SERVER['SERVER_NAME'] ) ? $_SERVER['SERVER_NAME'] : '' ) ) ) . '" target="_blank" >' . esc_attr( sanitize_text_field( wp_unslash( isset( $_SERVER['SERVER_NAME'] ) ? $_SERVER['SERVER_NAME'] : '' ) ) ) . '</a><br><br>Phone Number :' . $phone . '<br><br>Email :<a href="mailto:' . $from_email . '" target="_blank">' . $from_email . '</a><br><br>Query :' . $query . '</div>';

		$fields                   = array(
			'customerKey' => $customer_key,
			'sendEmail'   => true,
			'email'       => array(
				'customerKey' => $customer_key,
				'fromEmail'   => $from_email,
				'bccEmail'    => 'oauthsupport@xecurify.com',
				'fromName'    => 'miniOrange',
				'toEmail'     => 'oauthsupport@xecurify.com',
				'toName'      => 'oauthsupport@xecurify.com',
				'subject'     => $subject,
				'content'     => $content,
			),
		);
		$field_string             = wp_json_encode( $fields );
		$headers                  = array( 'Content-Type' => 'application/json' );
		$headers['Customer-Key']  = $customer_key;
		$headers['Timestamp']     = $current_time_in_millis;
		$headers['Authorization'] = $hash_value;
		$args                     = array(
			'method'      => 'POST',
			'body'        => $field_string,
			'timeout'     => '15',
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
	}

	/**
	 * Send demo alert
	 *
	 * @param email     $email for email.
	 *
	 * @param demo_plan $demo_plan for demo plan.
	 *
	 * @param message   $message for message.
	 *
	 * @param subject   $subject for subject.
	 */
	public function mo_oauth_send_demo_alert( $email, $demo_plan, $message, $subject ) {

		if ( ! $this->check_internet_connection() ) {
			return;
		}
		$url = get_option( 'host_name' ) . '/moas/api/notify/send';

		$customer_key = $this->default_customer_key;
		$api_key      = $this->default_api_key;

		$current_time_in_millis = self::get_timestamp();
		$string_to_hash         = $customer_key . $current_time_in_millis . $api_key;
		$hash_value             = hash( 'sha512', $string_to_hash );
		$customer_key_header    = 'Customer-Key: ' . $customer_key;
		$timestamp_header       = 'Timestamp: ' . $current_time_in_millis;
		$authorization_header   = 'Authorization: ' . $hash_value;
		$from_email             = $email;
		$site_url               = site_url();

		global $user;
		$user = wp_get_current_user();

		$content = '<div >Hello, </a><br><br>Email :<a href="mailto:' . $from_email . '" target="_blank">' . $from_email . '</a><br><br>Requested Demo for     : ' . $demo_plan . '<br><br>Requirements (User usecase)           : ' . $message . '</div>';

		$fields                   = array(
			'customerKey' => $customer_key,
			'sendEmail'   => true,
			'email'       => array(
				'customerKey' => $customer_key,
				'fromEmail'   => $from_email,
				'bccEmail'    => 'oauthsupport@xecurify.com',
				'fromName'    => 'miniOrange',
				'toEmail'     => 'oauthsupport@xecurify.com',
				'toName'      => 'oauthsupport@xecurify.com',
				'subject'     => $subject,
				'content'     => $content,
			),
		);
		$field_string             = wp_json_encode( $fields );
		$headers                  = array( 'Content-Type' => 'application/json' );
		$headers['Customer-Key']  = $customer_key;
		$headers['Timestamp']     = $current_time_in_millis;
		$headers['Authorization'] = $hash_value;
		$args                     = array(
			'method'      => 'POST',
			'body'        => $field_string,
			'timeout'     => '15',
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
	}

	/**
	 * Forgot password
	 *
	 * @param email $email for email.
	 */
	public function mo_oauth_forgot_password( $email ) {
		$url = get_option( 'host_name' ) . '/moas/rest/customer/password-reset';
		/* The customer Key provided to you */
		$customer_key = get_option( 'mo_oauth_admin_customer_key' );

		/* The customer API Key provided to you */
		$api_key = get_option( 'mo_oauth_admin_api_key' );

		/* Current time in milliseconds since midnight, January 1, 1970 UTC. */
		$current_time_in_millis = self::get_timestamp();

		/* Creating the Hash using SHA-512 algorithm */
		$string_to_hash = $customer_key . $current_time_in_millis . $api_key;
		$hash_value     = hash( 'sha512', $string_to_hash );

		$customer_key_header  = 'Customer-Key: ' . $customer_key;
		$timestamp_header     = 'Timestamp: ' . number_format( $current_time_in_millis, 0, '', '' );
		$authorization_header = 'Authorization: ' . $hash_value;

		$fields = '';

		// *check for otp over sms/email
		$fields = array(
			'email' => $email,
		);

		$field_string = wp_json_encode( $fields );

		$headers                  = array( 'Content-Type' => 'application/json' );
		$headers['Customer-Key']  = $customer_key;
		$headers['Timestamp']     = $current_time_in_millis;
		$headers['Authorization'] = $hash_value;
		$args                     = array(
			'method'      => 'POST',
			'body'        => $field_string,
			'timeout'     => '15',
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

		return wp_remote_retrieve_body( $response );
	}

	/**
	 * Check internet connection
	 */
	public function check_internet_connection() {
		return (bool) @fsockopen( 'login.xecurify.com', 443, $i_errno, $s_err_str, 5 ); //phpcs:ignore -- Ignoring this as this is not a file system call.
	}


}
