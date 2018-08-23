<?php

namespace app\api\model;

class Theme extends BaseModel
{
    protected $hidden = ['head_img_id', 'topic_img_id','delete_time', 'update_time'];

    /**
     * 关联Image
     * 要注意belongsTo和hasOne的区别
     * 带外键的表一般定义belongsTo，另外一方定义hasOne
     */
    public function topicImg()
    {
        return $this->belongsTo('Image', 'topic_img_id', 'id');
    }

    public function headImg()
    {
        return $this->belongsTo('Image', 'head_img_id', 'id');
    }

    public function products (){
        return $this->belongsToMany('Product', 'theme_product', 'product_id');
    }

    public static function getThemeList($ids) {
        return self::with('topicImg,headImg')->select($ids);
    }

    public static function getThemeWithProducts($id) {
        return self::with('products,topicImg,headImg')->find($id);
    }

    public static function addThemeProduct($t_id, $p_id) {
        $models = self::checkRelationExist($t_id, $p_id);
        $models['theme']->products()->attach($p_id);
        return true;
    }

    public static function deleteThemeProduct($t_id, $p_id) {
        $models = self::checkRelationExist($t_id, $p_id);
        $models['theme']->products()->detach($p_id);
        return true;
    }

    private static function checkRelationExist($themeID, $productID)
    {
        $theme = self::get($themeID);
        if (!$theme)
        {
            throw new ThemeException();
        }
        $product = Product::get($productID);
        if (!$product)
        {
            throw new ProductException();
        }
        return [
            'theme' => $theme,
            'product' => $product
        ];

    }
}
