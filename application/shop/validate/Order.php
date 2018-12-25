<?php
/*
 * Created on 2018年11月22日
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

namespace app\shop\validate;

use think\Validate;

class Order extends Validate {
	
	protected $rule = [
		'goods'   => 'require',
		'amount'  => 'require',
		'address' => 'require',
	];
	
	protected $message = [
		'goods'   => '不能为空',
		'amount'  => '不能为空，必须为数值',
		'address' => '不能为空',
	];
}