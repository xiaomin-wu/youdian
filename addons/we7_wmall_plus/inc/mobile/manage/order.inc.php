<?php
/**
 * 外送系统
 * @author 新睿社区
 * @QQ 10373458 * @url http://www.010xr.com/
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
$store = $_W['we7_wmall_plus']['store'];
$title = '订单管理';

if($op == 'list') {
	$condition = ' WHERE uniacid = :uniacid AND sid = :sid';
	$params[':uniacid'] = $_W['uniacid'];
	$params[':sid'] = $sid;

	$status = isset($_GPC['status']) ? intval($_GPC['status']) : 1;
	if($status > 0) {
		$condition .= ' AND status = :status';
		$params[':status'] = $status;
	}
	$orders = pdo_fetchall('SELECT * FROM ' . tablename('tiny_wmall_plus_order') . $condition . ' order by id desc limit 10', $params, 'id');
	$min = 0;
	if(!empty($orders)) {
		foreach($orders as &$da) {
			$da['pay_type_class'] = '';
			if($da['is_pay'] == 1) {
				$da['pay_type_class'] = 'have-pay';
				if($da['pay_type'] == 'delivery') {
					$da['pay_type_class'] = 'delivery-pay';
				}
			}
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
	$id = intval($_GPC['id']);
	$status = intval($_GPC['status']);
	$condition = ' WHERE uniacid = :uniacid AND sid = :sid and status = :status and id < :id';
	$params = array(
		':uniacid' => $_W['uniacid'],
		':sid' => $sid,
		':status' => $status,
		':id' => $id
	);
	$orders = pdo_fetchall('SELECT * FROM ' . tablename('tiny_wmall_plus_order') . $condition . ' order by id desc limit 10', $params, 'id');
	$min = 0;
	if(!empty($orders)) {
		$pay_types = order_pay_types();
		$order_status = order_status();
		foreach ($orders as &$row) {
			$row['goods'] = order_fetch_goods($row['id']);
			$row['addtime_cn'] = date('Y-m-d H:i:s', $row['addtime']);
			$row['status_color'] = $order_status[$row['status']]['color'];
			$row['status_cn'] = $order_status[$row['status']]['text'];
			$row['delivery_mode'] = $store['delivery_mode'];
			$row['pay_type_class'] = '';
			if($row['is_pay'] == 1) {
				$row['pay_type_class'] = 'have-pay';
				if($row['pay_type'] == 'delivery') {
					$row['pay_type_class'] = 'delivery-pay';
				}
			}
		}
		$min = min(array_keys($orders));
	}
	$orders = array_values($orders);
	$respon = array('error' => 0, 'message' => $orders, 'min' => $min);
	message($respon, '', 'ajax');
}

if($op == 'detail' || $op == 'consume') {
	$title = '订单详情';
	$id = intval($_GPC['id']);
	$order = order_fetch($id);
	if(empty($order)) {
		message('订单不存在或已删除', '', 'error');
	}
	$goods = order_fetch_goods($order['id']);
	$log = pdo_fetch('select * from ' . tablename('tiny_wmall_plus_order_status_log') . ' where uniacid = :uniacid and oid = :oid order by id desc', array(':uniacid' => $_W['uniacid'], ':oid' => $id));
	if($order['discount_fee'] > 0) {
		$activityed = order_fetch_discount($id);
	}
	$logs = order_fetch_status_log($id);
	if(!empty($logs)) {
		$maxid = max(array_keys($logs));
	}
	if($order['refund_status']) {
		$refund = order_refund_fetch($id);
		$refund_logs = order_fetch_refund_log($id);
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
	$type = trim($_GPC['type']);
	$result = order_status_update($id, $type);
	if(is_error($result)) {
		message(error(-1, "处理订单失败:{$result['message']}"), '', 'ajax');
	}
	message(error(0, ''), '', 'ajax');
}

if($op == 'cancel') {
	$id = $_GPC['id'];
	$result = order_status_update($id, 'cancel');
	if(is_error($result)) {
		message(error(-1, $result['message']), '', 'ajax');
	}
	if($result['message']['is_refund']) {
		message(error(0, '取消订单成功, 退款会在1-15个工作日打到客户账户'), '', 'ajax');
	} else {
		message(error(0, ''), '', 'ajax');
	}
}

if($op == 'deliveryer') {
	$id = $_GPC['id'];
	$deliveryer_id = intval($_GPC['deliveryer_id']);
	$result = order_assign_deliveryer($id, $deliveryer_id);
	if(is_error($result)) {
		message(error(-1, "ID为{$id}的订单分配配送员失败"), '', 'ajax');
	}
	message(error(0, ''), '', 'ajax');
}

if($op == 'consume_post') {
	$id = intval($_GPC['id']);
	$order = pdo_get('tiny_wmall_plus_order', array('uniacid' => $_W['uniacid'], 'id' => $id));
	if(empty($order)) {
		$this->imessage('订单不存在或已经删除', '', 'error');
	}
	$url = $this->createMobileUrl('mgorder', array('op' => 'detail', 'id' => $id));
	$result = order_status_update($id, 'end');
	if(is_error($result)) {
		$this->imessage("核销订单失败:{$result['message']}", $url, 'error');
	}
	$this->imessage('自提订单核销成功', $url, 'success');
}

if($op == 'reply') {
	$id = intval($_GPC['id']);
	$reply = trim($_GPC['reply']);
	$result = order_status_update($id, 'reply', array('reply' => $reply));
	message(error(0, ''), '', 'ajax');
}
