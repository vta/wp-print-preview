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
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}
    public function create_admin_menu() {
	    add_menu_page('Print Preview Settings', 'WP Print Preview', 'manage_options', 'print-preview-admin', [$this, 'admin_page_init'], 'dashicons-printer');
	    add_submenu_page('print-preview-admin', 'VTA SS Ballots', 'Manage Ballots', 'manage_options', 'print-preview-ballot-admin', [$this, 'ballot_page_init']);
        add_submenu_page('print-preview-admin', 'Mass Mailer', 'Mass Mailer', 'manage_options', 'wp-print-preview-mass-mailer', [$this, 'mass_mailer_page_init']);
    }
    function admin_page_init() {
        include_once 'PrintPreviewAdminView.php';
    }
    function ballot_page_init() {
        include_once 'PrintPreviewBallotAdminView.php';
    }

    /**
     * Used to render Mass Mailer Config submenu page
     *
     * @since 2.0.0
     */
    function mass_mailer_page_init() {

	    include_once 'partials/wp-print-preview-mass-mailer-config.php';

    }

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wp_Print_Preview_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wp_Print_Preview_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wp-print-preview-admin.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wp_Print_Preview_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wp_Print_Preview_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/wp-print-preview-admin.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/sort-table.min.js', array(), false, true);

        // send admin URL to allow for custom AJAX/hook call for Mass Mailer Settings
        wp_enqueue_script( 'wpp-mm-settings-js', plugin_dir_url( __FILE__ ) . 'js/wpp-mm-config.js', array( 'jquery' ), $this->version, true );
        wp_localize_script(
            'wpp-mm-settings-js',
            'mmAdminSettings',
            array(
                'ajaxUrl' => admin_url( 'admin-ajax.php' ),
                'nonce'   => wp_create_nonce('update_mass_mailer_settings-nonce')
            )
        );
        wp_enqueue_script('wp-ajax-response');

	}

    /**
     * Mass Mailer Settings AJAX handler
     *
     * Takes form data from custom AJAX call and converts data into return envelope preview.
     * Pass form data to "return_envelope_template"
     * @TODO - add exception handling. And perhaps move this into a separate admin class in the future.
     *
     * @since 2.0.0
     */
    public function update_mass_mailer_settings()
    {
        // extract text and template type
        error_log( json_encode($_POST, JSON_PRETTY_PRINT) );
        error_log( json_encode($_FILES, JSON_PRETTY_PRINT) );

        // extract post variables
        $template_name = $_POST['wpp_mm_template_name'];
        $template_type = $_POST['wpp_mm_template_type'];

        // Error check for missing input fields
        if ( empty( $template_type ) || empty( $template_name ) ) {
            // send back error message & error code (if possible)
            exit;
        }

        // Error check for missing file upload
        if ( empty( $_FILES['tmp_name'] ) ) {
            // send back error message & error code (if possible)
            exit;
        }

        // Extract from tmp & copy over to wp-content/uploads/wp-print-preview


        // format as an array an serialize as JSON
        $post_content = array
        (
            'wpp_mm_template_name' => $template_name,
            'wpp_mm_template_type' => $template_type,
            'wpp_mm_template_file' => array
            (
                'filepath' => '',
                'type'     => '',
                'size'     => '',
            )
        );

        /**
         * store as Custom Post for ease of access & use
         */
        $postarr = array(
            'post_type' => 'wpp_mm_template',
            'post_content' => json_encode( $post_content )
        );
        wp_insert_post( $postarr );

        exit;
    }


    private function _upload_mm_template_files($tempfile, $filename)
    {
        $upload_dir = wp_upload_dir();

        // Check if base directory exists for uploads/
        if ( ! empty( $upload_dir['basedir'] ) ) {

            $wpp_dir = $upload_dir['basedir'] . '/wp-print-preview';

            //  create a plugin dir /wp-print-preview if it does not exist
            if ( ! file_exists( $wpp_dir ) ) {
                wp_mkdir_p( $wpp_dir );
            }

            $mm_template_dir = $wpp_dir . '/mm-templates';

            //  create a plugin dir /wp-print-preview/mm-templates if it does not exist
            if ( ! file_exists( $mm_template_dir ) ) {
                wp_mkdir_p( $mm_template_dir );
            }

            $fullpath = $mm_template_dir . '/' . $filename;

            // to avoid overwriting, append 1 to name until $fullpath is unique
            while ( file_exists( $fullpath ) ) {
                
            }

            // absolute filepath to the newly created image i.e. "/var/www/html/wp-content/uploads/mass_mailer/"
            $filepath = $subdir . '/' . $filename;
            $image->writeImage( $filepath );

            // return the url pointing to the path above i.e. "https://documentservices.com/wp-content/uploads/mass_mailer/"
            $url = $upload_dir['baseurl'] . '/' . $uploads_subdir . '/' . $filename;
            return $url;
        }
    }

    /**
     * Mass Mailer Template Custom Post Type
     *
     * Establishes data structure for storing Mass Mailer Templates in our database. Leverages WordPress
     * Custom Post Types API. These template records are created in our "Mass Mailer" admin submenu page and
     * are used displayed as a custom Gravity Forms field.
     */
    public function init_mm_template_post_types()
    {
        $args = array(
            // For full range of label controls, see TemplatesDownloadWidget.php for more information
            'labels'              => 'Mass Mailer Template',
            'description'         => 'Mass mailer templates used to create preview and images for Document Services\' mass mailing service.',
            'public'              => false, // May have to change later if GF cannot render for customers
            'hierarchical'        => false,
            'show_ui'             => false,
            'show_in_menu'        => false,
            'show_in_nav_menus'   => false,
            'show_in_admin_bar'   => false,
            'can_export'          => true,
            'has_archive'         => true,
            'exclude_from_search' => true,
            'publicly_queryable'  => false,
//            'capability_type'     => 'post',  // not sure yet
            'show_in_rest'        => true,
         );

        register_post_type( 'wpp_mm_template', $args );
    }

}
