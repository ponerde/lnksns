<?php

declare(strict_types=1);

namespace app\admin\controller\auth;

use lite\controller\Backend;
use app\admin\model\auth\AdminModel;
use app\Request;
use lite\controller\traits\Crud;
use lite\exception\LiteException;
use think\facade\Db;

class Admin extends Backend
{
    use Crud;
    protected $childAdminIds = [];

    public function initialize()
    {
        $this->model = new AdminModel;
    }


    /**
     * 管理员列表 
     *
     * @return \think\Response
     */
    public function index(Request $request)
    {
        $params = $request->get();
        $query = $this->model->with('role');
        if($params['username']){
            $query->where('username',$params['username']);
        }
        $data = $query->paginate(request()->param('list_rows', 10));
        return success('获取成功', $data);
    }


    /**
     * 管理员添加
     *
     * @return \think\Response
     */
    public function save()
    {

        $params = $this->request->only(['username', 'role_id', 'password', 'avatar', 'nickname', 'mobile', 'email', 'status']);
        $params = array_filter($params);
        $this->svalidate($params, ".add");

        // $childRoleIds = $this->auth()->getChildRoleIds(false);
        // if (!$this->auth()->isSuper() && !in_array($params['role_id'], $childRoleIds)) {
        //     throw new LiteException('请选择正确的角色组');
        // }

        $admin = Db::transaction(function () use ($params) {
            $salt = mt_rand(1000, 9999);
            $params['salt'] = $salt;
            $params['password'] = $this->model->encryptPassword($params['password'], $salt);
            return $this->model->save($params);
        });
        return success('保存成功', $admin);
    }

    /**
     * 管理员详情
     *
     * @param  $id
     * @return \think\Response
     */
    public function read($id)
    {
        $admin = $this->auth()->user();
        $data = $this->model->field(['username', 'role_id', 'avatar', 'nickname', 'mobile', 'email', 'status','id'])->findOrFail($id);
        return success('获取成功', $data);
    }

    /**
     * 更新管理员
     *
     * @param  $id
     * @return \think\Response
     */
    public function update($id)
    {
        $params = $this->request->only(['role_id', 'avatar','password', 'nickname','username', 'mobile', 'email', 'status','id']);
        $admin =  $this->model->where('username', $params['username'])->find();
        if($admin){
            if($admin->id != $params['id']){
                throw new LiteException('用户名已经存在');
            }
            
        }
        
        Db::transaction(function () use ($id, $params) {
            foreach ($this->model->whereIn('id', $id)->cursor() as $admin) {
                if(isset($params['password'])){
                    $salt = mt_rand(1000, 9999);
                    $params['salt'] = $salt;
                    $params['password'] = $this->model->encryptPassword($params['password'], $salt);
                }
                $admin->save($params);
            }
        });

        return success('更新成功');
    }

    public function updatePassword()
    {
        $admin = $this->auth()->user();
        $old_password = $this->request->param('old_password');
        $password = $this->request->param('password');
        $confirm_password = $this->request->param('confirm_password');

        $admin = $this->model->where('id',$admin->id)->find();
        // 判断老密码是正确
        if ($admin->encryptPassword($old_password, $admin->salt) != $admin->password) {
            throw new LiteException('旧密码不正确');
        }

        // 判断两次密码是否输入正确
        if($confirm_password != $password){
            throw new LiteException('两次密码输入不一致');
        }
        $salt = mt_rand(1000, 9999);
        $admin->salt = $salt;
        $admin->password = $this->model->encryptPassword($password, $salt);
        $admin->save();
        return success('修改密码成功');

    }

    /**
     * 删除管理员(支持批量)
     *
     * @param  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        $id = explode(',', $id);

        $admin = $this->auth()->user();
        // if (in_array($admin->id, $id) || array_diff($id, $this->childAdminIds)) {
        //     throw new LiteException("无权限操作该管理员");
        // }

        Db::transaction(function () use ($id) {
            foreach ($this->model->whereIn('id', $id)->cursor() as $admin) {
                $admin->delete();
            }
        });

        return success('删除成功');
    }



    /**
     * 获取管理员的消息列表
     *
     * @return \think\Response
     */
    public function notifications()
    {
        $admin = $this->auth()->user();

        // 查询分页列表
        $notifications = $admin->notifications()->sheepFilter()->paginate(request()->param('list_rows', 10));

        return success('消息列表', $notifications);
    }


    /**
     * 指定消息标记已读
     *
     * @param string $id
     * @return void
     */
    public function notification($id)
    {
        $admin = $this->auth()->user();

        // 将要查询的列表全部标记为已读
        $notification = $admin->notifications()->findOrFail($id)->markAsRead();

        return success('已读成功', $notification);
    }


    /**
     * 删除已读消息
     *
     * @return void
     */
    public function delNotifications()
    {
        $admin = $this->auth()->user();

        // 将要查询的列表全部标记为已读
        $admin->notifications()->whereNotNull('read_time')->delete();

        return success('删除成功');
    }
}
