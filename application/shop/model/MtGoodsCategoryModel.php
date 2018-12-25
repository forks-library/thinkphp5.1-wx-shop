<?php
/*
 * Created on 2018年12月11日
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 * 商品类别模型
 */
namespace app\shop\model;

use app\shop\model\BaseModel;

class MtGoodsCategoryModel extends BaseModel {
	
	protected $pk = 'mt_goods_category_id';
	
	public function categoryAtrr()
	{
		return $this->hasMany('MtCategoryAtrrModel', 'category_id', 'mt_goods_category_id');
	}
	
	//定义首图获取器
	public function getIconAttr($value) 
	{
		return urldecode($value);
	}
}

