<?php
/**
 * 外送系统
 * @author 灯火阑珊
 * @QQ 2471240272
 * @url http://bbs.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'takeout';

$delivery_type = $_W['we7_wmall']['deliveryer']['type'];
$delivery_stores = implode(', ', $_W['we7_wmall']['deliveryer']['store']);
$deliveryer = $_W['we7_wmall']['deliveryer']['user'];
if($op == 'takeout') {
	$type = trim($_GPC['type']) ? trim($_GPC['type']) : 'date';
	$date = trim($_GPC['date']);
	if(empty($date)) {
		message(ierror(-1, '请选择日期'), '', 'ajax');
	}
	if($type == 'date') {
		$starttime = strtotime($date);
		$endtime = $starttime + 86400;
	}
	$stat = array(
		'plateform' => array(
			'num' => 0,
			'fee' => 0,
		),
		'store' => array()
	);
	$stat['plateform']['num'] = intval(pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_order') . ' where uniacid = :uniacid and deliveryer_id = :deliveryer_id and delivery_type = 2 and status =5 and deliveryedtime >= :starttime and deliveryedtime <= :endtime', array(':uniacid' => $_W['uniacid'], ':deliveryer_id' => $deliveryer_id, ':starttime' => $starttime, ':endtime' => $endtime)));
	//$stat['plateform']['fee'] = floatval(pdo_fetchcolumn('select sum(deliveryer_fee) from ' . tablename('tiny_wmall_order') . ' where uniacid = :uniacid and deliveryer_id = :deliveryer_id and delivery_type = 2 and status =5 and deliveryedtime >= :starttime and deliveryedtime <= :endtime', array(':uniacid' => $_W['uniacid'], ':deliveryer_id' => $deliveryer_id, ':starttime' => $starttime, ':endtime' => $endtime)));
	message(ierror(0, '', $stat), '', 'ajax');
}