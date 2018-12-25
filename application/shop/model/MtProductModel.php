<?php
/*
 * Created on 2018年11月2日
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 * 商品模型
 */
namespace app\shop\model;

use app\shop\model\BaseModel;

class MtProductModel extends BaseModel {
	
	protected $pk = 'mt_product_id';
	
	//关联模型， 一对多
	public function productAttrs()
	{
		return $this->hasMany('ProductAndAtrrfieldModel', 'product_id', $this->pk);
	}
	
	//定义首图获取器
	public function getDisplayPicAttr($value) 
	{
		return urldecode($value);
	}
	
	//定义多图获取
	public function getImgsAttr($value)
	{
		$images = urldecode($value);
		
		if (!empty($images) && strpos($images, ';') !== false) {
			return explode(';', $images);
		}
		
		return $images;
	}
	
	public function getInfoImgsAttr($value)
	{
		$images = urldecode($value);
		
		if (!empty($images) && strpos($images, ';') !== false) {
			return explode(';', $images);
		}
		
		return $images;
	}
	
	//解析参数
	public function getParameterAttr($value)
	{
		$param = htmlspecialchars_decode($value);
		
		if (!empty($param) && strpos($param, '</p>') !== false) {
			$param  = explode('</p>', $param);
			$param1 = array();
			foreach($param as $key => $val) {
				$val = str_replace('<p>', '', $val);
				$val = trim($val);
				
				if (!empty($val) && strpos($val, '<br />') === false) {
					$param1[$key] = explode('：', $val);
				}
			}
			
			return $param1;
		}
	}
	
	//解析说明
	public function getNotesAttr($value)
	{
		return htmlspecialchars_decode($value);
	}
}
