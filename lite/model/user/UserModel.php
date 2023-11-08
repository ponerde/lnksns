<?php

declare(strict_types=1);

namespace lite\model\user;

use lite\model\BaseModel;


class UserModel extends BaseModel
{
   

    protected $name = 'user';

    // 自动数据类型转换
    protected $type = [];

    protected $hidden = ['password', 'salt'];

}
