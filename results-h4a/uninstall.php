<?php

/**
 * "Magic" file which is only called when the uninstall process 
 * is invoked for this plugin.
 *
 * @package    results-h4a
 * @author     Yannick Zipf <yannick.zipf@icloud.com>
 */

if (!defined('WP_UNINSTALL_PLUGIN')) {
    die;
}

require_once __DIR__ . '/includes/class-rh4a-database.php';
require_once __DIR__ . '/includes/class-rh4a-http-helper.php';

$db = new RH4A_DB();
$http = new RH4A_HTTP_Helper();

// Delete options
delete_option( 'rh4a_options' );
delete_option( 'rh4a_version' );

// Delete transients, if any
$timetables = $db->get_timetables();
foreach($timetables as $timetable) {
    delete_transient($http->get_transient_name($timetable->type, $timetable->objkey));
}

$standings = $db->get_standings();
foreach($standings as $standing) {
    delete_transient($http->get_transient_name($this->http::STANDING, $standing->objkey));
}

// Delete tables
$db->delete_tables();
