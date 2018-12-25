<?php
/*
 * Created on 2018年11月19日
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
namespace app\shop\controller\v1\api;

use think\Controller;
use think\facade\Cache;
use think\facade\Session;
use app\shop\model\MtAddressModel;

class UseraddressController extends Controller {
	
	//创建用户收获地址
	public function createUserAddress(MtAddressModel $mtAddress)
	{
		$userName     = $this->request->param('userName');
		$phone        = $this->request->param('phone');
		$address      = $this->request->param('address');
		$default      = $this->request->param('default');
		$provinceCity = $this->request->param('provinceCity');
		
		$userAddress = [
			'userName'     => $userName,
			'phone'        => $phone,
			'address'      => $address,
			'provinceCity' => $provinceCity,
		];
		
		//数据验证
		$validate = new \app\shop\validate\UserAddress;
		if (!$validate->check($userAddress)) {
			return json(array('message' => $validate->getError()));
		}
		
		$userId = Session::get('wx_user_id') ? Session::get('wx_user_id') : '123456';
		$this->isDefaultAddress($default, $userId);
		
		$mtAddress->user_id       = $userId;
		$mtAddress->user_name     = $userName;
		$mtAddress->phone         = $phone;
		$mtAddress->address       = $address;
		$mtAddress->is_default    = $default;
		$mtAddress->province_city = $provinceCity;
		
		if (!$mtAddress->save()) {
			return json(array('message' => '地址添加失败'));
		}
		
		return json(array('message' => '地址添加成功'));
	}
	
	//获取用户地址列表
	public function getUserAddress(MtAddressModel $mtAddress)
	{
		$userId  = Session::get('wx_user_id') ? Session::get('wx_user_id') : '123456'; //$this->request->param('user_id');
		$addressList = $mtAddress->where('user_id', $userId)
								->field(['mt_address_id', 'phone', 'province_city', 'address', 'is_default', 'user_name'])
								->select()
								->toArray();
								
		return json(array('address' => $addressList));
	}
	
	//修改用户地址数据
	public function updateUserAddress(MtAddressModel $mtAddress)
	{
		$addressId    = $this->request->param('address_id');
		$phone        = $this->request->param('phone');
		$address      = $this->request->param('address');
		$userName     = $this->request->param('userName');
		$provinceCity = $this->request->param('provinceCity');
		$default      = $this->request->param('default');
		
		$userAddress = [
			'userName'     => $userName,
			'phone'        => $phone,
			'address'      => $address,
			'provinceCity' => $provinceCity,
		];
		
		//数据验证
		$validate = new \app\shop\validate\UserAddress;
		if (!$validate->check($userAddress)) {
			return json(array('message' => $validate->getError()));
		}
		
		$userId  = Session::get('wx_user_id') ? Session::get('wx_user_id') : '123456';
		$this->isDefaultAddress($default, $userId);
		$addressObj = $mtAddress->where('mt_address_id', $addressId)->where('user_id', $userId)->find();
		
		$addressObj->phone   = $phone;
		$addressObj->address = $address;
		$addressObj->user_name     = $userName;
		$addressObj->province_city = $provinceCity;
		$addressObj->is_default    = $default;
		
		if (!$addressObj->save()) {
			return json(array('message' => '地址修改失败'));
		} 
		
		return json(array('message' => '地址修改成功'));
	}
	
	//删除用户地址
	public function delUserAddress(MtAddressModel $mtAddress)
	{
		if (!$this->request->has('address_id')) {
			throw new \think\exception\ValidateException('缺少数据ID', '403');
		}
		
		$addressId = $this->request->param('address_id');
		
		if (!MtAddressModel::destroy($addressId)) {
			return json(array('message' => '删除失败'));
		}
		
		return json(array('message' => '删除成功'));
	}
	
	//获取地地址详情
	public function getAddressById(MtAddressModel $mtAddress) 
	{
		$addressId = $this->request->param('id');
		
		$address = $mtAddress->where('mt_address_id', $addressId)
				->field(['mt_address_id, user_name, phone, is_default, province_city, address'])
				->find();
				
		return json(array('address' => $address));
	}
	
	//地址默认状态
	protected function isDefaultAddress($isDefault, $userId)
	{
		if ($isDefault == 1) {
			$mtAddress = new MtAddressModel();
			
			$isDefalut = $mtAddress->where('is_default', $isDefault)->where('user_id', $userId)->find();
		
			if ($isDefalut) {
				$isDefalut->is_default = 0;
				
				if ($isDefalut->save()) {
					return true;
				} else {
					return false;
				}
			}
		}
		
		return true;	
	}
}
