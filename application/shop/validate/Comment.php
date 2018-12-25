<?php
/*
 * Created on 2018年12月14日
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
namespace app\shop\validate;

use think\Validate;

class Comment extends Validate {
	
	protected $rule = [
		'commentId'=> 'require|number',
		'userId'   => 'require',
		'comment'  => 'require',
	];
	
	protected $message = [
		'commentId'   => 'ID不能为空',
		'userId'      => '用户ID不能为空',
		'comment'     => '评论内容不能为空',
	];
}