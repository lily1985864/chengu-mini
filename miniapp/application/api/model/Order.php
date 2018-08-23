<?php
/**
 * Created by PhpStorm.
 * User: lily
 * Date: 8/1/18
 * Time: 2:49 PM
 */

namespace app\api\model;


class Order extends BaseModel
{
    protected $hidden = ['user_id', 'delete_time', 'update_time'];
    protected $autoWriteTimestamp = true;

    public function getSnapItemsAttr($value) {
        if(empty($value)) {
            return null;
        }
        return json_decode($value);
    }

    public function getSnapAddressAttr($value) {
        if(empty($value)) {
            return null;
        }
        return json_decode($value);
    }

    public static function getSummaryByUser($uid, $page=1, $size=15){
        //返回Paginator对象
        return self::where('user_id', '=', $uid)
            ->order('create_time desc')
            ->paginate($size, true, ['page'=> $page]);
    }

    public static function getSummaryByPage($page=1, $size=15) {
        return self::order('create_time desc')
            ->paginate($size, true, ['page'=>$page]);
    }

    public function products(){
        return $this->belongsToMany('Product', 'order_product', 'product_id','id');
    }
}