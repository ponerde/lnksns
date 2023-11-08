<?php

declare(strict_types=1);

namespace lite\controller;

use app\BaseController;
use lite\exception\LiteException;
use lite\facade\Auth;

class Backend extends BaseController
{
    /**
     * 当前管理员 auth 实例
     *
     * @return Auth
     */
    public function auth()
    {
        return Auth::guard('admin');
    }

    

    /**
     * 批量操作
     *
     * @param collection|array $roles
     * @param \Closure $callback
     * @return void
     */
    public function batchOper($items, \Closure $callback = null)
    {
        $count = \think\facade\Db::transaction(function () use ($items, $callback) {
            $count = 0;

            foreach ($items as $item) {
                if ($callback) {
                    $count += $callback($item);
                } else {
                    $count += $item->delete();
                }
            }

            return $count;
        });

        if ($count) {
            return true;
        } else {
            throw new LiteException('未操作任何行');
        }
    }


     /**
     * 参数验证 By Sheep-admin
     *
     * @param array $params
     * @param string $validator
     * @return void
     */
    protected function svalidate(array $params, string $validator = "")
    {
        if (false !== strpos($validator, '.')) {
            // 是否支持场景验证
            [$validator, $scene] = explode('.', $validator);
        }
        // 获取validate实例
        $class = false !== strpos($validator, '\\') ? $validator : str_replace("controller", "validate", get_class($this));
        if (!class_exists($class)) {
            return;
        }

        $validate     = new $class();
        // 添加场景验证
        if (!empty($scene)) {
            $validate->scene($scene);
        } else {
            // halt(22);
            // 只验证传入参数
            $validate->only(array_keys($params));
        }
        // 失败自动抛出异常信息
        return $validate->failException(true)->check($params);
    }



    /**
     * 过滤前端发来的短时间内的重复的请求
     *
     * @return void
     */
    public function repeatFilter($key = null, $expire = 5)
    {
        if (!$key) {
            $httpName = app('http')->getName();
            $url = request()->baseUrl();
            $ip = request()->ip();

            $key = $httpName . ':' . $url . ':' . $ip;
        }

        if (cache()->store('persistent')->has($key)) {
            throw new LiteException('请稍后再试');
        }

        // 缓存 5 秒
        cache()->store('persistent')->tag('repeat_filter')->set($key, time(), $expire);
    }




    /**
     * 监听数据库 sql
     *
     * @return void
     */
    public function dbListen()
    {
        \think\facade\Db::listen(function ($sql, $time) {
            echo $sql . '<br/>' . $time;
        });
    }



    /**
     * 获取请求的 access
     *
     * @return string
     */
    public function accessName()
    {
        $root = substr(request()->root(), 1);
        $controller = request()->controller();
        $action = request()->action();
        $access = strtolower("{$root}.{$controller}.{$action}");
        return $access;
    }
}
