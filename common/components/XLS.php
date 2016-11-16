<?php
namespace common\components;

class XLS
{
    private $_filename = "";

    public function __construct($filename = "")
    {
        if (!empty($filename)) {
            $this->_filename = mb_convert_encoding($filename, 'GBK', 'UTF-8'); // iconv('UTF-8', 'GBK//IGNORE', $filename);
        } else {
            $this->_filename = time();
        }

        header('Content-Type: text/html; charset=utf-8');
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: attachment;filename={$this->_filename}.xls;filename*={$this->_filename}.xls");
        header("Content-Transfer-Encoding: binary ");
    }

    public function getFilename()
    {
        return $this->_filename;
    }

    public function xlsBOF()
    {
        echo pack("ssssss", 0x809, 0x8, 0x0, 0x10, 0x0, 0x0);
    }

    public function xlsEOF()
    {
        echo pack("ss", 0x0A, 0x00);
        exit();
    }

    public static function format($str)
    {
        $str = empty($str) ? "" : $str;

        $str = str_replace("\"", "", $str);
        if (strpos($str, ",")) {
            $str = "\"" . $str . "\"";
        }
        $str = mb_convert_encoding($str, 'GBK', "UTF-8"); // iconv("UTF-8", "GBK//IGNORE", $str);
        return $str;
    }

    function xlsWriteNumber($row, $col, $Value)
    {
        echo pack("sssss", 0x203, 14, $row, $col, 0x0);
        echo pack("d", $Value);
    }

    function xlsWriteLabel($row, $col, $Value)
    {
        $Value = empty($Value) ? "" : $Value;
        $L = strlen($Value);
        echo pack("ssssss", 0x204, 8 + $L, $row, $col, 0x0, $L);
        echo $Value;
    }

    function writeExcelLine($row, $col, $val)
    {
        if (is_numeric($val)) {
            $this->xlsWriteNumber($row, $col, $val);
        } else {
            $this->xlsWriteLabel($row, $col, XLS::format($val));
        }
    }
}