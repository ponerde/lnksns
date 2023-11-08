<?php

declare(strict_types=1);

namespace app\admin\model;

use lite\model\BaseModel;

class PageModel extends BaseModel
{
    protected $name = 'page';
    // protected $autoWriteTimestamp = false;

    // 自动数据类型转换
    protected $type = [];

    // 自动 json 转换
    protected $json = [];


    public function setPageAttr($value)
    {
        return json_encode($value);
    }

    public function getPageAttr($value)
    {
        return $value ? json_decode($value,false):[];
    }

}
