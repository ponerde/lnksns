<?php

declare(strict_types=1);

namespace app\lnksns\model;

use app\admin\model\auth\AdminRoleModel;
use lite\model\BaseModel;
use lite\service\FileService;

class ClauseModel extends BaseModel
{
    protected $name = 'free_clause';

    protected $type = [];

    protected $json = [];    // 自动 json 转换

}
