<?php

declare(strict_types=1);

namespace app\admin\controller;

use lite\model\auth\AdminModel;
use app\admin\model\auth\AdminRoleModel;
use app\admin\model\auth\Permission;
use app\admin\service\MenuService;
use think\Request;
use app\BaseController;
use lite\controller\Backend;
use lite\exception\LiteException;

class Index extends Backend
{
    public function index()
    {
        return view('index');
    }


    /**
     * 登录
     */
    public function login(Request $request)
    {
        $account = $request->only(['username','password']);
        $this->auth('admin')->attempt($account);
        $admin = $this->auth()->user();
        $data = [
            "token" =>  $this->auth()->getToken(),
        ];
        return success($data);
    }

     /**
     * 获取管理员个人资料
     *
     * @return void
     */
    public function profile()
    {
        $admin = $this->auth()->user();
        // var_dump($admin);
        $permission = MenuService::userMenu($admin->id);
        $user = AdminModel::find($admin->id);
        $admin = [
            'permission'=>$permission,
            'user'=>$user,
        ];
        return success($admin);
    }

    /**
     * 用户菜单
     */
    public function userMenu()
    {
       $admin = $this->auth()->user();
       $adminId = $admin->id;
       $roleId = $admin->role_id;
       $m = MenuService::tree();
       $menu = MenuService::userMenu($adminId,$roleId,$m);
    //    var_dump($menu);
        $data = [
            'menu' => $menu,
            'dashboardGrid' => [
                "welcome",
                "ver",
                "time",
                "progress",
                "echarts",
                "about"
            ],
            'permissions' => [
                "list.add",
                "list.edit",
                "list.delete",
                "user.add",
                "user.edit",
                "user.delete"
            ]
        ];
        return success($data);
    }
}
