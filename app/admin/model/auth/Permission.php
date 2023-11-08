<?php

declare(strict_types=1);

namespace app\admin\model\auth;

use lite\model\BaseModel;

class Permission extends BaseModel
{
    protected $name = 'permissions';
    protected $autoWriteTimestamp = true;

    // 自动数据类型转换
    protected $type = [];

    // 自动 json 转换
    protected $json = [];


    protected $append = [
        'type_text',
        'status_text'
    ];


    /**
     * 类型列表
     *
     * @return void
     */
    // public function typeList()
    // {
    //     return ['menu' => '菜单', 'page' => '页面', 'modal' => '弹框', 'api' => '权限'];
    // }

    /**
     * 类型列表
     *
     * @return void
     */
    public function statusList()
    {
        return ['show' => '显示', 'hidden' => '隐藏', 'disabled' => '禁用'];
    }


    public function setNameAttr($value, $data)
    {
        return strtolower($value);
    }


    // public function children()
    // {
    //     return $this->hasMany(self::class, 'parent_id', 'id');
    // }
}
