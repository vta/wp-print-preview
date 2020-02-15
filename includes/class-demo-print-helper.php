<?php
Class BusinessCardHelper
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
        $current_user_id = intval(wp_get_current_user()->data->ID);

        if (is_wp_error($entry['created_by'])) {
            // if the following exists
            return "<h1>BIG ERROR</h1>";
            exit();
        } else {
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
}
