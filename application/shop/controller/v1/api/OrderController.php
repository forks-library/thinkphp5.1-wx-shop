<?php
/*
 * Created on 2018年11月19日
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
namespace app\shop\controller\v1\api;

use think\Controller;
use app\shop\model\MtShopCartModel;
use app\shop\validate\Order;
use app\shop\model\MtOrderModel;
use think\facade\Session;
use app\shop\logic\OrderLogic;
use app\shop\service\wxpay\WxPayNotify;
use app\shop\service\WxSendTemp;
use app\shop\model\MtAddressModel;

class OrderController extends Controller {
	
	//确认订单数据
	public function confirmOrderData() {
		if (!$this->request->has('cart_id')) {
			throw new \think\exception\ValidateException('缺少参数cart_id', '403');
		}
		
		$products = array();
		$cartId   = $this->request->param('cart_id');
		//修改选中状态
		$bln = MtShopCartModel::where('mt_shop_cart_id in ('.$cartId.')')->update(['selected' => 1]);
		if ($bln !== false) {
			$shopCart = new MtShopCartModel();
			$field = ['sc.mt_shop_cart_id, sc.sku_num, sc.sku_id, mps.good_no, mps.display_pic, mps.price, mps.title, mps.sp_model'];
			$products = $shopCart->alias('sc')
							->where('sc.mt_shop_cart_id in ('.$cartId.')')
							->join('mt_product_sku mps', 'sc.sku_id = mps.mt_product_sku_id')
							->field($field)
							->select();
			//数据过滤					
			foreach($products as $key => $val) {
				$products[$key]['display_pic'] = urldecode($val['display_pic']);
			}
			
			//默认地址
			$userId  = Session::get('wx_user_id') ? Session::get('wx_user_id') : '123456';
			$defaultAddress = MtAddressModel::where('user_id', $userId)
								->where('is_default', 1)
								->field(['mt_address_id', 'phone', 'province_city', 'address', 'is_default', 'user_name'])
								->find();
		}

		return json(array('data' => $products, 'address' => $defaultAddress));
	}
	
	//下单
	public function unifiedOrder(OrderLogic $orderLogic)
	{
		$goodInfo = $this->request->param('goods');
		$amount   = $this->request->param('amount');
		$address  = $this->request->param('address');
		$note     = $this->request->param('note');
		
		$OrderData = [
			'goods'   => $goodInfo,
			'amount'  => $amount,
			'address' => $address,
		];
		
		$validate = new \app\shop\validate\Order;
		if (!$validate->check($OrderData))	{
			return json(array('message' => $validate->getError()));
		}
		
		$userId = Session::get('wx_user_id') ? Session::get('wx_user_id') : '123456'; 
		//支付下单
		$orderLogic->setUserId($userId);
		$orderLogic->setGoodInfo($goodInfo);
		$orderLogic->setAmount(floatval($amount));
		$orderLogic->setAddress(json_encode($address));
		$orderLogic->setNote($note);
		
		$sign = $orderLogic->createOrder();
		
		if ($sign === false) {
			return json(array('signCode' => 'false', 'message' => '订单创建失败!!!'));
		}
		
		return json(array('signCode' => $sign));
	}
	
	//下单回调
	public function unfityOrder()
	{
		\think\facade\Log::write('支付回调开始');
		$wxPayNotify = new WxPayNotify(config('wxconf.wx_app_id'), config('wxconf.wx_shop_id'), config('wxconf.wx_api_key'));	
		$orderNo = $wxPayNotify->handle();
		
		if ($orderNo !== false) {
			$mtOrder   = new MtOrderModel();
			$orderInfo = $mtOrder->updateOrderStatus($orderNo);
			
			//模版发送
			if (!empty($orderInfo)) {
				\think\facade\Log::write('本次支付商品信息：'.$orderInfo);
//				$wxSendTemp = new WxSendTemp();
//				//设置userid
//				$wxSendTemp->setToUser(Session::get('wx_user_id'));
//				$wxSendTemp->setTemplateId(); //设置模版ID
//				$wxSendTemp->setDataFirst(array('value' => '恭喜你购买成功', 'color' => '#173177'));  //设置data - first
//				$wxSendTemp->setDataKeywords();
//				$wxSendTemp->setDataRemark(array('value' => '欢迎再次购买', 'color' => '#173177'));
//				
//				//发送
//				$wxSendTemp->sendTemp();
			}
		}
		
		echo "success";
	}
	
	//订单列表
	public function orderList(OrderLogic $orderLogic)
	{
		$orderStatus = $this->request->param('orderStatus');
		$userId = Session::get('wx_user_id') ? Session::get('wx_user_id') : '123456';
		
		//设置参数
		$orderLogic->setUserId($userId);
		$orderLogic->setOrderStatus($orderStatus);
		
		$orderList   = $orderLogic->getOrderList();
		
		return json(array('datas' => $orderList));
	}
	
	//支付待支付的订单
	public function goPayOrder(OrderLogic $orderLogic)
	{
		$orderNo = $this->request->param('orderNo');
		$userId = Session::get('wx_user_id') ? Session::get('wx_user_id') : '123456';
		
		$orderLogic->setOrderNo($orderNo);
		$orderLogic->setUserId($userId);
		
		//去支付为支付的订单
		$signCode = $orderLogic->payOrder();
		
		if (!$signCode) {
			return json(array('signCode' => 'false', 'message' => '订单异常'));
		}
		
		return json(array('signCode' => $signCode));
	}
	
	//取消订单
	public function cleanOrder(OrderLogic $orderLogic)
	{
		$orderNo = $this->request->param('orderNo');
		if (empty($orderNo)) {
			return json(array('error' => '订单号为空'));
		}
		
		//设置订单号
		$orderLogic->setOrderNo($orderNo);
		$bln = $orderLogic->cancelOrder();
		
		if (!$bln) {
			return json(array('error' => '订单取消异常'));
		}
		
		return json(array('message' => '订单取消成功'));
	}
	
	//订单详情
	public function getOrderDetail(OrderLogic $orderLogic) 
	{
		$orderNo = $this->request->param('orderNo');
		
		$orderDetail = array();
		if (!empty($orderNo)) {
			//设置订单no
			$orderLogic->setOrderNo($orderNo);
			//设置用户ID
//			$order->setUserId(Session::get('wx_user_id'));
			$orderDetail = $orderLogic->getOrderDetail();
		}
		
		return json(array('order' => $orderDetail));
	}
	
	//获取订单物流详情
	public function getOrderExpress(OrderLogic $orderLogic)
	{
		$orderNo = $this->request->param('orderNo');
		
		//设置订单no
		$orderLogic->setOrderNo($orderNo);
		$url = $orderLogic->getOrderExpress();
		
		return json(array('url' => $url));
	}
	
	//提醒发货
	public function remind(OrderLogic $orderLogic)
	{
		$orderNo = $this->request->param('orderNo');
		if (empty($orderNo)) {
			return json(array('message' => '参数出错'));
		}
		//设置订单no
		$orderLogic->setOrderNo($orderNo);
		$bln = $orderLogic->remindDeliverGoods();
		if ($bln) {
			return json(array('data' => 'success', 'message' => '提醒成功'));
		}
		
		return json(array('data' => 'error', 'message' => '提醒失败')); 
	}
	
	//确认收货
	public function confirmOrder(OrderLogic $orderLogic) 
	{
		$orderNo = $this->request->param('orderNo');
		if (empty($orderNo)) {
			return json(array('message' => '参数出错'));
		}
		//设置订单号
		$orderLogic->setOrderNo($orderNo);
		$bln = $orderLogic->confirmOrder();
		if ($bln) {
			return json(array('data' => 'success'));
		} 
		
		return json(array('data' => 'fail'));
	}
	
}
