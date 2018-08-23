<?php
/**
 * Created by PhpStorm.
 * User: lily
 * Date: 7/26/18
 * Time: 11:23 AM
 */

namespace app\api\controller\v2;

class Banner
{
    /**
     * 获取指定id的banner信息
     * @http GET
     * @url /banner/:id
     * @id banner的id
     */
    public function getBanner($id)
    {
       return 'This is V2 version';
    }
}