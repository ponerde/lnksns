<?php

declare(strict_types=1);

namespace app\admin\controller\setting;

use app\admin\service\ConfigService;
use app\Request;
use lite\controller\Backend;
use think\facade\Cache;

class System extends Backend
{
    public function detail()
    {
        $data = [
            'name' => ConfigService::get('website','name','LnkAdmin'),
            'logo' => ConfigService::get('website','logo',''),
            'dashboard_component' => ConfigService::get('website','dashboard_component','admin/views/dashboard/index'),
        ];
        return success($data);
    }

    public function update(Request $request)
    {
        
        ConfigService::set('website','name',$request->post('name',''));
        ConfigService::set('website','dashboard_component',$request->post('dashboard_component','admin/views/dashboard/index'));
        return success();
    }
}