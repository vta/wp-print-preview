<?php

class VTAImageTemplates {

    private VTAImageProcessor $image_processor;

    private string $plugin_name;
    private string $version;

    private string $save_img_ajax   = SAVE_IMG_AJAX;
    private string $file_robot_js   = 'filerobot-image-editor-js';
    private string $file_editor_js  = 'file-editor-js';
    private string $file_editor_obj = 'fileEditorObj';

    private string $post_type = VTA_IMAGE_TEMPLATE_CPT;

    public function __construct(
        string $plugin_name,
        string $version
    ) {
        $this->plugin_name = $plugin_name;
        $this->version     = $version;

        $this->image_processor = new VTAImageProcessor();

        if ( is_admin() ) {
            add_action("wp_ajax_{$this->save_img_ajax}", [ $this, 'save_img' ]);
            add_action('admin_enqueue_scripts', [ $this, 'enqueue_scripts' ]);
        }

        // Register CPT
        add_action('init', [ $this, 'register_vta_image_templates' ]);
        add_filter('parent_file', [ $this, 'edit_image_highlight' ]);
    }

    public function enqueue_scripts(): void {
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
                'action'  => $this->save_img_ajax
            ]
        );
    }

    // VTA IMAGE TEMPLATE CPT //

    /**
     * Register VTAImageTemplate Custom Post Type.
     * Also adds CPT submenu pages under plugin menu.
     * @return void
     */
    public function register_vta_image_templates(): void {
        $admin_url = admin_url();

        // labels for VTA Holiday (custom post)
        $labels = [
            'name'               => 'Image Templates',
            'singular_name'      => 'Image Template',
            'add_new'            => 'New Image Template',
            'add_new_item'       => 'Add Image Template',
            'edit_item'          => 'Edit Image Template',
            'new_item'           => 'New Image Template',
            'view_item'          => 'View Image Template',
            'search_items'       => 'Search Image Templates',
            'not_found'          => 'Image Templates Found',
            'not_found_in_trash' => 'Image Templates found in Trash'
        ];

        // create custom post type of "VTA Holiday"
        register_post_type(
            $this->post_type,
            [
                'labels'       => $labels,
                'public'       => false,
                'show_ui'      => true,
                'show_in_menu' => false, // "$admin_url?page=$this->settings_page&post_type=$post_type",
                'description'  => 'VTA Image Templates to be dynamically printed on the front-end.',
                'hierarchical' => true,
                'has_archive'  => true,
            ]
        );

        // add as a sub-page menu under plugins menu
        add_submenu_page(
            WP_PRINT_SETTINGS_PAGE,
            'VTA Image Templates',
            'VTA Images',
            'manage_options',
            "edit.php?post_type={$this->post_type}",
            false
        );

        // new VTA Image Template page
        add_submenu_page(
            WP_PRINT_SETTINGS_PAGE,
            'New VTA Holiday',
            'New VTA Holiday',
            'manage_options',
            "post-new.php?post_type={$this->post_type}",
            false
        );
    }

    /**
     * Highlights custom nested CPT submenus
     * @param string $file
     * @return string
     */
    public function edit_image_highlight( string $file ): string {
        global $plugin_page;

        $post_type = POST_TYPE;
        if ( preg_match("/$post_type/", $file) ) {
            $plugin_page = WP_PRINT_SETTINGS_PAGE;
        }

        return $file;
    }

    /**
     * "save_vta_img" POST Ajax handler. Saves image information & stores image file as PNG
     * @return void
     */
    public function save_img(): void {
        error_log('saving img...');
    }
}
