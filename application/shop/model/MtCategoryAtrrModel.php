<?php
/*
 * Created on 2018年12月11日
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 * 类别属性模型
 */
namespace app\shop\model;

use app\shop\model\BaseModel;

class MtCategoryAtrrModel extends BaseModel {
	
	protected $pk = 'mt_category_atrr_id';
	
	//定义首图获取器
	public function getIconAttr($value) 
	{
		return urldecode($value);
	}
}

