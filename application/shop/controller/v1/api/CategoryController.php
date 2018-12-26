<?php
/*
 * Created on 2018年12月11日
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
namespace app\shop\controller\v1\api;

use think\Controller;
use app\shop\model\MtGoodsCategoryModel;
use app\shop\model\MtCategoryAtrrModel;

class CategoryController extends Controller {
	
	//获取类别列表
	public function getCategoryList()
	{
		$list = MtGoodsCategoryModel::where('deleted', 0)->field(['mt_goods_category_id', 'icon', 'category_name'])->select()->toArray();
		return json(array('datas' => $list));
	}
	
	//获取类别属性列表
	public function getAtrrList()
	{
		$categoryId = $this->request->param('categoryid');
		$list       = MtCategoryAtrrModel::where('deleted', 0)->where('category_id', $categoryId)
						->field(['mt_category_atrr_id', 'category_id', 'icon', 'atrr_name'])->select();
						
		return json(array('datas' => $list));
	}
}

