<?php

declare(strict_types=1);

namespace app\admin\service;

use app\admin\model\auth\AdminModel;
use app\admin\model\auth\AdminRoleModel;
use app\admin\model\auth\Permission;
use lite\controller\traits\Crud;
use lite\exception\LiteException;
use think\facade\Db;

class MenuService
{
   


    public static function getMenu($where= [])
    {
        $menu = [];
        $menu =  Permission::where('status','show')->where($where)->order('weigh desc')->select();
        foreach($menu as &$v){
            $v['path'] = 1;
        }
        return $menu;
    }

    public static function getMenuTree()
    {
        
        $menu =  self::getMenu();
        $treeMenu = self::buildMenuTree($menu);
        return $treeMenu;
    }



    public static function buildMenuTree($menuItems, $parentId = null,$keyName='id',$parentKeyName='parent_id')
    {
        $tree = [];
        foreach ($menuItems as $k=>$menuItem) {  
            if ($menuItem[$parentKeyName] == $parentId) {  
               
                $menuItem['children']  = self::buildMenuTree($menuItems,$menuItem[$keyName]);
              
                $tree[] = $menuItem;
            }
            
        }  
        return $tree;
    }


    // 获取后台用户菜单
    public static function userMenu($adminId,$menu=[])
    {
        $admin = AdminModel::where('id',$adminId)->find();
        $rule = AdminRoleModel::where('id',$admin->role_id)->find();
        $where = [
            ['type','<>',2],
            
        ];
        if($admin->is_super !=1){
            array_push($where,['permission_mark','in',$rule->rules]);
        }
        $menu =  self::getMenu($where);
        $treeMenu = self::buildMenuTree($menu);
        return $treeMenu;
    }

    function removeKey($key, &$array, $childKey = 'children'){
        if(isset($array[$key])){
            unset($array[$key]);
            return;
        }
        foreach($array as &$item)
            if(isset($item[$childKey]))
                $this->removeKey($key, $item[$childKey], $childKey);
    }
}
