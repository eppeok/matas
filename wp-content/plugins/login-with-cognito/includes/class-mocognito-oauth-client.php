<?php
/**
 * MINIORANGE OAuth Includes
 *
 * @package    MoCognito\Includes
 */

/**
 * OAuth client
 */
class MoCognito_OAuth_Client {

	/**
	 * Loader
	 *
	 * @var Loader $loader for loader
	 */
	protected $loader;

	/**
	 * Plugin Name
	 *
	 * @var PluginName $plugin_name for plugin name
	 */
	protected $plugin_name;

	/**
	 * Version
	 *
	 * @var Version $version for version
	 */
	protected $version;

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->plugin_name = 'miniOrange ' . MO_OAUTH_PLUGIN_NAME;
		$this->version     = '1.0.1';
		$this->load_dependencies();
		$this->define_admin_hooks();
	}


	/**
	 * Load dependencies
	 */
	private function load_dependencies() {
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-mocognito-loader.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-mocognito-client-admin.php';
		$this->loader = new MoCognito_Loader();
	}


	/**
	 * Define Admin Hooks
	 */
	private function define_admin_hooks() {
		$plugin_admin = new MoCognito_Client_Admin( $this->get_plugin_name(), $this->get_version() );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'admin_menu' );
		$this->loader->add_action( 'admin_enqueue_scripts', '', 'mocognito_plugin_settings_style' );
		$this->loader->add_action( 'admin_enqueue_scripts', '', 'mocognito_plugin_settings_script' );
	}


	/**
	 * Run
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * Get Plugin Name
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * Get Loader
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Get version
	 */
	public function get_version() {
		return $this->version;
	}

}
