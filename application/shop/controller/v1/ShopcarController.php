<?php
/**
 * Created by PhpStorm.
 * User: sunxiaofeng
 * Date: 2018/9/1
 * Time: 00:23
 */
namespace app\shop\controller\v1;

use app\shop\controller\BaseController;
use think\facade\Hook;

class ShopcarController extends BaseController {

    public function index ()
    {
        //页面需要加载的JS，填写JS名称即可
        $this->assign('pageJs', array(
			    "shopcar"
			)
		);

        return view('/shopcar/index');
    }
}