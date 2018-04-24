<?php
namespace app\admin\controller;
use app\common\controller\Admin;

class Distance extends Admin
{
    public function distance(){
        $model = model('WellType');
        $data = $model->select();
        $this->assign('data',$data);
        if(request()->isPost()){
            $model = model('WellDistance');
            $sm = input('');
            if($sm['well_id']==$sm['names']){
                $cw = '不能选同样的井盖名';
                $this->assign('cw',$cw);
            }else{
                $model->save($sm);
                $this->success('添加成功', 'Index/index');
            }
        }
        return $this->fetch();
    }
    public function xx(){
        $model = model('WellDistance');
        $data = $model->with('well')->select();
        pr($data);
    }
}
