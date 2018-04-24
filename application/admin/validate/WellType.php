<?php
namespace app\admin\validate;

use think\Validate;

class WellType extends Validate
{
    protected $rule = [
        'name'   => 'require|unique:well_type',
        'type'   => 'require|unique:well_type',
    ];
    protected $message  =   [
        'name.require' => '名称不能为空',
        'type' => '值不能为空',
        'name.unique' => '名称重复',
        'type.unique'   => '值重复',
    ];
}