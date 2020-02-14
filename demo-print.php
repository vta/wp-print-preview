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

add_action('wp_loaded', 'redirect_edit_page');
function redirect_edit_page()
{

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

    // @TODO - change submission action to EDIT current entry instead of adding new entry
    // Used snippets from techslides.com
    // @reference - http://techslides.com/editing-gravity-forms-entries-on-the-front-end
    function pre_submission_edit($form)
    {
//            //submitted new values that need to be used to update the original entry via $success = GFAPI::update_entry( $entry );
//            var_dump($_POST);
//            $entry_id = $_GET['entry_id'];
//
//            //Get original entry id
//            parse_str($_SERVER["QUERY_STRING"]); //will be stored in $entry
//
//            //get the actual entry we want to edit
//            $edit_entry = GFAPI::get_entry($entry_id);
//
//            //make changes to it from new values in $_POST, this shows only the first field update
//            $edit_entry[1] = $_POST["input_1"];
//
//            //update it
//            $was_updated = GFAPI::update_entry($edit_entry);
//
//            if (is_wp_error($was_updated)) {
//                echo "<h3>Could not update your business card.</h3>";
//            } else {
//                //success, so redirect
//                header("Location: http://domain.com/confirmation/");
//            }
//
//            //dont process and create new entry
//            die();
    }
    add_action('gform_pre_submission_4', 'pre_submission_edit');

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
