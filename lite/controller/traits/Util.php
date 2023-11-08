<?php

declare(strict_types=1);

namespace lite\controller\traits;

use app\admin\model\PageModel;
use think\Request;
use sheep\exception\SheepException;
use sheep\facade\Sms;
use sheep\facade\Mail;
use sheep\facade\Uploader;
use sheep\model\user\User;

/**
 * 公共接口 trait
 */
trait Util
{

   
    /**
     * 上传图片（不存 file 表）
     *
     * @param Request $request
     * @return void
     */
    public function upload(Request $request)
    {
        $file = $request->file('file');
        // $group = $request->param('group', 'default');

        $result = Uploader::uploadSim($file, 'ugc');        // 前端固定上传目录

        return success('上传成功', $result);
    }


    /**
     * 客服通用 token,通过用户信息换取
     *
     * @return void
     */
    public function unifiedToken()
    {
        $root = substr(request()->root(), 1);
        if ($root == 'shop') {
            // 商城通过 token 自动获取
            $user = $this->auth()->user();
        } else {
            // 客服没有登录模块，所以直接传 id
            $id = $this->request->param('id');
            $user = User::find($id);
        }

        if (!$user) {
            throw new SheepException('用户不存在');
        }

        $token = $user->getUnifiedToken('user:' . $user->id);

        return success('获取成功', [
            'token' => $token
        ]); 
    }

    /**
     * 获取页面数据
     */
    public function page(Request $request)
    {
        $id = $request->param('id',0);
        $item = PageModel::where('id',$id)->where('status','normal')->field('page')->find();
        if($item){
            $page = $item['page']; 
         }else{
             $page = [];
         }
        return success(compact('page'));
    }
}
