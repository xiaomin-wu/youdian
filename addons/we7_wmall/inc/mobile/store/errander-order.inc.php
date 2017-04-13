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
$this->checkAuth();
mload()->model('errander');
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'list';

$config = $_W['we7_wmall']['config'];
if($op == 'list') {
	$title = "跑腿订单";
	$total_user = pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_errander_order') . ' where uniacid = :uniacid', array(':uniacid' => $_W['uniacid']));
	$orders = pdo_fetchall('select a.*,b.title,b.thumb from ' . tablename('tiny_wmall_errander_order') . ' as a left join ' . tablename('tiny_wmall_errander_category') . ' as b on a.order_cid = b.id where a.uniacid = :uniacid and a.uid = :uid order by a.id desc limit 15', array(':uniacid' => $_W['uniacid'], ':uid' => $_W['member']['uid']), 'id');
	$min = 0;
	if(!empty($orders)) {
		$order_status = errander_order_status();
		$min = min(array_keys($orders));
		foreach($orders as &$row) {
			if($row['deliveryer_id'] > 0) {
				$row['deliveryer'] = pdo_get('tiny_wmall_deliveryer', array('uniacid' => $_W['uniacid'], 'id' => $row['deliveryer_id']));
			}
		}
	} else {
		$others = pdo_fetchall('select a.*,b.title,b.thumb from ' . tablename('tiny_wmall_errander_order') . ' as a left join ' . tablename('tiny_wmall_errander_category') . ' as b on a.order_cid = b.id where a.uniacid = :uniacid order by a.id desc limit 5', array(':uniacid' => $_W['uniacid']), 'id');
	}
	include $this->template('errander-order-list');
}

if($op == 'more') {
	$id = intval($_GPC['id']);
	$orders = pdo_fetchall('select a.*,b.title,b.thumb from ' . tablename('tiny_wmall_errander_order') . ' as a left join ' . tablename('tiny_wmall_errander_category') . ' as b on a.order_cid = b.id where a.uniacid = :uniacid and a.uid = :uid and a.id < :id order by a.id desc limit 15', array(':uniacid' => $_W['uniacid'], ':uid' => $_W['member']['uid'], ':id' => $id), 'id');
	$min = 0;
	if(!empty($orders)) {
		$order_status = errander_order_status();
		foreach($orders as &$order) {
			$order['addtime_cn'] = date('Y-m-d H:i:s', $order['addtime']);
			$order['time_cn'] = date('H:i', $order['addtime']);
			$order['status_cn'] = $order_status[$order['status']]['text'];
			$order['thumb'] = tomedia($order['thumb']);
			$order['deliveryer'] = pdo_get('tiny_wmall_deliveryer', array('uniacid' => $_W['uniacid'], 'id' => $order['deliveryer_id']));
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
	pdo_update('tiny_wmall_order', array('status' => 5, 'delivery_status' => 5, 'deliveryedtime' => TIMESTAMP), array('uniacid' => $_W['uniacid'], 'id' => $id));
	order_update_current_log($id, 5);
	order_insert_status_log($id, $order['sid'], 'end');
	order_status_notice($order['sid'], $order['id'], 'end');
	message(error(0, ''), '', 'ajax');
}

if($op == 'detail') {
	$title = "订单详情";
	$id = intval($_GPC['id']);
	$order = errander_order_fetch($id);
	if(empty($order)) {
		message('订单不存在或已删除', '', 'error');
	}
	$log = pdo_fetch('select * from ' . tablename('tiny_wmall_errander_order_status_log') . ' where uniacid = :uniacid and oid = :oid order by id desc', array(':uniacid' => $_W['uniacid'], ':oid' => $id));
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
	$deliveryer = pdo_get('tiny_wmall_deliveryer', array('uniacid' => $_W['uniacid'], 'id' => $order['deliveryer_id']));
	$order_types = errander_types();
	$pay_types = order_pay_types();
	$order_status = errander_order_status();
	include $this->template('errander-order-detail');
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

