<?php
declare(strict_types=1);

namespace app\lnksns\controller;


use lite\controller\Backend;
use lite\service\UploadService;
use think\Request;

class Upload extends Backend
{
    /**
     * 上传图片
     */
    public function image(Request $request)
    {
        
        $file = UploadService::image($request->post('group_id',0));
        return success('上传成功',$file);
    }
}