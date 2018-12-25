<?php
/*
 * Created on 2018年12月15日
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 * 邮件发送队列任务类
 */
namespace app\shop\job;

use think\queue\Job;
use app\shop\service\SendEmail;

class EmailJob {
	
	public function fire(Job $job,$data)
	{
		//通过这个方法可以检查这个任务已经重试了几次了
		if($job->attempts() > 3) {
			\think\facade\Log::write("邮件发送队列执行失败:{$data}");
			$job->delete();
			return;
		}
		
		$data      = json_decode($data, true);
		$sendEmail = new SendEmail();
		$cc = isset($data['toCC']) && !empty($data['toCC']) ? $data['toCC'] : array();
		$bln = $sendEmail->send($data['title'], $data['body'], $data['toUser'], $cc);
		if ($bln) {
			//执行成功删除任务
			$job->delete();
		} else {
			//重新发布
			$job->release();
		}
	}
	
	public function failed($data)
	{
        // ...任务达到最大重试次数后，失败了
        $job->delete();
        \think\facade\Log::write("邮件发送队列执行失败:{$data}");
    }
}
