<?php
/**
 * Created by PhpStorm.
 * User: lily
 * Date: 8/1/18
 * Time: 4:52 PM
 */

namespace app\api\model;


class Category extends BaseModel
{
    public function product (){
        return $this->hasMany('Product', 'category_id', 'id');
    }

    public function img(){
        return $this->belongsTo('Image', 'topic_img_id', 'id');
    }
}