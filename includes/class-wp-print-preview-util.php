<?php

require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;

class Wp_Print_Preview_Util {
    public $excel_parser;

    public function __construct()
    {

    }
    /**
     * @param $file_name
     * @return Excel_Helper
     * @throws Exception
     */
    public function create_excel_parser($file_name)
    {
        try {
            $this->excel_parser = new Excel_Helper($file_name);
        } catch(Exception $error) {
            throw new Exception("Error constructing Excel Parser: " . $error->getMessage() . ", Line number: " . $error->getLine());
        }

       return $this->excel_parser;
    }
}

/**
 * Class Excel_Helper
 * @param $file_name string
 * @param $file_type string
 */
class Excel_Helper {
    private $file_name;
    private $spreadsheet;
    private $reader;
    private $spreadsheet_object;

    /**
     * Excel_Helper constructor.
     * @param $file_name string
     * @throws Exception
     */
    public function __construct($file_name = '/assets/6_column_example.xls')
    {
        /**
         * Set the global file name on construct.
         */
        $this->file_name = $file_name;
        /**
         * Make sure the filename exists and is has at least 3 characters for file extensions.
         */
        if (isset($this->file_name) && strlen($this->file_name) > 3) {
            try {
                /**
                 * Create a generic reader that determines the filetype automatically using extensions/patterns.
                 */
                $this->reader = PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($this->file_name);
                /**
                 * Make sure the reader exists and is a valid object.
                 */
                if (isset($this->reader) &&  gettype($this->reader) === "object") {
                    /**
                     * Set the read to read only to prevent unwanted writing to the original file.
                     */
                    $this->reader->setReadDataOnly(true);
                } else {
                    throw new Exception("Error with the reader.");
                }
            } catch (Exception $error) {
                throw new Exception("Error creating reader: " . $error->getMessage() . ", Line number: " . $error->getLine());
            }
        }
    }
    /**
     * This parses the Excel spreadsheet and returns either a JSON or PHP (default) Array.
     * @param string $return_type | "PHP" | "JSON" - whether to return PHP or JSON (PHP Object vs JSON Object)
     * @return false|string
     * @throws Exception
     */
    public function parse_excel($return_type = "PHP") {
        /**
         * Make sure the file name was included and that the reader is a valid object.
         */
        if (isset($this->file_name) && isset($this->reader) && gettype($this->reader) === 'object') {
            try {
                /**
                 * Get the first sheet withing the Worksheet.
                 */
                $this->spreadsheet = $this->reader->load($this->file_name)->getSheet(0);
            } catch (Exception $error) {
                throw new Exception("Error loading the sheet at index 0 (first sheet) \n Error Message: "
                    . $error->getMessage()
                    . "\n Line number: "
                    . $error->getLine());
            }
            try {
                /**
                 * Return a PHP object of rows and columns
                 * row (col, col, col, col)
                 */
                foreach ($this->spreadsheet->getRowIterator() as $row) {
                    /**
                     * Store each row in the iteration to the global spreadsheet object.
                     */
                    $this->store_row($row);
                }
                /**
                 * Return either a PHP or JSON object based on the parameter value passed.
                 */
                if ($return_type == 'PHP') {
                    return  $this->spreadsheet_object;
                } else if ($return_type == 'JSON') {
                    return json_encode($this->spreadsheet_object);
                }
            } catch (Exception $error) {
                throw new Exception("Error parsing and storing data using the Row/Cell Iterators \n Error Message: "
                    . $error->getMessage()
                    . "\n Line number: "
                    . $error->getLine());
            }

        }
    }
    public function store_row(\PhpOffice\PhpSpreadsheet\Worksheet\Row $row) {
        /**
         * Initialize the temporary row for storing each cell via the Iterator.
         */
        $temp_row = array();
        /**
         * Initialize the cell iterator
         */
        $cell_iterator = $row->getCellIterator();
        /**
         * Used to track the total number of columns per row
         */
        $col_count = 0;
        /**
         * Determine the total number of columns per row.
         */
        foreach ($cell_iterator as $cell) {
            $col_count++;
        }
        /**
         * Loop and store each cell/column per row.
         */
        foreach ($cell_iterator as $cell) {
            /**
             * Create the column key based on the definitions and number of columns per row
             */
            $column = $this->column_definitions[$col_count][$cell_iterator->getCurrentColumnIndex()];
            /**
             * Get the value of the cell.
             */
            $value = $cell->getValue();
            /**
             * Store the column name with the cell value in the temp row
             */
            $temp_row[$column] = $value;
        }
        /**
         * Store the complete row within the official spreadsheet object.
         */
        $this->spreadsheet_object[$row->getRowIndex()] = $temp_row;
    }

    public function convert_to_php_obj($row_column_count) {

    }

    /**
     * @var array
     * The parent is the number of columns total in the worksheet, ex: 5 is the mapping for a worksheet with 5 columns
     * 1 - 5 is the row cells that relate to the column.
     */
    private $column_definitions = array(
        "5" => array(
            "1" => "name",
            "2" => "address",
            "3" => "city",
            "4" => "state",
            "5" => "zip",
        ),
        "6" => array(
            "1" => "first",
            "2" => "last",
            "3" => "address",
            "4" => "city",
            "5" => "state",
            "6" => "zip",
        ),
    );
}