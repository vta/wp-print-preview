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

/**
 * Grab the latest submitted entry from a form
 *
 * @param $form_id
 * @return Entry - GF Entry object
 */
function get_latest_entry($form_id)
{
    $business_card_form = GFAPI::get_form($form_id);
    $entries = GFAPI::get_entries($form_id)[0];

    return $entries;
}

add_shortcode('business-card-preview', 'business_card_preview_shortcode');
/**
 *  Short Code to display gravity form's field entries
 * @TODO - separate shortcode into its own module
 */
function business_card_preview_shortcode()
{

    // business card form id
    $form_id = 4;

    // Grab the latest entry object.
    // @TODO - need to refactor form entry elsewhere. Query params?
    $entry = get_latest_entry($form_id);

    // retrieve input values
    $entry_id = $entry['id'];
    $job_title = $entry[1];
    $first_name = $entry['2.3'];
    $last_name = $entry['2.6'];
    $email = $entry[3];
    $address = $entry[5];

    // delete entry in preview. Not final submission
    // @TODO - Will delete latest entry if shortcode appears in a page/post.
    GFAPI::delete_entry($entry_id);

    if (isset($_POST['confirm'])) {

        $post_entry = $_POST['entry'];
        // submit entry to Gravity Forms page. Grab from below form POST
        GFAPI::add_entry($post_entry);

        echo "
            <h2>You're business card was submitted!</h2>
            <p>Please allow 2-3 days for the order to process.</p>
            <a href='http://wp-7_digitalpress'>Back to Home</a>
        ";

        exit();

    } elseif (isset($_POST['go_back'])) {
        // @TODO - go back to business card page
        // @TODO - pre-populate all inputs with previous user values
        $form_url = 'http://wp-7_digitalpress/business-card-printing';

        wp_redirect($form_url);

    } else {

        // Will be a preview in the future
        echo "
            <h1>$job_title</h1>
            <p>$first_name</p>
            <p>$last_name</p>
            <p>$email</p>
            <p>$address</p>
            
            <!--have to pass event object manually-->
            <form method='post'>
                <!--hidden input to pass to confirm POST-->
        ";

        foreach($entry as $key => $value) {
            echo "<input type='hidden' name='entry[$key]' value='$value'>";
        }

        echo "
                <button name='confirm' value='confirm'>Confirm</button>
                <button name='go_back' value='go_back'>Go Back</button>
                <button onclick='cancel(event);'>Cancel</button>
            </form>
            
            <!--Consider separating into its own JS file-->
            <script>
                // could not use es6 syntax 
                function cancel (e) {
                    // prevent page refresh upon button click
                    e.preventDefault();
                    // confirm if the user wants to cancel print job and redirect home
                    if(confirm('Are you sure you want to cancel this job?')) {
                        window.location.href = 'http://wp-7_digitalpress/';
                    }
                }
            </script>
        ";
    }
}

run_demo_print();
