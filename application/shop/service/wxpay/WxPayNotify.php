<?php
/*
 * Created on 2018年11月9日
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
namespace app\shop\service\wxpay;

use app\shop\service\wxpay\WxPayBase;

/**
 *支付回调类 
 */
class WxPayNotify extends WxPayBase {
	
	public function __construct($appId, $merchantId, $shopApiKey) {
		parent::__construct($appId, $merchantId, $shopApiKey);
	}
	
	public function handle()
	{
		if (null === file_get_contents('php://input')) {
			return false;
		}
		
		$xml = file_get_contents('php://input');
		$msg = 'OK';
		//将xml转换为数组
		$this->fromXml($xml);
		$bln = $this->notifyProcess($msg);
		
		if ($bln !== false) {
			\think\facade\Log::write('支付回调', $msg);
			return $this->values['out_trade_no'];
		}
		
		\think\facade\Log::write('支付回调', $msg);
		return false;
	}
	
	//回调处理函数
	protected function notifyProcess(&$msg)
	{
		//判断是否成功
		if (!array_key_exists('return_code', $this->values)
			|| (array_key_exists('return_code', $this->values) && $this->values['return_code'] !== 'SUCCESS')) {
			//TODO失败,不是支付成功的通知
			//如果有需要可以做失败时候的一些清理处理，并且做一些监控
			$msg = "异常异常";
			return false;
		}
		
		if(!array_key_exists("transaction_id", $this->values)){
			$msg = "输入参数不正确";
			return false;
		}
		
		//TODO 2、进行签名验证
		try {
			$checkResult = $this->checkSign();
			if($checkResult == false){
				//签名错误
				\think\facade\Log::write("签名错误...");
				return false;
			}
		} catch(Exception $e) {
			\think\facade\Log::write(json_encode($e));
		}
		
		\think\facade\Log::write("微信的订单号：".$this->values["transaction_id"]);
		//查询订单，判断订单真实性
//		if($this->queryorder($this->values["transaction_id"])){
//			$msg = "订单查询成功";
//			return true;
//		}
//		
//		$msg = "订单查询失败";
		return true;
	}
}
