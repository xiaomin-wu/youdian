<?php
/**
 * 外送系统
 * @author 灯火阑珊
 * @QQ 2471240272
 * @url http://bbs.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;

$op = trim($_GPC['op']);

if($op == 'deliveryer') {
	$sid = intval($_GPC['sid']);
	$account = pdo_get('tiny_wmall_store_account', array('uniacid' => $_W['uniacid'], 'sid' => $sid));
	if(empty($account)) {
		message(error(-1, '门店账户不存在'), '', 'ajax');
	}
	if($account['delivery_type'] == 2) {
		message(error(-1, '你没有使用店内配送员的权限, 请联系平台管理员'), '', 'ajax');
	}
	$condition = ' where uniacid = :uniacid and sid = :sid';
	$params = array(':uniacid' => $_W['uniacid'], ':sid' => $sid);
	$data = pdo_fetchall('select * from ' . tablename('tiny_wmall_store_deliveryer') . $condition, $params);
	if(!empty($data)) {
		foreach($data as &$da) {
			$da['deliveryer'] = pdo_get('tiny_wmall_deliveryer', array('id' => $da['deliveryer_id']));
		}
	}
	message(error(0, $data), '', 'ajax');
}