<?php
/**
 * Provide a admin area view for the Mass Mailer Specific settings
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://jamespham.io
 * @since      2.0.0
 *
 * @package    Wp_Print_Print
 * @subpackage Wp_Print_Print/admin/partials
 * @updated
 */
?>
<h1 class="wpp-mm-heading">Mass Mailer Settings</h1>

<form id="wpp-template-form" enctype="multipart/form-data" method="POST" onsubmit="uploadTemplate(event)">

    <fieldset>
        <legend>Admin Form Set Up</legend>

        <div>
            <label for="wpp-mm-template-name">Template Name</label>
            <input type="text" id="wpp-mm-template-name" name="wpp_mm_template_name">
        </div>

        <div>
            <label for="wpp-mm-template-upload">Template Upload</label>
            <input type="file" id="wpp-mm-template-upload" name="wpp_mm_template_upload">
        </div>

        <div>
            <select name="wpp_mm_template_type" id="wpp-mm-template-type">
                <option value="bmp_lg">Bulk Mailer Permit #589 - 8.5" x 11"</option>
                <option value="bmp_sm">Bulk Mailer Permit #589 - 5.66" x 11"</option>
                <option value="em_10_std">Employee Mailer - #10 Standard</option>
                <option value="em_10_priv">Employee Mailer - #10 Privacy</option>
                <option value="em_6_3_4">Employee Mailer - 6.5" x 9.5"</option>
                <option value="return_9">Return Template #9</option>
            </select>
        </div>
    </fieldset>

    <?php submit_button(); ?>

</form>
