<?php

namespace app\api\controller\v1;

use app\api\validate\IDCollection;
use app\api\model\Theme as ThemeModel;
use app\api\validate\IDMustBePositiveInt;
use app\api\validate\ThemeProduct;
use app\lib\exception\SuccessMessage;
use app\lib\exception\ThemeException;

class Theme
{
    /**
     * @url /theme?ids=id1,id2,id3,...
     * @param string $ids
     * @return 一组Theme模型
     * @throws ThemeException
     * @throws \app\lib\exception\ParameterException
     * @throws \think\Exception
     */
    public function getThemeList($ids=''){
        (new IDCollection())->goCheck();
        $ids = explode(',', $ids);
        $themeList = ThemeModel::getThemeList($ids);
        if (!$themeList) {
            throw new ThemeException();
        }
        return $themeList;
    }

    public function getThemeWithProduct($id){
        (new IDMustBePositiveInt())->goCheck();

        $theme = ThemeModel::getThemeWithProducts($id);
        if (!$theme) {
            throw new ThemeException();
        }
        return $theme;
    }

    public function addThemeProduct($t_id, $p_id) {
        (new ThemeProduct())->goCheck();
        ThemeModel::addThemeProduct($t_id, $p_id);
        return new SuccessMessage();
    }

    public function deleteThemeProduct($t_id, $p_id) {
        (new ThemeProduct())->goCheck();
        ThemeModel::deleteThemeProduct($t_id, $p_id);
        return new SuccessMessage();
    }
}
