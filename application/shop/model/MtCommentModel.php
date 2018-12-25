<?php
/*
 * Created on 2018年12月14日
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 * 商品评论模型
 */
namespace app\shop\model;

use app\shop\model\BaseModel;

class MtCommentModel extends BaseModel {
	
	protected $pk = 'mt_comment_id';
	
	public function getImgsAttr($value)
	{
		$imgs = urldecode($value);
		if (strpos($imgs, ';')) {
			$imgs = explode(';', $imgs);
		} else {
			return [$imgs];
		}
		
		return $imgs;
	}
	//获取评论数据
	public function getCommentByPro($productId, $page = 5, $length = null)
	{
		//评论数据
		$comment = $this->alias('mcm')
						->where('mp.mt_product_id', $productId)
						->where('mcm.status', '2')
						->join('mt_product_sku mps', 'mps.mt_product_sku_id = mcm.sku_id')
						->join('mt_product mp', 'mp.mt_product_id = mps.product_id')
						->join('wx_user_resource wur', 'wur.user_wx_id = mcm.user_id')
						->field(['mps.sp_model','mcm.imgs', 'mcm.notes', 'mcm.stars', 'wur.nick_name', 'wur.head_img_url'])
						->paginate([$page, $length]);
		return $comment;
	}
}
