<?php
namespace app\shop\controller\v1;

use app\shop\controller\BaseController;
use think\facade\Hook;
use think\facade\Session;

class GoodsController extends BaseController {

    public function index ()
    {
		//微信授权钩子函数
//        var_dump(Session::get('wx_user_id'));
        //页面需要加载的JS，填写JS名称即可
        $this->assign('pageJs', array(
			    "swiper-4.2.6.min",
			    "goods"
			)
		);
		//页面需要加载的额外css，填写css名称即可
        $this->assign('pageCss', array(
			    "swiper-4.2.6.min"
			)
		);
        return view('/goods/index');
    }
}