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
 * This class contains specific methods related to the Mass Mailer admin subpage.
 *
 * @package    Wp_Print_Preview
 * @subpackage Wp_Print_Preview/admin
 * @author     James Pham <jamespham93@yahoo.com>
 */

class Wp_Print_Preview_Admin_Mass_Mailer {

    /**
     * Add Mass Mailer Template (AJAX handler)
     *
     * Takes form data from custom AJAX call and converts data into return envelope preview.
     * Pass form data to "return_envelope_template"
     * @TODO - add exception handling. And perhaps move this into a separate admin class in the future.
     * @since 2.0.0
     */
    public function add_mass_mailer_template()
    {
        // extract text and template type
        error_log( json_encode($_POST, JSON_PRETTY_PRINT) );
        error_log( json_encode($_FILES, JSON_PRETTY_PRINT) );

        // extract POST variables
        $template_name = $_POST['wpp_mm_template_name'];
        $template_type = $_POST['wpp_mm_template_type'];

        // Error check for missing input fields
        if ( empty( $template_type ) || empty( $template_name ) ) {
            // send back error message & error code (if possible)
            exit;
        }

        // Error check for missing file upload
        if ( empty( $_FILES['wpp_mm_template_upload']['tmp_name'] ) ) {
            // send back error message & error code (if possible)
            exit;
        }

        // copy tmp file to wp-content/uploads/wp-print-preview/mm-templates/[category]/
        $this->_upload_mm_template_files(  )

        //        // format as an array an serialize as JSON
        //        $post_content = array
        //        (
        //            'wpp_mm_template_name' => $template_name,
        //            'wpp_mm_template_type' => $template_type,
        //            'wpp_mm_template_file' => array
        //            (
        //                'filepath' => '',
        //                'type'     => '',
        //                'size'     => '',
        //            )
        //        );
        //
        //        /**
        //         * store as Custom Post for ease of access & use
        //         */
        //        $postarr = array(
        //            'post_type' => 'wpp_mm_template',
        //            'post_content' => json_encode( $post_content )
        //        );
        //        wp_insert_post( $postarr );

        exit;
    }

    /**
     * Upload Mass Mailer Template Files
     *
     * Used to upload
     * @param $tempfile
     * @param $template_category
     * @param $filename
     * @return string|null
     */
    private function _upload_mm_template_files( $tempfile, $template_category, $filename )
    {
        $upload_dir = wp_upload_dir();

        // Check if base directory exists for uploads/
        if ( ! empty( $upload_dir['basedir'] ) ) {

            $wpp_dir = $upload_dir['basedir'] . '/wp-print-preview';

            //  create a plugin dir /wp-print-preview/ if it does not exist
            if ( ! file_exists( $wpp_dir ) ) {
                wp_mkdir_p( $wpp_dir );
            }

            $mm_template_dir = $wpp_dir . '/mm-templates';

            //  create a plugin dir /wp-print-preview/mm-templates/ if it does not exist
            if ( ! file_exists( $mm_template_dir ) ) {
                wp_mkdir_p( $mm_template_dir );
            }

            $template_category_dir = $mm_template_dir . '/' . $template_category;

            // create a subdir to organize by template category=
            if ( ! file_exists( $template_category_dir ) ) {
                wp_mkdir_p( $template_category_dir );
            }

            $fullpath = $template_category_dir . '/' . $filename;

            // to avoid overwriting, append 1 to name until $fullpath is unique
            while ( file_exists( $fullpath ) ) {
                preg_replace( '/(\.\w+$)/', '${1}1', $fullpath );
            }

            // copy from tmp to new absolute path
            copy( $tempfile, $fullpath );

            return $fullpath;
        }
        else {
            return null;
        }
    }

    /**
     * Mass Mailer Template Custom Post Type
     *
     * Used in the main class-wp-print-preview.php
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
