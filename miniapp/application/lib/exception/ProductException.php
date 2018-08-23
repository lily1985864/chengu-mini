<?php
/**
 * Created by PhpStorm.
 * User: lily
 * Date: 8/1/18
 * Time: 11:17 AM
 */

namespace app\api\validate;

use app\lib\exception\BaseException;

class ProductException extends BaseException
{
    public $code = 404;
    public $msg = '指定商品不存在，请检查商品ID';
    public $error_code = 20000;
}