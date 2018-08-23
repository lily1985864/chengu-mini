<?php
/**
 * Created by PhpStorm.
 * User: lily
 * Date: 8/1/18
 * Time: 1:14 PM
 */

namespace app\api\controller\v1;

use app\api\model\Category as CategoryModel;
use app\lib\exception\CategoryException;

class Category
{

    public function getAllCategories() {

        $category = CategoryModel::all([],'img');
        if (!$category) {
            throw new CategoryException();
        }
        return $category;
    }
}