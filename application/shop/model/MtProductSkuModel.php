<?php
/*
 * Created on 2018年11月2日
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 * 商品SKU模型
 */
namespace app\shop\model;

use app\shop\model\BaseModel;

class MtProductSkuModel extends BaseModel {
	
	protected $pk = 'mt_product_sku_id';
	
	public function getDisplayPicAttr($value)
	{
		return urldecode($value);
	}
	
	//减少库存
	public function reduceStock($id, $num)
	{
		$bln = false;
		$data = $this->where($this->pk, $id)->field(['repertory'])->lock(true)->find();
		
		if ($data && ($data->repertory > $num)) {
			$stock = $data->repertory;
			$data->repertory = ($stock - $num);
				
			if ($data->save()) {
				$bln = true;
			} 
		}
		
		return $bln;
	}
	
	//增加库存
	public function rollBackStock($id, $num)
	{
		$bln  = false;
		$data = $this->where($this->pk, $id)->field(['repertory'])->lock(true)->find();
		
		if ($data){
			$stock = $data->repertory;
			$data->repertory = ($stock + $num);
			
			if ($data->save()) {
				$bln = true;
			}
		}
		
		return $bln;
	}
	
}
