<?php
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
            $first_name = $entry['2.3'];
            $last_name = $entry['2.6'];
            $email = $entry[3];
            $address = $entry[5];

            wp_redirect('/business-card-edit/?entry_id=' . $entry_id);
        }
    }
}
