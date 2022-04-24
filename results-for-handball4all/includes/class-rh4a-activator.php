<?php

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation and provides an update mechanism.
 *
 * @package    results-h4a
 * @subpackage results-h4a/includes
 * @author     Yannick Zipf <yannick.zipf@icloud.com>
 */

class RH4A_Activator {

	/**
	 * Called on plugin activation.
	 * Calls the process_database function.
	 */
	public static function activate( $version ) {
        self::process_database( $version );
	}

    /**
	 * Called on plugin update.
	 * Calls the process_database function.
	 */
	public static function update( $version ) {
        self::process_database( $version );
	}

    /**
	 * Called by the action or update method.
	 * Creates or updates the database.
	 */
    private static function process_database( $version ) {

        // Set rh4a_options for the first time
        $rh4a_options = get_option( "rh4a_options" );
        if(false === $rh4a_options) {
            // Options are not in the database yet -> create with default values
            add_option("rh4a_options", array('rh4a_cache_active' => 1, 'rh4a_default_css_active' => 1));
        }

        $installed_version = get_option( "rh4a_version" );
        if ( $installed_version !== $version ) {

            global $wpdb;
            $charset_collate = '';
            if ( ! empty($wpdb->charset) )
                $charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
            if ( ! empty($wpdb->collate) )
                $charset_collate .= " COLLATE $wpdb->collate";

            $sql = "
                CREATE TABLE {$wpdb->prefix}rh4a_timetable (
                    ID smallint NOT NULL AUTO_INCREMENT,
                    description varchar(255) NOT NULL,
                    status char(1) NOT NULL DEFAULT '1',
                    type char(1) NOT NULL,
                    objkey varchar(10) NOT NULL,
                    highlight varchar(255) NULL,
                    PRIMARY KEY  (ID)
                ) $charset_collate;
                CREATE TABLE {$wpdb->prefix}rh4a_standing (
                    ID smallint NOT NULL AUTO_INCREMENT,
                    description varchar(255) NOT NULL,
                    status char(1) NOT NULL DEFAULT '1',
                    objkey varchar(10) NOT NULL,
                    highlight varchar(255) NULL,
                    invisible_columns varchar(255) NULL,
                    PRIMARY KEY  (ID)
                ) $charset_collate;
                CREATE TABLE {$wpdb->prefix}rh4a_next_match (
                    ID smallint NOT NULL AUTO_INCREMENT,
                    description varchar(255) NOT NULL,
                    status char(1) NOT NULL DEFAULT '1',
                    objkey varchar(255) NOT NULL,
                    PRIMARY KEY  (ID)
                ) $charset_collate;
            ";

            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($sql);

            // Check for additional, version specific updates
            
            // Updating to 1.1.0 -> Delete transients, because the transients are used differently from now on.
            // The whole http json reponse is now stored in the transient and not only the dataList attribute.
            if(version_compare('1.1.0', $installed_version)) {
                $db = new RH4A_DB();
                $db->delete_transients();
            }  

            // Updating to 1.0.2
            // if(version_compare('1.0.1', $installed_version)) {
            //     // Do specific database updates that the dbDelta function doesn't cover, e.g. deleting columns
            //     $wpdb->query(
            //         "ALTER TABLE {$wpdb->prefix}my_table
            //         ADD COLUMN `count` SMALLINT(6) NOT NULL
            //         ");
            //     // Set default values for new options
            //     $rh4a_options['new_option_name'] = "default_value";
            //     update_option("rh4a_options", $rh4a_options);
            // }

            update_option( "rh4a_version", $version );

        }
    }

}
