<?php

namespace app\admin\controller;

use app\common\controller\Admin;
use app\common\controller\Excel;
use think\Db;
use think\Debug;
use think\session\driver\Redis;
use app\admin\model\Well;

class Distance extends Admin
{
    public function distance()
    {
        $model = model('WellType');
        $data = $model->select();
        $this->assign('data', $data);
        if (request()->isPost()) {
            $model = model('WellDistance');
            $sm = input('');
            if ($sm['well_id'] == $sm['names']) {
                $cw = '不能选同样的井盖名';
                $this->assign('cw', $cw);
            } else {
                $model->save($sm);
                $this->success('添加成功', 'Index/index');
            }
        }
        return $this->fetch();
    }

    //>>查询出井盖对应的类型
    public function sewer()
    {
//        $data = Db::table('well_distance')
//            ->field('names,distance')
//            ->where('id', 5)
//            ->select();
        //SELECT t1.id,t1.`name`,t2.`name` FROM well t1 LEFT JOIN well_type t2 ON  t1.well_type_id=t2.type
        $data = Db::table('well')
            ->alias('t1')
            ->join('well_type t2', 't1.well_type_id = t2.type', 'LEFT')
            ->field('t1.name,t2.well_name,t1.id,t1.x,t1.y')
            ->paginate(20);
//        $model = model('Well');
//        $data = $model->select();
//        pr($data);
//        die;
//        pr($data);die;
        $this->assign('data', $data);
        return $this->fetch();
    }

    //>>查询出街道对应相关信息
    public function street()
    {
        $data = Db::table('street')
            ->alias('t1')
            ->join('well t2', 't1.street_id=t2.well_street_id', 'LEFT')
            ->join('well_type t3', 't2.well_type_id=t3.type', 'LEFT')
            ->field('t1.street_name,t2.`name`,t3.well_name')
            ->paginate(20);
//        var_dump($data);die;
        $this->assign('data', $data);
        return $this->fetch();
    }

    //>>查询出管道对应相关信息
    public function conduit()
    {
        $data = Db::table('conduit')
            ->alias('t1')
            ->join('street t2', 't1.well_street_id = t2.street_id', 'LEFT')
            ->join('well_type t3', 't1.well_type_id = t3.type', 'LEFT')
            ->field('t1.conduit_name,t1.sdeep,t1.pcode,t1.dtype,t1.material,t1.dsize,t2.street_name,t3.well_name')
            ->paginate(20);
        $this->assign('data', $data);
        return $this->fetch();

    }

    public function excel()
    {
        return $this->fetch();
    }

    //>>Excel导入数据
    public function addExcel()
    {
        //>>方法一
        vendor("PHPExcel.PHPExcel");
        //>>方法二
        //import('phpexcel.PHPExcel', EXTEND_PATH);
        $objPHPExcel = new \PHPExcel();

        //>>获取表单上传文件
        $file = request()->file('excel');
        $info = $file->validate(['size' => 99999, 'ext' => 'xlsx,xls,csv'])->move(ROOT_PATH . 'public' . DS . 'excel');
        if ($info) {
            //获取文件名
            $exclePath = $info->getSaveName();
            //>>上传文件的地址
            $file_name = ROOT_PATH . 'public' . DS . 'excel' . DS . $exclePath;
            $objReader = \PHPExcel_IOFactory::createReader('Excel2007');
            //>>加载文件内容,编码utf-8
            $obj_PHPExcel = $objReader->load($file_name, $encode = 'utf-8');
            echo "<pre>";
            //>>转换为数组格式
            $excel_array = $obj_PHPExcel->getsheet(0)->toArray();
            //>>删除第一个数组(标题)
            //>>array_shift（）删除数组中第一个元素，并返回被删除元素的值
            array_shift($excel_array);
            $data = [];
            $i = 0;
            foreach ($excel_array as $k => $v) {
                $data[$k]['conduit_name'] = $v[0];
                $data[$k]['sdeep'] = $v[1];
                $data[$k]['pcode'] = $v[2];
                $data[$k]['dtype'] = $v[3];
                $data[$k]['material'] = $v[4];
                $data[$k]['dsize'] = $v[5];
                $data[$k]['well_street_id'] = $v[6];
                $data[$k]['well_type_id'] = $v[7];
                $i++;
            }
            //>>批量插入数据
            $success = Db::name('conduit')->insertAll($data);
            $error = $i - $success;
            echo "<script language='JavaScript'>
                    alert('总{$i}条，成功{$success}条，失败{$error}条');
                    window.location = 'http://www.tp5.com/admin/distance/excel.html';
                    </script>
                    ";
        } else {
            //>>上传失败获取错误信息
            echo $file->getError();
        }

    }

    //>>Excel导出数据
    public function exportExcel()
    {
        $data = Db::table('well')
            ->alias('t1')
            ->field('t1.id,t1.name,t1.spec,t1.hsize,t1.msize')
            ->select();
        //>>定义字段名称
        $example_arr = [
            ['ID', '探点号', '井盖规格', '测试', '测试'],
        ];
        foreach ($data as $k => $vo) {
            //>>需要查询的字段名
            $example_arr[] = [
                $vo['id'],
                $vo['name'],
                $vo['spec'],
                $vo['hsize'],
                $vo['msize'],
            ];
        }
        Excel::exportExcelFromArr($example_arr);
    }
//    //>>Excel导出数据
//    /**
//     * execl数据导出
//     * @param string $title 模型名，用于导出生成文件名的前缀
//     * @param array $cellName 表头及字段名
//     * @param array $data 导出的表数据
//     *
//     * 特殊处理：合并单元格需要先对数据进行处理
//     */
//    function exportOrderExcel($title = "linkage",$cellName='',$data='')
//    {
//        $data = Db::table('well')
//                ->alias('wl')
//                ->field('wl.id,wl.name')
//                ->select();
//        $cellName = array_keys($data[0]);
//        //引入核心文件
//        vendor("PHPExcel.PHPExcel");
//        $objPHPExcel = new \PHPExcel();
//        //定义配置
//        $topNumber = 2;//表头有几行占用
//        $xlsTitle = iconv('utf-8', 'gb2312', $title);//文件名称
//        $fileName = $title . date('_YmdHis');//文件名称
//        $cellKey = array(
//            'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M',
//            'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z',
//            'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM',
//            'AN', 'AO', 'AP', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AV', 'AW', 'AX', 'AY', 'AZ'
//        );
//
//        //写在处理的前面（了解表格基本知识，已测试）
////     $objPHPExcel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(20);//所有单元格（行）默认高度
////     $objPHPExcel->getActiveSheet()->getDefaultColumnDimension()->setWidth(20);//所有单元格（列）默认宽度
////     $objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(30);//设置行高度
////     $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(30);//设置列宽度
////     $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setSize(18);//设置文字大小
////     $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);//设置是否加粗
////     $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_WHITE);// 设置文字颜色
////     $objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);//设置文字居左（HORIZONTAL_LEFT，默认值）中（HORIZONTAL_CENTER）右（HORIZONTAL_RIGHT）
////     $objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);//垂直居中
////     $objPHPExcel->getActiveSheet()->getStyle('A1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);//设置填充颜色
////     $objPHPExcel->getActiveSheet()->getStyle('A1')->getFill()->getStartColor()->setARGB('FF7F24');//设置填充颜色
//
//        //处理表头标题
//        $objPHPExcel->getActiveSheet()->mergeCells('A1:' . $cellKey[count($cellName) - 1] . '1');//合并单元格（如果要拆分单元格是需要先合并再拆分的，否则程序会报错）
//        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', '管道信息');
//        $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
//        $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setSize(18); //PHPExcel_Style_Alignment
//        $objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//        $objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
//
//        //处理表头
//        foreach ($cellName as $k => $v) {
//            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($cellKey[$k] . $topNumber, $v[1]);//设置表头数据
//            $objPHPExcel->getActiveSheet()->freezePane($cellKey[$k] . ($topNumber + 1));//冻结窗口
//            $objPHPExcel->getActiveSheet()->getStyle($cellKey[$k] . $topNumber)->getFont()->setBold(true);//设置是否加粗
//            $objPHPExcel->getActiveSheet()->getStyle($cellKey[$k] . $topNumber)->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);//垂直居中
//            if ($v > 0)//大于0表示需要设置宽度
//            {
//                $objPHPExcel->getActiveSheet()->getColumnDimension($cellKey[$k])->setWidth($v[3]);//设置列宽度
//            }
//        }
//        //处理数据
//        foreach ($data as $k => $v) {
//            foreach ($cellName as $k1 => $v1) {
//                $objPHPExcel->getActiveSheet()->setCellValue($cellKey[$k1] . ($k + 1 + $topNumber), $v[$v1[0]]);
//                if ($v['end'] > 0) {
//                    if ($v1[2] == 1)//这里表示合并单元格
//                    {
//                        $objPHPExcel->getActiveSheet()->mergeCells($cellKey[$k1] . $v['start'] . ':' . $cellKey[$k1] . $v['end']);
//                        $objPHPExcel->getActiveSheet()->getStyle($cellKey[$k1] . $v['start'])->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
//                    }
//                }
//                if ($v1[4] != "" && in_array($v1[4], array("LEFT", "CENTER", "RIGHT"))) {
//                    $v1[4] = eval('return PHPExcel_Style_Alignment::HORIZONTAL_' . $v1[4] . ';');
//                    //这里也可以直接传常量定义的值，即left,center,right；小写的strtolower
//                    $objPHPExcel->getActiveSheet()->getStyle($cellKey[$k1] . ($k + 1 + $topNumber))->getAlignment()->setHorizontal($v1[4]);
//                }
//            }
//        }
//        //导出execl
//        header('pragma:public');
//        header('Content-type:application/vnd.ms-excel;charset=utf-8;name="' . $xlsTitle . '.xls"');
//        header("Content-Disposition:attachment;filename=$fileName.xls");//attachment新窗口打印inline本窗口打印
//        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
//        $objWriter->save('php://output');
//        exit;
//    }

}
