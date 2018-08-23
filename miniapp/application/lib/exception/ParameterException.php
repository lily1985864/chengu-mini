<?php
/**
 * Created by PhpStorm.
 * User: lily
 * Date: 7/27/18
 * Time: 9:40 AM
 */

namespace app\lib\exception;


class ParameterException extends BaseException
{
    public $code = 400;
    public $msg = '参数错误';
    public $error_code = 10000;
}