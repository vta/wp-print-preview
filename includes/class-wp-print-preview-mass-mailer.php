<?php
require_once "class-wp-print-preview-util.php";
require_once "class-wp-print-preview-imagick.php";
class Wp_Print_Preview_Mass_Mailer
{

    private $entry;
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
     * @param $entry_id     int     - entry_id of GF entry
     * @param $is_preview   boolean - determine if file will be written as a preview or to the uploads folder
     */
    public function return_envelope_template( $entry_id, $is_preview )
    {
        $this->__set( 'entry', GFAPI::get_entry( $entry_id ) );
        $this->__set( 'gf_form', GFAPI::get_form( $this->entry['form_id'] ) );

        $BLACK = '#000000';         // COLOR CONSTANT
        $CHAR_SPACE = 0.3;          // character spacing
        $FONT_SIZE = 10.5;          // FONT SIZE
        $STROKE_WIDTH = 0.7;        // FONT WEIGHT
        $WORD_SPACING = 0.9;        // SPACING BETWEEN WORD
        $LINE_HEIGHT = 4.7;         // LINE HEIGHT
        // ANNOTATIONS
        $X = 1038;                  // X COORD FOR TEXT
        $Y = 598;                   // Y COORD FOR TEXT
        $return_address_text = $this->_return_address_extract(); // ADDRESS STRING
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
            $envelope_template = $this->_return_envelope_type();
            if ( $envelope_template === null ) {
                throw new Exception('Could not find value for "return_envelope_type".');
            }
        } catch (Exception $e) {
            error_log( $e );
            die();
        }

        // CREATE FILENAME
        $job_name = $this->_job_name();
        // if there is no job name, use generic "mm"
        empty( $job ) && $job_name = 'mm';
        // filename = [entry_id]_[job_name].pdf && sanitize from illegal characters
        $filename = $this->pp_util->sanitize_filename( $this->entry['id'] . '_' . $job_name . '.pdf' );

        try {
            // CREATE THE CANVAS
            $image = new \Imagick();

            // set image properties
            $image->setResolution( 300, 300 );
            $image->readImage( plugin_dir_path( __FILE__ ) . $envelope_template );
            $image->setImageColorspace( Imagick::COLORSPACE_SRGB );
            $image->setImageUnits( Imagick::RESOLUTION_PIXELSPERINCH );
            $image_format = $is_preview ? 'png' : 'pdf';
            $image->setImageFormat( $image_format );

            // draws text onto canvas
            $draw = $this->imagick->draw_text( $address_text );
            $image->drawImage( $draw );

            // write to uploads if not preview
            if ( !$is_preview ) {
                $this->imagick->_write_to_uploads( $image, 'mass_mailer', $filename );
            } else {
                // else write it assets for preview access
                $image->writeImage( plugin_dir_path( __FILE__ ) . '../public/assets/mm_preview.png' );
            }

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
     * Extract Return Address
     *
     * Used in Employee/Other mailers when user chooses.
     * @return string|null - return address text
     */
    private function _return_address_extract()
    {
        $res = null;

        // loop through and extract address field
        foreach ( $this->gf_form['fields'] as $form_field )
        {
            // check if field matches for adminLabel "return_address", then return text
            if ( $form_field['type'] === 'textarea' && $form_field['adminLabel'] === 'return_address' ) {
                $res = $this->entry[$form_field['id']];
                break;
            }
        }
        return $res;
    }


    private function _job_name()
    {
        $res = null;

        foreach ( $this->gf_form['fields'] as $form_field )
        {
            // check if field matches for adminLabel "job_name", then return text
            if ( $form_field['type'] === 'text' && $form_field['adminLabel'] === 'job_name' ) {
                $res = $this->entry[$form_field['id']];
                // sanitize the text to be used as a filename.
                break;
            }
        }
        return $res;
    }

    /**
     * Returns relative filepath for #9 envelope types. Will used template
     * file to produce final return envelope PDF.* @return string|null
     */
    private function _return_envelope_type()
    {
        $res = null;

        $atu_template = '../public/assets/9_VTA_ATU_TEMPLATE.pdf';
        $regular_template = '../public/assets/9_VTA_REG_TEMPLATE.pdf';

        // search through fields array in GF object
        $fields_arr = $this->gf_form['fields'];
        $key = array_search(
            'return_envelope_type',
            array_column( $fields_arr, 'adminLabel' )
        );

        // extract corresponding field ID
        $template_type_field_id = $fields_arr[$key]['id'];

        // retrieve field value from entry
        $return_address_value = $this->entry[$template_type_field_id];

        // assign the correct filepath based on return_address field value
        if ( $return_address_value === 'Regular' ) {
            $res = $regular_template;
        } elseif ( $return_address_value === 'ATU' ) {
            $res = $atu_template;
        }

        return $res;
    }

    public function mass_mailer_addresses( $form, $field, $uploaded_filename, $tmp_file_name, $file_path )
    {
        $parser = $this->pp_util->create_excel_parser($file_path);
        $addresses = $parser->parse_excel("PHP");
        error_log(print_r($addresses, true));
        foreach ($addresses as $address) {

        }
    }

    /**
     * Set private class members outside of constructor.
     * Make accessible outside of class scope.
     * @param $property - private member variable
     * @param $value - value
     */
    public function __set( $property, $value )
    {
        if ( property_exists( $this, $property ) ) {
            $this->$property = $value;
        }
    }

}
