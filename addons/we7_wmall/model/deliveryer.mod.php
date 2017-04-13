<?php
/**
 * [WeEngine System] Copyright (c) 2013 WE7.CC
 * $sn$
 */
defined('IN_IA') or exit('Access Denied');

function deliveryer_fetchall($sid = 0) {
	global $_W;
	$data = pdo_fetchall("SELECT * FROM " . tablename('tiny_wmall_store_deliveryer') . ' WHERE uniacid = :uniacid and sid = :sid', array(':uniacid' => $_W['uniacid'], ':sid' => $sid), 'deliveryer_id');
	if(!empty($data)) {
		$ids = implode(',', array_keys($data));
		$deliveryers = pdo_fetchall('select * from ' . tablename('tiny_wmall_deliveryer') . " WHERE uniacid = :uniacid and id in ({$ids})", array(':uniacid' => $_W['uniacid']), 'id');
		foreach($data as &$da) {
			$deliveryers[$da['deliveryer_id']]['avatar'] = tomedia($deliveryers[$da['deliveryer_id']]['avatar']);
			$da['deliveryer'] = $deliveryers[$da['deliveryer_id']];
		}
	}
	return $data;
}

//获取平台的所有配送员
function deliveryer_all($force_update = false) {
	global $_W;
	$cache_key = "tiny_wmall:deliveryers:{$_W['uniacid']}";
	$data = cache_read($cache_key);
	if(!empty($data) && !$force_update) {
		return $data;
	}
	$deliveryers = pdo_fetchall('select * from ' . tablename('tiny_wmall_deliveryer') . " WHERE uniacid = :uniacid", array(':uniacid' => $_W['uniacid']), 'id');
	cache_write($cache_key, $deliveryers);
	return $deliveryers;
}

function deliveryer_fetch($id) {
	global $_W;
	$data = pdo_fetch("SELECT * FROM " . tablename('tiny_wmall_store_deliveryer') . ' WHERE uniacid = :uniacid AND deliveryer_id = :deliveryer_id', array(':uniacid' => $_W['uniacid'], ':deliveryer_id' => $id));
	if(!empty($data)) {
		$data['deliveryer'] = pdo_get('tiny_wmall_deliveryer', array('uniacid' => $_W['uniacid'], 'id' => $id));
	}
	return $data;
}

function deliveryer_order_stat($sid, $deliveryer_id) {
	global $_W;
	$stat = array();
	$today_starttime = strtotime(date('Y-m-d'));
	$yesterday_starttime = $today_starttime - 86400;
	$month_starttime = strtotime(date('Y-m'));
	$stat['yesterday_num'] = intval(pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_order') . ' where uniacid = :uniacid and sid = :sid and deliveryer_id = :deliveryer_id and delivery_type = 1 and status =5 and addtime >= :starttime and addtime <= :endtime', array(':uniacid' => $_W['uniacid'], ':sid' => $sid, ':deliveryer_id' => $deliveryer_id, ':starttime' => $yesterday_starttime, ':endtime' => $today_starttime)));
	$stat['today_num'] = intval(pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_order') . ' where uniacid = :uniacid and sid = :sid and deliveryer_id = :deliveryer_id and delivery_type = 1 and status =5 and addtime >= :starttime', array(':uniacid' => $_W['uniacid'], ':sid' => $sid, ':deliveryer_id' => $deliveryer_id, ':starttime' => $today_starttime)));
	$stat['month_num'] = intval(pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_order') . ' where uniacid = :uniacid and sid = :sid and deliveryer_id = :deliveryer_id and delivery_type = 1 and status =5 and addtime >= :starttime', array(':uniacid' => $_W['uniacid'], ':sid' => $sid, ':deliveryer_id' => $deliveryer_id, ':starttime' => $month_starttime)));
	$stat['total_num'] = intval(pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_order') . ' where uniacid = :uniacid and sid = :sid and deliveryer_id = :deliveryer_id and delivery_type = 1 and status =5', array(':uniacid' => $_W['uniacid'], ':sid' => $sid, ':deliveryer_id' => $deliveryer_id)));
	return $stat;
}

function deliveryer_plateform_order_stat($deliveryer_id) {
	global $_W;
	$stat = array();
	$today_starttime = strtotime(date('Y-m-d'));
	$yesterday_starttime = $today_starttime - 86400;
	$month_starttime = strtotime(date('Y-m'));
	$stat['yesterday_num'] = intval(pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_order') . ' where uniacid = :uniacid and deliveryer_id = :deliveryer_id and delivery_type = 2 and status =5 and addtime >= :starttime and addtime <= :endtime', array(':uniacid' => $_W['uniacid'], ':deliveryer_id' => $deliveryer_id, ':starttime' => $yesterday_starttime, ':endtime' => $today_starttime)));
	$stat['today_num'] = intval(pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_order') . ' where uniacid = :uniacid and deliveryer_id = :deliveryer_id and delivery_type = 2 and status =5 and addtime >= :starttime', array(':uniacid' => $_W['uniacid'], ':deliveryer_id' => $deliveryer_id, ':starttime' => $today_starttime)));
	$stat['month_num'] = intval(pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_order') . ' where uniacid = :uniacid and deliveryer_id = :deliveryer_id and delivery_type = 2 and status =5 and addtime >= :starttime', array(':uniacid' => $_W['uniacid'], ':deliveryer_id' => $deliveryer_id, ':starttime' => $month_starttime)));
	$stat['total_num'] = intval(pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_order') . ' where uniacid = :uniacid and deliveryer_id = :deliveryer_id and delivery_type = 2 and status =5', array(':uniacid' => $_W['uniacid'], ':deliveryer_id' => $deliveryer_id)));
	return $stat;
}


function checkdeliveryer() {
	global $_W;
	if(empty($_W['openid'])) {
		$this->imessage('获取身份信息错误', referer(), 'error', '', '返回');
	}
	$deliveryer = pdo_get('tiny_wmall_deliveryer', array('uniacid' => $_W['uniacid'], 'openid' => $_W['openid']));
	if(empty($deliveryer)) {
		$this->imessage('您没有配送订单的权限', referer(), 'error', '请联系店铺管理员开通权限', '返回');
	}
	return $deliveryer;
}

function deliveryer_update_credit2($deliveryer_id, $fee, $trade_type, $extra, $remark = '', $order_type = 'order') {
	global $_W;
	//$trade_type 1: 订单入账, 2: 申请提现, 3: 其他变动
	$deliveryer = pdo_get('tiny_wmall_deliveryer', array('uniacid' => $_W['uniacid'], 'id' => $deliveryer_id));
	if(empty($deliveryer)) {
		return error(-1, '账户不存在');
	}
	$now_amount = $deliveryer['credit2'] + $fee;
	pdo_update('tiny_wmall_deliveryer', array('credit2' => $now_amount), array('uniacid' => $_W['uniacid'], 'id' => $deliveryer_id));
	$log = array(
		'uniacid' => $_W['uniacid'],
		'deliveryer_id' => $deliveryer_id,
		'order_type' => $order_type,
		'trade_type' => $trade_type,
		'extra' => $extra,
		'fee' => $fee,
		'amount' => $now_amount,
		'addtime' => TIMESTAMP,
		'remark' => $remark
	);
	pdo_insert('tiny_wmall_deliveryer_current_log', $log);
	return true;
}

//配送员app函数
function deliveryer_login($mobile, $password) {
	global $_W;
	$deliveryer = pdo_get('tiny_wmall_deliveryer', array('uniacid' => $_W['uniacid'], 'mobile' => $mobile));
	if(empty($deliveryer)) {
		return ierror(-1, '账号不存在');
	}
	$password = md5(md5($deliveryer['salt'] . $password) . $deliveryer['salt']);
	if($password != $deliveryer['password']) {
		return ierror(-1, '密码错误');
	}
	if(empty($deliveryer['token'])) {
		$token = $deliveryer['token'] = random(32);
		pdo_update('tiny_wmall_deliveryer', array('token' => $token), array('uniacid' => $_W['uniacid'], 'id' => $deliveryer['id']));
	}
	return ierror(0, '调用成功', $deliveryer);
}
