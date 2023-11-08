<?php

declare(strict_types=1);

namespace lite\facade;

use think\Facade;
use lite\library\sms\Sms as SmsManager;

/**
 * @see SmsManager
 * @method static array send(string $mobile, string $event) 发送短信验证码
 * @method static array sendNotice(string $mobile, string $event) 发送短信通知
 * @method static array check(string $mobile, string $event, $code, bool $exception = true) 验证短信验证码
 * 
 */
class Sms extends Facade
{
    /**
     * 获取当前Facade对应类名（或者已经绑定的容器对象标识）
     * @access protected
     * @return string
     */
    protected static function getFacadeClass()
    {
        return 'sms';
    }
}
