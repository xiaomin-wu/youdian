<?php

defined('IN_IA') or die('Access Denied');
function cron_order()
{
	global $_W;
	$config_takeout = $_W['we7_wmall_plus']['config']['takeout'];
	if ($config_takeout['pay_time_limit'] > 0) {
		$orders = pdo_fetchall('select id, sid, addtime from ' . tablename('tiny_wmall_plus_order') . ' where uniacid = :uniacid and is_pay = 0 and status = 1 and order_type <= 2 and addtime <= :addtime limit 5', array(':uniacid' => $_W['uniacid'], ':addtime' => time() - $config_takeout['pay_time_limit'] * 60));
		if (!empty($orders)) {
			$extra = array('note' => "提交订单{$config_takeout['pay_time_limit']}分钟内未支付,系统已自动取消订单");
			foreach ($orders as $order) {
				order_status_update($order['id'], 'cancel', $extra);
			}
		}
	}
	if ($config_takeout['handle_time_limit'] > 0) {
		$orders = pdo_fetchall('select id, sid, addtime from ' . tablename('tiny_wmall_plus_order') . ' where uniacid = :uniacid and is_pay = 1 and status = 1 and order_type <= 2 and addtime <= :addtime limit 5', array(':uniacid' => $_W['uniacid'], ':addtime' => time() - $config_takeout['handle_time_limit'] * 60));
		if (!empty($orders)) {
			$extra = array('note' => "{$config_takeout['handle_time_limit']}分钟内商户未接单,系统已自动取消订单");
			foreach ($orders as $order) {
				order_status_update($order['id'], 'cancel', $extra);
			}
		}
	}
	if ($config_takeout['auto_success_hours'] > 0) {
		$orders = pdo_fetchall('select id, sid, handletime from ' . tablename('tiny_wmall_plus_order') . ' where uniacid = :uniacid and status >= 2 and order_type <= 2 and handletime > 0 and handletime < :handletime order by id asc limit 5', array(':uniacid' => $_W['uniacid'], ':handletime' => time() - $config_takeout['auto_success_hours'] * 3600));
		if (!empty($orders)) {
			$extra = array('note' => "系统已自动完成订单");
			foreach ($orders as $order) {
				order_status_update($order['id'], 'end', $extra);
			}
		}
	}
	$config_settle = $_W['we7_wmall_plus']['config']['settle'];
	if ($config_settle['store_label_new'] > 0 && empty($_GPC['__store_new'])) {
		mload()->model('build');
		build_category('TY_store_label');
		$new = pdo_get('tiny_wmall_plus_category', array('uniacid' => $_W['uniacid'], 'type' => 'TY_store_label', 'alias' => 'new'));
		if (!empty($new)) {
			$params = array(':uniacid' => $_W['uniacid'], ':label' => $new['id'], ':addtime' => time() - $config_settle['store_label_new'] * 86400);
			$data = pdo_query('update ' . tablename('tiny_wmall_plus_store') . ' set label = :label where uniacid = :uniacid and label = 0 and addtime > :addtime', $params);
			pdo_query('update ' . tablename('tiny_wmall_plus_store') . ' set label = 0 where uniacid = :uniacid and label = :label and addtime < :addtime', $params);
		}
		isetcookie('__store_new', 1, 3600);
	}
	mload()->model('bargain');
	bargain_update_goods_total();
	if (MODULE_FAMILY != 'basic') {
		mload()->model('errander');
		$config_errander = $_W['we7_wmall_plus']['config']['errander'];
		if ($config_errander['pay_time_limit'] > 0) {
			$orders = pdo_fetchall('select id, addtime from ' . tablename('tiny_wmall_plus_errander_order') . ' where uniacid = :uniacid and is_pay = 0 and status = 1 and addtime <= :addtime limit 5', array(':uniacid' => $_W['uniacid'], ':addtime' => time() - $config_errander['pay_time_limit'] * 60));
			if (!empty($orders)) {
				$extra = array('note' => "提交订单{$config_errander['pay_time_limit']}分钟内未支付, 系统已自动取消订单");
				foreach ($orders as $order) {
					errander_order_status_update($order['id'], 'cancel', $extra);
				}
			}
		}
		if ($config_errander['handle_time_limit'] > 0) {
			$orders = pdo_fetchall('select id, addtime from ' . tablename('tiny_wmall_plus_errander_order') . ' where uniacid = :uniacid and is_pay = 1 and status = 1 and paytime <= :paytime limit 5', array(':uniacid' => $_W['uniacid'], ':paytime' => time() - $config_errander['handle_time_limit'] * 60));
			if (!empty($orders)) {
				$extra = array('note' => "平台{$config_errander['handle_time_limit']}分钟内未接单, 系统已自动取消订单");
				foreach ($orders as $order) {
					errander_order_status_update($order['id'], 'cancel', $extra);
				}
			}
		}
	}
	return true;
}