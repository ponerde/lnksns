<?php

declare(strict_types=1);

namespace lite\controller\traits;

use app\Request;
use think\facade\Db;
use think\Response;

trait Crud
{
    public $model;

    
    /**
     * 查看
     *
     * @return Response
     */
    public function index(Request $request): Response
    {
        if (!empty($request->param('page_size'))) {       // 使用分页
            $list = $this->model->liteFilter()->paginate($request->param('page_size', 10));
        } else {
            $list = $this->model->liteFilter()->select();               // 查询全部
        }

        return success('获取成功', $list);
    }

    /**
     * 详情
     *
     * @param  $id
     * @return \think\Response
     */
    public function read($id)
    {
        $detail = $this->model->findOrFail($id);
        return success('获取成功', $detail);
    }

    /**
     * 添加
     *
     * @return \think\Response
     */
    public function save(Request $request)
    {
        if (!empty($this->onlyParams)) {                        // 参数过滤
            $params = $request->only($this->onlyParams);
        } else {                                                // 接受全部参数
            $params = $request->param();
        }
        $this->svalidate($params, '.add');

        $result = Db::transaction(function () use ($params) {
            return $this->model->save($params);
        });
        if ($result) {
            return success('保存成功', $this->model);
        }
        return error('保存失败');
    }

    /**
     * 编辑(支持批量)
     *
     * @param  $id
     * @return \think\Response
     */
    public function update(Request $request, $id)
    {
        if (!empty($this->onlyParams)) {                        // 参数过滤
            $params = $request->only($this->onlyParams);
        } else {                                                // 接受全部参数
            $params = $request->param();
        }

        $pk = $this->model->getPk();

        $result = Db::transaction(function () use ($id, $params, $pk) {
            $count = 0;
            foreach ($this->model->whereIn($pk, $id)->cursor() as $row) {
                $params[$pk] = $row->$pk;
                $this->svalidate($params);
                $count += $row->save($params);
            }
            return $count;
        });

        if ($result) {
            return success('更新成功', $result);
        } else {
            return error('更新失败');
        }
    }

    /**
     * 删除(支持批量)
     *
     * @param  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        $pk = $this->model->getPk();

        $result = Db::transaction(function () use ($id, $pk) {
            $count = 0;
            foreach ($this->model->whereIn($pk, $id)->cursor() as $row) {
                $count += $row->delete();
            }
            return $count;
        });
        if ($result) {
            return success('删除成功', $result);
        }
        return error('删除失败');
    }

    /**
     * 回收站
     *
     * @return \think\Response
     */
    public function recyclebin()
    {
        $list = $this->model->onlyTrashed()->select();
        return success('操作成功', $list);
    }

    /**
     * 还原(支持批量)
     *
     * @param  $id
     * @return \think\Response
     */
    public function restore($id)
    {
        $pk = $this->model->getPk();

        $result = Db::transaction(function () use ($id, $pk) {
            $count = 0;
            foreach ($this->model->onlyTrashed()->whereIn($pk, $id)->cursor() as $row) {
                $count += $row->restore();
            }
            return $count;
        });
        if ($result) {
            return success('还原成功', $result);
        }
        return error('还原失败');
    }

    /**
     * 销毁(支持批量)
     *
     * @param  $id
     * @return \think\Response
     */
    public function destroy($id)
    {
        $pk = $this->model->getPk();

        $result = Db::transaction(function () use ($id, $pk) {
            $count = 0;
            if ($id !== 'all') {
                $model = $this->model->onlyTrashed()->whereIn($pk, $id);
            } else {
                $model = $this->model->onlyTrashed();
            }

            foreach ($model->cursor() as $row) {
                $count += $row->force()->delete();
            }
            return $count;
        });

        if ($result) {
            return success('销毁成功', $result);
        }
        return error('销毁失败');
    }
}
