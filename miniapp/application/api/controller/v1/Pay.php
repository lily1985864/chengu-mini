<?php
/**
 * Created by PhpStorm.
 * User: lily
 * Date: 8/13/18
 * Time: 8:56 PM
 */

namespace app\api\controller\v1;


use app\api\service\Pay as PayService;
use app\api\controller\BaseController;
use app\api\service\WxNotify;
use app\api\validate\IDMustBePositiveInt;

class Pay extends BaseController
{
    protected $beforeActionList = [
        'checkExclusiveScope' => ['only' => 'getPreOrder']
    ];

    /**
     * 向微信发送预订单请求
     * @param $id
     */
    public function getPreOrder($id='')
    {
        (new IDMustBePositiveInt())->goCheck($id);
        $pay = new PayService($id);
        return $pay->pay();
    }

    public function receiveNotify() {
        //通知频率为15/30/180/1800/1800/1800/3600 单位：秒

        //1. 检查库存量
        //2. 更新订单状态
        //3. 减少库存
        //post请求，xml格式，不会携带参数
        $notify = new WxNotify();
        $notify->Handle();
    }

}