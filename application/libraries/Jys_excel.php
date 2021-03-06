<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * =========================================================
 *
 *      Filename: Jys_excel.php
 *
 *   Description: 表格类
 *
 *       Created: 2016/11/22 21:24:20
 *
 *        Author: huazhiqiang
 *
 * =========================================================
 */
require_once FCPATH."application/third_party/phpexcel/PHPExcel.php";
class Jys_excel{

    /**
     * 导出表格
     * @param $header
     * @param $data
     * @param string $file_name
     * @return bool
     * @throws PHPExcel_Reader_Exception
     */
    public function export_order_list($header, $data, $file_name = "Jys_express_company") {
        if (empty($header) || !is_array($header)) {
            return FALSE;
        }

        $objPHPExcel = new PHPExcel();
        // 填写表头
        for ($i = 0; $i < count($header); $i++) {
            $column = chr(ord('A')+$i);
            $objPHPExcel->getActiveSheet()->setCellValue($column.'1', $header[$i]);
        }
        if (!empty($data) && is_array($data) && count($data) > 0) {
            // 填写数据
            for ($i = 0; $i < count($data); $i++) {
                $row = 2+$i;
                for ($j = 0; $j < count($data[$i]); $j++) {
                    $column = chr(ord('A')+$j);
                    $objPHPExcel->getActiveSheet()->setCellValueExplicit($column.$row, $data[$i][$j], PHPExcel_Cell_DataType::TYPE_STRING);
                }
            }
        }
        for ($i = 0; $i < count($header); $i++) {
            $column = chr(ord('A')+$i);
            $objPHPExcel->getActiveSheet()->getColumnDimension($column)->setAutoSize(true);
        }

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
        header("Content-Type:application/force-download");
        header("Content-Type:application/vnd.ms-execl");
        header("Content-Type:application/octet-stream");
        header("Content-Type:application/download");
        if (empty($file_name)) {
            header('Content-Disposition:attachment;filename="Jys_express_company.xlsx"');
        }else {
            header('Content-Disposition:attachment;filename="'.$file_name.'.xlsx"');
        }

        header("Content-Transfer-Encoding:binary");
        $objWriter->save('php://output');
    }

    /**
     * 导入excel文件来获取表格中数据
     * @param string $file_name
     * @return bool
     * @throws PHPExcel_Reader_Exception
     */
    public function export_excel($file_name = "Jys_express_company.xlsx"){
        if (empty($file_name)) {
            return FALSE;
        }
        //判断导入表格后缀格式
        $extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        if ($extension == 'xlsx') {
            $objReader =PHPExcel_IOFactory::createReader('Excel2007');
            $objPHPExcel =$objReader->load($file_name, $encode = 'gb2312');
        } else if ($extension == 'xls'){
            $objReader =PHPExcel_IOFactory::createReader('Excel5');
            $objPHPExcel =$objReader->load($file_name, $encode = 'gb2312');
        }
        $sheet =$objPHPExcel->getSheet(0);
        $highestRow = $sheet->getHighestRow();      //取得总行数
        for ($i = 2,$j = 0; $i <= $highestRow; $i++, $j++) { //除掉一二行
            $data[$j]['name'] =$objPHPExcel->getActiveSheet()->getCell("A" .$i)->getValue();
            $data[$j]['code'] =$objPHPExcel->getActiveSheet()->getCell("B" .$i)->getValue();
            $data[$j]['trajectory_query'] =$objPHPExcel->getActiveSheet()->getCell("C" .$i)->getValue();
            $data[$j]['electronic_delivery'] =$objPHPExcel->getActiveSheet()->getCell("D" .$i)->getValue();
            $data[$j]['visiting_service'] =$objPHPExcel->getActiveSheet()->getCell("E" .$i)->getValue();
            $data[$j]['create_time'] = date('Y-m-d H:i:s'); //创建时间
        }
        return $data;

    }

    public function export_user_data_excel($file_name = NULL){
        if (empty($file_name)) {
            return FALSE;
        }
        //判断导入表格后缀格式
        $extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        if ($extension == 'xlsx') {
            $objReader =PHPExcel_IOFactory::createReader('Excel2007');
            $objPHPExcel =$objReader->load($file_name, $encode = 'gb2312');
        } else if ($extension == 'xls'){
            $objReader =PHPExcel_IOFactory::createReader('Excel5');
            $objPHPExcel =$objReader->load($file_name, $encode = 'gb2312');
        }
        $sheet =$objPHPExcel->getSheet(0);
        $highestRow = $sheet->getHighestRow();      //取得总行数
        for ($i = 2,$j = 0; $i <= $highestRow; $i++, $j++) { //除掉一二行
            $data[$j]['username'] =$objPHPExcel->getActiveSheet()->getCell("A" .$i)->getValue();
            $data[$j]['name'] =$objPHPExcel->getActiveSheet()->getCell("B" .$i)->getValue();
            $data[$j]['gender'] =$objPHPExcel->getActiveSheet()->getCell("C" .$i)->getValue();
            $data[$j]['phone'] =$objPHPExcel->getActiveSheet()->getCell("D" .$i)->getValue();
            $pwd = substr($data[$j]['phone'], 5, 6);
            $data[$j]['password'] = password_hash($pwd, PASSWORD_DEFAULT);
            $data[$j]['role_id'] = 10;
            $data[$j]['create_time'] = date('Y-m-d H:i:s'); //创建时间
            $data[$j]['update_time'] = date('Y-m-d H:i:s');
        }
        return $data;

    }
}