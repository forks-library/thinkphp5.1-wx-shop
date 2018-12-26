<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
// 首页
Route::get('/', 'shop/v1.home/home');

/******************************page************************************/
//订单确认r
Route::get('shop/:version/checkorder', 'shop/:version.checkorder/checkorder');
//购物车r
Route::get('shop/:version/shopcar', 'shop/:version.shopcar/index');
//显示分类页面r
Route::get('shop/:version/category', 'shop/:version.category/index');
//排序页面r
Route::get('shop/:version/sort', 'shop/:version.sort/index');
//商品详情r
Route::get('shop/:version/goods', 'shop/:version.goods/index');
//商品列表
Route::get('shop/:version/prolist', 'shop/:version.goodslist/index');
//我的r
Route::get('shop/:version/user', 'shop/:version.user/mydetail');
//支付页面r
Route::get('shop/:version/successfulpay', 'shop/:version.order/successfulpay');
//订单r
Route::get('shop/:version/order', 'shop/:version.order/order');
//设置r
Route::get('shop/:version/set', 'shop/:version.user/set');
//添加地址r
Route::get('shop/:version/address', 'shop/:version.user/address');
//添加新地址r
Route::get('shop/:version/addnew', 'shop/:version.user/addnew');
//修改地址r
Route::get('shop/:version/editaddress', 'shop/:version.user/addedit');
//订单详情r
Route::get('shop/:version/info_order', 'shop/:version.order/orderinfo');
//评论列表r
Route::get('shop/:version/evaluate', 'shop/:version.order/evaluate');
//评论内页r
Route::get('shop/:version/toevaluate', 'shop/:version.order/toevaluate');
//查看评论r
Route::get('shop/:version/checkevaluate', 'shop/:version.order/checkevaluate');
//售后
Route::get('shop/:version/after', 'shop/:version.order/after');
//评论规则
Route::get('shop/:version/rull', 'shop/:version.order/rull');
//签到
Route::get('shop/:version/sign', 'shop/:version.user/sign');
//等级特权
Route::get('shop/:version/privilege', 'shop/:version.user/privilege');
//搜索页面
Route::get('shop/:version/search', 'shop/:version.search/index');
//搜索列表
Route::get('shop/:version/rakedlist', 'shop/:version.search/rakedlist');

/**************************************************API*********************************************/
//获取产品列表
Route::post('shop/:version/productList', 'shop/:version.api.product/getproductList');
//获取产品详情
Route::get('shop/:version/productDetail/:id', 'shop/:version.api.product/getProductDetail');
//加入购物车
Route::post('shop/:version/addToCart', 'shop/:version.api.shopcart/addToCart');
//获取购物车信息
Route::get('shop/:version/getShopCartGoods', 'shop/:version.api.shopcart/getCartGoodsINfo');
//修改购物车商品数量
Route::post('shop/:version/updShopCartGoodsNum', 'shop/:version.api.shopcart/updCartGoodsNum');
//删除购物车商品
Route::post('shop/:version/delShopCartGoods', 'shop/:version.api.shopcart/delCartGoods');
//添加收货地址
Route::post('shop/:version/addAddress', 'shop/:version.api.useraddress/createUserAddress');
//获取收货地址列表
Route::get('shop/:version/getAddress', 'shop/:version.api.useraddress/getUserAddress');
//获取收货地址详情
Route::get('shop/:version/getAddDetail/:id', 'shop/:version.api.useraddress/getAddressById');
//修改收货地址
Route::post('shop/:version/updAddress', 'shop/:version.api.useraddress/updateUserAddress');
//删除收货地址
Route::post('shop/:version/delAddress', 'shop/:version.api.useraddress/delUserAddress');
//确认订单
Route::post('shop/:version/confirmOrder', 'shop/:version.api.order/confirmOrderData');
//统一下单
Route::post('shop/:version/unifiedOrder', 'shop/:version.api.order/unifiedOrder');
//下单回调
Route::any('shop/:version/unfiyOrder', 'shop/:version.api.order/unfityOrder');
//获取订单列表
Route::post('shop/:version/getUserOrders', 'shop/:version.api.order/orderList');
//支付的订单
Route::post('shop/:version/goPayOrder', 'shop/:version.api.order/goPayOrder');
//取消订单
Route::post('shop/:version/cleanOrder', 'shop/:version.api.order/cleanOrder');
//获取订单详情
Route::post('shop/:version/orderDetail', 'shop/:version.api.order/getOrderDetail');
//获取物流信息
Route::post('shop/:version/orderExpress', 'shop/:version.api.order/getOrderExpress');
//用户信息
Route::get('shop/:version/meInfo', 'shop/:version.api.user/getUserInfo');
//获取类别列表
Route::get('shop/:version/goodCate', 'shop/:version.api.category/getCategoryList');
//属性（二级类别）
Route::get('shop/:version/secondCate/:categoryid', 'shop/:version.api.category/getAtrrList');
//提醒发货
Route::post('shop/:version/remind', 'shop/:version.api.order/remind');
//确认收货
Route::post('shop/:version/confGoods', 'shop/:version.api.order/confirmOrder');
//评论商品列表
Route::get('shop/:version/comGoodList/:status', 'shop/:version.api.comment/goodComList');
//评论商品
Route::post('shop/:version/comment', 'shop/:version.api.comment/commentByGoods');
//获取评论详情
Route::get('shop/:version/commDetail/:commentId', 'shop/:version.api.comment/getComDetail');
//单图
Route::post('shop/:version/img', 'shop/:version.api.upload/upload');
//多图
Route::post('shop/:version/images', 'shop/:version.api.upload/uploadArr');
//通过商品ID获取评论信息列表
Route::post('shop/:version/combypro', 'shop/:version.api.product/getCommentByProductId');