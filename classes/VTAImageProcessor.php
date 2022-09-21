<?php

class VTAImageProcessor {

    private string $upload_dir;
    private string $upload_path;

    public function __construct() {
        $dir = wp_get_upload_dir();
        $this->upload_dir = $dir['basedir'];
        $this->upload_path = $dir['baseurl'];
    }


}
