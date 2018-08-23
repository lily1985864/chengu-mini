<?php
/**
 * Created by PhpStorm.
 * User: lily
 * Date: 7/31/18
 * Time: 1:05 PM
 */

namespace app\api\model;

use think\Model;

class BaseModel extends Model
{

    protected function prefixImgUrl($value, $data){
        $finalUrl = $value;
        if ($data['from'] ==1) {
            $finalUrl = config('setting.img_prefix').$value;
        }
        return $finalUrl;
    }
}