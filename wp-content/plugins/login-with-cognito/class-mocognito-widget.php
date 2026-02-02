<?php
/**
 * MINIORANGE OAuth Views
 *
 * @package    MoCognito\Views
 */

/**
 * MoCognito Widget
 */
class MoCognito_Widget extends WP_Widget {

	/**
	 * Constructor
	 */
	public function __construct() {
		update_option( 'host_name', 'https://login.xecurify.com' );
		add_action( 'wp_enqueue_scripts', array( $this, 'register_plugin_styles' ) );
		add_action( 'init', array( $this, 'mo_oauth_start_session' ) );
		add_action( 'wp_logout', array( $this, 'mo_oauth_end_session' ) );
		add_action( 'login_form', array( $this, 'mo_oauth_wplogin_form_button' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'mo_oauth_wplogin_form_style' ) );
		parent::__construct( 'mocognito_widget', MO_OAUTH_ADMIN_MENU, array( 'description' => __( 'Login to Apps with OAuth', 'flw' ) ) );

	}

	/**
	 * Form style
	 */
	public function mo_oauth_wplogin_form_style() {

		wp_enqueue_style( 'mo_oauth_fontawesome', plugins_url( 'css/font-awesome.min.css', __FILE__ ), array(), '1.5.3' );
		wp_enqueue_style( 'mo_oauth_wploginform', plugins_url( 'css/login-page.min.css', __FILE__ ), array(), '1.5.3' );
	}

	/**
	 * Form button
	 */
	public function mo_oauth_wplogin_form_button() {
		$appslist = get_option( 'mo_oauth_apps_list' );
		if ( is_array( $appslist ) && count( $appslist ) > 0 ) {
			$this->mo_oauth_load_login_script();
			foreach ( $appslist as $key => $app ) {

				if ( isset( $app['show_on_login_page'] ) && 1 === $app['show_on_login_page'] ) {

					$this->mo_oauth_wplogin_form_style();

					echo '<br>';
					echo '<h4>Connect with :</h4><br>';
					echo '<div class="row">';

					$logo_class = $this->mo_oauth_client_login_button_logo( $app['appId'] );

					echo '<a style="text-decoration:none" href="javascript:void(0)" onClick="moOAuthLoginNew(\'' . esc_attr( $key ) . '\');"><div class="mo_oauth_login_button"><i class="' . esc_attr( $logo_class ) . ' mo_oauth_login_button_icon"></i><h3 class="mo_oauth_login_button_text">Login with ' . esc_attr( ucwords( $key ) ) . '</h3></div></a>';
					echo '</div><br><br>';
				}
			}
		}
	}

	/**
	 * Login Button logo
	 *
	 * @param currentAppId $current_app_id for currentAppId.
	 */
	public function mo_oauth_client_login_button_logo( $current_app_id ) {
		$currentapp = mocognito_get_app( $current_app_id );
		$logo_class = $currentapp->logo_class;
		return $logo_class;
	}

	/**
	 * Start session
	 */
	public function mo_oauth_start_session() {
		if ( ! session_id() && ! mocognito_is_ajax_request() && ! mocognito_is_rest_api_call() ) {
			session_start();
		}

		//@phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Ignoring Nonce as this is a safe operation
		if ( isset( $_REQUEST['option'] ) && 'testattrmappingconfig' === $_REQUEST['option'] ) {
			//@phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Ignoring Nonce as this is a safe operation
			$mo_oauth_app_name = sanitize_text_field( wp_unslash( isset( $_REQUEST['app'] ) ? $_REQUEST['app'] : '' ) );
			wp_safe_redirect( site_url() . '?option=oauthredirect&app_name=' . rawurlencode( $mo_oauth_app_name ) . '&test=true' );
			exit();
		}

	}

	/**
	 * End session
	 */
	public function mo_oauth_end_session() {
		if ( ! session_id() ) {
			session_start();
		}
		session_destroy();
	}

	/**
	 * Widget
	 *
	 * @param args     $args for args.
	 *
	 * @param instance $instance for instance.
	 */
	public function widget( $args, $instance ) {

		//@phpcs:ignore WordPress.PHP.DontExtract.extract_extract -- Ignoring Nonce as this is a safe operation
		extract( $args );

		//@phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Ignoring as this is already escaped by WordPress
		echo $args['before_widget'];
		if ( ! empty( $wid_title ) ) {
			echo $args['before_title'] . esc_html( $wid_title ) . $args['after_title']; //@phpcs:ignore
		}

		//@phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Ignoring as this is already escaped inside function mo_oauth_login_form()
		echo $this->mo_oauth_login_form();

		//@phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Ignoring as this is already escaped by WordPress
		echo $args['after_widget'];
	}

	/**
	 * Update
	 *
	 * @param new_instance $new_instance for new_instance.
	 *
	 * @param old_instance $old_instance for old_instance.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		if ( isset( $new_instance['wid_title'] ) ) {
			$instance['wid_title'] = wp_strip_all_tags( $new_instance['wid_title'] );
		}

		return $instance;
	}

	/**
	 * Login form
	 */
	public function mo_oauth_login_form() {
		global $post;
		$this->error_message();
		$temp = '';

		$appslist = get_option( 'mo_oauth_apps_list' );
		if ( $appslist && count( $appslist ) > 0 ) {
			$apps_configured = true;
		}

		if ( ! is_user_logged_in() ) {

			if ( isset( $apps_configured ) && $apps_configured ) {

				$this->mo_oauth_load_login_script();

				$style      = get_option( 'mo_oauth_icon_width' ) ? 'width:' . get_option( 'mo_oauth_icon_width' ) . ';' : '';
				$style     .= get_option( 'mo_oauth_icon_height' ) ? 'height:' . get_option( 'mo_oauth_icon_height' ) . ';' : '';
				$style     .= get_option( 'mo_oauth_icon_margin' ) ? 'margin:' . get_option( 'mo_oauth_icon_margin' ) . ';' : '';
				$custom_css = get_option( 'mo_oauth_icon_configure_css' );
				if ( empty( $custom_css ) ) {
					$temp .= '<style>.oauthloginbutton{background: #7272dc;height:40px;padding:8px;text-align:center;color:#fff;}</style>';
				} else {
					$temp .= '<style>' . esc_html( $custom_css ) . '</style>';
				}

				if ( is_array( $appslist ) ) {
					foreach ( $appslist as $key => $app ) {
						$logo_class = $this->mo_oauth_client_login_button_logo( $app['appId'] );

						$temp .= '<a style="text-decoration:none" href="javascript:void(0)" onClick="moOAuthLoginNew(\'' . esc_attr( $key ) . '\');"><div class="mo_oauth_login_button_widget"><i class="' . esc_attr( $logo_class ) . ' mo_oauth_login_button_icon_widget"></i><h3 class="mo_oauth_login_button_text_widget">Login with ' . esc_attr( ucwords( $key ) ) . '</h3></div></a>';
					}
				}
			} else {
				$temp .= '<div>No apps configured.</div>';
			}
		} else {
			$current_user       = wp_get_current_user();
			$link_with_username = __( 'Howdy, ', 'flw' ) . $current_user->display_name;
			$temp              .= '<div id="logged_in_user" class="login_wid">
			<li>' . esc_attr( $link_with_username ) . ' | <a href="' . esc_url( wp_logout_url( site_url() ) ) . '" >Logout</a></li>
		</div>';

		}
		return $temp;
	}

	/**
	 * Load login script
	 */
	private function mo_oauth_load_login_script() {
		?>
	<script type="text/javascript">

		function HandlePopupResult(result) {
			window.location.href = result;
		}

		function moOAuthLogin(app_name) {
			window.location.href = '<?php echo esc_url( site_url() ); ?>' + '/?option=generateDynmicUrl&app_name=' + app_name;
		}
		function moOAuthLoginNew(app_name) {
			window.location.href = '<?php echo esc_url( site_url() ); ?>' + '/?option=oauthredirect&app_name=' + app_name;
		}
	</script>
		<?php
	}


	/**
	 * Error message
	 */
	public function error_message() {
		if ( isset( $_SESSION['msg'] ) && $_SESSION['msg'] ) {
			echo '<div class="' . esc_attr( $_SESSION['msg_class'] ) . '">' . esc_attr( $_SESSION['msg'] ) . '</div>';
			unset( $_SESSION['msg'] );
			unset( $_SESSION['msg_class'] );
		}
	}

	/**
	 * Register plugin styles
	 */
	public function register_plugin_styles() {
		wp_enqueue_style( 'style_login_widget', plugins_url( 'css/style_login_widget.min.css', __FILE__ ), array(), '1.5.3' );
	}


}

/**
 * Update email to username Attr
 *
 * @param currentappname $currentappname for currentappname.
 */
function mocognito_update_email_to_username_attr( $currentappname ) {
	$appslist                                     = get_option( 'mo_oauth_apps_list' );
	$appslist[ $currentappname ]['username_attr'] = $appslist[ $currentappname ]['email_attr'];
	update_option( 'mo_oauth_apps_list', $appslist );
}

/**
 * Login validate
 */
function mocognito_login_validate() {

	/* Handle Eve Online old flow */

	//@phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Ignoring Nonce as this is a public SSO link
	if ( isset( $_REQUEST['option'] ) && isset( $_REQUEST['app_name'] ) && false !== strpos( sanitize_text_field( wp_unslash( $_REQUEST['option'] ) ), 'oauthredirect' ) ) {

		//@phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Ignoring Nonce as this is a public SSO link
		$appname  = sanitize_text_field( wp_unslash( $_REQUEST['app_name'] ) );
		$appslist = get_option( 'mo_oauth_apps_list' );

		//@phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Ignoring Nonce as this is a public SSO link
		if ( isset( $_REQUEST['redirect_url'] ) ) {
			//@phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Ignoring Nonce as this is a public SSO link
			update_option( 'mo_oauth_redirect_url', sanitize_text_field( wp_unslash( $_REQUEST['redirect_url'] ) ) );
		}

		//@phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Ignoring Nonce as this is a public SSO link
		if ( isset( $_REQUEST['test'] ) ) {
			setcookie( 'mo_oauth_test', true );
		} else {
			setcookie( 'mo_oauth_test', false );
		}

		if ( false === $appslist ) {
			exit( 'Looks like you have not configured OAuth provider, please try to configure OAuth provider first' );
		}

		foreach ( $appslist as $key => $app ) {
			if ( $appname === $key ) {

				//@phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode -- Ignoring as need base64 to persist special chars
				$state             = base64_encode( $appname );
				$authorization_url = $app['authorizeurl'];

				if ( strpos( $authorization_url, '?' ) !== false ) {
					$authorization_url = $authorization_url . '&client_id=' . $app['clientid'] . '&scope=' . $app['scope'] . '&redirect_uri=' . $app['redirecturi'] . '&response_type=code&state=' . $state;
				} else {
					$authorization_url = $authorization_url . '?client_id=' . $app['clientid'] . '&scope=' . $app['scope'] . '&redirect_uri=' . $app['redirecturi'] . '&response_type=code&state=' . $state;
				}

				if ( '' === session_id() || ! isset( $_SESSION ) ) {
					session_start();
				}
				$_SESSION['oauth2state'] = $state;
				$_SESSION['appname']     = $appname;

				header( 'Location: ' . $authorization_url );
				exit;
			}
		}
	} elseif ( strpos( isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '', '/oauthcallback' ) !== false || isset( $_GET['code'] ) ) { //@phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Ignoring as Nonce can not be returned by OAuth providers on callback URL

		if ( '' === session_id() || ! isset( $_SESSION ) ) {
			session_start();
		}

		//@phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Ignoring as Nonce can not be returned by OAuth providers on callback URL
		if ( ! isset( $_GET['code'] ) ) {
			//@phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Ignoring as Nonce can not be returned by OAuth providers on callback URL
			if ( isset( $_GET['error_description'] ) ) {
				exit( esc_attr( sanitize_text_field( wp_unslash( $_GET['error_description'] ) ) ) ); //@phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Ignoring as Nonce can not be returned by OAuth providers on callback URL
			} elseif ( isset( $_GET['error'] ) ) { //@phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Ignoring as Nonce can not be returned by OAuth providers on callback URL
				exit( esc_attr( sanitize_text_field( wp_unslash( $_GET['error'] ) ) ) ); //@phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Ignoring as Nonce can not be returned by OAuth providers on callback URL
			}
			exit( 'Invalid response' );
		} else {

			try {

				$currentappname = '';

				if ( isset( $_SESSION['appname'] ) && ! empty( $_SESSION['appname'] ) ) {
					$currentappname = sanitize_text_field( $_SESSION['appname'] );
				} elseif ( isset( $_GET['state'] ) && ! empty( $_GET['state'] ) ) { //@phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Ignoring as Nonce can not be returned by OAuth providers on callback URL
					$currentappname = sanitize_text_field( base64_decode( wp_unslash( $_GET['state'] ) ) ); //@phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode -- Ignoring as Nonce can not be returned by OAuth providers on callback URL, false alarm for Sanitization and base64 is needed
				}

				if ( empty( $currentappname ) ) {
					exit( 'No request found for this application.' );
				}

				$appslist      = get_option( 'mo_oauth_apps_list' );
				$username_attr = '';
				$currentapp    = false;
				foreach ( $appslist as $key => $app ) {
					if ( $key === $currentappname ) {
						$currentapp = $app;
						if ( isset( $app['username_attr'] ) ) {
							$username_attr = $app['username_attr'];
						} elseif ( isset( $app['email_attr'] ) ) {
								mocognito_update_email_to_username_attr( $currentappname );
								$username_attr = $app['email_attr'];
						}
					}
				}

				if ( ! $currentapp ) {
					exit( 'Application not configured.' );
				}

				$mo_oauth_handler = new MoCognito_OAuth_Handler();
				if ( isset( $currentapp['apptype'] ) && 'openidconnect' === $currentapp['apptype'] ) {
					if ( ! isset( $currentapp['send_headers'] ) ) {
						$currentapp['send_headers'] = false;
					}
					if ( ! isset( $currentapp['send_body'] ) ) {
						$currentapp['send_body'] = false;
					}
					$token_response = $mo_oauth_handler->get_id_token(
						$currentapp['accesstokenurl'],
						'authorization_code',
						$currentapp['clientid'],
						$currentapp['clientsecret'],
						sanitize_text_field( wp_unslash( $_GET['code'] ) ), //@phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Ignoring as Nonce can not be returned by OAuth providers on callback URL
						$currentapp['redirecturi'],
						$currentapp['send_headers'],
						$currentapp['send_body']
					);

					$id_token = isset( $token_response['id_token'] ) ? $token_response['id_token'] : $token_response['access_token'];

					if ( ! $id_token ) {
						exit( 'Invalid token received.' );
					} else {
						$resource_owner = $mo_oauth_handler->get_resource_owner_from_id_token( $id_token );
					}
				} else {
					$access_token_url = $currentapp['accesstokenurl'];

					if ( ! isset( $currentapp['send_headers'] ) ) {
						$currentapp['send_headers'] = false;
					}
					if ( ! isset( $currentapp['send_body'] ) ) {
						$currentapp['send_body'] = false;
					}

					$access_token = $mo_oauth_handler->get_access_token( $access_token_url, 'authorization_code', $currentapp['clientid'], $currentapp['clientsecret'], sanitize_text_field( wp_unslash( $_GET['code'] ) ), $currentapp['redirecturi'], $currentapp['send_headers'], $currentapp['send_body'] ); //@phpcs:ignore

					if ( ! $access_token ) {
						exit( 'Invalid token received.' );
					}

					$resourceownerdetailsurl = $currentapp['resourceownerdetailsurl'];
					if ( '=' === substr( $resourceownerdetailsurl, -1 ) ) {
						$resourceownerdetailsurl .= $access_token;
					}

					$resource_owner = $mo_oauth_handler->get_resource_owner( $resourceownerdetailsurl, $access_token );
				}
				$username = '';
				update_option( 'mo_oauth_attr_name_list', $resource_owner );
				if ( isset( $_COOKIE['mo_oauth_test'] ) && ! empty( $_COOKIE['mo_oauth_test'] ) ) {
					echo '<div style="font-family:Calibri;padding:0 3%;">';
					echo '<style>table{border-collapse:collapse;}th {background-color: #eee; text-align: center; padding: 8px; border-width:1px; border-style:solid; border-color:#212121;}tr:nth-child(odd) {background-color: #f2f2f2;} td{padding:8px;border-width:1px; border-style:solid; border-color:#212121;}</style>';
					echo '<h2>Test Configuration</h2><table><tr><th>Attribute Name</th><th>Attribute Value</th></tr>';
					mocognito_testattrmappingconfig( '', $resource_owner );
					echo '</table>';
					echo '<div style="padding: 10px;"></div><input style="padding:1%;width:100px;background: #0091CD none repeat scroll 0% 0%;cursor: pointer;font-size:15px;border-width: 1px;border-style: solid;border-radius: 3px;white-space: nowrap;box-sizing: border-box;border-color: #0073AA;box-shadow: 0px 1px 0px rgba(120, 200, 230, 0.6) inset;color: #FFF;"type="button" value="Done" onClick="self.close();"></div>';
					exit();
				}

				if ( ! empty( $username_attr ) ) {
					$username = mocognito_getnestedattribute( $resource_owner, $username_attr );
				}

				if ( empty( $username ) || '' === $username ) {
					exit( 'Username not received. Check your <b>Attribute Mapping</b> configuration.' );
				}

				if ( ! is_string( $username ) ) {
					wp_die( 'Username is not a string. It is ' . esc_attr( mocognito_get_proper_prefix( gettype( $username ) ) ) );
				}

				$user = get_user_by( 'login', $username );

				if ( $user ) {
					$user_id = $user->ID;
				} else {
					$user_id = 0;
					if ( mocognito_hbca_xyake() ) {
						$user = mocognito_jhuyn_jgsukaj( $username );
					} else {
						$user = mocognito_hjsguh_kiishuyauh878gs( $username );
					}
				}
				if ( $user ) {
					wp_set_current_user( $user->ID );
					wp_set_auth_cookie( $user->ID );
					$user = get_user_by( 'ID', $user->ID );
					do_action( 'wp_login', $user->user_login, $user );
					$redirect_to = get_option( 'mo_oauth_redirect_url' );

					if ( false === $redirect_to ) {
						$redirect_to = home_url();
					}

					wp_safe_redirect( $redirect_to );
					exit;
				}
			} catch ( Exception $e ) {

				// Failed to get the access token or user details.
				exit( esc_html( $e->getMessage() ) );

			}
		}
	}
}

/**
 * HK
 *
 * @param username $username for username.
 */
function mocognito_hjsguh_kiishuyauh878gs( $username ) {
	$random_password = wp_generate_password( 10, false );
	$user_id         = wp_create_user( $username, $random_password );
	$user            = get_user_by( 'login', $username );
	wp_update_user( array( 'ID' => $user_id ) );
	return $user;
}

/**
 * Here entity is corporation, alliance or character name. The administrator compares these when user logs in
 *
 * @param entity_value         $entity_value for entity_value.
 *
 * @param entity_session_value $entity_session_value for entity_session_value.
 *
 * @param entity_name          $entity_name for entity_name.
 */
function mocognito_check_validity_of_entity( $entity_value, $entity_session_value, $entity_name ) {

	$entity_string = $entity_value ? $entity_value : false;
	$valid_entity  = false;
	if ( $entity_string ) {           // checks if entityString is defined.
		if ( strpos( $entity_string, ',' ) !== false ) {         // checks if there are more than 1 entity defined.
			$entity_list = array_map( 'trim', explode( ',', $entity_string ) );
			foreach ( $entity_list as $entity ) {            // checks for each entity to exist.
				if ( $entity === $entity_session_value ) {
					$valid_entity = true;
					break;
				}
			}
		} else {        // only one entity is defined.
			if ( $entity_string === $entity_session_value ) {
				$valid_entity = true;
			}
		}
	} else {            // entity is not defined.
		$valid_entity = false;
	}
	return $valid_entity;
}

/**
 * JJ
 *
 * @param temp_var $temp_var for temp_var.
 */
function mocognito_jhuyn_jgsukaj( $temp_var ) {
	return mocognito_jkhuiysuayhbw( $temp_var );
}

/**
 * Test attribute mapping config
 *
 * @param nestedprefix           $nestedprefix for nestedprefix.
 *
 * @param resource_owner_details $resource_owner_details for resource_owner_details.
 *
 * @param tr_class_prefix        $tr_class_prefix for tr_class_prefix.
 */
function mocognito_testattrmappingconfig( $nestedprefix, $resource_owner_details, $tr_class_prefix = '' ) {
	foreach ( $resource_owner_details as $key => $resource ) {
		if ( is_array( $resource ) || is_object( $resource ) ) {
			if ( ! empty( $nestedprefix ) ) {
				$nestedprefix .= '.';
			}
			mocognito_testattrmappingconfig( $nestedprefix . $key, $resource, $tr_class_prefix );
			$nestedprefix = rtrim( $nestedprefix, '.' );
		} else {
			echo '<tr class="' . esc_attr( $tr_class_prefix ) . 'tr"><td class="' . esc_attr( $tr_class_prefix ) . 'td">';
			if ( ! empty( $nestedprefix ) ) {
				echo esc_attr( $nestedprefix ) . '.';
			}
			echo esc_html( $key ) . '</td><td class="' . esc_attr( $tr_class_prefix ) . 'td">' . esc_attr( $resource ) . '</td></tr>';
		}
	}
}

/**
 * Get Nested Attribute
 *
 * @param resource $resource for resource.
 *
 * @param key      $key for key.
 */
function mocognito_getnestedattribute( $resource, $key ) {
	if ( '' === $key ) {
		return '';
	}

	$keys = explode( '.', $key );
	if ( count( $keys ) > 1 ) {
		$current_key = $keys[0];
		if ( isset( $resource[ $current_key ] ) ) {
			return mocognito_getnestedattribute( $resource[ $current_key ], str_replace( $current_key . '.', '', $key ) );
		}
	} else {
		$current_key = $keys[0];
		if ( isset( $resource[ $current_key ] ) ) {
			return $resource[ $current_key ];
		}
	}
}

/**
 * JK
 *
 * @param ejhi $ejhi for ejhil.
 */
function mocognito_jkhuiysuayhbw( $ejhi ) {
	$option                  = 0;
	$flag                    = false;
	$mo_oauth_authorizations = get_option( 'mo_oauth_authorizations' );
	if ( ! empty( $mo_oauth_authorizations ) ) {
		$option = get_option( 'mo_oauth_authorizations' );
	}
	$user = mocognito_hjsguh_kiishuyauh878gs( $ejhi );
	// if ( $user ) { .
	// } .
		++$option;
	update_option( 'mo_oauth_authorizations', $option );
	if ( $option >= 10 ) {
		$mo_oauth_set_val = base64_decode( 'bW9fb2F1dGhfZmxhZw==' ); //@phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode --safe to use this here
		update_option( $mo_oauth_set_val, true );
	}
	return $user;
}

/**
 * Get proper prefix
 *
 * @param type $type for type.
 */
function mocognito_get_proper_prefix( $type ) {
	$letter = substr( $type, 0, 1 );
	$vowels = array( 'a', 'e', 'i', 'o', 'u' );
	return ( in_array( $letter, $vowels, true ) ) ? ' an ' . $type : ' a ' . $type;
}

/**
 * Register widget
 */
function mocognito_register_mo_oauth_widget() {
	register_widget( 'mocognito_widget' );
}

/**
 * Is Ajax Request
 */
function mocognito_is_ajax_request() {
	return defined( 'DOING_AJAX' ) && DOING_AJAX;
}

/**
 * IsRestAPICall
 */
function mocognito_is_rest_api_call() {
	return strpos( sanitize_text_field( wp_unslash( isset( $_SERVER['REQUEST_URI'] ) ? $_SERVER['REQUEST_URI'] : '' ) ), '/wp-json' ) === false;
}

	add_action( 'widgets_init', 'mocognito_register_mo_oauth_widget' );
	add_action( 'init', 'mocognito_login_validate' );
