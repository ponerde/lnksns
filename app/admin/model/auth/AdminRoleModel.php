<?php

declare(strict_types=1);

namespace app\admin\model\auth;

use lite\model\BaseModel;

class AdminRoleModel extends BaseModel
{
    protected $name = 'admin_role';

    public function setRulesAttr($value)
    {
        return implode(',',$value);
    }

    public function getRulesAttr($value)
    {
        return explode(',',$value);
    }

}