<?php
namespace app\index\controller;
use app\common\Controller;
class Index extends Index
{
    public function index()
    {

        return $this->fetch();
     }
    public function index_ajax(){
    	if(request()->isAjax()){
    		$name = input('name');
    		$data = Db::table('well')->where(['name'=>$name])->find();
    		$data = json_encode($data)
    		pr($data);
    	}
    }
}
