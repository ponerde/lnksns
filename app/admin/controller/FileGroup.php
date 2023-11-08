<?php

declare(strict_types=1);

namespace app\admin\controller;

use think\Request;
use lite\controller\Backend;
use lite\model\file\FileModel;
use lite\model\file\FileGroupModel;
use lite\facade\Uploader;
use lite\controller\traits\Crud;
use think\Response;

class FileGroup extends Backend
{
    use Crud;

    protected function initialize()
    {
        
        $this->model = new FileGroupModel;
    }

     /**
     * 查看
     *
     * @return Response
     */
    public function index(Request $request): Response
    {
        if (!empty($request->param('page_size'))) {       // 使用分页
            $list = $this->model->liteFilter()->paginate($request->param('page_size', 10));
        } else {
            $list = $this->model->liteFilter()->select();               // 查询全部
        }
        
        return success('获取成功', $list);
    }

}