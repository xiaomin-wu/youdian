<?php
/**
 * 外送系统
 * @author 新睿社区
 * @QQ 10373458 * @url http://www.010xr.com/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$do = 'utility';
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'build_time';

if($op == 'build_time') {
	if($_W['isajax']) {
		$pre_minute = intval($_GPC['pre_minute']);
		$starttime = strtotime(date('Y-m-d'));
		$endtime = $starttime + 86399;
		$times = array();
		for($start = $starttime; $start < $endtime;) {
			$end = $start + $pre_minute * 60;
			if($end >= $endtime) {
				$end = $starttime + 86340;
			}
			$times[] = array(
				'start' => date('H:i', $start),
				'end' => date('H:i', $end),
				'fee' => 0,
				'status' => 1,
			);
			$start += $pre_minute * 60;
		}
		message(error(0, $times), '', 'ajax');
	}
}


