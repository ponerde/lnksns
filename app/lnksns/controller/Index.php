<?php
declare(strict_types=1);

namespace app\lnksns\controller;


use lite\controller\Backend;
use lite\service\ConfigService;
use think\facade\Db;
use think\Request;

class Index extends Backend
{

    public function config()
    {
        $data = [
            'lnk_gw' => ConfigService::get('lnksns', 'lnk_gw', ''),
            'lnk_gzh' => ConfigService::get('lnksns', 'lnk_gzh', ''),
            'lnk_nl' => ConfigService::get('lnksns', 'lnk_nl', []),
            'lnk_zy' => ConfigService::get('lnksns', 'lnk_zy', []),
        ];
        return success('success',$data);
    }
}