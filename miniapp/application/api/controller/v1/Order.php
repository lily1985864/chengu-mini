<?php
/**
 * Created by PhpStorm.
 * User: lily
 * Date: 8/7/18
 * Time: 11:25 AM
 */

namespace app\api\controller\v1;

use app\api\controller\BaseController;
use app\api\service\Order as OrderService;
use app\api\service\Token as TokenService;
use app\api\model\Order as OrderModel;
use app\api\validate\IDMustBePositiveInt;
use app\api\validate\OrderPlace;
use app\api\validate\PagingParameter;
use app\lib\exception\OrderException;

class Order extends BaseController
{

    protected $beforeActionList = [
        'checkExclusiveScope' => ['only' => 'placeOrder'],
        'checkPrimaryScope' => ['only' => 'getDetail, getSummaryByUser']
    ];

    public function getDetail($orderID)
    {
        (new IDMustBePositiveInt($orderID))->goCheck();
        $orderDetail = OrderModel::get($orderID);
        if (!$orderDetail) {
            throw new OrderException();
        }
        return $orderDetail->hidden('prepay_id');
    }

    public function getSummaryByUser($page=1, $size=15)
    {
        (new PagingParameter())->goCheck();
        $uid = TokenService::getCurrentUid();
        $pagingOrders = OrderModel::getSummaryByUser($uid, $page, $size);
        if ($pagingOrders->isEmpty()){
             return [
                 'data' => [],
                 'currentPage' => $pagingOrders->getCurrentPage()
             ];
        } else {
            $data = $pagingOrders->hidden(['snap_items', 'snap_address', 'prepay_id'])->toArray();
            return [
            'data' => $data,
            'currentPage' => $pagingOrders->getCurrentPage()
            ];
        }
    }

    public function placeOrder(){
        (new OrderPlace())->goCheck();
        $products = input('post.products/a');
        $uid = TokenService::getCurrentUid();

        $order = new OrderService();
        $status = $order->place($uid, $products);
        return $status;
    }
}