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

class SearchController extends BaseController {

    public function index ()
    {
        $this->assign('pageJs', array(
			    "search"
			)
		);
        return view('/search/index');
    }
    public function rakedlist ()
    {
        $this->assign('pageJs', array(
			    "rakedlist"
			)
		);
        return view('/search/rakedlist');
    }
}