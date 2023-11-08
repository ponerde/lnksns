<?php

namespace app\lnksns\controller;

use app\lnksns\lib\TencentLbs;
use app\lnksns\model\CommentModel;
use app\lnksns\model\DynamicModel;
use app\lnksns\model\MessageModel;
use app\lnksns\model\UserModel;
use lite\controller\Backend;
use lite\controller\traits\Crud;
use think\facade\Db;
use think\Request;

class Dynamic extends Backend
{
    //------ Admin ------
    use Crud;

    public function initialize()
    {
        $this->model = new DynamicModel();
    }

    public function index(Request $request)
    {
        $params = $request->get();

        if (!empty($request->param('page_size'))) {       // 使用分页
            $query = $this->model->with('user')->append(['like_count', 'comment_count', 'img_count'])
                ->withAttr('like_count', function ($value, $data) {
                    return Db::name('free_user_like_dynamic')->where('dynamic_id', $data['id'])->count();
                })->withAttr('comment_count', function ($value, $data) {
                    return CommentModel::where('status', '<>', 0)->where('dynamic_id', $data['id'])->count();
                })->withAttr('img_count', function ($value, $data) {
                    return Db::name('free_dynamic_img')->where('dynamic_id', $data['id'])->count();
                });
            if ($params['content']) $query = $query->where('content', 'like', '%' . $params['content'] . '%');

            $list = $query->order(['status' => 'asc', 'top' => 'desc', 'id' => 'desc', 'weigh' => 'desc', 'view' => 'desc'])->paginate($request->param('page_size', 10));
        } else {
            $list = $this->model->order('status', 'asc')->order('weigh', 'desc')->order('id', 'desc')->select();               // 查询全部
        }

        return success('获取成功', $list);
    }

    public function read($id)
    {
        $detail = $this->model->findOrFail($id);
        $detail->imgs = Db::name('free_dynamic_img')
            ->where('dynamic_id', $id)
            ->order('weigh', 'desc')
            ->field('url,wide,high')
            ->select();
        return success('获取成功', $detail);
    }

    public function delete($id)
    {
        $pk = $this->model->getPk();

        $result = Db::transaction(function () use ($id, $pk) {
            $count = 0;
            foreach ($this->model->whereIn($pk, $id)->cursor() as $row) {
                $count += $row->delete();
                // 删除用户关注的动态
                Db::name('free_user_like_dynamic')->where('dynamic_id', $id)->delete();
            }
            return $count;
        });
        if ($result) {
            return success('删除成功', $result);
        }
        return error('删除失败');
    }

    //------ API ------
    public function save_dynamic(Request $request)
    {
        $uid = $request->uid;
        $params = $request->post();
        $id = $params['id'];
        $model = ['user_id' => $uid, 'content' => $params['content'], 'circle_id' => $params['circle_id'], 'circle_name' => $params['circle']];
        if (!empty($params['adds'])) {
            $model += ['adds' => $params['adds']['name']];
            $model += ['lat' => $params['adds']['latitude']];
            $model += ['lng' => $params['adds']['longitude']];
        }
        if ($id) {
            $model += ['update_time' => time()];
            $this->model->where('id', $id)->update($model);
            Db::name('free_dynamic_img')->where('dynamic_id', $id)->delete();
        } else {
            $model += ['status' => 0];
            $model += ['create_time' => time()];
            $ip = $request->ip();
            $model += ['ip' => $ip];
            $res = TencentLbs::ip($ip);
            if ($res['status'] == 0) {
                if (empty($params['adds'])) {
                    $model += ['lat' => $res['result']['location']['lat']];
                    $model += ['lng' => $res['result']['location']['lng']];
                }
                $model += ['country' => $res['result']['ad_info']['nation']];
                $model += ['province' => $res['result']['ad_info']['province']];
                $model += ['city' => $res['result']['ad_info']['city']];
                $model += ['district' => $res['result']['ad_info']['district']];
            }

            $id = $this->model->insertGetId($model);
        }
        if (!empty($params['imgs'])) {
            $img_model = [];
            foreach ($params['imgs'] as $k => $v) {
                $img_model[$k]['url'] = $v['url'];
                $img_model[$k]['wide'] = $v['wide'];
                $img_model[$k]['high'] = $v['high'];
                $img_model[$k]['weigh'] = $k;
                $img_model[$k]['dynamic_id'] = $id;
                $img_model[$k]['user_id'] = $uid;
                $img_model[$k]['status'] = 1;
            }
            Db::name('free_dynamic_img')->insertAll($img_model);
        }

        return success('发布成功 🎉', '');
    }

    public function get_dynamic_info(Request $request)
    {
        $id = $request->get('id');
        $data = $this->model
            ->where('id', $id)
            ->field(self::dynamic_find())
            ->find();
        $data->imgs = Db::name('free_dynamic_img')
            ->where('dynamic_id', $id)
            ->order('weigh', 'asc')
            ->field('url,wide,high')
            ->select();

        return success('成功', $data);
    }

    public function recommend_dynamic(Request $request)
    {
        $uid = $request->uid;
        $type = $request->get('type', 1);
        $keyword = $request->get('keyword');
        $order = 'weigh';
        $condition[] = ['status', '=', 1];
        $condition[] = ['show', '=', 1];
        if ($keyword) $condition[] = ['content|circle_name', 'like', '%' . $keyword . '%'];
        if ($type == 0) {
            $ids = Db::name('free_user_follow')
                ->where('user_id', $uid)
                ->column('follow_user_id');
            $condition[] = ['user_id', 'in', $ids];
            $order = 'id';
        }
        $data = $this->model->where($condition)
            ->order(['top' => 'desc', 'create_time' => 'desc', $order => 'desc', 'view' => 'desc'])
            ->field(self::dynamic_find())
            ->paginate(6)->toArray();
        $data = self::dynamic_map($data, true, $uid);

        return success('成功', $data);
    }

    public function del_dynamic(Request $request)
    {
        $id = $request->post('id');
        $this->model->where('id', $id)->update(['status' => 2]);

        return success('成功', '');
    }

    public function dynamic_details(Request $request)
    {
        $uid = $request->uid;
        $id = $request->get('id');
        $data = $this->model->where('id', $id)
            ->whereIn('status', [0, 1])
            ->field(self::dynamic_find())
            ->find();
        if (!$data) return error('未找到动态或动态异常', 600201);
        $this->model->where('id', $id)->update([
            'weigh' => Db::raw('weigh+1'),
            'view' => Db::raw('view+1'),
        ]);
        $data['user'] = UserModel::where('id', $data['user_id'])
            ->field('name,avatar,career')
            ->find();
        $data['img'] = Db::name('free_dynamic_img')
            ->where('dynamic_id', $data['id'])
            ->order('weigh', 'asc')
            ->field('url,wide,high')
            ->select();
        $data['province'] = User::adds_text($data['province']);
        $data['create_time_text'] = self::time_text($data['create_time']);
        $data['comment_count'] = CommentModel::where('dynamic_id', $data['id'])->count();
        $data['like_count'] = Db::name('free_user_like_dynamic')->where('dynamic_id', $data['id'])->count();
        $data['is_like'] = Db::name('free_user_like_dynamic')
            ->where('dynamic_id', $data['id'])
            ->where('user_id', $uid)
            ->where('status', 1)
            ->count() ? true : false;

        return success('成功', $data);
    }

    public function like_dynamic(Request $request)
    {
        $uid = $request->uid;
        $id = $request->post('id');
        $duid = $request->post('duid');
        $is_like = $request->post('is_like');
        $content = $request->post('content');
        $img = $request->post('img');
        if ($is_like) {
            Db::name('free_user_like_dynamic')->insert([
                'dynamic_id' => $id,
                'user_id' => $uid,
                'status' => 1,
                'create_time' => time()
            ]);
            $count = Db::name('free_user_like_dynamic')
                ->where('dynamic_id', $id)
                ->where('user_id', $uid)
                ->count();
            if ($count > 1 && $duid != $uid) {
                $avatar_url = '/pages/user/details?id=' . $uid;
                $content_url = '/pages/dynamic/details?id=' . $uid;
                if (isset($content{40})) {
                    $content = mb_substr($content, 0, 20, "utf-8") . '...';
                }
                (new MessageModel())->send($duid, '动态获赞', $content, $uid, $img, $avatar_url, $content_url, 1);
            }
        } else {
            Db::name('free_user_like_dynamic')
                ->where('dynamic_id', $id)
                ->where('user_id', $uid)
                ->where('status', 1)
                ->update(['status' => 0]);
        }
        return success('成功', '');
    }

    public static function dynamic_map($data, $type = true, $uid = 0)
    {
        $list = $data['data'];
        foreach ($list as $k => $v) {
            $list[$k]['user'] = UserModel::where('id', $v['user_id'])
                ->field('name,avatar,career')
                ->find();
            $list[$k]['img'] = Db::name('free_dynamic_img')
                ->where('dynamic_id', $v['id'])
                ->order('weigh', 'asc')
                ->field('url,wide,high')
                ->limit(3)
                ->select();
            $list[$k]['img_count'] = Db::name('free_dynamic_img')
                ->where('dynamic_id', $v['id'])
                ->count();
            $list[$k]['province'] = User::adds_text($v['province']);
            $list[$k]['create_time'] = self::time_text($v['create_time']);
            $list[$k]['comment_count'] = CommentModel::where('status', '<>', 0)->where('dynamic_id', $v['id'])->count();
            $list[$k]['like_count'] = Db::name('free_user_like_dynamic')->where('dynamic_id', $v['id'])->count();
            if ($type) {
                if ($list[$k]['comment_count']) {
                    $comment = CommentModel::where('dynamic_id', $v['id'])
                        ->where('status', '<>', 0)
                        ->field('user_id,content,status')
                        ->find();
                    $comment['content'] = Comment::comment_status($comment['content'], $comment['status']);
                    $comment['user_name'] = UserModel::where('id', $comment['user_id'])->value('name');
                    $list[$k]['comment'] = $comment;
                }
                $list[$k]['is_like'] = Db::name('free_user_like_dynamic')
                    ->where('dynamic_id', $v['id'])
                    ->where('user_id', $uid)
                    ->where('status', 1)
                    ->count() ? true : false;
            }
        }
        $data['data'] = $list;
        return $data;
    }

    public static function dynamic_find()
    {
        return ['id,user_id,content,circle_id,circle_name,province,adds,lat,lng,create_time,view,status'];
    }

    public static function time_text($time)
    {
        if (!$time) return '未知';
        $time = strtotime($time);
        //将日期转化为时间戳
        $etime = time() - $time;
        switch ($etime) {
            case $etime <= 60:
                $result = '刚刚';
                break;
            case $etime > 60 && $etime <= 60 * 60:
                $result = floor($etime / 60) . '分钟前';
                break;
            case $etime > 60 * 60 && $etime <= 24 * 60 * 60:
                $result = date('Ymd', $time) == date('Ymd', time()) ? '今天 ' . date('H:i', $time) : '昨天 ' . date('H:i', $time);
                break;
            case $etime > 24 * 60 * 60 && $etime <= 2 * 24 * 60 * 60:
                $result = date('Ymd', $time) + 1 == date('Ymd', time()) ? '昨天 ' . date('H:i', $time) : '前天 ' . date('H:i', $time);
                break;
            case $etime > 2 * 24 * 60 * 60 && $etime <= 12 * 30 * 24 * 60 * 60:
                $result = date('Y', $time) == date('Y', time()) ? date('m/d', $time) : date('Y/m/d', $time);
                break;
            default:
                $result = date('Y/m/d', $time);
        }
        return $result;
    }
}
