<?php

/**
 * Contains all of the Image Magick functions specific to Document services
 *
 * @link       https://jamespham.io
 * @since      1.1.0
 *
 * @package    Wp_Print_Preview_Imagick
 * @subpackage Wp_Print_Preview/includes
 */

/**
 * Contains all of the Image Magick functions that will be used within the plugin
 *
 * These functions will be called in other core plugin classes or functions depending where and when
 * the image needs to be created.
 *
 * @package    Wp_Print_Preview_Imagick
 * @subpackage Wp_Print_Preview/includes
 * @author     James Pham <jamespham93@yahoo.com>
 */
class Wp_Print_Preview_Imagick {

    /**
     * @param $params - array (
     *      font                => string       // OTF file for font styling
     *      color               => string       // hexadecimal number
     *      stroke_width        => float        // font weight
     *      font_size           => float        // font size
     *      kerning             => float        // character spacing
     *      annotation          => array (
     *
     *          x       => int      // indentation from left of the canvas
     *          y       => int      // distance from top of the canvas
     *          text    => string   // text content to be drawn (newlines inluded)
     *
     *      )
     *      line_height         => float        // (OPTIONAL) spacing between newlines
     *      word_spacing        => float        // (OPTIONAL) spacing between individual words
     * )
     * @return ImagickDraw
     */
    public function draw_text( $params )
    {
        $draw = new ImagickDraw();

        $draw->setFont( $params['font'] );
        $draw->setFillColor( $params['color'] );
        $draw->setStrokeColor( $params['color'] );
        $draw->setStrokeWidth( $params['stroke_width'] );
        $draw->setFontSize( $params['font_size'] );
        $draw->setTextKerning( $params['kerning'] );

        // positioning + text
        $x = $params['annotation']['x'];
        $y = $params['annotation']['y'];
        $text = $params['annotation']['text'];

        $draw->annotation( $x, $y, $text );

        // Check if "line_height" prop exists. For multiline height text (i.e. textarea values)
        if ( array_key_exists( 'line_height', $params ) ) {
            $draw->setTextInterLineSpacing( $params['line_height'] );
        }

        // Check if "word_spacing" prop exists
        if ( array_key_exists( 'word_spacing', $params ) ) {
            $draw->setTextInterWordSpacing( $params['word_spacing'] );
        }

        return $draw;
    }

    /**
     * Upload new image file to wp-content/uploads Will create a subfolder if it current does not
     * exists.
     *
     * @see - https://artisansweb.net/upload-files-programmatically-wordpress/
     * @param $image - Imagick object
     * @param $uploads_subdir - existing folder OR new folder inside wp-content/uploads
     * @param $filename - filename
     * @return string - path of the newly generated image file
     */
    public function write_to_uploads( $image, $uploads_subdir, $filename )
    {
        $upload_dir = wp_upload_dir();

        // Check if base directory exists for uploads/
        if ( !empty( $upload_dir['basedir'] ) ) {

            $subdir = $upload_dir['basedir'] . '/' . $uploads_subdir;

            //  create a new directory for business cards if it does not exist
            if ( !file_exists( $subdir ) ) {
                wp_mkdir_p( $subdir );
            }

            /**
             * Write the new file to wp-content/uploads via Image Magick
             *
             * For Ubuntu servers, please change uploads folder's group ownership
             * @see - https://stackoverflow.com/questions/15716428/cannot-save-thumbnail-with-imagick
             */
            $filepath = $subdir . '/' . $filename;
            $image->writeImage( $subdir . '/' . $filename );
            return $filepath;
        }
    }

}
