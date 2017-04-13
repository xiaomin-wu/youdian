<?php
/**
 * 外送系统
 * @author 新睿社区
 * @QQ 10373458 * @url http://www.010xr.com/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$_W['page']['title'] = '外卖订单&商家设置';
$do = 'takeout';
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'set';

if($op == 'set') {
	$config = sys_config();
	if(empty($config['id'])) {
		message('请先进行基本参数设置', $this->createWebUrl('ptfconfig'), 'info');
	}
	$config_takeout = $config['takeout'];
	if(!empty($config_takeout['map'])) {
		$config_takeout['map']['lat'] = $config_takeout['map']['location_x'];
		$config_takeout['map']['lng'] = $config_takeout['map']['location_y'];
	}
	$delivery_times = get_config_text('takeout_delivery_time');

	if(checksubmit('submit')) {
		if(empty($_GPC['times']['start'])) {
			message('请先生成配送时间段', '', 'info');
		}
		$grant_credit = array(
			'credit1' => array(
				'status' => intval($_GPC['credit1']['status']),
				'grant_type' => intval($_GPC['credit1']['grant_type']),
			)
		);
		$grant_credit['credit1']['grant_num'] = ($grant_credit['credit1']['grant_type'] == 1 ? intval($_GPC['credit1']['grant_num_1']) : intval($_GPC['credit1']['grant_num_2']));
		$takeout = array(
			'map' => array(
				'location_x' => trim($_GPC['map']['lat']),
				'location_y' => trim($_GPC['map']['lng']),
			),
			'city' => trim($_GPC['city']),
			'serve_radius' => intval($_GPC['serve_radius']),
			'grant_credit' => $grant_credit,
			'custom_goods_sailed_status' => intval($_GPC['custom_goods_sailed_status']),
			'pay_time_limit' => intval($_GPC['pay_time_limit']),
			'handle_time_limit' => intval($_GPC['handle_time_limit']),
			'auto_success_hours' => intval($_GPC['auto_success_hours']),
			'delivery_mode' => intval($_GPC['delivery_mode']),
			'deliveryer_fee_type' => intval($_GPC['deliveryer_fee_type']) ? intval($_GPC['deliveryer_fee_type']) : 1,
			'delivery_fee_mode' => intval($_GPC['delivery_fee_mode']),
			'delivery_fee' => intval($_GPC['delivery_fee']),
			'pre_delivery_time_minute' => intval($_GPC['pre_delivery_time_minute']),
			'auto_get_address' => intval($_GPC['auto_get_address']),
			'dispatch_mode' => intval($_GPC['dispatch_mode']),
			'deliveryer_collect_max' => intval($_GPC['deliveryer_collect_max']),
			'over_collect_max_notify' => intval($_GPC['over_collect_max_notify']),
		);
		$takeout['deliveryer_fee'] = $takeout['deliveryer_fee_type'] == 1 ? intval($_GPC['deliveryer_fee_1']) : intval($_GPC['deliveryer_fee_2']);

		if($takeout['delivery_fee_mode'] == 2) {
			$takeout['delivery_fee'] = array(
				'start_fee' => trim($_GPC['start_fee']),
				'start_km' => trim($_GPC['start_km']),
				'pre_km_fee' => trim($_GPC['pre_km_fee']),
			);
		}
		pdo_update('tiny_wmall_plus_config', array('takeout' => iserializer($takeout)) , array('uniacid' => $_W['uniacid']));
		pdo_query('update ' .  tablename('tiny_wmall_plus_store_deliveryer') . ' set delivery_type = :delivery_type where uniacid = :uniacid', array(':uniacid' => $_W['uniacid'], ':delivery_type' => $takeout['delivery_mode']));

		$times = array();
		if(!empty($_GPC['times']['start'])) {
			foreach($_GPC['times']['start'] as $key => $val) {
				$start = trim($val);
				$end = trim($_GPC['times']['end'][$key]);
				if(empty($start) || empty($end)) {
					continue;
				}
				$times[] = array(
					'start' => $start,
					'end' => $end,
					'status' => intval($_GPC['times']['status'][$key]),
					'fee' => intval($_GPC['times']['fee'][$key])
				);
			}
		}
		set_config_text('takeout_delivery_time', iserializer($times));

		$delivery_sync = intval($_GPC['delivery_sync']);
		if($delivery_sync == 1) {
			$update = array(
				'delivery_mode' => $takeout['delivery_mode'],
				'delivery_fee_mode' => $takeout['delivery_fee_mode'],
				'auto_get_address' => $takeout['auto_get_address'],
				'delivery_price' => $takeout['delivery_fee'],
				'delivery_free_price' => 0,
				'delivery_times' => iserializer($times)
			);
			if($takeout['delivery_fee_mode'] == 2) {
				$update['delivery_price'] = iserializer($takeout['delivery_fee']);
				$update['not_in_serve_radius'] = 1;
				$update['auto_get_address'] = 1;
			}
			pdo_update('tiny_wmall_plus_store', $update, array('uniacid' => $_W['uniacid']));
			$stores = pdo_getall('tiny_wmall_plus_store', array('uniacid' => $_W['uniacid']), array('id'));
			foreach($stores as $store) {
				store_delivery_times($store['id'], true);
			}
		}
		message('外卖订单设置成功', referer(), 'success');
	}
}

include $this->template('plateform/config-takeout');