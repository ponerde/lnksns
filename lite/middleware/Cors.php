<?php

declare (strict_types = 1);

namespace lite\middleware;

use Closure;
use think\Config;
use think\Request;
use think\Response;

/**
 * 跨域请求支持
 */
class Cors
{
    protected $cookieDomain;

    protected $header = [
        'Access-Control-Allow-Credentials' => 'true',
        'Access-Control-Max-Age'           => 1800,
        'Access-Control-Allow-Methods'     => 'GET, POST, PATCH, PUT, DELETE, OPTIONS',
        'Access-Control-Allow-Headers'     => 'Authorization, Content-Type, If-Match, If-Modified-Since, If-None-Match, If-Unmodified-Since, X-CSRF-TOKEN, X-Requested-With, platform',
    ];

    public function __construct(Config $config)
    {
        $this->cookieDomain = $config->get('cookie.domain', '');
    }

    /**
     * 允许跨域请求
     * @access public
     * @param Request $request
     * @param Closure $next
     * @param array   $header
     * @return Response
     */
    public function handle($request, Closure $next, ? array $header = [])
    {
        $header = !empty($header) ? array_merge($this->header, $header) : $this->header;

        if (!isset($header['Access-Control-Allow-Origin'])) {
            $origin = $request->header('origin');

            if ($origin && ('' == $this->cookieDomain || strpos($origin, $this->cookieDomain))) {
                $header['Access-Control-Allow-Origin'] = $origin;
            } else {
                $header['Access-Control-Allow-Origin'] = '*';
            }
        }
        if ($request->isOptions()) {
            // 如果是预检直接返回响应，后续都不在执行
            return response()->header($header);
        }

        // 直接将 header 添加到响应里面
        foreach ($header as $name => $val) {
            header($name . (!is_null($val) ? ':' . $val : ''));
        }
        
        return $next($request);
    }
}
