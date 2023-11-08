<?php

declare(strict_types=1);

namespace app\lnksns\model;

use lite\model\BaseModel;

class DynamicModel extends BaseModel
{
    protected $name = 'free_dynamic';

    protected $type = [];

    protected $json = [];    // 自动 json 转换

    public function user()
    {
        return $this->hasOne(UserModel::class, "id", "user_id")->field("id,name,avatar");
    }

}
