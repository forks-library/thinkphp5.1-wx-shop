<?php
/*
 * Created on 2018年11月14日
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 * 购物车模型
 */

namespace app\shop\model;

use app\shop\model\BaseModel;

class MtShopCartModel extends BaseModel {
	
	protected $pk = "mt_shop_cart_id";
	
	/**
	 * 加入购物车
	 * @param string $userId
	 * @param string $skuId
	 * @param string $num
	 * @return true/false
	 */
	public function addGoodsToCart($userId, $skuId, $num) {
		$bln = false;
		
		try {
			//开启事物
			$this->startTrans();
			
			//查询购物车数据通过用户
			$data = $this->where('user_id', $userId)->where('sku_id', $skuId)->field(['sku_num'])->find();
			if ($data) {
				$skunum = $data->sku_num;
				
				$data->sku_num     = ((int)$num + $skunum);	
				$data->update_date = time();
							
				if ($data->save()) {
					$this->commit();
					$bln = true;
				} else {
					$this->rollback();
				}
			} else {
				$this->user_id     = $userId;
				$this->sku_id      = $skuId;
				$this->sku_num     = $num;
				$this->create_date = time();
				
				if ($this->save()) {
					$this->commit();
					$bln = true;
				} else {
					$this->rollback();
				}
			}
			
		} catch(\Exception $e) {
			$this->rollback();
			\think\facade\Log::write('购物车加入失败：'.$e->getMessage());
		}
		
		return $bln;
	}
	
	//一对一
	public function mtProductSku() 
	{
		return $this->hasOne('MtProductSkuModel', 'mt_product_sku_id', 'sku_id');
	}
}


