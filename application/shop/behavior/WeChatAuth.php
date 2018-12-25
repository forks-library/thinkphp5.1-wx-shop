<?php
/**
 * Created by PhpStorm.
 * User: sunxiaofeng
 * Date: 2018/9/1
 * Time: 17:35
 * 微信授权行为类
 */
namespace app\shop\behavior;

use think\facade\Request;
use app\shop\service\WxManage;
use app\shop\service\WebTool;
use think\facade\Session;
use app\shop\model\WxUserResourceModel;

class WeChatAuth {

    protected $openId; //用户的openID

    public function run($isOpenAuth = false)
    {
        //实例化微信授权工具类
        $wxManage = new WxManage(config('wxconf.wx_app_id'), config('wxconf.wx_app_secret'));
		$userId = Session::get('wx_user_id');
		
        if (!isset($_GET['code']) && !$userId) {
            $rootUrl = WebTool::currentUrl();
            $jumpkey = rand();

            //用户拉取code
            if ($isOpenAuth === false) {
                $url = $wxManage->getOAuthConnectUri($rootUrl, $jumpkey);
            } else if ($isOpenAuth === true) {
                $url = $wxManage->getOAuthConnectUri($rootUrl, $jumpkey, 'snsapi_userinfo');
            }

            header("Location:". $url);
            return;

        } else if (isset($_GET['code']) && !$userId) {
            $code  = $_GET['code'];
            $userData = array();

            $wxUserResourceModel = new WxUserResourceModel(); //实例化用户类
            //通过code换取token
            $accessInfo = $wxManage ->getAccessTokenByCode($code);

            //code失效或其他原因
            if (isset($accessInfo['errcode'])) {

                $rootUrl = WebTool::currentUrl();
                if (strpos($rootUrl,'?')) {
                    $rootUrl = substr($rootUrl, 0, strpos($rootUrl,'?'));
                }

                header("Location:". $rootUrl);
                return;
            }

            if (isset($accessInfo['openid'])) {
                if ($isOpenAuth === true) {
                    //拉取用户信息
                    $userInfo = $wxManage->getUserInfoByAuth($accessInfo['access_token'], $accessInfo['openid']);

                    $userData['nick_name'] = $userInfo['nickname'];
                    $userData['sex']       = $userInfo['sex'];
                    $userData['city']      = $userInfo['city'];
                    $userData['province']  = $userInfo['province'];
                    $userData['country']   = $userInfo['country'];
                    $userData['language']  = $userInfo['language'];
                    $userData['head_img_url'] = $userInfo['headimgurl'];
                    $userData['user_wx_id']   = $userInfo['openid'];

                    $wxUserResourceModel->updUserData($userData, $accessInfo['openid']);
                    $this->openId = $accessInfo['openid'];

                } elseif ($isOpenAuth === false) {
                    $userData['user_wx_id']   = $accessInfo['openid'];

                    $wxUserResourceModel->updUserData($userData, $accessInfo['openid']);
                    $this->openId = $accessInfo['openid'];
                }
            }
            
            //将openid存入session
			Session::set('wx_user_id', $this->openId);
        }
    }
}