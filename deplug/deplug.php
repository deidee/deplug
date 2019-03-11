<?php
/**
 * Plugin Name: dePlug
 * Plugin URI: https://github.com/deidee/deplug
 * Description: Add Google Analytics, Open Graph, Twitter Cards, et al.
 * Version: 0.1
 * Author: deidee
 * Author URI: https://deidee.nl/
 * License: GPL3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: deplug
 */

defined( 'ABSPATH' ) or die( 'Computer says no.' );

class DeplugSettingsPage
{
    private $options;

    public function __construct()
    {
        add_action( 'admin_menu', array( $this, 'add_settings_page' ) );
        add_action( 'admin_init', array( $this, 'page_init' ) );
    }

    /**
     * Add options page
     */
    public function add_settings_page()
    {
        // This page will be under "Settings"
        add_options_page(
            'dePlug Settings', // Page title.
            'dePlug Settings', // Menu label.
            'manage_options', // Required capability.
            'deplug', // Menu slug.
            array( $this, 'create_admin_page' ) // Callback.
        );
    }

    /**
     * Options page callback
     */
    public function create_admin_page()
    {
        // Set class property
        $this->options = get_option( 'deplug_option_name' );
        ?>
        <div class="wrap">
            <h1>dePlug Settings</h1>
            <form method="post" action="options.php">
                <?php
                // This prints out all hidden setting fields
                settings_fields( 'deplug_option_group' );
                do_settings_sections( 'deplug' );
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    /**
     * Register and add settings
     */
    public function page_init()
    {
        register_setting(
            'deplug_option_group', // Option group
            'deplug_option_name', // Option name
            array( $this, 'sanitize' ) // Sanitize
        );

        add_settings_section(
            'setting_section_id', // ID
            'Meta Data', // Title
            array( $this, 'print_section_info' ), // Callback
            'deplug' // Page
        );

        add_settings_field(
            'facebook_app_id', // ID
            'Facebook app ID', // Title
            array( $this, 'id_number_callback' ), // Callback
            'deplug', // Page
            'setting_section_id' // Section
        );

        add_settings_field(
            'title',
            'Title',
            array( $this, 'title_callback' ),
            'deplug',
            'setting_section_id'
        );
    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function sanitize( $input )
    {
        $new_input = array();
        if( isset( $input['id_number'] ) )
            $new_input['id_number'] = absint( $input['id_number'] );

        if( isset( $input['title'] ) )
            $new_input['title'] = sanitize_text_field( $input['title'] );

        return $new_input;
    }

    /**
     * Print the Section text
     */
    public function print_section_info()
    {
        print 'Enter your settings below:';
    }

    /**
     * Get the settings option array and print one of its values
     */
    public function id_number_callback()
    {
        printf(
            '<input type="text" id="id_number" name="deplug_option_name[id_number]" value="%s" />',
            isset( $this->options['id_number'] ) ? esc_attr( $this->options['id_number']) : ''
        );
    }

    /**
     * Get the settings option array and print one of its values
     */
    public function title_callback()
    {
        printf(
            '<input type="text" id="title" name="deplug_option_name[title]" value="%s" />',
            isset( $this->options['title'] ) ? esc_attr( $this->options['title']) : ''
        );
    }
}

if( is_admin() )
    $deplug_settings = new DeplugSettingsPage();