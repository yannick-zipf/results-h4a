<?php

 /**
 * The class responsible for the general settings admin page.
 *
 * @package    results-h4a
 * @subpackage results-h4a/admin
 * @author     Yannick Zipf <yannick.zipf@icloud.com>
 */

class RH4A_Admin_General_Settings {

    public function add_menu_page() {
        add_menu_page(
			__('Results H4A - Options', 'results-h4a'),
			__('RH4A Options', 'results-h4a'),
			'manage_options',
			'rh4a',
            array( $this , 'general_settings_page_html' ),
            'dashicons-analytics'
		);
    }

    /**
     * Top level menu callback function
     */
    public function general_settings_page_html() {
        // check user capabilities
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
    
        // add error/update messages
    
        // check if the user have submitted the settings
        // WordPress will add the "settings-updated" $_GET parameter to the url
        if ( isset( $_GET['settings-updated'] ) ) {
            // add settings saved message with the class of "updated"
            add_settings_error( 'rh4a_messages', 'rh4a_message', __( 'Settings Saved', 'results-h4a' ), 'updated' );
        }
    
        // show error/update messages
        settings_errors( 'rh4a_messages' );
        ?>
        <div class="wrap">
            <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
            <form action="options.php" method="post">
                <?php
                    settings_fields( 'rh4a' );
                    do_settings_sections( 'rh4a' );
                    submit_button( __( 'Save Settings', 'results-h4a' ) );
                ?>
            </form>
        </div>
        <?php
    }

    public function register_settings() {
        // Register a new setting for "rh4a" page.
		register_setting( 'rh4a', 'rh4a_options' );
 
		// Register a new section in the "rh4a" page.
		add_settings_section(
			'rh4a_section_general',
			__( 'General Settings', 'results-h4a' ),
            function($args) {
                echo '';
            },
			'rh4a'
		);
	 
		// Register a new field in the "rh4a_section_general" section, inside the "rh4a" page.
		add_settings_field(
			'rh4a_cache_active',
            __( 'Cache active', 'results-h4a' ),
            array( $this , 'render_checkbox'),
			'rh4a',
			'rh4a_section_general',
			array(
				'label_for' => 'rh4a_cache_active',
				'class'     => 'rh4a_general_setting'
			)
		);
        add_settings_field(
			'rh4a_default_css_active',
            __( 'Default CSS active', 'results-h4a' ),
            array( $this , 'render_checkbox'),
			'rh4a',
			'rh4a_section_general',
			array(
				'label_for' => 'rh4a_default_css_active',
				'class'     => 'rh4a_general_setting'
			)
		);
    }

    public function render_checkbox($args) {
        $options = get_option('rh4a_options');
        ?>
        <input  type="checkbox" 
                id="<?php echo esc_attr($args['label_for']); ?>" 
                name="rh4a_options[<?php echo esc_attr($args['label_for']); ?>]"
                value="1"
                <?php isset($options[$args['label_for']]) ? (checked(1,$options[$args['label_for']])) : ('') ?>
        >
        <?php
    }
}
