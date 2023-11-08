<?php

declare(strict_types=1);

namespace app\admin\model\auth;

use lite\model\BaseModel;

class AdminLog extends BaseModel
{
    protected $name = 'admin_log';

    // 自动数据类型转换
    protected $type = [];

    protected $hidden = [];

    // 自动 json 转换
    protected $json = [];

    protected $append = [];


    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id', 'id');
    }
}
