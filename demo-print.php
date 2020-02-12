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

add_shortcode('business-card-preview', 'business_card_preview_shortcode');
/**
 *  Short Code to display gravity form's field entries
 * @TODO - separate shortcode into its own module
 */
function business_card_preview_shortcode()
{
    // Grab the latest entry object.
    // $entry_id provided by query param
    $entry = GFAPI::get_entry($_GET['entry_id']);

    // retrieve input values
    if (isset($entry)) {
        $job_title = $entry[1];
        $first_name = $entry['2.3'];
        $last_name = $entry['2.6'];
        $email = $entry[3];
        $address = $entry[5];
    }

    if (isset($_POST['delete'])) {
        // @TODO - confirm (alert) user if they are sure they want to delete
        // @TODO - on confirm, delete message and display delete message
        // @TODO - have buttons redirect to home or create new business card

        exit();

    } elseif (isset($_POST['go_back'])) {
        // @TODO - go back to business card page
        // @TODO - pre-populate all inputs with previous user values

    } else {

        // Will be a preview in the future
        echo "
            <h3>Your order is being processed. Please allow 2-3 business days for the order to complete.</h3>

            <h1>$job_title</h1>
            <p>$first_name</p>
            <p>$last_name</p>
            <p>$email</p>
            <p>$address</p>
            
            <!--have to pass event object manually-->
            <form method='post'>
                <button name='edit' value='edit'>Edit</button>
                <button name='delete' value='delete'>Delete</button>
            </form>
        ";
    }
}

run_demo_print();
