<?php
/**
 * [WeEngine System] Copyright (c) 2013 WE7.CC
 * $sn$
 */
defined('IN_IA') or exit('Access Denied');

function coupon_fetch($id) {
	global $_W;
	$data = pdo_get('tiny_wmall_activity_coupon', array('uniacid' => $_W['uniacid'], 'id' => $id));
	return $data;
}

function coupon_fetchall_user_available($sid, $uid) {
	global $_W;
	$condition = ' where uniacid = :uniacid and sid = :sid and status = 1';
	$is_first = pdo_get('tiny_wmall_store_members', array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'uid' => $uid));
	if(empty($is_first)) {
		$condition .= ' and (type_limit = 1 or type_limit = 2)';
	} else {
		$condition .= ' and type_limit = 1';
	}

	$filter1 = ' grant_type = 1 and id not in (select couponid from  ' . tablename('tiny_wmall_activity_coupon_grant_log') . ' where sid = :sid and uid = :uid and grant_type = 1)';
	$filter2 = ' grant_type = 2 and id not in (select couponid from  ' . tablename('tiny_wmall_activity_coupon_grant_log') . ' where sid = :sid and uid = :uid and grant_type = 2 and addtime > :todaytime)';
	$condition .= " and (amount-dosage > 0) and starttime <= :time and endtime >= :time and (({$filter1}) or ({$filter2}))";
	$params = array(
		':uniacid' => $_W['uniacid'],
		':sid' => $sid,
		':time' => TIMESTAMP,
		':uid' => $uid,
		':todaytime' => strtotime(date('Y-m-d'))
	);
	$data = pdo_fetchall('select *, amount-dosage as residue from ' . tablename('tiny_wmall_activity_coupon') . $condition, $params);
	return $data;
}

function coupon_grant($sid, $couponid, $uid, $remark = '') {
	global $_W;
	$token = coupon_fetch($couponid);
	if(empty($token)) {
		return error(-1, '代金券不存在');
	}
	if($token['amount'] <= $token['dosage']) {
		return error(-1, '代金券已抢光');
	}
	if($token['endtime'] <= TIMESTAMP) {
		return error(-1, '代金券已过期');
	}
	$is_first = pdo_get('tiny_wmall_store_members', array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'uid' => $uid));
	if($token['type_limit'] == 2 && !empty($is_first)) {
		return error(-1, '该代金券仅限新用户领取');
	}
	$record = pdo_get('tiny_wmall_activity_coupon_record', array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'uid' => $uid, 'couponid' => $couponid));
	if(!empty($record)) {
		if($token['grant_type'] == 1) {
			return error(-1, '已经领取过该优惠券');
		} else {
			$time = strtotime(date('Y-m-d'));
			if($record['granttime'] >= $time) {
				return error(-1, '今天已领取过该优惠券');
			}
		}
	}
	$data = array(
		'uniacid' => $_W['uniacid'],
		'sid' => $sid,
		'couponid' => $couponid,
		'uid' => $uid,
		'code' => random(8, true),
		'granttime' => TIMESTAMP,
		'status' => 1,
		'remark' => $remark,
	);
	pdo_insert('tiny_wmall_activity_coupon_record', $data);
	pdo_update('tiny_wmall_activity_coupon', array('dosage' => $token['dosage'] + 1), array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'id' => $couponid));
	$log = pdo_get('tiny_wmall_activity_coupon_grant_log', array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'uid' => $uid, 'couponid' => $couponid));
	if(empty($log)) {
		$data = array(
			'uniacid' => $_W['uniacid'],
			'sid' => $sid,
			'couponid' => $couponid,
			'uid' => $uid,
			'grant_type' => $token['grant_type'],
			'addtime' => TIMESTAMP
		);
		pdo_insert('tiny_wmall_activity_coupon_grant_log', $data);
	} else {
		pdo_update('tiny_wmall_activity_coupon_grant_log', array('addtime' => TIMESTAMP), array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'uid' => $uid, 'couponid' => $couponid));
	}
	return true;
}