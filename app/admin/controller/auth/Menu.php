<?php

declare(strict_types=1);

namespace app\admin\controller\auth;

use lite\controller\Backend;
use app\admin\model\auth\AdminModel;
use app\admin\service\MenuService;
use lite\controller\traits\Crud;
use lite\exception\LiteException;
use think\facade\Db;

class Menu extends Backend
{
    use Crud;
    

    public function tree()
    {
        return success(MenuService::tree());
    }
}