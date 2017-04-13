<?php
/**
 * 外送系统
 * @author 新睿社区
 * @QQ 10373458 * @url http://www.010xr.com/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;

$stores = pdo_fetchall('select id, uniacid, title from ' . tablename('tiny_wmall_plus_store') . ' where 1 order by id asc');
$i = 0;
if(!empty($stores)) {
	$settle_config = sys_settle_config($store['uniacid']);
	$delivery_config = sys_delivery_config($store['uniacid']);
	foreach($stores as $store) {
		$account = pdo_get('tiny_wmall_plus_store_account', array('sid' => $store['id']));
		if(empty($account)) {
			$store_account = array(
				'uniacid' => $store['uniacid'],
				'sid' => $store['id'],
				'delivery_type' => $delivery_config['delivery_type'],
				'fee_limit' => $settle_config['get_cash_fee_limit'],
				'fee_rate' => $settle_config['get_cash_fee_rate'],
				'fee_min' => $settle_config['get_cash_fee_min'],
				'fee_max' => $settle_config['get_cash_fee_max'],
			);
			pdo_insert('tiny_wmall_plus_store_account', $store_account);
			$i++;
		}
	}
}
message("共影响{$i}行数据");