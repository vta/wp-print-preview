<?php

use Imagick;
use Imagickname;
use ImagickPixel;

Class Demo_Print_Helper
{
    public function view()
    {
        return "<h1>It works!<</h1>";
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
            $first_name = $entry['$CHAR_SPACE'];
            $last_name = $entry['2.6'];
            $email = $entry[3];
            $address = $entry[5];

            wp_redirect('/business-card-edit/?entry_id=' . $entry_id);
        }
    }

    public function business_card_proof($entry)
    {
        // GF form input
        $job_title = $entry[1];
        $first_name = $entry['2.3'];
        $last_name = $entry['2.6'];
        $full_name = $first_name . ' ' . $last_name;
        $email = $entry[3];
        $address = $entry[5];

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

        // combine text params into one array
        $text_params_arr = array( $slogan_text, $name_text, $job_title_text, $address_text, $email_label_text, $email_text );

        // PHONE
        // @TODO - add phone + cell phone field

//        $overlay = new \Imagick();
//        $overlay->newImage(300*3.5,300*2,new ImagickPixel('transparent'));
//        $overlay->drawImage($draw);

        $image = new \Imagick();
        $image->readImage(plugin_dir_path(__FILE__).'../public/template.png');
        $image->setImageColorspace(Imagick::COLORSPACE_SRGB);
        $image->setImageUnits(Imagick::RESOLUTION_PIXELSPERINCH);
        $image->setResolution(600,600);

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

}
