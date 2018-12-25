<?php
/*
 * Created on 2018年11月3日
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
namespace app\shop\controller\v1\api;

use think\Controller;
use think\facade\Session;
use app\shop\model\MtProductModel;
use app\shop\model\MtProductSkuModel;
use app\shop\model\MtShopCartModel;

class ShopcartController extends Controller {
	
	//加入购物车
	public function addToCart(MtShopCartModel $mtShopCartModel)
	{
		if (!$this->request->has('sku_id') || !$this->request->has('sku_num')) {
			throw new \think\exception\ValidateException('购物车缺少参数', '403');
		}
		
		$userId = Session::get('wx_user_id') ? Session::get('wx_user_id') : '123456';
		$skuId  = $this->request->param('sku_id');
		$skuNum = $this->request->param('sku_num');
		
		$bln = $mtShopCartModel->addGoodsToCart($userId, $skuId, $skuNum);
		
		if (!$bln) {
			return json(array('message' => '加入购物车失败'));
		}
		
		return json(array('message' => '加入购物车成功'));
	}
	
	//查询购物车数据
	public function getCartGoodsINfo(MtShopCartModel $mtShopCartModel)
	{
		$userId = Session::get('wx_user_id') ? Session::get('wx_user_id') : '123456';
		
		$goodDatas = $mtShopCartModel->alias('msc')
									->where('user_id', $userId)
									->join('mt_product_sku mps', 'msc.sku_id = mps.mt_product_sku_id')
									->join('mt_product mp', 'mps.product_id = mp.mt_product_id')
									->field([
										'msc.mt_shop_cart_id',
										'msc.sku_num', 
										'mps.sp_model',
										'mps.display_pic',
										'mps.price',
										'mps.title ',
										'mp.product_name',
										'mps.repertory'
									])->select()->toArray();
									
		foreach($goodDatas as $key => $val) {
			$goodDatas[$key]['display_pic'] = urldecode($val['display_pic']);
		}
									
		return json(array('state' => 200, 'datas' => $goodDatas));							
	}
	
	//修改购物车数量
	public function updCartGoodsNum(MtShopCartModel $mtShopCartModel)
	{
		if (!$this->request->has('cart_id') || !$this->request->has('sku_num')) {
			throw new \think\exception\ValidateException('缺少参数:cart_id', '403');
		}
		
		$cartId = $this->request->param('cart_id');
		$skuNum = $this->request->param('sku_num');
		
		$cartGoods = $mtShopCartModel->where('mt_shop_cart_id', $cartId)->find();
		$cartGoods->sku_num = (int)$skuNum;
		
		if (!$cartGoods->save()) {
			return json(array('message' => '修改失败' ));
		}
		
		return json(array('message' => '修改成功'));
	}
	
	//删除购物车信息
	public function delCartGoods()
	{
		if (!$this->request->has('cart_id')) {
			throw new \think\exception\ValidateException('缺少参数:cart_id', '403');
		}
		
		$cartId = $this->request->param('cart_id');
		
		if (!MtShopCartModel::destroy($cartId)) {
			return json(array('message' => '删除失败' ));
		}
		
		return json(array('message' => '删除成功' ));
	}
	
}