<?php

declare(strict_types=1);

namespace lite\model;

use lite\model\BaseModel;

class ConfigModel extends BaseModel
{
    protected $name = 'config';

    // 自动数据类型转换
    protected $type = [];

    // 自动 json 转换
    protected $json = [];
}