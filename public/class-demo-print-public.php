<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://jamespham.io
 * @since      1.0.0
 *
 * @package    Demo_Print
 * @subpackage Demo_Print/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Demo_Print
 * @subpackage Demo_Print/public
 * @author     James Pham <jamespham93@yahoo.com>
 */
class Demo_Print_Public {

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
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Demo_Print_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Demo_Print_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/demo-print-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Demo_Print_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Demo_Print_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/demo-print-public.js', array( 'jquery' ), $this->version, false );

	}

    public function business_card_preview_shortcode()
    {
        // DEBUGGING
//    echo "<pre>";
//    var_dump(wp_get_current_user()->data->ID);    // user ID
//    print_r($entry);
//    echo "</pre>";

        // verify current user matches entry user
        check_entry_ownership();

        // retrieve input values
        // $entry_id provided by query param
        if (isset($_GET['entry_id'])) {
            $entry = GFAPI::get_entry($_GET['entry_id']);
            $job_title = $entry[1];
            $first_name = $entry['2.3'];
            $last_name = $entry['2.6'];
            $email = $entry[3];
            $address = $entry[5];

        }
        if (isset($_POST['cancel'])) {
            // @TODO - confirm (alert) user if they are sure they want to delete
            // @TODO - on confirm, delete message and display delete message
            // @TODO - have buttons redirect to home or create new business card
            GFAPI::delete_entry($entry['id']);
            return "
            <h3>Your order has been cancelled. The Copy Center team will be notified immediately.</h3>
            <a href='/'>Back to Home</a>
        ";
            exit();

        } // elseif (isset($_POST['edit'])) {
//        // @TODO - go back to business card page
//        // @TODO - pre-populate all inputs with previous user value
//        // @TODO - need to make sure that edits page EDITS (and not create)
//        // @TODO - need to confirm correct user before allowing edits
//        // access user ID with $entry['created_by'] and wp_get_current_user()->data->ID
//        // Will be needed to implemented on form page redirect
//        wp_redirect('/edit-business-card-order/');
//
//        exit();

//    }
        else {
            // Will be a preview in the future
            return "
            <h3>Your order is being processed. Please allow 2-3 business days for the order to complete.</h3>

            <h1>$job_title</h1>
            <p>$first_name</p>
            <p>$last_name</p>
            <p>$email</p>
            <p>$address</p>
            
            <!--have to pass event object manually-->
            <form method='post' id='confirm-bc'>
                <button name='edit' value='edit'>Edit Order</button>
                <button name='cancel' value='cancel'>Cancel Order</button>
            </form>
        ";
        }
    }

    }
