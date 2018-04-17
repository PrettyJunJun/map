<?php
namespace app\admin\controller;
use app\common\controller\Admin;

class Commodity extends Admin
{
    public function commodity()
    {
        $file = request()->file('file');
        if($file){
            $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
            if($info){
                // 成功上传后 获取上传信息
                // 输出 jpg
                echo $info->getSaveName();
                // 输出 42a79759f284b767dfcb2a0197904287.jpg
                echo $info->getFilename();
            }else{
                // 上传失败获取错误信息
                echo $file->getError();
            }
        }
        return $this->fetch();
     }
    public function lists(){

        return $this->fetch();
    }
}

//测试
