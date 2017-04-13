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
$this->checkAuth();
$sid = intval($_GPC['sid']);
$store = store_fetch($sid);
if(empty($store)) {
	message('门店不存在或已经删除', referer(), 'error');
}

$_share = array(
	'title' => $store['title'],
	'desc' => $store['content'],
	'imgUrl' => tomedia($store['logo'])
);
$op = trim($_GPC['op']) ? trim($_GPC['op']) : $store['template'];

if($_GPC['from'] == 'search') {
	pdo_query("update " . tablename('tiny_wmall_store') . " set click = click + 1 where uniacid = :uniacid and id = :id",  array(':uniacid' => $_W['uniacid'], ':id' => $sid));
}

if($op == 'index') {
	$title = "{$store['title']}-商品列表";
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
	$is_favorite = pdo_get('tiny_wmall_store_favorite', array('uniacid' => $_W['uniacid'], 'uid' => $_W['member']['uid'], 'sid' => $sid));

	$categorys_temp = $categorys = store_fetchall_goods_category($sid, 1);

	$dish = pdo_fetchall('SELECT * FROM ' . tablename('tiny_wmall_goods') . ' WHERE uniacid = :aid AND sid = :sid AND status = 1 ORDER BY displayorder DESC, id desc', array(':aid' => $_W['uniacid'], ':sid' => $sid), 'id');
	$min = 0;
	foreach($dish as $k => &$di) {
		if(!in_array($di['cid'], array_keys($categorys))) {
			unset($dish[$k]);
			continue;
		}
		$di['unitname_cn'] = !empty($di['unitname']) ? "/{$di['unitname']}" : '';
		if($di['is_options']) {
			$di['options'] = pdo_getall('tiny_wmall_goods_options', array('uniacid' => $_W['uniacid'], 'goods_id' => $di['id']));
		}
	}
	$min = min(array_keys($dish));
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

if($op == 'more') {
	$id = intval($_GPC['id']);
	$dish = pdo_fetchall('SELECT * FROM ' . tablename('tiny_wmall_goods') . ' WHERE uniacid = :aid AND sid = :sid AND status = 1 and id < :id order by displayorder DESC, id desc limit 12', array(':aid' => $_W['uniacid'], ':sid' => $sid, ':id' => $id), 'id');
	$min = 0;
	if(!empty($dish)) {
		foreach($dish as $k => &$di) {
			if(!in_array($di['cid'], array_keys($categorys))) {
				unset($dish[$k]);
				continue;
			}
			$di['thumb_cn'] = tomedia($di['thumb']);
			$di['is_in_business_hours'] = $store['is_in_business_hours'];
			if($di['is_options']) {
				$di['options'] = pdo_getall('tiny_wmall_goods_options', array('uniacid' => $_W['uniacid'], 'goods_id' => $di['id']));
			}
		}
		$min = min(array_keys($dish));
	}
	$dish = array_values($dish);
	$respon = array('error' => 0, 'message' => $dish, 'min' => $min);
	message($respon, '', 'ajax');
}

if($op == 'cate') {
	$cart = order_insert_member_cart($sid);
	$cid = intval($_GPC['cid']);
	$categorys = store_fetchall_goods_category($sid, 1);
	$dish = pdo_fetchall('SELECT * FROM ' . tablename('tiny_wmall_goods') . ' WHERE uniacid = :aid AND sid = :sid AND status = 1 ORDER BY displayorder DESC, id desc', array(':aid' => $_W['uniacid'], ':sid' => $sid), 'id');
	$total = 0;
	foreach($dish as $k => &$di) {
		if(!in_array($di['cid'], array_keys($categorys))) {
			unset($dish[$k]);
			continue;
		}
		if($di['cid'] == $cid) {
			$total++;
			$di['show'] = 1;
		} else {
			$di['show'] = 0;
		}
		if($di['is_options']) {
			$di['options'] = pdo_getall('tiny_wmall_goods_options', array('uniacid' => $_W['uniacid'], 'goods_id' => $di['id']));
		}
	}
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
	message(error(0, $goods), '', 'ajax');
}

if($op == 'cart_truncate') {
	if(!$_W['isajax']) {
		message(error(-1, '非法访问'), '', 'ajax');
	}
	pdo_delete('tiny_wmall_order_cart', array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'uid' => $_W['member']['uid']));
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

	$goods = pdo_fetchall('select * from ' . tablename('tiny_wmall_goods') . ' where uniacid = :uniacid and sid = :sid and status = 1 and title like :title', array(':uniacid' => $_W['uniacid'], ':sid' => $sid, ':title' => "%{$key}%"));
	if(!empty($goods)) {
		foreach($goods as &$good) {
			$good['thumb_cn'] = tomedia($good['thumb']);
			$good['is_in_business_hours'] = $store['is_in_business_hours'];
			if($good['is_options']) {
				$good['options'] = pdo_getall('tiny_wmall_goods_options', array('uniacid' => $_W['uniacid'], 'goods_id' => $good['id']));
			}
		}
	}
	message(error(0, $goods), '', 'ajax');
}
