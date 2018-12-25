<?php
/**
 * Created by PhpStorm.
 * User: sunxiaofeng
 * Date: 2018/10/15
 * Time: 18:16
 * 微信用户模型
 */

namespace app\shop\model;

use app\shop\model\BaseModel;

class WxUserResourceModel extends BaseModel
{
    //设置主键ID
    protected $pk = 'wx_user_resource_id';

    //更新用户信息
    public function updUserData($userData, $openid)
    {
        $userInfo = self::where('user_wx_id', $openid)->find();

        if ($userInfo) {
            //更新
            $userInfo->save($userData, [$this->pk => $userInfo->wx_user_resource_id]);
        } else {
            //创建
            $this->save($userData);
        }
    }
}