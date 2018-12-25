<?php
/*
 * Created on 2018年11月3日
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
namespace app\shop\controller\v1\api;

use think\Controller;
use app\shop\model\MtProductModel;
use app\shop\model\MtProductSkuModel;
use app\shop\model\MtCommentModel;

class ProductController extends Controller {
	
	//获取产品列表
	public function getproductList(MtProductModel $mtProduct) {
		$categoryId = $this->request->param('category_id');
		$page       = $this->request->param('page');
		if (!is_numeric($page) || intval($page) < 1) {
			$page = 1;
		}
		
		$where = ' 1 = 1 and deleted = 0 and on_sale = 1 ';
		if (!empty($categoryId)) {
			$where .= ' and atrr_id = \''.$categoryId.'\''; //二级类别  后期可以扩展商品模块，现在只是满足比较小的需求
		}
		
		$field = [
			'mt_product_id', 'category_id', 'product_name', 'display_pic', 'price'
		];
		$productLists = $mtProduct->where($where)->field($field)->paginate([$page, 20])->toArray();
		
		return json(array('datas' => $productLists));
	}
	
	//获取产品详情
	public function getProductDetail(MtProductModel $mtProduct, MtProductSkuModel $skuModel, MtCommentModel $commentModel) {
		$productId = $this->request->param('id');
		
		if (empty($productId)) {
			throw new \think\exception\ValidateException('id不能为空', '403');
		}
		$field = [
			'paf.mt_product_sku_id' ,'paf.price' ,'paf.repertory' ,'paf.sp_model'
			,'paf.product_id' ,'paf.display_pic'
		];
		//商品数据
		$productData= $mtProduct->where('mt_product_id', $productId)
						->field(['mt_product_id', 'imgs', 'product_name', 'price', 'parameter', 'min_notes', 'info_imgs'])->find();
		//sku数据
		$skuArray = $skuModel->alias('paf')->where('product_id', $productId)->field($field)->select()->toArray();
		//评论数据
		$comment = $commentModel->getCommentByPro($productId);
		if (!$productData) {
			$productData = [];
		}	
			 		
		$productData['skuArr'] = $skuArray; //sku
		$productData['comment']= $comment;
		
		return json(array('data' => $productData));
	}
	
	//获取商品评论信息
	public function getCommentByProductId(MtCommentModel $commentModel)
	{
		$productId = $this->request->param('id');
		$page      = $this->request->param('page');
		
		$comment = $commentModel->getCommentByPro($productId, $page);
		
		return json(array('datas' => $comment));
	}
}