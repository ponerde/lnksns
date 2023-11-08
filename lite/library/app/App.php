<?php

declare(strict_types=1);

namespace lite\library\app;

use think\facade\Filesystem;
use lite\exception\LiteException;
use lite\facade\Auth;
use lite\facade\Client;
use lite\model\Config;
use app\admin\model\FileModel;
use app\admin\model\FileGroupModel;

class App
{
    public function getConfigs()
    {
        $configs = [];
        foreach (get_apps() as $k=>$v) {
            $file = base_path() . $v . '/config.json';
            
            if (file_exists($file)) {
                $configJson = file_get_contents($file);
                $config = json_decode($configJson,true);
                $config['aa'] = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http" . "://" . $_SERVER['HTTP_HOST'];
 
                
                $configs[] = $config;
                
            }
        }
        return $configs;
    }
}