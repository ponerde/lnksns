<?php

use app\AppService;
use lite\service\LiteService;

// 系统服务定义文件
// 服务在完成全局初始化之后执行
return [
    AppService::class,
    LiteService::class,
];
