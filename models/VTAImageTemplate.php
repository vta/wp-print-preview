<?php

class VTAImageTemplate {

    private WP_Post $post;
    private string  $upload_dir;
    private string  $upload_url;

    /**
     * @param WP_Post|int $post
     * @throws Exception
     */
    public function __construct( $post ) {

        if ( is_int($post) ) {
            $wp_post = get_post($post);
            if ( !$wp_post ) {
                throw new Exception("VTAImageTemplate construction error - No post found for Post ID #$post");
            }
            $this->post = $wp_post;

        } elseif ( $post instanceof WP_Post ) {
            $this->post = $post;
        } else {
            throw new Exception("VTAImageTemplate construction error - Post not int or instance of WP_Post: $post");
        }

        $wp_upload_dir = wp_upload_dir();
        $this->upload_dir = $wp_upload_dir['basedir'];
        $this->upload_url = $wp_upload_dir['baseurl'];
    }

    // SETTERS //

    /**
     * Sets image path relative to upload directory
     * @param string $image_path
     * @return void
     */
    public function set_image_path(string $image_path): void {
        update_post_meta($this->get_post_id(), VTA_IMAGE_PATH_META, $image_path);
    }

    /**
     * Sets annotations array
     * @param array $annotations
     * @return void
     */
    public function set_fields(array $annotations): void {
        update_post_meta($this->get_post_id(), VTA_IMAGE_FIELDS_META, $annotations);
    }

    /**
     * Sets image metadata
     * @param array $image_meta
     * @return void
     */
    public function set_image_meta(array $image_meta): void {
        update_post_meta($this->get_post_id(), VTA_IMAGE_META, $image_meta);
    }

    // GETTERS //

    /**
     * @return WP_Post
     */
    public function get_post(): WP_Post {
        return $this->post;
    }

    public function get_post_id(): int {
        return $this->post->ID;
    }

    /**
     * Name of image template
     * @return string
     */
    public function get_name(): string {
        return $this->post->post_title;
    }

    /**
     * Description of image template
     * @return string
     */
    public function get_description(): string {
        return $this->post->post_content;
    }

    /**
     * Returns url path of image template
     * @return string|null
     */
    public function get_image_template_url(): ?string {
        if ( $image_path = $this->get_image_path()) {
            return "{$this->upload_url}/{$image_path}";
        }
        return null;
    }

    /**
     * Returns full path of image template
     * @return string|null
     */
    public function get_image_template_path(): ?string {
        if ( $image_path = $this->get_image_path()) {
            return "{$this->upload_dir}/{$image_path}";
        }
        return null;
    }

    /**
     * Returns image template annotations metadata
     * @return array
     */
    public function get_fields(): array {
        $annotations = get_post_meta($this->get_post_id(), VTA_IMAGE_FIELDS_META, true);
        return is_array($annotations) ? $annotations : [];
    }

    /**
     * Returns image metadata (ex. height, width, type, etc.)
     * @return array
     */
    public function get_image_meta(): array {
        $img_meta = get_post_meta($this->get_post_id(), VTA_IMAGE_FIELDS_META, true);
        return is_array($img_meta) ? $img_meta : [];
    }

    // PRIVATE METHODS //

    /**
     * Returns image template path relative to upload directory
     * @return string|null
     */
    public function get_image_path(): ?string {
        $image_path = get_post_meta($this->get_post_id(), VTA_IMAGE_PATH_META, true);
        return is_string($image_path) ? $image_path : null;
    }

}
