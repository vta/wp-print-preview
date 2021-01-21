<?php
/**
 * The Mass Mailer Gravity Forms Field integration.
 *
 * Uses GF's Field Framework to create a custom form field that connects form data
 * to our plugin's image processing functions.
 *
 * @package    Wp_Print_Preview
 * @subpackage Wp_Print_Preview/gravityforms
 * @author     James Pham <jamespham93@yahoo.com>
 * @see        https://awhitepixel.com/blog/tutorial-create-an-advanced-custom-gravity-forms-field-type-and-how-to-handle-multiple-input-values/
 * @updated    1/20/2021
 */

if ( class_exists( 'GF_Field' ) ) {

    class MassMailerField extends GF_Field {

        public $type = 'Mass Mailer';

    }

    GF_Fields::register( new MassMailerField() );

} else {

    $err_msg = sprintf( '"GF_Field" does not exist. Please ensure that the current installed version of Gravity Forms still 
    contains "GF_Field" as part of its plugin.' );
    error_log( $err_msg );

}
