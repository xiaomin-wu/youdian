<?php
/**
 * 外送系统
 * @author 新睿社区
 * @QQ 10373458 * @url http://www.010xr.com/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$sid = intval($_GPC['sid']);
isetcookie('__sid', $sid, 86400 * 7);
$url = $this->createWebUrl('order');
$forward = trim($_GPC['forward']);
if($forward == 'clerk') {
	$url = $this->createWebUrl('clerk', array('role' => 'manager', 'op' => 'post'));
}
$account = store_account($sid);
if(empty($account)) {
	$config_settle = $_W['we7_wmall_plus']['config']['settle'];
	$insert = array(
		'uniacid' => $_W['uniacid'],
		'sid' => $sid,
		'fee_limit' => $config_settle['get_cash_fee_limit'],
		'fee_rate' => $config_settle['get_cash_fee_rate'],
		'fee_min' => $config_settle['get_cash_fee_min'],
		'fee_max' => $config_settle['get_cash_fee_max'],
	);
	pdo_insert('tiny_wmall_plus_store_account', $insert);
}
header('location: ' . $url);
exit();
