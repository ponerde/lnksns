<?php
namespace app\index\subscribe;

use think\facade\Log;

class User
{
    public function onUserLogin($user)
    {
        Log::info(1222);
        // UserLogin事件响应处理
    }

    public function onUserLogout($user)
    {
        // UserLogout事件响应处理
    }
}