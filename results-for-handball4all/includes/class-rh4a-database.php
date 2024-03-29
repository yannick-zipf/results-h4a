<?php

/**
 * Central DB class for all accesses to the database.
 *
 * @package    results-h4a
 * @subpackage results-h4a/includes
 * @author     Yannick Zipf <yannick.zipf@icloud.com>
 */

class RH4A_DB {
    private $wpdb; // $wpdb object
    private $cc; // charset & collate

    public function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;

        // Currently charset and collate are not needed here because the tables are created in the RH4A_Activator class
        $charset_collate = '';
        if ( ! empty($wpdb->charset) ) $charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
        if ( ! empty($wpdb->collate) ) $charset_collate .= " COLLATE $wpdb->collate";
        $this->cc = $charset_collate;
    }

    /**
     * Timetable related functions
     */ 
    public function get_timetables() {
        return $this->wpdb->get_results("SELECT * FROM {$this->wpdb->prefix}rh4a_timetable");
    }
    public function get_timetable( $ID ) {
        return $this->wpdb->get_row($this->wpdb->prepare("SELECT * FROM {$this->wpdb->prefix}rh4a_timetable WHERE ID = %d", $ID), ARRAY_A);
    }
    public function save_timetable($item, $ID = -1 ) {
        if($ID > 0) {
            // Edit
            return $this->wpdb->update("{$this->wpdb->prefix}rh4a_timetable", $item, array("ID" => $ID));
        } else {
            // Create
            return $this->wpdb->insert("{$this->wpdb->prefix}rh4a_timetable", $item);
        }
    }
    public function delete_timetable($ID) {
        return $this->wpdb->delete("{$this->wpdb->prefix}rh4a_timetable", array("ID" => $ID), array("%d"));
    }

    /**
     * Standing related functions
     */
    public function get_standings() {
        return $this->wpdb->get_results("SELECT * FROM {$this->wpdb->prefix}rh4a_standing");
    }
    public function get_standing( $ID ) {
        return $this->wpdb->get_row($this->wpdb->prepare("SELECT * FROM {$this->wpdb->prefix}rh4a_standing WHERE ID = %d", $ID), ARRAY_A);
    }
    public function save_standing($item, $ID = -1 ) {
        if($ID > 0) {
            // Edit
            return $this->wpdb->update("{$this->wpdb->prefix}rh4a_standing", $item, array("ID" => $ID));
        } else {
            // Create
            return $this->wpdb->insert("{$this->wpdb->prefix}rh4a_standing", $item);
        }
    }
    public function delete_standing($ID) {
        return $this->wpdb->delete("{$this->wpdb->prefix}rh4a_standing", array("ID" => $ID), array("%d"));
    }
    /**
     * Next Match related functions
     */
    public function get_next_matches() {
        return $this->wpdb->get_results("SELECT * FROM {$this->wpdb->prefix}rh4a_next_match");
    }
    public function get_next_match( $ID ) {
        return $this->wpdb->get_row($this->wpdb->prepare("SELECT * FROM {$this->wpdb->prefix}rh4a_next_match WHERE ID = %d", $ID), ARRAY_A);
    }
    public function save_next_match($item, $ID = -1 ) {
        if($ID > 0) {
            // Edit
            return $this->wpdb->update("{$this->wpdb->prefix}rh4a_next_match", $item, array("ID" => $ID));
        } else {
            // Create
            return $this->wpdb->insert("{$this->wpdb->prefix}rh4a_next_match", $item);
        }
    }
    public function delete_next_match($ID) {
        return $this->wpdb->delete("{$this->wpdb->prefix}rh4a_next_match", array("ID" => $ID), array("%d"));
    }

    /**
     * Called by uninstall.php
     */
    public function delete_tables() {
        $this->wpdb->query( "DROP TABLE IF EXISTS {$this->wpdb->prefix}rh4a_timetable" );
        $this->wpdb->query( "DROP TABLE IF EXISTS {$this->wpdb->prefix}rh4a_standing" );
        $this->wpdb->query( "DROP TABLE IF EXISTS {$this->wpdb->prefix}rh4a_next_match" );
    }

    /**
     * Called by uninstall.php, the activator class and the general settings admin page.
     * @since 1.1.1
     * Before, the delete_transients method was placed in the http helper class. There it loaded
     * all items from the database and deleted the transients with the built-in method delete_transient.
     * With v1.1.0 next matches were introduced which load not only the data for a team but also for the
     * corresponding league in the background (LeagueID came from the team data).
     * So to delete these transients as well, you would have to read the data again first and then delete
     * them one by one, because the LeagueID is not in the db. 
     * Using the direct db call is much faster, although not the preferred way of doing this.
     */
    public function delete_transients() {
        $this->wpdb->query( "DELETE FROM {$this->wpdb->prefix}options WHERE option_name LIKE ('%\_transient\_rh4a\_%') OR option_name LIKE ('%\_transient\_timeout\_rh4a\_%')" );
    }
}
