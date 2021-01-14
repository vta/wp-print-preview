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

<form enctype="multipart/form-data" method="POST" onsubmit="saveReturnEnvelopeTemplate(event)">

    <?php
    foreach ( GFAPI::get_forms() as $form ) {
        error_log( json_encode( $form, JSON_PRETTY_PRINT ) );
    }
    ?>

    <fieldset>
        <legend>Admin Form Set Up</legend>

        <p for="wpp-gf-">
            To link a Gravity Forms form, please select from the below template
        </p>

        <label for="wpp-mm-gf-id-label">
            Link to Existing Gravity Forms
        </label>
        <select name="wpp_mm_gf_id" id="wpp-mm-gf-id">
            <?php foreach ( GFAPI::get_forms() as $form ) : ?>

                <option value="<?php echo $form['id'] ?>">
                    <?php printf('%s (Form ID: %s)', $form['title'], $form['id']) ?>
                </option>

            <?php endforeach; ?>
        </select>
    </fieldset>

    <?php submit_button(); ?>

</form>

<?php echo do_shortcode('[gravityform id="9" ajax="true"]') ?>
