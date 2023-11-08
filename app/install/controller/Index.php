<?php
declare (strict_types=1);

namespace app\install\controller;


use app\BaseController;
use app\install\service\InstallService;
use think\facade\Cache;
use think\facade\Env;

const SUCCESS = 'success';
const ERROR = 'error';

class Index extends BaseController
{

   public function __construct()
   {
    
        if(is_file(app_path('config').'install.lock')){
            exit('已经安装完成，无需重复安装');
        }
   }
    /**
     * 使用协议
     *
     * @return \support\Response
     */
    public function index()
    {
        Cache::clear();
        return view('/index/index');
    }

   

    /**
     * 检测安装环境
     *
     * @return mixed
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function step1()
    {

        if (request()->isPost()) {

            // 检测生产环境
            foreach ($this->checkEnv() as $key => $value) {
                if ($key == 'php' && (float)$value < 8.0) {
                    return error('PHP版本过低！');
                }
            }

            // 检测目录权限
            foreach ($this->checkDirFile() as $value) {
                if ($value[1] == ERROR
                    || $value[2] == ERROR) {
                    return error($value[3] . ' 权限读写错误！');
                }
            }

            Cache::set('checkEnv', 'success');
            return json(['code' => 200, 'url' => '/install/index/step2']);
        }

        return view('/index/step1', [
            'checkEnv' => $this->checkEnv(),
            'checkDirFile' => $this->checkDirFile(),
        ]);
    }


    /**
     * 检测环境变量
     * @return array
     */
    protected function checkEnv(): array
    {
        $items['php'] = PHP_VERSION;
        $items['mysqli'] = extension_loaded('mysqli');
        $items['redis'] = extension_loaded('redis');
        $items['curl'] = extension_loaded('curl');
        $items['fileinfo'] = extension_loaded('fileinfo');
        $items['exif'] = extension_loaded('exif');
        return $items;
    }

    /**
     * 检测读写环境
     * @return array
     */
    protected function checkDirFile(): array
    {
        $items = array(
            array('dir', SUCCESS, SUCCESS, './'),
            array('dir', SUCCESS, SUCCESS, './public'),
            array('dir', SUCCESS, SUCCESS, './public/uploads'),
            array('dir', SUCCESS, SUCCESS, './runtime'),
            array('dir', SUCCESS, SUCCESS, './app/install/config'),
        );

        foreach ($items as &$value) {

            $item = root_path() . $value[3];

            // 写入权限
            if (!is_writable($item)) {
                $value[1] = ERROR;
            }

            // 读取权限
            if (!is_readable($item)) {
                $value[2] = ERROR;
            }
        }

        return $items;
    }


    /**
     * 检查环境变量
     *
     * 
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function step2()
    {

        if (!Cache::get('checkEnv')) {
            return redirect('/install/index/step1');
        }

        if (request()->isPost()) {

            // 链接数据库
            $params = request()->all();
            $connect = @mysqli_connect($params['hostname'] . ':' . $params['hostport'], $params['username'], $params['password']);
            if (!$connect) {
                return error('数据库链接失败');
            }

            // 检测MySQL版本
            $mysqlInfo = @mysqli_get_server_info($connect);

            // 查询数据库名
            $database = false;
            $mysql_table = @mysqli_query($connect, 'SHOW DATABASES');
            while ($row = @mysqli_fetch_assoc($mysql_table)) {
                if ($row['Database'] == $params['database']) {
                    $database = true;
                    break;
                }
            }

            if (!$database) {
                $query = "CREATE DATABASE IF NOT EXISTS `" . $params['database'] . "` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;";
                if (!@mysqli_query($connect, $query)) {
                    return error('数据库创建失败或已存在，请手动修改');
                }
            }

            Cache::set('mysqlInfo', $params);
            return json(['code' => 200, 'url' => '/install/index/step3']);
        }

        return view('/index/step2');
    }

    /**
     * 初始化数据库
     * @return \support\Response
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function step3()
    {
        $mysqlInfo = Cache::get('mysqlInfo');
        if (!$mysqlInfo) {
            return redirect('/install/index/step2');
        }

        return view('/index/step3');
    }

    /**
     * 安装数据缓存
     * @return \support\Response|void
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function install()
    {
        if (request()->isAjax()) {

            $mysqlInfo = Cache::get('mysqlInfo');
            if (is_file(app_path('config/install.lock')) || !$mysqlInfo) {
                return error('请勿重复安装本系统');
            }

            // 读取SQL文件加载进缓存
            $mysqlPath = root_path('app/install') . 'install.sql';
            $sqlRecords = file_get_contents($mysqlPath);
            $sqlRecords = str_ireplace("\r", "\n", $sqlRecords);

            // 替换数据库表前缀
            $sqlRecords = explode(";\n", $sqlRecords);
            $sqlRecords = str_replace(" `__PREFIX__", " `{$mysqlInfo['prefix']}", $sqlRecords);

            $sqlConnect = @mysqli_connect($mysqlInfo['hostname'] . ':' . $mysqlInfo['hostport'], $mysqlInfo['username'], $mysqlInfo['password']);
            mysqli_select_db($sqlConnect, $mysqlInfo['database']);
            mysqli_query($sqlConnect, "set names utf8mb4");

            foreach ($sqlRecords as $index => $sqlLine) {
                $sqlLine = trim($sqlLine);
                if (!empty($sqlLine)) {
                    try {
                        // 创建表数据
                        if (mysqli_query($sqlConnect, $sqlLine) === false) {
                            throw new \Exception(mysqli_error($sqlConnect));
                        }
                    } catch (\Throwable $th) {
                        return error($th->getMessage());
                    }
                }
            }
            $pwd = $mysqlInfo['pwd'];
            $salt = random_int(1000,9999);
            $pwd =encrypt_password($pwd,$salt);
            mysqli_query($sqlConnect, "UPDATE {$mysqlInfo['prefix']}admin SET  password='{$pwd}' , salt='{$salt}' where is_super = 1");

            return success('success');
        }
    }

    /**
     * 清理安装文件包
     *
     * @return \support\Response|void
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function clear()
    {

        if (request()->isAjax()) {
            try {
           
                
                $mysqlInfo = Cache::get('mysqlInfo');
                
                InstallService::putEnv($mysqlInfo);
                
                file_put_contents(app_path('config').'install.lock','');

                // 清理安装包
                Cache::clear();
              
            } catch (\Throwable $th) {
                return error($th->getMessage());
            }

            return success('安装成功,如install模块未删除，请手动删除');
        }
    }
}