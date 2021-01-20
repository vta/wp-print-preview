<?php
/**
 * Provide a admin area view for the Mass Mailer Specific settings
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * NOTE: Experimental ReactJS is used for frontend development for this page. We can always revert, but this is for agile
 * development proof-of-concept.
 *
 * @link       https://jamespham.io
 * @since      2.0.0
 * @package    Wp_Print_Print
 * @subpackage Wp_Print_Print/admin/partials
 * @updated 1/20/2021
 */
?>
<h1 class="wpp-mm-heading">Mass Mailer Settings</h1>

<!--TODO - Create a tab, accordion, or separate into an action btn/link to this form -->
<!--TODO - Create AJAX loading animation to inform user of loading process -->
<form id="wpp-template-form" enctype="multipart/form-data" method="POST" onsubmit="uploadTemplate(event)">

    <fieldset>
        <legend>Mass Mailer Template Upload Form</legend>

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

<div id="mm-template-table"></div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/babel-standalone/6.26.0/babel.min.js" integrity="sha512-kp7YHLxuJDJcOzStgd6vtpxr4ZU9kjn77e6dBsivSz+pUuAuMlE2UTdKB7jjsWT84qbS8kdCWHPETnP/ctrFsA==" crossorigin="anonymous"></script>
<script crossorigin src="https://unpkg.com/react@17/umd/react.production.min.js"></script>
<script crossorigin src="https://unpkg.com/react-dom@17/umd/react-dom.production.min.js"></script>

<!-- Experimental React Integration -->
<script type="text/babel">

    /**
     * Array directly from DB
     */
    const massMailerRecords = <?php echo json_encode(
        get_posts(
            array(
                'post_type' => 'wpp_mm_template',
                'post_status' => array( 'draft ')
            )
        )
    ); ?>

    /**
     * MassMailerTable
     *
     * Renders main Mass Mailer table
     * @return {JSX.Element}
     * @constructor
     */
    const MassMailerTable = () => {

        // METHODS
        /**
         * Deletes custom post w/ ID
         */
        const deleteRow = async (postId) => {

            const deleleteConfirm = confirm('Are you sure you want to delete this template?');

            if (deleleteConfirm) {
                const payload = JSON.stringify({ postId });
                const res = await fetch(`<?php echo admin_url( 'admin-ajax.php' ) ?>`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: payload
                });
            }

        }

        // RENDERS
        /**
         * Table rows for each uploaded Mass Mailer template
         */
        const mmTableRows = massMailerRecords.map(({ post_content, ID }, i) => {

            // @todo - Maybe redo the way records are stored
            // all records are stored in JSON format. Extract via destructure
            const { wpp_mm_template_name, wpp_mm_template_type, wpp_mm_template_file } = JSON.parse(post_content);

            // extract file object
            const { filename, filepath } = wpp_mm_template_file;

            return(
                <tr key={i}>
                    <td>{ wpp_mm_template_name }</td>
                    <td>{ wpp_mm_template_type }</td>
                    <td><a href={ filepath } download>{ filename }</a></td>
                    <td>
                        <button onClick={() => deleteRow(ID)}>Delete</button>
                    </td>
                </tr>
            );
        });

        return(
            <div>
                {
                    massMailerRecords.length
                    ?
                        <table>
                            <thead>
                                <tr>
                                    <th>
                                        Template Name
                                    </th>
                                    <th>
                                        Template Category
                                    </th>
                                    <th>
                                        File
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                            { mmTableRows }
                            </tbody>
                        </table>
                    :
                        <h2>No templates saved yet.</h2>
                }
            </div>
        );
    }

    ReactDOM.render(
        <MassMailerTable />,
        document.getElementById('mm-template-table')
    );

</script>
