<?php
/**
 * Plugin Name: Results for Handball4All
 * Description: Show timetables with results and standings of handball teams and leagues provided by handball4all.de.
 * Version: 1.1.1
 * Author: Yannick Zipf
 * License: GPLv2 or later
 */

 // If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version. Using https://semver.org
 */
define( 'RH4A_VERSION', '1.1.1' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-h4a-activator.php
 */
function activate_results_h4a() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-rh4a-activator.php';
	RH4A_Activator::activate( RH4A_VERSION );
}
register_activation_hook( __FILE__, 'activate_results_h4a' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-rh4a.php';

/**
 * Begins execution of the plugin.
 */
function run_results_h4a() {
	$plugin = new Results_H4A();
	$plugin->run();
}
run_results_h4a();

?>
