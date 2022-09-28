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

        // Register CPT
        add_action('init', [ $this, 'register_vta_image_templates' ]);

        // should only run in admin dashboard
        if ( is_admin() ) {
            add_action("wp_ajax_{$this->save_img_ajax}", [ $this, 'save_img' ]);
            add_action('admin_enqueue_scripts', [ $this, 'enqueue_scripts' ]);
            add_filter('parent_file', [ $this, 'edit_image_highlight' ]);

            // Custom Post Page / List Table
            add_action('admin_init', [ $this, 'custom_edit_post' ]);
        }
    }

    /**
     * Enqueues script for appropriate pages.
     * @return void
     */
    public function enqueue_scripts(): void {
        // New/Edit post page
        if ( $this->is_post_page() ) {
            wp_enqueue_style(
                "{$this->plugin_name}_post_css",
                plugin_dir_url(__DIR__) . '/admin/css/post.css',
                [],
                $this->version
            );
            wp_enqueue_script(
                "{$this->plugin_name}_post_js",
                plugin_dir_url(__DIR__) . '/admin/js/post.js',
                [ 'jquery' ],
                $this->version,
                true
            );
        }
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

    // NEW/EDIT POST //

    /**
     * Customizes New/Edit post page.
     * @return void
     */
    public function custom_edit_post() {
        if ( $this->is_post_page() ) {
            $this->replace_title_placeholder();
            $this->default_content();
        }
    }

    /**
     * "save_vta_img" POST Ajax handler. Saves image information & stores image file as PNG
     * @return void
     */
    public function save_img(): void {
        error_log('saving img...');
    }

    // PRIVATE METHODS //

    /**
     * Replaces "Add Title" with "Image Template Name"
     * @return void
     */
    private function replace_title_placeholder(): void {
        add_filter('enter_title_here', fn() => 'Image Template Name');
    }

    /**
     * Inserts default description if there is none
     * @return void
     */
    private function default_content(): void {
        add_filter('default_content', function ( $content ) {
            return !empty($content) ? $content : 'Image Template description here...';
        });
    }

    /**
     * Determines if current page is New/Edit page for current post
     * @return bool
     */
    private function is_post_page(): bool {
        [ 'path' => $path, 'query_params' => $query_params ] = get_query_params();
        return preg_match("/(post-new|post)/", $path,) && in_array($this->post_type, $query_params);
    }

    private function is_list_table_page(): void {

    }
}
