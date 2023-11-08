<?php

declare(strict_types=1);

namespace app\lnksns\model;

use app\admin\model\auth\AdminRoleModel;
use lite\model\BaseModel;
use lite\service\FileService;

class CircleModel extends BaseModel
{
    protected $name = 'free_circle';

    protected $type = [];

    protected $json = [];    // 自动 json 转换


    public function getAvatarAttr($value)
    {
        return FileService::getFileUrl($value);
    }

    public function user()
    {
        return $this->hasOne(UserModel::class, "id", "user_id")->field("id, name");
    }

}
