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
mload()->model('goods');
mload()->model('table');
$this->checkAuth();
$sid = intval($_GPC['sid']);
$store = store_fetch($sid);
if(empty($store)) {
	message('门店不存在或已经删除', referer(), 'error');
}

if($store['is_meal'] != 1) {
	$this->imessage('店内点餐暂未开启', $this->createMobileUrl('store', array('sid' => $sid)), 'info');
}

$table_id = intval($_GPC['table_id']);
$table = table_fetch($table_id);
if(empty($table)) {
	$this->imessage('桌号不存在', $this->createMobileUrl('store', array('sid' => $sid)), 'error');
}

$_share = array(
	'title' => $store['title'],
	'desc' => $store['content'],
	'imgUrl' => tomedia($store['logo'])
);
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'index';

if($op == 'index') {
	$title = '商品列表';
	$activity = store_fetch_activity($sid);
	$is_favorite = pdo_get('tiny_wmall_store_favorite', array('uniacid' => $_W['uniacid'], 'uid' => $_W['member']['uid'], 'sid' => $sid));
	$categorys = store_fetchall_goods_category($sid, 1);
	$dish = pdo_fetchall('SELECT * FROM ' . tablename('tiny_wmall_goods') . ' WHERE uniacid = :aid AND sid = :sid AND status = 1 ORDER BY displayorder DESC, id ASC', array(':aid' => $_W['uniacid'], ':sid' => $sid));
	$cate_dish = array();
	foreach($dish as &$di) {
		$di['unitname_cn'] = !empty($di['unitname']) ? "/{$di['unitname']}" : '';
		if($di['is_options']) {
			$di['options'] = pdo_getall('tiny_wmall_goods_options', array('uniacid' => $_W['uniacid'], 'goods_id' => $di['id']));
		}
		$cate_dish[$di['cid']][] = $di;
	}
	//获取优惠券
	mload()->model('coupon');
	$tokens = coupon_fetchall_user_available($sid, $_W['member']['uid']);
	if(!empty($tokens)) {
		$token_nums = count($tokens);
		$token = $tokens[0];
	}

	$cart = order_fetch_member_cart($sid);
	include $this->template('table');
}

if($op == 'post') {
	$cart = order_insert_member_cart($sid);
	if(is_error($cart)) {
		$cart = order_fetch_member_cart($sid);
		if(empty($cart)) {
			header('location:' . $this->createMobileUrl('table', array('sid' => $sid, 'table_id' => $table_id)));
			die;
		}
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
	//计算服务费
	$serve_fee = 0;
	if($store['serve_fee']['type'] > 0 && $store['serve_fee']['fee'] > 0) {
		$serve_fee = $store['serve_fee']['fee'];
		if($store['serve_fee']['type'] == 2) {
			$serve_fee = round($store['serve_fee']['fee'] * $cart['price'] / 100, 2);
		}
	}
	$waitprice = $cart['price'] + $serve_fee - $activityed['total'];
	$waitprice = ($waitprice > 0) ? $waitprice : 0;
	include $this->template('table-submit');
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
	$recordid = intval($_GPC['record_id']);
	$activityed = order_count_activity($sid, $cart, $recordid);
	//计算服务费
	$serve_fee = 0;
	if($store['serve_fee']['type'] > 0 && $store['serve_fee']['fee'] > 0) {
		$serve_fee = $store['serve_fee']['fee'];
		if($store['serve_fee']['type'] == 2) {
			$serve_fee = round($store['serve_fee']['fee'] * $cart['price'] / 100, 2);
		}
	}
	$order = array(
		'uniacid' => $_W['uniacid'],
		'acid' => $_W['acid'],
		'sid' => $sid,
		'uid' => $_W['member']['uid'],
		'ordersn' => date('Ymd') . random(6, true),
		'code' => random(4, true),
		'groupid' => $cart['groupid'],
		'order_type' => 3, //店内单
		'openid' => $_W['openid'],
		'mobile' => '',
		'username' => trim($_GPC['username']),
		'person_num' => intval($_GPC['person_num']),
		'table_id' => intval($_GPC['table_id']),
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
		'serve_fee' => $serve_fee,
		'total_fee' => $cart['price'] + $serve_fee,
		'discount_fee' => $activityed['total'],
		'final_fee' => $cart['price'] + $serve_fee - $activityed['total'],
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
	//更新对应的桌号为已下单
	table_order_update($table_id, $id,  3);
	order_insert_current_log($id, $sid, $order['final_fee'], '', '');
	order_insert_discount($id, $sid, $activityed['list']);
	order_insert_status_log($id, $sid, 'place_order');
	order_update_goods_info($id, $sid);
	order_del_member_cart($sid);
	//插入会员下单统计数据
	$_W['member']['realname'] = $address['realname'];
	$_W['member']['mobile'] = $address['mobile'];
	order_stat_member($sid);
	order_clerk_notice($sid, $id, 'store_order_place');
	message(error(0, $id), '', 'ajax');
}


