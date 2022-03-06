<?php

/**
 * The class responsible for the admin page of type "Standing".
 *
 * @package    results-h4a
 * @subpackage results-h4a/admin
 * @author     Yannick Zipf <yannick.zipf@icloud.com>
 */

class RH4A_Admin_Standing extends RH4A_Shortcode_Abstract {

    protected $nonce = "nonce_rh4a_standing";
    protected $menu_slug = "rh4a-standing";

    public function add_menu_page() {
        add_submenu_page(
            'rh4a',
			__('Results H4A - Standings', 'results-h4a'),
			__('RH4A Standings', 'results-h4a'),
			'manage_options',
			$this->menu_slug,
            array( $this , 'render_html' )
		);
    }

    public function render_list() {
        $standings = $this->db->get_standings();
?>
        <table class="wp-list-table widefat fixed striped table-view-list rh4a-mt-1">
            <tr>
                <th><?php _e('Shortcode', 'results-h4a'); ?></th>
                <th><?php _e('Description', 'results-h4a'); ?></th>
                <th><?php _e('ID', 'results-h4a'); ?></th>
                <th><?php _e('Highlight', 'results-h4a'); ?></th>
                <th><?php _e('Status', 'results-h4a'); ?></th>
                <th><?php _e('Edit', 'results-h4a'); ?></th>
            </tr>
<?php
        foreach($standings as $standing) {
?>
            <tr>
                <td><code>[rh4a-standing <?php echo esc_html($standing->ID); ?>]</code></td>
                <td><?php echo esc_html($standing->description); ?></td>
                <td><?php echo esc_html($standing->objkey); ?></td>
                <td><?php echo esc_html($standing->highlight); ?></td>
                <td>
                    <span class="rh4a-status-text"><?php echo $this->get_status_text(esc_html($standing->status)); ?></span>
                    <a  data-item-type="standing" 
                        data-item-id="<?php esc_html_e($standing->ID); ?>" 
                        data-item-new-status="<?php echo esc_html($standing->status) == 1 ? 0 : 1; ?>" 
                        data-item-status-text-active="<?php echo $this->get_status_text(1); ?>" 
                        data-item-status-text-inactive="<?php echo $this->get_status_text(0); ?>" 
                        class="rh4a-change-status-link">
                        <span class="dashicons dashicons-<?php echo esc_html($standing->status) == 1 ? "hidden" : "visibility"; ?>"></span>
                    </a>
                </td>
                <td>
                    <a href="admin.php?page=<?php echo $this->menu_slug; ?>&action=edit&id=<?php esc_html_e($standing->ID); ?>" class="button button-primary"><?php _e('Edit', 'results-h4a'); ?></a>
                    <a data-item-type="standing" data-item-id="<?php esc_html_e($standing->ID); ?>" class="button rh4a-delete-item-link"><?php _e('Delete', 'results-h4a'); ?></a>
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
        $inv_col_matches = 0;
        $inv_col_wins = 0;
        $inv_col_ties = 0;
        $inv_col_losses = 0;
        $inv_col_goals = 0;
        $inv_col_difference = 0;
        if(0 === count($values)) {
            $values = $this->get_defaults();
        }
        $inv_cols = unserialize($values['invisible_columns']);
        if(null !== $inv_cols && is_array($inv_cols)) {
            if(isset($inv_cols['matches']))     $inv_col_matches = $inv_cols['matches'];
            if(isset($inv_cols['wins']))        $inv_col_wins = $inv_cols['wins'];
            if(isset($inv_cols['ties']))        $inv_col_ties = $inv_cols['ties'];
            if(isset($inv_cols['losses']))      $inv_col_losses = $inv_cols['losses'];
            if(isset($inv_cols['goals']))       $inv_col_goals = $inv_cols['goals'];
            if(isset($inv_cols['difference']))  $inv_col_difference = $inv_cols['difference'];
        }

?>
        <form method="post">
            <?php wp_nonce_field('new-edit', $this->nonce); ?>
            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row"><label for="rh4a_standing_description"><?php _e('Description', 'results-h4a'); ?></label></th>
                    <td><input id="rh4a_standing_description" name="rh4a_standing_description" type="text" value="<?php echo esc_html($values['description']); ?>" maxlength="255" class="regular-text"></td>
                </tr>
                <tr>
                    <th scope="row"><label for="rh4a_standing_objkey"><?php _e('League ID', 'results-h4a'); ?></label></th>
                    <td>
                        <input id="rh4a_standing_objkey" name="rh4a_standing_objkey" type="text" value="<?php echo esc_html($values['objkey']); ?>" class="regular-text">
                        <p class="description"><?php _e('Supply LeagueID.', 'results-h4a'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="rh4a_standing_highlight"><?php _e('Team name (Optional)', 'results-h4a'); ?></label></th>
                    <td>
                        <input id="rh4a_standing_highlight" name="rh4a_standing_highlight" type="text" value="<?php echo esc_html($values['highlight']); ?>" class="regular-text">
                        <p class="description"><?php _e('Supply team name to highlight your team.', 'results-h4a'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="rh4a_standing_invisible_columns"><?php _e('Invisible Columns (Optional)', 'results-h4a'); ?></label></th>
                    <td>
                        <input id="rh4a_standing_invisible_columns_matches" name="rh4a_standing_invisible_columns_matches" type="checkbox" value="1" <?php checked(1,$inv_col_matches); ?>><label for="rh4a_standing_invisible_columns_matches"><?php _e('Matches', 'results-h4a'); ?></label><br>
                        <input id="rh4a_standing_invisible_columns_wins" name="rh4a_standing_invisible_columns_wins" type="checkbox" value="1" <?php checked(1,$inv_col_wins); ?>><label for="rh4a_standing_invisible_columns_wins"><?php _e('Wins', 'results-h4a'); ?></label><br>
                        <input id="rh4a_standing_invisible_columns_ties" name="rh4a_standing_invisible_columns_ties" type="checkbox" value="1" <?php checked(1,$inv_col_ties); ?>><label for="rh4a_standing_invisible_columns_ties"><?php _e('Ties', 'results-h4a'); ?></label><br>
                        <input id="rh4a_standing_invisible_columns_losses" name="rh4a_standing_invisible_columns_losses" type="checkbox" value="1" <?php checked(1,$inv_col_losses); ?>><label for="rh4a_standing_invisible_columns_losses"><?php _e('Losses', 'results-h4a'); ?></label><br>
                        <input id="rh4a_standing_invisible_columns_goals" name="rh4a_standing_invisible_columns_goals" type="checkbox" value="1" <?php checked(1,$inv_col_goals); ?>><label for="rh4a_standing_invisible_columns_goals"><?php _e('Goals', 'results-h4a'); ?></label><br>
                        <input id="rh4a_standing_invisible_columns_difference" name="rh4a_standing_invisible_columns_difference" type="checkbox" value="1" <?php checked(1,$inv_col_difference); ?>><label for="rh4a_standing_invisible_columns_difference"><?php _e('Difference', 'results-h4a'); ?></label>
                        <p class="description"><?php _e('Check the columns you want to hide.', 'results-h4a'); ?></p>
                    </td>
                </tr>
            </table>
<?php
        submit_button( __('Save Standing', 'results-h4a') );
?>
        </form>
<?php
    }

    protected function verify_item() {
        $error = false;
        $desc = $_POST['rh4a_standing_description'];
        $objkey = $_POST['rh4a_standing_objkey'];
        if(!isset($desc) or empty($desc)) {
            $error = true;
            Results_H4A_Admin::show_msg(__('Please fill a description.', 'results-h4a'), "error");
        }
        if(!isset($objkey) or empty($objkey) or !intval($objkey)) {
            $error = true;
            Results_H4A_Admin::show_msg(__('Please provide a valid ID.', 'results-h4a'), "error");
        }
        // Dont check highlight and invisible columns.
        return !$error;
    }

    protected function prepare_item() {
        $invisible_columns = array();
        if(isset($_POST['rh4a_standing_invisible_columns_matches']))    $invisible_columns['matches'] = 1;
        if(isset($_POST['rh4a_standing_invisible_columns_wins']))       $invisible_columns['wins'] = 1;
        if(isset($_POST['rh4a_standing_invisible_columns_ties']))       $invisible_columns['ties'] = 1;
        if(isset($_POST['rh4a_standing_invisible_columns_losses']))     $invisible_columns['losses'] = 1;
        if(isset($_POST['rh4a_standing_invisible_columns_goals']))      $invisible_columns['goals'] = 1;
        if(isset($_POST['rh4a_standing_invisible_columns_difference'])) $invisible_columns['difference'] = 1;
        if(count($invisible_columns) > 0) {
            $invisible_columns = serialize($invisible_columns);
        } else {
            $invisible_columns = null;
        }
        return array(
            "description" => sanitize_text_field($_POST['rh4a_standing_description']),
            "objkey" => sanitize_key($_POST['rh4a_standing_objkey']),
            "highlight" => sanitize_text_field($_POST['rh4a_standing_highlight']),
            "invisible_columns" => $invisible_columns
        );
    }

    protected function create_item($item) {
        return $this->db->save_standing($item);
    }

    /**
	 * Public because of ajax (otherwise protected)
	 */
    public function edit_item($item, $ID) {
        // ID mit an die DB geben, damit update und nicht insert
        return $this->db->save_standing($item, $ID);
    }

    protected function get_item($ID) {
        return $this->db->get_standing($ID);
    }

    /**
	 * Public because of ajax (otherwise protected)
	 */
    public function delete_item($ID) {
        return $this->db->delete_standing($ID);
    }

    private function get_defaults() {
        return array(
            "description" => "",
            "objkey" => "",
            "highlight" => "",
            "invisible_columns" => null
        );
    }

}
