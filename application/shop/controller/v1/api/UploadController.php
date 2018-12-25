<?php
/*
 * Created on 2018年12月11日
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
namespace app\shop\controller\v1\api;

use think\Controller;
use app\shop\service\UploadService;

class UploadController extends Controller {
	
	//单图
	public function upload()
	{
		$uploadService = new UploadService(true); //true 为上传到阿里云
		$imgUrl = $uploadService->upload('file', 'dgchaye');
		
		return json(array('imgUrl' => $imgUrl));
	}
	
	//多图
	public function uploadArr()
	{
		$uploadService = new UploadService(true);  //true 为上传到阿里云
		$imgUrls = $uploadService->uploadArr('file', 'dgchaye');
		
		return json(array('imgUrls' => $imgUrls));
	}
}

