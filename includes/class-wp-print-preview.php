<?php

include_once(plugin_dir_path( __DIR__ ) . '/includes/class-wp-print-preview-helper.php');

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://jamespham.io
 * @since      1.0.0
 *
 * @package    Wp_Print_Print
 * @subpackage Wp_Print_Print/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Wp_Print_Preview
 * @subpackage Wp_Print_Preview/includes
 * @author     James Pham <jamespham93@yahoo.com>
 */
class Wp_Print_Preview {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Wp_Print_Preview_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'WP_PRINT_PREVIEW_VERSION' ) ) {
			$this->version = WP_PRINT_PREVIEW_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'wp-print-preview';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
		$this->get_loader();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Wp_Print_Preview_Loader. Orchestrates the hooks of the plugin.
	 * - Wp_Print_Preview_i18n. Defines internationalization functionality.
	 * - Wp_Print_Preview_Admin. Defines all hooks for the admin area.
	 * - Wp_Print_Preview_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wp-print-preview-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wp-print-preview-i18n.php';

        /**
         * The class responsible for adding Utilities and Parsing Excel files to JSON or PHP Objects
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wp-print-preview-util.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wp-print-preview-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-wp-print-preview-public.php';

		$this->loader = new Wp_Print_Preview_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Wp_Print_Preview_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Wp_Print_Preview_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Wp_Print_Preview_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action('admin_menu', $plugin_admin, 'create_admin_menu');

	}
	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Wp_Print_Preview_Public( $this->get_plugin_name(), $this->get_version() );
		$plugin_mass_mailer = new Wp_Print_Preview_Mass_Mailer();
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
//		$this->loader->add_action( 'wp_loaded', $plugin_public, 'Wp_Print_Preview_Helper::business_card_edit_redirect');
		$this->loader->add_shortcode( 'business-card-preview', $plugin_public, "Wp_Print_Preview_Public::business_card_preview_shortcode", $priority = 10, $accepted_args = 2 );
		$this->loader->add_shortcode('business-card-edit', $plugin_public, 'Wp_Print_Preview_Public::business_card_edit_shortcode',  $priority = 10, $accepted_args = 2);

		/**
         * Get the form_id for the Mass Mailer form.
         */
		$mm_form_id = $this->get_form_id("Mass Mailer");
		/**
         * Create the action hook for the specific form.
         */
		$this->loader->add_action("gform_post_multifile_upload_{$mm_form_id}", $plugin_mass_mailer, 'mass_mailer_addresses', 10, 5);

        $this->loader->add_action("gform_entry_created", $plugin_mass_mailer, 'get_mass_mailer_entry_id', 10, 2);
    }


	private function get_form_id($title) {
	    $matching_form = "";
        $forms = GFAPI::get_forms();
        foreach ($forms as $form) {
            if ($form['title'] === $title) {
                $matching_form = $form['id'];
                break;
            }
        }
        return $matching_form;
    }

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Wp_Print_Preview_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
