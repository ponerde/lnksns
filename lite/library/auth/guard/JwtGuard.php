<?php

declare(strict_types=1);

namespace lite\library\auth\guard;

use xptech\jwt\facade\JwtAuth;
use lite\exception\LiteException;

class JwtGuard
{
    /**
     * 当前登录用户
     */
    protected $user = null;

    protected $token = null;

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

        if (JwtAuth::token() && ($payload = JwtAuth::auth()) && $payload['type'] == $this->provider) {
            $this->user = $this->model->find($payload['uid']);
        }
        if (is_null($this->user) && $failException) {
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
    public function id($failException = false)
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
       
        if (empty($credentials['username']) || empty($credentials['password'])) {
            throw new LiteException('请输入正确的账号或密码');
        }
        // 查询用户信息
    
        $user = $this->model->where(function ($query) use ( $credentials) {
                $query->whereOr('username', $credentials['username']);  
        })->find();

        if (!$user) {
            throw new LiteException('您的账号或密码不正确');  // 实际没有检测密码
        }

        // 失败次数尝试
        $this->getProvider()->rateLimiter($user);

        if ($this->model->encryptPassword($credentials['password'], $user->salt) == $user->password) {
            $this->login($user, $remember);

            return true;
        }

        // 登录失败
        $this->getProvider()->loginFail($user);
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
        // TODO: remember没有用上
        $user = $user->isEmpty() ? null : $user;

        // 检测用户是否正常
        $this->getProvider()->checkUser($user);

        $this->user = $user;
        $this->getTokenToSession();

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
        $user = $this->model->find($id);
        if($user) {
            $this->login($user, $remember);
            return $this;
        }
        return false;
    }


    /**
     * 退出登录，删除token
     *
     * @return void
     */
    public function logout()
    {
        JwtAuth::invalidate(JwtAuth::token());

        $this->user = null;
    }


    private function getTokenToSession()
    {
        $token = JwtAuth::builder([
            'type' => $this->provider,
            'uid' => $this->id(),
        ]);
        $this->token = $token;
        session('header_authorization', $token);        // 将新的 token 存入 session
    }

    /**
     * 获取已登录用户token
     *
     * @return void
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * 实例化 provider
     *
     * @return \lite\library\auth\provider\Admin|\lite\library\auth\provider\User
     */
    public function getProvider()
    {
        if (!$this->providerClass) {
            $class = "\\lite\\library\\auth\\provider\\" . ucfirst($this->provider);

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
