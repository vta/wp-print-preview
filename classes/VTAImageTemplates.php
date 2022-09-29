<?php

class VTAImageTemplates {

    private string $plugin_name;
    private string $version;

    private string $save_img_ajax = SAVE_IMG_AJAX;
    private string $post_type     = VTA_IMAGE_TEMPLATE_CPT;

    public function __construct(
        string $plugin_name,
        string $version
    ) {
        $this->plugin_name = $plugin_name;
        $this->version     = $version;

        // Register CPT
        add_action('init', [ $this, 'register_vta_image_templates' ]);
        add_action("save_post_{$this->post_type}", [ $this, 'save_post' ], 11, 3);
        add_action("edit_post_{$this->post_type}", [ $this, 'edit_post' ], 10, 2);

        // should only run in admin dashboard
        if ( is_admin() ) {
            add_action('admin_enqueue_scripts', [ $this, 'enqueue_scripts' ]);
            add_filter('parent_file', [ $this, 'edit_image_highlight' ]);

            // Custom New/Edit Post Page
            add_action('admin_init', [ $this, 'custom_edit_post' ]);
            add_action('add_meta_boxes', [ $this, 'add_pdf_metabox' ]);
            add_action("wp_ajax_{$this->save_img_ajax}", [ $this, 'save_img' ]);
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

        // labels for VTA Image Templates (custom post)
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

        // create custom post type of "VTA Image Template"
        register_post_type(
            $this->post_type,
            [
                'labels'       => $labels,
                'public'       => false,
                'show_ui'      => true,
                'show_in_menu' => false, // "$admin_url?page=$this->settings_page&post_type=$post_type",
                'description'  => 'VTA Image Templates to be dynamically generated with Gravity Forms entries.',
                'hierarchical' => true,
                'has_archive'  => true,
                'supports'     => [ 'title', 'revisions' ]
            ]
        );
    }

    // ADMIN NAV MENU //

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
     * Add PDF meta box for file upload & PDF file information.
     * @return void
     */
    public function add_pdf_metabox(): void {
        if ( $this->is_post_page() ) {
            add_meta_box(
                'pdf-upload',
                'PDF Upload',
                [ $this, 'render_pdf_upload' ],
                null,
                'advanced',
                'high'
            );
        }
    }

    /**
     * Custom save post action for VTA Image Template CPT.
     * @param int $post_ID
     * @param WP_Post|null $post
     * @param bool|null $update
     * @return void
     */
    public function save_post( int $post_ID, ?WP_Post $post, ?bool $update ): void {
        try {
            $files = $_FILES;
        } catch ( Exception $e ) {
            error_log("VTAImageTemplates::save_post() error - $e");
        }
    }

    public function edit_post( int $post_ID, WP_Post $post ): void {

    }

    // RENDER METHODS //

    /**
     * PDF upload field. If PDF is already uploaded, it will show custom highly customized fields.
     * @return void
     */
    public function render_pdf_upload() {
        global $post;

        ?>

        <table class="pdf-upload-field">
            <tr class="pdf-upload-row">
                <td class="pdf-upload-label">
                    <label for="pdf-input">PDF Upload <span class="required">*</span></label>
                </td>
                <td class="pdf-upload-input">
                    <div class="pdf-drag-drop">
                        <input id="pdf-input" name="pdf_file" type="file" accept="application/pdf,pdf" required>
                    </div>
                </td>
            </tr>
        </table>

        <?php
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

        // edit page Post type check
        $is_post_type = false;
        if ( $post_id = $query_params['post'] ?? null ) {
            $wp_post      = get_post($post_id);
            $is_post_type = $wp_post->post_type === $this->post_type;
        }

        $is_post = in_array($this->post_type, $query_params) || $is_post_type;
        return preg_match("/.*(post-new|post).*/", $path) && $is_post;
    }

    private function is_list_table_page(): void {

    }
}
