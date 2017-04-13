<?php
/**
 * 外送系统
 * @author 新睿社区
 * @QQ 10373458 * @url http://www.010xr.com/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$do = 'order';
$this->icheckAuth();
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'list';

if($op == 'list') {
	$condition = ' where a.uniacid = :uniacid and a.uid = :uid';
	$params = array(':uniacid' => $_W['uniacid'], ':uid' => $_W['member']['uid']);
	$id = intval($_GPC['id']);
	if($id > 0) {
		$condition .= ' and a.id < :id';
		$params[':id'] = $id;
	}
	$orders = pdo_fetchall('select a.id as aid, a.*, b.title, b.logo, b.pay_time_limit from ' . tablename('tiny_wmall_plus_order') . ' as a left join ' . tablename('tiny_wmall_plus_store') . " as b on a.sid = b.id {$condition} order by a.id desc limit 15", $params, 'aid');
	$min = 0;
	if(!empty($orders)) {
		$order_status = order_status();
		foreach($orders as &$order) {
			$order['goods'] = pdo_get('tiny_wmall_plus_order_stat', array('oid' => $order['aid']), array('goods_title'));
			$order['addtime_cn'] = date('Y-m-d H:i:s', $order['addtime']);
			$order['time_cn'] = date('H:i', $order['addtime']);
			$order['status_cn'] = $order_status[$order['status']]['text'];
			$order['logo_cn'] = tomedia($order['logo']);
		}
		$min = min(array_keys($orders));
	}
	$orders = array_values($orders);
	$data = array(
		'order' => $orders,
		'min_id' => $min,
	);
	message(ierror(0, '', $data), '', 'ajax');
}

if($op == 'cancel') {
	$id = intval($_GPC['id']);
	$order = order_fetch($id);
	if(empty($order)) {
		message(ierror(-1, '订单不存在或已删除'), '', 'ajax');
	}
	if($order['status'] != 1) {
		message(ierror(-1, '商户已接单,如需取消订单请联系商户处理'), '', 'ajax');
	}
	if(!$order['is_pay']) {
		pdo_update('tiny_wmall_plus_order', array('status' => '6'), array('uniacid' => $_W['uniacid'], 'id' => $id));
		order_insert_status_log($id, $order['sid'], 'cancel', '您取消了订单');
	} else {
		message(ierror(-1, '该订单已支付,如需取消订单请联系商户处理'), '', 'ajax');
	}
	message(ierror(0, '订单取消成功'), '', 'ajax');
}

if($op == 'end') {
	$id = intval($_GPC['id']);
	$order = order_fetch($id);
	if(empty($order)) {
		message(ierror(-1, '订单不存在或已删除'), '', 'ajax');
	}
	pdo_update('tiny_wmall_plus_order', array('status' => 5, 'delivery_status' => 5, 'deliveryedtime' => TIMESTAMP), array('uniacid' => $_W['uniacid'], 'id' => $id));
	order_update_current_log($id, 5);
	order_insert_status_log($id, $order['sid'], 'end');
	order_status_notice($order['sid'], $order['id'], 'end');
	message(ierror(0, '确认送达成功'), '', 'ajax');
}

if($op == 'detail') {
	$id = intval($_GPC['id']);
	$order = order_fetch($id);
	if(empty($order)) {
		message(ierror(-1, '订单不存在或已删除'), '', 'ajax');
	}
	$store = store_fetch($order['sid'], array('title', 'telephone', 'pack_price', 'logo', 'delivery_price', 'address', 'location_x', 'location_y'));
	$store['logo'] = tomedia($store['logo']);
	$order['store'] = $store;

	$goods = order_fetch_goods($order['id']);
	$order['goods'] = $goods;

	$log = pdo_fetch('select * from ' . tablename('tiny_wmall_plus_order_status_log') . ' where uniacid = :uniacid and oid = :oid order by id desc', array(':uniacid' => $_W['uniacid'], ':oid' => $id));
	$order['log'] = $log;

	$order['activityed'] = array();
	if($order['discount_fee'] > 0) {
		$activityed = order_fetch_discount($id);
		$order['activityed'] = $activityed;
	}

	$logs = order_fetch_status_log($id);
	$order['logs'] = $logs;

	$order['refund'] = array();
	if($order['is_refund']) {
		$refund = order_current_fetch($id);
		$refund_logs = order_fetch_refund_status_log($id);
		$refund['logs'] = $refund_logs;
		$order['refund'] = $refund;
	}
	message(ierror(0, '', $order), '', 'ajax');
}

if($op == 'remind') {
	$id = intval($_GPC['id']);
	$order = order_fetch($id);
	if(empty($order)) {
		message(ierror(-1, '订单不存在或已删除'), '', 'ajax');
	}
	$log = pdo_fetch('select * from ' . tablename('tiny_wmall_plus_order_status_log') . ' where uniacid = :uniacid and oid = :oid and status = 8 order by id desc',  array(':uniacid' => $_W['uniacid'], ':oid' => $id));
	$store = store_fetch($order['sid'], array('remind_time_limit'));
	$remind_time_limit = intval($store['remind_time_limit']) ? intval($store['remind_time_limit']) : 10;
	if($log['addtime'] >= (time() - $remind_time_limit * 60)) {
		message(ierror(-1, "距离上次催单不超过{$remind_time_limit}分钟,不能催单"), '', 'ajax');
	}
	order_insert_status_log($id, $order['sid'], 'remind');
	order_clerk_notice($order['sid'], $id, 'remind');
	pdo_update('tiny_wmall_plus_order', array('is_remind' => '1'), array('uniacid' => $_W['uniacid'], 'id' => $id));
	message(ierror(0, '催单成功'), '', 'ajax');
}

if($op == 'comment') {
	mload()->func('tpl.app');
	$id = intval($_GPC['id']);
	$order = order_fetch($id);
	if(empty($order)) {
		message(ierror(-1, '订单不存在或已删除'), '', 'ajax');
	}
	if($order['is_comment'] == 1) {
		message(ierror(-1, '订单已评价'), '', 'ajax');
	}

	$store = store_fetch($order['sid'], array('comment_status'));
	$insert = array(
		'uniacid' => $_W['uniacid'],
		'uid' => $_W['member']['uid'],
		'username' => $order['username'],
		'avatar' => $_W['fans']['avatar'],
		'mobile' => $order['mobile'],
		'oid' => $id,
		'sid' => $order['sid'],
		'goods_quality' => intval($_GPC['goods_quality']) ? intval($_GPC['goods_quality']) : 5,
		'delivery_service' => intval($_GPC['delivery_service']) ? intval($_GPC['delivery_service']) : 5,
		'note' => trim($_GPC['note']),
		'status' => $store['comment_status'],
		'data' => '',
		'addtime' => TIMESTAMP,
	);
	if(!empty($_GPC['thumbs'])) {
		$thumbs = array();
		foreach($_GPC['thumbs'] as $thumb) {
			if(empty($thumb)) continue;
			$thumbs[] = $thumb;
		}
		$insert['thumbs'] = iserializer($thumbs);
	}
	$goods = order_fetch_goods($order['id']);
	foreach($goods as $good) {
		$value = intval($_GPC['goods'][$good['id']]);
		if(!$value) {
			continue;
		}
		$update = ' set comment_total = comment_total + 1';
		if($value == 1) {
			$update .= ' , comment_good = comment_good + 1';
			$insert['data']['good'][] = $good['goods_title'];
		} else {
			$insert['data']['bad'][] = $good['goods_title'];
		}
		pdo_query('update ' . tablename('tiny_wmall_plus_goods') . $update . ' where id = :id', array(':id' => $good['goods_id']));
	}
	$insert['score'] = $insert['goods_quality'] + $insert['delivery_service'];
	$insert['data'] = iserializer($insert['data']);
	pdo_insert('tiny_wmall_plus_order_comment', $insert);
	pdo_update('tiny_wmall_plus_order', array('is_comment' => 1), array('id' => $id));
	if($store['comment_status'] == 1) {
		store_comment_stat($order['sid']);
	}
	message(ierror(0, ''), '', 'ajax');
}

