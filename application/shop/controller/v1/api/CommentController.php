<?php
/*
 * Created on 2018年12月14日
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
namespace app\shop\controller\v1\api;

use think\Controller;
use think\facade\Session;
use app\shop\model\MtCommentModel;
use app\shop\model\MtOrderModel;

class CommentController extends Controller {
	
	//添加评论
	public function commentByGoods(MtCommentModel $commentModel)
	{
		$dataId  = $this->request->param('commentId');
		$imgUrl  = $this->request->param('imgUrl');
		$comment = $this->request->param('comment');
		$stars   = $this->request->param('stars');
		$userId  = Session::get('wx_user_id') ? Session::get('wx_user_id') : 123456;
		
		$data = [
			'commentId' => $dataId,
			'comment'   => $comment,
			'userId'    => $userId,
		];
		
		$validate = new \app\shop\validate\Comment;
		if (!$validate->check($data))	{
			return json(array('message' => $validate->getError()));
		}
		
		$commentM = MtCommentModel::where('mt_comment_id', $dataId)->find();
		$commentM->notes   = $comment;
		$commentM->imgs    = !empty($imgUrl) ? urlencode($imgUrl) : '';
		$commentM->stars   = $stars;
		$commentM->status  = 2;
		$bln = $commentM->save();
		
		if ($bln) {
			return json(array('data' => 'success', 'message' => '评论成功'));
		}
		
		return json(array('data' => 'fail', 'message' => '评论失败'));
	}
	
	//获取评论商品
	public function goodComList()
	{
		$status = $this->request->param('status');
		$userId = Session::get('wx_user_id') ? Session::get('wx_user_id') : 123456;
		$field  = ['mcm.mt_comment_id', 'mcm.status', 'mps.title', 'mps.sp_model', 
					'mps.price', 'mps.display_pic', 'mps.product_id'];
		
		$goodList = MtCommentModel::alias('mcm')
								->where('mcm.user_id', $userId)
								->where('mcm.status', $status)
								->join('mt_product_sku mps', 'mcm.sku_id = mps.mt_product_sku_id')
								->field($field)
								->select();
		foreach($goodList as $key => $val) {
			$goodList[$key]['display_pic'] = urldecode($val['display_pic']);
		}
		return json(array('datas' => $goodList));
	}
	
	//查看评价信息
	public function getComDetail()
	{
		$dataId = $this->request->param('commentId');
		$comment = MtCommentModel::where('mt_comment_id', $dataId)->find();
		return json(array('data' => $comment));
	}
}
