<?php
/**
 * 外送系统
 * @author 灯火阑珊
 * @QQ 2471240272
 * @url http://bbs.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
mload()->model('coupon');
$do = 'coupon';
$this->checkAuth();
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'list';
$title = '我的代金券';

if($op == 'get') {
	$sid = intval($_GPC['sid']);
	if(!$sid) {
		message(error(-1, '商户信息错误'), '', 'ajax');
	}
	$tokens = coupon_fetchall_user_available($sid, $_W['member']['uid']);
	if(empty($tokens)) {
		message(error(-1, '亲,没有可领取的优惠券'), '', 'ajax');
	}
	foreach($tokens as $key => &$token) {
		$status = coupon_grant($sid, $token['id'], $_W['member']['uid']);
		if(is_error($status)) {
			unset($tokens[$key]);
		}
	}
	if(empty($tokens)) {
		message(error(-1, '亲,没有可领取的优惠券'), '', 'ajax');
	}
	message(error(0, $tokens), '', 'ajax');
}

if($op == 'list') {
	$status = isset($_GPC['status']) ? intval($_GPC['status']) : 1;
	$condition = ' where a.uniacid = :uniacid and a.uid = :uid';
	$params = array(':uniacid' => $_W['uniacid'], ':uid' => $_W['member']['uid']);
	if($status == 1 || $status == 2) {
		$condition .= ' and a.status = :status';
		$params[':status'] = $status;
		if($status == 1) {
			$condition .= ' and b.endtime > :time';
			$params[':time'] = TIMESTAMP;
		}
	} else {
		$condition .= ' and a.status = 1 and b.endtime <= :time';
		$params[':time'] = TIMESTAMP;
	}

	$coupons = pdo_fetchall('select  a.*, a.id as aid, b.id,b.title,b.starttime,b.endtime,b.use_limit,b.discount,b.condition from ' . tablename('tiny_wmall_activity_coupon_record') . ' as a left join ' . tablename('tiny_wmall_activity_coupon') . ' as b on a.couponid = b.id ' . $condition . ' order by a.id desc limit 10', $params, 'aid');
	$min = 0;
	if(!empty($coupons)) {
		foreach($coupons as &$row) {
			$row['store'] = pdo_get('tiny_wmall_store', array('uniacid' => $_W['uniacid'], 'id' => $row['sid']), array('id', 'title'));
		}
		$min = min(array_keys($coupons));
	}
	include $this->template('coupon');
}

if($op == 'more') {
	$id = intval($_GPC['id']);
	$status = isset($_GPC['status']) ? intval($_GPC['status']) : 1;
	$condition = ' where a.uniacid = :uniacid and a.uid = :uid';
	$params = array(':uniacid' => $_W['uniacid'], ':uid' => $_W['member']['uid'], ':id' => $id);
	if($status == 1 || $status == 2) {
		$condition .= ' and a.status = :status';
		$params[':status'] = $status;
	} else {
		$condition .= ' and a.status = 1 and b.endtime <= :time';
		$params[':time'] = TIMESTAMP;
	}
	$coupons = pdo_fetchall('select a.*, a.id as aid, b.id,b.title,b.starttime,b.endtime,b.use_limit,b.discount,b.condition from ' . tablename('tiny_wmall_activity_coupon_record') . ' as a left join ' . tablename('tiny_wmall_activity_coupon') . ' as b on a.couponid = b.id ' . $condition . ' and a.id < :id order by a.id desc limit 15', $params, 'aid');
	$min = 0;
	if(!empty($coupons)) {
		foreach($coupons as &$row) {
			$row['store'] = pdo_get('tiny_wmall_store', array('uniacid' => $_W['uniacid'], 'id' => $row['sid']), array('id', 'title'));
			$row['endtime_cn'] = date('Y-m-d', $row['endtime']);
		}
		$min = min(array_keys($coupons));
	}
	$coupons = array_values($coupons);
	$respon = array('error' => 0, 'message' => $coupons, 'min' => $min);
	message($respon, '', 'ajax');
}
