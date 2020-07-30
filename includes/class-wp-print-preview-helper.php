<?php

class Wp_Print_Preview_Helper
{
    /**
     * @param $entry - Gravity Forms entry object
     * @param $create25up - flag to indicate to create 25-up PDF (not required in preview)
     * @return string
     * @throws ImagickException
     */
    public function business_card_proof( $entry, $create25up, $isPreview )
    {
        // Store entry_id in SESSION
        // i.e. /?add_to_cart=39 will have access to this entry_id upon "Add Order"
        $_SESSION['entry_id'] = $entry['id'];

        // retrieve array of IDs
        $field_ids = $this->_retrieveFieldIds();

        // GF form input
        $job_title = $entry[$field_ids['job_title']];
        $first_name = $entry[$field_ids['first_name']];
        $last_name = $entry[$field_ids['last_name']];
        $full_name = $first_name . ' ' . $last_name;
        $department = $entry[$field_ids['department']];
        $email = $entry[$field_ids['email']];
        $address = $entry[$field_ids['address']];
        $phone = $this->_convertPhoneFormat( $entry[$field_ids['phone']] );

        // indentation for text
        $x_indentation = 98;

        // character spacing
        $CHAR_SPACE = 2.3;

        // stroke width
        $STROKE_WIDTH = 2;

        // COLOR CONTANTS
        $LIGHT_BLUE = '#4CB4E7';
        $DARK_BLUE = '#29588C';
        $DARK_GRAY = '#4C4E56';

        /** text draw params */
        // NAME
        $name_text = array(
            'font' => plugin_dir_path( __DIR__ ) . '/public/assets/MuseoSans_700.otf',
            'color' => $DARK_BLUE,
            'stroke_width' => $STROKE_WIDTH,
            'font_size' => 9,
            'kerning' => ($CHAR_SPACE - 1.1),
            'annotation' => array( 'x' => $x_indentation, 'y' => 560, 'text' => $full_name )
        );
        // JOB TITLE
        $job_title_text = array(
            'font' => plugin_dir_path( __DIR__ ) . '/public/assets/MuseoSans_300.otf',
            'color' => $DARK_GRAY,
            'stroke_width' => $STROKE_WIDTH,
            'font_size' => 7.5,
            'kerning' => $CHAR_SPACE,
            'annotation' => array( 'x' => $x_indentation, 'y' => 630, 'text' => $job_title )
        );
        // DEPARTMENT
        $department_text = array(
            'font' => plugin_dir_path( __DIR__ ) . '/public/assets/MuseoSans_300.otf',
            'color' => $DARK_GRAY,
            'stroke_width' => $STROKE_WIDTH,
            'font_size' => 7.5,
            'kerning' => $CHAR_SPACE,
            'annotation' => array( 'x' => $x_indentation, 'y' => 750, 'text' => $department )
        );
        // ADDRESS
        $address_text = array(
            'font' => plugin_dir_path( __DIR__ ) . '/public/assets/MuseoSans_300.otf',
            'color' => $DARK_GRAY,
            'stroke_width' => $STROKE_WIDTH,
            'font_size' => 7.5,
            'kerning' => $CHAR_SPACE,
            'annotation' => array( 'x' => $x_indentation, 'y' => 835, 'text' => $address )
        );
        // EMAIL LABEL
        $email_label_text = array(
            'font' => plugin_dir_path( __DIR__ ) . '/public/assets/MuseoSans_300.otf',
            'color' => $DARK_GRAY,
            'stroke_width' => $STROKE_WIDTH,
            'font_size' => 7.5,
            'kerning' => $CHAR_SPACE,
            'annotation' => array( 'x' => $x_indentation, 'y' => 920, 'text' => 'Email' )
        );
        // EMAIL TEXT
        $email_text = array(
            'font' => plugin_dir_path( __DIR__ ) . '/public/assets/MuseoSans_300.otf',
            'color' => $LIGHT_BLUE,
            'stroke_width' => $STROKE_WIDTH,
            'font_size' => 7.5,
            'kerning' => $CHAR_SPACE,
            'annotation' => array( 'x' => $x_indentation + 188, 'y' => 920, 'text' => $email )
        );
        // PHONE LABEL
        $phone_label_text = array(
            'font' => plugin_dir_path( __DIR__ ) . '/public/assets/MuseoSans_300.otf',
            'color' => $DARK_GRAY,
            'stroke_width' => $STROKE_WIDTH,
            'font_size' => 7.5,
            'kerning' => $CHAR_SPACE,
            'annotation' => array( 'x' => $x_indentation, 'y' => 1003, 'text' => 'Phone' )
        );
        // PHONE
        $phone_text = array(
            'font' => plugin_dir_path( __DIR__ ) . '/public/assets/MuseoSans_300.otf',
            'color' => $LIGHT_BLUE,
            'stroke_width' => $STROKE_WIDTH,
            'font_size' => 7.5,
            'kerning' => $CHAR_SPACE,
            'annotation' => array( 'x' => $x_indentation + 220, 'y' => 1003, 'text' => $phone )
        );

        // combine text params into one array
        $text_params_arr = array(
            $name_text,
            $department_text,
            $job_title_text,
            $address_text,
            $email_label_text, $email_text,
            $phone_label_text, $phone_text
        );

        // CONDITIONALLY ADD MOBILE FIELD IF EXISTS
        if ( !empty( $entry[$field_ids['mobile']] ) ) {

            // grab mobile from entry & convert format
            $mobile = $this->_convertPhoneFormat( $entry[$field_ids['mobile']] );

            // MOBILE LABEL
            $mobile_label_text = array(
                'font' => plugin_dir_path( __DIR__ ) . '/public/assets/MuseoSans_300.otf',
                'color' => $DARK_GRAY,
                'stroke_width' => $STROKE_WIDTH,
                'font_size' => 7.5,
                'kerning' => $CHAR_SPACE,
                'annotation' => array( 'x' => $x_indentation + 684, 'y' => 1003, 'text' => '| Mobile' )
            );
            // MOBILE
            $mobile_text = array(
                'font' => plugin_dir_path( __DIR__ ) . '/public/assets/MuseoSans_300.otf',
                'color' => $LIGHT_BLUE,
                'stroke_width' => $STROKE_WIDTH,
                'font_size' => 7.5,
                'kerning' => $CHAR_SPACE,
                'annotation' => array( 'x' => $x_indentation + 951, 'y' => 1003, 'text' => $mobile )
            );

            // add to array
            array_push( $text_params_arr, $mobile_label_text, $mobile_text );

        }

        // CONDITIONALLY ADD FAX FIELD IF EXISTS
        if ( !empty( $entry[$field_ids['fax']] ) ) {

            // grab fax from entry & convert format
            $fax = $this->_convertPhoneFormat( $entry[$field_ids['fax']] );

            // FAX LABEL
            $fax_label_text = array(
                'font' => plugin_dir_path( __DIR__ ) . '/public/assets/MuseoSans_300.otf',
                'color' => $DARK_GRAY,
                'stroke_width' => $STROKE_WIDTH,
                'font_size' => 7.5,
                'kerning' => $CHAR_SPACE,
                'annotation' => array( 'x' => $x_indentation, 'y' => 1088, 'text' => 'Fax' )
            );
            // FAX
            $fax_text = array(
                'font' => plugin_dir_path( __DIR__ ) . '/public/assets/MuseoSans_300.otf',
                'color' => $LIGHT_BLUE,
                'stroke_width' => $STROKE_WIDTH,
                'font_size' => 7.5,
                'kerning' => $CHAR_SPACE,
                'annotation' => array( 'x' => $x_indentation + 122, 'y' => 1088, 'text' => $fax )
            );

            // add to array
            array_push( $text_params_arr, $fax_label_text, $fax_text );
        }

        $image = new \Imagick();
        $image->readImage( plugin_dir_path( __FILE__ ) . '../public/assets/blank.png' );
        $image->setImageColorspace( Imagick::COLORSPACE_SRGB );
        $image->setImageUnits( Imagick::RESOLUTION_PIXELSPERINCH );
        $image->setResolution( 600, 600 );
        $image->setImageResolution( 300, 300 );

        $image->setImageFormat( 'pdf' );

        foreach ( $text_params_arr as $text_params ) {

            $draw = $this->_drawText( $text_params );
            $image->drawImage( $draw );

        }

        // Form entry_id added to PDF preview
        $entry_filename = 'bizcard_' . $first_name . '_' . $last_name . '_entry_' . $entry['id'];

        $image->setFilename( $entry_filename );

        // assets/ directory path
        $assets_dir = plugin_dir_path( __DIR__ ) . 'public/assets/';

        // write to WC Product
        // $image->writeImage($assets_dir . $entry_filename . '.pdf');

        // write Image to /wp-content/uploads/business_cards
//        $this->_writeToUploads( $image, $entry_filename . '.pdf' );

        /** CREATE PNG FILE FOR PREVIEW */
        // Switch format to PNG
        $image->setImageFormat( 'png' );

        // Filename for temporary file Preview
        $temp_file = 'business_card_template';

        // write latest file to entry for preview
        $image->writeImage( $assets_dir . $temp_file . '.png' );

        // only write PDF if it is not preview
        if ( ! $isPreview )
        {
            /**
             * temp workaround to replace the above. Used the command line to write PDF file
             * from temp_file
             */
            $source = $assets_dir . $temp_file . '.png';
            $uploads_dir = wp_upload_dir();
            $target = $uploads_dir['basedir'] . '/business_cards/' . $entry_filename . '.pdf';
            $target = str_replace( ' ', '_', $target );

            // output & exit code for command line
            $output = [];
            $res = 0;
            // define PATH
            putenv( 'PATH=/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin' );
            // run magick command to run
            exec( 'magick convert ' . $source . ' -resize 50% quality 300 ' . $target . ' 2>&1', $output, $res );

            if ( $res > 0 ) {
                error_log( json_encode( $output, JSON_PRETTY_PRINT ) );
            }

            // create 25 up output on 12 x 18 if flag is set
            if ( $create25up ) {
                $this->_create25Up( $image, $entry_filename );
            }
        }

        // return the img filename to shortcode
        return $temp_file;
    }

    /**
     * Grabs field IDs dynamically based on field label names.
     * @return array - array of field ids
     */
    private function _retrieveFieldIds()
    {
        // grab Business Card GF Form object
        $form = GFAPI::get_form( 4 );

        // iterate through all field objects in Form
        foreach ( $form['fields'] as $field ) {
            // use labels as keys to dynamically retrieve field ids
            switch ( $field['adminLabel'] ) {
                case 'job_title':
                    $job_title_id = $field['id'];
                    break;
                case 'name':
                    foreach ( $field['inputs'] as $subfield ) {
                        if ( $subfield['label'] === 'First' ) {
                            $firstname_id = $subfield['id'];
                        } elseif ( $subfield['label'] === 'Last' ) {
                            $lastname_id = $subfield['id'];
                        }
                    }
                    break;
                case 'department':
                    $department_id = $field['id'];
                    break;
                case 'email':
                    foreach ( $field['inputs'] as $subfield ) {
                        if ( $subfield['label'] === 'Enter Email' ) {
                            $email_id = $subfield['id'];
                        }
                    }
                    break;
                case 'address':
                    $address_id = $field['id'];
                    break;
                case 'phone':
                    $phone_id = $field['id'];
                    break;
                case 'mobile':
                    $moble_id = $field['id'];
                    break;
                case 'fax':
                    $fax_id = $field['id'];
                    break;
            }

        }

        return array(
            'job_title' => $job_title_id,
            'first_name' => $firstname_id,
            'last_name' => $lastname_id,
            'department' => $department_id,
            'email' => $email_id,
            'address' => $address_id,
            'phone' => $phone_id,
            'mobile' => $moble_id,
            'fax' => $fax_id
        );
    }

    /**
     * @param $params
     * @return ImagickDraw
     */
    private function _drawText( $params )
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
        $draw->annotation( $x, $y, $text );

        return $draw;
    }

    /**
     * convert format (XXX)XXX-XXXX to XXX-XXX-XXXX
     *
     * @param $str - raw string from phone field
     * @return string - converted string format with missing dashes and removed parentheses
     */
    private function _convertPhoneFormat( $str )
    {
        preg_match( '/^\((\d{3})\) (\d{3})-(\d{4})$/', $str, $matches );
        return $matches[1] . '-' . $matches[2] . '-' . $matches[3];
    }

    /**
     * uploading files programmatically in to wp-content/uploads/
     * @see - https://artisansweb.net/upload-files-programmatically-wordpress/
     * @param $image - Image Magick image object
     * @param $filename - name of file to be saved as
     */
    private function _writeToUploads( $image, $filename )
    {
        $upload_dir = wp_upload_dir();

        // Check if base directory exists for uploads/
        if ( !empty( $upload_dir['basedir'] ) ) {

            $bc_dirname = $upload_dir['basedir'] . '/business_cards';

            //  create a new directory for business cards if it does not exist
            if ( !file_exists( $bc_dirname ) ) {
                wp_mkdir_p( $bc_dirname );
            }

            /**
             * Write the new file to wp-content/uploads via Image Magick
             *
             * For Ubuntu servers, please change uploads folder's group ownership
             * @see - https://stackoverflow.com/questions/15716428/cannot-save-thumbnail-with-imagick
             */
            $image->writeImage( $bc_dirname . '/' . $filename );
            // save into database $upload_dir['baseurl'].'/product-images/'.$filename;
        }
    }

    /**
     * Creates and writes a 25 up Business Card printout on a 12 x 18 stock
     * @param $image - Image Magick object (Business Card)
     * @param $filename - filename of business card PDF
     */
    private function _create25Up( $image, $filename )
    {
        // new filename for 25-up PDF
//        $new_filename = $filename . '_25_up.pdf';
//
//        // create Stack of Images (i.e. 5x5)
//        $stack = new Imagick();
//
//        // create 25 images in the stack at 300 DPI
//        for ( $i = 0; $i < 25; $i++ )
//        {
//            $stack->addImage( $image );
//            $stack->setImageColorspace( Imagick::COLORSPACE_SRGB );
//            $stack->setImageUnits( Imagick::RESOLUTION_PIXELSPERINCH );
//            $stack->setResolution( 600, 600 );
//            $stack->setImageResolution( 300, 300 );
//        }
//        // Create raw 17.5" x 10" 25 up (before adding padding to the edges for 18" x 12" centering)
//        $montage = $stack->montageImage( new ImagickDraw(), '5x5', '2100x1200', 0, 0);
//
//        // create padding to center 25-up on 18" x 12" (Note: 300px = 1 in.)
//        $horizontalPadding = 300;           // 0.5 in.
//        $verticalPadding = 1200;            // 2 in.
//        $offsetX = $horizontalPadding / 2;  // 0.25 in.
//        $offsetY = $verticalPadding / 2;    // 2 in.
//        $montage->extentImage( 10800, 7200, -$offsetX, -$offsetY );
//
//        $this->_writeToUploads( $montage, $new_filename );
        /**
         * temp workaround to replace the above. Used the command line to use temp PNG file
         * to create another temporary 25-up PNG to preserve resolution. Then we can convert and
         * resize w/ 300 DPI and establish border for 12x18 layout from temp_file.
         */
        $assets_dir = plugin_dir_path( __DIR__ ) . 'public/assets/';
        $temp_file = 'business_card_template';

        // Used to create temp 25-up PNG image to retain resolution
        $base_png = $assets_dir . $temp_file . '.png';

        $uploads_dir = wp_upload_dir();

        // Create targets for temp 25-up PNG and final 25-up PDF output
        $temp_25_png = $uploads_dir['basedir'] . '/business_cards/' . $filename . '_25_up.png';
        $final_25_pdf = $uploads_dir['basedir'] . '/business_cards/' . $filename . '_25_up.pdf';

        // output & exit code for command line
        $output = [];
        $res = 0;

        /**
         * RUN COMMAND (1 command to catch exception as soon as it occurs)
         * Commands in order – separated by lines
         * 1. Create 25-up with PNG to retain resolution
         * 2. Convert to PDF and add border.
         * 3. Remove temp 25-up PNG from business_cards directory
         */
        // define PATH
        putenv( 'PATH=/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin' );
        exec(
            'magick montage ' . $base_png . ' -mode concatenate -duplicate 24 -tile 5x5 ' . $temp_25_png . ' && ' .
            'magick convert ' . $temp_25_png . ' -density 600 -bordercolor white -border 150x600 ' . $final_25_pdf . ' && ' .
            'rm ' . $temp_25_png,
            $output,
            $res
        );

        // exit with error code if command unsuccessful
        if ( $res > 0 ) {
            error_log( 'Error Code: ' . $res . "\n" . json_encode( $output, JSON_PRETTY_PRINT ) );
            var_dump( $output );
            var_dump( $res );
            exit( $res );
        }
    }
}
