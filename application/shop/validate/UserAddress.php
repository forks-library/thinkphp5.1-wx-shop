<?php
/*
 * Created on 2018年11月22日
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

namespace app\shop\validate;

use think\Validate;

class UserAddress extends Validate {
	
	protected $rule = [
		'userName'     => 'require',
		'phone'        => 'require|number|length:11',
		'address'      => 'require',
		'provinceCity' => 'require'
	];
	
	protected $message = [
		'userName'     => '用户名不能为空并且长度不能超过25',
		'phone'        => '手机号不正确',
		'address'      => '详细地址不能为空',
		'provinceCity' => '省市不能为空'
	];
}