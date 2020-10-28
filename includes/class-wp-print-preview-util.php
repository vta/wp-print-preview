<?php

require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Reader\Xls;

class Wp_Print_Preview_Util {
    public $excel_parser;
    private function excel_parser($file_name, $file_type)
    {
       $this->excel_parser = new Excel_Helper($file_name, $file_type);
       return $this->excel_parser;
    }
    private function remove_excel_parser() {
        if ($this->excel_parser) {

        }
    }
}

class Excel_Helper {
    private $file_name;
    private $file_type;
    private $reader;
    public function __construct($file_name, $file_type)
    {
        $this->file_name = $file_name;
        $this->file_type = $file_type;
        switch ($file_type) {
            case 'xls':
                $this->reader = new PhpOffice\PhpSpreadsheet\Reader\Xls();
                break;
            case 'xlsx':
                $this->reader = new PhpOffice\PhpSpreadsheet\Reader\Xlsx();
                break;
            default:
                break;
        }
    }
    public function parse_excel() {
        
    }
}