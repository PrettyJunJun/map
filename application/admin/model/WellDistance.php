<?php
namespace app\admin\model;
use think\Model;

class WellDistance extends Model
{
    public function well()
    {
        return $this->hasOne('Well');
    }
}