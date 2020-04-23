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

        // verify current user matches entry user
        (new Wp_Print_Preview_Helper)->check_entry_ownership();

        // retrieve input values
        // $entry_id provided by query param
        if ( isset($_GET['entry_id']) ) {
            $entry = GFAPI::get_entry($_GET['entry_id']);
            $image = (new Wp_Print_Preview_Helper())->business_card_proof($entry);
            $_SESSION['entry_id'] = $_GET['entry_id'];
        }

        /**
         * @todo - Decide upon a better 'Confirmation/Cancellation' process, perhaps built-in GF methods or prior to this execution?
         */
        if ( isset($_POST['cancel']) ) {
            // @TODO - confirm (alert) user if they are sure they want to delete
            // @TODO - on confirm, delete message and display delete message
            // @TODO - have buttons redirect to home or create new business card
            GFAPI::delete_entry($entry['id']);
            return "
                <h3>Your business card order has been canceled.</h3>
                <a style='display:inline-block; text-align: right' href='/'>Back to Home</a>
                
                <style>
                  #post-157 > div.post-inner.thin > div > p {display: none !important;}
                </style>
            ";
        }

        /**
         * @todo - send entry form as we need to new imagick method for screen image return and print ready proof
         * helper->business_card(first,last,email,address,title, etc... - maybe array or object) - standardized - not overly flexible
         */

        return "
            <h3 style='text-align: center;'>Business Card Proof</h3>
            <p>Please review the proof to see if everything is correct. 
            If it is, please click <b>Add Order</b> to add it to your cart.</p>
            
            <img class='bc-preview-image' src='/wp-content/plugins/wp-print-preview/public/assets/$image.png' />

            <!--have to pass event object manually-->
            <form method='post' id='confirm-bc'>
                <!-- <button name='edit' value='edit'>Edit Order</button> -->
                <div class='bc-preview-button-container'>
                  <button name='cancel' value='cancel' class='bc-preview-cancel'>Cancel</button>
                </div>
            </form>
        ";

        // ADD CODE TO CALL FILTERS FOR CART_ITEM_NAME AND

        // code...
    }

    /**
     * Back-end logic to manipulate business card forms to edit instead
     * of display.
     *
     * @param $atts
     */
    public function business_card_edit_shortcode($atts)
    {
        // WORKAROUND
        // @TODO - find another way to prevent shortcode from firing within
        if ( !isset($_GET['entry_id']) ) {
            return;
        }

        // @TODO - method 1: have the GF shortcode grab the query param and populate values
        // verify current user matches entry user
        (new Wp_Print_Preview_Helper)->check_entry_ownership();

        // DEBUGGING
        $entry_id = $_GET['entry_id'];
        $entry = GFAPI::get_entry($entry_id);
        echo "<pre>";
        print_r($entry);
        echo "</pre>";

        // @TODO - change submission action to EDIT current entry instead of adding new entry
        // Used snippets from techslides.com
        // @reference - http://techslides.com/editing-gravity-forms-entries-on-the-front-end
        function pre_submission_edit($form)
        {
            $entry_id = $_GET['entry_id'];

            // update entry fields with new post values
            $entry = GFAPI::get_entry($entry_id);
            $entry['id'] = $entry_id;
            $entry[1] = $_POST[1];
            $entry['2.3'] = $_POST['2.3'];
            $entry['2.6'] = $_POST['2.6'];
            $entry[3] = $_POST[3];
            $entry[5] = $_POST[5];

            // make changes to current entry
            GFAPI::update_entry($entry);

            // @TODO - attach additional POST variable to tag this submission as an edit
            $_POST['edit_business_card'] = true;
            $_POST['entry_id'] = $entry_id;

        }
    }

}
