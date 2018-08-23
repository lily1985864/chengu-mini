<?php
/**
 * Created by PhpStorm.
 * User: lily
 * Date: 8/1/18
 * Time: 10:04 AM
 */

namespace app\lib\exception;


class ThemeException extends BaseException
{
    public $code = 404;
    public $msg = '指定主题不存在，请检查主题ID';
    public $error_code = 30000;
}