<?php
/**
 * MoCognito_Admin_Account Class Doc Comment
 *
 * @package    Admin\Account
 * @author     miniOrange <info@miniorange.com>
 * @license    Expat
 * @link       https://miniorange.com
 */

/**
 * Include partials
 */
require 'partials/register.php';
require 'partials/verify-password.php';

/**
 * Login form UI
 *
 * @package    Admin\Account
 * @author     miniOrange <info@miniorange.com>
 * @license    Expat
 * @link       https://miniorange.com
 */
class MoCognito_Admin_Account {

	/**
	 * Login form UI
	 *
	 * @package    Admin\Account
	 * @author     miniOrange <info@miniorange.com>
	 * @license    Expat
	 * @link       https://miniorange.com
	 */
	public static function register() {
		if ( ! mocognito_is_customer_registered() ) {
			mocognito_register_ui();
		} else {
			mocognito_show_customer_info();
		}
	}

	/**
	 * Login form UI
	 *
	 * @package    Admin\Account
	 * @author     miniOrange <info@miniorange.com>
	 * @license    Expat
	 * @link       https://miniorange.com
	 */
	public static function verify_password() {
		mocognito_verify_password_ui();
	}
}


