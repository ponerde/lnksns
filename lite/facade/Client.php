<?php

declare(strict_types=1);

namespace lite\facade;

use think\Facade;
use lite\library\Client as ClientLibrary;

/**
 * @see ClientLibrary
 * @method static mixed httpGet(string $url, array $query) get请求
 * @method static mixed httpPost(string $url, array $data) post请求
 * @method static mixed httpPostJson(string $url, array $data, array $query) postJson请求
 */
class Client extends Facade
{
    /**
     * 获取当前Facade对应类名（或者已经绑定的容器对象标识）
     * @access protected
     * @return string
     */
    protected static function getFacadeClass()
    {
        return 'client';
    }
}
