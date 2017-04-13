<?php
/**
 * 外送系统
 * @author 灯火阑珊
 * @QQ 2471240272
 * @url http://bbs.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$_W['page']['title'] = '后台下单-' . $_W['we7_wmall']['config']['title'];

$store = store_check();
$sid = $store['id'];
$do = 'place';
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'index';

if($op == 'index') {
	$store = store_fetch($sid, array('delivery_price', 'delivery_free_price', 'pack_price'));
	$categorys = store_fetchall_goods_category($sid);
	$goods = pdo_fetchall('SELECT * FROM ' . tablename('tiny_wmall_goods') . ' WHERE uniacid = :aid AND sid = :sid AND status = 1 ORDER BY displayorder DESC, id ASC', array(':aid' => $_W['uniacid'], ':sid' => $sid));
	foreach($goods as &$good) {
		if($good['total'] == -1) {
			$good['total_cn'] = '库存充足';
		} elseif($good['total'] == 0) {
			$good['total_cn'] = '库存不足';
		} else {
			$good['total_cn'] = "库存{$good['total']}份";
		}
		$good['totalNum'] = 0;
		if($good['is_options']) {
			$good['options'] = pdo_getall('tiny_wmall_goods_options', array('uniacid' => $_W['uniacid'], 'goods_id' => $good['id']));
		}
	}
}

if($op == 'post') {
	if(!$_W['isajax']) {
		message(error(-1, '非法访问'), '', 'ajax');
	}
	$post = $_GPC['__input'];
	$goods = array();
	foreach($post['cart'] as $good) {
		$goods[] = $good['id'];
	}
	if(!empty($goods)) {
		$goods = implode(',', array_values($goods));
		$goods_info = pdo_fetchall('SELECT * FROM ' . tablename('tiny_wmall_goods') ." WHERE uniacid = :aid AND sid = :sid AND id IN ({$goods})", array(':aid' => $_W['uniacid'], ':sid' => $sid), 'id');
	}
	$cart = array();
	$goods = array();
	foreach($post['cart'] as $data) {
		$v = $data['id'];
		if(!$goods_info[$v]['is_options']) {
			$goods[$v][0] = array(
				'title' => $goods_info[$v]['title'],
				'num' => $data['num'],
				'price' => $goods_info[$v]['price'],
			);
			$num_data[$v] = $data['num'];
			$num += $data['num'];
			$price += $goods_info[$v]['price'] * $data['num'];
		} else {
			foreach($data['good']['options'] as $val) {
				$option_id = intval($val['id']);
				$option_num = intval($val['num']);
				if($option_id > 0 && $option_num > 0) {
					$option = pdo_get('tiny_wmall_goods_options', array('uniacid' => $_W['uniacid'], 'id' => $option_id));
					if(empty($option) || !empty($goods[$v][$option_id])) {
						continue;
					}
					$goods[$v][$option_id] = array(
						'title' => $goods_info[$v]['title'] . "({$option['name']})",
						'num' => $option_num,
						'price' => $option['price'],
					);
					$num_data[$v] += $option_num;
					$num += $option_num;
					$price += $option['price'] * $option_num;
				}
			}
		}
	}

	$cart = array(
		'price' => $price,
		'num' => $num,
		'data' => $goods,
		'num_data' => $num_data
	);

	$store = store_fetch($sid, array('delivery_price', 'delivery_free_price', 'pack_price'));
	//配送费
	$delivery_price = $store['delivery_price'];
	if($store['delivery_free_price'] > 0 && $price >= $store['delivery_free_price']) {
		$delivery_price = 0;
	}
	$order = array(
		'uniacid' => $_W['uniacid'],
		'acid' => $_W['acid'],
		'sid' => $sid,
		'uid' => 0,
		'ordersn' => date('Ymd') . random(6, true),
		'code' => random(4, true),
		'groupid' => 0,
		'order_type' => 1,
		'openid' => '',
		'mobile' => $post['user']['mobile'],
		'username' => $post['user']['username'],
		'sex' => '',
		'address' => $post['user']['address'],
		'location_x' => '',
		'location_y' => '',
		'delivery_day' => date('Y-m-d'),
		'delivery_time' => '尽快送出',
		'delivery_fee' => $delivery_price,
		'pack_fee' => $store['pack_price'],
		'pay_type' => 'delivery',
		'num' => $cart['num'],
		'price' => $cart['price'],
		'total_fee' => $cart['price'] + $delivery_price + $store['pack_price'],
		'discount_fee' => 0,
		'final_fee' => $cart['price'] + $delivery_price + $store['pack_price'] - 0,
		'status' => 2,
		'is_comment' => 0,
		'invoice' => trim($_GPC['invoice']),
		'addtime' => TIMESTAMP,
		'data' => iserializer($cart['data']),
		'note' => $post['user']['note'],
	);
	if($order['final_fee'] < 0) {
		$order['final_fee'] = 0;
	}
	pdo_insert('tiny_wmall_order', $order);
	$id = pdo_insertid();
	order_insert_current_log($id, $sid, $order['final_fee'], '', '');
	order_insert_status_log($id, $sid, 'place_order');
	order_update_goods_info($id, $sid, $cart);
	order_print($id);
	message(error(0, $id), '', 'ajax');
}

$GLOBALS['frames'] = array();
include $this->template('store/place');