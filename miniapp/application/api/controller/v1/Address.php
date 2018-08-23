<?php
/**
 * Created by PhpStorm.
 * User: lily
 * Date: 8/7/18
 * Time: 11:29 AM
 */

namespace app\api\controller\v1;

use app\api\controller\BaseController;
use app\api\model\User as UserModel;
use app\api\model\UserAddress;
use app\api\service\Token as TokenService;
use app\api\validate\AddressNew;
use app\lib\exception\SuccessMessage;
use app\lib\exception\UserException;

class Address extends BaseController
{
    protected $beforeActionList = [
        'checkPrimaryScope' => ['only' => 'createOrUpdateAddress,getUserAddress']
    ];

    /**
     * 获取用户地址信息
     * @return UserAddress
     * @throws UserException
     */
    public function getUserAddress(){
        $uid = TokenService::getCurrentUid();
        $userAddress = UserAddress::where('user_id', $uid)
            ->find();
        if(!$userAddress){
            throw new UserException([
                'msg' => '用户地址不存在',
                'errorCode' => 60001
            ]);
        }
        return $userAddress;
    }


    /**
     * 创建或更新收货地址
     */
    public function createOrUpdateAddress()
    {
        $validate = new AddressNew();
        $validate->goCheck();
        //1. 根据token获取用户uid
        //2. 判断用户是否存在
        //      如果不存在，抛出异常
        //      如果存在，获取用户提交的地址信息
        //3. 根据用户地址信息是否存在，判断是添加地址还是更新地址
        $uid = TokenService::getCurrentUid();
        $user = UserModel::get($uid);
        if (!$user){
            throw new UserException([
                'code' => 404,
                'msg' => '用户收货地址不存在',
                'errorCode' => 60001
            ]);
        }
        $data = $validate->getDataByRule(input('post.'));
        $userAddress = $user->address;
        var_dump($user);
        if (!$userAddress) {
            $user->address()->save($data);
        } else {
            //更新操作
            $user->address->save($data);
        }
        return new SuccessMessage();

    }
}