<?php
/*
 * Created on 2018年11月23日
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
 namespace app\shop\logic;
 
 use app\shop\model\BaseModel;
 use app\shop\model\MtOrderModel;
 use app\shop\model\MtProductSkuModel;
 use app\shop\model\MtShopCartModel;
 use app\shop\model\MtCommentModel;
 use app\shop\service\wxpay\WxPayUnifiedOrder;
 use think\facade\Cache;
 use Db;
 use app\shop\service\WxQueue;
 use app\shop\service\ExpressService;
  
 /**
  *订单逻辑 
  * 
  */
 class OrderLogic extends BaseModel {
 	
 	protected $userId; //用户ID
 	protected $goodInfo; //商品信息
 	protected $amount; //金额
 	protected $address; //发货地址
 	protected $note;    //订单备注
 	protected $orderStatus; //订单状态
 	protected $orderNo; //订单号
 	
 	/**
 	 * 下单逻辑
 	 */
 	public function createOrder()
 	{
 		$sign = false;
 		if ($this->userId && $this->goodInfo && $this->amount && $this->address) {
 			$orderModel = new MtOrderModel();
 			//记录下单人数
 			$this->queueInc();
 			//开启事务，如果里面SQL有异常将回滚
 			$orderNo = Db::transaction(function() use ($orderModel) {
 				//创建订单
	 			$orderNo = $orderModel->createPayOrder($this->userId, json_encode($this->goodInfo), $this->amount, $this->address, $this->note);
	 			if ($orderNo === false) {
	 				 return false;
	 			}
	 			//减少库存
 				$this->reduceStock();
 				//清空购物车
 				$this->cartClear();
 				
 				return $orderNo;
 			});
 			//加入延时队列，用于自动取消订单
 			if (!empty($orderNo)) {
 				$data  = array('orderNo' => $orderNo);
				$delay = 3600;
				$queue = 'closeOrderQueue'; 
 				WxQueue::laterJob($delay, 'app\shop\job\OrderJob@closeOrder', json_encode($data));
 			}
 			//下单完成
 			$this->queueDec();
 		}
 		
 		return $orderNo;
 	}
 	
 	/**
 	 * 支付 
 	 */
 	protected function payMent($orderNo)
 	{
 		$sign = false;
 		if (!empty($orderNo) && is_string($orderNo)) {
 			$wxPay = new WxPayUnifiedOrder(config('wxconf.wx_app_id'), config('wxconf.wx_shop_id'), config('wxconf.wx_api_key'), config('wxconf.wx_notify_url'));
 			$sign = $wxPay->unifiedOrder($this->userId, $orderNo, $this->amount, '东莞茶叶');
 		}
 		
 		return $sign;
 	}
 	
 	/**
 	 *订单支付 
 	 * 
 	 */
 	public function payOrder()
 	{
 		$sign = false;
 		if (!empty($this->orderNo)) {
 			$orderData = MtOrderModel::where('order_no', $this->orderNo)->where('user_id', $this->userId)
 						 	  ->where('order_status', 1)->find();
 			if ($orderData) {
 				$this->amount = $orderData->amount;
 				$sign = $this->payMent($orderData->order_no);
 			}
 		}
 		
 		return $sign;
 	}
 	
 	/**
 	 *取消订单 
 	 * 
 	 */
 	public function cancelOrder()
 	{
 		$bln = false;
 		$orderNo = $this->orderNo;
 		$bln = Db::transaction(function()use($orderNo){
 				//通过no查询订单
 				$orderData = MtOrderModel::where('order_no', $orderNo)->field(['good_info', 'order_status'])->find();
 				if ($orderData) {
 					$orderData->order_status = 5; //取消订单状态
 					$orderData->save();
 					$this->goodInfo = json_decode($orderData->good_info, true);
					//将库存加回商品
 					$bln = $this->addStock();
 					
 					return $bln;
 				}
 			});
 			
 		return $bln;
 	}
 	
 	/**
 	 * 确认订单
 	 */
 	public function confirmOrder()
 	{
 		$comentModel = new MtCommentModel();
 		$bln = Db::transaction(function()use($comentModel){
	 			//将评论商品插入评论数据表
		 		$order = MtOrderModel::where('order_no', $this->orderNo)->find();
		 		if ($order) {
		 			$info = json_decode($order->good_info);
		 			$saveData = array();
		 			foreach($info as $k => $v) {
		 				$save = array();
		 				$save['user_id'] = $order->user_id;
		 				$save['sku_id']  = $v->sku_id;
		 				$save['status']  = 1;
		 				
		 				$saveData[] = $save;
		 			}
		 			
		 			$comentModel->saveAll($saveData);
		 		}
		 		
		 		return MtOrderModel::where('order_no', $this->orderNo)->update(['order_status' => 4]);
 		});
 		
 		return $bln;
 	}
 	
 	/**
 	 * 查询订单列表
 	 */
 	public function getOrderList()
 	{
 		$orderList = array();
 		if (!empty($this->userId)) {
 			$orderModel = new MtOrderModel();
 			
 			$where = '1 = 1 and user_id = \''.$this->userId.'\'';
 			if (!empty($this->orderStatus)) {
 				$where .= ' and order_status = \''.$this->orderStatus.'\'';
 			}
 			$orderList = $orderModel->where($where)->order('order_time', 'desc')
 						->field(['order_no', 'order_status', 'order_time', 'amount', 'good_info', 'remind'])
 						->select()->toArray();
 			//整合商品数据数据
 			foreach($orderList as $key => $order) {
 				$orderList[$key]['good_info'] = $this->getGoodByOrder($order['good_info']);
 			}
 		}
 		
 		return $orderList;
 	}
 	
 	/**
 	 *获取订单详情 
 	 */
 	public function getOrderDetail()
 	{
 		$orderDetail = array();
 		
 		if (!empty($this->orderNo)) {
 			$where = ' order_no = \''.$this->orderNo.'\'';
 			if (!empty($this->userId)) {
 				$where .= ' and user_id = \''.$this->userId.'\'';
 			}
 			
 			$order = MtOrderModel::where($where)->find();
 			if ($order) {
 				$orderDetail['order_no']    = $order->order_no;
 				$orderDetail['order_status']= $order->order_status;
 				$orderDetail['order_time']  = date('Y-m-d H:i:s', $order->order_time);
 				$orderDetail['amount']      = $order->amount;
 				$orderDetail['good_info']   = $this->getGoodByOrder($order->good_info);
 				$orderDetail['address']     = json_decode($order->address, TRUE);
 				$orderDetail['note']        = $order->user_note;
 			}
 		}
 		
 		return $orderDetail;
 	}
 	
 	/**
 	 * 获取订单商品详情
 	 */
 	public function getGoodByOrder($good)
 	{
 		$goodInfo = array();
 		if (!empty($good)) {
 			$goodArray = is_string($good) ? json_decode($good) : $good ;
 			foreach($goodArray as $k => $v) {
 				$good = array();
 				$good['info'] = MtProductSkuModel::alias('mps')->where('mt_product_sku_id', $v->sku_id)
 								->join('mt_product mp', 'mps.product_id = mp.mt_product_id')
 								->field(['mp.mt_product_id', 'mps.title', 'mps.sp_model', 'mps.price', 'mps.display_pic'])
 								->find()
 								->toArray();
 								
 				$good['sum']  = $v->sku_num;
 				$goodInfo[]   = $good;
 			}
 		}
 		
 		return $goodInfo;
 	}
 	
 	/**
 	 * 获取物流信息
 	 */
 	public function getOrderExpress()
 	{
 		$url = '';
 		if (!empty($this->orderNo)) {
 			$order = MtOrderModel::where('order_no', $this->orderNo)->find();
 			
 			if ($order) {
 				$express = new ExpressService();
 				$jumpUrl = 'http://dgcywx.ybizp.com/shop/v1/order';
 				//设置跳转url
 				$express->setCallbackUrl($jumpUrl);
 				//设置物流公司
 				$express->setCompanyType($order->services_company);
 				//设置订单号
 				$express->setPostId($order->waybill_no);
 				$url = $express->getExpressInfo();
 			}
 		}
 		
 		return $url;
 	}
 	
 	/**
 	 * 提醒发货 
 	 */
 	public function remindDeliverGoods()
 	{
 		$blnsucc = false;
 		if (!empty($this->orderNo)) {
 			$order = MtOrderModel::where('order_no', $this->orderNo)->where('remind', 1)->find();
 			if ($order) {
 				$blnsucc = true; 
 			} else {
 				$bln = MtOrderModel::where('order_no', $this->orderNo)->update(['remind' => 1]);
 				if ($bln) {
 					//可以使用队列发送邮件到管理员
 					$data = [
 						'title' => '订单发货提醒',
 						'body'  => '订单号：'.$this->orderNo.' 请尽快安排发货!',
 						'toUser'=> '1228534424@qq.com',  //管理员邮箱 收件人
						'toCC'  => array(),   	            //抄送人邮箱
 					];
 					$data = json_encode($data);
					WxQueue::pushJob('app\shop\job\EmailJob', $data);
										
 					$blnsucc = true;
 				} else {
 					$blnsucc = false;
 				}
 			}
 		}
 		
 		return $blnsucc;
 	}
 	
 	/**
 	 * 模拟队列
 	 */
 	private function queueInc()
    {
        $queue = Cache::get('queue');
        if($queue >= 100){
            return json(['status'=>-99, 'msg' => "当前人数过多请耐心排队{$queue}!"]);
        }
        Cache::inc('queue');
    }

    /**
     * 订单提交结束
     */
    private function queueDec()
    {
        Cache::dec('queue');
    }
 	
 	/**
 	 *减库存 
 	 */
 	public function reduceStock()
 	{
 		$skuModel = new MtProductSkuModel();
 		foreach ($this->goodInfo as $key => $val) {
 			$bln = $skuModel->reduceStock($val['sku_id'], $val['sku_num']);
 		}
 		return $bln;
 	}
 	
 	/**
 	 *加缓存 
 	 */
 	public function addStock()
 	{
 		$skuModel = new MtProductSkuModel();
 		foreach ($this->goodInfo as $key => $val) {
 			$bln = $skuModel->rollBackStock($val['sku_id'], $val['sku_num']);
 		}
 		return $bln;
 	}
 	
 	/**
 	 *清空购物车 
 	 */
 	public function cartClear()
 	{
 		MtShopCartModel::where('user_id', $this->userId)->where('selected', 1)->delete();
 	}
 	
 	//设置用户ID
 	public function setUserId($value) 
 	{
		$this->userId = $value;
	}
 	//设置商品信息
 	public function setGoodInfo($value)
 	{
 		$this->goodInfo = $value;
 	}
 	//设置订单金额
 	public function setAmount($value) 
 	{
 		$this->amount = $value;
 	}
 	//设置收获地址
 	public function setAddress($value)
 	{
 		$this->address = $value;
 	}
 	//设置备注
 	public function setNote($value) 
 	{
 		$this->note = $value;
 	}
 	
 	//设置订单状态
 	public function setOrderStatus($value)
 	{
 		$this->orderStatus = $value;
 	}
 	
 	//设置订单号
 	public function setOrderNo($value)
 	{
 		$this->orderNo = $value;
 	}
 }
