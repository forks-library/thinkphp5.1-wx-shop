<?php

namespace app\shop\service\wxpay;

use app\shop\service\wxpay\WxPayBase;

class WxPayUnifiedOrder extends WxPayBase {

	protected $notifyUrl  = ''; //回调url
 	
 	public function __construct($appId, $merchantId, $shopApiKey, $notifyUrl) {
 		if (empty($notifyUrl)) {
 			throw new \think\exception\ValidateException('数组数据异常！', '403');
 		} 
 		
 		$this->notifyUrl = $notifyUrl;
 		parent::__construct($appId, $merchantId, $shopApiKey);
 	}
 	
 	//统一下单
 	public function unifiedOrder ($openId, $payOrderNo, $amount, $body, $detail = '') {
 		$url = "https://api.mch.weixin.qq.com/pay/unifiedorder";
 		
 		if (!empty($openId) && !empty($payOrderNo) && !empty($amount)
 		    && is_numeric($amount) && floatval($amount) > 0 && !empty($body)) {
 		    //设置参数
 		    $this->values = array(
 		    	'appid'             => $this->appId
				,'mch_id'           => $this->merchantId
				,'nonce_str'        => get_nonce_str()
				,'sign'             => ''
				,'body'             => $body
				,'detail'           => !empty($detail) ? $detail : $body
				,'out_trade_no'     => $payOrderNo
				,'fee_type'         => 'CNY'
				,'total_fee'        => floatval($amount) * 100
				,'spbill_create_ip' => $_SERVER['REMOTE_ADDR']
				,'notify_url'       => $this->notifyUrl
				,'trade_type'       => 'JSAPI'
				,'openid'           => $openId
 		    );
 		    //生成签名
 		    $sign = $this->MakeSign();
 		    $this->values['sign'] = $sign;
 		    //生成xml
 		    $xml = $this->ToXml();
 		    $response = $this->postXmlCurl($xml, $url);
 		    $arrData  = $this->initResponseXml($response);
 		    $this->getJsApiParameters($arrData);
 		    
 		    return $this->values;
 		}
 		
 		return false;
 	}
 	
 	protected function getJsApiParameters($result)
 	{
 		if (array_key_exists('appid', $result) && array_key_exists('prepay_id', $result) && $result['prepay_id'] != '') {
 			$this->values = array(
 				'appId'     => $result['appid'],
 				'package'   => 'prepay_id='.$result["prepay_id"].'',
 				'nonceStr'  => get_nonce_str(),
 				'signType'  => 'MD5',
 				'timeStamp' => strval(time()),
 				'paySign'   => ''
 			);
 			 //生成签名
 		    $sign = $this->MakeSign();
 		    $this->values['paySign'] = $sign;
 		}
 	}
}
