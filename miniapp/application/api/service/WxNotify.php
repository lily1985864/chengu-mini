<?php
/**
 * Created by PhpStorm.
 * User: lily
 * Date: 8/14/18
 * Time: 3:36 PM
 */

namespace app\api\service;

use app\api\model\Product;
use app\api\service\Order as OrderService;
use app\api\model\Order as OrderModel;
use app\lib\enum\OrderStatusEnum;
use think\Db;
use think\Exception;
use think\Loader;
use think\Log;

Loader::import('WxPay.WxPay',EXTEND_PATH, '.API.PHP');

class WxNotify extends \WxPayNotify
{
    /**
     * @param array $data 微信回调函数结果
     * @param string $msg
     * @return \true回调出来完成不需要继续回调，false回调处理未完成需要继续回调
     */
    public function NotifyProcess($data, &$msg)
    {
        if ($data['result_code'] == 'SUCCESS') {
            //检测库存量
            $orderNo = $data['out_trade_no'];
            Db::startTrans();
            try{
                $order = OrderModel::where('order_no', '=', $orderNo)
                    ->find();
                if ($order->status == OrderStatusEnum::UNPAID) {
                    $service = new OrderService();
                    $stockStatus = $service->checkOrderStock($order->id);
                    if ($stockStatus['pass']) {
                        //更新订单状态：已支付
                        $this->updateOrderStatus($order->id, true);
                        //消减库存量
                        $this->reduceStock($stockStatus);
                    } else {
                        //更新订单状态：已支付，但库存不足
                        $this->updateOrderStatus($order->id, false);
                    }
                }
                Db::commit();
                return true;
            }catch(Exception $ex){
                Log::error($ex);
                Db::rollback();
                return false;
            }
        } else {
            return true;
        }
    }

    private function reduceStock($stockStatus)
    {
        foreach ($stockStatus['pStatusArray'] as $singlePStatus)
        {
            Product::where('id', '=', $singlePStatus['id'])
                ->setDec('stock', $singlePStatus['count']);
        }
    }

    private function updateOrderStatus($orderID, $success)
    {
        $paidStatus = $success?
            OrderStatusEnum::PAID:
            OrderStatusEnum::PAID_BUT_OUT_OF;
        OrderModel::where('id', '=', $orderID)->Update(['status'=>$paidStatus]);

    }
}