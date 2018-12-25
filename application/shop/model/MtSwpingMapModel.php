<?php
/*
 * Created on 2018年11月2日
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 * 轮播图模型
 */
namespace app\shop\model;

use app\shop\model\BaseModel;

class MtSwpingMapModel extends BaseModel {
	
	protected $pk = 'mt_swping_map_id';
	
	//定义获取跳转连接
	public function getJumpUrlAttr($value)
	{
		return urldecode($value);
	}
	
	//
	public function getDisplayPicAttr($value)
	{
		return urldecode($value);
	}
}
