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

class UserController extends BaseController {


  public function mydetail()
  {
    //页面需要加载的JS，填写JS名称即可
    $this->assign('pageJs', array(
        "swiper.min",
        "user"
      )
    );
    //页面需要加载的额外css，填写css名称即可
    $this->assign('pageCss', array(
        "swiper.min"
      )
    );
    return view('/user/index');
  }

  public function sign()
  {
    //页面需要加载的JS，填写JS名称即可
    $this->assign('pageJs', array(
        "sign"
      )
    );
    return view('/sign/index');
  }

  public function privilege()
  {
    //页面需要加载的JS，填写JS名称即可
    $this->assign('pageJs', array(
        "privilege"
      )
    );
    return view('/sign/privilege');
  }

  public function set()
  {
    //页面需要加载的JS，填写JS名称即可
    $this->assign('pageJs', array(
        "set"
      )
    );
    return view('/user/set');
  }

  public function address()
  {
    //页面需要加载的JS，填写JS名称即可
    $this->assign('pageJs', array(
        "address"
      )
    );
    return view('/user/address');
  }
  public function addnew()
  {
    //页面需要加载的额外css，填写css名称即可
    $this->assign('pageCss', array(
            "area"
        )
    );
    //页面需要加载的JS，填写JS名称即可
    $this->assign('pageJs', array(
        "jquery.area",
        "addnew"
      )
    );
    return view('/user/addnew');
  }

  public function addedit()
  {
    //页面需要加载的额外css，填写css名称即可
    $this->assign('pageCss', array(
            "area"
        )
    );
    //页面需要加载的JS，填写JS名称即可
    $this->assign('pageJs', array(
        "jquery.area",
        "addedit"
      )
    );
    return view('/user/addedit');
  }
}