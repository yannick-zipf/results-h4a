<?php

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    results-h4a
 * @subpackage results-h4a/public
 * @author     Yannick Zipf <yannick.zipf@icloud.com>
 */

 // If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-rh4a-render-abstract.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-rh4a-render-timetable.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-rh4a-render-standing.php';

class Results_H4A_Public {

	private $plugin_name;
	private $version;
	private $renderer;
	private $db;
	private $http;

	public function __construct( $plugin_name, $version, $db, $http ) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->db = $db;
		$this->http = $http;
	}

    /**
	 * Initialize the main shortcode rh4a
	 */
    public function shortcodes_init() {
        add_shortcode('rh4a-timetable', [$this, 'rh4a_shortcode']);
		add_shortcode('rh4a-standing', [$this, 'rh4a_shortcode']);
    }
    public function rh4a_shortcode($atts, $content, $tag) {
		// ID set?
		$atts = array_change_key_case( (array) $atts, CASE_LOWER );
		if(!isset($atts[0]) or empty($atts[0])) {
			// throw error and return
			return '<!-- ID is missing -->';
		}

		// Which object?
		switch($tag) {
			case "rh4a-timetable":
				$this->renderer = new RH4A_Render_Timetable($atts[0], $this->db, $this->http);
				break;
			case "rh4a-standing":
				$this->renderer = new RH4A_Render_Standing($atts[0], $this->db, $this->http);
				break;
			default:
				// cannot happen, because if shortcode is not recognized, this function would not be run
				return "";
				break;
		}

		// Render item
		return $this->renderer->render_item();
    }

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 */
	public function enqueue_styles() {
		$options = get_option('rh4a_options');
		$css_active = $options['rh4a_default_css_active'];
		if(isset($css_active) && intval($css_active)) {
			wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/rh4a-public.css', array(), $this->version, 'all' );
		}
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 */
	// public function enqueue_scripts() {
		// wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/plugin-name-public.js', array( 'jquery' ), $this->version, false );
	// }

}
