<?php
namespace app\shop\controller\v1;

use app\shop\controller\BaseController;
use think\facade\Hook;

class SortController extends BaseController {

    public function index ()
    {
        //微信授权钩子函数
		// $openid = Hook::listen('wx_auth', false);
        $this->assign('openid', '111111');

        //页面需要加载的JS，填写JS名称即可
        $this->assign('pageJs', array(
        		"swiper.min",
			    "sort"
			)
		);
		//页面需要加载的额外css，填写css名称即可
        $this->assign('pageCss', array(
			    "swiper.min"
			)
		);
        return view('/sort/index');
    }
}