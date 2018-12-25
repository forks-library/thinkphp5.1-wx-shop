<?php
namespace app\shop\controller\v1;

use app\shop\controller\BaseController;
use think\facade\Hook;
use think\facade\Session;

class GoodslistController extends BaseController {

    public function index ()
    {
		//微信授权钩子函数
//        var_dump(Session::get('wx_user_id'));
        //页面需要加载的JS，填写JS名称即可
        $this->assign('pageJs', array(
			    "goodslist"
			)
		);
        return view('/goodslist/index');
    }
}