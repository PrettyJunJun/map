<?php
namespace app\common\controller;
use think\Controller;

class Admin extends Controller{

    public $login = ['login'];
    public function _initialize(){
        if ( !session('?login') && !in_array(request()->action(),$this->login) ) {
            $this->redirect('login/login');
        }
        $this->params = array(
            'module' => request()->module(),
            'controller' => request()->controller(),
            'action' => request()->action(),
            'args' => request()->param()
        );
        $this->root = $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].request()->root().'/';
        $this->assign('root',$this->root);
    }
}
