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

<form enctype="multipart/form-data" method="POST" onsubmit="saveMMconfigForm(event)">

    <fieldset>
        <legend>Admin Form Set Up</legend>

        <p class="wpp-mm-gf-instructions">
            To link a Gravity Forms form, please select from the drop-down below. The pre-configured gravity forms will
            be used to upload Mass Mailer templates & uploads.
        </p>

        <label for="wpp-mm-gf-id" class="wpp-mm-gf-label">
            Link to Existing Gravity Forms
        </label>
        <select name="wpp_mm_gf_id" id="wpp-mm-gf-id">
            <option value="" disabled <?php if (!get_option('wpp_mm_gf_id')) echo 'selected'; ?>>Please select a form</option>

            <?php foreach ( GFAPI::get_forms() as $form ) : ?>

                <option value="<?php echo $form['id']; ?>" <?php if ( get_option('wpp_mm_gf_id') == $form['id'] ) echo 'selected'; ?>>
                    <?php printf('%s (Form ID: %s)', $form['title'], $form['id']); ?>
                </option>

            <?php endforeach; ?>
        </select>
    </fieldset>

    <?php submit_button(); ?>

</form>

<?php
    $gf_id_setting = get_option('wpp_mm_gf_id');

    // Check setting exists
    if ( $gf_id_setting ) {
        // render gravity forms shortcode to frontend
        echo do_shortcode( sprintf('[gravityform id="%d" ajax="true"]', $gf_id_setting ) );

        // retrieve entries
        $entries = GFAPI::get_entries( $gf_id_setting );

        // if entries exist, render in table
        error_log( json_encode( $entries, JSON_PRETTY_PRINT ) );

        // if entries exist, display in a table
        if ( ! empty( $entries ) ) : ?>

            <table>
                <thead>
                    <tr>
                        <th>
                            Entry ID
                        </th>
                        <th>
                            Template Name (Option Name)
                        </th>
                        <th>
                            Envelope Type
                        </th>
                        <th>
                            Template File
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php


                        foreach( $entries as $entry ) {
                    ?>

                        <tr>
                            <td>
                                <?php echo $entry['']; ?>
                            </td>
                            <td>
                                <?php echo $entry['']; ?>
                            </td>
                            <td>
                                <?php echo $entry['']; ?>
                            </td>
                            <td>
                                <?php echo $entry['']; ?>
                            </td>
                        </tr>

                    <?php } ?>
                </tbody>
            </table>

        <?php endif;
    }
?>
