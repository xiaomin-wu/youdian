<?php
/**
 * 外送系统
 * @author 新睿社区
 * @QQ 10373458 * @url http://www.010xr.com/
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
$store = store_fetch($sid, array('title', 'location_x', 'location_y', 'payment', 'invoice_status', 'delivery_type', 'delivery_mode', 'delivery_price', 'delivery_fee_mode', 'delivery_time', 'delivery_free_price', 'pack_price', 'delivery_within_days', 'delivery_reserve_days', 'order_note'));

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
	$address = member_fetch_available_address($sid);
	$address_id = $address['id'];

	//商家配送方式
	$delivery_time = store_delivery_times($sid);
	$time_flag = 0;
	$predict_index = 0;
	$predict_timestamp = TIMESTAMP + 60 * $store['delivery_time'];
	if(!$delivery_time['reserve']) {
		$data = array_order(TIMESTAMP + 60 * $store['delivery_time'], $delivery_time['timestamp']);
		if(!empty($data)) {
			$time_flag = 1;
			$predict_index = array_search($data, $delivery_time['timestamp']);
			$predict_day = $delivery_time['days'][0];
			$predict_time = "{$delivery_time['times'][$predict_index]['start']}~{$delivery_time['times'][$predict_index]['end']}";
			$text_time = "尽快送达";
			$predict_extra_price = $delivery_time['times'][$predict_index]['fee'];
		} else {
			$predict_day = $delivery_time['days'][1];
			$predict_times = array_shift($delivery_time['times']);
			$predict_time = "{$predict_times['start']}~{$predict_times['end']}";
			$text_time = "{$predict_day} {$predict_time}";
		}
		$predict_delivery_price = $store['delivery_price'] + $delivery_time['times'][$predict_index]['fee'];
		if($store['delivery_fee_mode'] == 1) {
			$predict_delivery_price = "{$predict_delivery_price}元配送费";
		} else {
			$predict_delivery_price = "配送费{$predict_delivery_price}元起";
		}
	} else {
		$predict_day = $delivery_time['days'][0];
		$predict_time = $delivery_time['times'][0];
		$text_time = "{$predict_day} {$predict_time}";
	}

	//计算配送费
	$delivery_price = 0;
	if(($store['delivery_mode'] == 1 && $store['delivery_free_price'] > 0 && $cart['price'] >= $store['delivery_free_price']) || $store['delivery_type'] == 2) {
		$delivery_price = 0;
	} else {
		if($store['delivery_fee_mode'] == 1) {
			$delivery_price_basic = $store['delivery_price'];
			$delivery_price = $store['delivery_price'] + $delivery_time['times'][$predict_index]['fee'];
		} else {
			$delivery_price = $delivery_price_basic = $store['delivery_price_extra']['start_fee'];
			$distance = $address['distance'];
			if(!empty($address) && $distance > 0) {
				if($distance > $store['delivery_price_extra']['start_km']) {
					$delivery_price += ($distance - $store['delivery_price_extra']['start_km']) * $store['delivery_price_extra']['pre_km_fee'];
				}
				$delivery_price = $delivery_price_basic = round($delivery_price, 2);
				$delivery_price += $delivery_time['times'][$predict_index]['fee'];
			}
			$distance = round($distance, 2);
		}
	}

	//代金券
	$coupon_text = '无可用代金券';
	$coupons = order_coupon_available($sid, $cart['price']);
	if(!empty($coupons)) {
		$coupon_text = count($coupons) . '张可用代金券';
	}
	$recordid = intval($_GPC['recordid']);
	$activityed = order_count_activity($sid, $cart, $recordid, $delivery_price);
	if(!empty($activityed['list']['token'])) {
		$coupon_text = "{$activityed['list']['token']['value']}元券";
		$conpon = $activityed['list']['token']['coupon'];
	}
	$delivery_activity_price = 0;
	$activity_price = $activityed['total'];
	if(!empty($activityed) && !empty($activityed['list']['delivery'])) {
		$delivery_activity_price = $activityed['list']['delivery']['value'];
	}
	$waitprice = $cart['price'] + $cart['box_price'] + $delivery_price + $store['pack_price'] - $activityed['total'];
	$waitprice = ($waitprice > 0) ? $waitprice : 0;
}

if($op == 'submit') {
	if(!$_W['isajax']) {
		message(error(-1, '非法访问'), '', 'ajax');
	}
	$cart = order_check_member_cart($sid);
	if(is_error($cart)) {
		message($cart, '', 'ajax');
	}
	if($_GPC['order_type'] == 1) {
		$address = member_fetch_address($_GPC['address_id']);
		if(empty($address)) {
			message(error(-1, '收货地址信息错误'), '', 'ajax');
		}
		$delivery_time = store_delivery_times($sid);
		//计算配送费
		$predict_index = intval($_GPC['delivery_index']);
		$delivery_price = 0;
		if(($store['delivery_mode'] == 1 && $store['delivery_free_price'] > 0 && $cart['price'] >= $store['delivery_free_price']) || $store['delivery_type'] == 2) {
			$delivery_price = 0;
		} else {
			if($store['delivery_fee_mode'] == 1) {
				$delivery_price = $store['delivery_price'] + $delivery_time['times'][$predict_index]['fee'];
			} else {
				$distance = distanceBetween($address['location_y'], $address['location_x'], $store['location_y'], $store['location_x']);
				$distance = $distance / 1000;
				$delivery_price = $store['delivery_price_extra']['start_fee'];
				if($distance > 0) {
					if($distance > $store['delivery_price_extra']['start_km']) {
						$delivery_price += ($distance - $store['delivery_price_extra']['start_km']) * $store['delivery_price_extra']['pre_km_fee'];
					}
					$delivery_price = round($delivery_price, 2);
					$delivery_price += $delivery_time['times'][$predict_index]['fee'];
				}
			}
		}
	} elseif($_GPC['order_type'] == 2) {
		$address = array(
			'realname' => trim($_GPC['username']),
			'mobile' => trim($_GPC['mobile'])
		);
	}

	$recordid = intval($_GPC['record_id']);
	$activityed = order_count_activity($sid, $cart, $recordid, $delivery_price);

	$order_type = intval($_GPC['order_type']) ? intval($_GPC['order_type']) : 1;
	if($order_type == 2 && !empty($activityed['list']['delivery'])) {
		$activityed['total'] -= $activityed['list']['delivery']['value'];
		$activityed['activity'] = $activityed['total'];
		unset($activityed['list']['delivery']);
	}
	$total_fee = $cart['price'] + $cart['box_price'] + $store['pack_price'] + $delivery_price;
	$order = array(
		'uniacid' => $_W['uniacid'],
		'acid' => $_W['acid'],
		'sid' => $sid,
		'uid' => $_W['member']['uid'],
		'ordersn' => date('YmdHis') . random(6, true),
		'serial_sn' => store_order_serial_sn($sid),
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
		'box_price' => $cart['box_price'],
		'price' => $cart['price'],
		'total_fee' => $total_fee,
		'discount_fee' => $activityed['total'],
		'final_fee' => $total_fee - $activityed['total'],
		'vip_free_delivery_fee' => !empty($activityed['list']['delivery']) ? 1 : 0,
		'delivery_type' => $store['delivery_mode'],
		'status' => 1,
		'is_comment' => 0,
		'invoice' => trim($_GPC['invoice']),
		'addtime' => TIMESTAMP,
		'data' => $cart['original_data'],
		'note' => trim($_GPC['note']),
	);
	if($order['final_fee'] < 0) {
		$order['final_fee'] = 0;
	}
	pdo_insert('tiny_wmall_plus_order', $order);
	$order_id = pdo_insertid();

	order_update_bill($order_id);
	order_insert_discount($order_id, $sid, $activityed['list']);
	order_insert_status_log($order_id, 'place_order');
	order_update_goods_info($order_id, $sid);
	order_del_member_cart($sid);

	//插入会员下单统计数据
	$_W['member']['realname'] = $address['realname'];
	$_W['member']['mobile'] = $address['mobile'];
	order_stat_member($sid);
	message(error(0, $order_id), '', 'ajax');
}
include $this->template('submit');
