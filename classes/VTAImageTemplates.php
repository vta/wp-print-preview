<?php

class VTAImageTemplates {

    private string $plugin_name;
    private string $version;

    private string $save_img_ajax  = SAVE_IMG_AJAX;
    private string $file_robot_js  = 'filerobot-image-editor-js';
    private string $file_editor_js = 'file-editor-js';
    private string $file_editor_obj = 'fileEditorObj';

    public function __construct(
        string $plugin_name,
        string $version
    ) {
        $this->plugin_name = $plugin_name;
        $this->version     = $version;

        if ( is_admin() ) {
            add_action("wp_ajax_{$this->save_img_ajax}", [ $this, 'save_img' ]);
            add_action('admin_enqueue_scripts', [ $this, 'enqueue_scripts' ]);
        }
    }

    public function enqueue_scripts() {
        wp_enqueue_script(
            $this->file_robot_js,
            FILEROBOT_JS_CDN,
            [],
            '4.3.7'
        );

        wp_enqueue_script(
            $this->file_editor_js,
            plugin_dir_url(__DIR__) . '/admin/js/image-editor.js',
            [ $this->file_robot_js ],
            $this->version,
            true
        );

        wp_localize_script(
            $this->file_editor_js,
            $this->file_editor_obj,
            [
                'ajaxUrl' => admin_url('admin-ajax.php'),
                'action' => $this->save_img_ajax
            ]
        );
    }

    public function save_img() {
        error_log('saving img...');
    }
}
