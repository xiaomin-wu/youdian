<?php
/**
 * 外送系统
 * @author 新睿社区
 * @QQ 10373458 * @url http://www.010xr.com/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'index';
$deliveryer = $_W['we7_wmall_plus']['deliveryer']['user'];

if($op == 'index') {
	$type = trim($_GPC['type']) ? trim($_GPC['type']) : 'today';
	if($type == 'today') {
		$starttime = strtotime(date('Y-m-d'));
		$endtime = $starttime + 86399;
	} elseif($type == 'week') {
		$starttime = mktime(0, 0, 0, date('m'), date('d') - date('w') + 6,date('Y'));
		$endtime = mktime(23, 59, 59, date('m'), date('d') - date('w'), date('Y'));
	} elseif($type == 'month') {
		$starttime =  mktime(0, 0, 0, date('m'), 1, date('Y'));
		$endtime = mktime(23, 59, 59, date('m'), date('t'), date('Y'));
	} elseif($type == 'custom') {
		$start = trim($_GPC['start']);
		$end = trim($_GPC['end']);
		if(empty($start) || empty($end)) {
			message(ierror(-1, '请选择日期'), '', 'ajax');
		}
		$starttime = strtotime($start);
		$endtime = strtotime($end) + 86399;
	}

	$stat = array(
		'takeout' => array(
			'num' => 0,
			'fee' => 0,
		),
		'errander' => array(
			'num' => 0,
			'fee' => 0,
		),
		'total' => array(
			'num' => 0,
			'fee' => 0,
		),
		'time' => array(
			'start' => date('Y-m-d H:i', $starttime),
			'end' => date('Y-m-d H:i', $endtime),
		)
	);
	$stat['takeout']['num'] = intval(pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_plus_order') . ' where uniacid = :uniacid and deliveryer_id = :deliveryer_id and delivery_type = 2 and status = 5 and deliveryedtime >= :starttime and deliveryedtime <= :endtime', array(':uniacid' => $_W['uniacid'], ':deliveryer_id' => $deliveryer_id, ':starttime' => $starttime, ':endtime' => $endtime)));
	//$stat['takeout']['fee'] = floatval(pdo_fetchcolumn('select sum(delivery_fee) from ' . tablename('tiny_wmall_plus_order_current_log') . ' where uniacid = :uniacid and deliveryer_id = :deliveryer_id and delivery_type = 2 and status = 5 and deliveryedtime >= :starttime and deliveryedtime <= :endtime', array(':uniacid' => $_W['uniacid'], ':deliveryer_id' => $deliveryer_id, ':starttime' => $starttime, ':endtime' => $endtime)));
	$stat['errander']['num'] = intval(pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_plus_errander_order') . ' where uniacid = :uniacid and deliveryer_id = :deliveryer_id and status = 3 and delivery_success_time >= :starttime and delivery_success_time <= :endtime', array(':uniacid' => $_W['uniacid'], ':deliveryer_id' => $deliveryer_id, ':starttime' => $starttime, ':endtime' => $endtime)));
	$stat['errander']['fee'] = floatval(pdo_fetchcolumn('select sum(deliveryer_total_fee) from ' . tablename('tiny_wmall_plus_errander_order') . ' where uniacid = :uniacid and deliveryer_id = :deliveryer_id and status = 3 and delivery_success_time >= :starttime and delivery_success_time <= :endtime', array(':uniacid' => $_W['uniacid'], ':deliveryer_id' => $deliveryer_id, ':starttime' => $starttime, ':endtime' => $endtime)));
	message(ierror(0, '', $stat), '', 'ajax');
}