<?php
/*
 * Created on 2018年11月28日
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
namespace app\shop\service;

use app\shop\service\WxManage;

class WxSendTemp {
	
	protected $temp = array();
	protected $data = array();
	protected $miniprogram = array();
	
	//发送模版
	public function sendTemp()
	{
		$wxManage = new WxManage(config('wxconf.wx_app_id'), config('wxconf.wx_app_secret'));
		
		if (!empty($this->miniprogram)) {
			$this->temp['miniprogram'] = $this->miniprogram;
		}
		$this->temp['data'] = $this->data;
		
		return $wxManage->sendTemplate(json_encode($this->temp));
	} 
	
	//设置用户
	public function setToUser($value)
	{
		$this->temp['touser'] = $value;
	}
	
	public function getToUser()
	{
		return $this->temp['touser'];
	}
	
	public function setTemplateId($value)
	{
		$this->temp['template_id'] = $value;
	}
	
	public function setUrl($value)
	{
		$this->temp['url'] = $value;
	}
	
	public function setXcxAppid($value)
	{
		$this->miniprogram['appid'] = $value;
	}
	
	public function setXcxPagepath($value)
	{
		$this->miniprogram['pagepath'] = $value;
	}
	
	/**
	 *@param array $value = array('value'=> '', 'color' => '') 
	 */
	public function setDataFirst($value) {
		$this->data['first'] = $value;
	}
	
	/**
	 * @param string $key
	 * @param array $value = array('value'=> '', 'color' => '') 
	 */
	public function setDataKeywords($key, $value)
	{
		$this->data[$key] = $value;
	}
	
	/**
	 * 
	 */
	public function setDataRemark($value)
	{
		$this->data['remark'] = $value;
	}
	
}