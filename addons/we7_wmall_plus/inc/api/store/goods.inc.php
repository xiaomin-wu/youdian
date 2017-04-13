<?php
/**
 * 外送系统
 * @author 新睿社区
 * @QQ 10373458 * @url http://www.010xr.com/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
mload()->model('goods');
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'index';
$sid = intval($_GPC['sid']);

if($op == 'index') {
	$store = store_fetch($sid);
	if(empty($store)) {
		message(ierror(-1, '商户不存在或已删除'), '', 'ajax');
	}
	$activity = store_fetch_activity($sid);
	$is_favorite = pdo_get('tiny_wmall_plus_store_favorite', array('uniacid' => $_W['uniacid'], 'uid' => $_W['member']['uid'], 'sid' => $sid));

	$categorys = store_fetchall_goods_category($sid, 1);
	$dish = pdo_fetchall('SELECT * FROM ' . tablename('tiny_wmall_plus_goods') . ' WHERE uniacid = :aid AND sid = :sid AND status = 1 ORDER BY displayorder DESC, id ASC', array(':aid' => $_W['uniacid'], ':sid' => $sid));
	$cate_dish = array();
	foreach($dish as &$di) {
		$di['unitname_cn'] = !empty($di['unitname']) ? "/{$di['unitname']}" : '';
		if($di['is_options']) {
			$di['options'] = pdo_getall('tiny_wmall_plus_goods_options', array('uniacid' => $_W['uniacid'], 'goods_id' => $di['id']));
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
	$data = array(
		'store' => $store,
		'activity' => $activity,
		'favorite' => intval($is_favorite),
		'tokens' => $tokens,
		'category' => $categorys,
		'goods' => $cate_dish,
		'cart' => $cart
	);
	message(ierror(0, '', $data), '', 'ajax');
}

if($op == 'market') {
	$store = store_fetch($sid);
	if(empty($store)) {
		message(ierror(-1, '商户不存在或已删除'), '', 'ajax');
	}
	$activity = store_fetch_activity($sid);
	$is_favorite = pdo_get('tiny_wmall_plus_store_favorite', array('uniacid' => $_W['uniacid'], 'uid' => $_W['member']['uid'], 'sid' => $sid));
	//获取优惠券
	mload()->model('coupon');
	$tokens = coupon_fetchall_user_available($sid, $_W['member']['uid']);
	if(!empty($tokens)) {
		$token_nums = count($tokens);
		$token = $tokens[0];
	}
	$cart = order_fetch_member_cart($sid);

	$categorys = store_fetchall_goods_category($sid, 1);
	$label = array(
		'id' => -1,
		'title' => '热销',
	);
	array_unshift($categorys, $label);

	$goods = pdo_fetchall('SELECT * FROM ' . tablename('tiny_wmall_plus_goods') . " WHERE uniacid = :aid AND sid = :sid AND status = 1 AND label != '' ORDER BY displayorder DESC, id ASC", array(':aid' => $_W['uniacid'], ':sid' => $sid));
	foreach($goods as &$good) {
		$good['thumb_cn'] = tomedia($good['thumb']);
		if($good['is_options']) {
			$good['options'] = pdo_getall('tiny_wmall_plus_goods_options', array('uniacid' => $_W['uniacid'], 'goods_id' => $good['id']));
		}
	}
	$data = array(
		'store' => $store,
		'activity' => $activity,
		'favorite' => intval($is_favorite),
		'tokens' => $tokens,
		'category' => $categorys,
		'goods' => $goods,
		'cart' => $cart
	);
	message(ierror(0, '', $data), '', 'ajax');
}

if($op == 'more') {
	$cid = intval($_GPC['cid']);
	$id = intval($_GPC['id']);
	$goods = pdo_fetchall('SELECT * FROM ' . tablename('tiny_wmall_plus_goods') . ' WHERE uniacid = :aid AND sid = :sid AND cid = :cid AND status = 1 and id < :id order by displayorder DESC, id desc limit 15', array(':aid' => $_W['uniacid'], ':sid' => $sid, ':cid' => $cid, ':id' => $id), 'id');
	$min = 0;
	if(!empty($goods)) {
		foreach($goods as $k => &$good) {
			$good['thumb_cn'] = tomedia($good['thumb']);
			if($good['is_options']) {
				$good['options'] = pdo_getall('tiny_wmall_plus_goods_options', array('uniacid' => $_W['uniacid'], 'goods_id' => $good['id']));
			}
		}
		$min = min(array_keys($goods));
	}
	$goods = array_values($goods);
	$data = array(
		'goods' => $goods,
		'min_id' => $min,
	);
	message(ierror(0, '', $data), '', 'ajax');
}

if($op == 'detail') {
	$id = intval($_GPC['id']);
	$goods = goods_fetch($id);
	if(is_error($goods)) {
		message(ierror(0, '商品不存在或已删除'), '', 'ajax');
	}
	$goods['thumb_cn'] = tomedia($goods['thumb']);
	if(!$goods['comment_total']) {
		$goods['comment_good_percent'] = '0%';
	} else {
		$goods['comment_good_percent'] = round($goods['comment_good'] / $goods['comment_total'] * 100, 2) . '%';
	}
	message(ierror(0, '', $goods), '', 'ajax');
}
