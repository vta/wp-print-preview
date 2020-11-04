<?php

require_once "class-wp-print-preview-util.php";
require_once "class-wp-print-preview-imagick.php";

class Wp_Print_Preview_Mass_Mailer
{

    private $entry;
    private $entry_id;
    private $gf_form;
    private $pp_util;
    private $imagick;

    /**
     * Wp_Print_Preview_Mass_Mailer constructor
     * @throws Exception
     */
    function __construct()
    {
        $this->pp_util = new Wp_Print_Preview_Util();
        $this->imagick = new Wp_Print_Preview_Imagick();
    }

    /**
     * Return Address Template
     *
     * stamps return address in the correct location on the #9 Return Envelope.
     * @param $return_address_text      string      - return address text from textarea
     * @param $template_type            string      - envelope template type from user selection "Regular or ATU"
     * @param $is_preview               boolean     - determines if the method will produce a preview file or PDF
     * @param $job_name                 null|string - used as part of file name if is not preview
     * @return string|null                          - file path if generated or null (for preview)
     */
    public function create_return_envelope_template( $return_address_text, $template_type, $is_preview = false, $job_name = null)
    {
        $BLACK = '#000000';         // COLOR CONSTANT
        $CHAR_SPACE = 0.3;          // character spacing
        $FONT_SIZE = 10.5;          // FONT SIZE
        $STROKE_WIDTH = 0.7;        // FONT WEIGHT
        $WORD_SPACING = 0.9;        // SPACING BETWEEN WORD
        $LINE_HEIGHT = 4.7;         // LINE HEIGHT
        // ANNOTATIONS
        $X = 1038;                  // X COORD FOR TEXT
        $Y = 598;                   // Y COORD FOR TEXT
        $ANNOTATION = array(
            'x' => $X,
            'y' => $Y,
            'text' => $return_address_text
        );

        /** text draw params */
        $address_text = array(
            'font'         => plugin_dir_path( __DIR__ ) . '/public/assets/MuseoSans_300.otf',
            'color'        => $BLACK,
            'stroke_width' => $STROKE_WIDTH,
            'font_size'    => $FONT_SIZE,
            'kerning'      => $CHAR_SPACE,
            'annotation'   => $ANNOTATION,
            'line_height'  => $LINE_HEIGHT,
            'word_spacing' => $WORD_SPACING
        );

        // ENVELOPE TEMPLATE FILE
        try {
            $envelope_template = $this->_return_envelope_file_path( $template_type );
            if ( $envelope_template === null ) {
                throw new Exception('Could not find value for "return_envelope_template".');
            }
        } catch (Exception $e) {
            error_log( $e );
            die();
        }

        // CREATE FILENAME
        // if there is no job name, use generic "mm"
        empty( $job_name ) && $job_name = 'mm';
        // filename = [entry_id]_[job_name].pdf && sanitize from illegal characters
        $filename = $this->pp_util->sanitize_filename(
            $job_name . '_return_envelope_' . '.pdf'
        );

        try {
            // CREATE THE CANVAS
            $image = new \Imagick();

            // set image properties
            $image->setResolution( 300, 300 );
            $image->readImage( plugin_dir_path( __FILE__ ) . $envelope_template );
            $image->setImageColorspace( Imagick::COLORSPACE_SRGB );
            $image->setImageUnits( Imagick::RESOLUTION_PIXELSPERINCH );
            $image_format = ! $is_preview ? 'pdf' : 'png';
            $image->setImageFormat( $image_format );

            // draws text onto canvas
            $draw = $this->imagick->draw_text( $address_text );
            $image->drawImage( $draw );

            // write to uploads if not preview
            if ( !  $is_preview ) {
                $file = $this->imagick->write_to_uploads( $image, 'mass_mailer', $filename );
            } else {
                // else write it to assets for preview access
                $image->writeImage( plugin_dir_path( __FILE__ ) . '../public/assets/mm_return_env_preview.png' );
            }

            return isset( $file ) ? $file : null;

        } catch ( Exception $e ) {
            // LOG ERROR IF WE CANNOT CREATE THE RETURN ENVELOPE
            $err_message = 'Could not generate return mail template.';
            var_dump( $e );
            echo $err_message;
            error_log( $err_message );
            error_log( json_encode( ( array ) $e, JSON_PRETTY_PRINT ) );
            die();
        }
    }

    /**
     * Get Field ID
     *
     * Returns the field ID with the given from. This removes the need to pass an entry object
     * especially since it is not available in "generate_order_item_pdfs" callback method.
     * This method also consolidates the previously separate "get" methods.
     * @param $form        GF Form Obj  - Gravity forms object. Contains field array.
     * @param $type        string       - form field type (radio, text, textarea, etc.)
     * @param $admin_label string       - User defined admin label
     * @return mixed       string       - field ID in string format
     * @throws Exception                - throws exception when field ID can't be found
     */
    public function get_field_id( $form, $type, $admin_label )
    {
        $res = null;

        // iterate through GF form fields
        foreach( $form['fields'] as $field )
        {
            // match the field type and admin label to retrieve the field ID
            if ( $field['type'] === $type && $field['adminLabel'] === $admin_label ) {
                $res = $field['id'];
                break;
            }
        }

        // if no field ID was retrieved, throw an exception
        if ( $res === null ) {
            throw new Exception('Cannot find find admin label "' . $admin_label . '" for type "' . type . '".');
        }

        return $res;
    }

    /**
     * Return Envelope File Path
     *
     * Returns relative filepath for #9 envelope types. Will used template
     * file to produce final return envelope PDF.*
     * @param $template_type    - template type defined in form
     * @return string|null      - null or string of template filepath
     */
    private function _return_envelope_file_path( $template_type )
    {
        $res = null;

        $atu_template = '../public/assets/9_VTA_ATU_TEMPLATE.pdf';
        $regular_template = '../public/assets/9_VTA_REG_TEMPLATE.pdf';

        // assign the correct filepath based on return_address field value
        if ( $template_type === 'Regular' ) {
            $res = $regular_template;
        } elseif ( $template_type === 'ATU' ) {
            $res = $atu_template;
        }

        return $res;
    }

    /**
     * Return Envelope Preview AJAX handler
     *
     * Takes form data from custom AJAX call and converts data into return envelope preview.
     * Pass form data to "return_envelope_template"
     * @TODO - add exception handling
     */
    public function handle_return_envelope_preview()
    {
        // extract text and template type
        $return_address = $_POST['address'];
        $template_type = $_POST['template_type'];

        // generate template first
        $this->create_return_envelope_template( $return_address, $template_type, $preview = true );

        // return HTML element (<img> w/ image source)
        include plugin_dir_path( __DIR__ ) . 'public/partials/return-envelope-preview.php';

        exit;
    }

    /**
     * Form Submission Hook callback
     *
     * Callback to be used in "gform_pre_submission" hook.
     * Save form object & entry object to be used to process images and attach to the cart
     * @param $entry    - GF entry object
     * @param $form     - GF form object
     */
    public function mm_form_submission( $entry, $form )
    {
        // assign form & entry objects values to private variables (in case it has not been set yet)
        ! isset( $this->entry ) && $this->__set( 'entry', $entry );
        ! isset( $this->gf_form ) && $this->__set( 'gf_form', $form );

        error_log('calling from mm_form_submission');
    }

    /**
     * Generate Order Item PDFs
     *
     * Callback for "woocommerce_checkout_create_order_line_item" hook. Fires when the user checkouts their order.
     * @param $item
     * @param $cart_item_key
     * @param $values
     * @param $order
     * @throws Exception
     */
    public function generate_order_item_pdfs( $item, $cart_item_key, $values, $order )
    {
        // similar to entry object but w/o entry info (has form values, form id, etc.)
        error_log(json_encode($values, JSON_PRETTY_PRINT));
        $entry_values = $values['_gravity_form_lead'];

        // extract form_id from cart item info
        $form = GFAPI::get_form( $entry_values['form_id'] );

        // extract require_return_env to check if user required a return envelope
        $require_return_env_id = $this->get_field_id( $form, 'radio', 'require_return_env' );
        $require_return_env = $entry_values[$require_return_env_id];

        // if return env was required, the extract the field values and generate the PDF
        if ( $require_return_env === 'Yes' ) {

            // grab field IDs for the following values
            $return_envelope_template_id = $this->get_field_id( $form, 'radio', 'return_envelope_template' );
            $return_address_id = $this->get_field_id( $form, 'textarea', 'return_address' );
            $job_name_id = $this->get_field_id( $form, 'text', 'job_name' );

            // grab the field values with the field_id's
            $return_envelope_template = $entry_values[$return_envelope_template_id];
            $return_address = $entry_values[$return_address_id];
            $job_name = $entry_values[$job_name_id];

            // used the extracted return address, env type, and job name to create the file and attach it to the order item
            $filepath = $this->create_return_envelope_template( $return_address, $return_envelope_template, false, $job_name );

            error_log($filepath);

            // attach hyperlink w/ filepath to the order item
            $item->add_meta_data(
                __( 'Return Envelope Download' ),
                '<a href="' . esc_url( $filepath ) . '" download>' . $job_name . ' - #9 Return Envelope</a>'
            );

        }

    }
    /**
     * SETTER
     *
     * Set private class members outside of constructor.
     * Make accessible outside of class scope.
     * @param $property     - private member variable
     * @param $value        - value
     */
    public function __set( $property, $value )
    {
        if ( property_exists( $this, $property ) ) {
            $this->$property = $value;
        }
    }
    public function mass_mailer_addresses( $form, $field, $uploaded_filename, $tmp_file_name, $file_path ) {
        if ($field['adminLabel'] === 'addresses_file') {
            error_log('Addresses FILE');
            $parser = $this->pp_util->create_excel_parser($file_path);
            $addresses = $parser->parse_excel("PHP");
            error_log(print_r($addresses, true));
            foreach ($addresses as $address) {
            }
        } else {
            error_log($field['adminLabel']);
        }
    }
    /**
     * @param $entry
     * @param $form
     * @throws Exception
     */
    public function get_mass_mailer_entry_id($entry, $form) {
        if (isset($entry['id'])) {
            $this->entry_id = $entry['id'];
            error_log("Entry ID: --- {$entry['id']}");
        } else {
            throw new Exception("Entry ID not set.");
        }
    }
}

