<?php
/**
 * Created by PhpStorm.
 * User: lily
 * Date: 8/1/18
 * Time: 11:06 AM
 */

namespace app\api\controller\v1;

use app\api\model\Product as ProductModel;
use app\api\validate\Count;
use app\api\validate\IDMustBePositiveInt;
use app\api\validate\PagingParameter;
use app\api\validate\ProductException;
use app\lib\exception\ThemeException;

class Product
{
    /**
     * 获取最新商品列表
     * @param int $count
     * @return false|\PDOStatement|string|\think\Collection
     * @throws ProductException
     * @throws \app\lib\exception\ParameterException
     * @throws \think\Exception
     */
    public function getRecent($count=15){

        (new Count())->goCheck();
        $products = ProductModel::getMostRecent($count);
        if (!$products) {
            throw new ProductException();
        }
        return $products;
    }

    /**根据类目ID获取该类目下所有商品(分页）
     * @param int $id
     * @param int $page
     * @param int $size
     * @return array
     * @throws \app\lib\exception\ParameterException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getProductByCategoryPerPage($id=-1, $page=1, $size=30){
        var_dump($id);
        var_dump($page);
        var_dump($size);
        (new IDMustBePositiveInt($id))->goCheck();
        (new PagingParameter())->goCheck();
        $pagingProducts = ProductModel::getProductByCategoryID($id);
        if ($pagingProducts->isEmpty()) {
            return [
                'current_page'=>$pagingProducts->currentPage(),
                'data'=>[]
            ];
        }
        $data = $pagingProducts
            ->hidden(['summary'])
            ->toArray();
        return [
            'current_page'=>$pagingProducts->currentPage(),
            'data'=>$data
        ];
    }

    public function getProductByCategory($id=-1) {
        (new IDMustBePositiveInt($id))->goCheck();
        $products = ProductModel::getProductByCategoryID($id, false);
        if ($products->isEmpty()){
            throw new ProductException();
        }
        $data = $products->hidden(['summary'])->toArray();
        return $data;
    }

    /**获取商品详情
     * @param $id
     * @return array|false|\PDOStatement|string|\think\Model
     * @throws ProductException
     * @throws \app\lib\exception\ParameterException
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getOne($id)
    {
        (new IDMustBePositiveInt())->goCheck();
        $product = ProductModel::getProductDetail($id);
        if (!$product)
        {
            throw new ProductException();
        }
        return $product;
    }

    public function createOne()
    {
        $product = new ProductModel();
        $product->save(
            [
                'id' => 1
            ]);
    }

    public function deleteOne($id)
    {
        ProductModel::destroy($id);
        //        ProductModel::destroy(1,true);
    }


    /**
     * 获取某分类下全部商品(不分页）
     * @url /product/all?id=:category_id
     * @param int $id 分类id号
     * @return \think\Paginator
     * @throws ThemeException
     */
    public function getAllInCategory($id = -1)
    {
        (new IDMustBePositiveInt())->goCheck();
        $products = ProductModel::getProductsByCategoryID(
            $id, false);
        if ($products->isEmpty())
        {
            throw new ThemeException();
        }
        $data = $products
            ->hidden(['summary'])
            ->toArray();
        return $data;
    }
}