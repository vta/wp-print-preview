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
 * @see        https://docs.gravityforms.com/gf_field/
 * @updated    1/20/2021
 */

if ( class_exists( 'GF_Field' ) ) {

    class MassMailerField extends GF_Field {

        public $type = 'mass_mailer';

        /**
         * Defines button label on GF admin field's panel.
         * @return string
         */
        public function get_form_editor_field_title()
        {
            return 'Mailer Templates';
        }

        /**
         * Places type of field
         * @return array|string[]
         */
        public function get_form_editor_button()
        {
            return array(
                'group' => 'advanced_fields',
                'text'  => $this->get_form_editor_field_title(),
            );
        }

        /**
         * Displays predefined GF settings for our custom fields
         * @return array|string[]
         */
        public function get_form_editor_field_settings()
        {
            return array(
                'label_setting',
                'choices_setting',
                'description_setting',
                'rules_setting',
                'error_message_setting',
                'css_class_setting',
                'conditional_logic_field_setting',
                'admin_label_setting'
            );
        }



    }

    GF_Fields::register( new MassMailerField() );

} else {

    $err_msg = sprintf( '"GF_Field" does not exist. Please ensure that the current installed version of Gravity Forms still 
    contains "GF_Field" as part of its plugin.' );
    error_log( $err_msg );

}
