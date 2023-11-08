<?php

namespace app\lnksns\controller;

use app\admin\model\auth\AdminModel;
use app\lnksns\lib\JwtAuth;
use app\lnksns\lib\TencentLbs;
use app\lnksns\model\DynamicModel;
use app\lnksns\model\MessageModel;
use app\lnksns\model\UserModel;
use EasyWeChat\Factory;
use GuzzleHttp\Client;
use lite\controller\Backend;
use lite\controller\traits\Crud;
use lite\service\ConfigService;
use think\facade\Db;
use think\Request;

class User extends Backend
{
    //------ Admin ------
    use Crud;

    public function initialize()
    {
        $this->model = new UserModel();
    }

    public function index(Request $request)
    {
        $params = $request->get();

        if (!empty($request->param('page_size'))) {       // 使用分页
            $query = $this->model;
            if ($params['name']) $query = $query->where('name', 'like', '%' . $params['name'] . '%');
            if ($params['mobile']) $query = $query->where('mobile', '=', $params['mobile']);

            $list = $query->paginate($request->param('page_size', 10));
        } else {
            $list = $this->model->select();               // 查询全部
        }

        return success('获取成功', $list);
    }

    //------ API ------
    public function wx_empower(Request $request)
    {

        $code = $request->post('code');
        if (!$code) return error('授权失败', 500001);

        $config = [
            'app_id' => ConfigService::get('lnksns', 'lnk_app_id', ''),
            'secret' => ConfigService::get('lnksns', 'lnk_app_secret', ''),
            'response_type' => 'array',
        ];
        $app = Factory::miniProgram($config);
        $response = $app->auth->session($code);
        if (!$response['openid']) return error('授权失败', 500001);

        $info = $this->model->where('weixin_openid', $response['openid'])->field(self::user_find())->find();
        if (!$info) {
            $model = [
                'name' => '用户' . rand(100000, 999999),
                'avatar' => $request->domain() . '/static/images/avatar.png',
                'weixin_openid' => $response['openid'],
                'ip' => $request->ip(),
                'create_time' => time()
            ];
            $res = TencentLbs::ip($request->ip());
            if ($res['status'] == 0) {
                $model = self::ip_text($model, $res['result']);
            }
            $this->model->insert($model);
            $info = $this->model->where('weixin_openid', $response['openid'])->field(self::user_find())->find();
        }
        $info = self::user_other($info);
        $data['info'] = $info;
        $data['token'] = JwtAuth::signToken($info['id']);
        return success('success', $data);
    }

    public function user_refresh_info(Request $request)
    {
        $uid = $request->uid;
        $info = $this->model->where('id', $uid)->field(self::user_find())->find();
        $info = self::user_other($info);
        $data['info'] = $info;
        $data['token'] = JwtAuth::signToken($info['id']);
        return success('success', $data);
    }

    public function edit_user_info(Request $request)
    {
        $uid = $request->uid;
        $model = [];
        $model['name'] = $request->post('name');
        $model['avatar'] = $request->post('avatar');
        $model['gender'] = $request->post('gender');
        $model['age'] = $request->post('age');
        $model['career'] = $request->post('career');
        $model['update_time'] = time();
        $this->model->where('id', $uid)->update($model);

        return success('修改成功 🎉');
    }

    public function user_bind_mobile(Request $request)
    {
        $uid = $request->uid;
        $code = $request->post('code');
        if (!$code) return error('授权失败', 500001);

        $config = [
            'app_id' => ConfigService::get('lnksns', 'lnk_app_id', ''),
            'secret' => ConfigService::get('lnksns', 'lnk_app_secret', ''),
            'response_type' => 'array',
        ];
        $app = Factory::miniProgram($config);
        $response = $app->phone_number->getUserPhoneNumber($code);

        if ($response['errmsg'] == 'ok') {
            $model['mobile'] = $response['phone_info']['phoneNumber'];
            $model['update_time'] = time();
            $this->model->where('id', $uid)->update($model);

            $data = $this->model->where('id', $uid)->field(self::user_find())->find();
            $data = self::user_other($data);

            return success('操作成功 🎉', $data);
        } else {
            return error('授权失败', 500001);
        }
    }

    public function user_refresh_ip(Request $request)
    {
        $uid = $request->uid;
        $res = TencentLbs::ip($request->ip());
        if ($res['status'] == 0) {
            $model = self::ip_text([], $res['result']);
            $this->model->where('id', $uid)->update($model);

            $data = $this->model->where('id', $uid)->field(self::user_find())->find();
            $data = self::user_other($data);

            return success('刷新成功 🎉', $data);
        } else {
            return error('刷新失败', 500001);
        }
    }

    public function user_details(Request $request)
    {
        $uid = $request->uid;
        $user_id = $request->get('user_id', 0);
        $data = $this->model->where('id', $user_id)->field(self::user_find())->find();
        if (!$data) return error('未找到用户或用户异常！', 500101);
        $data = self::user_other($data, $uid);
        return success('成功', $data);
    }

    public function user_publish_content(Request $request)
    {
        $uid = $request->uid;
        $type = $request->get('type');
        $user_id = $request->get('user_id', 0);
        if ($user_id > 0) {
            $uid = $user_id;
            $condition[] = ['status', '=', 1];
            $condition[] = ['show', '=', 1];
        }else{
            $condition[] = ['status', 'in', [0, 1]];
        }
        if ($type == 0) {
            $data = DynamicModel::where('user_id', $uid)
                ->where($condition)
                ->order('id', 'desc')
                ->field(Dynamic::dynamic_find())
                ->paginate(6)->toArray();
            if ($data) $data = Dynamic::dynamic_map($data, $user_id > 0, $request->uid);
        } else {
            $ids = Db::name('free_user_like_dynamic')
                ->where('user_id', $uid)
                ->where('status', 1)
                ->order('id', 'desc')
                ->column('dynamic_id');
            $ids_ordered = implode(',', $ids);
            if (count($ids)) {
                $data = DynamicModel::where('status', 1)
                    ->whereIn('id', $ids)
                    ->orderRaw("FIELD(id, $ids_ordered)")
                    ->field(Dynamic::dynamic_find())
                    ->paginate(6)->toArray();
                if ($data) $data = Dynamic::dynamic_map($data, $user_id > 0, $request->uid);
            }
        }

        return success('成功', $data ?? '');
    }

    public function follow_user(Request $request)
    {
        $uid = $request->uid;
        $uname = $request->post('uname');
        $user_id = $request->post('user_id');
        $is_follow = $request->post('is_follow');
        if ($user_id == $uid) return error('您不能关注您自己', 500201);
        if ($is_follow) {
            Db::name('free_user_follow')
                ->where('user_id', $uid)
                ->where('follow_user_id', $user_id)
                ->delete();
        } else {
            Db::name('free_user_follow')->insert([
                'user_id' => $uid,
                'follow_user_id' => $user_id,
                'create_time' => time()
            ]);
            $message = $uname . ' 关注了你。';
            $avatar_url = '/pages/user/details?id=' . $uid;
            (new MessageModel())->send($user_id, '收到关注', $message, $uid, '', $avatar_url);
        }
        return success('成功', '');
    }

    public function user_follow(Request $request)
    {
        $uid = $request->uid;
        $type = $request->get('type');
        if ($type == 1) {
            $data = Db::name('free_user_follow')
                ->where('user_id', $uid)
                ->order('id', 'desc')
                ->field('id,follow_user_id')
                ->paginate(15)->toArray();
            foreach ($data['data'] as $k => $v) {
                $data['data'][$k]['user'] = $this->model->where('id', $v['follow_user_id'])
                    ->field('id,name,avatar')
                    ->find();
                $data['data'][$k]['is_follow'] = Db::name('free_user_follow')
                    ->where('user_id', $v['follow_user_id'])
                    ->where('follow_user_id', $uid)
                    ->count() ? true : false;
            }
        } else {
            $data = Db::name('free_user_follow')
                ->where('follow_user_id', $uid)
                ->order('id', 'desc')
                ->field('id,user_id')
                ->paginate(15)->toArray();
            foreach ($data['data'] as $k => $v) {
                $data['data'][$k]['user'] = $this->model->where('id', $v['user_id'])
                    ->field('id,name,avatar')
                    ->find();
                $data['data'][$k]['is_follow'] = Db::name('free_user_follow')
                    ->where('user_id', $uid)
                    ->where('follow_user_id', $v['user_id'])
                    ->count() ? true : false;
            }
        }

        return success('成功', $data);
    }

    public
    static function user_other($info, $uid = 0)
    {
        if ($uid) {
            $condition[] = ['status', '=', 1];
            $condition[] = ['show', '=', 1];
            $info['is_follow'] = Db::name('free_user_follow')
                ->where('follow_user_id', $info['id'])
                ->where('user_id', $uid)
                ->count() ? true : false;
        }else{
            $condition[] = ['status', 'in', [0, 1]];
        }
        $info['follow'] = Db::name('free_user_follow')
            ->where('user_id', $info['id'])
            ->count();
        if ($info['follow'] > 999) {
            $info['follow'] = self::convert($info['follow']);
        }
        $info['fans'] = Db::name('free_user_follow')
            ->where('follow_user_id', $info['id'])
            ->count();
        if ($info['fans'] > 999) {
            $info['fans'] = self::convert($info['fans']);
        }
        $info['dynamic_count'] = DynamicModel::where('user_id', $info['id'])->where($condition)->count();
        $info['like_dynamic_count'] = Db::name('free_user_like_dynamic')->where('status',1)->where('user_id', $info['id'])->count();
        $info['mobile'] = self::mobile_text($info['mobile']);
        $info['province'] = self::adds_text($info['province']);
        return $info;
    }

    public
    static function user_find()
    {
        return ['id,name,avatar,gender,age,career,mobile,province'];
    }

    public
    static function ip_text($model, $data)
    {
        $model['lat'] = $data['location']['lat'];
        $model['lng'] = $data['location']['lng'];
        $model['country'] = $data['ad_info']['nation'];
        $model['province'] = $data['ad_info']['province'];
        $model['city'] = $data['ad_info']['city'];
        $model['district'] = $data['ad_info']['district'];
        return $model;
    }

    public
    static function adds_text($text)
    {
        if (!$text) return 'IP未知';
        $text = str_replace('省', '', $text);
        $text = str_replace('市', '', $text);
        $text = str_replace('壮族自治区', '', $text);
        $text = str_replace('回族', '', $text);
        $text = str_replace('自治区', '', $text);
        $text = str_replace('特别行政区', '', $text);
        return $text;
    }

    public
    static function mobile_text($mobile)
    {
        if ($mobile) $mobile = substr_replace($mobile, '****', 3, 4);
        return $mobile;
    }

    public
    static function convert($num)
    {
        $num = round($num);
        if ($num >= 100000000) {
            $num = round($num / 100000000, 2) . '亿';
        } else if ($num >= 10000) {
            $num = round($num / 10000, 2) . '万';
        } else if ($num >= 1000) {
            $num = round($num / 1000, 2) . 'k';
        }
        return $num;
    }

}
