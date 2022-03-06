<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @package    results-h4a
 * @subpackage results-h4a/admin
 * @author     Yannick Zipf <yannick.zipf@icloud.com>
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-rh4a-general-settings.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-rh4a-shortcode-abstract.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-rh4a-admin-timetable.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-rh4a-admin-standing.php';

class Results_H4A_Admin {

	private $plugin_name;
	private $version;
	private $RH4A_General_Settings;
	private $RH4A_Admin_Timetable;
	private $RH4A_Admin_Standing;
	private $db;

	/**
	 * Initialize the class and set its properties.
	 */
	public function __construct( $plugin_name, $version, $db ) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->db = $db;
		$this->RH4A_General_Settings = new RH4A_Admin_General_Settings();
		$this->RH4A_Admin_Timetable = new RH4A_Admin_Timetable($db);
		$this->RH4A_Admin_Standing = new RH4A_Admin_Standing($db);
	}

    /**
	 * Register the update mechanism, called by hook 'plugins_loaded'. Important, because 
	 * when updating plugins via the auto-update mechanism, the activation hook is not called. 
	 * So no update would take place. That's why we call the update functionality every time 
	 * and compare it with the current installed version.
	 */
    public function update_plugin() {
        RH4A_Activator::update( $this->version );
    }

    /**
	 * Register the RH4A Settings Pages in the Admin menu, called by hook 'admin_menu'.
	 */
	public function register_admin_menu() {
		$this->RH4A_General_Settings->add_menu_page();
		$this->RH4A_Admin_Timetable->add_menu_page();
		$this->RH4A_Admin_Standing->add_menu_page();
	}

	/**
	 * Register the RH4A Settings, called by hook 'admin_init'.
	 */
	public function register_settings() {
		$this->RH4A_General_Settings->register_settings();
	}

	/**
	 * Show messages in the admin section. Static function called from everywhere.
	 */
	public static function show_msg($content, $type, $linktext = '', $linkhref = '') {
        echo '<div class="notice notice-' . $type . ' is-dismissible"><p>' . $content;
		if("" !== $linktext && "" !== $linkhref) {
			echo  ' <a href="'.$linkhref.'">'.$linktext.'</a>';
		}
		echo '</p></div>';
    }

	/**
	 * Register the JavaScript for the admin area.
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/rh4a-admin.js', array( 'jquery' ), $this->version, false );
		wp_localize_script(
			$this->plugin_name, 
			'oRH4A', 
			array(
				'ajax_url' => admin_url( 'admin-ajax.php' ), 
				'nonce' => wp_create_nonce( 'rh4a-ajax-action' )
			)
		);
	}
	
	/**
	 * Public ajax handler for deleting an item. Will call the deletion function on the correct handler.
	 */
	public function delete_item() {
		check_ajax_referer( 'rh4a-ajax-action' );
		$handler = $this->get_item_handler(sanitize_key($_POST['item_type']));
		if($handler) {
			wp_send_json( $handler->delete_item(sanitize_key($_POST['item_id'])) );
		} else {
			wp_send_json( __("No handler for item type found.", "results-h4a") );
		}
	}

	/**
	 * Public ajax handler for changing the status of an item. Will call the edit item function on the correct handler.
	 */
	public function change_status() {
		check_ajax_referer( 'rh4a-ajax-action' );
		$handler = $this->get_item_handler(sanitize_key($_POST['item_type']));
		if($handler) {
			wp_send_json( $handler->edit_item(array("status" => sanitize_key($_POST['item_new_status'])), sanitize_key($_POST['item_id'])) );
		} else {
			wp_send_json( __("No handler for item type found.", "results-h4a") );
		}
	}

	/**
	 * Internal method for finding the correct handler for ajax requests for the given item type.
	 */
	private function get_item_handler($item_type) {
		$handler = null;
		switch($item_type) {
			case "timetable":
				$handler = $this->RH4A_Admin_Timetable;
				break;
			case "standing":
				$handler = $this->RH4A_Admin_Standing;
				break;
		}
		return $handler;
	}

	/**
	 * Register the stylesheets for the admin area.
	 */
	public function enqueue_styles() {
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/rh4a-admin.css', array(), $this->version, 'all' );
	}

	// private const TRANSIENT_NOTICES = "rh4a_notices";
	// private const TRANSIENT_REDIRECT = "rh4a_notices";

	// public function show_notices() {
	// 	$notices = get_transient(self::TRANSIENT_NOTICES);
	// 	if(is_array($notices) && count($notices) > 0) {
	// 		foreach($notices as $notice) {
	// 			self::show_msg($notice['content'], $notice['type']);
	// 		}
	// 		set_transient(self::TRANSIENT_NOTICES, [], MONTH_IN_SECONDS);
	// 	}
	// }
	// public static function set_notice($content = '', $type = 'success') {
	// 	$notices = get_transient(self::TRANSIENT_NOTICES);
	// 	if("" === $notices or 0 === count($notices)) {
	// 		$notices = [];
	// 	}
	// 	$notices[] = array(
	// 		"content" => $content,
	// 		"type" => $type
	// 	);
	// 	set_transient(self::TRANSIENT_NOTICES, $notices, MONTH_IN_SECONDS);
	// }

	// public static function set_redirect($url) {
	// 	set_transient(self::TRANSIENT_REDIRECT, $url);
	// }
    // public function redirect() {
	// 	$url = get_transient(self::TRANSIENT_REDIRECT);
	// 	delete_transient(self::TRANSIENT_REDIRECT);
	// 	if(isset($url) && !empty($url)) {
    //     	wp_safe_redirect($url);
	// 		exit;
	// 	}
    // }
}
