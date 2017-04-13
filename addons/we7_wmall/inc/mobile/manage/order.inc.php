<?php
/**
 * 外送系统
 * @author 灯火阑珊
 * @QQ 2471240272
 * @url http://bbs.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$do = 'mgorder';
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'list';
mload()->model('manage');
mload()->model('order');
mload()->model('deliveryer');
checkstore();
$sid = intval($_GPC['__mg_sid']);
$store = $_W['we7_wmall']['store'];
$account = $store['account'];
$title = '订单管理';

if($op == 'list') {
	$condition = ' WHERE uniacid = :aid AND sid = :sid';
	$params[':aid'] = $_W['uniacid'];
	$params[':sid'] = $sid;

	$status = isset($_GPC['status']) ? intval($_GPC['status']) : 1;
	if($status > 0) {
		$condition .= ' AND status = :stu';
		$params[':stu'] = $status;
	}
	$orders = pdo_fetchall('SELECT * FROM ' . tablename('tiny_wmall_order') . $condition . ' order by id desc limit 5', $params, 'id');
	$min = 0;
	if(!empty($orders)) {
		foreach($orders as &$da) {
			$da['goods'] = order_fetch_goods($da['id']);
		}
		$min = min(array_keys($orders));
	}
	$order_status = order_status();
	$pay_types = order_pay_types();
	$deliveryers = deliveryer_fetchall($sid);
	include $this->template('manage/order-list');
}

if($op == 'more') {
	$pay_types = order_pay_types();
	$order_status = order_status();
	$id = intval($_GPC['id']);
	$orders = pdo_fetchall('select * from ' . tablename('tiny_wmall_order') . ' where uniacid = :uniacid and sid = :sid and id < :id order by id desc limit 5', array(':uniacid' => $_W['uniacid'], ':sid' => $sid, ':id' => $id), 'id');
	$min = 0;
	if(!empty($orders)) {
		foreach ($orders as &$row) {
			$row['goods'] = order_fetch_goods($row['id']);
			$row['addtime_cn'] = date('Y-m-d H:i:s', $row['addtime']);
			$row['status_color'] = $order_status[$row['status']]['color'];
			$row['status_cn'] = $order_status[$row['status']]['text'];
			$row['delivery_type'] = $account['delivery_type'];
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
	$log = pdo_fetch('select * from ' . tablename('tiny_wmall_order_status_log') . ' where uniacid = :uniacid and oid = :oid order by id desc', array(':uniacid' => $_W['uniacid'], ':oid' => $id));
	if($order['discount_fee'] > 0) {
		$activityed = order_fetch_discount($id);
	}
	$logs = order_fetch_status_log($id);
	if(!empty($logs)) {
		$maxid = max(array_keys($logs));
	}
	if($order['is_refund']) {
		$refund = order_current_fetch($id);
		$refund_logs = order_fetch_refund_status_log($id);
		if(!empty($refund_logs)) {
			$refundmaxid = max(array_keys($refund_logs));
		}
	}
	$order_types = order_types();
	$pay_types = order_pay_types();
	$order_status = order_status();
	$deliveryers = deliveryer_fetchall($sid);
	include $this->template('manage/order-detail');
}

if($op == 'print') {
	$id = intval($_GPC['id']);
	$status = order_print($id, true);
	if(is_error($status)) {
		message($status, '', 'ajax');
	}
	message(error(0, ''), '', 'ajax');
}

if($op == 'status') {
	$id = $_GPC['id'];
	$order = pdo_get('tiny_wmall_order', array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'id' => $id));
	if(empty($order)) {
		message(error(-1, '订单不存在或已删除'), '', 'ajax');
	}
	if($order['status'] >= 5) {
		message(error(-1, '订单已关闭'), '', 'ajax');
	}
	$status = intval($_GPC['status']);
	if($status == 6) {
		message(error(-1, '非法访问'), '', 'ajax');
	}
	$type = trim($_GPC['type']);
	if($status == 7) {
		pdo_update('tiny_wmall_order', array('pay_type' => 'cash', 'is_pay' => 1), array('uniacid' => $_W['uniacid'], 'id' => $id, 'is_pay' => 0));
		order_update_current_pay_type($id, 'cash', '');
	} else {
		if($status == '3' && $order['deliveryer_id'] > 0) {
			message(error(-1, "该订单已被配送员接单,不能重复通知配置员"), '', 'ajax');
		}
		order_update_current_log($id, $status);
		$update = array(
			'status' => $status,
			'delivery_status' => $status
		);
		if($status == 4) {
			$update['deliveryingtime'] = TIMESTAMP;
		}
		pdo_update('tiny_wmall_order', $update, array('uniacid' => $_W['uniacid'], 'id' => $id));
		//如果是自提订单, 将订单状态设置为4
		pdo_update('tiny_wmall_order', array('status' => 4), array('uniacid' => $_W['uniacid'], 'id' => $id, 'order_type' => 2, 'status' => 2));
	}
	if($status > 2) {
		pdo_update('tiny_wmall_order_stat', array('status' => 1), array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'oid' => $id));
	}
	order_insert_status_log($id, $sid, $type);
	order_status_notice($sid, $id, $type);
	if($status == '3') {
		pdo_update('tiny_wmall_order', array('delivery_type' => $account['delivery_type']), array('uniacid' => $_W['uniacid'], 'id' => $id));
		pdo_update('tiny_wmall_order_current_log', array('delivery_type' => $account['delivery_type']), array('uniacid' => $_W['uniacid'], 'orderid' => $id));
		order_deliveryer_notice($sid, $id, $type);
	}
	message(error(0, ''), '', 'ajax');
}

if($op == 'cancel') {
	$id = $_GPC['id'];
	$order = pdo_get('tiny_wmall_order', array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'id' => $id));
	if(empty($order)) {
		message(error(-1, '订单不存在或已删除'), '', 'ajax');
	}
	if($order['status'] >= 5) {
		message(error(-1, '订单已关闭'), '', 'ajax');
	}

	if(!$order['is_pay'] || ($order['is_pay'] == 1 && $order['pay_type'] == 'delivery' || $order['pay_type'] == 'cash')) {
		pdo_update('tiny_wmall_order', array('status' => 6, 'delivery_status' => 6), array('uniacid' => $_W['uniacid'], 'id' => $id));
		order_update_current_log($order['id'], 6);
		order_insert_status_log($id, $order['sid'], 'cancel');
		order_status_notice($order['sid'], $id, 'cancel');
		message(error(-1, '取消订单成功'), '', 'ajax');
	} else {
		$refund = order_build_payrefund($id);
		if(is_error($refund)) {
			message(error(-1, $refund['message']), '', 'ajax');
		}
		$update = array(
			'status' => 6,
			'delivery_status' => 6,
			'is_refund' => 1,
		);
		pdo_update('tiny_wmall_order', $update, array('uniacid' => $_W['uniacid'], 'id' => $id));
		order_update_current_log($order['id'], 6);
		order_insert_status_log($id, $order['sid'], 'cancel');
		$note = array(
			"退款金额: {$order['final_fee']}",
			"已付款项会在1-15工作日内返回您的账号",
		);
		order_status_notice($order['sid'], $id, 'cancel', $note);
		order_refund_notice($order['sid'], $id, 'apply');
		message(error(-1, '取消订单成功, 退款会在1-15个工作日打到客户账户'), '', 'ajax');
	}
	message(error(0, ''), '', 'ajax');
}

if($op == 'deliveryer') {
	if($account['delivery_type'] == 2) {
		message(error(-1, '当前配送模式为平台配送模式, 不能指定配送员'), '', 'ajax');
	}
	$deliveryer_id = intval($_GPC['deliveryer_id']);
	$deliveryer = pdo_get('tiny_wmall_deliveryer', array('uniacid' => $_W['uniacid'], 'id' => $deliveryer_id));
	if(empty($deliveryer)) {
		message(error(-1, '没有找到对应的配送员'), '', 'ajax');
	}
	$is_store_delivery = pdo_get('tiny_wmall_store_deliveryer', array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'deliveryer_id' => $deliveryer_id));
	if(empty($is_store_delivery)) {
		message(error(-1, '您的门店没有使用该配送员的权限'), '', 'ajax');
	}
	$id = intval($_GPC['id']);
	pdo_update('tiny_wmall_order', array('deliveryer_id' => $deliveryer_id, 'delivery_type' => 1, 'status' => '4', 'delivery_status' => '4', 'deliveryingtime' => TIMESTAMP), array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'id' => $id));
	pdo_update('tiny_wmall_order_current_log', array('delivery_type' => 1), array('uniacid' => $_W['uniacid'], 'orderid' => $id));
	$content = "配送员:{$deliveryer['title']}, 手机号:{$deliveryer['mobile']}";
	order_insert_status_log($id, $sid, 'delivery_ing', $content);
	order_status_notice($sid, $id, 'delivery_ing', "配送　员：{$deliveryer['title']}\n手机　号：{$deliveryer['mobile']}");
	order_deliveryer_notice($sid, $id, 'new_delivery', $deliveryer_id);
	message(error(0, ''), '', 'ajax');
}

if($op == 'consume') {
	$id = intval($_GPC['id']);
	$order = pdo_get('tiny_wmall_order', array('uniacid' => $_W['uniacid'], 'id' => $id));
	if(empty($order)) {
		message('订单不存在或已经删除', '', 'error');
	}
	$code = trim($_GPC['code']);
	if($order['code'] != $code) {
		message('收货码错误', '', 'error');
	}
	if($order['status'] == 5) {
		$this->imessage('该订单已核销，请勿重复核销', $this->createMobileUrl('mgorder', array('op' => 'list')), 'error');
	}
	pdo_update('tiny_wmall_order', array('status' => 5, 'delivery_status' => 5, 'deliveryedtime' => TIMESTAMP), array('uniacid' => $_W['uniacid'], 'id' => $id));
	order_update_current_log($id, 5);
	$this->imessage('自提订单核销成功', $this->createMobileUrl('mgorder', array('op' => 'detail', 'id' => $id)), 'success');
}

if($op == 'reply') {
	if(!$_W['isajax']) {
		return false;
	}
	$id = intval($_GPC['id']);
	$order = pdo_get('tiny_wmall_order', array('uniacid' => $_W['uniacid'], 'id' => $id));
	if(empty($order)) {
		message(error(-1, '订单不存在或已经删除'), '', 'ajax');
	}
	pdo_update('tiny_wmall_order', array('is_remind' => 0), array('uniacid' => $_W['uniacid'], 'id' => $id));
	$reply = trim($_GPC['reply']);
	order_insert_status_log($id, $order['sid'], 'remind_reply', $reply);
	order_status_notice($order['sid'], $id, 'reply_remind', "回复内容：" . $reply);
	message(error(0, ''), '', 'ajax');
}
