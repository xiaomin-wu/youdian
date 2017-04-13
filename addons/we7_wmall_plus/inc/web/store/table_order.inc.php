<?php
/**
 * 外送系统
 * @author 新睿社区
 * @QQ 10373458 * @url http://www.010xr.com/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$_W['page']['title'] = '订单列表-' . $_W['we7_wmall_plus']['config']['title'];
mload()->model('table');
$store = store_check();
$sid = $store['id'];
$store = store_fetch($sid);
$do = 'table_order';
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'list';

if($op == 'list') {
	$condition = ' WHERE uniacid = :aid AND sid = :sid and order_type >= 3';
	$params[':aid'] = $_W['uniacid'];
	$params[':sid'] = $sid;

	$status = intval($_GPC['status']);
	if($status > 0) {
		$condition .= ' AND status = :stu';
		$params[':stu'] = $status;
	}
	$is_pay = isset($_GPC['is_pay']) ? intval($_GPC['is_pay']) : -1;
	if($is_pay >= 0) {
		$condition .= ' AND is_pay = :is_pay';
		$params[':is_pay'] = $is_pay;
	}
	$keyword = trim($_GPC['keyword']);
	if(!empty($keyword)) {
		$condition .= " AND (username LIKE '%{$keyword}%' OR mobile LIKE '%{$keyword}%')";
	}
	if(!empty($_GPC['addtime'])) {
		$starttime = strtotime($_GPC['addtime']['start']);
		$endtime = strtotime($_GPC['addtime']['end']) + 86399;
	} else {
		$starttime = strtotime('-15 day');
		$endtime = TIMESTAMP;
	}
	$condition .= " AND addtime > :start AND addtime < :end";
	$params[':start'] = $starttime;
	$params[':end'] = $endtime;

	$pindex = max(1, intval($_GPC['page']));
	$psize = 15;

	$wait_total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('tiny_wmall_plus_order') . ' WHERE uniacid = :uniacid AND sid = :sid and status = 1 and order_type > 2', array(':uniacid' => $_W['uniacid'], ':sid' => $sid));
	$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('tiny_wmall_plus_order') .  $condition, $params);
	$data = pdo_fetchall('SELECT * FROM ' . tablename('tiny_wmall_plus_order') . $condition . ' ORDER BY addtime DESC LIMIT '.($pindex - 1) * $psize.','.$psize, $params);
	if(!empty($data)) {
		$tables = array();
		foreach($data as $da) {
			if($da['order_type'] == 3) {
				$tables[] = $da['table_id'];
			}
		}
		if(!empty($tables)) {
			$tables_str = implode(',', $tables);
			$tables = pdo_fetchall('select * from ' . tablename('tiny_wmall_plus_tables') . " where uniacid = :uniacid and sid = :sid and id in ({$tables_str})", array(':uniacid' => $_W['uniacid'], ':sid' => $sid), 'id');
		}
	}
	$pager = pagination($total, $pindex, $psize);
	$pay_types = order_pay_types();
	$order_types = order_types();
	$order_reserve_types = order_reserve_type();
	$order_status = order_status();
	$refund_status = order_refund_status();
	$store_ = store_fetch($sid, array('remind_reply'));
	$table_categorys = table_category_fetchall($sid);
}

if($op == 'status') {
	$ids = $_GPC['id'];
	if(!is_array($ids)) {
		$ids = array($ids);
	}
	$type = trim($_GPC['type']);
	if(empty($type)) {
		message('订单状态错误', referer(), 'error');
	}
	foreach($ids as $id) {
		$id = intval($id);
		if($id <= 0) continue;
		$result = order_status_update($id, $type);
		if(is_error($result)) {
			message("处理编号为:{$id}的订单失败，具体原因：{$result['message']}", referer(), 'error');
		}
	}
	message('更新订状态成功', referer(), 'success');
}

if($op == 'cancel') {
	$id = intval($_GPC['id']);
	$result = order_status_update($id, 'cancel');
	if(is_error($result)) {
		message($result['message'], referer(), 'error');
	}
	if($result['message']['is_refund']) {
		message('取消订单成功, 退款会在1-15个工作日打到客户账户', referer(), 'success');
	} else {
		message('取消订单成功', referer(), 'success');
	}
}

if($op == 'pay_status') {
	$id = intval($_GPC['id']);
	$result = order_status_update($id, 'pay');
	if(is_error($result)) {
		message($result['message'], referer(), 'error');
	}
	message('设置订单支付成功', referer(), 'success');
}

if($op == 'detail') {
	$id = intval($_GPC['id']);
	$order = order_fetch($id);
	if(empty($order)) {
		message('订单不存在或已经删除', $this->createWebUrl('manage', array('op' => 'order')), 'error');
	}
	$order['goods'] = order_fetch_goods($order['id']);
	if($order['is_comment'] == 1) {
		$comment = pdo_fetch('SELECT * FROM ' . tablename('tiny_wmall_plus_order_comment') .' WHERE uniacid = :aid AND oid = :oid', array(':aid' => $_W['uniacid'], ':oid' => $id));
		if(!empty($comment)) {
			$comment['data'] = iunserializer($comment['data']);
		}
	}
	if($order['discount_fee'] > 0) {
		$discount = order_fetch_discount($id);
	}
	$pay_types = order_pay_types();
	$order_types = order_types();
	$order_status = order_status();
	$logs = order_fetch_status_log($id);
}

if($op == 'del' && 0) {
	$id = intval($_GPC['id']);
	$order = order_fetch($id);
	if(empty($order)) {
		message('订单不存在或已删除', referer(), 'error');
	}
	if($order['status'] != 6) {
		message('该订单正在进行中或已完成,不能删除', referer(), 'error');
	}
	pdo_delete('tiny_wmall_plus_order', array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'id' => $id));
	pdo_delete('tiny_wmall_plus_order_stat', array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'oid' => $id));
	pdo_delete('tiny_wmall_plus_order_comment', array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'oid' => $id));
	pdo_delete('tiny_wmall_plus_order_refund_log', array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'oid' => $id));
	message('删除订单成功', $this->createWebUrl('table_order', array('op' => 'list')), 'success');
}

if($op == 'print') {
	$id = intval($_GPC['id']);
	$status = order_print($id, true);
	if(is_error($status)) {
		exit($status['message']);
	}
	exit('success');
}

if($op == 'reply_remind') {
	if(!$_W['isajax']) {
		return false;
	}
	$id = intval($_GPC['id']);
	$order = order_fetch($id);
	if(empty($order)) {
		message(error(-1, '订单不存在或已经删除'), '', 'ajax');
	}
	$content = trim($_GPC['content']);
	pdo_update('tiny_wmall_plus_order', array('is_remind' => 0), array('uniacid' => $_W['uniacid'], 'id' => $id));
	order_insert_status_log($id, 'remind_reply', $content);
	order_status_notice($order['sid'], $id, 'reply_remind', "回复内容：" . $content);
	message(error(0, ''), '', 'ajax');
}
$GLOBALS['frames'] = array();
include $this->template('store/table-order');