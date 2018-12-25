<?php
/*
 * Created on 2018年11月22日
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 * 订单模型
 */
namespace app\shop\model;

use app\shop\model\BaseModel;

class MtOrderModel extends BaseModel {
	
	protected $pk = 'mt_order_id';
	protected $autoWriteTimestamp = true;
	
	public function createPayOrder($userId, $goodInfo, $amount, $address, $note) 
	{	
		$orderNo = false;
		
		$this->user_id   = $userId;
		$this->good_info = $goodInfo;
		$this->amount    = $amount;
		$this->address   = $address;
		if (!empty($note)) {
			$this->user_note = $note;
		}
		$this->order_no  = $this->getOrderNo();
		$this->order_status = 1;
		$this->order_time   = time();
			
		if ($this->save()) {
			$orderNo = $this->order_no;
		}
		
		return $orderNo;
	}
	
	/**
	 * 获取订单 order_no
	 * @return string
	 */
	public function getOrderNo()
	{
	    $orderNo = null;
	    // 订单编号
	    $orderNo = 'dgcy'.date('YmdHis').rand(10000,99999);	        
	    $orderCount = $this->where("order_no" , $orderNo)->count();
	    
	    if ($orderCount > 0) {
	    	$this->getOrderNo();
	    }
	    
	    return $orderNo;
	}
	
	//回调修改订单支付状态
	public function updateOrderStatus($orderNo)
	{
		$bln = false;
		
		try {
			$this->startTrans();
			
			$orderObj = $this->where('order_no', $orderNo)->lock(true)->find();
			if ($orderObj && $orderObj->order_status == 1) {
				
				$orderObj->order_status = 2;
				$orderObj->pay_time     = time();
				
				if ($orderObj->save()) {
					$this->commit();
					$bln = $orderObj->good_info;
				} else {
					$this->rollback();
				}
			}
		} catch(\Exception $e) {
			$this->rollback();
			\think\facade\Log::write('订单修改状态失败：'.$e->getMessage());
		}
		
		return $bln;
	}
}
