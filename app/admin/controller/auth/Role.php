<?php

declare(strict_types=1);

namespace app\admin\controller\auth;

use lite\controller\Backend;
use app\admin\model\auth\AdminModel;
use app\admin\model\auth\AdminRoleModel;
use app\Request;
use lite\controller\traits\Crud;
use lite\exception\LiteException;
use think\facade\Db;

class Role extends Backend
{
    use Crud;
    protected $childAdminIds = [];

    public function initialize()
    {
        $this->model = new AdminRoleModel();
    }


    public function tree()
    {
        $list = $this->model->liteFilter()->select();   
        return success($list);
    }

    public function updateRolePermissions(Request $request)
    {
        $roleId = $request->put('role_id');
        $menuKeys = $request->put('menu_keys');
        AdminRoleModel::where('id',$roleId)->update(['rules'=>implode(',',$menuKeys)]);
        return success();
    }

   

  
}
