<?php
/**
 * MINIORANGE OAuth client apps
 *
 * @package    Admin\Partials
 */

/**
 * Admin Utils
 */
class MoCognito_Admin_Utils {

	/**
	 * Curl extension check
	 */
	public static function curl_extension_check() {
		if ( ! in_array( 'curl', get_loaded_extensions(), true ) ) {
			echo '<p style="color:red;">(Warning: <a href="http://php.net/manual/en/curl.installation.php" target="_blank">PHP CURL extension</a> is not installed or disabled. Please install/enable it before you proceed.)</p>';
		}
	}


}


