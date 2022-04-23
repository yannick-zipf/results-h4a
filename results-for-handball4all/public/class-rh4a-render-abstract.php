<?php

/**
 * Abstract base class for the renderers of the different shortcode types.
 *
 * @package    results-h4a
 * @subpackage results-h4a/public
 * @author     Yannick Zipf <yannick.zipf@icloud.com>
 */

abstract class RH4A_Render_Abstract {

    protected $db;
    protected $http;
    protected $ID;
    protected $item;

    abstract protected function get_item();
    abstract protected function render_item_specific();

    /**
     * Constructor loads item on instantiation.
     */
    public function __construct($ID, $db, $http) {
        $this->db = $db;
        $this->http = $http;
        $this->ID = $ID;
        $this->item = $this->get_item();
    }

    /**
     * Returns 1 if active and 0 if inactive.
     */
    private function get_item_status() {
        return intval($this->item['status']);
    }

    /** 
     * Gets called when shortcode is executed.
     */
    public function render_item() {
        if(is_null($this->item)) {
            return '';
        }
        if(1 === $this->get_item_status()) {
            return $this->render_item_specific();
        } else {
            return "<!-- Shortcode with ID found, but it is inactive -->";
        }
    }

}
