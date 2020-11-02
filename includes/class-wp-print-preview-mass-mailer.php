<?php

class Wp_Print_Preview_Mass_Mailer {

    private $entry;
    private $form;

    /**
     * Wp_Print_Preview_Mass_Mailer constructor.
     * @param $entry_id - Gravity Forms entry ID
     */
    function __construct( $entry_id )
    {
        $this->entry = GFAPI::get_entry( $entry_id );
        error_log(json_encode($this->entry, JSON_PRETTY_PRINT));
    }
    
}