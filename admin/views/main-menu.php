<?php
/**
 * TODO - requires installation of "pdftk" in environment i.e. sudo apt-get install pdftk
 */
require_once(plugin_dir_path(__DIR__) . '../utils/php-pdftk/vendor/autoload.php');

use mikehaertl\pdftk\DataFields;
use mikehaertl\pdftk\Pdf;

$pdf_meta = new Pdf(ABSPATH . '/wp-content/plugins/wp-print-preview/admin/views/bc_template_form.pdf');

//$data = $pdf_meta->getData();
//$debug = $data->getArrayCopy();

/** @var DataFields | null $dataFields */
$dataFields = $pdf_meta->getDataFields();
$dataFieldsArr = $dataFields->getArrayCopy();

// Fill Form
$form_fields = [];
foreach ( $dataFieldsArr as $dataField ) {
    $form_fields[$dataField['FieldName']] = 'Example';
    break;
}

$pdf = new Pdf(ABSPATH . '/wp-content/plugins/wp-print-preview/admin/views/bc_template_form.pdf');

//$pdf->addFile('/var/www/ccsingle/wp-content/plugins/wp-print-preview/admin/views/bc_template_form.pdf');
//$pdf->fillForm($form_fields);

//$tmp = $pdf->getTmpFile()->saveAs('/var/www/ccsingle/wp-content/plugins/wp-print-preview/admin/views/temp.pdf');
$pdf->fillForm($form_fields)
    ->needAppearances()
    ->saveAs(ABSPATH . '/wp-content/plugins/wp-print-preview/admin/views/temp.pdf');
//$error = $pdf->getError();
//copy($tmp, '/var/www/ccsingle/wp-content/plugins/wp-print-preview/admin/views/temp.pdf');

$stop = null;

?>
<h1>Example Main Plugin Page</h1>
