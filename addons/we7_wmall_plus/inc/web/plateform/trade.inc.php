<?php
/**
 * 外送系统
 * @author 新睿社区
 * @QQ 10373458 * @url http://www.010xr.com/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$_W['page']['title'] = '财务统计-' . $_W['we7_wmall_plus']['config']['title'];
mload()->model('finance');
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'account';

if($op == 'account') {
	$stores = pdo_getall('tiny_wmall_plus_store', array('uniacid' => $_W['uniacid']), array('id', 'title', 'logo'), 'id');
	if(!empty($stores)) {
		$stores_ids = implode(',', array_keys($stores));
		pdo_query('delete from ' . tablename('tiny_wmall_plus_store_account') . " where uniacid = :uniacid and sid not in ({$stores_ids})", array(':uniacid' => $_W['uniacid']));
	}

	$condition = ' as a left join ' . tablename('tiny_wmall_plus_store_account') . ' as b on a.id = b.sid where a.uniacid = :uniacid';
	$params = array(
		':uniacid' => $_W['uniacid']
	);
	$keyword = trim($_GPC['keyword']);
	if(!empty($keyword)) {
		$condition .= " and a.title like '%{$keyword}%'";
	}
	$pindex = max(1, intval($_GPC['page']));
	$psize = 15;
	$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('tiny_wmall_plus_store') . $condition, $params);
	$accounts = pdo_fetchall('SELECT a.*, b.* FROM ' . tablename('tiny_wmall_plus_store') . $condition . ' ORDER BY b.id ASC LIMIT '.($pindex - 1) * $psize.','.$psize, $params);
	if(!empty($accounts)) {
		foreach($accounts as &$row) {
			if($row['delivery_fee_mode'] == 2) {
				$row['delivery_price'] = iunserializer($row['delivery_price']);
			}
		}
	}
	$pager = pagination($total, $pindex, $psize);
	$delivery_modes = store_delivery_modes();
	include $this->template('plateform/trade-account');
}

if($op == 'account_turncate') {
	if(!$_W['isajax']) {
		return false;
	}
	if(empty($_GPC['ids'])) {
		message(error(-1, '请选择要操作的账户'), '', 'ajax');
	}
	$remark = trim($_GPC['remark']);
	foreach($_GPC['ids'] as $sid) {
		$sid = intval($sid);
		if(!$sid) continue;
		$account = pdo_get('tiny_wmall_plus_store_account', array('uniacid' => $_W['uniacid'], 'sid' => $sid));
		if(empty($account) || empty($account['amount']) || $account['amount'] == 0) {
			continue;
		}
		store_update_account($sid, -$account['amount'], 3, '', $remark);
	}
	message(error(0, ''), '', 'ajax');
}

if($op == 'set') {
	$sid = intval($_GPC['id']);
	$store = store_fetch($sid);
	if(empty($store)) {
		message('门店不存在或已删除', referer(), 'error');
	}
	$account = store_account($sid);
	if(checksubmit()) {
		$data = array(
			'uniacid' => $_W['uniacid'],
			'sid' => $sid,
			'instore_serve_rate' => intval($_GPC['instore_serve_rate']),
			'takeout_serve_rate' => intval($_GPC['takeout_serve_rate']),
			'fee_limit' => intval($_GPC['get_cash_fee_limit']),
			'fee_rate' => trim($_GPC['get_cash_fee_rate']) ? trim($_GPC['get_cash_fee_rate']) : 0,
			'fee_min' => intval($_GPC['get_cash_fee_min']),
			'fee_max' => intval($_GPC['get_cash_fee_max']),
		);
		if(empty($config)) {
			$data['amount'] = 0.00;
			pdo_insert('tiny_wmall_plus_store_account', $data);
		} else {
			pdo_update('tiny_wmall_plus_store_account', $data, array('uniacid' => $_W['uniacid'], 'sid' => $sid));
		}
		$update = array(
			'delivery_mode' => intval($_GPC['delivery_mode']),
			'delivery_fee_mode' => intval($_GPC['delivery_fee_mode']),
			'delivery_free_price' => intval($_GPC['delivery_free_price']),
			'auto_get_address' => intval($_GPC['auto_get_address']),
			'send_price' => intval($_GPC['send_price']),
			'delivery_time' => intval($_GPC['delivery_time']),
			'serve_radius' => intval($_GPC['serve_radius']),
			'not_in_serve_radius' => intval($_GPC['not_in_serve_radius']),
		);
		if($update['delivery_fee_mode'] == 1) {
			$update['delivery_price'] = trim($_GPC['delivery_price']);
			if(!$update['not_in_serve_radius']) {
				$update['auto_get_address'] = 1;
				if(empty($update['serve_radius'])) {
					message('您设置了超出配送费范围不允许下单, 此项设置需要设置门店的的配送半径', '', 'info');
				}
			}
		} else {
			$update['auto_get_address'] = 1;
			$update['not_in_serve_radius'] = intval($_GPC['not_in_serve_radius']);
			$update['delivery_price'] = iserializer(array(
				'start_fee' => trim($_GPC['start_fee']),
				'start_km' => trim($_GPC['start_km']),
				'pre_km_fee' => trim($_GPC['pre_km_fee']),
			));
		}
		$times = array();
		if(!empty($_GPC['times']['start'])) {
			foreach($_GPC['times']['start'] as $key => $val) {
				$start = trim($val);
				$end = trim($_GPC['times']['end'][$key]);
				if(empty($start) || empty($end)) {
					continue;
				}
				$times[] = array(
					'start' => $start,
					'end' => $end,
					'status' => intval($_GPC['times']['status'][$key]),
					'fee' => trim($_GPC['times']['fee'][$key])
				);
			}
			$update['delivery_times'] = iserializer($times);
		}
		pdo_update('tiny_wmall_plus_store', $update, array('uniacid' => $_W['uniacid'], 'id' => $sid));
		pdo_query('update ' .  tablename('tiny_wmall_plus_store_deliveryer') . ' set delivery_type = :delivery_type where uniacid = :uniacid and sid = :sid', array(':uniacid' => $_W['uniacid'], ':delivery_type' => $update['delivery_mode'], ':sid' => $sid));
		store_delivery_times($sid, true);
		message('设置提现参数成功', $this->createWebUrl('ptftrade', array('op' => 'account')), 'success');
	}
	include $this->template('plateform/trade-account');
}

if($op == 'currentlog') {
	$condition = ' WHERE uniacid = :aid';
	$params[':aid'] = $_W['uniacid'];
	$sid = intval($_GPC['sid']);
	if($sid > 0) {
		$condition .= ' AND sid = :sid';
		$params[':sid'] = $sid;
	}
	$status = intval($_GPC['status']);
	if($status > 0) {
		if($status == 5) {
			$condition .= ' AND refund_status > 0';
		} else {
			$condition .= ' AND trade_status = :status';
			$params[':status'] = $status;
		}
	}
	if(!empty($_GPC['addtime'])) {
		$starttime = strtotime($_GPC['addtime']['start']);
		$endtime = strtotime($_GPC['addtime']['end']) + 86399;
	} else {
		$today = strtotime(date('Y-m-d'));
		$starttime = strtotime('-15 day', $today);
		$endtime = $today + 86399;
	}
	$condition .= " AND addtime > :start AND addtime < :end";
	$params[':start'] = $starttime;
	$params[':end'] = $endtime;

	$pindex = max(1, intval($_GPC['page']));
	$psize = 15;

	$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('tiny_wmall_plus_order_current_log') .  $condition, $params);
	$records = pdo_fetchall('SELECT * FROM ' . tablename('tiny_wmall_plus_order_current_log') . $condition . ' ORDER BY id DESC LIMIT '.($pindex - 1) * $psize.','.$psize, $params);
	$pager = pagination($total, $pindex, $psize);

	$pay_types = order_pay_types();
	$order_status = order_status();
	$order_trade_status = order_trade_status();
	$order_refund_status = order_refund_status();
	$order_refund_channel = order_refund_channel();
	$stores = pdo_getall('tiny_wmall_plus_store', array('uniacid' => $_W['uniacid']), array('id', 'title', 'logo'), 'id');
	include $this->template('plateform/trade-current');
}

if($op == 'getcashlog') {
	$condition = ' WHERE uniacid = :aid';
	$params[':aid'] = $_W['uniacid'];

	$sid = intval($_GPC['sid']);
	if($sid > 0) {
		$condition .= ' AND sid = :sid';
		$params[':sid'] = $sid;
	}
	$status = intval($_GPC['status']);
	if($status > 0) {
		$condition .= ' AND status = :status';
		$params[':status'] = $status;
	}
	if(!empty($_GPC['addtime'])) {
		$starttime = strtotime($_GPC['addtime']['start']);
		$endtime = strtotime($_GPC['addtime']['end']) + 86399;
	} else {
		$today = strtotime(date('Y-m-d'));
		$starttime = strtotime('-15 day', $today);
		$endtime = $today + 86399;
	}
	$condition .= " AND addtime > :start AND addtime < :end";
	$params[':start'] = $starttime;
	$params[':end'] = $endtime;

	$pindex = max(1, intval($_GPC['page']));
	$psize = 15;

	$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('tiny_wmall_plus_store_getcash_log') .  $condition, $params);
	$records = pdo_fetchall('SELECT * FROM ' . tablename('tiny_wmall_plus_store_getcash_log') . $condition . ' ORDER BY id DESC LIMIT '.($pindex - 1) * $psize.','.$psize, $params);
	if(!empty($records)) {
		foreach($records as &$row) {
			$row['account'] = iunserializer($row['account']);
		}
	}
	$pager = pagination($total, $pindex, $psize);
	$stores = pdo_getall('tiny_wmall_plus_store', array('uniacid' => $_W['uniacid']), array('id', 'title', 'logo'), 'id');
	include $this->template('plateform/trade-getcash');
}

if($op == 'gatcashstatus') {
	$id = intval($_GPC['id']);
	$status = intval($_GPC['status']);
	pdo_update('tiny_wmall_plus_store_getcash_log', array('status' => $status, 'endtime' => TIMESTAMP), array('uniacid' => $_W['uniacid'], 'id' => $id));
	message('设置提现状态成功', referer(), 'success');
}

if($op == 'transfers') {
	$id = intval($_GPC['id']);
	$log = pdo_get('tiny_wmall_plus_store_getcash_log', array('uniacid' => $_W['uniacid'], 'id' => $id));
	if(empty($log)) {
		message('提现记录不存在', referer(), 'error');
	}
	if($log['status'] == 1) {
		message('该提现记录已处理', referer(), 'error');
	}

	$store = store_fetch($log['sid'], array('title'));
	$log['account'] = iunserializer($log['account']);
	mload()->classs('wxpay');
	$pay = new WxPay();
	$params = array(
		'partner_trade_no' => $log['trade_no'],
		'openid' => $log['account']['openid'],
		'check_name' => 'FORCE_CHECK',
		're_user_name' => $log['account']['realname'],
		'amount' => $log['final_fee'] * 100,
		'desc' => "{$store['title']}" . date('Y-m-d H:i') . "提现申请"
	);
	$response = $pay->mktTransfers($params);
	if(is_error($response)) {
		message($response['message'], referer(), 'error');
	}
	sys_notice_store_getcash($log['sid'], $id, 'success');
	pdo_update('tiny_wmall_plus_store_getcash_log', array('status' => 1, 'endtime' => TIMESTAMP), array('uniacid' => $_W['uniacid'], 'id' => $id));
	message('打款成功', referer(), 'success');
}

if($op == 'begin_refund') {
	$id = intval($_GPC['id']);
	$record = pdo_get('tiny_wmall_plus_order_current_log', array('uniacid' => $_W['uniacid'], 'id' => $id));
	if(empty($record)) {
		message('交易记录不存在', referer(), 'error');
	}
	order_insert_order_refund_log($record['orderid'], $record['sid'], 'handel');
	$result = order_begin_payrefund($id);
	if(!is_error($result)) {
		$query = order_query_payrefund($id);
		if(is_error($query)) {
			message('发起退款成功, 获取退款状态失败', referer(), 'error');
		} else {
			$current = order_current_fetch($record['orderid']);
			if($current['refund_status'] == 3) {
				order_insert_order_refund_log($current['orderid'], $current['sid'], 'success');
				order_refund_notice($current['sid'], $current['orderid'], 'success');
			}
			message('发起退款成功, 退款状态已更新', referer(), 'success');
		}
	} else {
		message($result['message'], referer(), 'error');
	}
}

if($op == 'query_refund') {
	$id = intval($_GPC['id']);
	$query = order_query_payrefund($id);
	if(is_error($query)) {
		message('获取退款状态失败', referer(), 'error');
	} else {
		$current = pdo_get('tiny_wmall_plus_order_current_log', array('uniacid' => $_W['uniacid'], 'id' => $id));
		if($current['refund_status'] == 3) {
			order_insert_order_refund_log($current['orderid'], $current['sid'], 'success');
			order_refund_notice($current['sid'], $current['orderid'], 'success');
		}
		message('更新退款状态成功', referer(), 'success');
	}
}
