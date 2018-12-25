<?php
/*
 * Created on 2018年12月17日
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
namespace app\shop\service;

use OSS\OssClient;
use OSS\Core\OssException;
use think\facade\Config;

class AliossService {
	
	protected $config = [
		'accessKeyId'    => '',
		'accessKeySecret'=> '',
		'endpoint'       => '',
	];
	protected $ossClient;
	
	public function __construct()
	{
		$config = Config::pull('alioss');
		
		if (empty($config['accessKeyId'])) {
			return json(array('error' => 'AccessKeyId不能为空'));
		} elseif (empty($config['accessKeySecret'])) {
			return json(array('error' => 'AccessKeySecret不能为空'));
		} elseif(empty($config['endpoint'])) {
			return json(array('error' => 'OSS数据中心访问域名不能为空'));
		}
		
		$this->config = array_merge($this->config, $config);
		try {
			$this->ossClient = new OssClient($config['accessKeyId'], $config['accessKeySecret'], sprintf($config['endpoint'], ''), $config['isCName']);
		} catch (OssException $e) {
			\think\facade\Log::write('aliyun oss: '.$e->getMessage());
			return json(array('error' => 'aliyun obj error'));
		}
	}
	
	/**
	 * @param string|array $fileName 文件整体路径
	 * @param string $filePath bucket下的目录 
	 */
	public function uploadImg($fileName, $filePath = '')
	{
		$blnRtn = false;
		if (!empty($fileName)) {
			if (is_array($fileName)) {
				$blnRtn = array();
				foreach($fileName as $key => $val) {
					$blnRtn[$key] = $this->uploadFile($val, $filePath);
					unlink($val);
				}
			} else {
				$blnRtn = $this->uploadFile($fileName, $filePath);
				unlink($fileName);
			}
		}
		
		return $blnRtn;
	}
	
	/**
	 * @param string $fileName 文件整体路径
	 * @param string $filePath bucket下的目录 
	 * @param string $bucket
	 */
	protected function uploadFile($fileName, $filePath = '', $bucket = "itmato-yusm")
	{
		$file = false;
		if (!empty($fileName)) {
			$relFilePath = '';
			if (strrpos($fileName, '/')) {
				$fileN = substr($fileName, strrpos($fileName, '/')+1);
				
				if (!empty($filePath)) {
					$relFilePath = $filePath.'/'.$fileN;
				} else {
					$relFilePath = $fileN;
				}
			}
			
			if (!empty($relFilePath)) {
				$fileCom = file_get_contents($fileName);
				
				try {
					$this->ossClient->putObject($bucket, $relFilePath, $fileCom);
					$file = sprintf($this->config['imgServer'], $bucket).'/'.$relFilePath;
				} catch(OssException $e) {
					\think\facade\Log::write('aliyun oss upload: '.$e->getMessage());
					return json(array('error' => 'aliyun upload error'));
				}
			}
		}
		
		return $file;
	}
	
}
