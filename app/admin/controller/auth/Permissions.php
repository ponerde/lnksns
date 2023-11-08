<?php

declare(strict_types=1);

namespace app\admin\controller\auth;

use app\admin\model\auth\AdminRoleModel;
use app\admin\model\auth\Permission as PermissionModel;
use app\admin\service\MenuService;
use think\Request;
use app\BaseController;
use lite\controller\Backend;
use lite\exception\LiteException;
use lite\controller\traits\Crud;
class Permissions extends Backend
{
    use Crud;
    public function initialize()
    {
        $this->model = new PermissionModel();
    }

    public function index()
    {
        $data = MenuService::getMenuTree();
        return success($data);
    }

    public function save(Request $request)
    {
        $params = $request->all();
        PermissionModel::create([
            'permission_mark' => $params['permission_mark'] ?? '',
            'permission_name' => $params['permission_name'],
            'icon' => $params['icon'],
            'parent_id'=>$params['parent_id'],
            'component' => $params['component'] ?? '',
            'route' => $params['route'] ?? '',
            'weigh' => $params['weigh'],
            'icon' => $params['icon'],
            'type' => $params['type'],
        ]);
        return success();
    }

    public function update(Request $request,$id)
    {
        $params = $request->all();
        PermissionModel::where('id',$id)->update([
            'permission_mark' => $params['permission_mark'],
            'permission_name' => $params['permission_name'],
            'component' => $params['component'],
            'route' => $params['route'],
            'weigh' => $params['weigh'],
            'icon' => $params['icon'],
            'parent_id'=>$params['parent_id'],
            'type' => $params['type'],
        ]);
        return success();
    }

    public function delete($id)
    {
        $permission = PermissionModel::whereIn('id',$id)->find();
        $parent_permission = PermissionModel::whereIn('parent_id',$permission->id)->count();
        if($parent_permission>0){
            return error('当前菜单有下级菜单不能删除');
        }
        PermissionModel::where('id',$id)->delete();
        return success('删除成功');
    }

    public function tree()
    {
        $menu =  PermissionModel::where('status','show')->field('parent_id,id,id as value,permission_name as label')->order('weigh desc')->select();
        $data = MenuService::buildMenuTree($menu);
        // var_dump($menu);
   
        return success($data);
    }


}