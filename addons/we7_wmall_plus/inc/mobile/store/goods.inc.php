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
$this->checkAuth();
$sid = intval($_GPC['sid']);
$store = store_fetch($sid);
if(empty($store)) {
	message('门店不存在或已经删除', referer(), 'error');
}
//更新天天特价状态
mload()->model('bargain');
bargain_update_status();

$_share = array(
	'title' => $store['title'],
	'desc' => $store['content'],
	'imgUrl' => tomedia($store['logo'])
);
$op = trim($_GPC['op']) ? trim($_GPC['op']) : $store['template'];

if($_GPC['from'] == 'search') {
	pdo_query("update " . tablename('tiny_wmall_plus_store') . " set click = click + 1 where uniacid = :uniacid and id = :id",  array(':uniacid' => $_W['uniacid'], ':id' => $sid));
}

if($op == 'index') {
	$title = "{$store['title']}-商品列表";
	$activity = store_fetch_activity($sid);
	$is_favorite = pdo_get('tiny_wmall_plus_store_favorite', array('uniacid' => $_W['uniacid'], 'uid' => $_W['member']['uid'], 'sid' => $sid));

	$result = goods_avaliable_fetchall($sid);
	$categorys = $result['category'];
	$cate_goods = $result['cate_goods'];
	$goods = $result['goods'];
	$bargains = $result['bargains'];

	$categorys_limit_status = 0;
	$categorys_limit = array();
	foreach($categorys as $row) {
		if($row['min_fee'] > 0) {
			$categorys_limit_status = 1;
			$row['fee'] = 0;
			$categorys_limit[$row['id']] = $row;
		}
	}
	$categorys_index = array_keys($categorys_limit);

	//获取优惠券
	mload()->model('coupon');
	$tokens = coupon_fetchall_user_available($sid, $_W['member']['uid']);
	if(!empty($tokens)) {
		$token_nums = count($tokens);
		$token = $tokens[0];
	}

	if(!$_GPC['f']) {
		//再来一单的处理逻辑
		$cart = order_fetch_member_cart($sid);
	} else {
		$cart = order_place_again($sid, $_GPC['id']);
		if(empty($cart)) {
			$cart = order_fetch_member_cart($sid);
		}
	}
	include $this->template('goods');
}

if($op == 'market') {
	$title = '商品列表';
	$activity = store_fetch_activity($sid);
	$is_favorite = pdo_get('tiny_wmall_plus_store_favorite', array('uniacid' => $_W['uniacid'], 'uid' => $_W['member']['uid'], 'sid' => $sid));

	$result = goods_avaliable_fetchall($sid);
	$categorys = $result['category'];
	$cate_goods = $result['cate_goods'];
	$goods = $result['goods'];
	$bargains = $result['bargains'];

	$categorys_limit_status = 0;
	$categorys_limit = array();
	foreach($categorys as $row) {
		if($row['min_fee'] > 0) {
			$categorys_limit_status = 1;
			$row['fee'] = 0;
			$categorys_limit[$row['id']] = $row;
		}
	}
	$categorys_index = array_keys($categorys_limit);

	//获取优惠券
	mload()->model('coupon');
	$tokens = coupon_fetchall_user_available($sid, $_W['member']['uid']);
	if(!empty($tokens)) {
		$token_nums = count($tokens);
		$token = $tokens[0];
	}

	if(!$_GPC['f']) {
		//再来一单的处理逻辑
		$cart = order_fetch_member_cart($sid);
	} else {
		$cart = order_place_again($sid, $_GPC['id']);
		if(empty($cart)) {
			$cart = order_fetch_member_cart($sid);
		}
	}
	$cart['data'] = (array)$cart['data'];
	include $this->template('goods-market');
}

if($op == 'cate') {
	$cart = order_insert_member_cart($sid);
	$cid = trim($_GPC['cid']);
	$result = goods_avaliable_fetchall($sid, $cid);
	$categorys = $result['category'];
	$cate_goods = $result['cate_goods'];
	$goods = $result['goods'];
	$bargains = $result['bargains'];
	$total = count($cate_goods[$cid]);

	$categorys_limit_status = 0;
	$categorys_limit = array();
	foreach($categorys as $row) {
		if($row['min_fee'] > 0) {
			$categorys_limit_status = 1;
			$row['fee'] = 0;
			$categorys_limit[$row['id']] = $row;
		}
	}
	$categorys_index = array_keys($categorys_limit);
	include $this->template('goods-market-cate');
}

if($op == 'detail') {
	if(!$_W['isajax']) {
		message(error(-1, '非法访问'), '', 'ajax');
	}
	$id = intval($_GPC['id']);
	$goods = goods_fetch($id);
	if(is_error($goods)) {
		message(error(-1, '商品不存在或已删除'), '', 'ajax');
	}
	if(!$goods['comment_total']) {
		$goods['comment_good_percent'] = '0%';
	} else {
		$goods['comment_good_percent'] = round($goods['comment_good'] / $goods['comment_total'] * 100, 2) . '%';
	}
	goods_fetchall();
	message(error(0, $goods), '', 'ajax');
}

if($op == 'cart_truncate') {
	if(!$_W['isajax']) {
		message(error(-1, '非法访问'), '', 'ajax');
	}
	pdo_delete('tiny_wmall_plus_order_cart', array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'uid' => $_W['member']['uid']));
	message(error(0, ''), '', 'ajax');
}

if($op == 'search') {
	if(!$_W['isajax']) {
		message(error(-1, '非法访问'), '', 'ajax');
	}
	$key = trim($_GPC['key']);
	if(empty($key)) {
		message(error(-1, '关键词不能为空'), '', 'ajax');
	}

	$goods = pdo_fetchall('select * from ' . tablename('tiny_wmall_plus_goods') . ' where uniacid = :uniacid and sid = :sid and status = 1 and title like :title', array(':uniacid' => $_W['uniacid'], ':sid' => $sid, ':title' => "%{$key}%"));
	if(!empty($goods)) {
		foreach($goods as &$good) {
			$good['thumb_cn'] = tomedia($good['thumb']);
			$good['is_in_business_hours'] = $store['is_in_business_hours'];
			if($good['is_options']) {
				$good['options'] = pdo_getall('tiny_wmall_plus_goods_options', array('uniacid' => $_W['uniacid'], 'goods_id' => $good['id']));
			}
		}
	}
	message(error(0, $goods), '', 'ajax');
}
