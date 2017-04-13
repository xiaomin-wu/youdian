<?php
/**
 * 外送系统
 * @author 灯火阑珊
 * @QQ 2471240272
 * @url http://bbs.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$_W['page']['title'] = '收入/提现-' . $_W['we7_wmall']['config']['title'];
mload()->model('coupon');
$store = store_check();
$sid = $store['id'];
$do = 'trade';

$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'index';

if($op == 'index') {
	$account = store_account($sid);
	$stat = array();
	$addtime = strtotime('-7days', strtotime(date('Y-m-d')));
	$stat['week_total'] = floatval(pdo_fetchcolumn('select sum(final_fee) from ' . tablename('tiny_wmall_order_current_log') . ' where uniacid = :uniacid and sid = :sid and trade_status = 1 and addtime >= :addtime', array(':uniacid' => $_W['uniacid'], ':sid' => $sid, ':addtime' => $addtime)));
	$stat['wait_total'] = floatval(pdo_fetchcolumn('select sum(final_fee) from ' . tablename('tiny_wmall_order_current_log') . ' where uniacid = :uniacid and sid = :sid and trade_status = 2', array(':uniacid' => $_W['uniacid'], ':sid' => $sid)));
	$records = pdo_fetchall('SELECT * FROM ' . tablename('tiny_wmall_order_current_log') . 'WHERE uniacid = :uniacid AND sid = :sid ORDER BY id DESC LIMIT 30', array(':uniacid' => $_W['uniacid'], ':sid' => $sid));
	$order_status = order_status();
	$order_trade_status = order_trade_status();
	include $this->template('store/trade-index');
}

if($op == 'currentlog') {
	$condition = ' WHERE uniacid = :aid AND sid = :sid';
	$params[':aid'] = $_W['uniacid'];
	$params[':sid'] = $sid;
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

	$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('tiny_wmall_order_current_log') .  $condition, $params);
	$records = pdo_fetchall('SELECT * FROM ' . tablename('tiny_wmall_order_current_log') . $condition . ' ORDER BY id DESC LIMIT '.($pindex - 1) * $psize.','.$psize, $params);

	$pager = pagination($total, $pindex, $psize);
	$pay_types = order_pay_types();
	$order_status = order_status();
	$order_trade_status = order_trade_status();
	$order_refund_status = order_refund_status();
	$order_refund_channel = order_refund_channel();
	include $this->template('store/trade-current');
}

if($op == 'inout') {
	$condition = ' WHERE uniacid = :aid AND sid = :sid';
	$params[':aid'] = $_W['uniacid'];
	$params[':sid'] = $sid;
	$trade_type = intval($_GPC['trade_type']);
	if($trade_type > 0) {
		$condition .= ' AND trade_type = :trade_type';
		$params[':trade_type'] = $trade_type;
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

	$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('tiny_wmall_store_current_log') .  $condition, $params);
	$records = pdo_fetchall('SELECT * FROM ' . tablename('tiny_wmall_store_current_log') . $condition . ' ORDER BY id DESC LIMIT '.($pindex - 1) * $psize.','.$psize, $params);
	$order_trade_type = order_trade_type();
	$pager = pagination($total, $pindex, $psize);
	include $this->template('store/trade-inout');
}

if($op == 'getcashlog') {
	$condition = ' WHERE uniacid = :aid AND sid = :sid';
	$params[':aid'] = $_W['uniacid'];
	$params[':sid'] = $sid;
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

	$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('tiny_wmall_store_getcash_log') .  $condition, $params);
	$records = pdo_fetchall('SELECT * FROM ' . tablename('tiny_wmall_store_getcash_log') . $condition . ' ORDER BY id DESC LIMIT '.($pindex - 1) * $psize.','.$psize, $params);
	if(!empty($records)) {
		foreach($records as &$row) {
			$row['account'] = iunserializer($row['account']);
		}
	}
	$pager = pagination($total, $pindex, $psize);
	include $this->template('store/trade-getcash');
}

if($op == 'getcash') {
	$account = store_account($sid);
	if(checksubmit()) {
		if(empty($account['wechat']['openid']) || empty($account['wechat']['realname'])) {
			message('提现前请先完善提现账户', referer(), 'error');
		}
		$get_fee = intval($_GPC['get_fee']);
		if($get_fee < $account['fee_limit']) {
			message('提现金额小于最低提现金额限制', referer(), 'error');
		}
		if($get_fee > $account['amount']) {
			message('提现金额大于账户可用余额', referer(), 'error');
		}
		$take_fee = round($get_fee * ($account['fee_rate'] / 100), 2);
		$take_fee = max($take_fee, $account['fee_min']);
		$take_fee = min($take_fee, $account['fee_max']);
		$final_fee = $get_fee - $take_fee;
		if($final_fee < 0)  {
			$final_fee = 0;
		}
		$data = array(
			'uniacid' => $_W['uniacid'],
			'sid' => $sid,
			'trade_no' => date('YmdHis') . random(10, true),
			'get_fee' => $get_fee,
			'take_fee' => $take_fee,
			'final_fee' => $final_fee,
			'account' => iserializer($account['wechat']),
			'status' => 2,
			'addtime' => TIMESTAMP,
		);
		pdo_insert('tiny_wmall_store_getcash_log', $data);
		$getcash_id = pdo_insertid();
		store_update_account($sid, -$get_fee, 2, $getcash_id);
		//提现通知
		sys_notice_store_getcash($sid, $getcash_id, 'apply');
		message('申请提现成功', $this->createWebUrl('trade', array('op' => 'getcashlog')) , 'success');
	}
	include $this->template('store/trade-getcash');
}

if($op == 'getcashaccount') {
	$config = store_account($sid);
	if(checksubmit()) {
		$data = array(
			'uniacid' => $_W['uniacid'],
			'sid' => $sid,
		);
		$wechat = array();
		$wechat['openid'] = trim($_GPC['wechat']['openid']) ? trim($_GPC['wechat']['openid']) : message('微信昵称不能为空');
		$wechat['nickname'] = trim($_GPC['wechat']['nickname']);
		$wechat['avatar'] = trim($_GPC['wechat']['avatar']);
		$wechat['realname'] = trim($_GPC['wechat']['realname']) ? trim($_GPC['wechat']['realname']) : message('微信实名认证姓名不能为空');
		$data['wechat'] = iserializer($wechat);

		if(empty($config)) {
			$data['amount'] = 0.00;
			pdo_insert('tiny_wmall_store_account', $data);
		} else {
			pdo_update('tiny_wmall_store_account', $data, array('uniacid' => $_W['uniacid'], 'sid' => $sid));
		}
		message('设置提现账户成功', $this->createWebUrl('trade', array('op' => 'getcashaccount')), 'success');
	}
	include $this->template('store/trade-getcash');
}

