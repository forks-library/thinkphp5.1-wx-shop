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
use think\facade\Session;

class HomeController extends BaseController {

    public function home ()
    {
        //微信授权钩子函数
		Hook::listen('wx_auth', true);
//		var_dump(Session::get('wx_user_id'));
        //页面需要加载的JS，填写JS名称即可
        $this->assign('pageJs', array(
			    "swiper.min",
			    "hello"
			)
		);
		//页面需要加载的额外css，填写css名称即可
        $this->assign('pageCss', array(
			    "swiper.min"
			)
		);

        return view('/home/app');
    }
}