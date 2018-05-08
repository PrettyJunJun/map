<?php

namespace app\admin\controller;

use app\common\controller\Admin;
use think\Db;
use think\Debug;

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

    public function excel()
    {
        return $this->fetch();
    }

    //>>Excel保存导入
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
                $data[$k]['name'] = $v[0];
                $data[$k]['sex'] = $v[1];
                $i++;
            }
            $success = Db::name('excel')->insertAll($data); //批量插入数据
            //$i=
            $error = $i - $success;
            echo "总{$i}条，成功{$success}条，失败{$error}条。";
//             Db::name('t_station')->insertAll($city); //批量插入数据
        } else {
            //>>上传失败获取错误信息
            echo $file->getError();
        }

    }
}
