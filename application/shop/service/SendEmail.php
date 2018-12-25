<?php
/*
 * Created on 2018年12月13日
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 *
 */
namespace app\shop\service;

use PHPMailer\PHPMailer\PHPMailer;
use think\facade\Config;

class SendEmail {
	protected $phpMailer;
	
	protected $config = [
		'host'        => 'smtp.163.com', 
		'smtpSecure'  => 'ssl',
		'port'        => '465', 
		'userName'    => '',  //用来发邮件的邮箱地址
		'password'    => '',  //邮件发送授权码
		'smtpAuth'    => true,
		'ishtml'      => true,
		'fromName'    => ''
	];
	
	public function __construct()
	{
		$config = Config::pull('mail');
		if (empty($config['userName'])) {
			return json(array('error' => '基础发送邮箱不能为空'));
		} elseif (empty($config['password'])) {
			return json(array('error' => '授权码不能为空'));
		}
		
		$this->config = array_merge($this->config, $config);
		$this->phpMailer = new PHPMailer();
	}
	
	//创建转换内容
	protected function createBody($body = array())
	{
		if ($this->config['ishtml']) {
			$html = '<html>';
			if (is_array($body)) {
				foreach($body as $key => $val) {
					$html .= '<p>'.$val.'</p>';
				}
			} else {
				$html .= '<p>'.$body.'</p>';
			}
			$html .= '</html>';
			return $html;
		} else {
			if (is_array($body)) {
				$str = '';
				foreach($body as $key => $val) {
					$str .= '，'.$val;
				}
				
				return $str.'。';
			} else {
				return $body;
			}
		}
	}
	
	/**
	 * @param string $title 标题
	 * @param array|string $body 内容
	 * @param string $toUser 收件人
	 * @param array $toCC 抄送人
	 */
	public function send($title, $body, $toUser = '', $toCC = array()) 
	{
		$body    = $this->createBody($body);
		$blnsucc = false;
		
		if (!empty($toUser)) {
			$this->phpMailer->isSMTP();
			$this->phpMailer->Host       = $this->config['host'];
			$this->phpMailer->Port       = $this->config['port'];
			$this->phpMailer->SMTPAuth   = $this->config['smtpAuth'];
			$this->phpMailer->CharSet    = 'utf-8';
			$this->phpMailer->Username   = $this->config['userName'];
			$this->phpMailer->Password   = $this->config['password'];
			$this->phpMailer->SMTPSecure = $this->config['smtpSecure'];
			$this->phpMailer->isHTML($this->config['ishtml']);
			
			//设置发件人信信息
			$this->phpMailer->setFrom($this->config['userName'], $this->config['fromName']);
			//设置收件人地址
			$this->phpMailer->addAddress($toUser);
			//设置cc
			if (!empty($toCC)) {
				foreach($toCC as $k => $cc) {
					$this->phpMailer->addCC($cc);
				}
			}
			
			//设置标题
			$this->phpMailer->Subject = $title;
			//设置内容
			$this->phpMailer->Body    = $body;
			
			if ($this->phpMailer->send()) {
				$blnsucc = true;
			}
		}
		
		return $blnsucc;
	}
}
