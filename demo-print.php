<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://jamespham.io
 * @since             1.0.0
 * @package           Demo_Print
 *
 * @wordpress-plugin
 * Plugin Name:       Demoprint
 * Plugin URI:        http://wp-wordpress/demoprint
 * Description:       A demo plugin to print dynamic input from Gravity Forms
 * Version:           1.0.0
 * Author:            James Pham
 * Author URI:        https://jamespham.io
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       demo-print
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define('DEMO_PRINT_VERSION', '1.0.0');

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-demo-print-activator.php
 */
function activate_demo_print()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-demo-print-activator.php';
    Demo_Print_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-demo-print-deactivator.php
 */
function deactivate_demo_print()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-demo-print-deactivator.php';
    Demo_Print_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_demo_print');
register_deactivation_hook(__FILE__, 'deactivate_demo_print');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-demo-print.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_demo_print()
{

    $plugin = new Demo_Print();
    $plugin->run();

}

// SOLUTION TO PREVIEW PAGE: (CONFIRMATION STORES ENTRY EVEN IF USER DID NOT CONFIRM)
// 1. STORE ENTRY INFORMATION IN LOCAL VARIABLE
// 2. IMMEDIATELY DELETE ENTRY FROM ENTRIES
// 3. PROCESS LOCAL VARIABLE INTO A PREVIEW
// 4. PROVIDE BUTTONS TO ROUTE BACK TO PREVIOUS FORM, CANCEL TO GO HOME, SUBMIT
// RETURN "<html>". DO NOT ECHO
// @see https://wordpress.stackexchange.com/questions/140466/custom-shortcode-being-executed-when-saving-page-in-wp-admin

add_shortcode('business-card-preview', 'business_card_preview_shortcode');
/**
 *  Short Code to display gravity form's field entries
 * @TODO - separate shortcode into its own module
 */
function business_card_preview_shortcode()
{
    // DEBUGGING
    echo "<pre>";
//    var_dump(wp_get_current_user()->data->ID);    // user ID
    print_r($_POST);
    echo "</pre>";

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

add_action('wp_loaded', 'redirect_edit_page');
function redirect_edit_page()
{
    // check if current user owns entry
    check_entry_ownership();

    if (isset($_POST['edit'])) {
        // @TODO - go back to business card page
        // @TODO - pre-populate all inputs with previous user value
        // @TODO - need to make sure that edits page EDITS (and not create)
        // @TODO - need to confirm correct user before allowing edits
        // access user ID with $entry['created_by'] and wp_get_current_user()->data->ID
        // Will be needed to implemented on form page redirect
        if (isset($_GET['entry_id'])) {
            $entry_id = $_GET['entry_id'];
            $entry = GFAPI::get_entry($_GET['entry_id']);
            $job_title = $entry[1];
            $first_name = $entry['2.3'];
            $last_name = $entry['2.6'];
            $email = $entry[3];
            $address = $entry[5];

            $query_param = "?job_title=$job_title&first_name=$first_name&last_name=$last_name&email=$email&address=$email&entry_id=$entry_id";
            wp_redirect('/edit-business-card-order/' . $query_param);

            exit();
        }
    }

}

/**
 * Temporary short code to be added to an "EDIT" page
 *
 * Acts as auth code to make sure logged in user is the owner of short code
 */
add_shortcode('business-card-edit', 'business_card_edit_shortcode');
/**
 * Short Code to display gravity form's field entries
 *
 * PHP injection to manipulate Business Card Form to:
 * - ensure that owner entry is the current logged-in user
 * - pre-populate the form via query params. This is done with the redirect "redirect_edit_page"
 * - AND update existing entry using $entry_id (as opposed to creating a new entry)
 */
function business_card_edit_shortcode()
{
    // @TODO - method 1: have the GF shortcode grab the query param and populate values
    // verify current user matches entry user
    check_entry_ownership();

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

/**
 * Business Card Edit redirect
 *
 * redirects to business card confirmation page from business card edit page after submission.
 */
add_action('wp_loaded', 'redirect_edit_submission');
function redirect_edit_submission()
{
    check_entry_ownership();

    if (isset($_POST['edit_business_card'])) {
        // redirect user to confirmation page

        $entry_id = $_POST['entry_id'];
        header('Location: ' . '/business-card-confirmation/?entry_id=' . $entry_id);

    }
}

/**
 * Check if current user matches entry user. Notifies user if not and provides login or home page
 */
function check_entry_ownership()
{
    // @TODO - current workaround. Fires within editor (missing query param causes error)
    if (!isset($_GET['entry_id'])) {
        return;
    }

    // entry_id provided by query param
    $entry = GFAPI::get_entry($_GET['entry_id']);
    $current_user_id = intval(wp_get_current_user()->data->ID);

    if (is_wp_error($entry['created_by'])) {
        // if the following exists
        return "<h1>BIG ERROR</h1>";
        exit();
    } else {
        $entry_user = $entry['created_by'];
    }

    if ($current_user_id != $entry_user) {
        // Current user does not match entry owner
        return "
            <h1>Sorry, you are not authorized to edit this page</h1>
            <p>Please login to access this page.</p>
            <a href='/wp-login.php?'>Login</a>
            <a href='/'>Back to Home</a>
        ";
        exit();
    }

}

run_demo_print();
