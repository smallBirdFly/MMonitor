<?php
namespace common\components;

use PHPExcel_Cell_DataType;

require (__DIR__ . '/../../common/lib/PHPExcel-1.8/Classes/PHPExcel.php');
require (__DIR__ . '/../../common/lib/PHPExcel-1.8/Classes/PHPExcel/Writer/Excel5.php');

class PHPExcelExport
{
    private $_filename = "";
    private $_excel = null;
    private $_writer = null;

    public function __construct($filename = "")
    {
        if (!empty($filename))
        {
            $this->_filename = mb_convert_encoding($filename, 'GBK', 'UTF-8');
        }
        else
        {
            $this->_filename = time();
        }

        $this->_excel = new \PHPExcel();
        $this->_excel->getProperties()->setCreated();
        $this->_excel->getProperties()->setModified();
    }

    public function getExcel()
    {
        return $this->_excel;
    }

    /**
     * 保存数据
     * @param $path
     * @throws \PHPExcel_Writer_Exception
     */
    public function save($path)
    {
        $this->_writer = new \PHPExcel_Writer_Excel5($this->_excel);
        $this->_writer->save($path);
    }

    /**
     * 下载数据
     * @throws \PHPExcel_Writer_Exception
     */
    public function download()
    {
        ob_end_clean();

        header('Content-Type: text/html; charset=utf-8');
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: attachment;filename={$this->_filename}.xls;filename*={$this->_filename}.xls");
        header("Content-Transfer-Encoding: binary");

        $this->_writer = new \PHPExcel_Writer_Excel5($this->_excel);
        $this->_writer->save("php://output");
        exit();
    }

    /**
     * 获取当前sheet数量
     * @return int
     */
    public function getSheetCount()
    {
        return $this->_excel->getSheetCount();
    }

    /**
     * 创建新的sheet
     * @param null $iSheetIndex
     * @return int|null
     * @throws \PHPExcel_Exception
     */
    public function createSheet($iSheetIndex=null)
    {
        $sheet = $this->_excel->createSheet($iSheetIndex);
        if (isset($iSheetIndex))
        {
            return $iSheetIndex;
        }
        else
        {
            return $this->_excel->getIndex($sheet);
        }
    }

    /**
     * 设置当前sheet
     * @param $index
     * @throws \PHPExcel_Exception
     */
    public function setActiveSheet($index)
    {
        $this->_excel->setActiveSheetIndex($index);
    }

    /**
     * 通过名称设置当前sheet
     * @param $name
     * @throws \PHPExcel_Exception
     */
    public function setActiveSheetByName($name)
    {
        $this->_excel->setActiveSheetIndexByName($name);
    }

    /**
     * 获取当前sheet
     * @return \PHPExcel_Worksheet
     */
    public function getActiveSheet()
    {
        return $this->_excel->getActiveSheet();
    }

    /**
     * 设置单元格的值
     * @param $cell
     * @param $value
     * @param bool $returnCell
     * @return \PHPExcel_Cell|\PHPExcel_Worksheet
     */
    public function setCellValue($cell, $value, $returnCell=false)
    {
        return $this->_excel->getActiveSheet()->setCellValue($cell, $value, $returnCell);
    }

    /**
     * 设置单元格的值, 并指定类型
     * @param $cell
     * @param $value
     * @param string $dataType
     * @param bool $returnCell
     * @return \PHPExcel_Cell|\PHPExcel_Worksheet
     */
    public function setCellValueExplicit($cell, $value, $dataType=PHPExcel_Cell_DataType::TYPE_STRING, $returnCell=false)
    {
        return $this->_excel->getActiveSheet()->setCellValueExplicit($cell, $value, $dataType, $returnCell);
    }

    /**
     * 合并单元格
     * @param $region
     * @throws \PHPExcel_Exception
     */
    public function mergeCells($region)
    {
        $this->_excel->getActiveSheet()->mergeCells($region);
    }

    /**
     * 分离单元格
     * @param $region
     * @throws \PHPExcel_Exception
     */
    public function unmergeCells($region)
    {
        $this->_excel->getActiveSheet()->unmergeCells($region);
    }

    /**
     * 获取单元格格式
     * @param $cell
     * @return \PHPExcel_Style
     */
    public function getStyle($cell)
    {
        return $this->_excel->getActiveSheet()->getStyle($cell);
    }

    /**
     * 获取列的尺寸相关
     * @param $column
     * @param bool $create
     * @return \PHPExcel_Worksheet_ColumnDimension
     */
    public function getColumnDimension($column, $create = true)
    {
        return $this->_excel->getActiveSheet()->getColumnDimension($column, $create);
    }

    /**
     * 设置属性
     * @param $properties
     */
    public function setProperties($properties)
    {
        if (array_key_exists('creator', $properties))
        {
            $this->_excel->getProperties()->setCreator($properties['creator']);
        }
        if (array_key_exists('lastModifiedBy', $properties))
        {
            $this->_excel->getProperties()->setLastModifiedBy($properties['lastModifiedBy']);
        }
        if (array_key_exists('title', $properties))
        {
            $this->_excel->getProperties()->setTitle($properties['title']);
        }
        if (array_key_exists('subject', $properties))
        {
            $this->_excel->getProperties()->setSubject($properties['subject']);
        }
        if (array_key_exists('description', $properties))
        {
            $this->_excel->getProperties()->setDescription($properties['description']);
        }
        if (array_key_exists('keywords', $properties))
        {
            $this->_excel->getProperties()->setKeywords($properties['keywords']);
        }
        if (array_key_exists('category', $properties))
        {
            $this->_excel->getProperties()->setCategory($properties['category']);
        }
        if (array_key_exists('company', $properties))
        {
            $this->_excel->getProperties()->setCompany($properties['company']);
        }
        if (array_key_exists('manager', $properties))
        {
            $this->_excel->getProperties()->setManager($properties['manager']);
        }
    }


}