<?php

namespace app\api\model;

use think\Model;

class BannerItem extends BaseModel
{
    protected $hidden = ['id', 'img_id', 'banner_id', 'from', 'type', 'delete_time', 'update_time'];
    public function images() {
        // 一对一关系用belongsTo
        return $this->belongsTo('Image', 'img_id', 'id');
    }
}
