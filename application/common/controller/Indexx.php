<?php
namespace app\common\controller;
use think\Controller;

class Indexx extends Controller
{
    public function _initialize(){
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
