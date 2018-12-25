<?php
/*
 * Created on 2018年12月11日
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
namespace app\shop\controller\v1\api;

use think\Controller;
use think\facade\Session;
use app\shop\model\WxUserResourceModel;

class UserController extends Controller {
	
	//获取类别列表
	public function getUserInfo()
	{
		$userId = Session::get('wx_user_id');
		$user   = WxUserResourceModel::where('user_wx_id', $userId)
									->field(['nick_name', 'head_img_url', 'sex'])
									->find();
		return json(array('data' => $user));
	}
}

