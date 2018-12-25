<?php
/**
 * Created by PhpStorm.
 * User: sunxiaofeng
 * Date: 2018/8/31
 * Time: 23:44
 */
namespace app\shop\controller;

use think\App;
use think\Controller;
use app\shop\service\WebTool;

class BaseController extends Controller {

    public function __construct(App $app = null)
    {
        parent::__construct($app);

        //判断手机类型
        if (WebTool::isIos()) {
            $handyType = 'ios';
        } elseif (WebTool::isAndroid()) {
            $handyType = 'android';
        } else {
            $handyType = 'other';
        }

        $this->assign('handyType', $handyType);
        //设置商城标题
        $this->assign('title', '东莞茶叶');
        //设置模版路径
        $this->assign('webUrl', stripslashes($_SERVER['REQUEST_SCHEME']."://".$_SERVER['HTTP_HOST'].rtrim(dirname($_SERVER['SCRIPT_NAME'])).'/'));
    }
}
