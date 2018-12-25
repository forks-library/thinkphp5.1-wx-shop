<?php
/*
 * Created on 2018年12月4日
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 * 监听任务并执行， 在项目根目录运行， 可以写个linux定时任务 定时执行
 * 两者具体区别请自行学习。
 * php think queue:listen 
 * php think queue:work --daemon（不加--daemon为执行单个任务）
 */
namespace app\shop\service;

use think\Queue;

class WxQueue {
	
	/**
	 * 普通队列
	 * @param string $job 任务类
	 * @param array  $data 要传输的数据
	 * @paran string $queue 队列名称
	 */
	public static function pushJob($job, $data, $queue = null) 
	{
		if (strpos($job, '@')) {
			$className = \strstr($job, '@', true);
		} else {
			$className = $job;
		}
		
		if (class_exists($className)) {
			return Queue::push($job, $data, $queue);
		} 
		
		return json(array('error' => '任务类不存在！！！'));
	}
	
	/**
	 * 延迟队列
	 * @param number $delay, 延迟时间 秒
	 * @param string $job, 任务类
	 * @param array  $data, 要传输的数据
	 * @param string $queue 队列名称
	 */
	 public static function laterJob($delay, $job, $data = '', $queue = null)
	 {
	 	if (strpos($job, '@')) {
			$className = \strstr($job, '@', true);
		} else {
			$className = $job;
		}
		
	 	if (class_exists($className)) {
	 		if (intval($delay) > 0) {
	 			return Queue::later($delay, $job, $data, $queue);
	 		}
	 		
	 		return json(array('error' => '延迟时间必须大于0！！！'));
	 	}
	 	
	 	return json(array('error' => '任务类不存在！！！'));
	 }
}
