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

    private string            $plugin_name;
    private string            $version;
    private VTAImageTemplates $vta_image_templates;

    /**
     * Initialize the class and set its properties.
     * @param string $plugin_name The name of this plugin.
     * @param string $version The version of this plugin.
     * @since 1.0.0
     */
    public function __construct( string $plugin_name, string $version ) {
        $this->plugin_name = $plugin_name;
        $this->version     = $version;

        $this->vta_image_templates = new VTAImageTemplates($plugin_name, $version);

        add_action('admin_menu', [ $this, 'register_menu' ]);
    }

    // MENU FUNCTIONS //

    /**
     * Registers plugin menu in admin dashboard
     * @return void
     */
    public function register_menu(): void {
        add_menu_page(
            'Print Preview Settings',
            'Print Preview',
            'manage_options',
            WP_PRINT_SETTINGS_PAGE,
            [ $this, 'render_main_menu' ],
            'dashicons-printer',
            25
        );

        $post_type = VTA_IMAGE_TEMPLATE_CPT;

        // add as a sub-page menu under plugins menu
        add_submenu_page(
            WP_PRINT_SETTINGS_PAGE,
            'VTA Image Templates',
            'VTA Images',
            'manage_options',
            "edit.php?post_type=$post_type",
           false
        );

        // new VTA Image Template page
        add_submenu_page(
            WP_PRINT_SETTINGS_PAGE,
            'New VTA Holiday',
            'New VTA Holiday',
            'manage_options',
            "post-new.php?post_type=$post_type",
            false
        );
    }

//    /**
//     * Highlights the main menu
//     * @param string $file
//     * @return string
//     */
//    public function highlight_main_menu( string $file ): string {
//        global $plugin_page;
//
//        $post_type = POST_TYPE;
//        if ( preg_match("/$post_type/", $file) ) {
//            $plugin_page = WP_PRINT_SETTINGS_PAGE;
//        }
//        return $file;
//    }

    // RENDER FUNCTIONS //

    /**
     * Renders menu page
     * @return void
     */
    public function render_main_menu(): void {
        include_once(__DIR__ . '/views/main-menu.php');
    }

    public function render_help_page() {

    }

}
