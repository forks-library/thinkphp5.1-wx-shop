<?php
/**
 * Created by PhpStorm.
 * User: sunxiaofeng
 * Date: 2018/9/1
 * Time: 20:22
 */
namespace app\shop\service;

use think\facade\Cache;

class WxManage {

    public static $_URL_API_ROOT = 'https://api.weixin.qq.com';

    public static $_URL_OP_ROOT  = 'https://open.weixin.qq.com';

    protected $appId;  //微信appid
    protected $appsecret; //微信secret

    public function __construct($appId, $appsecret)
    {
        $this->appId        = $appId;
        $this->appsecret    = $appsecret;
    }

    /*
     * 用户同意授权，获取code
     * @param $redirect_url
     * @param $state
     */

    public function getOAuthConnectUri($redirect_url, $state ='', $scope = 'snsapi_base')
    {
        $redirect_url = urlencode($redirect_url);

        $url  = self::$_URL_OP_ROOT.'/connect/oauth2/authorize?';
        $url .= 'appid='.$this->appId;
        $url .= '&redirect_uri='.$redirect_url;
        $url .= '&response_type=code';
        $url .= '&scope='.$scope;
        $url .= '&state='.$state.'#wechat_redirect';

        return $url;
    }

    /**
     * 2、通过code换取网页授权access_token
     *
     * @param $code
     *
     * @return mixed
     */
    public function getAccessTokenByCode($code) 
    {
        $url  = self::$_URL_API_ROOT."/sns/oauth2/access_token?";
        $url .= 'appid='.$this->appId;
        $url .= '&secret='.$this->appsecret;
        $url .= '&code='.$code;
        $url .= '&grant_type=authorization_code';

        $rtn = curl_get($url);
        $res = json_decode($rtn, TRUE);
        return $res;
    }

    /**
     * 4、拉取用户信息(需scope为 snsapi_userinfo)
     *
     * @param        $access_token
     * @param        $openid
     * @param string $lang
     *
     * @return mixed
     */
    public function getUserInfoByAuth($access_token, $openid, $lang = 'zh_CN') 
    {
        $url = self::$_URL_API_ROOT."/sns/userinfo?";
        $url .= 'access_token='.$access_token;
        $url .= '&openid='.$openid;
        $url .= '&lang='.$lang;

        $rtn = curl_get($url);

        $res = json_decode($rtn, TRUE);
        return $res;
    }
    
    /**
     *获取access_token
     *
     */
    public function getAccessToken()
    {
    	$tokenInfo = Cache::get('access_token_info');
    	$accessTokenInfo = json_decode($tokenInfo, TRUE);
    	
    	if (isset($accessTokenInfo['access_token']) && time() > $accessTokenInfo['expires_in']) {
    		return $accessTokenInfo['access_token'];
    	}
    	
    	$url  = self::$_URL_API_ROOT."/cgi-bin/token?";
		$url .= "grant_type=client_credential";
		$url .= "&appid=".$this->appId;
		$url .= "&secret=".$this->appsecret;
		
		$rtn = curl_get($url);
		$res = json_decode($rtn, TRUE);
		
		if (self::check($res)) {
			$expires_in = time() + ((int)$res['expires_in'] - 1800);
			$accessTokenInfo = [
				'access_token' => $res['access_token'],
				'expires_in'   => $expires_in
			];
			
			Cache::set('access_token_info', json_encode($accessTokenInfo));
		}
		
		return $accessTokenInfo['access_token'];
    }
    
    /**
     * 发送模版信息
     * 
     */
    public function sendTemplate($temp)
    {
    	$retData = array();
    	
    	if (!empty($tmep)) {
    		$asseccToken = $this->getAccessToken();
    		$url  = self::$_URL_API_ROOT."/cgi-bin/message/template/send?";
			$url .= "access_token={$asseccToken}";
			
    		$rtn     = curl_post_raw($url, $tmep);
    		$retData = json_decode($rtn, TRUE);
    	}
    	
    	return $retData;
    }
    
    /**
     * 校验返回数据
     * 
     */
    public static function check($res)
    {
    	if (is_string($res)) {
    		$res = json_decode($res);
    	}
    	
    	if (isset($res['errcode']) && (0 != $res['errcode'])) {
    		return false;
    	}
    	
    	return true;
    } 
    
}