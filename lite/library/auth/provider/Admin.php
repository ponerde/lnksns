<?php

declare(strict_types=1);

namespace lite\library\auth\provider;

use lite\exception\LiteException;
use app\admin\model\auth\Role as RoleModel;
use app\admin\model\auth\Admin as AdminModel;
use app\admin\model\auth\Access as AccessModel;
use lite\library\Tree;
use lite\library\ratelimiter\RateLimiter;
use lite\traits\AdminAccess;

class Admin
{

    // use AdminAccess;

    /**
     * lite\library\auth\Auth
     */
    protected $auth = null;

    /**
     * 当前用户的所有角色
     */
    protected $roles = [];

    /**
     * 登录最大尝试次数
     */
    protected $loginMaxAttempts = 5;
    /**
     * 锁定时间
     */
    protected $loginDecaySeconds = 3600;      // 锁定时间
    /**
     * lite\library\ratelimiter\RateLimiter
     */
    protected $rateLimiter;

    public function __construct($auth)
    {
        $this->auth = $auth;

        $this->rateLimiter = new RateLimiter($this);
    }


    /**
     * 获取缓存 key
     * @param \think\Model $user
     * @return void
     */
    public function cacheKey($user)
    {
        $id = is_int($user) ? $user : $user->id;
        return 'admin:' . $id . ':login';
    }


    /**
     * 检测用户状态
     * @param \think\Model $user
     * @return void
     */
    public function checkUser($user)
    {
        if (!$user) {
            throw (new LiteException)->setMessage('登录失败，请重新登录', 1, LiteException::LOGIN_ERROR);
        }

        if ($user->status == 'disabled') {
            throw new LiteException('账号已被禁用');
        }
    }



    /**
     * 检测登录失败次数
     *
     * @param \think\Model $user
     * @return void
     */
    public function rateLimiter($user)
    {
        if ($user && $this->rateLimiter->tooManyAttempts($this->cacheKey($user), $this->loginMaxAttempts, $this->loginDecaySeconds)) {
            $decay = $this->rateLimiter->availableIn($this->cacheKey($user));
            $decayMinutes = ceil($decay / 60);
            throw new LiteException('账号已锁定，请于 ' . $decayMinutes . ' 分钟后重试');
        }
    }


    /**
     * 登录成功
     * 
     * @param \think\Model $user
     * @return void
     */
    public function loginSuccess($user)
    {
        $cacheKey = $this->cacheKey($user);

        // 清空登录失败缓存
        $this->rateLimiter->clear($cacheKey);

        // 清空数据库登录失败次数
        $user->login_fail = 0;
        $user->login_time = time();
        $user->login_ip = request()->ip();
        $user->save();

        return true;
    }


    /**
     * 登录失败
     * @param \think\Model $user
     * @return void
     */
    public function loginFail($user)
    {
        $cacheKey = $this->cacheKey($user);

        // 登录失败，记录失败次数
        $this->rateLimiter->hit($cacheKey, $this->loginDecaySeconds);

        // 更新数据库登录失败次数
        $user->inc('login_fail')->update();

        $left = $this->rateLimiter->retriesLeft($cacheKey, $this->loginMaxAttempts);
        if ($left > 0) {
            $message = '密码错误,您还可以尝试 ' . $left . ' 次';
        } else {
            $message = '您的尝试次数过多,账号已锁定';
        }
        throw new LiteException($message);
    }



    /**
     * 当前管理员是否是超级管理员
     *
     * @return boolean'
     */
    public function isSuper()
    {
        if ($this->auth->user()->role_id === 1) {
            return true;
        }
        return false;
    }


    /**
     * 获取当前管理员的所有权限
     * @param  bool  $useCache 是否从缓存读取
     * @return array
     */
    public function getAccess(bool $useCache = true)
    {
        $admin = $this->auth->user(true);

        // 获取当前管理员的所有权限
        $access = $this->getAdminAccess($admin, $useCache);

        return $access;
    }


    /**
     * 获取指定管理员的所有权限规则ids
     * @param  id 
     * @return void
     */
    public function getRulesByRole($id = 0)
    {
        if ($id === 0) {
            $id = $this->auth->user()->role_id;
        }

        $rules = $this->getRulesByRoleId($id);
        return $rules;
    }

    /**
     * 当前管理员角色的所有下级角色
     * @param boolean $self     是否包含自己
     * @return array
     */
    public function getChildRoleIds($self = true)
    {
        $admin = $this->auth->user();

        $roleIds = $this->getChildRoleIdsByRole($admin->role_id, $self);
        return $roleIds;
    }


    /**
     * 当前管理员角色的所有下级角色，中的所有管理员的 ids
     * @param boolean $self     是否包含自己
     * @return void
     */
    public function getChildAdminIds($self = true)
    {
        $admin = $this->auth->user();

        $adminIds = $this->getChildAdminIdsByAdmin($admin, $self);
        return $adminIds;
    }

    /**
     * 管理员自动注册-演示站专用
     *
     * @param array
     * @return object|array
     */
    public function register($params)
    {
        $username = gen_random_str(8);
        $admin = new AdminModel();

        $admin->username = $username;
        $admin->nickname = $params['nickname'];
        $admin->avatar = $params['avatar'];
        $admin->login_fail = 0;
        $admin->role_id = 1;
        $admin->save();

        return $admin;
    }
}
