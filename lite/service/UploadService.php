<?php
namespace lite\service;

use lite\library\storage\Storage as StorageDriver;
use Exception;
use lite\model\file\FileModel;

class UploadService
{

   /**
    * 图片上传
    */
    public static function image($cid, int $sourceId = 0, int $source = 0, string $saveDir = 'uploads/images')
    {
        try {
            $config = [
                'default' => ConfigService::get('storage', 'default', 'local'),
                'engine'  => ConfigService::get('storage') ?? ['local'=>[]],
            ];

            // 2、执行文件上传
            $StorageDriver = new StorageDriver($config);
            $StorageDriver->setUploadFile('file');
            $fileName = $StorageDriver->getFileName();
            $fileInfo = $StorageDriver->getFileInfo();

            // 校验上传文件后缀
            if (!in_array(strtolower($fileInfo['ext']), config('lite.file_image'))) {
                throw new Exception("上传图片不允许上传". $fileInfo['ext'] . "文件");
            }

            // 上传文件
            $saveDir = $saveDir . '/' .  date('Ymd');
            if (!$StorageDriver->upload($saveDir)) {
                throw new Exception($StorageDriver->getError());
            }

            // 3、处理文件名称
            if (strlen($fileInfo['name']) > 128) {
                $name = substr($fileInfo['name'], 0, 123);
                $nameEnd = substr($fileInfo['name'], strlen($fileInfo['name'])-5, strlen($fileInfo['name']));
                $fileInfo['name'] = $name . $nameEnd;
            }

            // 4、写入数据库中
            $file = FileModel::create([
                'admin_id'   => 0,
                'group_id'   => $cid,
                'uri'        => $saveDir . '/' . str_replace("\\","/", $fileName),
                'file_md5'   => $fileName,
                'filename'   => $fileInfo['name'],
                'extension'  => $fileInfo['ext'],
                'mimetype'   => $fileInfo['mime'],
                'filesize'   => $fileInfo['size'],
                
            ]);

            // 5、返回结果
            return [
                'id'   => $file['id'],
                'group_id'  => $file['group_id'],
                'type' => $file['extension'],
                'name' => $file['filename'],
                'url' => FileService::getFileUrl($file['uri']),
                'uri'  => $file['uri']
            ];

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }


    // 视频上传
    public static function video($cid, int $sourceId = 0, int $source = 0, string $saveDir = 'uploads/video')
    {
        try {
            $config = [
                'default' => ConfigService::get('storage', 'default', 'local'),
                'engine'  => ConfigService::get('storage') ?? ['local'=>[]],
            ];

            // 2、执行文件上传
            $StorageDriver = new StorageDriver($config);
            $StorageDriver->setUploadFile('file');
            $fileName = $StorageDriver->getFileName();
            $fileInfo = $StorageDriver->getFileInfo();

            // 校验上传文件后缀
            if (!in_array(strtolower($fileInfo['ext']), config('lite.file_video'))) {
                throw new Exception("上传视频不允许上传". $fileInfo['ext'] . "文件");
            }

            // 上传文件
            $saveDir = $saveDir . '/' .  date('Ymd');
            if (!$StorageDriver->upload($saveDir)) {
                throw new Exception($StorageDriver->getError());
            }

            // 3、处理文件名称
            if (strlen($fileInfo['name']) > 128) {
                $name = substr($fileInfo['name'], 0, 123);
                $nameEnd = substr($fileInfo['name'], strlen($fileInfo['name'])-5, strlen($fileInfo['name']));
                $fileInfo['name'] = $name . $nameEnd;
            }

            var_dump($fileInfo['name']);
            // 4、写入数据库中
            $file = FileModel::create([
                'admin_id'   => 0,
                'group_id'         => $cid,
                'url'=>$saveDir . '/' . str_replace("\\","/", $fileName),
                
                'filename'        => $fileInfo['name'],
                
                'create_time' => time(),
            ]);

            // 5、返回结果
            return [
                'id'   => $file['id'],
                'cid'  => $file['cid'],
                'type' => $file['type'],
                'name' => $file['name'],
                'uri'  => FileService::getFileUrl($file['uri']),
                'url'  => $file['uri']
            ];

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

}