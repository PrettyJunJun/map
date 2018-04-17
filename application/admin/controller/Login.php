<?php
namespace app\admin\controller;
use app\common\controller\Admin;

class Login extends Admin
{
    public function login()
    {
        if (request()->isPost()){
            $data = input('');
            $model = model('User');
            $sm = $model->where(['user'=>$data['user'],'password'=>$data['password']])->find();
            session('login',$sm);
            if($sm) {
                $this->success('登陆成功', 'Index/index');
            }else{
                $this->success('帐号密码错误', 'Login/login');
            }
        }
     return $this->fetch();
    }
    public function outLogin(){
        session('login',null);
        $this->success('退出成功', 'Login/login');
    }
}
