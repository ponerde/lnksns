<?php
namespace lite\exception;

use Exception;

/**
 * 抛出业务错误 不记录日志，只开发环境显示错误信息， 一般用于参数合法性校验
 */
class LiteDebugException extends Exception
{
    
}
