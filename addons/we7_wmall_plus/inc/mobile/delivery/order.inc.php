<?php
/**
 * 外送系统
 * @author 新睿社区
 * @QQ 10373458 * @url http://www.010xr.com/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$do = 'dyorder';
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'list';
$title = '外卖订单管理';

$delivery_type = $_W['we7_wmall_plus']['deliveryer']['type'];
$delivery_stores = implode(', ', $_W['we7_wmall_plus']['deliveryer']['store']);
$deliveryer = $_W['we7_wmall_plus']['deliveryer']['user'];

if($op == 'list') {
	$condition = ' WHERE uniacid = :uniacid';
	$params[':uniacid'] = $_W['uniacid'];
	$status = isset($_GPC['status']) ? intval($_GPC['status']) : 3;
	if($status == 4) {
		$condition .= ' and (delivery_status = 4 or delivery_status = 7)';
	} else {
		$condition .= ' and delivery_status = :status';
		$params[':status'] = $status;
	}
	if($status == 3) {
		if($delivery_type == 1) {
			$condition .= ' and delivery_type = 2';
		} elseif ($delivery_type == 2) {
			$condition .= " and delivery_type = 1 and sid in ({$delivery_stores})";
		} else {
			$condition .= " and (delivery_type = 2 or (delivery_type = 1 and sid in ({$delivery_stores})))";
		}
	} else {
		$condition .= ' and deliveryer_id = :deliveryer_id';
		$params[':deliveryer_id'] = $deliveryer['id'];
		$condition .= ' order by id desc limit 15';
	}
	$orders = pdo_fetchall('SELECT id, serial_sn, addtime, is_pay, pay_type, status, username, mobile, address, delivery_status, delivery_type, delivery_time,sid, num, final_fee FROM ' . tablename('tiny_wmall_plus_order') . $condition, $params, 'id');
	$min = 0;
	if(!empty($orders)) {
		$stores_id = array();
		foreach($orders as &$da) {
			if($da['delivery_status'] == 7) {
				$da['delivery_status'] = 4;
			}
			if($da['delivery_type'] == 2) {
				if($config_takeout['deliveryer_fee_type'] == 1) {
					$da['deliveryer_fee'] = $config_takeout['deliveryer_fee'];
				} else {
					$da['deliveryer_fee'] = round($da['final_fee'] * $config_takeout['deliveryer_fee'] / 100, 2);
				}
			}
			$da['pay_type_class'] = '';
			if($da['is_pay'] == 1) {
				$da['pay_type_class'] = 'have-pay';
				if($da['pay_type'] == 'delivery') {
					$da['pay_type_class'] = 'delivery-pay';
				}
			}
			$stores_id[] = $da['sid'];
		}
		$stores_str = implode(',', array_unique($stores_id));
		$stores = pdo_fetchall('select id, title, address from ' . tablename('tiny_wmall_plus_store') . " where uniacid = :uniacid and id in ({$stores_str})", array(':uniacid' => $_W['uniacid']), 'id');
		$min = min(array_keys($orders));
	}
	$delivery_status = order_delivery_status();
	include $this->template('delivery/order-list');
}

if($op == 'more') {
	$id = intval($_GPC['id']);
	$status = intval($_GPC['status']);
	$orders = pdo_fetchall('select * from ' . tablename('tiny_wmall_plus_order') . ' where uniacid = :uniacid and delivery_status = :delivery_status and deliveryer_id = :deliveryer_id and id < :id order by id desc limit 15', array(':uniacid' => $_W['uniacid'], ':delivery_status' => $status, ':deliveryer_id' => $deliveryer['id'], ':id' => $id), 'id');
	$min = 0;
	if(!empty($orders)) {
		$delivery_status = order_delivery_status();
		foreach ($orders as &$row) {
			$row['addtime_cn'] = date('Y-m-d H:i:s', $row['addtime']);
			$row['status_color'] = $delivery_status[$row['delivery_status']]['color'];
			$row['status_cn'] = $delivery_status[$row['delivery_status']]['text'];
			$row['store_address'] = pdo_fetchcolumn('select address from ' . tablename('tiny_wmall_plus_store') . ' where uniacid = :uniacid and id = :sid', array(':uniacid' => $_W['uniacid'], ':sid' => $row['sid']));
		}
		$min = min(array_keys($orders));
	}
	$orders = array_values($orders);
	$respon = array('error' => 0, 'message' => $orders, 'min' => $min);
	message($respon, '', 'ajax');
}

if($op == 'detail') {
	$title = '订单详情';
	$id = intval($_GPC['id']);
	$order = order_fetch($id);
	if(empty($order)) {
		message('订单不存在或已删除', '', 'error');
	}
	if($order['delivery_type'] == 2) {
		if($config_takeout['deliveryer_fee_type'] == 1) {
			$order['deliveryer_fee'] = $config_takeout['deliveryer_fee'];
		} else {
			$order['deliveryer_fee'] = round($order['final_fee'] * $config_takeout['deliveryer_fee'] / 100, 2);
		}
	}
	$goods = order_fetch_goods($order['id']);
	if($order['discount_fee'] > 0) {
		$activityed = order_fetch_discount($id);
	}
	$log = pdo_fetch('select * from ' . tablename('tiny_wmall_plus_order_status_log') . ' where uniacid = :uniacid and oid = :oid order by id desc', array(':uniacid' => $_W['uniacid'], ':oid' => $id));
	$store = store_fetch($order['sid'], array('id', 'title', 'address', 'telephone', 'logo', 'location_x', 'location_y'));
	$order_types = order_types();
	$pay_types = order_pay_types();
	$order_status = order_status();
	$deliveryer = deliveryer_fetch($order['deliveryer_id']);
	include $this->template('delivery/order-detail');
}

if($op == 'collect') {
	$id = intval($_GPC['id']);
	$result = order_deliveryer_update_status($id, 'delivery_assign', array('deliveryer_id' => $deliveryer['id']));
	if(is_error($result)) {
		message($result, '', 'ajax');
	}
	message(error(0, '抢单成功'), '', 'ajax');
}

if($op == 'success') {
	$id = intval($_GPC['id']);
	$result = order_deliveryer_update_status($id, 'delivery_success', array('deliveryer_id' => $deliveryer['id']));
	if(is_error($result)) {
		message($result, '', 'ajax');
	}
	message(error(0, '确认送达成功'), '', 'ajax');
}

if($op == 'notice') {
	$id = intval($_GPC['id']);
	$order = pdo_get('tiny_wmall_plus_order', array('uniacid' => $_W['uniacid'], 'id' => $id));
	if(empty($order)) {
		message(error(-1, '订单不存在或已经删除'), '', 'ajax');
	}
	if($order['delivery_id'] > 0 && $order['delivery_id'] != $deliveryer['id']) {
		message(error(-1, '该订单不是由您配送,不能进行微信通知'), '', 'ajax');
	}
	$content = array('title' => $deliveryer['title'], 'mobile' => $deliveryer['mobile']);
	order_status_notice($id, 'delivery_notice', $content);
	message(error(0, '通知成功'), '', 'ajax');
}


