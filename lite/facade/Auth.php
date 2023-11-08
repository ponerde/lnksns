<?php

declare(strict_types=1);

namespace lite\facade;

use think\Facade;

/**
 * @see \lite\library\auth\Auth
 * @method static object guard(string $name) 设置认证驱动 admin|user
 * @method static int id() 当前登录用户 id
 * @method static object user() 当前登录用户
 * @method static boolean attempt(array $credentials = []) 登录
 * @method static object login(object $user) 根据模型登录用户
 * @method static object loginUsingId(int $id) 根据 id 登录用户
 * @method static void logout() 退出登录
 * @method static array getAccess(bool $cache = true) （仅管理端）获取当前管理员的所有权限
 * @method static array getRulesByRole(int $id = 0) （仅管理端）获取当前管理员的所有角色
 * @method static array getChildRoleIds(bool $self) （仅管理端）获取当前管理员的角色的所有下级角色ids
 * @method static array getChildAdminIds() （仅管理端）获取当前管理员的角色的所有下级角色 中的管理员 ids
 * @method static array isSuper() （仅管理端）当前管理员是否是超级管理员
 * 
 * @method static object register($params) （仅用户端）用户注册
 * @method static boolean resetPassword($params) （仅用户端）重置密码
 * @method static boolean modifyPassword($old_password, $password) （仅用户端）修改密码
 */
class Auth extends Facade
{
    /**
     * 获取当前Facade对应类名（或者已经绑定的容器对象标识）
     * @access protected
     * @return string
     */
    protected static function getFacadeClass()
    {
        return 'auth';
    }
}
