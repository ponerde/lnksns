<?php

declare(strict_types=1);

namespace lite\middleware;

use xptech\jwt\exception\TokenExpiredException;
use xptech\jwt\exception\TokenBlacklistGracePeriodException;
use xptech\jwt\exception\TokenBlacklistException;
use xptech\jwt\exception\TokenInvalidException;
use xptech\jwt\middleware\BaseMiddleware;
use lite\facade\Auth;
use lite\exception\LiteException;

class CheckLogin extends BaseMiddleware
{
    public function handle($request, \Closure $next, $guard = null)
    {
        // 验证token
        try {
            // $guard = 'admin';
            $user = Auth::guard($guard)->user();
            if (!$user) {
             
                throw (new LiteException)->loginError();
            }
        } catch (TokenExpiredException $e) { // 捕获token过期
            // 尝试刷新token
            try {
                // 重新获取token
                $token = $this->auth->refresh();
                session('header_authorization', $token);        // 将新的 token 存入 session

                // 重新登录，保证这次请求正常进行
                $payload = $this->auth->auth(false);
                Auth::guard($guard)->loginUsingId($payload['uid']->getValue());

                // return $next($request);
                // return $this->setAuthentication($response, $token);
            } catch (TokenBlacklistException $e) {
                throw (new LiteException)->setMessage('您还没有登录，请先登录2', 403, LiteException::LOGIN_ERROR);
            } catch (TokenBlacklistGracePeriodException $e) { // 捕获黑名单宽限期
                throw (new LiteException)->setMessage('您还没有登录，请先登录3', 403, LiteException::LOGIN_ERROR);
            } catch (TokenExpiredException $e) {
                // 续期失败,需要重新登录
                throw (new LiteException)->setMessage('您的登录已过期, 请重新登录1', 1, LiteException::LOGIN_ERROR);
            }
        } catch (TokenBlacklistException $e) {
            throw (new LiteException)->setMessage('账号已下线,请重新登录', 1, LiteException::LOGIN_ERROR);
        } catch (TokenBlacklistGracePeriodException $e) { // 捕获黑名单宽限期
            throw (new LiteException)->setMessage('您还没有登录，请先登录5', 1, LiteException::LOGIN_ERROR);
        } catch (TokenInvalidException $e) { // token 无效
            throw (new LiteException)->setMessage('令牌无效', 1, LiteException::LOGIN_ERROR);
        }

        return $next($request);
    }
}
