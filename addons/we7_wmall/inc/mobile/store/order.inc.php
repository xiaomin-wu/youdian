<?php
/**
 * 外送系统
 * @author 灯火阑珊
 * @QQ 2471240272
 * @url http://bbs.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$do = 'order';
mload()->model('store');
$this->checkAuth();
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'list';

if($op == 'list') {
	$title = "{$_W['we7_wmall']['config']['title']}-订单列表";
	$orders = pdo_fetchall('select a.id as aid, a.*, b.title, b.logo, b.delivery_mode, b.pay_time_limit from ' . tablename('tiny_wmall_order') . ' as a left join ' . tablename('tiny_wmall_store') . ' as b on a.sid = b.id where a.uniacid = :uniacid and a.uid = :uid order by a.id desc limit 15', array(':uniacid' => $_W['uniacid'], ':uid' => $_W['member']['uid']), 'aid');
	$min = 0;
	if(!empty($orders)) {
		$order_status = order_status();
		foreach($orders as &$da) {
			$da['goods'] = pdo_get('tiny_wmall_order_stat', array('oid' => $da['id']));
		}
		$min = min(array_keys($orders));
	}
	include $this->template('order-list');
}

if($op == 'more') {
	$id = intval($_GPC['id']);
	$orders = pdo_fetchall('select a.id as aid, a.*, b.title, b.logo, b,delivery_mode, b.pay_time_limit from ' . tablename('tiny_wmall_order') . ' as a left join ' . tablename('tiny_wmall_store') . ' as b on a.sid = b.id where a.uniacid = :uniacid and a.uid = :uid and a.id < :id order by a.id desc limit 15', array(':uniacid' => $_W['uniacid'], ':uid' => $_W['member']['uid'], ':id' => $id), 'aid');
	$min = 0;
	if(!empty($orders)) {
		$order_status = order_status();
		foreach($orders as &$order) {
			$order['goods'] = pdo_get('tiny_wmall_order_stat', array('oid' => $order['aid']), array('goods_title'));
			$order['addtime_cn'] = date('Y-m-d H:i:s', $order['addtime']);
			$order['time_cn'] = date('H:i', $order['addtime']);
			$order['status_cn'] = $order_status[$order['status']]['text'];
			$order['logo_cn'] = tomedia($order['logo']);
		}
		$min = min(array_keys($orders));
	}
	$orders = array_values($orders);
	$respon = array('error' => 0, 'message' => $orders, 'min' => $min);
	message($respon, '', 'ajax');
}

if($op == 'cancel') {
	$id = intval($_GPC['id']);
	$order = order_fetch($id);
	if(empty($order)) {
		message(error(-1, '订单不存在或已删除'), '', 'ajax');
	}
	if($order['status'] != 1) {
		message(error(-1, '商户已接单,如需取消订单请联系商户处理'), '', 'ajax');
	}
	if(!$order['is_pay']) {
		pdo_update('tiny_wmall_order', array('status' => '6'), array('uniacid' => $_W['uniacid'], 'id' => $id));
		order_insert_status_log($id, $order['sid'], 'cancel', '您取消了订单');
	} else {
		message(error(-1, '该订单已支付,如需取消订单请联系商户处理'), '', 'ajax');
	}
	message(error(0, ''), '', 'ajax');
}

if($op == 'end') {
	$id = intval($_GPC['id']);
	$order = order_fetch($id);
	if(empty($order)) {
		message(error(-1, '订单不存在或已删除'), '', 'ajax');
	}
	pdo_update('tiny_wmall_order', array('status' => 5, 'delivery_status' => 5, 'deliveryedtime' => TIMESTAMP), array('uniacid' => $_W['uniacid'], 'id' => $id));
	order_update_current_log($id, 5);
	order_insert_status_log($id, $order['sid'], 'end');
	order_status_notice($order['sid'], $order['id'], 'end');
	message(error(0, ''), '', 'ajax');
}

if($op == 'detail') {
	$title = "{$_W['we7_wmall']['config']['title']}-订单详情";
	$id = intval($_GPC['id']);
	$order = order_fetch($id);
	if(empty($order)) {
		message('订单不存在或已删除', '', 'error');
	}
	$store = store_fetch($order['sid'], array('title', 'telephone', 'pack_price', 'logo', 'delivery_price', 'address', 'location_x', 'location_y'));
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
	include $this->template('order-detail');
}

if($op == 'remind') {
	$id = intval($_GPC['id']);
	$order = order_fetch($id);
	if(empty($order)) {
		message(error(-1, '订单不存在或已删除'), '', 'ajax');
	}
	$log = pdo_fetch('select * from ' . tablename('tiny_wmall_order_status_log') . ' where uniacid = :uniacid and oid = :oid and status = 8 order by id desc',  array(':uniacid' => $_W['uniacid'], ':oid' => $id));
	$store = store_fetch($order['sid'], array('remind_time_limit'));
	$remind_time_limit = intval($store['remind_time_limit']) ? intval($store['remind_time_limit']) : 10;
	if($log['addtime'] >= (time() - $remind_time_limit * 60)) {
		message(error(-1, "距离上次催单不超过{$remind_time_limit}分钟,不能催单"), '', 'ajax');
	}
	order_insert_status_log($id, $order['sid'], 'remind');
	order_clerk_notice($order['sid'], $id, 'remind');
	pdo_update('tiny_wmall_order', array('is_remind' => '1'), array('uniacid' => $_W['uniacid'], 'id' => $id));
	message(error(0, ''), '', 'ajax');
}

if($op == 'comment') {
	mload()->func('tpl.app');
	$title = '商品评价';
	$id = intval($_GPC['id']);
	$order = order_fetch($id);
	if(!$_W['ispost']) {
		if(empty($order)) {
			message('订单不存在或已删除', '', 'error');
		}
		$goods = order_fetch_goods($order['id']);
	} else {
		if(empty($order)) {
			message(error(-1, '订单不存在或已删除'), '', 'ajax');
		}
		if($order['is_comment'] == 1) {
			message(error(-1, '订单已评价'), '', 'ajax');
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
			pdo_query('update ' . tablename('tiny_wmall_goods') . $update . ' where id = :id', array(':id' => $good['goods_id']));
		}
		$insert['score'] = $insert['goods_quality'] + $insert['delivery_service'];
		$insert['data'] = iserializer($insert['data']);
		pdo_insert('tiny_wmall_order_comment', $insert);
		pdo_update('tiny_wmall_order', array('is_comment' => 1), array('id' => $id));
		if($store['comment_status'] == 1) {
			store_comment_stat($order['sid']);
		}
		message(error(0, ''), '', 'ajax');
	}
	include $this->template('order-detail');
}

