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

    private const DB_TABLE_TIMETABLE = "rh4a_timetable";
    private const DB_TABLE_STANDING = "rh4a_standing";

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
     * Private functions, that execute queries
     */
    private function get_items( $table ) {
        return $this->wpdb->get_results("SELECT * FROM {$this->wpdb->prefix}{$table}");
    }
    private function get_item( $ID, $table ) {
        return $this->wpdb->get_row($this->wpdb->prepare("SELECT * FROM {$this->wpdb->prefix}{$table} WHERE ID = %d", $ID), ARRAY_A);
    }
    private function save_item($item, $ID, $table ) {
        if($ID > 0) {
            // Edit
            return $this->wpdb->update("{$this->wpdb->prefix}{$table}", $item, array("ID" => $ID));
        } else {
            // Create
            return $this->wpdb->insert("{$this->wpdb->prefix}{$table}", $item);
        }
    } 
    private function delete_item( $ID, $table ) {
        return $this->wpdb->delete("{$this->wpdb->prefix}{$table}", array("ID" => $ID), array("%d"));
    }

    /**
     * Timetable related functions
     */ 
    public function get_timetables() {
        return $this->get_items( self::DB_TABLE_TIMETABLE );
    }
    public function get_timetable( $ID ) {
        return $this->get_item( $ID, self::DB_TABLE_TIMETABLE );
    }
    public function save_timetable($item, $ID = -1 ) {
        return $this->save_item( $item, $ID, self::DB_TABLE_TIMETABLE );
    }
    public function delete_timetable($ID) {
        return $this->delete_item( $ID, self::DB_TABLE_TIMETABLE );
    }

    /**
     * Standing related functions
     */
    public function get_standings() {
        return $this->get_items( self::DB_TABLE_STANDING );
    }
    public function get_standing( $ID ) {
        return $this->get_item( $ID, self::DB_TABLE_STANDING );
    }
    public function save_standing($item, $ID = -1 ) {
        return $this->save_item( $item, $ID, self::DB_TABLE_STANDING );
    }
    public function delete_standing($ID) {
        return $this->delete_item( $ID, self::DB_TABLE_STANDING );
    }

    /**
     * Called by uninstall.php
     */
    public function delete_tables() {
        $this->wpdb->query( sprintf( "DROP TABLE IF EXISTS %s", $this->wpdb->prefix . self::DB_TABLE_TIMETABLE ) );
        $this->wpdb->query( sprintf( "DROP TABLE IF EXISTS %s", $this->wpdb->prefix . self::DB_TABLE_STANDING ) );
    }
}
