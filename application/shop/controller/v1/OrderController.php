<?php
/**
 * Created by PhpStorm.
 * User: sunxiaofeng
 * Date: 2018/9/1
 * Time: 00:23
 */
namespace app\shop\controller\v1;

use app\shop\controller\BaseController;
use think\facade\Hook;

class OrderController extends BaseController {
     
	public function privilege()
    {
         //页面需要加载的JS，填写JS名称即可
         $this->assign('pageJs', array(
                   "privilege"
              )
         );
        return view('/sign/privilege');
    }

    public function successfulPay ()
    {
         //页面需要加载的JS，填写JS名称即可
         $this->assign('pageJs', array(
                "successfulpay1"
              )
         );
        return view('/successfulpay/index');
    }

    public function order()
    {
         //页面需要加载的JS，填写JS名称即可
         $this->assign('pageJs', array(
                "order"
              )
         );
        return view('/order/index');
    }

    public function evaluate()
    {
         //页面需要加载的JS，填写JS名称即可
         $this->assign('pageJs', array(
                "evaluate"
              )
         );
        return view('/evaluate/index');
    }

    public function toevaluate()
    {
         //页面需要加载的JS，填写JS名称即可
         $this->assign('pageJs', array(
         		"lrz.all.bundle",
                "toevaluate1"
              )
         );
        return view('/toevaluate/index');
    }

    public function checkevaluate()
    {
         //页面需要加载的JS，填写JS名称即可
         $this->assign('pageJs', array(
                "checkevaluate"
              )
         );
        return view('/checkevaluate/index');
    }

    public function rull()
    {
         //页面需要加载的JS，填写JS名称即可
         $this->assign('pageJs', array(
                "rull"
              )
         );
        return view('/evaluate/evaluate');
    }

    public function after()
    {
         //页面需要加载的JS，填写JS名称即可
         $this->assign('pageJs', array(
                "after"
              )
         );
        return view('/aftermarket/index');
    }

    public function orderinfo()
    {
         //页面需要加载的JS，填写JS名称即可
         $this->assign('pageJs', array(
                "orderinfo"
              )
         );
        return view('/orderinfo/index');
    }
}