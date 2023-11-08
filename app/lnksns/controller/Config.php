<?php

declare(strict_types=1);

namespace app\lnksns\controller;

use app\Request;
use lite\controller\Backend;
use lite\service\ConfigService;

class Config extends Backend
{

    public function detail()
    {
        $data = [
            'lnk_app_id' => ConfigService::get('lnksns', 'lnk_app_id',''),
            'lnk_app_secret' => ConfigService::get('lnksns', 'lnk_app_secret',''),
            'lnk_gw' => ConfigService::get('lnksns', 'lnk_gw','https://lnksns.lnksns.com'),
            'lnk_gzh' => ConfigService::get('lnksns', 'lnk_gzh','https://mp.weixin.qq.com/s?__biz=MzkzNDIxNTE0NA==&mid=2247483756&idx=1&sn=89db153378f11e2f2f15fa8e47f3a5d5&chksm=c241edebf53664fdccf4784a28742af76da5c4d87f89e9ce643533da24fcc867ee2f86206288#rd'),
            'lnk_tx_kay' => ConfigService::get('lnksns', 'lnk_tx_kay',''),
            'lnk_nl' => ConfigService::get('lnksns', 'lnk_nl',[]),
            'lnk_zy' => ConfigService::get('lnksns', 'lnk_zy',[]),
        ];
        return success($data);
    }

    public function update(Request $request)
    {

        ConfigService::set('lnksns','lnk_app_id',$request->post('lnk_app_id',''));
        ConfigService::set('lnksns','lnk_app_secret',$request->post('lnk_app_secret',''));
        ConfigService::set('lnksns','lnk_gw',$request->post('lnk_gw',''));
        ConfigService::set('lnksns','lnk_gzh',$request->post('lnk_gzh',''));
        ConfigService::set('lnksns','lnk_tx_kay',$request->post('lnk_tx_kay',''));
        ConfigService::set('lnksns','lnk_nl',$request->post('lnk_nl',[]));
        ConfigService::set('lnksns','lnk_zy',$request->post('lnk_zy',[]));
        return success();
    }

}
