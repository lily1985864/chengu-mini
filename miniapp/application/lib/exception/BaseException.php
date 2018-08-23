<?php
/**
 * Created by PhpStorm.
 * User: lily
 * Date: 7/26/18
 * Time: 3:48 PM
 */

namespace app\lib\exception;
use think\Exception;

/**
 * Class BaseException
 * 自定义异常类的基类
 */
class BaseException  extends Exception
{
    // HTTP 状态码 404，200
    public $code = 400;
    public $msg = 'Invalid parameters';
    public $errorCode = 999;

    public $shouldToClient = true;

    public function __construct($params = [])
    {
        if (!is_array($params)) {
            throw new Exception('参数必须是数组');
        }
        if (array_key_exists('code', $params)) {
            $this->code = $params['code'];
        }
        if (array_key_exists('msg', $params)) {
            $this->msg = $params['msg'];
        }
        if (array_key_exists('errorCode', $params)) {
            $this->errorCode = $params['errorCode'];
        }
    }


}