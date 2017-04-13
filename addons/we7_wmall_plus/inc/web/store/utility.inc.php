<?php
/**
 * 外送系统
 * @author 新睿社区
 * @QQ 10373458 * @url http://www.010xr.com/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;

$op = trim($_GPC['op']);

if($op == 'deliveryer') {
	$sid = intval($_GPC['sid']);
	$store = store_fetch($sid, array('delivery_mode'));
	if(empty($store)) {
		message(error(-1, '门店账户不存在'), '', 'ajax');
	}
	if($store['delivery_mode'] == 2) {
		message(error(-1, '你没有使用店内配送员的权限, 请联系平台管理员'), '', 'ajax');
	}
	$condition = ' where uniacid = :uniacid and sid = :sid';
	$params = array(':uniacid' => $_W['uniacid'], ':sid' => $sid);
	$data = pdo_fetchall('select * from ' . tablename('tiny_wmall_plus_store_deliveryer') . $condition, $params);
	if(!empty($data)) {
		foreach($data as &$da) {
			$da['deliveryer'] = pdo_get('tiny_wmall_plus_deliveryer', array('id' => $da['deliveryer_id']));
		}
	}
	message(error(0, $data), '', 'ajax');
}