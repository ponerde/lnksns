<?php

declare(strict_types=1);

namespace lite\model\file;

use lite\model\BaseModel;
use lite\service\FileService;

class FileModel extends BaseModel
{
    protected $name = 'file';

    protected $append = ['url'];

    // 自动数据类型转换
    protected $type = [];

    // 自动 json 转换
    protected $json = [];


    public function getUrlAttr()
    {
        return $this->uri ? FileService::getFileUrl($this->uri):'';
    }

   
}
