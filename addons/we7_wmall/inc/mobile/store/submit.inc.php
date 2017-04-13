<?php
/**
 * 外送系统
 * @author 灯火阑珊
 * @QQ 2471240272
 * @url http://bbs.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$do = 'goods';
mload()->model('store');
mload()->model('order');
mload()->model('member');
$this->checkAuth();
$title = '提交订单';
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'goods';
$sid = intval($_GPC['sid']);
$store = store_fetch($sid, array('title', 'payment', 'invoice_status', 'delivery_type', 'delivery_mode', 'delivery_price', 'delivery_time', 'delivery_free_price', 'pack_price', 'delivery_within_days', 'delivery_reserve_days', 'order_note'));
if(empty($store)) {
	message('门店不存在', '', 'error');
}

if($op == 'goods') {
	$cart = order_insert_member_cart($sid);
	if(is_error($cart)) {
		header('location:' . $this->createMobileUrl('goods', array('sid' => $sid)));
		die;
	}
	header('location:' . $this->createMobileUrl('submit', array('sid' => $sid, 'op' => 'index')));
	die;
}

if($op == 'index') {
	$address = member_fetch_available_address($sid);
	$address_id = $address['id'];
	$cart = order_fetch_member_cart($sid);
	if(empty($cart)) {
		header('location:' . $this->createMobileUrl('goods', array('sid' => $sid)));
		die;
	}
	$pay_types = order_pay_types();
	//支付方式
	if(empty($store['payment'])) {
		message('店铺没有设置有效的支付方式', referer(), 'error');
	}
	//商家配送方式
	$account = store_account($sid);
	$delivery_time = store_delivery_times($sid);
	$time_flag = 0;
	if(!$delivery_time['reserve']) {
		$data = array_order(TIMESTAMP + 60 * $store['delivery_time'], $delivery_time['timestamp']);
		if(!empty($data)) {
			$time_flag = 1;
			$index = array_search($data, $delivery_time['timestamp']);
			$predict_day = $delivery_time['days'][0];
			$predict_time = "{$delivery_time['times'][$index]['start']}~{$delivery_time['times'][$index]['end']}";
			$text_time = "尽快送达";
		} else {
			$predict_day = $delivery_time['days'][1];
			$predict_time = "{$delivery_time['times'][0]['start']}~{$delivery_time['times'][0]['end']}";
			$text_time = "{$predict_day} {$predict_time}";
		}
	} else {
		$predict_day = $delivery_time['days'][0];
		$predict_time = $delivery_time['times'][0];
		$text_time = "{$predict_day} {$predict_time}";
	}

	//代金券
	$coupon_text = '无可用代金券';
	$coupons = order_coupon_available($sid, $cart['price']);
	if(!empty($coupons)) {
		$coupon_text = count($coupons) . '张可用代金券';
	}
	$recordid = intval($_GPC['recordid']);
	$activityed = order_count_activity($sid, $cart, $recordid);
	if(!empty($activityed['list']['token'])) {
		$coupon_text = "{$activityed['list']['token']['value']}元券";
		$conpon = $activityed['list']['token']['coupon'];
	}
	$delivery_activity_price = 0;
	$activity_price = $activityed['total'];
	if(!empty($activityed) && !empty($activityed['list']['delivery'])) {
		$delivery_activity_price = $activityed['list']['delivery']['value'];
	}

	//配送费
	$delivery_price = $store['delivery_price'];
	if(($store['delivery_mode'] == 1 && $store['delivery_free_price'] > 0 && $cart['price'] >= $store['delivery_free_price']) || $store['delivery_type'] == 2) {
		$delivery_price = 0;
	}
	$waitprice = $cart['price'] + $delivery_price + $store['pack_price'] - $activityed['total'];
	$waitprice = ($waitprice > 0) ? $waitprice : 0;
}

if($op == 'submit') {
	if(!$_W['isajax']) {
		message(error(-1, '非法访问'), '', 'ajax');
	}
	$cart = order_fetch_member_cart($sid);
	if(empty($cart)) {
		header('location:' . $this->createMobileUrl('goods', array('sid' => $sid)));
		die;
	}
	if($_GPC['order_type'] == 1) {
		$address = member_fetch_address($_GPC['address_id']);
		if(empty($address)) {
			message(error(-1, '收货地址信息错误'), '', 'ajax');
		}
	} elseif($_GPC['order_type'] == 2) {
		$address = array(
			'realname' => trim($_GPC['username']),
			'mobile' => trim($_GPC['mobile'])
		);
	}
	$recordid = intval($_GPC['record_id']);
	$activityed = order_count_activity($sid, $cart, $recordid);

	$order_type = intval($_GPC['order_type']) ? intval($_GPC['order_type']) : 1;
	if($order_type == 2 && !empty($activityed['list']['delivery'])) {
		$activityed['total'] -= $activityed['list']['delivery']['value'];
		$activityed['activity'] = $activityed['total'];
		unset($activityed['list']['delivery']);
	}

	//配送费
	$delivery_price = $store['delivery_price'];
	if(($store['delivery_mode'] == 1 && $store['delivery_free_price'] > 0 && $cart['price'] >= $store['delivery_free_price']) || $store['delivery_type'] == 2 || $order_type == 2) {
		$delivery_price = 0;
	}

	$order = array(
		'uniacid' => $_W['uniacid'],
		'acid' => $_W['acid'],
		'sid' => $sid,
		'uid' => $_W['member']['uid'],
		'ordersn' => date('Ymd') . random(6, true),
		'code' => random(4, true),
		'groupid' => $cart['groupid'],
		'order_type' => $order_type,
		'openid' => $_W['openid'],
		'mobile' => $address['mobile'],
		'username' => $address['realname'],
		'sex' => $address['sex'],
		'address' => $address['address'] . $address['number'],
		'location_x' => $address['location_x'],
		'location_y' => $address['location_y'],
		'delivery_day' => trim($_GPC['delivery_day']) ? (date('Y') .'-'. trim($_GPC['delivery_day'])) : date('Y-m-d'),
		'delivery_time' => trim($_GPC['delivery_time']) ? trim($_GPC['delivery_time']) : '尽快送出',
		'delivery_fee' => $delivery_price,
		'pack_fee' => $store['pack_price'],
		'pay_type' => trim($_GPC['pay_type']),
		'num' => $cart['num'],
		'price' => $cart['price'],
		'total_fee' => $cart['price'] + $delivery_price + $store['pack_price'],
		'discount_fee' => $activityed['total'],
		'final_fee' => $cart['price'] + $delivery_price + $store['pack_price'] - $activityed['total'],
		'vip_free_delivery_fee' => !empty($activityed['list']['delivery']) ? 1 : 0,
		'status' => 1,
		'is_comment' => 0,
		'invoice' => trim($_GPC['invoice']),
		'addtime' => TIMESTAMP,
		'data' => iserializer($cart['data']),
		'note' => trim($_GPC['note'])
	);
	if($order['final_fee'] < 0) {
		$order['final_fee'] = 0;
	}
	pdo_insert('tiny_wmall_order', $order);
	$id = pdo_insertid();
	order_insert_current_log($id, $sid, $order['final_fee'], '', '');
	order_insert_discount($id, $sid, $activityed['list']);
	order_insert_status_log($id, $sid, 'place_order');
	order_update_goods_info($id, $sid);
	order_del_member_cart($sid);
	//插入会员下单统计数据
	$_W['member']['realname'] = $address['realname'];
	$_W['member']['mobile'] = $address['mobile'];
	order_stat_member($sid);
	message(error(0, $id), '', 'ajax');
}
include $this->template('submit');
