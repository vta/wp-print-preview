<?php
require_once "class-wp-print-preview-util.php";
class Wp_Print_Preview_Mass_Mailer
{

    private $entry;
    private $gf_form;
    private $pp_util;

    /**
     * Wp_Print_Preview_Mass_Mailer constructor
     * @param $entry_id
     * @throws Exception
     */
    function __construct()
    {
        $this->pp_util = new Wp_Print_Preview_Util();
        // store Gravity Forms entry & form arrays as private member variable
        // (to be used in most public functions)
//        $this->entry = GFAPI::get_entry( $entry_id );
//        $this->gf_form = GFAPI::get_form( $this->entry['form_id'] );
//        $this->return_envelope_template();
    }

    /**
     * Return Address Template
     *
     * stamps return address in the correct location on the #9 Return Envelope.
     */
    public function return_envelope_template()
    {
        // ADDRESS STRING
        $return_address_text = $this->_return_address_extract();

        // COLOR CONSTANTS
        $BLACK = '#000000';

        // character spacing
        $CHAR_SPACE = 0.4;

        // stroke width
        $STROKE_WIDTH = 0.7;

        /** text draw params */
        // ADDRESS
        $address_text = array(
            'font'         => plugin_dir_path( __DIR__ ) . '/public/assets/MuseoSans_300.otf',
            'color'        => $BLACK,
            'stroke_width' => $STROKE_WIDTH,
            'font_size'    => 10,
            'kerning'      => $CHAR_SPACE,
            'annotation'   => array( 'x' => 345, 'y' => 199, 'text' => $return_address_text ),
            'line_height'  => 2.4
        );

        // ENVELOPE TEMPLATE FILE
        $envelope_template = '../public/assets/9_VTA_REG_GUIDE_TEMPLATE.png';

        try {
            // CREATE THE CANVAS
            $image = new \Imagick();
            $image->setResolution( 100, 100 );
            $image->readImage( plugin_dir_path( __FILE__ ) . $envelope_template );
            $image->setImageColorspace( Imagick::COLORSPACE_SRGB );
            $image->setImageUnits( Imagick::RESOLUTION_PIXELSPERINCH );
            $image->setImageFormat( 'pdf' );
            $draw = $this->_draw_text( $address_text );
            $image->drawImage( $draw );
            $image->writeImage( plugin_dir_path( __FILE__ ) . '/test_file.pdf' );

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
     * Image Magick method for drawing the address text on then envelope
     * @param $params
     * @return ImagickDraw
     */
    private function _draw_text( $params )
    {
        $draw = new ImagickDraw();

        $draw->setFont( $params['font'] );
        $draw->setFillColor( $params['color'] );
        $draw->setStrokeColor( $params['color'] );
        $draw->setStrokeWidth( $params['stroke_width'] );
        $draw->setFontSize( $params['font_size'] );
        $draw->setTextKerning( $params['kerning'] );

        // positioning + text
        $x = $params['annotation']['x'];
        $y = $params['annotation']['y'];
        $text = $params['annotation']['text'];

        // for multiline height text (i.e. textarea values)
        if ( isset( $params['line_height'] ) ) {
            $draw->setTextInterLineSpacing( $params['line_height'] );
        }

        $draw->annotation( $x, $y, $text );

        return $draw;
    }

    /**
     * Extract Return Address
     *
     * Used in Employee/Other mailers when user chooses
     * @return string|null
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

    public function mass_mailer_addresses( $form, $field, $uploaded_filename, $tmp_file_name, $file_path ) {
        $parser = $this->pp_util->create_excel_parser($file_path);
        $addresses = $parser->parse_excel("PHP");
        error_log(print_r($addresses, true));
        foreach ($addresses as $address) {

        }

    }

}
