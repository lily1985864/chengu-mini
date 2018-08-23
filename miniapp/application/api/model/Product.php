<?php

namespace app\api\model;

class Product extends BaseModel
{
    protected $autoWriteTimestamp = 'datetime';
    protected $hidden = [
        'delete_time', 'main_img_id', 'pivot', 'from', 'category_id',
        'create_time', 'update_time'];
    //'pivot'多对多关系

    public function imgs()
    {
        return $this->hasMany('ProductImage', 'product_id', 'id');
    }

    public function getMainImgUrlAttr($value, $data)
    {
        return $this->prefixImgUrl($value, $data);
    }

    public function properties()
    {
        return $this->hasMany('ProductProperty', 'product_id', 'id');
    }

    public static function getMostRecent($count) {
        return self::limit($count)->order('create_time desc')->select();
    }

    /**获取某分类下商品
     * @param $category_id
     * @param bool $paginate
     * @param int $page
     * @param int $size
     * @return false|\PDOStatement|string|\think\Collection|\think\Paginator
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getProductsByCategoryID
    ($category_id, $paginate=true, $page=1, $size=30) {
        $query=self::where('category_id','=',$category_id);
        if (!$paginate) {
            return $query->select();
        } else {
            // paginate 第二参数true表示采用简洁模式，简洁模式不需要查询记录总数
            return $query->paginate(
                $size, true, [
                'page' => $page
            ]);
        }
    }

    /**获取商品详情
     * @param $id
     * @return array|false|\PDOStatement|string|\think\Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getProductDetail($id) {
        return self::with(
            [
                'imgs' => function ($query) {
                    $query->with(['imgUrl'])->order('order', 'asc');
                }])
            ->with('properties')
            ->find($id);
    }
}
