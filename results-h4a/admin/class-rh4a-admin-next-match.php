<?php

/**
 * The class responsible for the admin page of type "Next Match".
 *
 * @package    results-h4a
 * @subpackage results-h4a/admin
 * @author     Yannick Zipf <yannick.zipf@icloud.com>
 */

class RH4A_Admin_NextMatch extends RH4A_Shortcode_Abstract {

    protected $nonce = "nonce_rh4a_next_match";
    protected $menu_slug = "rh4a-next-match";

    public function add_menu_page() {
        add_submenu_page(
            'rh4a',
			__('Results H4A - Next Match', 'results-h4a'),
			__('RH4A Next Match', 'results-h4a'),
			'manage_options',
			$this->menu_slug,
            array( $this , 'render_html' )
		);
    }

    public function render_list() {
        $next_matches = $this->db->get_next_matches();
?>
        <table class="wp-list-table widefat fixed striped table-view-list rh4a-mt-1">
            <tr>
                <th><?php _e('Shortcode', 'results-h4a'); ?></th>
                <th><?php _e('Description', 'results-h4a'); ?></th>
                <th><?php _e('ID(s)', 'results-h4a'); ?></th>
                <th><?php _e('Status', 'results-h4a'); ?></th>
                <th><?php _e('Edit', 'results-h4a'); ?></th>
            </tr>
<?php
        foreach($next_matches as $next_match) {
?>
            <tr>
                <td><code>[rh4a-next-match <?php echo esc_html($next_match->ID); ?>]</code></td>
                <td><?php echo esc_html($next_match->description); ?></td>
                <td><?php echo esc_html($next_match->objkey); ?></td>
                <td>
                    <span class="rh4a-status-text"><?php echo $this->get_status_text(esc_html($next_match->status)); ?></span>
                    <a  data-item-type="standing" 
                        data-item-id="<?php esc_html_e($next_match->ID); ?>" 
                        data-item-new-status="<?php echo esc_html($next_match->status) == 1 ? 0 : 1; ?>" 
                        data-item-status-text-active="<?php echo $this->get_status_text(1); ?>" 
                        data-item-status-text-inactive="<?php echo $this->get_status_text(0); ?>" 
                        class="rh4a-change-status-link">
                        <span class="dashicons dashicons-<?php echo esc_html($next_match->status) == 1 ? "hidden" : "visibility"; ?>"></span>
                    </a>
                </td>
                <td>
                    <a href="admin.php?page=<?php echo $this->menu_slug; ?>&action=edit&id=<?php esc_html_e($next_match->ID); ?>" class="button button-primary"><?php _e('Edit', 'results-h4a'); ?></a>
                    <a data-item-type="next_match" data-item-id="<?php esc_html_e($next_match->ID); ?>" class="button rh4a-delete-item-link"><?php _e('Delete', 'results-h4a'); ?></a>
                </td>
            </tr>
<?php
        }
?>
        </table>  
<?php
    }

    /**
	 * Render form for creating a new or editing an existing standing.
	 */
    public function render_form($values = array()) {
        if(0 === count($values)) {
            $values = $this->get_defaults();
        }
?>
        <form method="post">
            <?php wp_nonce_field('new-edit', $this->nonce); ?>
            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row"><label for="rh4a_next_match_description"><?php _e('Description', 'results-h4a'); ?></label></th>
                    <td>
                        <input id="rh4a_next_match_description" name="rh4a_next_match_description" type="text" value="<?php echo esc_html($values['description']); ?>" maxlength="255" class="regular-text">
                        <p class="description"><?php _e('The description is only for your reference. It will not be shown on the frontend.', 'results-h4a'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="rh4a_next_match_objkey"><?php _e('ID', 'results-h4a'); ?></label></th>
                    <td>
                        <input id="rh4a_next_match_objkey" name="rh4a_next_match_objkey" type="text" value="<?php echo esc_html($values['objkey']); ?>" class="regular-text">
                        <p class="description">
                            <?php _e('Supply single TeamID or multiple commma-sparated TeamIDs.', 'results-h4a'); ?><br><br>
                            <?php _e('Visit www.handball4all.de and navigate via the links on the page to the site that shows the data you want to integrate into WordPress.', 'results-h4a'); ?><br><br>
                            <strong><?php _e('Examples:', 'results-h4a'); ?></strong><br>
                            <?php _e('- Next matches for Team: https://www.handball4all.de/home/portal/bhv#/league?ogId=35&lId=67731&tId=720111', 'results-h4a'); ?><br><br>
                            <?php _e('From the URL you can see the corresponding IDs:', 'results-h4a'); ?><br>
                            <?php _e('- tId = Team ID (e.g. 720111)', 'results-h4a'); ?><br>
                        </p>
                    </td>
                </tr>
            </table>
<?php
        submit_button( __('Save Next Match', 'results-h4a') );
?>
        </form>
<?php
    }

    protected function verify_item() {
        $error = false;
        $desc = sanitize_text_field($_POST['rh4a_next_match_description']);
        // Here sanitize_text_field instead of sanitize_key, because of multiple comma-separated IDs.
        $objkey = sanitize_text_field($_POST['rh4a_next_match_objkey']);
        if(!isset($desc) or empty($desc)) {
            $error = true;
            Results_H4A_Admin::show_msg(__('Please fill a description.', 'results-h4a'), "error");
        }
        if(!isset($objkey) or empty($objkey)) {
            $error = true;
            Results_H4A_Admin::show_msg(__('Please provide a valid ID.', 'results-h4a'), "error");
        }
        return !$error;
    }

    protected function prepare_item() {
        return array(
            "description" => sanitize_text_field($_POST['rh4a_next_match_description']),
            // Here sanitize_text_field instead of sanitize_key, because of multiple comma-separated IDs.
            "objkey" => sanitize_text_field($_POST['rh4a_next_match_objkey'])
        );
    }

    protected function create_item($item) {
        return $this->db->save_next_match($item);
    }

    /**
	 * Public because of ajax (otherwise protected)
	 */
    public function edit_item($item, $ID) {
        // ID mit an die DB geben, damit update und nicht insert
        return $this->db->save_next_match($item, $ID);
    }

    protected function get_item($ID) {
        return $this->db->get_next_match($ID);
    }

    /**
	 * Public because of ajax (otherwise protected)
	 */
    public function delete_item($ID) {
        return $this->db->delete_next_match($ID);
    }

    private function get_defaults() {
        return array(
            "description" => "",
            "objkey" => ""
        );
    }

}
