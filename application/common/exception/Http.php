<?php
/**
 * Created by PhpStorm.
 * User: sunxiaofeng
 * Date: 2018/10/19
 * Time: 10:16
 */

namespace app\common\exception;

use Exception;
use think\exception\Handle;
use think\exception\HttpException;
use think\exception\ValidateException;

//错误验证类
class Http extends Handle
{
    public function render(Exception $e)
    {
        // 参数验证错误
        if ($e instanceof ValidateException && request()->isAjax()) {
            return json($e->getError(), $e->getStatusCode());
        }
        
        // 请求异常
        if ($e instanceof HttpException && request()->isAjax()) {
            return response($e->getMessage(), $e->getStatusCode());
        }
        
        // 其他错误交给系统处理
        return parent::render($e);
    }

}
