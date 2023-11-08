<?php

namespace app\lnksns\controller;

use app\lnksns\lib\TencentLbs;
use app\lnksns\model\CircleModel;
use app\lnksns\model\DynamicModel;
use app\lnksns\model\UserModel;
use lite\controller\Backend;
use lite\controller\traits\Crud;
use think\facade\Db;
use think\Request;

class Circle extends Backend
{
    //------ Admin ------
    use Crud;

    public function initialize()
    {
        $this->model = new CircleModel();
    }

    public function index(Request $request)
    {
        $params = $request->get();

        if (!empty($request->param('page_size'))) {       // 使用分页
            $query = $this->model;
            if ($params['name']) $query = $query->where('name', 'like', '%' . $params['name'] . '%');

            $list = $query->order('weigh', 'desc')->paginate($request->param('page_size', 10));
        } else {
            $list = $this->model->order('weigh', 'desc')->select();               // 查询全部
        }

        return success('获取成功', $list);
    }

    public function delete($id)
    {
        $pk = $this->model->getPk();

        $result = Db::transaction(function () use ($id, $pk) {
            $count = 0;
            foreach ($this->model->whereIn($pk, $id)->cursor() as $row) {
                $count += $row->delete();
                // 删除用户关注的圈子
                Db::name('free_circle_fans')->where('circle_id',$id)->delete();
            }
            return $count;
        });
        if ($result) {
            return success('删除成功', $result);
        }
        return error('删除失败');
    }

    //------ API ------
    public function get_top_circle(Request $request)
    {
        $data = $this->model->where('status', 1)
            ->field('id,name,highlight')
            ->limit(15)
            ->select()->toArray();
        foreach ($data as $k => $v) {
            $data[$k]['user'] = [];
            $data[$k]['user_count'] = Db::name('free_circle_fans')->where('circle_id', $v['id'])->count();
            if ($data[$k]['user_count']) {
                $uids = Db::name('free_circle_fans')
                    ->where('circle_id', $v['id'])
                    ->limit(3)
                    ->column('user_id');
                $data[$k]['user'] = UserModel::whereIn('id', $uids)->column('avatar');
            }
        }
        $data = array_chunk($data, 5);

        return success('成功', $data);
    }

    public function get_circle_list(Request $request)
    {
        $data = $this->model->where('status', 1)
            ->order('weigh', 'desc')
            ->field('id,avatar,name')
            ->paginate(6)->toArray();
        foreach ($data['data'] as $k => $v) {
            $data['data'][$k]['user_count'] = Db::name('free_circle_fans')->where('circle_id', $v['id'])->count();
            $data['data'][$k]['dynamic_count'] = DynamicModel::where('circle_id', $v['id'])->count();
            $dynamic = DynamicModel::where('status', 1)
                ->where('show', 1)
                ->where('circle_id', $v['id'])
                ->field('id,user_id,content')
                ->limit(2)
                ->select()->toArray();
            if (count($dynamic)) {
                foreach ($dynamic as $dk => $dv) {
                    $dynamic[$dk]['user'] = UserModel::where('id', $dv['user_id'])->field('name,avatar,career')->find();
                    $dynamic[$dk]['dynamic_comment'] = Db::name('free_dynamic_comment')->where('dynamic_id', $dv['id'])->count();
                    $dynamic[$dk]['dynamic_like'] = Db::name('free_user_like_dynamic')->where('dynamic_id', $dv['id'])->count();
                    $dynamic[$dk]['dynamic_img'] = Db::name('free_dynamic_img')->where('dynamic_id', $dv['id'])->count();
                    if ($dynamic[$dk]['dynamic_img']) {
                        $dynamic[$dk]['img'] = Db::name('free_dynamic_img')
                            ->where('dynamic_id', $dv['id'])
                            ->order('weigh', 'asc')
                            ->value('url');
                    }
                }
            }
            $data['data'][$k]['dynamic'] = $dynamic ?? [];
        }

        return success('成功', $data);
    }

    public function get_circle_details(Request $request)
    {
        $uid = $request->uid;
        $id = $request->get('id');
        $data = $this->model->where('status', 1)
            ->where('id', $id)
            ->field('id,avatar,name,intro')
            ->find();
        if ($data) {
            $data['is_follow'] = Db::name('free_circle_fans')
                ->where('user_id', $uid)
                ->where('circle_id', $data['id'])
                ->count() ? true : false;

            $data['user'] = [];
            $data['user_count'] = Db::name('free_circle_fans')->where('circle_id', $data['id'])->count();
            if ($data['user_count']) {
                $uids = Db::name('free_circle_fans')
                    ->where('circle_id', $data['id'])
                    ->limit(3)
                    ->column('user_id');
                $data['user'] = UserModel::whereIn('id', $uids)->column('avatar');
            }
            return success('成功', $data);
        } else {
            return error('未找到圈子或圈子异常！', 400);
        }
    }

    public function get_circle_dynamic(Request $request)
    {
        $uid = $request->uid;
        $id = $request->get('id');
        $type = $request->get('type', 0);
        $order = ['top' => 'desc', 'weigh' => 'desc', 'create_time' => 'desc', 'view' => 'desc'];
        if ($type == 1) $order = ['create_time' => 'desc', 'weigh' => 'desc', 'view' => 'desc'];
        $data = DynamicModel::where('status', 1)
            ->where('show', 1)
            ->where('circle_id', $id)
            ->order($order)
            ->order('create_time', 'desc')
            ->field(Dynamic::dynamic_find())
            ->paginate(6)->toArray();
        $data = Dynamic::dynamic_map($data, true, $uid);

        return success('成功', $data);
    }

    public function follow_circle(Request $request)
    {
        $uid = $request->uid;
        $id = $request->post('id');
        $is_follow = $request->post('is_follow');
        if ($is_follow) {
            Db::name('free_circle_fans')
                ->where('user_id', $uid)
                ->where('circle_id', $id)
                ->delete();
        } else {
            Db::name('free_circle_fans')->insert([
                'user_id' => $uid,
                'circle_id' => $id,
                'create_time' => time()
            ]);
        }
        return success('成功', '');
    }

    public function user_circle(Request $request)
    {
        $uid = $request->uid;
        $data = Db::name('free_circle_fans')
            ->where('user_id', $uid)
            ->order('id', 'desc')
            ->field('id,circle_id')
            ->select()->toArray();
        if (count($data)) {
            foreach ($data as $k => $v) {
                $circle = $this->model->where('status', 1)
                    ->where('id', $v['circle_id'])
                    ->field('name,avatar')
                    ->find();
                $circle['is_new'] = DynamicModel::where('circle_id', $v['circle_id'])
                    ->where('create_time', '>', strtotime(date('Y-m-d')))
                    ->count() ? true : false;
                $data[$k]['circle'] = $circle;
            }
        }

        return success('成功', $data);
    }

    public function circle_fans(Request $request)
    {
        $id = $request->get('id');
        $data = Db::name('free_circle_fans')->where('circle_id', $id)
            ->field('id,user_id')
            ->paginate(40)->toArray();
        if (count($data['data'])) {
            foreach ($data['data'] as $k => $v) {
                $data['data'][$k]['user'] = UserModel::where('id', $v['user_id'])
                    ->field('name,avatar')
                    ->find();
            }
        }

        return success('成功', $data);
    }

    public function dynamic_circle(Request $request)
    {
        $data = $this->model->where('status', 1)
            ->order('weigh', 'desc')
            ->field('id,name,avatar')
            ->select();

        return success('成功', $data);
    }

}
