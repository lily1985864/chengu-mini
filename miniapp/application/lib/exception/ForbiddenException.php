<?php
/**
 * Created by PhpStorm.
 * User: lily
 * Date: 8/1/18
 * Time: 2:31 PM
 */

namespace app\lib\exception;

/**
 * token验证失败时抛出此异常
 */
class ForbiddenException extends BaseException
{
    public $code = 403;
    public $msg = '权限不够';
    public $errorCode = 10001;
}