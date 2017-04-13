<?php
/**
 * 外送系统
 * @author 灯火阑珊
 * @QQ 2471240272
 * @url http://bbs.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$_W['page']['title'] = '代金券-' . $_W['we7_wmall']['config']['title'];
mload()->model('coupon');
$store = store_check();
$sid = $store['id'];
$do = 'token';

$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'set';

if($op == 'post') {
	$id = intval($_GPC['id']);
	if($id > 0) {
		$token = coupon_fetch($id);
	} else {
		$token['starttime'] = TIMESTAMP;
		$token['endtime'] = strtotime('7days');
	}

	if(checksubmit()) {
		$title = trim($_GPC['title']) ? trim($_GPC['title']) : message('标题不能为空', '', 'info');
		$condition = intval($_GPC['condition']) ? intval($_GPC['condition']) : message('使用条件必须大于0', '', 'info');
		$discount = intval($_GPC['discount']) ? intval($_GPC['discount']) : message('优惠金额必须大于0', '', 'info');
		if($discount > $condition) {
			message('优惠金额不能大于使用条件的金额', '', 'info');
		}
		$data = array(
			'uniacid' => $_W['uniacid'],
			'sid' => $sid,
			'title' => $title,
			'type' => 2,
			'condition' => $condition,
			'discount' => $discount,
			'amount' => intval($_GPC['amount']),
			'get_limit' => intval($_GPC['get_limit']),
			'grant_type' => intval($_GPC['grant_type']),
			'status' => intval($_GPC['status']),
			'type_limit' => intval($_GPC['type_limit']),
			'use_limit' => intval($_GPC['use_limit']),
			'starttime' => strtotime($_GPC['datelimit']['start']),
			'endtime' => strtotime($_GPC['datelimit']['end']),
		);
		if($token['id'] > 0) {
			pdo_update('tiny_wmall_activity_coupon', $data, array('uniacid' => $_W['uniacid'], 'id' => $id));
			pdo_update('tiny_wmall_activity_coupon_grant_log', array('grant_type' => $data['grant_type']), array('uniacid' => $_W['uniacid'], 'couponid' => $id));
		} else {
			$data['addtime'] = TIMESTAMP;
			pdo_insert('tiny_wmall_activity_coupon', $data);
			$id = pdo_insertid();
		}
		message('编辑代金券成功', $this->createWebUrl('token'), 'success');
	}
}

if($op == 'list') {
	$condition = ' where uniacid = :uniacid and sid = :sid and type = 2';
	$params = array(
		':uniacid' => $_W['uniacid'],
		':sid' => $sid,
	);
	$pindex = max(1, intval($_GPC['page']));
	$psize = 20;
	$orderby = ' order by id desc limit '. ($pindex - 1) * $psize . ',' . $psize;

	$total = pdo_fetchcolumn('SELECT count(*) FROM ' . tablename('tiny_wmall_activity_coupon') . $condition , $params);
	$tokens = pdo_fetchall('SELECT * FROM ' . tablename('tiny_wmall_activity_coupon') . $condition . $orderby , $params);
	$pager = pagination($total, $pindex, $psize);
}

if($op == 'del') {
	$id = intval($_GPC['id']);
	pdo_delete('tiny_wmall_activity_coupon',  array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'id' => $id));
	pdo_delete('tiny_wmall_activity_coupon_record',  array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'couponid' => $id));
	pdo_delete('tiny_wmall_activity_coupon_grant_log',  array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'couponid' => $id));
	message('删除代金券成功', referer(), 'success');
}

if($op == 'record_del') {
	$id = intval($_GPC['id']);
	pdo_delete('tiny_wmall_activity_coupon_record',  array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'id' => $id));
	message('删除领取记录成功', referer(), 'success');
}

if($op == 'status') {
	$id = intval($_GPC['id']);
	$status = intval($_GPC['status']);
	$state = pdo_update('tiny_wmall_activity_coupon', array('status' => $status), array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'id' => $id));
	if($state !== false) {
		exit('success');
	}
	exit('error');
}

if($op == 'record') {
	$id = intval($_GPC['id']);
	$token = coupon_fetch($id);
	if(empty($token)) {
		message('代金券不存在或已删除', referer(), 'error');
	}

	$condition = ' where uniacid = :uniacid and sid = :sid and couponid = :couponid';
	$params = array(
		':uniacid' => $_W['uniacid'],
		':sid' => $sid,
		':couponid' => $id,
	);
	$status = intval($_GPC['status']);
	if($status > 0) {
		$condition .= ' AND status = :status';
		$params[':status'] = $status;
	}
	if(!empty($_GPC['addtime'])) {
		$starttime = strtotime($_GPC['addtime']['start']);
		$endtime = strtotime($_GPC['addtime']['end']) + 86399;
	} else {
		$starttime = strtotime('-15 day');
		$endtime = TIMESTAMP;
	}
	$condition .= " AND granttime > :start AND granttime < :end";
	$params[':start'] = $starttime;
	$params[':end'] = $endtime;

	$pindex = max(1, intval($_GPC['page']));
	$psize = 20;
	$orderby = ' order by id desc limit '. ($pindex - 1) * $psize . ',' . $psize;

	$total = pdo_fetchcolumn('SELECT count(*) FROM ' . tablename('tiny_wmall_activity_coupon_record') . $condition , $params);
	$records = pdo_fetchall('SELECT * FROM ' . tablename('tiny_wmall_activity_coupon_record') . $condition . $orderby , $params);
	if(!empty($records)) {
		foreach($records as &$record) {
			$record['user'] = pdo_get('tiny_wmall_members', array('uid' => $record['uid']));
		}
	}
	$pager = pagination($total, $pindex, $psize);
}

if($op == 'set') {
	if(checksubmit()) {
		$collect_coupon_status = intval($_GPC['collect_coupon_status']);
		pdo_update('tiny_wmall_store', array('collect_coupon_status' => $collect_coupon_status), array('uniacid' => $_W['uniacid'], 'id' => $sid));
		pdo_update('tiny_wmall_store_activity', array('collect_coupon_status' => $collect_coupon_status), array('uniacid' => $_W['uniacid'], 'sid' => $sid));
		message('设置进店领取优惠券成功', $this->createWebUrl('token'), 'success');
	}
	$activity = store_fetch_activity($sid);
	if(empty($activity)) {
		$insert = array(
			'uniacid' => $_W['uniacid'],
			'sid' => $sid
		);
		pdo_insert('tiny_wmall_store_activity', $insert);
	}
}

include $this->template('store/token');
