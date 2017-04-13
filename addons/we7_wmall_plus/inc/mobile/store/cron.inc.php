<?php
/**
 * 外送系统
 * @author 新睿社区
 * @QQ 10373458 * @url http://www.010xr.com/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$do = 'cron';
$op = trim($_GPC['op']);
$sid = intval($_GPC['sid']);

if($op == 'order_notice') {
	$order = pdo_fetch('SELECT id FROM ' . tablename('tiny_wmall_plus_order') . ' WHERE uniacid = :uniacid AND sid = :sid AND is_notice = 0 ORDER BY id asc', array(':uniacid' => $_W['uniacid'], ':sid' => $sid));
	if(!empty($order)) {
		pdo_update('tiny_wmall_plus_order', array('is_notice' => 1), array('uniacid' => $_W['uniacid'], 'id' => $order['id']));
		exit('success');
	}
	exit('error');
}

if($op == 'order_cancel') {
	init_cron();
}

