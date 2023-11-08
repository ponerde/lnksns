<?php

declare(strict_types=1);

namespace app\lnksns\model;

use lite\model\BaseModel;
use lite\service\FileService;

class UserModel extends BaseModel
{
    protected $name = 'free_user';

    protected $type = [

    ];

    protected $json = [];    // 自动 json 转换


    public function getAvatarAttr($value)
    {
        return FileService::getFileUrl($value);
        
    }

}
