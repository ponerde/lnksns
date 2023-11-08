<?php

declare(strict_types=1);

namespace lite\controller;

use lite\exception\LiteException;
use lite\facade\Auth;

class Api
{
    /**
     * 当前用户 auth 实例
     *
     * @return Auth
     */
    public function auth()
    {
        return Auth::guard('user');
    }
}
