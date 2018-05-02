<?php

namespace app\admin\controller;

use app\common\controller\Admin;
use think\Db;

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
            ->field('t1.name,t2.well_name,t1.id')
            ->select();
//        $model = model('Well');
//        $data = $model->select();
//        pr($data);
//        die;
//        pr($data);die;
        $this->assign('data',$data);
        return $this->fetch();
    }

    //>>查询出街道对应信息
    public function street(){
        
    }
}
