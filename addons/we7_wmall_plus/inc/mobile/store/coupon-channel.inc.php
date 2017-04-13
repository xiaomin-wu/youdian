<?php
/**
 * 外送系统
 * @author 新睿社区
 * @QQ 10373458 * @url http://www.010xr.com/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$this->checkAuth();
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'list';

if($op == 'list'){
	$id = intval($_GPC['id']);
	$condition = ' where a.status = 1 and a.dosage < a.amount and a.starttime < :time and a.endtime > :time';
	$params = array(':time' => TIMESTAMP);
	if($id > 0) {
		$condition .= ' and a.id < :id';
		$params[':id'] = $id;
	}
	$condition = ' order by a.id desc limit 10';
	$coupons = pdo_fetchall('select a.*,b.title as store_title,b.logo from ' . tablename('tiny_wmall_plus_activity_coupon') . ' as a left join '.tablename('tiny_wmall_plus_store').' as b on a.sid = b.id '. $condition, $params, 'id');
	$min = 0;
	if(!empty($coupons)) {
		$condition = " where uid = :uid group by couponid";
		$params = array(
			':uid' => $_W['member']['uid']
		);
		$records = pdo_fetchall('select id,couponid,granttime from' . tablename('tiny_wmall_plus_activity_coupon_record') . $condition, $params, 'couponid');
		$coupon_ids = array();
		if(!empty($records)) {
			$coupon_ids = array_keys($records);
		}
		foreach ($coupons as &$row) {
			$row['logo'] = tomedia($row['logo']);
			$row['percent'] = $row['dosage'] / $row['amount'] * 100;
			$row['get_status'] = 1;
			if ($row['grant_type'] == 1) {
				if(in_array($row['id'], $coupon_ids)) {
					$row['get_status'] = 0;
				}
			} else {
				$record = pdo_fetch('select granttime from ' . tablename('tiny_wmall_plus_activity_coupon_record') . ' where uniacid = :uniacid and uid = :uid and couponid = :couponid order by id desc', array(':uniacid' => $_W['uniacid'], ':uid' => $_W['member']['uid'], ':couponid' => $row['id']));
				if($record['granttime'] > strtotime(date('Y-m-d'))) {
					$row['get_status'] = 0;
				}
			}
		}
		$min = min(array_keys($coupons));
	}
	if($_W['isajax']) {
		$coupons = array_values($coupons);
		$respon = array('error' => 0, 'message' => $coupons, 'min' => $min);
		message($respon, '', 'ajax');
	}
	include $this->template('coupon-channel');
}

if($op == 'get') {
	mload()->model('coupon');
	$sid = intval($_GPC['sid']);
	$id = intval($_GPC['id']);
	$result = coupon_grant($sid, $id, $_W['member']['uid']);
	if(is_error($result)) {
		message($result, '', 'ajax');
	}
	message(error(0, '领取优惠券成'), '', 'ajax');
}



