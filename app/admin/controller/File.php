<?php

declare(strict_types=1);

namespace app\admin\controller;

use think\Request;
use lite\controller\Backend;
use lite\model\file\FileModel;
use lite\model\file\FileGroupModel;
use lite\facade\Uploader;

class File extends Backend
{
    private $model = null;
    protected function initialize()
    {
        $this->model = new FileModel;
    }

    /**
     * 文件列表
     *
     * @return \think\Response
     */
    public function list(Request $request)
    {
        $group_id = $request->get('group_id','');
        $query = $this->model->liteFilter();
        if($group_id>=0){
            $query->where('group_id',$group_id);
        }
        $files = $query->order('id','desc')->paginate(request()->param('list_rows', 10));
        return success('获取成功', $files);
    }




    




    /**
     * 文件重命名
     *
     * @param Request $request
     * @param integer $id
     * @return void
     */
    public function rename(Request $request, $id)
    {
        $params = $request->only(['filename']);
        $this->svalidate($params, ".rename");

        $file = $this->model->findOrFail($id);
        $file->filename = $params['filename'] . '.' . $file->extension;
        $file->save();

        return success('修改成功', $file);
    }


    /**
     * 文件移动
     *
     * @param Request $request
     * @param string $id
     * @return void
     */
    public function move(Request $request, $id)
    {
        $id = explode(',', $id);
        $params = $request->only(['group']);
        $this->svalidate($params, ".move");

        $result = FileGroupModel::where('group', $params['group'])->count();
        if (!$result) {
            return error('请选择正确的分组');
        }
        $file = $this->model->whereIn('id', $id)->update(['group' => $params['group']]);

        return success('移动成功');
    }



    /**
     * 批量删除图片
     *
     * @param Request $request
     * @return void
     */
    public function delete($id)
    {
        $id = explode(',', $id);
        $request = $this->request;
        $is_real = $request->param('is_real', 0);

        foreach ($this->model->whereIn('id', $id)->cursor() as $file) {
            if ($is_real) {
                // 删除文件
                Uploader::driver($file->storage)->delete($file->getData('url'));
            }
            $file->delete();
        }

        return success('删除成功');
    }
}
