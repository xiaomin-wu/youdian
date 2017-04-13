<?php
/**
 * 外送系统
 * @author 灯火阑珊
 * @QQ 2471240272
 * @url http://bbs.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'list';

$delivery_type = $_W['we7_wmall']['deliveryer']['type'];
$delivery_stores = implode(', ', $_W['we7_wmall']['deliveryer']['store']);
$deliveryer = $_W['we7_wmall']['deliveryer']['user'];

if($op == 'list') {
	$condition = ' WHERE uniacid = :uniacid';
	$params[':uniacid'] = $_W['uniacid'];
	$status = isset($_GPC['status']) ? intval($_GPC['status']) : 3;
	$condition .= ' and delivery_status = :status';
	$params[':status'] = $status;

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

	if($status == 3) {
		$condition .= ' and delivery_type = 2';
	} else {
		$condition .= ' and deliveryer_id = :deliveryer_id and delivery_type = 2';
		$params[':deliveryer_id'] = $deliveryer['id'];
	}
	$min_id = intval(pdo_fetchcolumn('SELECT min(id) as min_id FROM ' . tablename('tiny_wmall_order') . $condition , $params));
	$orders = pdo_fetchall('SELECT id, addtime, status, username, mobile, address, location_x, location_y, delivery_status, delivery_type, delivery_time,sid, num, final_fee FROM ' . tablename('tiny_wmall_order') . $condition . ' order by id desc limit 15', $params, 'id');
	$min = $max = 0;
	if(!empty($orders)) {
		$stores_id = array();
		foreach($orders as &$da) {
			$stores_id[] = $da['sid'];
		}
		$stores_str = implode(',', array_unique($stores_id));
		$stores = pdo_fetchall('select id, title, address, location_x, location_y, telephone from ' . tablename('tiny_wmall_store') . " where uniacid = :uniacid and id in ({$stores_str})", array(':uniacid' => $_W['uniacid']), 'id');

		foreach($orders as &$da) {
			if($da['delivery_type'] == 2) {
				if($dy_config['delivery_fee_type'] == 1) {
					$da['deliveryer_fee'] = $dy_config['delivery_fee'];
				} else {
					$da['deliveryer_fee'] = round($da['final_fee'] * $dy_config['delivery_fee'] / 100, 2);
				}
			}
			$da['addtime_cn'] = date('m-d H:i', $da['addtime']);
			$da['store'] = array(
				'title' => $stores[$da['sid']]['title'],
				'telephone' => $stores[$da['sid']]['telephone'],
				'address' => $stores[$da['sid']]['address'],
				'location_x' => $stores[$da['sid']]['location_x'],
				'location_y' => $stores[$da['sid']]['location_y']
			);
			$da['store2user_distance'] = $da['store2deliveryer_distance'] = '未知';
			if(!empty($da['location_x']) && !empty($da['location_y'])) {
				if(!empty($da['store']['location_x']) && !empty($da['store']['location_y'])) {
					$da['store2user_distance'] = distanceBetween($da['location_y'], $da['location_x'], $da['store']['location_y'], $da['store']['location_x']);
					$da['store2user_distance'] = round($da['store2user_distance'] / 1000, 2);
				}
				if(!empty($deliveryer['location_x']) && !empty($deliveryer['location_y'])) {
					$da['store2deliveryer_distance'] = distanceBetween($da['store']['location_y'], $da['store']['location_x'], $deliveryer['location_y'], $deliveryer['location_x']);
					$da['store2deliveryer_distance'] = round($da['store2deliveryer_distance'] / 1000, 2);
				}
			}
			$stores_id[] = $da['sid'];
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
	$order = order_fetch($id);
	if(empty($order)) {
		message(ierror(-1, '订单不存在或已删除'), '', 'ajax');
	}
	$order['addtime_cn'] = date('Y-m-d H:i', $order['addtime']);
	$order['paytime_cn'] = date('Y-m-d H:i', $order['paytime']);
	$order['deliveryingtime_cn'] = date('Y-m-d H:i', $order['deliveryingtime']);
	$order['deliveryinstoretime_cn'] = date('Y-m-d H:i', $order['deliveryinstoretime']);
	$order['deliveryedtime_cn'] = date('Y-m-d H:i', $order['deliveryedtime']);

	$store = store_fetch($order['sid'], array('id', 'title', 'address', 'telephone', 'logo', 'location_x', 'location_y'));
	$order['store'] = array(
		'title' => $store['title'],
		'address' => $store['address'],
		'telephone' => $store['telephone'],
		'location_x' => $store['location_x'],
		'location_y' => $store['location_y'],
	);

	$deliveryer = deliveryer_fetch($order['deliveryer_id']);
	$order['deliveryer'] = array(
		'title' => $deliveryer['deliveryer']['title'],
		'mobile' => $deliveryer['deliveryer']['mobile'],
		'age' => $deliveryer['deliveryer']['age'],
		'sex' => $deliveryer['deliveryer']['sex'],
		'location_x' => $deliveryer['deliveryer']['location_x'],
		'location_y' => $deliveryer['deliveryer']['location_y'],
	);

	$order['store2user_distance'] = $order['store2deliveryer_distance'] = '未知';
	if(!empty($order['location_x']) && !empty($order['location_y'])) {
		if(!empty($order['store']['location_x']) && !empty($order['store']['location_y'])) {
			$order['store2user_distance'] = distanceBetween($order['location_y'], $order['location_x'], $order['store']['location_y'], $order['store']['location_x']);
			$order['store2user_distance'] = round($order['store2user_distance'] / 1000, 2);
		}
		if(!empty($order['deliveryer']['location_x']) && !empty($order['deliveryer']['location_y'])) {
			$order['store2deliveryer_distance'] = distanceBetween($order['store']['location_y'], $order['store']['location_x'], $order['deliveryer']['location_y'], $order['deliveryer']['location_x']);
			$order['store2deliveryer_distance'] = round($order['store2deliveryer_distance'] / 1000, 2);
		}
	}

	$goods = order_fetch_goods($order['id']);
	$order['goods'] = $goods;

	if($order['discount_fee'] > 0) {
		$activityed = order_fetch_discount($id);
	}
	$order['activityed'] = $activityed;

	$order_types = order_types();
	$pay_types = order_pay_types();
	$order_status = order_status();
	message(ierror(0, '', $order), '', 'ajax');
}

//抢单
if($op == 'collect') {
	$id = intval($_GPC['id']);
	$order = pdo_get('tiny_wmall_order', array('uniacid' => $_W['uniacid'], 'id' => $id));
	if(empty($order)) {
		message(ierror(-1, '订单不存在或已经删除'), '', 'ajax');
	}
	if($order['deliveryer_id'] > 0) {
		message(ierror(-1, '来迟了, 该订单已被别人接单'), '', 'ajax');
	}
	pdo_update('tiny_wmall_order', array('status' => 4, 'delivery_status' => 7, 'deliveryer_id' => $deliveryer['id'], 'deliveryingtime' => TIMESTAMP), array('uniacid' => $_W['uniacid'], 'id' => $id));
	$note = array(
		"配送　员: {$deliveryer['title']}",
		"手机　号: {$deliveryer['mobile']}",
	);
	//如果是平台单, 计算配送费
	if($order['delivery_type'] == 2) {
		if($order['vip_free_delivery_fee'] == 1) {
			$order['store_deliveryer_fee'] = 0;
		} else {
			$order['store_deliveryer_fee'] = $order['delivery_fee'];
		}

		if($dy_config['delivery_fee_type'] == 1) {
			$order['deliveryer_fee'] = $dy_config['delivery_fee'];
		} else {
			$order['deliveryer_fee'] = round($order['final_fee'] * $dy_config['delivery_fee'] / 100, 2);
		}
		pdo_update('tiny_wmall_order_current_log', array('deliveryer_fee' => $order['deliveryer_fee'], 'deliveryer_id' => $deliveryer['id'], 'store_deliveryer_fee' => $order['store_deliveryer_fee'], 'vip_free_delivery_fee' => $order['vip_free_delivery_fee'],  'order_status' => 4), array('uniacid' => $_W['uniacid'], 'orderid' => $order['id']));
		$note[] = "配送　费: {$order['deliveryer_fee']}元";
	}
	$content = "配送员:{$deliveryer['title']}, 手机号:{$deliveryer['mobile']}";
	order_insert_status_log($id, $order['sid'], 'delivery_ing', $content);
	order_status_notice($order['sid'], $id, 'delivery_ing', "配送　员：{$deliveryer['title']}\n手机　号：{$deliveryer['mobile']}");
	order_clerk_notice($order['sid'], $id, 'collect', $note);
	message(ierror(0, '抢单成功'), '', 'ajax');
}

//到店(这个没有客户微信通知)
if($op == 'instore') {
	$id = intval($_GPC['id']);
	$order = pdo_get('tiny_wmall_order', array('uniacid' => $_W['uniacid'], 'id' => $id));
	if(empty($order)) {
		message(ierror(-1, '订单不存在或已经删除'), '', 'ajax');
	}
	if($order['delivery_id'] > 0 && $order['delivery_id'] != $deliveryer['id']) {
		message(ierror(-1, '该订单不是由您配送,不能变更状态'), '', 'ajax');
	}
	pdo_update('tiny_wmall_order', array('status' => 4, 'delivery_status' => 4, 'deliveryinstoretime' => TIMESTAMP), array('uniacid' => $_W['uniacid'], 'id' => $id));
	$content = "配送员:{$deliveryer['title']}, 手机号:{$deliveryer['mobile']}";
	order_insert_status_log($id, $order['sid'], 'delivery_instore', $content);
	message(ierror(0, '确认到店成功'), '', 'ajax');
}

if($op == 'success') {
	$id = intval($_GPC['id']);
	$order = pdo_get('tiny_wmall_order', array('uniacid' => $_W['uniacid'], 'id' => $id));
	if(empty($order)) {
		message(ierror(-1, '订单不存在或已经删除'), '', 'ajax');
	}
	if($order['delivery_id'] > 0 && $order['delivery_id'] != $deliveryer['id']) {
		message(ierror(-1, '该订单不是由您配送,不能变更状态'), '', 'ajax');
	}
	pdo_update('tiny_wmall_order', array('status' => 5, 'delivery_status' => 5, 'deliveryedtime' => TIMESTAMP), array('uniacid' => $_W['uniacid'], 'id' => $id));
	order_update_current_log($id, 5);
	$content = "配送员:{$deliveryer['title']}, 手机号:{$deliveryer['mobile']}";
	order_insert_status_log($id, $order['sid'], 'delivery_success', $content);
	order_status_notice($order['sid'], $id, 'end');
	message(ierror(0, '确认送达成功'), '', 'ajax');
}
