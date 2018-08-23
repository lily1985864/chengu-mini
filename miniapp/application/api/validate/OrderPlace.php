<?php
/**
 * Created by PhpStorm.
 * User: lily
 * Date: 8/8/18
 * Time: 12:41 PM
 */

namespace app\api\validate;


use app\lib\exception\ParameterException;

class OrderPlace extends BaseValidate
{
    protected $rule = [
        'product' => 'checkProducts'
    ];

    protected $singleRule = [
        'product' => 'require|isPositiveInteger',
        'count' => 'require|isPositiveInteger'
    ];

    protected function checkProducts($values)
    {
        if (is_array($values)) {
            throw new ParameterException([
                'msg'=>'商品参数不正确'
            ]);
        }
        if (empty($values)) {
            throw new ParameterException([
                'msg'=>'商品列表不能为空'
            ]);
        }
        foreach ($values as $value) {
            $this->checkProduct($value);
        }

    }

    protected function checkProduct($value)
    {
        $validate = new BaseValidate($this->singleRule);
        $result = $validate->check($value);
        if (!$result){
            throw new ParameterException([
                'msg'=>'商品参数不正确'
            ]);
        }
    }

}