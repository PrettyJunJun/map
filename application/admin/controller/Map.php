<?php
namespace app\admin\controller;

use app\admin\model\Admin;


class Map extends \app\common\controller\Admin {

    public function map()
    {
        return $this->fetch();
    }
}