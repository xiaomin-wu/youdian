<?php
/**
 * 外送系统
 * @author 灯火阑珊
 * @QQ 2471240272
 * @url http://bbs.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');
mload()->model('errander');
global $_W, $_GPC;
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'list';

$delivery_type = $_W['we7_wmall']['deliveryer']['type'];
if($delivery_type != 1) {
	message(ierror(-1, '你没有配送订单的权限'), '', 'ajax');
}
$deliveryer = $_W['we7_wmall']['deliveryer']['user'];

if($op == 'list') {
	$condition = ' WHERE uniacid = :uniacid';
	$params[':uniacid'] = $_W['uniacid'];
	$status = isset($_GPC['status']) ? intval($_GPC['status']) : 1;
	if($status == 1) {
		$condition .= ' and delivery_status = :status';
		$params[':status'] = $status;
	} else {
		$condition .= ' and delivery_status = :status and deliveryer_id = :deliveryer_id';
		$params[':deliveryer_id'] = $deliveryer['id'];
	}

	$type = trim($_GPC['type']) ? trim($_GPC['type']) : 'load';
	$id = intval($_GPC['id']);
	if($type == 'load') {
		if($id > 0) {
			$condition .= " and id < :id";
			$params[':id'] = $id;
		}
	} else {
		$condition .= " and id > :id";
		$params[':id'] = $id;
	}
	$min_id = intval(pdo_fetchcolumn('SELECT min(id) as min_id FROM ' . tablename('tiny_wmall_errander_order') . $condition , $params));
	$orders = pdo_fetchall('SELECT * FROM ' . tablename('tiny_wmall_errander_order') . $condition . ' order by id desc limit 15', $params, 'id');
	$min = $max = 0;
	if(!empty($orders)) {
		$errander_type = errander_types();
		foreach($orders as &$da) {
			$da['order_type_cn'] = $errander_type[$da['order_type']]['text'];
			$da['order_type_color'] = $errander_type[$da['order_type']]['color'];
			$da['addtime_cn'] = date('m-d H:i', $da['addtime']);
			if($da['order_type'] == 'buy') {
				$da['buy_address_title'] = '买';
				$da['accept_address_title'] = '送';
				$da['buy_address'] = ($da['buy_address'] ? $da['buy_address'] : '用户未指定，可自由选择');
			} else {
				$da['buy_address_title'] = '取';
				$da['accept_address_title'] = '送';
			}
			$da['store2user_distance'] = $da['distance'] ? $da['distance'] : '未知';
			$da['store2deliveryer_distance'] = '未知';
			if(!empty($da['buy_location_x']) && !empty($da['buy_location_y'])) {
				if(!empty($deliveryer['location_x']) && !empty($deliveryer['location_y'])) {
					$da['store2deliveryer_distance'] = distanceBetween($da['buy_location_y'], $da['buy_location_x'], $deliveryer['location_y'], $deliveryer['location_x']);
					$da['store2deliveryer_distance'] = round($da['store2deliveryer_distance'] / 1000, 2);
				}
			}
		}
		$more = 1;
		$min = min(array_keys($orders));
		$max = max(array_keys($orders));
		if($min <= $min_id) {
			$more = 0;
		}
	}
	$orders = array_values($orders);
	$data = array(
		'list' => $orders,
		'max_id' => $max,
		'min_id' => $min,
		'more' => $more
	);
	$delivery_status = order_delivery_status();
	$respon = array('resultCode' => 0, 'resultMessage' => '调用成功', 'data' => $data);
	message($respon, '', 'ajax');
}

if($op == 'detail') {
	$id = intval($_GPC['id']);
	$order = errander_order_fetch($id);
	if(empty($order)) {
		message(ierror(-1, '订单不存在或已删除'), '', 'ajax');
	}
	$errander_type = errande_types();
	$pay_types = order_pay_types();
	$order_status = errander_order_status();

	$order['order_type_cn'] = $errander_type[$order['order_type']]['text'];
	$order['order_type_color'] = $errander_type[$order['order_type']]['color'];
	$order['addtime_cn'] = date('m-d H:i', $order['addtime']);
	if($order['order_type'] == 'buy') {
		$order['buy_address_title'] = '买';
		$order['accept_address_title'] = '送';
		$order['buy_address'] = ($order['buy_address'] ? $order['buy_address'] : '用户未指定，可自由选择');
	} else {
		$order['buy_address_title'] = '取';
		$order['accept_address_title'] = '送';
	}
	$order['addtime_cn'] = date('Y-m-d H:i', $order['addtime']);
	$order['paytime_cn'] = date('Y-m-d H:i', $order['paytime']);
	$order['delivery_assign_time_cn'] = date('Y-m-d H:i', $order['delivery_assign_time']);
	$order['delivery_instore_time_cn'] = date('Y-m-d H:i', $order['delivery_instore_time']);
	$order['delivery_success_time_cn'] = date('Y-m-d H:i', $order['delivery_success_time']);

	$deliveryer = deliveryer_fetch($order['deliveryer_id']);
	$order['deliveryer'] = array(
		'title' => $deliveryer['deliveryer']['title'],
		'mobile' => $deliveryer['deliveryer']['mobile'],
		'age' => $deliveryer['deliveryer']['age'],
		'sex' => $deliveryer['deliveryer']['sex'],
		'location_x' => $deliveryer['deliveryer']['location_x'],
		'location_y' => $deliveryer['deliveryer']['location_y'],
	);

	$order['store2user_distance'] = $order['distance'] ? $order['distance'] : '未知';
	$order['store2deliveryer_distance'] = '未知';
	if(!empty($order['buy_location_x']) && !empty($order['buy_location_y'])) {
		if(!empty($deliveryer['location_x']) && !empty($deliveryer['location_y'])) {
			$order['store2deliveryer_distance'] = distanceBetween($order['buy_location_y'], $order['buy_location_x'], $deliveryer['location_y'], $deliveryer['location_x']);
			$order['store2deliveryer_distance'] = round($order['store2deliveryer_distance'] / 1000, 2);
		}
	}
	message(ierror(0, '', $order), '', 'ajax');
}

//抢单
if($op == 'collect') {
	$id = intval($_GPC['id']);
	$order = errander_order_fetch($id);
	if(empty($order)) {
		message(ierror(-1, '订单不存在或已删除'), '', 'ajax');
	}
	$status = errander_order_status_update($id, 'delivery_assgin', array('deliveryer_id' => $deliveryer['id']));
	if(is_error($status)) {
		message(ierror(-1, $status['message']), '', 'ajax');
	}
	message(ierror(0, '抢单成功'), '', 'ajax');
}

if($op == 'instore') {
	$id = intval($_GPC['id']);
	$order = errander_order_fetch($id);
	if(empty($order)) {
		message(ierror(-1, '订单不存在或已删除'), '', 'ajax');
	}
	$status = errander_order_status_update($id, 'delivery_instore', array('deliveryer_id' => $deliveryer['id']));
	if(is_error($status)) {
		message(ierror(-1, $status['message']), '', 'ajax');
	}
	message(ierror(0, '确认取货成功'), '', 'ajax');
}

if($op == 'success') {
	$id = intval($_GPC['id']);
	$status = errander_order_status_update($id, 'delivery_instore', array('deliveryer_id' => $deliveryer['id'], 'code' => trim($_GPC['code'])));
	if(is_error($status)) {
		message(ierror(-1, $status['message']), '', 'ajax');
	}
	message(ierror(0, '确认送达成功'), '', 'ajax');
}
