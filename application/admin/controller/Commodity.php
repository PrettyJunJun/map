<?php

namespace app\admin\controller;

use app\common\controller\Admin;
use think\Db;

class Commodity extends Admin
{
    public function commodity()
    {
        $model = model('WellType');
        $data = $model->select();  //查出井盖类型
        $this->assign('data', $data);
        if (request()->isPost()) {  //添加井盖信息
            $mel = model('Well');
            $file = request()->file('img');
            if ($file) {
                $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
                if ($info) {
                    $sm = input('');
                    if ($sm['well_type_id'] != '') {
                        $sm['file'] = $info->getSaveName();
                        $mel->save($sm);
                        $this->success('发布成功', 'Index/index');
                    } else {
                        $error = '类型值不能为空';
                        $this->assign('error', $error);
                    }
                } else {
                    echo $file->getError();
                }
            }
            $sm = input('');
            if ($sm['well_type_id'] != '') {
                $mel->save($sm);
                $this->success('发布成功', 'Index/index');
            } else {
                $error = '类型值不能为空';
                $this->assign('error', $error);
            }
        }
        return $this->fetch();
    }

    public function commodityy()
    { //修改井盖信息
        $model = model('WellType');
        $data = $model->select();
        $this->assign('data', $data);
        $sm = input('');
        if (request()->isPost()) {
            $mel = model('Well');
            $file = request()->file('img');
            if ($file) {
                $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
                if ($info) {
                    $sm = input('');
                    if ($sm['well_type_id'] != '') {
                        $sm['file'] = $info->getSaveName();
                        $mel->where(['id' => $sm['id']])->update($sm);
                        $this->success('修改成功', 'Index/index');
                    } else {
                        $error = '类型值不能为空';
                        $this->assign('error', $error);
                    }

                } else {
                    echo $file->getError();
                }
            }
            $sm = input('');
            if ($sm['well_type_id'] != '') {
                $mel->where(['id' => $sm['id']])->update($sm);
                $this->success('修改成功', 'Index/index');
            } else {
                $error = '类型值不能为空';
                $this->assign('error', $error);
            }
        }
        return $this->fetch();
    }

    public function lists()
    {  //查出井盖类型列表
        $model = model('WellType');
        //$list = $model->paginate(1);
        $list = $model->select();
        //$page = $list->render();
        $this->assign('list', $list);
        // $this->assign('page', $page);
        return $this->fetch();
    }

    public function type()
    {  //添加井盖类型属性值
        if (request()->isPost()) {
            $data = input('');
            $model = model('WellType');
            $validate = validate('WellType');
            if (!$validate->batch()->check($data)) {
                $cw = $validate->getError();
                $this->assign('cw', $cw);
            } else {
                $model->save($data);
                $this->success('发布成功', 'Index/index');
            }
        }
        return $this->fetch();
    }

    public function delete()
    { //删除井盖类型相对应的井盖列表也将删除
        if (request()->isAjax()) {
            Db::transaction(function () {
                $id = input('id');
                $sm = Db::table('well_type')->where(['id' => $id])->find();
                $type_id = $sm['type'];
                Db::table('well')->where(['well_type_id' => $type_id])->delete();
                Db::table('well_type')->where(['id' => $id])->delete();
                echo 1;
            });
        }
    }

    public function typee()
    {  //修改井盖类型
        if (request()->isPost()) {
            $data = input('');
            $validate = validate('WellType');
            if (!$validate->batch()->check($data)) {
                $cw = $validate->getError();
                $this->assign('cw', $cw);
            } else {
                $model = model('WellType');
                $model->where(['id' => $data['id']])->update($data);
                $this->success('修改成功', 'Commodity/lists');
            }
        }
        //>>回显
        $id = input('id');
        $model = model('WellType');
        $data = $model->where(['id' => $id])->find();
        $this->assign('data', $data);
        return $this->fetch();
    }

    public function listss()
    { //井盖信息列表
        $id = input('id');
        $model = model('Well');
        $list = $model->where(['well_type_id' => $id])->paginate(8);
        //$list = $model->where(['well_type_id'=>$id])->select();
        $page = $list->render();
        $this->assign('list', $list);
        $this->assign('page', $page);
        return $this->fetch();
    }

    public function delte()
    {  //删除
        if (request()->isAjax()) {
            $id = input('id');
            $model = model('Well');
            $model->where(['id' => $id])->delete();
            echo 1;
        }
    }

    public function batch()
    { //批量删除
        if (request()->isAjax()) {
            $data = input('');
            $model = model('Well');
            foreach ($data['checkData'] as $value) {
                $model->where(['id' => $value])->delete();
            }
            echo 1;
        }
    }

    //>>修改井盖属性类型
    public function sewer()
    {
        if (request()->isPost()) {
            $data = input('');
            $model = model('Well');
//                var_dump($model);die;
            $model->where(['id' => $data['id']])->update($data);
            $this->success('修改成功', 'Distance/sewer');

        }
        //>>回显
        $id = input('id');
        $model = model('Well');
        $data = $model->where(['id' => $id])->find();
        $this->assign('data', $data);
        return $this->fetch();
    }

    //>>删除井盖类型
    public function off()
    {
        if (request()->isAjax()) {
            $id = input('id');
            $model = model('Well');
            $model->where(['id' => $id])->delete();
        }
    }
}
