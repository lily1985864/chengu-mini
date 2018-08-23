<?php
/**
 * Created by PhpStorm.
 * User: lily
 * Date: 8/8/18
 * Time: 5:23 PM
 */

namespace app\api\service;

use app\api\model\OrderProduct as OrderProductModel;
use app\api\model\Order as OrderModel;
use app\api\model\OrderProduct;
use app\api\model\Product;
use app\api\model\UserAddress;
use app\lib\exception\OrderException;
use app\lib\exception\UserException;
use think\Db;
use think\Exception;

class Order
{
    //订单中商品列表（客户端传递过来的product_id,count）
    protected $oProducts;

    //数据库中查询出的产品列表(真实的）
    protected $products;

    protected $uid;

    /**下单
     * @param $uid
     * @param $oProducts
     */
    public function place($uid, $oProducts)
    {
        //对比oProducts和products，确定库存
        //查询products
        $this->oProducts = $oProducts;
        $this->products = self::getProductsByOrder($oProducts);
        $this->uid = $uid;
        $status = $this->getOrderStatus();
        if (!$status['pass']) {
            $status['order_id'] = -1;
            return $status;
        }
        //开始创建订单快照
        $orderSnap = $this->snapOrder($status);

        //生成订单，写入数据库
        $order = $this->createOrder($orderSnap);
        $order['pass'] = true;
        return $order;
    }

    private function createOrder($snap)
    {
        Db::startTrans();
        try
        {
            //save to Order table
            $orderNo = $this->makeOrderNo();
            $order = new OrderModel();
            $order->user_id = $this->uid;
            $order->order_no = $orderNo;
            $order->total_price = $snap['orderPrice'];
            $order->total_count = $snap['totalCount'];
            $order->snap_img = $snap['snapImg'];
            $order->snap_name = $snap['snapName'];
            $order->snap_address = $snap['snapAddress'];
            $order->snap_items = json_encode($snap['pStatus']); //把数组序列化
            $order->save();

            //save to order_product table
            $orderID = $order->id;
            $create_time = $order->create_time;
            foreach ($this->oProducts as &$oProduct) {
                $oProduct['order_id'] = $orderID;
            }
            $orderProduct = new OrderProductModel();
            $orderProduct->saveAll($this->oProducts);
            Db::commit();
            return [
                'order_no' => $orderNo,
                'order_id' => $orderID,
                'create_time' => $create_time
            ];
        } catch (Exception $ex) {
            Db::rollback();
            throw $ex;
        }
    }

    public static function makeOrderNo()
    {
        $yCode = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J');
        $orderSn =
            $yCode[intval(date('Y')) - 2017] . strtoupper(dechex(date('m'))) . date(
                'd') . substr(time(), -5) . substr(microtime(), 2, 5) . sprintf(
                '%02d', rand(0, 99));
        return $orderSn;
    }

    // 预检测并生成订单快照
    private function snapOrder()
    {
        // status可以单独定义一个类
        $snap = [
            'orderPrice' => 0,
            'totalCount' => 0,
            'pStatus' => [],
            'snapAddress' => json_encode($this->getUserAddress()),
            'snapName' => $this->products[0]['name'],
            'snapImg' => $this->products[0]['main_img_url'],
        ];

        if (count($this->products) > 1) {
            $snap['snapName'] .= '等';
        }


        for ($i = 0; $i < count($this->products); $i++) {
            $product = $this->products[$i];
            $oProduct = $this->oProducts[$i];

            $pStatus = $this->snapProduct($product, $oProduct['count']);
            $snap['orderPrice'] += $pStatus['totalPrice'];
            $snap['totalCount'] += $pStatus['count'];
            array_push($snap['pStatus'], $pStatus);
        }
        return $snap;
    }

    private function getUserAddress()
    {
        $userAddress = UserAddress::where('user_id', '=', $this->uid)->find();
        if (!$userAddress) {
            throw new UserException([
                'msg' => '用户收货地址不存在，下单失败',
                'errorCode' => 60001
            ]);
        }
        return $userAddress->toArray();
    }

    private function getOrderStatus()
    {
        $status = [
            'pass' => true,  //代表是否有商品库存不足
            'orderPrice' => 0,
            'totalCount' => 0, //所有商品数量总和
            'pStatusArray' => [] //保存了订单里所有商品的详细信息
        ];
        foreach($this->oProducts as $oProduct)
        {
            $product = $this->getProductStatus(
                $oProduct['product_id'], $oProduct['count'],$this->products);
            if (!$product['hasStock']){
                $status['pass']=false;
            }
            $status['orderPrice'] += $product['totalPrice'];
            $status['totalCount'] += $product['count'];
            array_push($status['pStatusArray'], $product);
        }
        return $status;
    }

    // 单个商品库存检测
    private function snapProduct($product, $oCount)
    {
        $pStatus = [
            'id' => null,
            'name' => null,
            'main_img_url'=>null,
            'count' => $oCount,
            'totalPrice' => 0,
            'price' => 0
        ];

        $pStatus['counts'] = $oCount;
        // 以服务器价格为准，生成订单
        $pStatus['totalPrice'] = $oCount * $product['price'];
        $pStatus['name'] = $product['name'];
        $pStatus['id'] = $product['id'];
        $pStatus['main_img_url'] =$product['main_img_url'];
        $pStatus['price'] = $product['price'];
        return $pStatus;
    }

    private function getProductStatus($oPID, $oCount, $products)
    {

        $pIndex = -1;
        //商品详细信息
        $pStatus =[
            'id'=> null,
            'hasStock'=> false,
            'count'=> 0,
            'name'=> '',
            'totalPrice'=> 0
        ];
        for($i=0; $i<count($products);$i++)
        {
            if($oPID == $products[$i]['id']) {
                $pIndex = $i;
            }
        }

        if($pIndex==-1) {
            throw new OrderException([
                'msg'=>'id为'.$oPID.'商品不存在，创建订单失败'
            ]);
        } else {
            $product = $products[$pIndex];
            $pStatus['id']=$product['id'];
            $pStatus['name']=$product['name'];
            $pStatus['count']=$oCount;
            $pStatus['totalPrice']=$product['price'] * $oCount;
            if ($product['stock']-$oCount >= 0) {
                $pStatus['hasStock']=true;
            }
        }
        return $pStatus;
    }

    //根据订单信息查找真实的Product信息
    private function getProductsByOrder($oProducts)
    {
        $oPIDs = [];
        foreach ($oProducts as $oProduct) {
            array_push($oPIDs, $oProduct['product_id']);
        }
        $products = Product::all($oPIDs)
            ->visible(['id', 'price', 'stock', 'name', 'main_img_url'])
            ->toArray();

        return $products;
    }

    public function checkOrderStock($orderID)
    {
        //获取oProducts
        $this->oProducts = OrderProductModel::where('order_id', '=', $orderID)
            ->select();
        //获取Products
        $this->products = self::getProductsByOrder($this->oProducts);
        return $this->getOrderStatus();
    }
}