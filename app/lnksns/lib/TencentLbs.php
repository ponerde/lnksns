<?php

namespace app\lnksns\lib;


use GuzzleHttp\Client;
use lite\service\ConfigService;

class TencentLbs
{

    public static function ip($ip = null)
    {
        $http = new Client();
        $response = $http->get('https://apis.map.qq.com/ws/location/v1/ip', [
            'query' => [
                'ip' => $ip,
                'key' => ConfigService::get('lnksns', 'lnk_tx_kay',''),
            ],
        ]);
        $result = json_decode($response->getBody(), true);
        return $result;
    }

}
