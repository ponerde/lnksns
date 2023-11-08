<?php

declare(strict_types=1);

namespace lite\facade;

use think\Facade;
use lite\library\storage\Storage as StorageDriver;


class Storage extends Facade
{
    /**
     * 获取当前Facade对应类名（或者已经绑定的容器对象标识）
     * @access protected
     * @return string
     */
    protected static function getFacadeClass()
    {
        return 'storage';
    }
}
