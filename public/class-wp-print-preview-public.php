<?php

include_once(plugin_dir_path(__DIR__) . '/includes/class-wp-print-preview-helper.php');

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://jamespham.io
 * @since      1.0.0
 *
 * @package    Wp_Print_Preview
 * @subpackage Wp_Print_Preview/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Wp_Print_Preview
 * @subpackage Wp_Print_Preview/public
 * @author     James Pham <jamespham93@yahoo.com>
 */
class Wp_Print_Preview_Public
{

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $plugin_name The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $version The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @param string $plugin_name The name of the plugin.
     * @param string $version The version of this plugin.
     * @since    1.0.0
     */
    public function __construct($plugin_name, $version)
    {

        $this->plugin_name = $plugin_name;
        $this->version = $version;

    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_styles()
    {

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

        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/wp-print-preview-public.css', array(), $this->version, 'all');

    }

    /**
     * Register the JavaScript for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts()
    {

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

        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/wp-print-preview-public.js', array('jquery'), $this->version, false);

    }

    /**
     * A shortcode to preview Business card image. Gives user options to edit
     * current entry or delete.
     *
     * @param $atts
     * @return string - HTML user front-end
     */
    public function business_card_preview_shortcode($atts)
    {

        $args = shortcode_atts(
            array(
                'arg1' => 'arg1',
                'arg2' => 'arg2',
            ),
            $atts
        );

        // retrieve input values
        // $entry_id provided by query param
        if ( isset($_GET['entry_id']) ) {

            $entry = GFAPI::get_entry($_GET['entry_id']);
            $image = (new Wp_Print_Preview_Helper())->business_card_proof( $entry, false, true );

            // STORE IN PREVIEW PAGE TO USE FOR REDIRECT LATER
            $_SESSION['entry_id'] = $_GET['entry_id'];

            // Check if entry creator matches current user
            if ( get_current_user_id() != $entry['created_by'] ) {

                return '
                    <h2 class="bc-preview-unauthorized-header" style="text-align: center;">Unauthorized Access</h2>
                    <p class="bc-preview-unauthorized-content">
                      You are not allowed to view this page. Please navigate to the home page and start over or contact 
                      support for more assistance. 
                    </p>
                    <a href="' . site_url() . '">Back to Home</a>
                ';

            }
        }

        /**
         * @todo - Decide upon a better 'Confirmation/Cancellation' process, perhaps built-in GF methods or prior to this execution?
         */
        if ( isset($_POST['back']) ) {

            $entry = GFAPI::get_entry($_SESSION['entry_id']);

            $due_date = $entry['13'];
            $job_title = $entry['1'];
            $firstname = $entry['2.3'];
            $lastname = $entry['2.6'];
            $department = $entry['9'];
            $email = $entry['3'];
            $address = $entry['5'];
            $phone = $entry['6'];
            $mobile = $entry['7'];
            $fax = $entry['8'];
            $quantity = $entry['10'];

            $redirect_url = '/business-card-printing/?due_date=' . $due_date . '&job_title=' . $job_title . '&firstname=' . $firstname .
                '&lastname=' . $lastname . '&department=' . $department . '&email=' . $email . '&address=' . $address .
                '&phone=' . $phone . '&mobile=' . $mobile . '&fax=' . $fax . '&quantity=' . $quantity;

            $redirect_url = str_replace(' ', '+', $redirect_url);

            // Redirect user to the form
            GFAPI::delete_entry($entry['id']);
            return "
                <script>
                    window.location.href='" . $redirect_url . "';
                    $(`#gform_target_page_number_4`).val(3);
                    $(`#gform_4`).trigger('submit', [true]);
                </script>";
        }

        /**
         * helper->business_card(first,last,email,address,title, etc... - maybe array or object) - standardized - not overly flexible
         */

        return "
            <h3 style='text-align: center;'>Business Card Proof</h3>
            <p style='text-align: center;'>Here is a digital proof of your business card. To return the form, please click \"Back\"</p>
            
            <img class='bc-preview-image' src='/wp-content/plugins/wp-print-preview/public/assets/$image.png' />

            <!--have to pass event object manually-->
            <form method='post' id='confirm-bc'>
                <!-- <button name='edit' value='edit'>Edit Order</button> -->
                <div class='bc-preview-button-container'>
                  <button name='back' value='back' class='bc-preview-back'>Back</button>
                </div>
            </form>
        ";

        // ADD CODE TO CALL FILTERS FOR CART_ITEM_NAME AND

        // code...
    }

}
