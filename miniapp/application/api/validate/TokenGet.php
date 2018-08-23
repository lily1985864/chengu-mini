<?php
/**
 * Created by PhpStorm.
 * User: lily
 * Date: 8/6/18
 * Time: 10:40 AM
 */

namespace app\api\validate;


class TokenGet extends BaseValidate
{
    protected $rule =[
        'code'=> 'require|isNotEmpty'
    ];

    protected $message = [
        'code' => '没有code无法获得token'
    ];
}