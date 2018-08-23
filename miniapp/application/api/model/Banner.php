<?php
/**
 * Created by PhpStorm.
 * User: lily
 * Date: 7/26/18
 * Time: 3:14 PM
 */

namespace app\api\model;

use think\Model;

class Banner extends BaseModel
{
    protected $hidden = ['delete_time', 'update_time'];

    public function items()
    {
        //一对多关系用hasMany
        return $this->hasMany('BannerItem', 'banner_id', 'id');
    }

    public static function getBannerByID($id)
    {
        $result = self::with(['items', 'items.images'])->find($id);

        return $result;
    }
}