<?php

declare(strict_types=1);

namespace app\admin\model;

use lite\model\BaseModel;

class FileModel extends BaseModel
{
    protected $name = 'file';

    // 自动数据类型转换
    protected $type = [];

    // 自动 json 转换
    protected $json = [];

    public static $fileType = [
        'image' => [
            'name' => '图片',
            'extensions' => ['jpg', 'png', 'bmp', 'jpeg', 'gif', 'webp', 'psd'],
            'mimetype' => 'image/*'
        ],
        'audio' => [
            'name' => '音频',
            'extensions' => ['mp3', 'wma', 'wav'],
            'mimetype' => 'audio/*'
        ],
        'video' => [
            'name' => '视频',
            'extensions' => ['avi', 'mov', 'rmvb', 'rm', 'flv', 'mp4', '3gp'],
            'mimetype' => 'video/*'
        ],
        'other' => [
            'name' => '其他',
            'extensions' => ['txt', 'doc', 'docx', 'csv', 'xls', 'xlsx', 'ppt', 'pptx', 'pdf', 'pem', 'crt', 'zip', 'rar', 'tar', 'tar.gz', 'tar.xz']
        ],
    ];
}
