<?php
/**
 * 外送系统
 * @author 灯火阑珊
 * @QQ 2471240272
 * @url http://bbs.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$do = 'dyorder';
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'list';
$title = '订单管理';

$delivery_type = $_W['we7_wmall']['deliveryer']['type'];
$delivery_stores = implode(', ', $_W['we7_wmall']['deliveryer']['store']);
$deliveryer = $_W['we7_wmall']['deliveryer']['user'];

if($op == 'list') {
	$condition = ' WHERE uniacid = :uniacid';
	$params[':uniacid'] = $_W['uniacid'];
	$status = isset($_GPC['status']) ? intval($_GPC['status']) : 3;
	$condition .= ' and delivery_status = :status';
	$params[':status'] = $status;
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
	$orders = pdo_fetchall('SELECT id, addtime, status, username, mobile, address, delivery_status, delivery_type, delivery_time,sid, num, final_fee FROM ' . tablename('tiny_wmall_order') . $condition, $params, 'id');
	$min = 0;
	if(!empty($orders)) {
		$stores_id = array();
		foreach($orders as &$da) {
			if($da['delivery_type'] == 2) {
				if($dy_config['delivery_fee_type'] == 1) {
					$da['deliveryer_fee'] = $dy_config['delivery_fee'];
				} else {
					$da['deliveryer_fee'] = round($da['final_fee'] * $dy_config['delivery_fee'] / 100, 2);
				}
			}
			$stores_id[] = $da['sid'];
		}
		$stores_str = implode(',', array_unique($stores_id));
		$stores = pdo_fetchall('select id, title, address from ' . tablename('tiny_wmall_store') . " where uniacid = :uniacid and id in ({$stores_str})", array(':uniacid' => $_W['uniacid']), 'id');
		$min = min(array_keys($orders));
	}
	$delivery_status = order_delivery_status();
	include $this->template('delivery/order-list');
}

if($op == 'more') {
	$id = intval($_GPC['id']);
	$status = intval($_GPC['status']);
	$orders = pdo_fetchall('select * from ' . tablename('tiny_wmall_order') . ' where uniacid = :uniacid and delivery_status = :delivery_status and deliveryer_id = :deliveryer_id and id < :id order by id desc limit 15', array(':uniacid' => $_W['uniacid'], ':delivery_status' => $status, ':deliveryer_id' => $deliveryer['id'], ':id' => $id), 'id');
	$min = 0;
	if(!empty($orders)) {
		$delivery_status = order_delivery_status();
		foreach ($orders as &$row) {
			$row['addtime_cn'] = date('Y-m-d H:i:s', $row['addtime']);
			$row['status_color'] = $delivery_status[$row['delivery_status']]['color'];
			$row['status_cn'] = $delivery_status[$row['delivery_status']]['text'];
			$row['store_address'] = pdo_fetchcolumn('select address from ' . tablename('tiny_wmall_store') . ' where uniacid = :uniacid and id = :sid', array(':uniacid' => $_W['uniacid'], ':sid' => $row['sid']));
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
	$goods = order_fetch_goods($order['id']);
	if($order['discount_fee'] > 0) {
		$activityed = order_fetch_discount($id);
	}
	$log = pdo_fetch('select * from ' . tablename('tiny_wmall_order_status_log') . ' where uniacid = :uniacid and oid = :oid order by id desc', array(':uniacid' => $_W['uniacid'], ':oid' => $id));
	$store = store_fetch($order['sid'], array('id', 'title', 'address', 'telephone', 'logo', 'location_x', 'location_y'));
	$order_types = order_types();
	$pay_types = order_pay_types();
	$order_status = order_status();
	$deliveryer = deliveryer_fetch($order['deliveryer_id']);
	include $this->template('delivery/order-detail');
}

if($op == 'collect') {
	$id = intval($_GPC['id']);
	$order = pdo_get('tiny_wmall_order', array('uniacid' => $_W['uniacid'], 'id' => $id));
	if(empty($order)) {
		message(error(-1, '订单不存在或已经删除'), '', 'ajax');
	}
	if($order['deliveryer_id'] > 0) {
		message(error(-1, '来迟了, 该订单已被别人接单'), '', 'ajax');
	}
	pdo_update('tiny_wmall_order', array('status' => 4, 'delivery_status' => 4, 'deliveryer_id' => $deliveryer['id'], 'deliveryingtime' => TIMESTAMP), array('uniacid' => $_W['uniacid'], 'id' => $id));
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
	message(error(0, '抢单成功'), '', 'ajax');
}

if($op == 'success') {
	$id = intval($_GPC['id']);
	$order = pdo_get('tiny_wmall_order', array('uniacid' => $_W['uniacid'], 'id' => $id));
	if(empty($order)) {
		message(error(-1, '订单不存在或已经删除'), '', 'ajax');
	}
	if($order['delivery_id'] > 0 && $order['delivery_id'] != $deliveryer['id']) {
		message(error(-1, '该订单不是由您配送,不能变更状态'), '', 'ajax');
	}
	pdo_update('tiny_wmall_order', array('status' => 5, 'delivery_status' => 5, 'deliveryedtime' => TIMESTAMP), array('uniacid' => $_W['uniacid'], 'id' => $id));
	order_update_current_log($id, 5);
	$content = "配送员:{$deliveryer['title']}, 手机号:{$deliveryer['mobile']}";
	order_insert_status_log($id, $order['sid'], 'delivery_success', $content);
	order_status_notice($order['sid'], $id, 'end');
	message(error(0, '变更配送状态成功'), '', 'ajax');
}

if($op == 'consume') {
	$id = intval($_GPC['id']);
	$order = pdo_get('tiny_wmall_order', array('uniacid' => $_W['uniacid'], 'id' => $id));
	if(empty($order)) {
		message('订单不存在或已经删除', '', 'error');
	}
	if($order['deliveryer_id'] > 0 && $order['deliveryer_id'] != $deliveryer['id']) {
		message('该订单不是由您配送,不能变更状态', '', 'error');
	}
	if($order['status'] == 5) {
		$this->imessage('该订单已核销，请勿重复核销', $this->createMobileUrl('dyorder', array('op' => 'list')), 'error');
	}
	pdo_update('tiny_wmall_order', array('status' => 5, 'delivery_status' => 5, 'deliveryedtime' => TIMESTAMP), array('uniacid' => $_W['uniacid'], 'id' => $id));
	order_update_current_log($id, 5);
	$content = "配送员:{$deliveryer['title']}, 手机号:{$deliveryer['mobile']}";
	order_insert_status_log($id, $order['sid'], 'delivery_success', $content);
	order_status_notice($order['sid'], $id, 'end');
	$this->imessage('确认订单送达成功', $this->createMobileUrl('dyorder', array('op' => 'detail', 'id' => $id)), 'success');
}

if($op == 'notice') {
	$id = intval($_GPC['id']);
	$order = pdo_get('tiny_wmall_order', array('uniacid' => $_W['uniacid'], 'id' => $id));
	if(empty($order)) {
		message(error(-1, '订单不存在或已经删除'), '', 'ajax');
	}
	if($order['delivery_id'] > 0 && $order['delivery_id'] != $deliveryer['id']) {
		message(error(-1, '该订单不是由您配送,不能进行微信通知'), '', 'ajax');
	}
	$content = array('title' => $deliveryer['title'], 'mobile' => $deliveryer['mobile']);
	order_status_notice($order['sid'], $id, 'delivery_notice', $content);
	message(error(0, '通知成功'), '', 'ajax');
}


