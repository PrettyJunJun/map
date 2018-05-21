<?php
namespace app\index\controller;

use app\common\controller\Indexx;

class Index extends Indexx{
	
    public function index()
    {
    	
        return $this->fetch();
    }
    public function index_ajax()
    {
    	if(request()->isAjax()){
    		$name = input('data');
    		$data = db('well')->where('name',$name)->find();
    		if($data){
    			$sm = json_encode($data);
    			echo $sm;
    		}else{
    			echo(1);
    		}
    		
    		
    	
    	}
    }
}
