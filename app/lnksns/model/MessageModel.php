<?php

declare(strict_types=1);

namespace app\lnksns\model;

use app\admin\model\auth\AdminRoleModel;
use lite\model\BaseModel;
use lite\service\FileService;

class MessageModel extends BaseModel
{
    protected $name = 'free_message';

    protected $type = [];

    protected $json = [];    // 自动 json 转换

    public function user()
    {
        return $this->hasOne(UserModel::class, "id", "user_id")->field("id,name,avatar");
    }

    public function send($user_id, $title, $content, $launch_id = 1, $img = '', $avatar_url = '', $content_url = '', $type = 0)
    {
        $exists = $this->where('content', $content)
            ->where('user_id', $user_id)
            ->value('id') ? true : false;

        if (!$exists) {
            $this->insert([
                'user_id' => $user_id,
                'title' => $title,
                'content' => $content,
                'launch_id' => $launch_id,
                'img' => $img,
                'avatar_url' => $avatar_url,
                'content_url' => $content_url,
                'type' => $type,
                'read' => 0,
                'status' => 1,
                'create_time' => time()
            ]);
        }
    }

}
