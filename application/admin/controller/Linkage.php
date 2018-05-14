<?php

namespace app\admin\controller;

use app\common\controller\Admin;

class Linkage extends Admin
{
    //>>三级联动
    public function linkage()
    {
        return $this->fetch();
    }

    //>>四级联动
    public function FourLevel()
    {
        return $this->fetch();
    }
}