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
            exit();
        }

    }

    /**
     * @todo - removed from public hooks - we should discuss merits versus GF built-in calls
     * Callback to redirect to business-card-edit. Contains
     */
    public function business_card_edit_redirect()
    {
        if ( isset($_POST['edit']) ) {
            // grab entry_id and its respective field/values
            $entry_id = $_GET['entry_id'];
            $entry = GFAPI::get_entry($entry_id);
            $job_title = $entry[1];
            $first_name = $entry['2.3'];
            $last_name = $entry['2.6'];
            $email = $entry[3];
            $address = $entry[4];

            wp_redirect('/business-card-edit/?entry_id=' . $entry_id);
        }
    }

    public function business_card_proof($entry)
    {
        // retrieve array of IDs
        $field_ids = $this->_retrieveFieldIds();

        // GF form input
        $job_title = $entry[ $field_ids['job_title'] ];
        $first_name = $entry[ $field_ids['first_name'] ];
        $last_name = $entry[ $field_ids['last_name']];
        $full_name = $first_name . ' ' . $last_name;
        $email = $entry[ $field_ids['email'] ];
        $address = $entry[ $field_ids['address'] ];
        $phone = $this->_convertPhoneFormat($entry[ $field_ids['phone'] ]);

        echo "<pre>";
        var_dump($field_ids);
        echo "\n--------------------------\n";
        var_dump(GFAPI::get_field(4, 8));
        echo "\n--------------------------\n";
        var_dump(GFAPI::get_form(4));
        var_dump($entry);
        echo "</pre>";

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
            'font' => plugin_dir_path(__FILE__) . '/MuseoSans_300_Italic.otf',
            'color' => $LIGHT_BLUE,
            'stroke_width' => $STROKE_WIDTH,
            'font_size' => 6.5,
            'kerning' => $CHAR_SPACE,
            'annotation' => array('x' => 1261, 'y' => 435, 'text' => 'Solutions that move you')
        );
        // NAME
        $name_text = array(
            'font' => plugin_dir_path(__FILE__) . '/MuseoSans_700.otf',
            'color' => $DARK_BLUE,
            'stroke_width' => $STROKE_WIDTH,
            'font_size' => 9,
            'kerning' => $CHAR_SPACE,
            'annotation' => array('x' => $x_indentation, 'y' => 560, 'text' => $full_name)
        );
        // JOB TITLE
        $job_title_text = array(
            'font' => plugin_dir_path(__FILE__) . '/MuseoSans_300.otf',
            'color' => $DARK_GRAY,
            'stroke_width' => $STROKE_WIDTH,
            'font_size' => 7.5,
            'kerning' => $CHAR_SPACE,
            'annotation' => array('x' => $x_indentation, 'y' => 630, 'text' => $job_title)
        );
        // ADDRESS
        $address_text = array(
            'font' => plugin_dir_path(__FILE__) . '/MuseoSans_300.otf',
            'color' => $DARK_GRAY,
            'stroke_width' => $STROKE_WIDTH,
            'font_size' => 7.5,
            'kerning' => $CHAR_SPACE,
            'annotation' => array('x' => $x_indentation, 'y' => 835, 'text' => $address)
        );
        // EMAIL LABEL
        $email_label_text = array(
            'font' => plugin_dir_path(__FILE__) . '/MuseoSans_300.otf',
            'color' => $DARK_GRAY,
            'stroke_width' => $STROKE_WIDTH,
            'font_size' => 7.5,
            'kerning' => $CHAR_SPACE,
            'annotation' => array('x' => $x_indentation, 'y' => 920, 'text' => 'Email')
        );
        // EMAIL TEXT
        $email_text = array(
            'font' => plugin_dir_path(__FILE__) . '/MuseoSans_300.otf',
            'color' => $LIGHT_BLUE,
            'stroke_width' => $STROKE_WIDTH,
            'font_size' => 7.5,
            'kerning' => $CHAR_SPACE,
            'annotation' => array('x' => $x_indentation + 200, 'y' => 920, 'text' => $email)
        );
        // PHONE LABEL
        $phone_label_text = array(
            'font' => plugin_dir_path(__FILE__) . '/MuseoSans_300.otf',
            'color' => $DARK_GRAY,
            'stroke_width' => $STROKE_WIDTH,
            'font_size' => 7.5,
            'kerning' => $CHAR_SPACE,
            'annotation' => array('x' => $x_indentation, 'y' => 1003, 'text' => 'Phone')
        );
        // PHONE
        $phone_text = array(
            'font' => plugin_dir_path(__FILE__) . '/MuseoSans_300.otf',
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
                'font' => plugin_dir_path(__FILE__) . '/MuseoSans_300.otf',
                'color' => $DARK_GRAY,
                'stroke_width' => $STROKE_WIDTH,
                'font_size' => 7.5,
                'kerning' => $CHAR_SPACE,
                'annotation' => array('x' => $x_indentation + 684, 'y' => 1003, 'text' => '| Mobile')
            );
            // MOBILE
            $mobile_text = array(
                'font' => plugin_dir_path(__FILE__) . '/MuseoSans_300.otf',
                'color' => $LIGHT_BLUE,
                'stroke_width' => $STROKE_WIDTH,
                'font_size' => 7.5,
                'kerning' => $CHAR_SPACE,
                'annotation' => array('x' => $x_indentation + 951, 'y' => 1003, 'text' => $mobile)
            );

            // add to array
            array_push($text_params_arr, $mobile_label_text, $mobile_text);

        }

//        $overlay = new \Imagick();
//        $overlay->newImage(300*3.5,300*2,new ImagickPixel('transparent'));
//        $overlay->drawImage($draw);

        $image = new \Imagick();
        $image->readImage(plugin_dir_path(__FILE__).'../public/template.png');
        $image->setImageColorspace(Imagick::COLORSPACE_SRGB);
        $image->setImageUnits(Imagick::RESOLUTION_PIXELSPERINCH);
        $image->setResolution(600,600);
        $image->setImageResolution(300, 300);

        $image->setImageFormat('png');

        foreach ( $text_params_arr as $text_params) {
            $draw = $this->_drawText($text_params);
            $image->drawImage($draw);
        }

        $image->setFilename('newimage');

//        $image->compositeImage($overlay, Imagick::COMPOSITE_BLEND, 50, 50,Imagick::CHANNEL_ALPHA);
        $image->writeImage(plugin_dir_path(__FILE__).'../public/newimage.png');

        return $image->getFilename();
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
            // use labels as keys to dynmically retrieve field ids
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
            }

        }

        return array(
            'job_title' => $job_title_id,
            'first_name' => $firstname_id,
            'last_name' => $lastname_id,
            'email' => $email_id,
            'address' => $address_id,
            'phone' => $phone_id,
            'mobile' => $moble_id
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

}
