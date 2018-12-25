<?php

namespace app\shop\service\wxpay;

/*
 * 数据对象基础类，该类中定义数据类最基本的行为，包括：
 * 计算/设置/获取签名、输出xml格式的参数、从xml读取数据对象等
 * @author widyhu
 * 
 */
 
 class WxPayBase {
 	
 	protected $values        = array();
 	protected $signType      = 'MD5';
 	protected $appId         = ''; //公众号appID
	protected $merchantId    = ''; //商户号ID
	protected $shopApiKey    = ''; //商户API秘钥
 	
 	public function __construct($appId, $merchantId, $shopApiKey) {
 		if (empty($appId)) {
 			throw new \think\exception\ValidateException('appID 不能为空！', '403');
 		} elseif (empty($merchantId)) {
 			throw new \think\exception\ValidateException('商户号不能为空！', '403');
 		} elseif (empty($shopApiKey)) {
 			throw new \think\exception\ValidateException('商户API密钥', '403');
 		}
 		
 		$this->appId       = $appId;
 		$this->merchantId  = $merchantId;
 		$this->shopApiKey  = $shopApiKey;
 	}
 	
	/**
	* 获取签名，详见签名生成算法的值
	* @return 值
	**/
	public function GetSign()
	{
		return $this->values['sign'];
	}
	
	/**
	* 判断签名，详见签名生成算法是否存在
	* @return true 或 false
	**/
	public function IsSignSet()
	{
		return array_key_exists('sign', $this->values);
	}

	/**
	 * 输出xml字符
	 * @throws WxPayException
	**/
	public function ToXml()
	{
		if(!is_array($this->values) || count($this->values) <= 0)
		{
    		throw new \think\exception\ValidateException('数组数据异常！', '403');
    	}
    	
    	$xml = "<xml>";
    	foreach ($this->values as $key => $val)
    	{
    		if (is_numeric($val)) {
    			$xml.="<".$key.">".$val."</".$key.">";
    		} else {
    			$xml.="<".$key."><![CDATA[".$val."]]></".$key.">";
    		}
        }
        $xml.="</xml>";
        return $xml; 
	}

    /**
     * 将xml转为array
     * @param string $xml
     * @throws WxPayException
     */
	public function fromXml ($xml)
	{	
		if (!$xml) {
			throw new \think\exception\ValidateException('xml数据异常！', '403');
		}
        //将XML转为array
        //禁止引用外部xml实体
        libxml_disable_entity_loader(true);
        $this->values = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
		return $this->values;
	}
	
	/**
	 * 格式化参数格式化成url参数
	 */
	public function ToUrlParams()
	{
		$buff = "";
		foreach ($this->values as $k => $v)
		{
			if($k != "sign" && $v != "" && !is_array($v)){
				$buff .= $k . "=" . $v . "&";
			}
		}
		
		$buff = trim($buff, "&");
		return $buff;
	}
	
	/**
	 * 验证签名
	 */
	public function checkSign () {
		if(!$this->IsSignSet()){
			throw new \think\exception\ValidateException('签名错误！', '403');		}
		
		$sign = $this->MakeSign();
		if($this->GetSign() == $sign){
			//签名正确
			return true;
		}
	}
	
	/**
	 * 生成签名
	 * @param bool $needSignType  是否需要补signtype
	 * @return 签名，本函数不覆盖sign成员变量，如要设置签名需要调用SetSign方法赋值
	 */
	public function MakeSign($needSignType = true)
	{
		if ($needSignType && $this->signType == '') {
			$this->signType = 'MD5';
		}
		
		//签名步骤一：按字典序排序参数
		ksort($this->values);
		$string = $this->ToUrlParams();
		//签名步骤二：在string后加入KEY
		$string = $string . "&key=".$this->shopApiKey;
		//签名步骤三：MD5加密或者HMAC-SHA256
		if($this->signType == "MD5"){
			$string = md5($string);
		} else if($this->signType == "HMAC-SHA256") {
			$string = hash_hmac("sha256",$string ,$this->shopApiKey);
		} else {
			throw new \think\exception\ValidateException('签名类型不支持！', '403');
		}
		
		//签名步骤四：所有字符转为大写
		$result = strtoupper($string);
		return $result;
	}
	
	/**
     * 将xml转为array
     * @param WxPayConfigInterface $config  配置对象
     * @param string $xml
     * @throws WxPayException
     */
	public function initResponseXml($xml)
	{	
		$this->fromXml($xml);
		//失败则直接返回失败
		if($this->values['return_code'] != 'SUCCESS') {
			foreach ($this->values as $key => $value) {
				#除了return_code和return_msg之外其他的参数存在，则报错
				if($key != "return_code" && $key != "return_msg"){
					throw new \think\exception\ValidateException('输入数据存在异常！', '403');
					return false;
				}
			}
			
			return $this->GetValues();
		}
		
        return $this->GetValues();
	}
	
	/**
	 * 以post方式提交xml到对应的接口url
	 * 
	 * @param string $xml  需要post的xml数据
	 * @param string $url  url
	 * @param bool $useCert 是否需要证书，默认不需要
	 * @param int $second   url执行超时时间，默认30s
	 * @throws WxPayException
	 */
	protected function postXmlCurl($xml, $url, $useCert = false, $second = 30)
	{		
		$ch = curl_init();
		$curlVersion = curl_version();
		$ua = "WXPaySDK/3.0.9 (".PHP_OS.") PHP/".PHP_VERSION." CURL/".$curlVersion['version']." "
		.$this->shopApiKey;

		//设置超时
		curl_setopt($ch, CURLOPT_TIMEOUT, $second);

//		$proxyHost = "0.0.0.0";
//		$proxyPort = 0;

//		//如果有配置代理这里就设置代理
//		if($proxyHost != "0.0.0.0" && $proxyPort != 0){
//			curl_setopt($ch,CURLOPT_PROXY, $proxyHost);
//			curl_setopt($ch,CURLOPT_PROXYPORT, $proxyPort);
//		}
		curl_setopt($ch,CURLOPT_URL, $url);
		curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,TRUE);
		curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,2);//严格校验
		curl_setopt($ch,CURLOPT_USERAGENT, $ua); 
		//设置header
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		//要求结果为字符串且输出到屏幕上
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	
		if($useCert == true){
			//设置证书
			//使用证书：cert 与 key 分别属于两个.pem文件
			//证书文件请放入服务器的非web目录下
			$sslCertPath = "";
			$sslKeyPath = "";

			curl_setopt($ch,CURLOPT_SSLCERTTYPE,'PEM');
			curl_setopt($ch,CURLOPT_SSLCERT, $sslCertPath);
			curl_setopt($ch,CURLOPT_SSLKEYTYPE,'PEM');
			curl_setopt($ch,CURLOPT_SSLKEY, $sslKeyPath);
		}
		//post提交方式
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
		//运行curl
		$data = curl_exec($ch);
		//返回结果
		if($data){
			curl_close($ch);
			return $data;
		} else { 
			$error = curl_errno($ch);
			curl_close($ch);
			throw new \think\exception\ValidateException("curl出错，错误码:{$error}");
		}
	}
	
	/**
	 * 获取设置的值
	 */
	public function GetValues()
	{
		return $this->values;
	}
	
	/**
	 * 查询订单
	 * @param string $transactionId
	 * @param number $timeOut
	 */
	public function queryorder($transactionId, $timeOut = 6) {
		$url = "https://api.mch.weixin.qq.com/pay/closeorder";
		
		//重新赋值values
		$this->values = array(
			'appid'          => $this->appId,
			'transaction_id' => $transactionId,
			'mch_id'         => $this->merchantId,
			'nonce_str'      => get_nonce_str(),
			'sign'           => ''
		);
		
		$sign = $this->MakeSign();
 		$this->values['sign'] = $sign;
 		\think\facade\Log::write("订单查询数据前：".json_encode($this->values));
 		//生成xml
 		$xml = $this->ToXml();
 		$response = $this->postXmlCurl($xml, $url);
 		$this->fromXml($response);
 		
 		\think\facade\Log::write("订单查询数据后：".json_encode($this->values));
 		if(array_key_exists("return_code", $this->values)
			&& array_key_exists("result_code", $this->values)
			&& $this->values["return_code"] == "SUCCESS"
			&& $this->values["result_code"] == "SUCCESS")
		{
			return true;
		}
		
		return false;
	}
 }