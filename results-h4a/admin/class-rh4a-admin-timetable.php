<?php

/**
 * The class responsible for the admin page of type "Timetable".
 *
 * @package    results-h4a
 * @subpackage results-h4a/admin
 * @author     Yannick Zipf <yannick.zipf@icloud.com>
 */

class RH4A_Admin_Timetable extends RH4A_Shortcode_Abstract {

    protected $nonce = "nonce_rh4a_timetable";
    protected $menu_slug = "rh4a-timetable";

    public function add_menu_page() {
        add_submenu_page(
            'rh4a',
			__('Results H4A - Timetables', 'results-h4a'),
			__('RH4A Timetables', 'results-h4a'),
			'manage_options',
			$this->menu_slug,
            array( $this , 'render_html' )
		);
    }

    public function render_list() {
        $timetables = $this->db->get_timetables();
?>
        <table class="wp-list-table widefat fixed striped table-view-list rh4a-mt-1">
            <tr>
                <th><?php _e('Shortcode', 'results-h4a'); ?></th>
                <th><?php _e('Description', 'results-h4a'); ?></th>
                <th><?php _e('Type', 'results-h4a'); ?></th>
                <th><?php _e('ID', 'results-h4a'); ?></th>
                <th><?php _e('Highlight', 'results-h4a'); ?></th>
                <th><?php _e('Status', 'results-h4a'); ?></th>
                <th><?php _e('Edit', 'results-h4a'); ?></th>
            </tr>
<?php
        foreach($timetables as $timetable) {
?>
            <tr>
                <td><code>[rh4a-timetable <?php echo esc_html($timetable->ID); ?>]</code></td>
                <td><?php echo esc_html($timetable->description); ?></td>
                <td><?php echo $this->get_type_text(esc_html($timetable->type)); ?></td>
                <td><?php echo esc_html($timetable->objkey); ?></td>
                <td><?php echo esc_html($timetable->highlight); ?></td>
                <td>
                    <span class="rh4a-status-text"><?php echo $this->get_status_text(esc_html($timetable->status)); ?></span>
                    <a  data-item-type="timetable" 
                        data-item-id="<?php esc_html_e($timetable->ID); ?>" 
                        data-item-new-status="<?php echo esc_html($timetable->status) == 1 ? 0 : 1; ?>" 
                        data-item-status-text-active="<?php echo $this->get_status_text(1); ?>" 
                        data-item-status-text-inactive="<?php echo $this->get_status_text(0); ?>" 
                        class="rh4a-change-status-link">
                        <span class="dashicons dashicons-<?php echo esc_html($timetable->status) == 1 ? "hidden" : "visibility"; ?>"></span>
                    </a>
                </td>
                <td>
                    <a href="admin.php?page=<?php echo $this->menu_slug; ?>&action=edit&id=<?php esc_html_e($timetable->ID); ?>" class="button button-primary"><?php _e('Edit', 'results-h4a'); ?></a>
                    <a data-item-type="timetable" data-item-id="<?php esc_html_e($timetable->ID); ?>" class="button rh4a-delete-item-link"><?php _e('Delete', 'results-h4a'); ?></a>
                </td>
            </tr>
<?php
        }
?>
        </table>  
<?php
    }

    /**
	 * Render form for creating a new or editing an existing timetable.
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
                    <th scope="row"><label for="rh4a_timetable_description"><?php _e('Description', 'results-h4a'); ?></label></th>
                    <td><input id="rh4a_timetable_description" name="rh4a_timetable_description" type="text" value="<?php echo esc_html($values['description']); ?>" maxlength="255" class="regular-text"></td>
                </tr>
                <tr>
                    <th scope="row"><?php _e('Type', 'results-h4a'); ?></th>
                    <td>
                        <fieldset>
                            <legend class="screen-reader-text"><?php _e('Type', 'results-h4a'); ?></legend>
                            <?php
                                $types = $this->get_possible_types();
                                foreach($types as $key => $type) {
                            ?>
                            <label>
                                <input id="rh4a_timetable_type_<?php echo $key; ?>" name="rh4a_timetable_type" type="radio" value="<?php echo $type['db']; ?>" <?php checked($type['db'], $values['type']); ?>>
                                <span><?php echo $type['label']; ?></span>
                            </label>
                            <br>
                            <?php
                                }
                            ?> 
                        </fieldset>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="rh4a_timetable_objkey"><?php _e('ID', 'results-h4a'); ?></label></th>
                    <td>
                        <input id="rh4a_timetable_objkey" name="rh4a_timetable_objkey" type="text" value="<?php echo esc_html($values['objkey']); ?>" class="regular-text">
                        <p class="description"><?php _e('Supply TeamID or LeagueID depending on chosen type.', 'results-h4a'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="rh4a_timetable_highlight"><?php _e('Team name (Optional)', 'results-h4a'); ?></label></th>
                    <td>
                        <input id="rh4a_timetable_highlight" name="rh4a_timetable_highlight" type="text" value="<?php echo esc_html($values['highlight']); ?>" class="regular-text">
                        <p class="description"><?php _e('Only relevant for type LEAGUE to highlight your team.', 'results-h4a'); ?></p>
                    </td>
                </tr>
            </table>
<?php
        submit_button( __('Save Timetable', 'results-h4a') );
?>
        </form>
<?php
    }

    protected function verify_item() {
        $error = false;
        $desc = $_POST['rh4a_timetable_description'];
        $type = $_POST['rh4a_timetable_type'];
        $objkey = $_POST['rh4a_timetable_objkey'];
        $highlight = $_POST['rh4a_timetable_highlight'];
        if(!isset($desc) or empty($desc)) {
            $error = true;
            Results_H4A_Admin::show_msg(__('Please fill a description.', 'results-h4a'), "error");
        }
        $found_type = false;
        foreach($this->get_possible_types() as $t) {
            if($t['db'] === $type) $found_type = true;
        }
        if(!isset($type) or !$found_type) {
            $error = true;
            Results_H4A_Admin::show_msg(__('Please choose a valid type.', 'results-h4a'), "error");
        }
        if(!isset($objkey) or empty($objkey) or !intval($objkey)) {
            $error = true;
            Results_H4A_Admin::show_msg(__('Please provide a valid ID.', 'results-h4a'), "error");
        }
        return !$error;
    }

    protected function prepare_item() {
        return array(
            "description" => sanitize_text_field($_POST['rh4a_timetable_description']),
            "type" => sanitize_text_field($_POST['rh4a_timetable_type']),
            "objkey" => sanitize_key($_POST['rh4a_timetable_objkey']),
            "highlight" => sanitize_text_field($_POST['rh4a_timetable_highlight'])
        );
    }

    protected function create_item($item) {
        return $this->db->save_timetable($item);
    }

    /**
	 * Public because of ajax (otherwise protected)
	 */
    public function edit_item($item, $ID) {
        // ID mit an die DB geben, damit update und nicht insert
        return $this->db->save_timetable($item, $ID);
    }

    protected function get_item($ID) {
        return $this->db->get_timetable($ID);
    }

    /**
	 * Public because of ajax (otherwise protected)
	 */
    public function delete_item($ID) {
        return $this->db->delete_timetable($ID);
    }

    private function get_type_text($type) {
        $types = $this->get_possible_types();
        foreach($types as $t) {
            if($t['db'] === $type) return $t['label'];
        }
    }

    private function get_possible_types() {
        $types = array(
            "league" => array(
                "db" => "l",
                "label" => __('League', 'results-h4a')
            ),
            "team" => array(
                "db" => "t",
                "label" => __('Team', 'results-h4a')
            )
        );
        return $types;
    }  

    private function get_defaults() {
        return array(
            "description" => "",
            "type" => "l",
            "objkey" => "",
            "highlight" => ""
        );
    }

}
