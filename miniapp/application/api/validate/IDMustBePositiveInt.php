<?php
/**
 * Created by PhpStorm.
 * User: lily
 * Date: 7/26/18
 * Time: 1:20 PM
 */

namespace app\api\validate;


class IDMustBePositiveInt extends BaseValidate
{
    protected $message = [
        'id' => 'id必须为正整数'
    ];

    protected $rule = [
        'id' => 'require|isPositiveInteger'
    ];

}