<?php
/**
 * 外送系统
 * @author 新睿社区
 * @QQ 10373458 * @url http://www.010xr.com/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
mload()->model('cron');
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'order_status';

set_time_limit(0);
if($op == 'order_status') {
	cron_order();
	load()->model('build');
	build_category('TY_store_label');
	exit('success');
}

if($op == 'order_notice') {
	if($_GPC['_do'] == 'ptforder-takeout' && $_GPC['_status_order_notice']) {
		$order = pdo_get('tiny_wmall_plus_order', array('uniacid' => $_W['uniacid'], 'status' => 1, 'is_pay' => 1));
		if(!empty($order)) {
			exit('success');
		}
		exit('error');
	}
	if($_GPC['_do'] == 'ptforder-errander' && $_GPC['_status_errander_notice']) {
		$order = pdo_get('tiny_wmall_plus_errander_order', array('uniacid' => $_W['uniacid'], 'status' => 1, 'is_pay' => 1));
		if(!empty($order)) {
			exit('success');
		}
		exit('error');
	}
	$store = store_check();
	$sid = $store['id'];
	if(!$store['pc_notice_status']) {
		exit('error');
	}
	$order = pdo_fetch('SELECT id FROM ' . tablename('tiny_wmall_plus_order') . ' WHERE uniacid = :uniacid AND sid = :sid and status = 1 and is_pay = 1 ORDER BY id asc', array(':uniacid' => $_W['uniacid'], ':sid' => $sid));
	if(!empty($order)) {
		exit('success');
	}
	exit('error');
}
