<?php

use mikehaertl\pdftk\InfoFields;
use mikehaertl\pdftk\DataFields;
use mikehaertl\pdftk\Pdf;

/**
 * @class PhpPdftK
 * @description Wrapper class that encapsulates PHP PDFTK library
 * NOTE: certain execution of PDF class commands requires a new instance of the PDF object. Chaining methods will fail...
 */
class PhpPdftk {

    const FIELD_NAME_KEY = 'FieldName';
    const PDF_TYPE       = 'application/pdf';

    private string $pdf_file;

    /**
     * @param string $pdf_file Full path of PDF file
     * @throws Exception
     */
    public function __construct( string $pdf_file ) {
        // exceptions...
        if ( !file_exists($pdf_file) ) {
            throw new Exception("PhpPdftK::__construct() error - \"$pdf_file\" does not exist.");
        }
        if ( mime_content_type($pdf_file) !== self::PDF_TYPE ) {
            throw new Exception("PhpPdftK::__construct() error - \"$pdf_file\" is not a PDF file.");
        }

        $this->pdf_file = $pdf_file;
    }

    /**
     * Returns data fields of PDF form. Used to write text to form.
     * @return array
     */
    public function get_data_fields(): array {
        try {
            $pdf = new Pdf($this->pdf_file);
            return $pdf->getDataFields()->getArrayCopy();

        } catch ( Exception $e ) {
            error_log("PhpPdftK::get_data_fields() error - $e");
            return [];
        }
    }

    /**
     * Fills PDF form fields & saves to a new PDF file.
     * @param array $form_fields should be in in FieldName => TextValue format
     * @param string $new_file_path Absolute filepath
     * @return bool
     */
    public function fill_form_fields( array $form_fields, string $new_file_path ): bool {
        try {
            $pdf = new Pdf($this->pdf_file);
            return $pdf->fillForm($form_fields)
                ->needAppearances()
                ->saveAs($new_file_path);

        } catch ( Exception $e ) {
            error_log("PhpPdftK::fill_form_fields() error - $e");
            return false;
        }
    }

    /**
     * Saves PDF upload
     * @param string $new_file_path
     * @return bool
     */
    public function save_pdf( string $new_file_path ): bool {
        try {
            $pdf = new Pdf($this->pdf_file);
            return $pdf->needAppearances()
                ->saveAs($new_file_path);

        } catch ( Exception $e ) {
            error_log("PhpPdftK::save_pdf() error - $e");
            return false;
        }
    }

}
