<?php
/**
 * 外送系统
 * @author 灯火阑珊
 * @QQ 2471240272
 * @url http://bbs.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$do = 'errander';
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'index';
mload()->model('errander');
$this->checkAuth();
$config = $_W['we7_wmall']['config']['errander'];

if($op == 'index') {
	$categorys = pdo_fetchall('select * from ' . tablename('tiny_wmall_errander_category') . ' where uniacid = :uniacid and status = 1 order by displayorder desc', array(':uniacid' => $_W['uniacid']));
	if(empty($config['map']['location_x']) || empty($config['map']['location_y'])) {
		$config['map'] = array(
			'location_x' => '39.90923',
			'location_y' => '116.397428',
		);
	}
	$orders = pdo_fetchall('select a.*,b.title,b.thumb from ' . tablename('tiny_wmall_errander_order') . ' as a left join ' . tablename('tiny_wmall_errander_category') . ' as b on a.order_cid = b.id where a.uniacid = :uniacid order by a.id desc limit 5', array(':uniacid' => $_W['uniacid']));
	$delivery_num = pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_store_deliveryer') . ' where uniacid = :uniacid and sid = 0', array(':uniacid' => $_W['uniacid']));
	include $this->template('errander-index');
}

if($op == 'deliveryer') {
	mload()->model('deliveryer');
	$deliveryer = deliveryer_fetchall();
	message(error(0, $deliveryer), '', 'ajax');
}

if($op == 'submit') {
	mload()->model('member');
	mload()->func('tpl.app');
	$id = intval($_GPC['id']);
	$category = errander_category_fetch($id);
	if(empty($category)) {
		$this->imessage('跑腿类型不存在', referer(), 'error');
	}
	$types = errander_types();
	$title = "{$types[$category['type']]['text']} - {$category['title']}";

	$rule = array(
		'start_fee' => $category['start_fee'],
		'start_km' => $category['start_km'],
		'pre_km_fee' => $category['pre_km_fee'],
		'tip_min' => $category['tip_min'],
		'tip_max' => $category['tip_max']
	);
	$filter = array('serve_radius' => $config['serve_radius'], 'location_x' => $config['map']['location_x'], 'location_y' => $config['map']['location_y']);
	$serves = member_fetchall_serve_address($filter);
	$addresses = member_fetchall_address($filter);

	if(!empty($_COOKIE['errander_order'])) {
		$errander_order = json_decode($_COOKIE['errander_order'], true);
		setcookie('errander_order', 0, -1000);
	}
	$start_address_id = intval($_GPC['start_address_id']) ? intval($_GPC['start_address_id']) : $errander_order['start_address_id'];
	if($start_address_id > 0) {
		$start_address = member_fetch_address($start_address_id);
	}
	$end_address_id = intval($_GPC['end_address_id']) ? intval($_GPC['end_address_id']) : $errander_order['end_address_id'];
	if($end_address_id > 0) {
		$end_address = member_fetch_address($end_address_id);
	}
	$payment = $_W['we7_wmall']['config']['payment'];
	$pay_types = order_pay_types();
	$price_select = array(
		array('id' => 1, 'title' => '100元以下'),
		array('id' => 2, 'title' => '100元-200元'),
		array('id' => 3, 'title' => '200元-300元'),
		array('id' => 4, 'title' => '300元-400元'),
		array('id' => 5, 'title' => '400元-500元'),
	);
	include $this->template("errander-submit-{$category['type']}");
}

if($op == 'post') {
	mload()->model('member');
	$id = intval($_GPC['id']);
	$category = errander_category_fetch($id);
	if(empty($category)) {
		message(error(-1, '跑腿类型不存在'), '', 'ajax');
	}

	$goods_name = trim($_GPC['goods_name']);
	if(empty($goods_name)) {
		message(error(-1, '商品名称不能为空'), '', 'ajax');
	}

	$start_address_id = intval($_GPC['start_address_id']);
	if($category['type'] == 'buy') {
		$start_address = member_fetch_serve_address($start_address_id);
	} else {
		$start_address = member_fetch_address($start_address_id);
		if(empty($start_address)) {
			message(error(-1, '取货地址不存在'), '', 'ajax');
		}
	}

	$end_address_id = intval($_GPC['end_address_id']);
	$end_address = member_fetch_address($end_address_id);
	if(empty($end_address)) {
		message(error(-1, '收货地址不存在'), '', 'ajax');
	}

	$delivery_fee = $category['start_fee'];
	$distance = distanceBetween($start_address['location_y'], $start_address['location_x'], $end_address['location_y'], $end_address['location_x']);
	$distance = round($distance / 1000, 2);
	if($distance > $category['start_km']) {
		$delivery_fee += round($category['pre_km_fee'] * ($distance - $category['start_km']), 2);
	}

	$delivery_tips = trim($_GPC['delivery_tips']);
	if($delivery_tips < $category['tip_min'] || ($category['tip_max'] > 0 && $delivery_tips > $category['tip_max'])) {
		message(error(-1, "红包金额只能在{$category['tip_min']}元~{$category['tip_max']}元之间"), '', 'ajax');
	}

	$pay_type = trim($_GPC['pay_type']);
	$payment = $_W['we7_wmall']['config']['payment'];
	if(!in_array($pay_type, array_keys($payment))) {
		message(error(-1, '支付方式有误'), '', 'ajax');
	}
	$order = array(
		'uniacid' => $_W['uniacid'],
		'acid' => $_W['acid'],
		'uid' => $_W['member']['uid'],
		'openid' => $_W['openid'],
		'code' => random(4, true),
		'order_sn' => date('Ymd') . random(6, true),
		'order_type' => $category['type'],
		'order_cid' => $category['id'],
		'buy_username' => $start_address['realname'],
		'buy_mobile' => $start_address['mobile'],
		'buy_sex' => $start_address['sex'],
		'buy_address' => $start_address['address'] . $start_address['number'],
		'buy_location_x' => $start_address['location_x'],
		'buy_location_y' => $start_address['location_y'],
		'accept_mobile' => $end_address['mobile'],
		'accept_username' => $end_address['realname'],
		'accept_sex' => $end_address['sex'],
		'accept_address' => $end_address['address'] . $end_address['number'],
		'accept_location_x' => $end_address['location_x'],
		'accept_location_y' => $end_address['location_y'],
		'distance' => $distance,
		'delivery_time' => '立即送达',
		'goods_name' => $goods_name,
		'goods_price' => ($category['type'] == 'buy' ? trim($_GPC['goods_price']) : trim($_GPC['goods_price_cn'])),
		'goods_weight' => trim($_GPC['goods_weight']),
		'note' => trim($_GPC['note']),
		'delivery_fee' => $delivery_fee,
		'delivery_tips' => $delivery_tips,
		'total_fee' => $delivery_fee + $delivery_tips,
		'discount_fee' => 0,
		'final_fee' => $delivery_fee + $delivery_tips,
		'deliveryer_fee' => 0,
		'is_anonymous' => intval($_GPC['is_anonymous']),
		'is_pay' => 0,
		'pay_type' => $pay_type,
		'note' => trim($_GPC['note']),
		'status' => 1,
		'delivery_status' => 1,
		'addtime' => TIMESTAMP
	);
	if($config['deliverer_fee_type'] == 1) {
		$order['deliveryer_fee'] = $config['deliveryer_fee'];
	} else {
		$order['deliveryer_fee'] = round($order['delivery_fee'] * $config['deliveryer_fee'] / 100, 2);
	}
	$name = $order['accept_username'];
	if($order['is_anonymous'] == 1) {
		if(!empty($config['anonymous'])) {
			$index = array_rand($config['anonymous']);
			$name = $config['anonymous'][$index];
		} else {
			$name = cutstr($order['accept_username'], 1) . '**';
		}
	}
	$order['anonymous_username'] = $name;

	pdo_insert('tiny_wmall_errander_order', $order);
	$id = pdo_insertid();
	errander_order_insert_status_log($id, 'place_order');
	message(error(0, $id), '', 'ajax');
}

if($op == 'cart') {
	setcookie('errander_order', 0, -1000);
	$errander_order = array(
		'goods_name' => trim($_GPC['goods_name']),
		'goods_weight' => trim($_GPC['goods_weight']),
		'goods_price' => trim($_GPC['goods_price']),
		'note' => trim($_GPC['note']),
		'delivery_tips' => trim($_GPC['delivery_tips']),
		'is_anonymous' => trim($_GPC['is_anonymous']),
		'start_address_id' => trim($_GPC['start_address_id']),
		'end_address_id' => trim($_GPC['end_address_id']),
	);
	setcookie('errander_order', json_encode($errander_order), 600 + time());
	exit();
}



