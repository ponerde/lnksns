<?php
declare (strict_types=1);

namespace app\install\service;


use app\BaseController;
use think\facade\Cache;
use think\facade\Env;


class InstallService 
{

        public static function putEnv($databaseEnv)
        {
            $applyDbEnv = [
                'DATABASE_HOSTNAME' => $databaseEnv['hostname'],
                'DATABASE_DATABASE' => $databaseEnv['database'] ?? '',
                'DATABASE_USERNAME' => $databaseEnv['username'] ?? '',
                'DATABASE_PASSWORD' => $databaseEnv['password'] ?? '',
                'DATABASE_HOSTPORT' => $databaseEnv['hostport'] ?? '',
                'DATABASE_PREFIX' => $databaseEnv['prefix'] ?? '',
            ];
            $env = new Env();
            $env::load(root_path().'.example.env');
        
            $exampleArr = $env::get();

            // var_dump($exampleArr);
            $envLine = array_merge($exampleArr, $applyDbEnv);
    
            $content = '';
            $lastPrefix = '';
    
            global $uniqueSalt;
    
            foreach ($envLine as $index => $value) {
    
                if ($index == 'PROJECT.UNIQUE_IDENTIFICATION' && !empty($uniqueSalt)) {
                    $value = $uniqueSalt;
                }
    
                @list($prefix, $key) = explode('_', $index);
    
                if ($prefix != $lastPrefix && $key != null) {
                    if ($lastPrefix != '')
                        $content .= "\n";
                    $content .= "[$prefix]\n";
                    $lastPrefix = $prefix;
                }
    
                if ($prefix != $lastPrefix && $key == null) {
                    $content .= "$index = \"$value\"\n";
                } else {
                    $content .= "$key = \"$value\"\n";
                }
            }
    
            if (!empty($content)) {
                $envFilePath = root_path().'.env';
                file_put_contents($envFilePath, $content);
            }
        }
}