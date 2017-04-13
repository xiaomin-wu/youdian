<?php
/**
 * [WeEngine System] Copyright (c) 2013 WE7.CC
 * $sn$
 */
defined('IN_IA') or exit('Access Denied');

function bargain_status() {
	$data = array(
		'ongoing' => array(
			'css' => 'label label-success',
			'text' => '进行中',
			'color' => 'color-success',
		),
		'end' => array(
			'css' => 'label label-danger',
			'text' => '活动未开始或已结束',
			'color' => 'color-danger',
		),
	);
	return $data;
}

function bargain_update_status($sid = 0) {
	global $_W;
	$params = array(
		':uniacid' => $_W['uniacid'],
		':sid' => $sid,
		':time' => TIMESTAMP,
		':hour' => date('Hi'),
	);
	$params[':hour'] = ltrim($params[':hour'], '0');
	pdo_query('update ' . tablename('tiny_wmall_plus_activity_bargain') . " set status = 'ongoing' where uniacid = :uniacid and starttime < :time and endtime > :time and starthour < :hour and endhour > :hour", $params);
	pdo_query('update ' . tablename('tiny_wmall_plus_activity_bargain') . " set status = 'end' where uniacid = :uniacid and (starttime > :time or endtime < :time or starthour > :hour or endhour < :hour)", $params);
	return true;
}

function bargain_update_goods_total() {
	global $_W;
	$params = array(
		':uniacid' => $_W['uniacid'],
		':time' => TIMESTAMP,
		':status' => 'ongoing',
	);
	$bargains = pdo_fetchall('select id from ' . tablename('tiny_wmall_plus_activity_bargain') . ' where uniacid = :uniacid and status = :status and total_updatetime < :time limit 3 order by total_updatetime asc', $params);
	if(!empty($bargains)) {
		$time = strtotime(date('Y-m-d')) + 86400;
		foreach($bargains as $bargain) {
			pdo_query('update' . tablename('tiny_wmall_plus_activity_bargain_goods') . ' set discount_available_total = discount_total where bargain_id = :bargain_id', array(':bargain_id' => $bargain['id']));
			pdo_update('tiny_wmall_plus_activity_bargain', array('total_updatetime' => $time), array('id' => $bargain['id']));
		}
	}
	return true;
}

