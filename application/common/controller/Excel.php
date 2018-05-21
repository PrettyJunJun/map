<?php

/**
 *
 * 消息通知模型类
 *
 */

namespace app\common\controller;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


class Excel
{
    public static function getExcelToArr($input_name)
    {

        //>>获取表单上传文件
        $file = request()->file($input_name);
        //>>移动到框架应用根目录/uploads/ 目录下
        $dir_path = ROOT_PATH . 'uploads/tmp/';
        $info = $file->move($dir_path);
        if (!$info) {
            //>>上传失败获取错误信息
            return $file->getError();
//            return $file_path;
        }
        //>>成功上传后 获取上传地址
        $file_path = $info->getPath() . '/' . $info->getFilename();

        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx');
        $reader->setReadDataOnly(TRUE);
        $spreadsheet = $reader->load($file_path);
        $worksheet = $spreadsheet->getActiveSheet();
        $ret_arr = [];
        foreach ($worksheet->getRowIterator() as $row) {
            $row_arr = [];
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(FALSE); // This loops through all cells,
            foreach ($cellIterator as $cell) {
                $row_arr[] = $cell->getValue();
            }
            $ret_arr[] = $row_arr;
        }
        return $ret_arr;
    }

    //>>导出数据
    public static function exportExcelFromArr($array = [], $width = 20, $height = 15)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $spreadsheet->getActiveSheet()->getDefaultColumnDimension()->setWidth($width);
        $spreadsheet->getActiveSheet()->getDefaultRowDimension()->setRowHeight($height);

        $Architecture = 1;
        foreach ($array as $row) {
            $i = 1;
            foreach ($row as $cell) {
                $sheet->setCellValueByColumnAndRow($i, $Architecture, $cell);
                $i++;
            }
            $Architecture++;
        }


        //>>告诉浏览器输出07Excel文件
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        //>>告诉浏览器将要输出Excel03版本文件
//header(‘Content-Type:application/v    nd.ms-excel‘);
        //header('Content-type:application/vnd.ms-excel;charset=utf-8;name="' . $xlsTitle . '.xls"');
        //>>告诉浏览器需要输出的文件名称
        $time = date("科翔数据-Y-m-d丨H∶i∶s");
        header('Content-Disposition: attachment;filename="' . $time . '.xlsx"');
        //>>禁止缓存
        header('Cache-Control: max-age=0');
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
    }

}
