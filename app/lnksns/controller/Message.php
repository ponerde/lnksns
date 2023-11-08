<?php

namespace app\lnksns\controller;

use app\lnksns\lib\TencentLbs;
use app\lnksns\model\MessageModel;
use app\lnksns\model\UserModel;
use lite\controller\Backend;
use lite\controller\traits\Crud;
use think\facade\Db;
use think\Request;

class Message extends Backend
{
    //------ Admin ------
    use Crud;

    public function initialize()
    {
        $this->model = new MessageModel();
    }

    public function index(Request $request)
    {
        $params = $request->get();

        if (!empty($request->param('page_size'))) {       // 使用分页
            $query = $this->model->with('user');
            if ($params['content']) $query = $query->where('content', 'like', '%' . $params['content'] . '%');

            $list = $query->order('id', 'desc')->paginate($request->param('page_size', 10));
        } else {
            $list = $this->model->order('id', 'desc')->select();               // 查询全部
        }

        return success('获取成功', $list);
    }

    //------ API ------
    public function user_message_count(Request $request)
    {
        $uid = $request->uid;
        $data = $this->model->where('user_id', $uid)->where('read', 0)->count();
        return success('success', $data);
    }

    public function get_message(Request $request)
    {
        $uid = $request->uid;
        $type = $request->get('type');
        $data = $this->model->where('user_id', $uid)
            ->where('type', $type)
            ->order('read','asc')
            ->order('id','desc')
            ->field('id,launch_id,title,content,img,avatar_url,content_url,read,create_time')
            ->paginate(8)->toArray();
        if (count($data['data'])) {
            foreach ($data['data'] as $k => $v) {
                if ($v['launch_id']) {
                    $data['data'][$k]['user'] = UserModel::where('id', $v['launch_id'])->field('name,avatar')->find();
                }
                $data['data'][$k]['create_time'] = Dynamic::time_text($v['create_time']);
            }
        }

        return success('success', $data);
    }

    public function get_message_count(Request $request)
    {
        $uid = $request->uid;
        $data[0] = $this->model->where('user_id', $uid)->where('type', 0)->where('read', 0)->count();
        $data[1] = $this->model->where('user_id', $uid)->where('type', 1)->where('read', 0)->count();
        $data[2] = $this->model->where('user_id', $uid)->where('type', 2)->where('read', 0)->count();

        return success('success', $data);
    }

    public function read_message(Request $request)
    {
        $uid = $request->uid;
        $type = $request->post('type');
        $id = $request->post('id', 0);
        $condition[] = ['user_id', '=', $uid];
        if ($id) $condition[] = ['id', '=', $id];
        $this->model->where('type', $type)
            ->where($condition)
            ->update(['read' => 1]);

        return success('success', '');
    }

}
