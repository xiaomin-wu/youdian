<?php
/**
 * 外送系统
 * @author 新睿社区
 * @QQ 10373458 * @url http://www.010xr.com/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$do = 'order';
$this->checkAuth();
mload()->model('errander');
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'list';

$config = $_W['we7_wmall_plus']['config'];
if($op == 'list') {
	$title = "跑腿订单";
	$total_user = pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_plus_errander_order') . ' where uniacid = :uniacid', array(':uniacid' => $_W['uniacid']));
	$orders = pdo_fetchall('select a.*,b.title,b.thumb from ' . tablename('tiny_wmall_plus_errander_order') . ' as a left join ' . tablename('tiny_wmall_plus_errander_category') . ' as b on a.order_cid = b.id where a.uniacid = :uniacid and a.uid = :uid order by a.id desc limit 15', array(':uniacid' => $_W['uniacid'], ':uid' => $_W['member']['uid']), 'id');
	$min = 0;
	if(!empty($orders)) {
		$order_status = errander_order_status();
		$min = min(array_keys($orders));
		foreach($orders as &$row) {
			if($row['deliveryer_id'] > 0) {
				$row['deliveryer'] = pdo_get('tiny_wmall_plus_deliveryer', array('uniacid' => $_W['uniacid'], 'id' => $row['deliveryer_id']));
			}
		}
	} else {
		$others = pdo_fetchall('select a.*,b.title,b.thumb from ' . tablename('tiny_wmall_plus_errander_order') . ' as a left join ' . tablename('tiny_wmall_plus_errander_category') . ' as b on a.order_cid = b.id where a.uniacid = :uniacid order by a.id desc limit 5', array(':uniacid' => $_W['uniacid']), 'id');
	}
	include $this->template('errander-order-list');
}

if($op == 'more') {
	$id = intval($_GPC['id']);
	$orders = pdo_fetchall('select a.*,b.title,b.thumb from ' . tablename('tiny_wmall_plus_errander_order') . ' as a left join ' . tablename('tiny_wmall_plus_errander_category') . ' as b on a.order_cid = b.id where a.uniacid = :uniacid and a.uid = :uid and a.id < :id order by a.id desc limit 15', array(':uniacid' => $_W['uniacid'], ':uid' => $_W['member']['uid'], ':id' => $id), 'id');
	$min = 0;
	if(!empty($orders)) {
		$order_status = errander_order_status();
		foreach($orders as &$order) {
			$order['addtime_cn'] = date('Y-m-d H:i:s', $order['addtime']);
			$order['time_cn'] = date('H:i', $order['addtime']);
			$order['status_cn'] = $order_status[$order['status']]['text'];
			$order['thumb'] = tomedia($order['thumb']);
			$order['deliveryer'] = pdo_get('tiny_wmall_plus_deliveryer', array('uniacid' => $_W['uniacid'], 'id' => $order['deliveryer_id']));
		}
		$min = min(array_keys($orders));
	}
	$orders = array_values($orders);
	$respon = array('error' => 0, 'message' => $orders, 'min' => $min);
	message($respon, '', 'ajax');
}

if($op == 'cancel') {
	$id = intval($_GPC['id']);
	$status = errander_order_status_update($id, 'cancel');
	if(is_error($status)) {
		message($status, '', 'ajax');
	}
	message(error(0, ''), '', 'ajax');
}

if($op == 'end') {
	$id = intval($_GPC['id']);
	$order = order_fetch($id);
	if(empty($order)) {
		message(error(-1, '订单不存在或已删除'), '', 'ajax');
	}
	$status = errander_order_status_update($id, 'end');
	if(is_error($status)) {
		message($status, '', 'ajax');
	}
	message(error(0, ''), '', 'ajax');
}

if($op == 'detail') {
	$title = "订单详情";
	$id = intval($_GPC['id']);
	$order = errander_order_fetch($id);
	if(empty($order)) {
		message('订单不存在或已删除', '', 'error');
	}
	$log = pdo_fetch('select * from ' . tablename('tiny_wmall_plus_errander_order_status_log') . ' where uniacid = :uniacid and oid = :oid order by id desc', array(':uniacid' => $_W['uniacid'], ':oid' => $id));
	$logs = errander_order_fetch_status_log($id);
	if(!empty($logs)) {
		$maxid = max(array_keys($logs));
	}
	if($order['refund_status'] > 0) {
		$refund_logs = errander_order_fetch_refund_status_log($id);
		if(!empty($refund_logs)) {
			$refundmaxid = max(array_keys($refund_logs));
		}
	}
	$deliveryer = pdo_get('tiny_wmall_plus_deliveryer', array('uniacid' => $_W['uniacid'], 'id' => $order['deliveryer_id']));
	$order_types = errander_types();
	$pay_types = order_pay_types();
	$order_status = errander_order_status();
	include $this->template('errander-order-detail');
}

