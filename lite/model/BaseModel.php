<?php
namespace lite\model;

use lite\filter\BaseFilter;
use lite\library\auth\traits\AuthMethod;
use think\Model;

class BaseModel extends Model
{
    use AuthMethod;
    protected $autoWriteTimestamp = true;
    /**
     * 当前 model 对应的 filter 实例
     *
     * @return BaseFilter
     */
    public function filterInstance()
    {
        $app_name = app('http')->getName();     // 当前模块名
        $filter_class = static::class;

        $class = str_replace('model', 'filter',  $filter_class) . 'Filter';

        if (!class_exists($class)) { 
            return new BaseFilter();
        }
        return new $class();
    }


    /**
     * 查询范围 filter 搜索入口
     *
     * @param Query $query
     * @return void
     */
    public function scopeLiteFilter($query, $sort = true, $filters = null)
    {
        // $instance = $this->filterInstance();
        // $query = $instance->apply($query, $filters);

        // if ($sort) {
        //     $query = $instance->filterOrder($query);
        // }
        $params = request()->all();
        

        return $query;
    }


     /**
     * 将时间格式化为时间戳
     *
     * @param [type] $time
     * @return void
     */
    protected function setCreateTimeAttr($time) 
    {
        return $time ? (!is_numeric($time) ? strtotime($time) : $time) : null;
    }

}