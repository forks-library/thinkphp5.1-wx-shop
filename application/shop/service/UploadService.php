<?php
/*
 * Created on 2018年12月17日
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
namespace app\shop\service;

use app\shop\service\AliossService;

class UploadService {
	
	protected $isUploadAliyun; //是否启用阿里云
	protected $config = ['size'=>10485760, 'ext'=>'jpg,jpeg,bmp,png,gif'];
	
	public function __construct($isUploadAliyun = false)
	{
		$this->isUploadAliyun = $isUploadAliyun;
	}
	
	/**
	 * 上传单个文件
	 * @param string $clomun 接收文件字段
	 * @param string $filePath oss bucket下的目录
	 * return string $fileName; 
	 */
	public function upload($clomun, $filePath = '')
	{
		$file = request()->file($clomun);
		
		$info = $file->validate($this->config)->rule('uniqid')->move('../uploads');
		if ($info) {
			$imgPath = realpath('../uploads/'.$info->getSaveName());
			
			if ($this->isUploadAliyun) {
				$alioss = new AliossService();
				return $alioss->uploadImg($imgPath, $filePath);
			}
			
			return $imgPath;
		}
		
		return $info;
	}
	
	/**
	 * 上传多个文件 
	 * @param string $clomun 接收文件字段
	 * @param string $filePath oss bucket下的目录
	 * return array $fileArr;
	 */
	public function uploadArr($clomun, $filePath = '')
	{
		$files   = request()->file($clomun);
		$fileArr = array();
		foreach($files as $k => $file) {
			$info = $file->validate($this->config)->rule('uniqid')->move( '../uploads');
			if ($info) {
				$fileArr[$k] = realpath('../uploads/'.$info->getSaveName());
			}
		}
		
		if (!empty($fileArr)) {
			if ($this->isUploadAliyun) {
				$alioss = new AliossService();
				return $alioss->uploadImg($fileArr, $filePath);
			}
		}
		
		return $fileArr;
	}
}
