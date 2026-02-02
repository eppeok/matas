<?php
/**
 * MINIORANGE OAuth client apps
 *
 * @package    Admin\Apps
 */

/**
 * Include partials
 */
require 'partials/sign-in-settings.php';
require 'partials/customization.php';
require 'partials/addapp.php';
require 'partials/updateapp.php';
require 'partials/app-list.php';
require 'partials/attr-role-mapping.php';

/**
 * Admin apps
 */
class MoCognito_Admin_Apps {

	/**
	 * Sign in settings
	 */
	public static function sign_in_settings() {
		mocognito_sign_in_settings_ui();
	}

	/**
	 * Customization UI
	 */
	public static function customization() {
		mocognito_customization_ui();
	}

	/**
	 * App list page
	 */
	public static function applist() {
		mocognito_applist_page();
	}

	/**
	 * Add App page
	 */
	public static function add_app() {
		mocognito_add_app_page();
	}

	/**
	 * Update App page
	 *
	 * @param AppName $appname for application name.
	 */
	public static function update_app( $appname ) {
		mocognito_update_app_page( $appname );
	}

	/**
	 * Role Mapping UI
	 */
	public static function attribute_role_mapping() {
		mocognito_attribite_role_mapping_ui();
	}
}


