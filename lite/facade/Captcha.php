<?php

declare(strict_types=1);

namespace lite\facade;

use think\Facade;
use lite\library\captcha\Captcha as CaptchaManager;

/**
 * @see CaptchaManager
 * @method static UploaderManager driver(string $driver) 设置驱动
 * @method static think\Response captcha(string $driver) 生成验证码或者参数
 * @method static void validateExtend() 注册验证码验证规则
 * @method static boolean check(array $params) 手动验证验证码
 */
class Captcha extends Facade
{
    /**
     * 获取当前Facade对应类名（或者已经绑定的容器对象标识）
     * @access protected
     * @return string
     */
    protected static function getFacadeClass()
    {
        return 'captcha';
    }
}
