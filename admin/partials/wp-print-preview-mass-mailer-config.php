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
 */
?>
<h1 class="wpp-mm-heading">Mass Mailer Settings</h1>

<?php echo do_shortcode('[gravityform id="9" ajax="true"]') ?>

<!--<form enctype="multipart/form-data" method="POST" onsubmit="saveReturnEnvelopeTemplate(event)">-->
<!---->
        <!--  @todo - only image/* file types   -->
<!--    <div>-->
<!--        <label for="wpp-return-env-upload">-->
<!--            #9 Return Envelope Template Name-->
<!--        </label>-->
<!--        <input type="text" name="return_env_template_name">-->
<!--    </div>-->
<!--    <div>-->
<!--        <label for="wpp-return-env-upload">-->
<!--            #9 Return Envelope Template File Upload-->
<!--        </label>-->
<!--        <input type="file" id="wpp-return-env-upload" name="return_env_upload_file">-->
<!--    </div>-->
<!---->
<!--    --><?php //submit_button(); ?>
<!---->
<!--</form>-->
