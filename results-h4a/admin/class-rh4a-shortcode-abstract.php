<?php

 /**
 * An abstract class for the shortcode admin pages.
 * Includes basic functionalities and admin area mechanisms.
 *
 * @package    results-h4a
 * @subpackage results-h4a/admin
 * @author     Yannick Zipf <yannick.zipf@icloud.com>
 */

abstract class RH4A_Shortcode_Abstract {

    protected $db;
    protected $nonce;
    protected $menu_slug;

    public function __construct($db) {
        $this->db = $db;
    }

    abstract protected function render_form($values = array());
    abstract protected function verify_item();
    abstract protected function prepare_item();
    abstract protected function create_item($item);
    abstract public    function edit_item($item, $ID);
    abstract protected function get_item($ID);
    abstract public    function delete_item($ID);

    public function render_html() {
        // check user capabilities
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
        ?>
        <div class="wrap">
            <h1 class="wp-heading-inline"><?php echo esc_html( get_admin_page_title() ); ?></h1>
        <?php
            if(isset($_GET['action']) && 'edit' === $_GET['action'] && isset($_GET['id']) && "" !== intval($_GET['id'])) {
                // Show form for editable item
                if(isset($_POST[$this->nonce]) && wp_verify_nonce($_POST[$this->nonce], 'new-edit')) {
                    // Process edited item
                    if($this->verify_item()) {
                        // Save edited item
                        if($this->edit_item($this->prepare_item(), $_GET['id'])) {
                            Results_H4A_Admin::show_msg(__('The item was edited successfully.', 'results-h4a'), "success", __('Back to overview', 'results-h4a'), menu_page_url($this->menu_slug, false));
                        } else {
                            Results_H4A_Admin::show_msg(__('That didn\'t work as espected. Please try again.', 'results-h4a'), "error");
                            $this->render_form($this->prepare_item());
                        }
                    } else {
                        // Show form for corrections with sent data
                        $this->render_form($this->prepare_item());
                    }
                } else {
                    // Show form with loaded content
                    $this->render_form($this->get_item($_GET['id']));
                }
            } else if(isset($_GET['action']) && 'new' === $_GET['action']) {
                if(isset($_POST[$this->nonce]) && wp_verify_nonce($_POST[$this->nonce], 'new-edit')) {
                    // Process new item
                    if($this->verify_item()) {
                        // Save new item
                        if($this->create_item($this->prepare_item())) {
                            Results_H4A_Admin::show_msg(__('The item was created successfully.', 'results-h4a'), "success", __('Back to overview', 'results-h4a'), menu_page_url($this->menu_slug, false));
                        } else {
                            Results_H4A_Admin::show_msg(__('That didn\'t work as espected. Please try again.', 'results-h4a'), "error");
                            $this->render_form();
                        }
                    } else {
                        // Show form for corrections with sent data
                        $this->render_form($this->prepare_item());
                    }  
                } else {
                    // Show empty form for new item
                    $this->render_form();
                }
            } else {
                // Show all items
                echo '<a href="admin.php?page='.$this->menu_slug.'&action=new" class="page-title-action">' . __('Add new', 'results-h4a') . '</a>';
                $this->render_list();
            }
        ?>
        </div>
        <?php
    }

    protected function get_status_text($status) {
        return $status == 1 ? __('Active', 'results-h4a') : __('Inactive', 'results-h4a');
    }

}
