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
        $first_name = $entry['$CHAR_SPACE'];
        $last_name = $entry['2.6'];
        $full_name = $first_name . ' ' . $last_name;
        $email = $entry[3];
        $address = $entry[5];

        // indentation for text
        $x_indentation = 98;

        // character spacing
        $CHAR_SPACE = 2.3;

        // COLOR CONTANTS
        $LIGHT_BLUE = '#4CB4E7';
        $DARK_BLUE = '#29588C';
        $DARK_GRAY = '#4C4E56';

        // SLOGAN
        $slogan_text = new \ImagickDraw();
        $slogan_text->setFont(plugin_dir_path(__FILE__) . '/MuseoSans_300_Italic.otf');
        $slogan_text->setFillColor($LIGHT_BLUE);
        $slogan_text->setStrokeColor($LIGHT_BLUE);
        $slogan_text->setStrokeWidth(2);
        $slogan_text->setFontSize(6.5);
        $slogan_text->setTextKerning($CHAR_SPACE);
        $slogan_text->annotation(1261, 435, 'Solutions that move you');

        // NAME
        $name_text = new \ImagickDraw();
        $name_text->setFont(plugin_dir_path(__FILE__) . '/MuseoSans_700.otf');
        $name_text->setFillColor($DARK_BLUE);
        $name_text->setStrokeColor($DARK_BLUE);
        $name_text->setStrokeWidth(2);
        $name_text->setFontSize(9);
        $name_text->setTextKerning($CHAR_SPACE);
        $name_text->annotation($x_indentation, 560, $full_name);

        // JOB TITLE
        $job_title_text = new \ImagickDraw();
        $job_title_text->setFont(plugin_dir_path(__FILE__) . '/MuseoSans_300.otf');
        $job_title_text->setFillColor($DARK_GRAY);
        $job_title_text->setStrokeColor($DARK_GRAY);
        $job_title_text->setStrokeWidth(2);
        $job_title_text->setFontSize(7.5);
        $job_title_text->setTextKerning($CHAR_SPACE);
        $job_title_text->annotation($x_indentation, 630, $job_title);

        // ADDRESS
        $address_text = new \ImagickDraw();
        $address_text->setFont(plugin_dir_path(__FILE__) . '/MuseoSans_300.otf');
        $address_text->setFillColor($DARK_GRAY);
        $address_text->setStrokeColor($DARK_GRAY);
        $address_text->setStrokeWidth(2);
        $address_text->setFontSize(7.5);
        $address_text->setTextKerning($CHAR_SPACE);
        $address_text->annotation($x_indentation, 835, $address);

        // EMAIL LABEL
        $email_label_text = new \ImagickDraw();
        $email_label_text->setFont(plugin_dir_path(__FILE__) . '/MuseoSans_300.otf');
        $email_label_text->setFillColor($DARK_GRAY);
        $email_label_text->setStrokeColor($DARK_GRAY);
        $email_label_text->setFontSize(7.5);
        $email_label_text->setTextKerning($CHAR_SPACE);
        $email_label_text->annotation($x_indentation, 920, 'Email');

        // EMAIL
        $email_text = new \ImagickDraw();
        $email_text->setFont(plugin_dir_path(__FILE__) . '/MuseoSans_300.otf');
        $email_text->setFillColor($LIGHT_BLUE);
        $email_text->setStrokeColor($LIGHT_BLUE);
        $email_text->setStrokeWidth(2);
        $email_text->setFontSize(7.5);
        $email_text->setTextKerning($CHAR_SPACE);
        $email_text->annotation($x_indentation + 200, 920, $email);

        // PHONE
        // @TODO - add phone + cell phone field

        $image = new \Imagick();
        $image->readImage(plugin_dir_path(__FILE__).'../public/template.png');
        $image->setImageColorspace(Imagick::COLORSPACE_SRGB);
        $image->setImageUnits(Imagick::RESOLUTION_PIXELSPERINCH);
        $image->setResolution(600,600);

        $image->setImageFormat('png');

        $image->drawImage($slogan_text);
        $image->drawImage($name_text);
        $image->drawImage($job_title_text);
        $image->drawImage($address_text);
        $image->drawImage($email_label_text);
        $image->drawImage($email_text);

        $image->setFilename('newimage');
        $image->writeImage(plugin_dir_path(__FILE__).'../public/newimage.png');

        return $image->getFilename();
    }

}
