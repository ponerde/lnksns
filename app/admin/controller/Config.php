<?php

declare(strict_types=1);

namespace app\admin\controller;

use lite\controller\Backend;
use app\admin\model\ConfigModel;
use lite\controller\traits\Crud;
use lite\exception\LiteException;
use lite\service\ConfigService;
use lite\service\FileService;
use think\facade\Db;

class Config extends Backend
{
    use Crud;

    public function initialize()
    {
        $this->model = new ConfigModel();
    }


    /**
     * 后台初始化配置
     */
    public function initConfig()
    {
       
        $data = [
            // 文件域名
            'oss_domain' => FileService::getFileUrl(),
            // 网站名称
            'web_name' => ConfigService::get('website', 'name'),
            // 网站图标
            'web_favicon' => FileService::getFileUrl(ConfigService::get('website', 'web_favicon','')),
            // 网站logo
            'web_logo' => FileService::getFileUrl(ConfigService::get('website', 'web_logo','')),
            // 登录页
            'login_image' => FileService::getFileUrl(ConfigService::get('website', 'login_image','')),
            // 版权信息
            'copyright_config' => ConfigService::get('copyright', 'config', []),
            // 控制台视图
            'dashboard_component' => ConfigService::get('website','dashboard_component','admin/views/index/index'),
            // JWT
            'jwt' => [
                //JWT time to live
                'ttl'         => config('jwt.ttl'),
                //Refresh time to live
                'refresh_ttl' => config('jwt.refresh_ttl'),
            ],
        ];
        return success($data);
    }


   

   

  
}
