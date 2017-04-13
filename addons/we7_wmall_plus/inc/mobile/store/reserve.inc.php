<?php
/**
 * 外送系统
 * @author 新睿社区
 * @QQ 10373458 * @url http://www.010xr.com/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$do = 'goods';
mload()->model('goods');
mload()->model('table');
$this->checkAuth();
$sid = intval($_GPC['sid']);
$store = store_fetch($sid);
if(empty($store)) {
	message('门店不存在或已经删除', referer(), 'error');
}

if($store['is_reserve'] != 1) {
	$this->imessage('预定功能暂未开启', $this->createMobileUrl('store', array('sid' => $sid)), 'info');
}

$_share = array(
	'title' => $store['title'],
	'desc' => $store['content'],
	'imgUrl' => tomedia($store['logo'])
);
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'index';

if($op == 'index') {
	$title = '预定';
	$categorys = pdo_fetchall('select * from ' . tablename('tiny_wmall_plus_tables_category') . ' where uniacid = :uniacid and sid = :sid', array(':uniacid' => $_W['uniacid'], ':sid' => $sid));
	$data = pdo_fetchall('select * from ' . tablename('tiny_wmall_plus_reserve') . ' where uniacid = :uniacid and sid = :sid order by id asc', array(':uniacid' => $_W['uniacid'], ':sid' => $sid));
	if(!empty($data)) {
		$reserves = array();
		foreach($data as $da) {
			$reserves[$da['table_cid']][] = $da['time'];
		}
	}
	include $this->template('reserve');
}

if($op == 'post') {
	$date = trim($_GPC['date']);
	$time = trim($_GPC['time']);
	$cid = intval($_GPC['cid']);
	$category = pdo_get('tiny_wmall_plus_tables_category', array('uniacid' => $_W['uniacid'], 'id' => $cid, 'sid' => $sid));
	if(empty($category)) {
		$this->imessage('桌台类型错误', $this->createMobileUrl('store', array('sid' => $sid)), 'info');
	}
	$reserve_type = trim($_GPC['reserve_type']);
	//只订座
	$cart['price'] = $category['reservation_price'];
	$cart['num'] = 0;
	if($_GPC['from'] == 'goods') {
		$cart = order_insert_member_cart($sid);
	} else {
		if($reserve_type == 'order') {
			$cart = order_fetch_member_cart($sid);
		}
	}
	if(empty($cart)) {
		$this->imessage('商品信息错误', $this->createMobileUrl('store', array('sid' => $sid)), 'info');
	}

	$pay_types = order_pay_types();
	//支付方式
	if(empty($store['payment'])) {
		message('店铺没有设置有效的支付方式', referer(), 'error');
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
	$waitprice = $cart['price'] - $activityed['total'];
	$waitprice = ($waitprice > 0) ? $waitprice : 0;
	include $this->template('reserve-submit');
}

if($op == 'goods') {
	$title = '商品列表';
	$date = trim($_GPC['date']);
	$time = trim($_GPC['time']);
	$cid = intval($_GPC['cid']);
	$category = pdo_get('tiny_wmall_plus_tables_category', array('uniacid' => $_W['uniacid'], 'id' => $cid, 'sid' => $sid));
	if(empty($category)) {
		$this->imessage('桌台类型错误', $this->createMobileUrl('store', array('sid' => $sid)), 'info');
	}
	$activity = store_fetch_activity($sid);
	$is_favorite = pdo_get('tiny_wmall_plus_store_favorite', array('uniacid' => $_W['uniacid'], 'uid' => $_W['member']['uid'], 'sid' => $sid));

	$result = goods_avaliable_fetchall($sid, 0, true);
	$categorys = $result['category'];
	$cate_goods = $result['cate_goods'];
	$goods = $result['goods'];
	$bargains = $result['bargains'];

	//获取优惠券
	mload()->model('coupon');
	$tokens = coupon_fetchall_user_available($sid, $_W['member']['uid']);
	if(!empty($tokens)) {
		$token_nums = count($tokens);
		$token = $tokens[0];
	}

	$cart = order_fetch_member_cart($sid);
	include $this->template('reserve-goods');
}

if($op == 'submit') {
	if(!$_W['isajax']) {
		message(error(-1, '非法访问'), '', 'ajax');
	}
	$date = trim($_GPC['date']);
	$time = trim($_GPC['time']);
	$cid = intval($_GPC['cid']);
	$category = pdo_get('tiny_wmall_plus_tables_category', array('uniacid' => $_W['uniacid'], 'id' => $cid, 'sid' => $sid));
	if(empty($category)) {
		message(error(-1, '桌台类型错误'), '', 'ajax');
	}

	$reserve_type = trim($_GPC['reserve_type']);
	if($reserve_type == 'order') {
		$cart = order_fetch_member_cart($sid);
		if(empty($cart)) {
			header('location:' . $this->createMobileUrl('reserve', array('sid' => $sid, 'cid' => $cid, 'date' => $date, 'time' => $time)));
			die;
		}
	} else {
		$cart = array(
			'num' => 0,
			'price' => $category['reservation_price']
		);
	}
	$recordid = intval($_GPC['record_id']);
	$activityed = order_count_activity($sid, $cart, $recordid);
	$order = array(
		'uniacid' => $_W['uniacid'],
		'acid' => $_W['acid'],
		'sid' => $sid,
		'uid' => $_W['member']['uid'],
		'ordersn' => date('YmdHis') . random(6, true),
		'serial_sn' => store_order_serial_sn($sid),
		'code' => random(4, true),
		'groupid' => $cart['groupid'],
		'order_type' => 4, //预定单
		'openid' => $_W['openid'],
		'mobile' => trim($_GPC['mobile']),
		'username' => trim($_GPC['username']),
		'person_num' => 0,
		'table_cid' => $cid,
		'reserve_type' => $reserve_type,
		'reserve_time' => "{$date} {$time}",
		'sex' => '',
		'address' => '',
		'location_x' => '',
		'location_y' => '',
		'delivery_day' => '',
		'delivery_time' => '',
		'delivery_fee' => 0,
		'pack_fee' => 0,
		'pay_type' => trim($_GPC['pay_type']),
		'num' => $cart['num'],
		'price' => $cart['price'],
		'total_fee' => $cart['price'],
		'discount_fee' => $activityed['total'],
		'final_fee' => $cart['price'] - $activityed['total'],
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
	pdo_insert('tiny_wmall_plus_order', $order);
	$id = pdo_insertid();

	order_update_bill($id);
	order_insert_discount($id, $sid, $activityed['list']);
	order_insert_status_log($id, 'place_order');
	if($reserve_type == 'order') {
		order_update_goods_info($id, $sid);
		order_del_member_cart($sid);
	}
	//插入会员下单统计数据
	$_W['member']['realname'] = $order['username'];
	$_W['member']['mobile'] = $order['mobile'];
	order_stat_member($sid);
	message(error(0, $id), '', 'ajax');
}


