<?php
/*
 * Created on 2018年12月7日
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
namespace app\shop\service;

/**
 * 快递服务类 
 * 
 */
class ExpressService {
	
	protected $type        = ''; //快递类型 公司，小写字母
	protected $postid      = ''; //快递单号
	protected $callbackurl = ''; //回调url
	
	//设置快递类型
	public function setCompanyType($value) 
	{
		$this->type = $value;
	}
	
	//设置快递单号
	public function setPostId($value) 
	{
		$this->postid = $value;
	}
	
	//设置回调URL
	public function setCallbackUrl($value)
	{
		$this->callbackurl = $value;
	}
	
	/**
	 *查询快递信息 
	 *
	 */
	public function getExpressInfo() 
	{
		if (empty($this->type) || empty($this->postid)) {
			return json(array('error' => '物流信息不全'));
		}
		
		$url  = "https://m.kuaidi100.com/index_all.html";
		$url .= "?type=".$this->type."&postid=".$this->postid;
		if (!empty($this->callbackurl)) {
			$url .= "&callbackurl=".$this->callbackurl;
		}
		
//		header("Location:".$url);
		return urlencode($url);
	}
}

