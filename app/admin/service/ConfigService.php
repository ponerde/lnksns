<?php

declare(strict_types=1);

namespace app\admin\service;
use app\admin\model\ConfigModel;


class ConfigService
{
    /**
     * 设置配置
     */
    public static function set(string $type, string $key, $value)
    {
        $original = $value;
        if (is_array($value)) {
            $value = json_encode($value, JSON_UNESCAPED_UNICODE);
        }

        $data = ConfigModel::where(['type' => $type, 'key' => $key])->findOrEmpty();

        if ($data->isEmpty()) {
            ConfigModel::create([
                'type' => $type,
                'key' => $key,
                'value' => $value,
            ]);
        } else {
            $data->value = $value;
            $data->save();
        }

        // 返回原始值
        return $original;
    }

    /**
     * 获取配置
     */
    public static function get(string $type, string $key = '', $default_value = null)
    {
        if (!empty($key)) {
            $value = ConfigModel::where(['type' => $type, 'key' => $key])->value('value');
            if (!is_null($value)) {
                $json = json_decode($value, true);
                $value = json_last_error() === JSON_ERROR_NONE ? $json : $value;
            }
            if ($value) {
                return $value;
            }
            // 返回特殊值 0 '0'
            if ($value === 0 || $value === '0') {
                return $value;
            }
            // 返回默认值
            if ($default_value !== null) {
                return $default_value;
            }
            // 返回本地配置文件中的值
            return config('project.' . $type . '.' . $key);
        }

        // 取某个类型下的所有name的值
        $data = ConfigModel::where(['type' => $type])->column('value', 'key');
        foreach ($data as $k => $v) {
            $json = json_decode($v, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $data[$k] = $json;
            }
        }
        if ($data) {
            return $data;
        }
    }
}
