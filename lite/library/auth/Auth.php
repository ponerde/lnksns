<?php
declare(strict_types=1);

namespace lite\library\auth;

use lite\exception\LiteException;
use lite\model\user\UserModel;
use lite\model\auth\AdminModel;

class Auth
{
    protected $guard = [];
    protected $auth = null;
    protected $driver = null;
    protected $model = null;
    protected $provider = null;

    protected $guards = [
        'web' => [
            'driver' => 'session',
            'provider' => 'user',
            'model' => \lite\model\user\UserModel::class,
        ],
        'user' => [
            'driver' => 'jwt',
            'provider' => 'user',
            'model' => \lite\model\user\UserModel::class,
        ],
        'admin' => [
            'driver' => 'jwt',
            'provider' => 'admin',
            'model' => \lite\model\auth\AdminModel::class,
        ],
    ];

    public function __construct() {
        
    }

    public function guard($guard = null)
    {
        return $this->resolve($guard);
    }



    /**
     * 获取当前 guard
     *
     * @return void
     */
    public function resolve($guard)
    {
        $this->model = $this->guards[$guard]['model'];

        if (!isset($this->guard[$guard]) || is_null($this->guard[$guard])) {
            $guard_class = "\\lite\\library\\auth\\guard\\" . ucfirst($this->guards[$guard]['driver']) . 'Guard';
            $this->guard[$guard] = new $guard_class($this->guards[$guard], $guard);
        }

        return $this->guard[$guard];
    }
}
