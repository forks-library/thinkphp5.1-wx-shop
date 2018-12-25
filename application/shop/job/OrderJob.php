<?php
/*
 * Created on 2018年12月1日
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

namespace app\shop\job;

use think\queue\Job;
use app\shop\model\MtOrderModel;
use app\shop\logic\OrderLogic;

class OrderJob {
	
	/**
	 * 关闭订单任务
	 * @param Job            $job      当前的任务对象
     * @param array|mixed    $data     发布任务时自定义的数据
	 */
	public function closeOrder(Job $job, $data)
	{
		//判断订单状态不是待支付状态 删除任务；
		if (!$this->checkJobStatus($data)) {
			$job->delete();
		} else {
			//执行业务逻辑 取消订单并回滚所有库存
			$bln = $this->doColseOrder($data);
			//如果任务失败
	//		if (!$bln) {
	//			$job->release(60);
	//		}
	        //如果任务执行成功后 记得删除任务，不然这个任务会重复执行，直到达到最大重试次数后失败后，执行failed方法
        	$job->delete();
		}
		if ($job->attempts() > 3) {
			
			 \think\facade\Log::write("取消订单队列执行失败:{$data}");  
	        //通过这个方法可以检查这个任务已经重试了几次了
	        $job->delete();
	    }
        // 也可以重新发布这个任务
        //$job->release(); //$delay为延迟时间
	}
	
	public function failed($data)
	{
        // ...任务达到最大重试次数后，失败了
        \think\facade\Log::write("取消订单队列执行失败:{$data}");
    }
	
	//查看该任务是否还有处理必要
	private function checkJobStatus($data)
	{
		$order     = json_decode($data, true);
		$orderData = MtOrderModel::where('order_no', $order['orderNo'])->find();
		
		if ($orderData && $orderData->order_status == 1) {
			$orderStatus = true;
		} else {
			$orderStatus = false;
		}
		
		return $orderStatus;
	}
	
	//执行具体的任务
	private function doColseOrder($data)
	{
		$order = json_decode($data, true);
		$bln   = false; 
		if (!empty($order)) {
			$orderLogic = new OrderLogic();
			
			$orderLogic->setOrderNo($order['orderNo']);
			$bln = $orderLogic->cancelOrder();
		}
		
		return $bln;
	}
}
