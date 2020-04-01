<?php

Class Wp_Print_Preview_Helper
{
    public function view()
    {

    }

    /**
     * Check if current user matches entry user. Notifies user if not and provides login or home page
     */
    public function check_entry_ownership()
    {
        // @TODO - current workaround. Fires within editor (missing query param causes error)
        if (!isset($_GET['entry_id'])) {
            return;
        }

        // entry_id provided by query param
        $entry = GFAPI::get_entry($_GET['entry_id']);
        $current_user = wp_get_current_user();
        ($current_user->exists()) ? $current_user_id = $current_user->ID : $current_user_id = 0;

        if (is_wp_error($entry['created_by'])) {
            trigger_error('Gravity Forms::get_entry - ' . $entry->get_error(), E_ERROR);
        }else {
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
            /**
             * @todo clean-up
             * @see https://www.php.net/manual/en/function.return.php
             * @jpham93 - this code is never reached after the above **return** is sent...
             * exit();
             **/
        }

    }

    public function business_card_proof($entry)
    {
        // Store entry_id in SESSION
        // i.e. /?add_to_cart=39 will have access to this entry_id upon "Add Order"
        $_SESSION['entry_id'] = $entry['id'];

        // retrieve array of IDs
        $field_ids = $this->_retrieveFieldIds();

        // GF form input
        $job_title = $entry[ $field_ids['job_title'] ];
        $first_name = $entry[ $field_ids['first_name'] ];
        $last_name = $entry[ $field_ids['last_name']];
        $full_name = $first_name . ' ' . $last_name;
        $department = $entry[ $field_ids['department'] ];
        $email = $entry[ $field_ids['email'] ];
        $address = $entry[ $field_ids['address'] ];
        $phone = $this->_convertPhoneFormat($entry[ $field_ids['phone'] ]);

<<<<<<< HEAD
=======

// @todo clean-up the debugging and development statements

//        echo "<pre>";
//        var_dump($field_ids);
//        echo "\n--------------------------\n";
//        var_dump(GFAPI::get_field(4, 8));
//        echo "\n--------------------------\n";
//        var_dump(GFAPI::get_form(4));
//        var_dump($entry);
//        echo "</pre>";

>>>>>>> 260b572a87d4e7c2239ca388e56d27f53ae017bb
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
        // SLOGAN
        $slogan_text = array(
            'font' => plugin_dir_path(__DIR__) . '/public/assets/MuseoSans_300_Italic.otf',
            'color' => $LIGHT_BLUE,
            'stroke_width' => $STROKE_WIDTH,
            'font_size' => 6.5,
            'kerning' => $CHAR_SPACE,
            'annotation' => array('x' => 1261, 'y' => 435, 'text' => 'Solutions that move you')
        );
        // NAME
        $name_text = array(
            'font' => plugin_dir_path(__DIR__) . '/public/assets/MuseoSans_700.otf',
            'color' => $DARK_BLUE,
            'stroke_width' => $STROKE_WIDTH,
            'font_size' => 9,
            'kerning' => ($CHAR_SPACE - 1.1),
            'annotation' => array('x' => $x_indentation, 'y' => 560, 'text' => $full_name)
        );
        // JOB TITLE
        $job_title_text = array(
            'font' => plugin_dir_path(__DIR__) . '/public/assets/MuseoSans_300.otf',
            'color' => $DARK_GRAY,
            'stroke_width' => $STROKE_WIDTH,
            'font_size' => 7.5,
            'kerning' => $CHAR_SPACE,
            'annotation' => array('x' => $x_indentation, 'y' => 630, 'text' => $job_title)
        );
        // DEPARTMENT
        $department_text = array(
            'font' => plugin_dir_path(__DIR__) . '/public/assets/MuseoSans_300.otf',
            'color' => $DARK_GRAY,
            'stroke_width' => $STROKE_WIDTH,
            'font_size' => 7.5,
            'kerning' => $CHAR_SPACE,
            'annotation' => array('x' => $x_indentation, 'y' => 750, 'text' => $department)
        );
        // ADDRESS
        $address_text = array(
            'font' => plugin_dir_path(__DIR__) . '/public/assets/MuseoSans_300.otf',
            'color' => $DARK_GRAY,
            'stroke_width' => $STROKE_WIDTH,
            'font_size' => 7.5,
            'kerning' => $CHAR_SPACE,
            'annotation' => array('x' => $x_indentation, 'y' => 835, 'text' => $address)
        );
        // EMAIL LABEL
        $email_label_text = array(
            'font' => plugin_dir_path(__DIR__) . '/public/assets/MuseoSans_300.otf',
            'color' => $DARK_GRAY,
            'stroke_width' => $STROKE_WIDTH,
            'font_size' => 7.5,
            'kerning' => $CHAR_SPACE,
            'annotation' => array('x' => $x_indentation, 'y' => 920, 'text' => 'Email')
        );
        // EMAIL TEXT
        $email_text = array(
            'font' => plugin_dir_path(__DIR__) . '/public/assets/MuseoSans_300.otf',
            'color' => $LIGHT_BLUE,
            'stroke_width' => $STROKE_WIDTH,
            'font_size' => 7.5,
            'kerning' => $CHAR_SPACE,
            'annotation' => array('x' => $x_indentation + 188, 'y' => 920, 'text' => $email)
        );
        // PHONE LABEL
        $phone_label_text = array(
            'font' => plugin_dir_path(__DIR__) . '/public/assets/MuseoSans_300.otf',
            'color' => $DARK_GRAY,
            'stroke_width' => $STROKE_WIDTH,
            'font_size' => 7.5,
            'kerning' => $CHAR_SPACE,
            'annotation' => array('x' => $x_indentation, 'y' => 1003, 'text' => 'Phone')
        );
        // PHONE
        $phone_text = array(
            'font' => plugin_dir_path(__DIR__) . '/public/assets/MuseoSans_300.otf',
            'color' => $LIGHT_BLUE,
            'stroke_width' => $STROKE_WIDTH,
            'font_size' => 7.5,
            'kerning' => $CHAR_SPACE,
            'annotation' => array('x' => $x_indentation + 220, 'y' => 1003, 'text' => $phone)
        );

        // combine text params into one array
        $text_params_arr = array(
            $slogan_text,
            $name_text,
            $department_text,
            $job_title_text,
            $address_text,
            $email_label_text, $email_text,
            $phone_label_text, $phone_text
        );

        // CONDITIONALLY ADD MOBILE FIELD IF EXISTS
        if ( !empty($entry[ $field_ids['mobile'] ]) ) {

            // grab mobile from entry & convert format
            $mobile = $this->_convertPhoneFormat($entry[ $field_ids['mobile'] ]);

            // MOBILE LABEL
            $mobile_label_text = array(
                'font' => plugin_dir_path(__DIR__) . '/public/assets/MuseoSans_300.otf',
                'color' => $DARK_GRAY,
                'stroke_width' => $STROKE_WIDTH,
                'font_size' => 7.5,
                'kerning' => $CHAR_SPACE,
                'annotation' => array('x' => $x_indentation + 684, 'y' => 1003, 'text' => '| Mobile')
            );
            // MOBILE
            $mobile_text = array(
                'font' => plugin_dir_path(__DIR__) . '/public/assets/MuseoSans_300.otf',
                'color' => $LIGHT_BLUE,
                'stroke_width' => $STROKE_WIDTH,
                'font_size' => 7.5,
                'kerning' => $CHAR_SPACE,
                'annotation' => array('x' => $x_indentation + 951, 'y' => 1003, 'text' => $mobile)
            );

            // add to array
            array_push($text_params_arr, $mobile_label_text, $mobile_text);

        }

        // CONDITIONALLY ADD FAX FIELD IF EXISTS
        if ( !empty($entry[ $field_ids['fax'] ]) ) {

            // grab fax from entry & convert format
            $fax = $this->_convertPhoneFormat($entry[ $field_ids['fax'] ]);

            // FAX LABEL
            $fax_label_text = array(
                'font' => plugin_dir_path(__DIR__) . '/public/assets/MuseoSans_300.otf',
                'color' => $DARK_GRAY,
                'stroke_width' => $STROKE_WIDTH,
                'font_size' => 7.5,
                'kerning' => $CHAR_SPACE,
                'annotation' => array('x' => $x_indentation, 'y' => 1088, 'text' => 'Fax')
            );
            // FAX
            $fax_text = array(
                'font' => plugin_dir_path(__DIR__) . '/public/assets/MuseoSans_300.otf',
                'color' => $LIGHT_BLUE,
                'stroke_width' => $STROKE_WIDTH,
                'font_size' => 7.5,
                'kerning' => $CHAR_SPACE,
                'annotation' => array('x' => $x_indentation + 122, 'y' => 1088, 'text' => $fax)
            );

            // add to array
            array_push($text_params_arr, $fax_label_text, $fax_text);
        }

        $image = new \Imagick();
        $image->readImage(plugin_dir_path(__FILE__).'../public/template.png');
        $image->setImageColorspace(Imagick::COLORSPACE_SRGB);
        $image->setImageUnits(Imagick::RESOLUTION_PIXELSPERINCH);
        $image->setResolution(600,600);
        $image->setImageResolution(300, 300);

        $image->setImageFormat('pdf');

        foreach ( $text_params_arr as $text_params) {
            $draw = $this->_drawText($text_params);
            $image->drawImage($draw);
        }

        // Filename for the latest preview created
        $temp_file = 'business_card_template';

        // Form entry_id added to png preview
        $entry_filename = 'business_card';

        $image->setFilename($entry_filename);

        // write latest file to entry
        $image->writeImage(plugin_dir_path(__FILE__).'../public/' . $temp_file . '.png');

        // write to WC Product
        $image->writeImage(plugin_dir_path(__FILE__).'../public/' . $entry_filename . '.pdf');


        // write Image to /wp-content/uploads/business_cards
        $this->_copyToUploads();

        // return to shortcode to preview bc proof
        return $temp_file;
    }

    /**
     * Grabs field IDs dynamically based on field label names.
     * @return array - array of field ids
     */
    private function _retrieveFieldIds() {
        // grab Business Card GF Form object
        $form = GFAPI::get_form(4);

        // iterate through all field objects in Form
        foreach($form['fields'] as $field)
        {
            // use labels as keys to dynamically retrieve field ids
            switch($field['adminLabel'])
            {
                case 'job_title':
                    $job_title_id = $field['id'];
                    break;
                case 'name':
                    foreach($field['inputs'] as $subfield)
                    {
                        if ($subfield['label'] === 'First') {
                            $firstname_id =  $subfield['id'];
                        } elseif ($subfield['label'] === 'Last') {
                            $lastname_id = $subfield['id'];
                        }
                    }
                    break;
                case 'department':
                    $department_id = $field['id'];
                    break;
                case 'email':
                    foreach($field['inputs'] as $subfield)
                    {
                        if ($subfield['label'] === 'Enter Email') {
                            $email_id =  $subfield['id'];
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
    private function _drawText($params)
    {
        $draw = new ImagickDraw();

        $draw->setFont($params['font']);
        $draw->setFillColor($params['color']);
        $draw->setStrokeColor($params['color']);
        $draw->setStrokeWidth($params['stroke_width']);
        $draw->setFontSize($params['font_size']);
        $draw->setTextKerning($params['kerning']);

        // positioning + text
        $x = $params['annotation']['x'];
        $y = $params['annotation']['y'];
        $text = $params['annotation']['text'];
        $draw->annotation($x, $y, $text);

        return $draw;
    }

    /**
     * convert format (XXX)XXX-XXXX to XXX-XXX-XXXX
     *
     * @param $str
     * @return string
     */
    private function _convertPhoneFormat($str)
    {
        preg_match('/^\((\d{3})\) (\d{3})-(\d{4})$/', $str, $matches );
        return $matches[1] . '-' . $matches[2] . '-' . $matches[3];
    }

    /**
     * uploading files programmatically in WordPress
     * @see - https://artisansweb.net/upload-files-programmatically-wordpress/
     */
    private function _copyToUploads($file)
    {
        $upload_dir = wp_upload_dir();

        if ( ! empty( $upload_dir['basedir'] ) ) {
            $bc_dirname = $upload_dir['basedir'] . '/business_card';

            //  create a new directory for business cards if it does not exist
            if ( ! file_exists( $bc_dirname ) ) {
                wp_mkdir_p( $bc_dirname );
            }

            $filename = wp_unique_filename( $bc_dirname, $_FILES['file']['name'] );
            move_uploaded_file($file, $bc_dirname .'/'. $filename);
            // save into database $upload_dir['baseurl'].'/product-images/'.$filename;
        }
    }

}
