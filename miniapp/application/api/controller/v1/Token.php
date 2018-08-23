<?php
/**
 * Created by PhpStorm.
 * User: lily
 * Date: 8/6/18
 * Time: 10:36 AM
 */

namespace app\api\controller\v1;


use app\api\service\UserToken;
use app\api\validate\TokenGet;

class Token
{
    public function getToken($code='') {
        (new TokenGet())->goCheck();
        $userToken = new UserToken($code);
        $token = $userToken->get($code);
        $returnValue = [
            'token'=> $token
        ];
        return $returnValue;
    }
}