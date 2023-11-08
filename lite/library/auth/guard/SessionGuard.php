<?php

declare(strict_types=1);

namespace lite\library\auth\guard;

use lite\exception\LiteException;
use lite\library\auth\traits\Remember;
use think\facade\Cookie;

class SessionGuard
{
    use Remember;

    /**
     * 当前登录用户
     */
    protected $user = null;

    protected $guard = null;

    protected $providerClass = null;

    public function __construct($currentGuard, $guard)
    {
        $this->guard = $guard;
        $this->provider = $currentGuard['provider'];
        $this->model = new $currentGuard['model'];
    }


    /**
     * 获取用户信息，自动从 token 中解析用户信息
     *
     * @return object
     */
    public function user(bool $failException = false)
    {
        if ($this->user !== null) {
            return $this->user;
        }

        $id = session($this->getName());

        if (!is_null($id)) {
            $this->user = $this->model->find($id);
        }

        if (is_null($this->user) && !is_null($recaller = $this->recaller())) {
            $this->user = $this->userFromRecaller($recaller);

            if ($this->user) {
                $this->login($this->user, true);
            }
        }

        if(is_null($this->user) && $failException) {
            throw new LiteException('请登录后再继续操作');
        } else if ($this->user) {
            // 检测用户是否正常
            $this->getProvider()->checkUser($this->user);
        }

        return $this->user;
    }


    /**
     * 获取用户的 id
     *
     * @return void
     */
    public function id(bool $failException = false)
    {
        $user = $this->user($failException);

        return $user ? $user->id : null;
    }

    /**
     * Attempt to authenticate a user using the given credentials.
     *
     * @param  array  $credentials
     * @param  bool   $remember
     * @return bool
     */
    public function attempt(array $credentials = [], $remember = false)
    {
        // 查询管理员信息
        $accountname = $this->model->accountname();
        $user = $this->model->where(function ($query) use ($accountname, $credentials) {
            $accountname = is_string($accountname) ? [$accountname] : $accountname;
            foreach ($accountname as $account) {
                $query->whereOr($account, $credentials['account']);
            }
        })->find();

        if (!$user) {
            throw new SheepException('登录失败，请重试');
        }

        // 失败次所尝试
        $this->getProvider()->rateLimiter($user);

        if ($this->model->encryptPassword($credentials['password'], $user->salt) == $user->password) {
            $this->login($user, $remember);

            return true;
        }

        // 登录失败
        $this->getProvider()->loginFail($user);
    }

    /**
     * Log a user into the application without sessions or cookies.
     *
     * @param  array  $credentials
     * @return bool
     */
    public function once(array $credentials = [])
    {
        // 等待补充
        return false;
    }

    /**
     * 通过用户模型实例登录
     *
     * @param  \think\Model  $user
     * @param  bool  $remember
     * @return void
     */
    public function login(\think\Model $user, $remember = false)
    {
        $user = $user->isEmpty() ? null : $user;

        // 检测用户是否正常
        $this->getProvider()->checkUser($user);

        $this->user = $user;

        // session 缓存
        session($this->getName(), $user->id);

        if ($remember) {
            $this->ensureRememberTokenIsSet($user);

            $this->responseCookie($user);
        }

        // 登录成功
        $this->getProvider()->loginSuccess($user);

        return $this;
    }

    /**
     * 通过用户 id 登录
     *
     * @param  mixed  $id
     * @param  bool   $remember
     * @return \Illuminate\Contracts\Auth\Authenticatable
     */
    public function loginUsingId($id, $remember = false)
    {
        $user = $this->model->findOrFail($id);

        $this->login($user, $remember);
        return $this;
    }

    /**
     * Log the given user ID into the application without sessions or cookies.
     *
     * @param  mixed  $id
     * @return bool
     */
    public function onceUsingId($id)
    {
        // $this->getTokenToSession();
        // 等待补充
        return false;
    }

    public function getToken()
    {
        return $this->getName();
    }

    /**
     * 退出登录，删除token
     *
     * @return void
     */
    public function logout()
    {
        // session 缓存
        session($this->getName(), null);

        // 删除cookie
        Cookie::delete($this->getRecallerName());

        if (!is_null($this->user) && !empty($this->user->getRememberToken())) {
            $this->cycleRememberToken($this->user);
        }


        $this->user = null;
    }



    /**
     * 返回 session key
     *
     * @return void
     */
    public function getName()
    {
        return $this->provider . ':login:' . 'user_id';
    }



    /**
     * 实例化 provider
     *
     * @return \sheep\library\auth\provider\Admin|\sheep\library\auth\provider\User
     */
    public function getProvider()
    {
        if (!$this->providerClass) {
            $class = "\\sheep\\library\\auth\\provider\\" . ucfirst($this->provider);

            $this->providerClass = new $class($this);
        }

        return $this->providerClass;
    }


    /**
     * 转向 provider
     *
     * @return \sheep\library\auth\provider\Admin|\sheep\library\auth\provider\User
     */
    public function __call($funcname, $arguments)
    {
        return $this->getProvider()->{$funcname}(...$arguments);
    }
}
