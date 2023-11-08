<?php

declare(strict_types=1);

namespace app\admin\model\auth;

use lite\model\BaseModel;
use lite\service\FileService;

class AdminModel extends BaseModel
{
    protected $name = 'admin';

    protected $type = [
        'role_id'     => 'integer',
        'login_time'  =>  'timestamp',
    ];

    protected $json = [];    // 自动 json 转换


    public function getAvatarAttr($value)
    {
        return FileService::getFileUrl($value);
        
    }
    
    

    public function role()
    {
        return $this->hasOne(AdminRoleModel::class, "id", "role_id")->field("id, name");
    }
}
