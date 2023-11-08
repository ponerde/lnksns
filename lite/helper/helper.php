<?php

declare(strict_types=1);

use think\exception\HttpResponseException;
use think\Response;

/**
 * 助手函数文件，通过composer autoload 加载
 */


if (!function_exists('success')) {
    /**
     * 返回成功方法
     *
     * @param string $msg
     * @param array $data
     * @return \think\response\Json
     */
    function success($msg = '', $data = null)
    {
        $result = [
            'code' => 200,
            'msg' => $msg,
            'data' => $data
        ];
        if(is_array($msg) || is_object($msg)){
            $result = [
                'code' => 200,
                'msg' => 'success',
                'data' => $msg
            ];
        }
       

        return json($result);
    }
}

if (!function_exists('error')) {
    /**
     * 返回失败方法
     *
     * @param string $msg
     * @param array $data
     * @return \think\response\Json
     */
    function error($msg = '', $error_code = 1, $data = null, $status_code = 200)
    {
        $result = [
            'error' => $error_code,
            'msg' => $msg,
            'data' => $data
        ];

        return json($result, $status_code);

        // 抛异常方式
        // if ($error_code != 1 || $status_code !== 200) {
        //     throw (new SheepException)->setMessage($msg, $error_code, $status_code);
        // } else {
        //     throw new SheepException($msg);
        // }
    }
}

/**
 * 格式化经纬度
 */
if (!function_exists('match_latlng')) {
    /**
     * 格式化经纬度
     * @param string  $latlng    要格式化的经纬度
     * @return string
     */
    function match_latlng($latlng)
    {
        $match = "/^\d{1,3}\.\d{1,30}$/";
        return preg_match($match, $latlng) ? $latlng : 0;
    }
}


/**
 * 拼接查询距离 sql
 */
if (!function_exists('distance_builder')) {
    /**
     * 拼接查询距离 sql
     * @param string  $lat    纬度
     * @param string  $lng    经度
     * @return string
     */
    function distance_builder($lat, $lng)
    {
        return "ROUND(6378.138 * 2 * ASIN(SQRT(POW(SIN((" . match_latlng($lat) . " * PI() / 180 - latitude * PI() / 180) / 2), 2) + COS(" . match_latlng($lat) . " * PI() / 180) * COS(latitude * PI() / 180) * POW(SIN((" . match_latlng($lng) . " * PI() / 180 - longitude * PI() / 180) / 2), 2))) * 1000) AS distance";
    }
}



/**
 * 检测字符串是否是版本号
 */
if (!function_exists('is_version_str')) {
    /**
     * 检测字符串是否是版本号
     * @param string  $version
     * @return boolean
     */
    function is_version_str($version)
    {
        $match = "/^([0-9]\d|[0-9])(\.([0-9]\d|\d))+/";
        return preg_match($match, $version) ? true : false;
    }
}


/**
 * 删除目录
 */
if (!function_exists('rmdirs')) {

    /**
     * 删除目录
     * @param string $dirname  目录
     * @param bool   $withself 是否删除自身
     * @return boolean
     */
    function rmdirs($dirname, $withself = true)
    {
        if (!is_dir($dirname)) {
            return false;
        }
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dirname, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($files as $fileinfo) {
            if ($fileinfo->isDir() === 'rmdir') rmdir(($fileinfo->getRealPath()));
            if ($fileinfo->isDir() === 'unlink') unlink(($fileinfo->getRealPath()));
        }
        if ($withself) {
            @rmdir($dirname);
        }
        return true;
    }
}


/**
 * 复制目录
 */
if (!function_exists('copydirs')) {

    /**
     * 复制目录
     * @param string $source 源文件夹
     * @param string $dest   目标文件夹
     */
    function copydirs($source, $dest)
    {
        if (!is_dir($dest)) {
            @mkdir($dest, 0755, true);
        }
        foreach ($iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($source, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        ) as $item) {
            if ($item->isDir()) {
                $sontDir = $dest . DIRECTORY_SEPARATOR . $iterator->getSubPathName();
                if (!is_dir($sontDir)) {
                   @mkdir($sontDir, 0755, true);
                }
            } else {
                copy($item, $dest . DIRECTORY_SEPARATOR . $iterator->getSubPathName());
            }
        }
    }
}


if (!function_exists('is_url')) {
    function is_url($url)
    {
        if (preg_match("/^(http:\/\/|https:\/\/)/", $url)) {
            return true;
        }

        return false;
    }
}

/**
 * 获取所有应用
 */
if (!function_exists('get_apps')) {
    /**
     * 获取所有应用
     */
    function get_apps()
    {
        $finder = new \Symfony\Component\Finder\Finder();
        $finder->directories()->ignoreVCS(false)->depth('== 0')->in(root_path('app'));

        $apps = [];
        foreach ($finder as $dir) {
            $apps[] = $dir->getRelativePathname();
        }
        return $apps;
    }
}



/**
 * 快捷设置跨域请求头（跨域中间件失效时，一些特殊拦截时候需要用到）
 */
if (!function_exists('set_cors')) {
    /**
     * 快捷设置跨域请求头（跨域中间件失效时，一些特殊拦截时候需要用到）
     *
     * @return void
     */
    function set_cors() {
        $header = [
            'Access-Control-Allow-Origin' => '*',           // 规避跨域
            'Access-Control-Allow-Credentials' => 'true',
            'Access-Control-Max-Age'           => 1800,
            'Access-Control-Allow-Methods'     => 'GET, POST, PATCH, PUT, DELETE, OPTIONS',
            'Access-Control-Allow-Headers'     => 'Authorization, Content-Type, If-Match, If-Modified-Since, If-None-Match, If-Unmodified-Since, X-CSRF-TOKEN, X-Requested-With, platform',
        ];
        // 直接将 header 添加到响应里面
        foreach ($header as $name => $val) {
            header($name . (!is_null($val) ? ':' . $val : ''));
        }

        if (request()->isOptions()) {
            // 如果是预检直接返回响应，后续都不在执行
            exit;
        }
    }
}










/**
 * 拼接资源域名，当前域名或者对象存储域名
 */
if (!function_exists('get_file_domain')) {
    /**
     * 获取本地 public 资源的域名的地址
     * @param string  $url    资源相对地址
     * @param boolean $domain 是否显示域名 或者直接传入域名
     * @return string
     */
    function get_file_domain($url, $domain = true)
    {
        $regex = "/^((?:[a-z]+:)?\/\/|data:image\/)(.*)/i";
        $driver = config('filesystem.default');
        $domainurl = config('filesystem.disks.' . $driver . '.url');
        $url = preg_match($regex, $url) || ($domainurl && stripos($url, $domainurl) === 0) ? $url : $domainurl . $url;
        if ($domain && !preg_match($regex, $url)) {
            $domain = is_bool($domain) ? request()->domain() : $domain;
            $url = $domain . $url;
        }
        return $url;
    }
}


/**
 * 如果是命令行模式，获取当前命令行正在执行的命令
 */
if (!function_exists('current_command')) {
    /**
     * 获取当前命令
     * @param string $type  all 整个命令从桉树，only_name 仅获取命令名称
     */
    function current_command($type = 'all')
    {
        if (!request()->isCli()) {
            return null;
        }

        $argv = $_SERVER['argv'];
        array_shift($argv);

        return $type == 'only_name' ? ($argv[0] ?? null) : $argv;
    }
}








/**
 * 验证自定义签名
 */
if (!function_exists('get_nonce_str')) {
    /**
     * 验证自定义签名 boolean
     *
     * @return void
     */
    function get_nonce_str()
    {
        $license_key = config('app.license_key');
        $referer = request()->header('referer');
        $host = request()->host(true);
        if ($referer) {
            $hosts = parse_url($referer);
            $host = $hosts['host'] ?? ($hosts['path'] ?? $referer);
        }

        $timestamp = time();
        $nonce_str = md5($host . $license_key . $timestamp);

        return $nonce_str. '.' .$timestamp;
    }
}







if (!function_exists('diff_in_time')) {
    /**
     * 计算两个时间相差多少天，多少小时，多少分钟
     * 
     * @param mixed $first 要比较的第一个时间 Carbon 或者时间格式
     * @param mixed $second 要比较的第二个时间 Carbon 或者时间格式
     * @param bool $format 是否格式化为字符串
     * @return string|array 
     */
    function diff_in_time($first, $second = null, $format = true, $simple = false)
    {
        $first = $first instanceof \Carbon\Carbon ? $first : \Carbon\Carbon::parse($first);
        $second = is_null($second) ? \Carbon\Carbon::now() : $second;
        $second = $second instanceof \Carbon\Carbon ? $second : \Carbon\Carbon::parse($second);

        $years = $first->diffInYears($second);
        $days = $first->diffInDays($second);
        $hours = $first->diffInHours($second);
        $minutes = $first->diffInMinutes($second);
        $second = $first->diffInSeconds($second);

        if (!$format) {
            return compact('years', 'days', 'hours', 'minutes', 'second');
        }

        $format_text = '';
        $start = false;
        if ($years) {
            $start = true;
            $format_text .= $years . '年';
        }
        if ($start || $days) {
            $start = true;
            $format_text .= ($days % 365) . '天';
        }

        if ($start || $hours) {
            $start = true;
            $format_text .= ($hours % 24) . '时';
        }
        if ($start || $minutes) {
            $start = true;
            $format_text .= ($minutes % 60) . '分钟';
        }
        if (($start || $second) && !$simple) {
            $start = true;
            $format_text .= ($second % 60) . '秒';
        }

        return $format_text;
    }
}


if (!function_exists('get_sn')) {
    /**
     * 获取唯一编号
     *
     * @param mixed $id       唯一标识
     * @param string $type    类型
     * @return string
     */
    function get_sn($id, $type = '') 
    {
        $id = (string)$id;

        $rand = $id < 9999 ? mt_rand(100000, 99999999) : mt_rand(100, 99999);
        $sn = date('Yhis') . $rand;

        $id = str_pad($id, (24 - strlen($sn)), '0', STR_PAD_BOTH);

        return $type . $sn . $id;
    }
}


if (!function_exists('string_hide')) {
    /**
     * 隐藏部分字符串
     *
     * @param string $string       原始字符串
     * @param int $start    开始位置
     * @return string
     */
    function string_hide($string, $start = 2, $end = 0, $hidden_length = 3) 
    {
        if (mb_strlen($string) > $start) {
            $hide = mb_substr($string, 0, $start) . str_repeat('*', $hidden_length) . ($end > 0 ? mb_substr($string, -$end) : '');
        } else {
            $hide = $string . '***';
        }

        return $hide;
    }
}


if (!function_exists('account_hide')) {
    /**
     * 隐藏账号部分字符串
     *
     * @param string $string       原始字符串
     * @param int $start    开始位置
     * @param int $end    开始位置
     * @return string
     */
    function account_hide($string, $start = 2, $end = 2) 
    {
        $hide = mb_substr($string, 0, $start) . '*****' . mb_substr($string, -$end);
        return $hide;
    }
}


if (!function_exists('get_php')) {
    /**
     * 获取 php
     *
     * @param mixed $id       唯一标识
     * @param string $type    类型
     * @return string
     */
    function get_php() 
    {
        $phpBin = (new \Symfony\Component\Process\PhpExecutableFinder)->find();

        if ($phpBin === false) {
            // 如果都没有获取，尝试拼接宝塔 php 路径
            $phpBin = '/www/server/php/' . substr(str_replace('.', '', PHP_VERSION), 0, 2) . '/bin/php';
        } else {
            if (is_string($phpBin) && strpos(strtolower(PHP_OS), 'win') !== false) {
                // windows 上获取的 php 执行文件会是 php-cgi.exe，这里去掉 -cgi
                $phpBin = str_replace('-cgi', '', $phpBin);
            }
        }

        return $phpBin;
    }
}


if (!function_exists('is_throttle')) {
    /**
     * 判断路由是否需要限流控制
     *
     * @return boolean
     */
    function is_throttle() 
    {
        $noThrottleUris = config('throttle.no_throttle_uris');

        $url = request()->baseUrl();
        foreach ($noThrottleUris as $uri) {
            if (strpos($uri, ':') !== false) {
                if (strpos($url, strstr($uri, ':', true)) !== false) {
                    return false;
                }
            } else {
                if ($url == $uri) {
                    return false;
                }
            }
        }

        return true;
    }
}



    if (!function_exists('lite_config')) {
        /**
         * 获取SheepAdmin配置
         * @param string  $code    配置名
         * @return string
         */
        function lite_config(string $code, $cache = true)
        {
            return \lite\model\Config::getConfigs($code, $cache);
        }
    }

    if (!function_exists('gen_random_str')) {
        /**
         * 随机生成字符串
         * @param int  $length    字符串长度
         * @return bool $upper 默认小写
         */
        function gen_random_str($length = 10, $upper = false)
        {
            if ($upper) {
                $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            } else {
                $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
            }

            $randomString = '';
            for ($i = 0; $i < $length; $i++) {
                $randomString .= $characters[rand(0, strlen($characters) - 1)];
            }
            return $randomString;
        }
    }



    if(!function_exists('encrypt_password')){
        function encrypt_password($password,$salt){
            return md5(md5($password) . $salt);
        }
    }






