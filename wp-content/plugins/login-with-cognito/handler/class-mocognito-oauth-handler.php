<?php
/**
 * MINIORANGE OAuth Handler
 *
 * @package    MoCognito
 */

/**
 * Include
 */
class MoCognito_OAuth_Handler {

	/**
	 * MO Cognito Client Admin
	 *
	 * @param Tokenendpoint $tokenendpoint for token endpoint URL.
	 *
	 * @param GrantType     $grant_type for grant type.
	 *
	 * @param ClientID      $clientid for client ID.
	 *
	 * @param ClientSecret  $clientsecret for client secret.
	 *
	 * @param code          $code for code.
	 *
	 * @param RedirectUrl   $redirect_url for redirect URL.
	 *
	 * @param SendHeaders   $send_headers for send headers.
	 *
	 * @param SendBody      $send_body for sent body.
	 */
	public function get_access_token( $tokenendpoint, $grant_type, $clientid, $clientsecret, $code, $redirect_url, $send_headers, $send_body ) {
		$response = $this->get_token( $tokenendpoint, $grant_type, $clientid, $clientsecret, $code, $redirect_url, $send_headers, $send_body );
		$content  = json_decode( $response, true );

		if ( isset( $content['access_token'] ) ) {
			return $content['access_token'];
		} else {
			echo 'Invalid response received from OAuth Provider. Contact your administrator for more details.<br><br><b>Response : </b><br>' . esc_attr( $response );
			exit;
		}
	}

	/**
	 * Get Token (OAuth flow)
	 *
	 * @param Tokenendpoint $tokenendpoint for token endpoint URL.
	 *
	 * @param GrantType     $grant_type for grant type.
	 *
	 * @param ClientID      $clientid for client ID.
	 *
	 * @param ClientSecret  $clientsecret for client secret.
	 *
	 * @param code          $code for code.
	 *
	 * @param RedirectUrl   $redirect_url for redirect URL.
	 *
	 * @param SendHeaders   $send_headers for send headers.
	 *
	 * @param SendBody      $send_body for sent body.
	 */
	public function get_token( $tokenendpoint, $grant_type, $clientid, $clientsecret, $code, $redirect_url, $send_headers, $send_body ) {

		$clientsecret = html_entity_decode( $clientsecret );
		$body         = array(
			'grant_type'    => $grant_type,
			'code'          => $code,
			'client_id'     => $clientid,
			'client_secret' => $clientsecret,
			'redirect_uri'  => $redirect_url,
		);
		$headers      = array(
			'Accept'        => 'application/json',
			'charset'       => 'UTF - 8',
			'Authorization' => 'Basic ' . base64_encode( $clientid . ':' . $clientsecret ), // @phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode -- Base64 needed to generate Authorization header
			'Content-Type'  => 'application/x-www-form-urlencoded',
		);
		if ( $send_headers && ! $send_body ) {
				unset( $body['client_id'] );
				unset( $body['client_secret'] );
		} elseif ( ! $send_headers && $send_body ) {
				unset( $headers['Authorization'] );
		}

		$response = wp_remote_post(
			$tokenendpoint,
			array(
				'method'      => 'POST',
				'timeout'     => 45,
				'redirection' => 5,
				'httpversion' => '1.0',
				'blocking'    => true,
				'headers'     => $headers,
				'body'        => $body,
				'cookies'     => array(),
				'sslverify'   => false,
			)
		);
		if ( is_wp_error( $response ) ) {
			wp_die( esc_html( $response ) );
		}
		$response = $response['body'];

		if ( ! is_array( json_decode( $response, true ) ) ) {
			echo '<b>Response : </b><br>';
			print_r( esc_html( $response ) ); //phpcs:ignore
			echo '<br><br>';
			exit( 'Invalid response received.' );
		}

		$content = json_decode( $response, true );
		if ( isset( $content['error_description'] ) ) {
			exit( esc_html( $content['error_description'] ) );
		} elseif ( isset( $content['error'] ) ) {
			exit( esc_html( $content['error'] ) );
		}

		return $response;
	}

	/**
	 * Get ID Token (OpenID flow)
	 *
	 * @param Tokenendpoint $tokenendpoint for token endpoint URL.
	 *
	 * @param GrantType     $grant_type for grant type.
	 *
	 * @param ClientID      $clientid for client ID.
	 *
	 * @param ClientSecret  $clientsecret for client secret.
	 *
	 * @param code          $code for code.
	 *
	 * @param RedirectUrl   $redirect_url for redirect URL.
	 *
	 * @param SendHeaders   $send_headers for send headers.
	 *
	 * @param SendBody      $send_body for sent body.
	 */
	public function get_id_token( $tokenendpoint, $grant_type, $clientid, $clientsecret, $code, $redirect_url, $send_headers, $send_body ) {
		$response = $this->get_token( $tokenendpoint, $grant_type, $clientid, $clientsecret, $code, $redirect_url, $send_headers, $send_body );
		$content  = json_decode( $response, true );
		if ( isset( $content['id_token'] ) || isset( $content['access_token'] ) ) {
			return $content;
		} else {
			echo 'Invalid response received from OpenId Provider. Contact your administrator for more details.<br><br><b>Response : </b><br>' . esc_attr( $response );
			exit;
		}
	}

	/**
	 * Get Resource Owner from ID Token
	 *
	 * @param IDToken $id_token for ID token.
	 */
	public function get_resource_owner_from_id_token( $id_token ) {
		$id_array = explode( '.', $id_token );
		if ( isset( $id_array[1] ) ) {
			$id_body = base64_decode( $id_array[1] ); //@phpcs:ignore
			if ( is_array( json_decode( $id_body, true ) ) ) {
				return json_decode( $id_body, true );
			}
		}
		echo 'Invalid response received.<br><b>Id_token : </b>' . esc_attr( $id_token );
		exit;
	}

	/**
	 * Get Resource Owner from endpoint
	 *
	 * @param ResourceOwnerDetailsUrl $resourceownerdetailsurl for resourceownerdetailsurl.
	 *
	 * @param AccessToken             $access_token for Access Token.
	 */
	public function get_resource_owner( $resourceownerdetailsurl, $access_token ) {
		$headers                  = array();
		$headers['Authorization'] = 'Bearer ' . $access_token;

		$response = wp_remote_post(
			$resourceownerdetailsurl,
			array(
				'method'      => 'GET',
				'timeout'     => 45,
				'redirection' => 5,
				'httpversion' => '1.0',
				'blocking'    => true,
				'headers'     => $headers,
				'cookies'     => array(),
				'sslverify'   => false,
			)
		);

		if ( is_wp_error( $response ) ) {
			wp_die( esc_html( $response ) );
		}

		$response = $response['body'];

		if ( ! is_array( json_decode( $response, true ) ) ) {
			$response = addcslashes( $response, '\\' );
			if ( ! is_array( json_decode( $response, true ) ) ) {
				echo '<b>Response : </b><br>';
				print_r( esc_html( $response ) ); //phpcs:ignore
				echo '<br><br>';
				exit( 'Invalid response received.' );
			}
		}

		$content = json_decode( $response, true );
		if ( isset( $content['error_description'] ) ) {
			exit( esc_html( $content['error_description'] ) );
		} elseif ( isset( $content['error'] ) ) {
			exit( esc_attr( $content['error'] ) );
		}

		return $content;
	}

	/**
	 * Get Response
	 *
	 * @param URL $url for url.
	 */
	public function get_response( $url ) {
		$response = wp_remote_get(
			$url,
			array(
				'method'      => 'GET',
				'timeout'     => 45,
				'redirection' => 5,
				'httpversion' => 1.0,
				'blocking'    => true,
				'headers'     => array(),
				'cookies'     => array(),
				'sslverify'   => false,
			)
		);

		$content = json_decode( $response, true );
		if ( isset( $content['error_description'] ) ) {
			exit( esc_html( $content['error_description'] ) );
		} elseif ( isset( $content['error'] ) ) {
			exit( esc_html( $content['error'] ) );
		}

		return $content;
	}

}


