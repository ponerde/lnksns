<?php

namespace app\lnksns\controller;

use app\lnksns\lib\TencentLbs;
use app\lnksns\model\CommentModel;
use app\lnksns\model\MessageModel;
use app\lnksns\model\UserModel;
use lite\controller\Backend;
use lite\controller\traits\Crud;
use think\facade\Db;
use think\Request;

class Comment extends Backend
{
    //------ Admin ------
    use Crud;

    public function initialize()
    {
        $this->model = new CommentModel();
    }

    public function index(Request $request)
    {
        $params = $request->get();

        if (!empty($request->param('page_size'))) {       // 使用分页
            $query = $this->model->with('user');
            if ($params['content']) $query = $query->where('content', 'like', '%' . $params['content'] . '%');

            $list = $query->order('status', 'asc')->order('id', 'desc')->paginate($request->param('page_size', 10));
        } else {
            $list = $this->model->order('status', 'asc')->order('id', 'desc')->select();               // 查询全部
        }

        return success('获取成功', $list);
    }

    //------ Api ------
    public function comment_dynamic(Request $request)
    {
        $uid = $request->uid;
        $id = $request->post('id');
        $duid = $request->post('duid');
        $content = $request->post('ct');
        $img = $request->post('img');
        $reply_user_id = $request->post('reply_user_id', 0);
        $reply_comment_id = $request->post('reply_comment_id', 0);
        $model = [
            'dynamic_id' => $id,
            'user_id' => $uid,
            'reply_user_id' => $reply_user_id,
            'reply_comment_id' => $reply_comment_id,
            'content' => $content,
            'type' => 1,
            'status' => 1,
            'create_time' => time()
        ];
        $res = TencentLbs::ip($request->ip());
        if ($res['status'] == 0) {
            $model = User::ip_text($model, $res['result']);
        }
        $cid = $this->model->insertGetId($model);
        if ($uid != $reply_user_id) {
            $avatar_url = '/pages/user/details?id=' . $uid;
            $content_url = '/pages/dynamic/details?id=' . $id;
            $title = '回复了你的评论';
            if (!$reply_user_id) {
                $title = '评论了你的动态';
                $reply_user_id = $duid;
            }
            (new MessageModel())->send($reply_user_id, $title, $content, $uid, $img, $avatar_url, $content_url, 2);
        }
        $item = $this->model->where('id', $cid)->field('id,user_id,reply_user_id,content,province,status')->find();
        if ($item['reply_user_id']) $item['reply_user'] = UserModel::where('id', $item['reply_user_id'])->field('id,name')->find();
        $item['user'] = UserModel::where('id', $item['user_id'])->field('id,name,avatar')->find();
        $item['province'] = User::adds_text($item['province']);
        $item['create_time_str'] = "刚刚";
        $item['list_count'] = 0;
        return success('评论成功 🎉', $item);
    }

    public function dynamic_comment(Request $request)
    {
        $id = $request->get('id');
        $data = $this->model->where('dynamic_id', $id)
            ->where('reply_comment_id', 0)
            ->whereIn('status', [1, 2, 3])
            ->order('id', 'desc')
            ->field('id,user_id,content,province,create_time,status')
            ->paginate(8)->toArray();

        foreach ($data['data'] as $k => $v) {
            $list = $this->model->where('reply_comment_id', $v['id'])
                ->whereIn('status', [1, 2, 3])
                ->order('id', 'desc')
                ->field('id,user_id,content,province,reply_user_id,create_time,status')
                ->limit(4)
                ->select()->toArray();
            foreach ($list as $ik => $iv) {
                $list[$ik]['content'] = self::comment_status($iv['content'], $iv['status']);
                $list[$ik]['user'] = UserModel::where('id', $iv['user_id'])->field('id,name,avatar')->find();
                $list[$ik]['reply_user'] = UserModel::where('id', $iv['reply_user_id'])->field('id,name')->find();
                $list[$ik]['province'] = User::adds_text($iv['province']);
                $list[$ik]['create_time_str'] = Dynamic::time_text($iv['create_time']);
            }
            $data['data'][$k]['list_count'] = 0;
            if (count($list)) {
                $data['data'][$k]['list_count'] = $this->model->where('reply_comment_id', $v['id'])
                    ->where('status', '<>', 0)
                    ->count();
                $data['data'][$k]['list'] = $list;
            }
            $data['data'][$k]['content'] = self::comment_status($v['content'], $v['status']);
            $data['data'][$k]['user'] = UserModel::where('id', $v['user_id'])->field('id,name,avatar')->find();
            $data['data'][$k]['province'] = User::adds_text($v['province']);
            $data['data'][$k]['create_time_str'] = Dynamic::time_text($v['create_time']);
        }

        return success('成功', $data);
    }

    public function son_comment(Request $request)
    {
        $id = $request->get('id');
        $data = $this->model->where('reply_comment_id', $id)
            ->where('status', '<>', 0)
            ->order('id', 'desc')
            ->field('id,user_id,content,province,reply_user_id,create_time,status')
            ->paginate(4)->toArray();
        foreach ($data['data'] as $k => $v) {
            $data['data'][$k]['content'] = self::comment_status($v['content'], $v['status']);
            $data['data'][$k]['user'] = UserModel::where('id', $v['user_id'])->field('id,name,avatar')->find();
            $data['data'][$k]['reply_user'] = UserModel::where('id', $v['reply_user_id'])->field('id,name')->find();
            $data['data'][$k]['province'] = User::adds_text($v['province']);
            $data['data'][$k]['create_time_str'] = Dynamic::time_text($v['create_time']);
        }

        return success('成功', $data);
    }

    public function del_comment(Request $request)
    {
        $id = $request->post('id');
        $this->model->where('id', $id)->update(['status' => 3]);
        return success('操作成功，已删除', '');
    }

    public static function comment_status($content, $status)
    {
        if ($status == 2) {
            $content = '（评论内容违法违规）';
        } else if ($status == 3) {
            $content = '（该评论已被删除）';
        }
        return $content;
    }
}
