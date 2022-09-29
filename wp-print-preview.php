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
 * @since             1.1.0
 * @package           Wp_Print_Preview
 *
 * @wordpress-plugin
 * Plugin Name:       VTA Print Preview
 * Plugin URI:        https://github.com/vta/wp-print-preview
 * Description:       A WordPress print preview plugin to preview and output PNG file of a business card.
 * Version:           1.1.0
 * Author:            James Pham
 * Author URI:        https://jamespham.io
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wp-print-preview
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( !defined('WPINC') ) {
    die;
}
// CONSTANTS //
// Dependencies
const SAVE_IMG_AJAX = 'save_vta_img';
// Custom Post Type
const WP_PRINT_SETTINGS_PAGE = 'vta-print';
const VTA_IMAGE_TEMPLATE_CPT = 'vta_image_template';
const VTA_IMAGE_PATH_META    = 'vta_image_path';
const VTA_IMAGE_FIELDS_META  = 'vta_image_fields_annotations';
const VTA_IMAGE_META         = 'vta_image_meta';

// IMPORTS //
// PHP PDFTK
require_once('utils/php-pdftk/vendor/autoload.php');
require_once('classes/PhpPdftk.php');
// Utils
require_once('utils/common.php');
// Models
require_once('models/VTAImageTemplate.php');
// Core classes
require_once('classes/VTAImageTemplates.php');

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
const WP_PRINT_PREVIEW_VERSION = '1.1.0';

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-wp-print-preview-activator.php
 */
function activate_wp_print_preview() {
    require_once plugin_dir_path(__FILE__) . 'includes/class-wp-print-preview-activator.php';
    Wp_Print_Preview_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-wp-print-preview-deactivator.php
 */
function deactivate_wp_print_preview() {
    require_once plugin_dir_path(__FILE__) . 'includes/class-wp-print-preview-deactivator.php';
    Wp_Print_Preview_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_wp_print_preview');
register_deactivation_hook(__FILE__, 'deactivate_wp_print_preview');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-wp-print-preview.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_wp_print_preview() {

    $plugin = new Wp_Print_Preview();
    $plugin->run();

}

run_wp_print_preview();
