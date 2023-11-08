<?php

declare(strict_types=1);

namespace lite\service;

use lite\library\app\App;
use lite\library\auth\Auth;
use lite\library\captcha\Captcha;
use lite\library\sms\Sms;
use lite\library\mail\Mail;
use think\Model;
use lite\library\Redis;
use lite\library\Client;
use lite\library\storage\Storage;

class LiteService extends \think\Service
{
   

    public $bind = [
        'auth' => Auth::class,          // 用户认证服务
        'redis' => Redis::class,        // 注册 redis 服务
        'storage' => Storage::class,  // 文件上传服务
        'mail' => Mail::class,          // 邮件服务
        'sms' => Sms::class,            // 短信服务
        'captcha' => Captcha::class,    // 验证码服务
        'client' => Client::class,      // Http请求服务
        'apps' => App::class

    ];
    /**
     * 注册自定义服务
     *
     * @return mixed
     */
    public function register()
    {
        // 全局加载其它应用服务
        $apps = glob(app()->getAppPath() . '*', GLOB_ONLYDIR);
        foreach ($apps as $app) {
            $appName = str_replace(app()->getAppPath(), "", $app);
            $appFile = $app . DIRECTORY_SEPARATOR . ucfirst($appName) . '.php';
            if (file_exists($appFile)) {
                $className = '\app\\' . $appName . '\\' . ucfirst($appName);
                $class = new $className;
                $class->boot();
            }
        }
    }

    /**
     * 执行服务
     *
     * @return mixed
     */
    public function boot()
    {
       
    }
}
