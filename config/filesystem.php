<?php

return [
    // 默认磁盘
    'default' => env('filesystem.driver', 'public'),
     // 上传文件大小限制
     'filesize' => '10',       // 单位 M
     // 上传图片大小限制
     'imagesize' => '10',       // 单位 M
     // 允许的后缀名
     'extensions' => 'png,jpeg,mp4,jpg',       //
    // 磁盘列表
    'disks'   => [
        'local'  => [
            'type' => 'local',
            'root' => app()->getRuntimePath() . 'storage',
        ],
        'public' => [
            // 磁盘类型
            'type'       => 'local',
            // 磁盘路径
            'root'       => app()->getRootPath() . 'public/storage',
            // 磁盘路径对应的外部URL路径
            'url'        => 'http://127.0.0.1:8000',
            // 可见性
            'visibility' => 'public',
        ],
        // 阿里云oss
        'aliyun' => [
            'type'         => 'aliyun',
            'accessId'     => '',
            'accessSecret' => '',
            'bucket'       => '',
            'endpoint'     => '',
            'url'          => '',//不要斜杠结尾，此处为URL地址域名。
            'root'         => '/storage',
        ],
        // 七牛云
        'qiniu'  => [
            'type'      => 'qiniu',
            'accessKey' => '******',
            'secretKey' => '******',
            'bucket'    => 'bucket',
            'url'       => '',//不要斜杠结尾，此处为URL地址域名。
            'root'         => '/storage',
        ],
        // 腾讯cos
        'qcloud' => [
            'type'       => 'qcloud',
            'region'      => '***', //bucket 所属区域 英文
            'appId'      => '***', // 域名中数字部分
            'secretId'   => '***',
            'secretKey'  => '***',
            'bucket'          => '***',
            'timeout'         => 60,
            'connect_timeout' => 60,
            'cdn'             => '您的 CDN 域名',
            'scheme'          => 'https',
            'read_from_cdn'   => false,
            'root'         => '/storage',
        ]
    ],
];
