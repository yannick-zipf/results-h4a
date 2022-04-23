<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @package    results-h4a
 * @subpackage results-h4a/includes
 * @author     Yannick Zipf <yannick.zipf@icloud.com>
 */

class Results_H4A {

	protected $loader;
	protected $db;
	protected $http;
	protected $plugin_name;
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 */
	public function __construct() {
		if ( defined( 'RH4A_VERSION' ) ) {
			$this->version = RH4A_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'results-h4a';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 * Create an instance of the db which will be used in the admin and public area.
	 * Create an instance of the http helper which wille be used in the public area.
	 *
	 */
	private function load_dependencies() {
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-rh4a-loader.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-rh4a-activator.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-rh4a-admin.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-rh4a-public.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-rh4a-database.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-rh4a-http-helper.php';
		$this->loader = new RH4A_Loader();
		$this->db = new RH4A_DB();
		$this->http = new RH4A_HTTP_Helper();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 */
	private function set_locale() {
		load_plugin_textdomain( 'results-h4a', false, dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/' );
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 */
	private function define_admin_hooks() {
		$plugin_admin = new Results_H4A_Admin( $this->get_plugin_name(), $this->get_version(), $this->db );
		$this->loader->add_action( 'plugins_loaded', $plugin_admin, 'update_plugin' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'register_admin_menu' );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'register_settings' );
		$this->loader->add_action( 'wp_ajax_delete_item', $plugin_admin, 'delete_item' );
		$this->loader->add_action( 'wp_ajax_change_status', $plugin_admin, 'change_status' );
		// $this->loader->add_action( 'admin_notices', $plugin_admin, 'show_notices' );
		// $this->loader->add_action( 'admin_init', $plugin_admin, 'redirect' );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 */
	private function define_public_hooks() {
		$plugin_public = new Results_H4A_Public( $this->get_plugin_name(), $this->get_version(), $this->db, $this->http );
		$this->loader->add_action( 'init', $plugin_public, 'shortcodes_init' );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		// $this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 */
	public function run() {
		$this->loader->run();
	}

	public function get_plugin_name() {
		return $this->plugin_name;
	}
	
	public function get_version() {
		return $this->version;
	}

}
