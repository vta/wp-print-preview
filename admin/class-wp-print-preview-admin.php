<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://jamespham.io
 * @since      1.0.0
 *
 * @package    Wp_Print_Preview
 * @subpackage Wp_Print_Preview/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Wp_Print_Preview
 * @subpackage Wp_Print_Preview/admin
 * @author     James Pham <jamespham93@yahoo.com>
 */
class Wp_Print_Preview_Admin {

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $plugin_name The ID of this plugin.
     */
    private string $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $version The current version of this plugin.
     */
    private string $version;

    /**
     * Initialize the class and set its properties.
     *
     * @param string $plugin_name The name of this plugin.
     * @param string $version The version of this plugin.
     * @since 1.0.0
     */
    public function __construct( string $plugin_name, string $version ) {
        $this->plugin_name = $plugin_name;
        $this->version     = $version;

        add_action('admin_menu', [$this, 'register_menu']);
    }

    public function register_menu() {

    }

    public function render_main_menu() {

    }

    public function render_help_page() {

    }

}
