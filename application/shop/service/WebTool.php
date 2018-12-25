<?php
/**
 * Created by PhpStorm.
 * User: sunxiaofeng
 * Date: 2018/10/17
 * Time: 11:24
 */

namespace app\shop\service;

/**
 * 工具类
 */
class WebTool
{
    //判断是否为iOS系统
    public static function isIos()
    {
        $userAgent = $_SERVER["HTTP_USER_AGENT"];
        return strpos($userAgent, "iPhone") || strpos($userAgent, "iPad");
    }
    //判断是否为android系统
    public static function isAndroid()
    {
        $userAgent = $_SERVER["HTTP_USER_AGENT"];
        return strpos($userAgent, "Android");
    }

    //获取当前URL
    public static function  currentUrl()
    {
        $httpType = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on')
            || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https'))
            ? 'https://' : 'http://';

        return $httpType.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
    }
}