<?php
/**
 * MINIORANGE OAuth client apps
 *
 * @package    Admin
 */

/**
 * Include
 */
require 'partials/class-mocognito-admin-menu.php';

/**
 * MO Cognito Client Admin
 */
class MoCognito_Client_Admin {

	/**
	 * Plugin Name
	 *
	 * @var PluginName $plugin_name for plugin name
	 */
	private $plugin_name;
	/**
	 * Version
	 *
	 * @var Version $version for version
	 */
	private $version;

	/**
	 * Constructor
	 *
	 * @param PluginName $plugin_name for plugin name.
	 *
	 * @param Version    $version for version.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	/**
	 * Register the stylesheets for the admin area.
	 */
	public function enqueue_styles() {
		// @phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Safe to ignore nonce here as no data operation
		if ( isset( $_REQUEST['tab'] ) && 'licensing' === $_REQUEST['tab'] ) {
			wp_enqueue_style( 'mo_oauth_bootstrap_css', plugins_url( 'css/bootstrap/bootstrap.min.css', __FILE__ ), array(), '1.5.3' );
		}

	}

	/**
	 * Register the JavaScript for the admin area.
	 */
	public function enqueue_scripts() {
		// @phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Safe to ignore nonce here as no data operation
		if ( isset( $_REQUEST['tab'] ) && 'licensing' === $_REQUEST['tab'] ) {
			wp_enqueue_script( 'mo_oauth_modernizr_script', plugins_url( 'js/modernizr.min.js', __FILE__ ), array(), '1.5.3', true );
			wp_enqueue_script( 'mo_oauth_popover_script', plugins_url( 'js/bootstrap/popper.min.js', __FILE__ ), array(), '1.5.3', true );
			wp_enqueue_script( 'mo_oauth_bootstrap_script', plugins_url( 'js/bootstrap/bootstrap.min.js', __FILE__ ), array(), '1.5.3', true );
		}
	}

	/**
	 * Admin Menu
	 */
	public function admin_menu() {

		$page = add_menu_page( 'MO OAuth Settings ' . __( 'Configure OAuth', 'mo_oauth_settings' ), MO_OAUTH_ADMIN_MENU, 'administrator', 'mo_oauth_settings', array( $this, 'menu_options' ), plugin_dir_url( __FILE__ ) . 'images/miniorange.png' );

		global $submenu;
		if ( is_array( $submenu ) && isset( $submenu['mo_oauth_settings'] ) ) {
			// @phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- Need to have compatibility with other plugin
			$submenu['mo_oauth_settings'][0][0] = __( 'Configure OAuth', 'mo_oauth_login' );
		}
	}

	/**
	 * Menu options
	 */
	public function menu_options() {
		global $wpdb;
		update_option( 'host_name', 'https://login.xecurify.com' );
		mocognito_is_customer_registered();
		mocognito_main_menu();
	}
}
