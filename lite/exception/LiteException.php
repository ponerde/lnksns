<?php

namespace lite\exception;

use Exception;

/**
 * 抛出正常业务错误 不记录日志，开发环境/生产环境都显示错误信息
 */
class LiteException extends Exception
{
    const LOGIN_ERROR = 401;
    const ACCESS_ERROR = 403;

    /**
     * 自定义错误码
     */
    public $error_code = 1;
    /**
     * http 状态码
     */
    public $status_code = 200;
    /**
     * 错误信息
     */
    public $message = '';

    /**
     * 多样化错误信息组合，非必须使用， throw (new SheepException)->setMessage("错了", 20, 404);
     * 
     * @param string $msg 错误信息
     * @param integer $error_code 自定义错误码
     * @param integer $statu_code http 状态码
     */
    public function setMessage($msg = '', $error_code = 1, $status_code = 200)
    {
        // 错误信息组合模式
        $this->message = $msg;
        $this->error_code = $error_code;
        $this->status_code = $status_code;

        return $this;
    }

   

    public function loginError()
    {
        return $this->setMessage("您还没有登录，请先登录", 1, self::LOGIN_ERROR);
    }

    public function accessError()
    {
        return $this->setMessage("无操作权限", 1, self::ACCESS_ERROR);
    }
}
