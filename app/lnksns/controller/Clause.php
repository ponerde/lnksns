<?php

namespace app\lnksns\controller;

use app\lnksns\lib\TencentLbs;
use app\lnksns\model\ClauseModel;
use lite\controller\Backend;
use lite\controller\traits\Crud;
use think\facade\Db;
use think\Request;

class Clause extends Backend
{
    //------ Admin ------
    use Crud;

    public function initialize()
    {
        $this->model = new ClauseModel();
    }

    public function index(Request $request)
    {
        $params = $request->get();

        if (!empty($request->param('page_size'))) {       // 使用分页
            $query = $this->model;
            if ($params['title']) $query = $query->where('title', 'like', '%' . $params['title'] . '%');

            $list = $query->paginate($request->param('page_size', 10));
        } else {
            $list = $this->model->select();               // 查询全部
        }

        return success('获取成功', $list);
    }

    //------ API ------
    public function details(Request $request)
    {
        $id = $request->get('id');
        $data = Db::name('free_clause')->where('id', $id)->where('status',1)->field('id,title,content')->find();
        return success('success',$data);
    }



}
