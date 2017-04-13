<?php
/**
 * [WeEngine System] Copyright (c) 2013 WE7.CC
 * $sn$
 */
defined('IN_IA') or exit('Access Denied');

function errander_types() {
	$data = array(
		'buy' => array(
			'css' => 'label label-success',
			'text' => '随意购',
		),
		'delivery' => array(
			'css' => 'label label-warning',
			'text' => '快速送',
		),
		'pickup' => array(
			'css' => 'label label-danger',
			'text' => '快速取',
		),
	);
	return $data;
}

function errander_order_status() {
	$data = array(
		'0' => array(
			'css' => '',
			'text' => '所有',
			'color' => ''
		),
		'1' => array(
			'css' => 'label label-default',
			'text' => '待接单',
			'color' => '',
		),
		'2' => array(
			'css' => 'label label-info',
			'text' => '正在进行中',
			'color' => 'color-info'
		),
		'3' => array(
			'css' => 'label label-success',
			'text' => '已完成',
			'color' => 'color-success'
		),
		'4' => array(
			'css' => 'label label-danger',
			'text' => '已取消',
			'color' => 'color-danger'
		)
	);
	return $data;
}

function errander_category_fetch($id) {
	global $_W;
	$category = pdo_get('tiny_wmall_errander_category', array('uniacid' => $_W['uniacid'], 'id' => $id));
	if(!empty($category)) {
		$category['tip_min'] = $category['tip_min'] ? $category['tip_min'] : 0;
		$category['tip_max'] = $category['tip_max'] ? $category['tip_max'] : 200;
		$category['label'] = iunserializer($category['label']);
	}
	return $category;
}

function errander_order_fetch($id) {
	global $_W;
	$id = intval($id);
	$order = pdo_fetch('SELECT * FROM ' . tablename('tiny_wmall_errander_order') . ' WHERE uniacid = :aid AND id = :id', array(':aid' => $_W['uniacid'], ':id' => $id));
	if(empty($order)) {
		return false;
	}
	$order_status = errander_order_status();
	$pay_types = order_pay_types();
	$order_types = errander_types();
	$order['order_type_cn'] = $order_types[$order['order_type']]['text'];
	$order['status_cn'] = $order_status[$order['status']]['text'];
	if(empty($order['is_pay'])) {
		$order['pay_type_cn'] = '未支付';
	} else {
		$order['pay_type_cn'] = !empty($pay_types[$order['pay_type']]['text']) ? $pay_types[$order['pay_type']]['text'] : '其他支付方式';
	}
	if(empty($order['delivery_time'])) {
		$order['delivery_time'] = '立即送达';
	}
	$order['category'] = pdo_get('tiny_wmall_errander_category', array('uniacid' => $_W['uniacid'], 'id' => $order['order_cid']), array('id', 'title', 'thumb'));
	if($order['order_type'] == 'buy') {
		$order['buy_address'] = !empty($order['buy_address']) ? $order['buy_address'] : '用户未指定,您可自由寻找商户购买商品';
	}
	if($order['refund_status'] > 0) {
		$refund_channel = order_refund_channel();
		$refund_status = order_refund_status();
		$order['refund_status_cn'] = $refund_status[$order['refund_status']]['text'];
		$order['refund_channel_cn'] = $refund_channel[$order['refund_channel']]['text'];
	}
	return $order;
}

function errander_order_fetch_status_log($id) {
	global $_W;
	$data = pdo_fetchall("SELECT * FROM " . tablename('tiny_wmall_errander_order_status_log') . ' WHERE uniacid = :uniacid and oid = :oid order by id asc', array(':uniacid' => $_W['uniacid'], ':oid' => $id), 'id');
	return $data;
}

function errander_order_insert_status_log($id, $type, $note = '') {
	global $_W;
	if(empty($type)) {
		return false;
	}
	$config = $_W['we7_wmall']['config']['errander'];
	mload()->model('store');
	$order = errander_order_fetch($id);
	$notes = array(
		'place_order' => array(
			'status' => 1,
			'title' => '订单提交成功',
			'note' => "单号:{$order['order_sn']}",
			'ext' => array(
				array(
					'key' => 'pay_time_limit',
					'title' => '待支付',
					'note' => "请在订单提交后{$config['pay_time_limit']}分钟内完成支付",
				)
			)
		),
		'pay' => array(
			'status' => 2,
			'title' => '订单已支付',
			'note' => '支付成功.付款时间:' . date('Y-m-d H:i:s'),
			'ext' => array(
				array(
					'key' => 'handel_time_limit',
					'title' => '待接单',
					'note' => "超出{$config['handel_time_limit']}分钟未接单，订单自动取消",
				)
			)
		),
		'delivery_assign' => array(
			'status' => 3,
			'title' => '已接单',
			'note' => ''
		),
		'delivery_instore' => array(
			'status' => 4,
			'title' => '已取货',
			'note' => '',
		),
		'end' => array(
			'status' => 5,
			'title' => '订单已完成',
			'note' => '任何意见和吐槽,都欢迎联系我们'
		),
		'cancel' => array(
			'status' => 6,
			'title' => '订单已取消',
			'note' => ''
		),
	);
	$title = $notes[$type]['title'];
	$note = $note ? $note : $notes[$type]['note'];
	$data = array(
		'uniacid' => $_W['uniacid'],
		'oid' => $id,
		'status' => $notes[$type]['status'],
		'type' => $type,
		'title' => $title,
		'note' => $note,
		'addtime' => TIMESTAMP,
	);
	pdo_insert('tiny_wmall_errander_order_status_log', $data);
	if(!empty($notes[$type]['ext'])) {
		foreach($notes[$type]['ext'] as $val) {
			if($val['key'] == 'pay_time_limit' && !$config['pay_time_limit']) {
				unset($val['note']);
			}
			if($val['key'] == 'handel_time_limit' && !$config['handel_time_limit']) {
				unset($val['note']);
			}
			$data = array(
				'uniacid' => $_W['uniacid'],
				'oid' => $id,
				'title' => $val['title'],
				'note' => $val['note'],
				'addtime' => TIMESTAMP,
			);
			pdo_insert('tiny_wmall_errander_order_status_log', $data);
		}
	}
	return true;
}

function errander_order_status_notice($id, $status, $note = '') {
	global $_W;
	$status_arr = array(
		'pay',//已支付
		'delivery_assign', //抢单完成
		'delivery_instore', //确认到店
		'end', //已完成
		'cancel',//已取消
		'delivery_notice'
	);
	if(!in_array($status, $status_arr)) {
		return false;
	}
	$type = $status;
	$order = errander_order_fetch($id);
	$acc = WeAccount::create($order['acid']);
	if(!empty($order['openid'])) {
		if($type == 'pay') {
			$title = '您的跑腿订单已付款,等待平台接单';
			$remark = array(
				"订单类型: {$order['order_type_cn']}",
				"商品信息: {$order['goods_name']}",
				"总金　额: {$order['total_fee']}",
				"支付方式: {$order['pay_type_cn']}",
				"支付时间: " . date('Y-m-d H: i', $order['paytime']),
			);
		}

		if($type == 'delivery_assign') {
			$title = '平台已接受您的跑腿订单， 订单正在处理中';
			$remark = array(
				"订单类型: {$order['order_type_cn']}",
				"商品信息: {$order['goods_name']}",
				"总金　额: {$order['total_fee']}",
				"接单时间: " . date('Y-m-d H:i:s', $order['delivery_assign_time']),
			);
			$end_remark = "";
		}

		if($type == 'delivery_instore') {
			$title = '配送员已取货，正在配送中';
			$remark = array(
				"订单类型: {$order['order_type_cn']}",
				"商品信息: {$order['goods_name']}",
				"总金　额: {$order['total_fee']}",
			);
			$end_remark = "";
		}

		if($type == 'end') {
			$title = '订单处理完成';
			$remark = array(
				"订单类型: {$order['order_type_cn']}",
				"商品信息: {$order['goods_name']}",
				"总金　额: {$order['total_fee']}",
				"完成时间: " . date('Y-m-d H: i', time()),
			);
			$end_remark = "您的订单已处理完成, 如对商品有不满意或投诉请联系客服:{$_W['we7_wmall']['config']['mobile']},欢迎您下次光临.戳这里记得给我们的服务评价.";
		}

		if($type == 'cancel') {
			$title = '订单已取消';
			$remark = array(
				"订单类型: {$order['order_type_cn']}",
				"商品信息: {$order['goods_name']}",
				"总金　额: {$order['total_fee']}",
				"取消时间: " . date('Y-m-d H: i', time()),
			);
		}

		if(!empty($note)) {
			if(!is_array($note)) {
				$remark[] = $note;
			} else {
				$remark[] = implode("\n", $note);;
			}
		}
		if(!empty($end_remark)) {
			$remark[] = $end_remark;
		}
		$url = murl('entry', array('do' => 'errander-order', 'm' => 'we7_wmall', 'op' => 'detail', 'id' => $order['id']), true, true);
		$remark = implode("\n", $remark);
		$send = tpl_format($title, $order['order_sn'], $order['status_cn'], $remark);
		$status = $acc->sendTplNotice($order['openid'], $_W['we7_wmall']['config']['public_tpl'], $send, $url);
		return $status;
	}
	return true;
}

function errander_order_insert_refund_log($id, $type, $note = '') {
	global $_W;
	if(empty($type)) {
		return false;
	}
	$notes = array(
		'apply' => array(
			'status' => 1,
			'title' => '提交退款申请',
			'note' => "",
		),
		'handel' => array(
			'status' => 2,
			'title' => "{$_W['we7_wmall']['config']['title']}接受退款申请",
			'note' => ''
		),
		'success' => array(
			'status' => 3,
			'title' => "退款成功",
			'note' => ''
		),
		'fail' => array(
			'status' => 4,
			'title' => "退款失败",
			'note' => ''
		),
	);
	$title = $notes[$type]['title'];
	$note = $note ? $note : $notes[$type]['note'];
	$data = array(
		'uniacid' => $_W['uniacid'],
		'order_type' => 'errander',
		'sid' => 0,
		'oid' => $id,
		'status' => $notes[$type]['status'],
		'type' => $type,
		'title' => $title,
		'note' => $note,
		'addtime' => TIMESTAMP,
	);
	pdo_insert('tiny_wmall_order_refund_log', $data);
	return true;
}

function errander_order_fetch_refund_status_log($id) {
	global $_W;
	$data = pdo_fetchall("SELECT * FROM " . tablename('tiny_wmall_order_refund_log') . ' WHERE uniacid = :uniacid and oid = :oid and order_type = :order_type order by id asc', array(':uniacid' => $_W['uniacid'], ':oid' => $id, ':order_type' => 'errander'), 'id');
	return $data;
}

function errander_order_deliveryer_notice($id, $type, $deliveryer_id = 0, $note = '') {
	global $_W;
	$order = errander_order_fetch($id);
	if(empty($order)) {
		return error(-1, '订单不存在或已删除');
	}
	mload()->model('deliveryer');
	$deliveryers = deliveryer_fetchall(0);
	if(empty($deliveryers)) {
		return false;
	}
	$acc = WeAccount::create($order['acid']);
	if($type == 'new_delivery') {
		$title = "您有新的跑腿订单,配送费: {$order['deliveryer_fee']}元,小费: {$order['delivery_tips']}元 ";
		$remark = array(
			"下单时间: " . date('Y-m-d H:i', $order['addtime']),
			"配送　费: {$order['deliveryer_fee']}元",
			"小　　费: {$order['delivery_tips']}元",
			"本单收入: " . ($order['deliveryer_fee'] + $order['delivery_tips']) . "元",
			"收货　人: {$order['accept_username']}",
			"联系手机: {$order['accept_mobile']}",
			"送货地址: {$order['accept_address']}",
		);
		$remark = implode("\n", $remark);
		$url = murl('entry', array('do' => 'dyerrander', 'm' => 'we7_wmall', 'op' => 'detail', 'id' => $order['id']), true, true);
		//手机端推送
		//Jpush_deliveryman_send('您有新的跑腿配送订单', $title, array('voice_play_nums' => 2, 'voice_text' => $title));
	} else if($type == 'delivery_wait') {
		$remark = array(
			"订单类型: {$order['order_type_cn']}",
			"下单时间: " . date('Y-m-d H:i', $order['addtime']),
			"配送　费: {$order['deliveryer_fee']}",
			"小　　费: {$order['delivery_tips']}",
		);
		if($order['order_type'] == 'buy') {
			$remark[] = "购买商品: {$order['goods_name']}";
			if(!empty($order['goods_price'])) {
				$remark[] = "预期价格: {$order['goods_price']}";
			}
			$remark[] = "购买地址: {$order['buy_address']}";
		} elseif($order['order_type'] == 'delivery') {
			$remark[] = "物品信息: {$order['goods_name']}";
			if(!empty($order['goods_price'])) {
				$remark[] = "物品价值: {$order['goods_price']}";
			}
			$remark[] = "发货地址: {$order['buy_address']}";
			$remark[] = "联系　人: {$order['buy_username']}";
			$remark[] = "手机　号: {$order['buy_mobile']}";
		} else {
			$remark[] = "物品信息: {$order['goods_name']}";
			if(!empty($order['goods_price'])) {
				$remark[] = "物品价值: {$order['goods_price']}";
			}
			$remark[] = "取货地址: {$order['buy_address']}";
			$remark[] = "联系　人: {$order['buy_username']}";
			$remark[] = "手机　号: {$order['buy_mobile']}";
		}

		$remark[] = "收货　人: {$order['accept_username']}\n联系手机: {$order['accept_mobile']}\n送货地址: {$order['accept_address']}";
		$remark = implode("\n", $remark);
		$url = $_W['siteroot'] . 'app' . ltrim(murl('entry', array('do' => 'dyerrander-order', 'm' => 'we7_wmall', 'op' => 'list')), '.');
		//手机端推送
		$title = "平台有新的配送订单, 配送地址为{$order['accept_address']}, 快去抢单吧";
		Jpush_deliveryman_send('您有新的外卖待抢订单', $title, array('voice_play_nums' => 2, 'voice_text' => $title));
	}
	$send = tpl_format($title, $order['order_sn'], $order['status_cn'], $remark);
	mload()->model('sms');
	if($type == 'new_delivery') {
		$deliveryer = $deliveryers[$deliveryer_id];
		if(empty($deliveryer)) {
			return error(-1, '配送员不存在');
		}
		$acc->sendTplNotice($deliveryer['deliveryer']['openid'], $_W['we7_wmall']['config']['public_tpl'], $send, $url);
	} else {
		foreach($deliveryers as $deliveryer) {
/*			if(!empty($deliveryer['deliveryer']['mobile'])) {
				sms_singlecall($deliveryer['deliveryer']['mobile'], array('name' => $deliveryer['deliveryer']['title'], 'store' => $store['title']), 'deliveryer');
			}*/
			$acc->sendTplNotice($deliveryer['deliveryer']['openid'], $_W['we7_wmall']['config']['public_tpl'], $send, $url);
		}
	}
	return true;
}

function errander_order_analyse($id) {
	$order = errander_order_fetch($id);
	if(empty($order)) {
		return error(-1, '订单不存在或已删除');
	}
	mload()->model('deliveryer');
	$deliveryers = deliveryer_fetchall();
	if(!empty($deliveryers)) {
		foreach($deliveryers as &$deliveryer) {
			$deliveryer['order_id'] = $id;
			if(empty($order['buy_location_x']) || empty($order['buy_location_y']) || empty($deliveryer['deliveryer']['location_y']) || empty($deliveryer['deliveryer']['location_x'])) {
				$deliveryer['store2deliveryer_distance'] = '未知';
				$deliveryer['store2user_distance'] = '未知';
			} else {
				$deliveryer['store2deliveryer_distance'] = distanceBetween($order['buy_location_y'], $order['buy_location_x'], $deliveryer['deliveryer']['location_y'], $deliveryer['deliveryer']['location_x']);
				$deliveryer['store2deliveryer_distance'] = round($deliveryer['store2deliveryer_distance'], 2) . 'km';
				$deliveryer['store2user_distance'] = $order['distance'] . 'km';
				$deliveryer['user2deliveryer_distance'] = distanceBetween($order['accept_location_y'], $order['accept_location_x'], $deliveryer['deliveryer']['location_y'], $deliveryer['deliveryer']['location_x']);
				$deliveryer['user2deliveryer_distance'] = round($deliveryer['user2deliveryer_distance'], 2) . 'km';
			}
		}
		if(empty($order['buy_location_x']) || empty($order['buy_location_y'])) {
			$deliveryers = array_sort($deliveryers, 'user2deliveryer_distance');
		} else {
			$deliveryers = array_sort($deliveryers, 'store2deliveryer_distance');
		}
		$order['deliveryers'] = $deliveryers;
	} else {
		return error(-1, '没有平台配送员，无法进行自动调度');
	}
	return $order;
}

function errander_order_assign_deliveryer($order_id, $deliveryer_id, $update_deliveryer = false) {
	global $_W;
	$order = errander_order_fetch($order_id);
	if(empty($order)) {
		return error(-1, '订单不存在或已经删除');
	}
	if($order['status'] == 3) {
		return error(-1, '订单已处理完成, 不能指定配送员');
	}
	if($order['status'] == 4) {
		return error(-1, '订单已取消, 不能指定配送员');
	}
	if($order['status'] == 2 && !$update_deliveryer) {
		return error(-1, '该订单已经分配给其他配送员，不能重新指定配送员');
	}
	mload()->model('deliveryer');
	$deliveryer = pdo_get('tiny_wmall_deliveryer', array('uniacid' => $_W['uniacid'], 'id' => $deliveryer_id));
	if(empty($deliveryer)) {
		return error(-1, '配送员不存在或已经删除,请指定其他配送员配送');
	}
	$permission = pdo_get('tiny_wmall_store_deliveryer', array('uniacid' => $_W['uniacid'], 'sid' => 0, 'deliveryer_id' => $deliveryer_id));
	if(empty($permission)) {
		return error(-1, "配送员{$deliveryer['title']}不是平台配送员，没有配送订单的权利");
	}
	$update = array(
		'status' => 2,
		'deliveryer_id' => $deliveryer_id,
		'delivery_assign_time' => TIMESTAMP,
		'delivery_status' => 2 //已分配配送员
	);
	pdo_update('tiny_wmall_errander_order', $update, array('uniacid' => $_W['uniacid'], 'id' => $order_id));
	$note = "配送员：{$deliveryer['title']}, 手机号：{$deliveryer['mobile']}";
	errander_order_insert_status_log($order_id, 'delivery_assign', $note);
	$remark = array("配送员：{$deliveryer['title']}", "手机号：{$deliveryer['mobile']}");
	errander_order_status_notice($order_id, 'delivery_assign', $remark);
	errander_order_deliveryer_notice($order_id, 'new_delivery', $deliveryer_id);
	return true;
}

function errander_order_manager_notice($order_id, $type, $note = '') {
	global $_W;
	$maneger = $_W['we7_wmall']['config']['manager'];
	if(empty($maneger)) {
		return error(-1, '管理员信息不完善');
	}
	$order = errander_order_fetch($order_id);
	if(empty($order)) {
		return error(-1, '订单不存在或已经删除');
	}
	$acc = WeAccount::create($order['acid']);
	if($type == 'new_delivery') {
		$title = '平台有新的跑腿订单，请尽快调度处理';
		$remark = array(
			"订单类型: {$order['order_type_cn']}",
			"商品信息: {$order['goods_name']}",
			"总金　额: {$order['total_fee']}",
			"支付方式: {$order['pay_type_cn']}",
			"支付时间: " . date('Y-m-d H: i', $order['paytime']),
		);
	}
	if($type == 'dispatch_error') {
		$title = '平台有新的跑腿订单，系统自动调度失败，请登录后台人工调度';
		$remark = array(
			"订单类型: {$order['order_type_cn']}",
			"商品信息: {$order['goods_name']}",
			"总金　额: {$order['total_fee']}",
		);
	}

	if(!empty($note)) {
		if(!is_array($note)) {
			$remark[] = $note;
		} else {
			$remark[] = implode("\n", $note);;
		}
	}
	if(!empty($end_remark)) {
		$remark[] = $end_remark;
	}
	$remark = implode("\n", $remark);
	$send = tpl_format($title, $order['order_sn'], $order['status_cn'], $remark);
	$status = $acc->sendTplNotice($maneger['openid'], $_W['we7_wmall']['config']['public_tpl'], $send);
	return $status;
}

function errander_order_status_update($id, $type, $extra = array()) {
	global $_W;
	$order = errander_order_fetch($id);
	if(empty($order)) {
		return error(-1, '订单不存在或已删除');
	}
	$config = $_W['we7_wmall']['config'];
	if($type == 'dispatch') {
		if(empty($order['is_pay'])) {
			return error(-1, '订单尚未支付，支付后才能进行调度派单');
		}
		if($config['errander']['dispatch_mode'] == 1) {
			//抢单模式
			errander_order_deliveryer_notice($id, 'delivery_wait');
		} elseif($config['errander']['dispatch_mode'] == 2) {
			//管理员派单(只需要通知平台管理员调度即可, 在支付成功里已通知， 这里不在通知)
		} else {
			//系统自动分配
			$order = errander_order_analyse($id);
			if(is_error($order)) {
				errander_order_manager_notice($id, 'dispatch_error', "失败原因：{$order['message']}");
			}
			$deliveryer = array_shift($order['deliveryers']);
			$status = errander_order_assign_deliveryer($id, $deliveryer['deliveryer']['id']);
		}
	} elseif($type == 'pay') {
		errander_order_insert_status_log($id, 'pay');
		errander_order_status_notice($id, 'pay');
		errander_order_manager_notice($id, 'new_delivery');
	} elseif($type == 'cancel') {
		if($order['status'] == 3) {
			return error(-1, '系统已完成， 不能取消订单');
		}
		if($order['status'] == 4) {
			return error(-1, '系统已取消， 不能取消订单');
		}
		if($order['delivery_status'] > 2) {
			return error(-1, '配送员已取货， 不能取消订单');
		}

		if(!$order['is_pay'] || $order['final_fee'] <= 0) {
			pdo_update('tiny_wmall_errander_order', array('status' => 4), array('uniacid' => $_W['uniacid'], 'id' => $id));
			errander_order_insert_status_log($id, 'cancel');
			errander_order_status_notice($id, 'cancel');
		} else {
			if($order['refund_status'] > 0) {
				return error(-1, '退款申请处理中, 请勿重复发起');
			}
			$update = array(
				'status' => 4,
				'refund_status' => 1, //发起退款申请
				'refund_out_no' => date('YmdHis') . random(10, true),
				'refund_apply_time' => TIMESTAMP,
			);
			pdo_update('tiny_wmall_errander_order', $update, array('uniacid' => $_W['uniacid'], 'id' => $id));
			errander_order_insert_status_log($id, 'cancel');
			errander_order_insert_refund_log($id, 'apply');
			$note = array(
				"退款金额: {$order['final_fee']}",
				"已付款项会在1-15工作日内返回您的账号",
			);
			errander_order_status_notice($id, 'cancel', $note);
			$status = errander_order_refund_notice($id, 'apply');
		}
	} elseif($type == 'end') {
		if($order['status'] == 3) {
			return error(-1, '系统已完成， 请勿重复操作');
		}
		if($order['status'] == 4) {
			return error(-1, '系统已取消， 不能在进行其他操作');
		}
		$update = array(
			'status' => 3,
			'delivery_status' => 4, //已送达
		);
		if($order['deliveryer_id'] > 0) {
			$update['delivery_success_time'] = TIMESTAMP;
		}
		pdo_update('tiny_wmall_errander_order', $update, array('uniacid' => $_W['uniacid'], 'id' => $id));
		$total_deliveryer_fee = $order['deliveryer_fee'] + $order['delivery_tips'];
		if($total_deliveryer_fee > 0) {
			mload()->model('deliveryer');
			deliveryer_update_credit2($order['deliveryer_id'], $total_deliveryer_fee, 1, $id, '', 'errander');
		}
		errander_order_insert_status_log($id, 'end');
		errander_order_status_notice($id, 'end');
	} elseif($type == 'delivery_assign') {
		if($order['status'] == 3) {
			return error(-1, '系统已完成， 不能抢单或分配订单');
		}
		if($order['status'] == 4) {
			return error(-1, '系统已取消， 不能抢单或分配订单');
		}
		if($order['deliveryer_id'] > 0) {
			return error(-1, '来迟了, 该订单已被别人接单');
		}
		if(empty($extra['deliveryer_id'])) {
			return error(-1, '配送员id不存在');
		}
		$deliveryer = pdo_get('tiny_wmall_deliveryer', array('uniacid' => $_W['uniacid'], 'id' => $extra['deliveryer_id']));
		if(empty($deliveryer)) {
			return error(-1, '配送员不存在');
		}
		$update = array(
			'status' => 2,
			'delivery_status' => 2, //订单已被抢单
			'deliveryer_id' => $extra['deliveryer_id'],
			'delivery_assign_time' => TIMESTAMP,
		);
		pdo_update('tiny_wmall_errander_order', $update, array('uniacid' => $_W['uniacid'], 'id' => $id));
		$note = "配送员：{$deliveryer['title']}, 手机号：{$deliveryer['mobile']}";
		errander_order_insert_status_log($id, 'delivery_assign', $note);
		$remark = array("配送员：{$deliveryer['title']}", "手机号：{$deliveryer['mobile']}");
		errander_order_status_notice($id, 'delivery_assign', $remark);
	} elseif($type == 'delivery_instore') {
		if($order['status'] == 3) {
			return error(-1, '系统已完成， 不能变更状态');
		}
		if($order['status'] == 4) {
			return error(-1, '系统已取消， 不能变更状态');
		}
		if(empty($extra['deliveryer_id'])) {
			return error(-1, '配送员不存在');
		}
		$deliveryer = pdo_get('tiny_wmall_deliveryer', array('uniacid' => $_W['uniacid'], 'id' => $extra['deliveryer_id'] ));
		if(empty($deliveryer)) {
			return error(-1, '配送员不存在');
		}
		if($order['deliveryer_id'] != $deliveryer['id']) {
			return error(-1, '该订单不是您配送，不能确认取货');
		}
		$update = array(
			'status' => 2,
			'delivery_status' => 3, //已取货
			'delivery_instore_time' => TIMESTAMP,
		);
		pdo_update('tiny_wmall_errander_order', $update, array('uniacid' => $_W['uniacid'], 'id' => $id));
		$note = "配送员：{$deliveryer['title']}, 手机号：{$deliveryer['mobile']}";
		errander_order_insert_status_log($id, 'delivery_instore', $note);
	} elseif($type == 'delivery_success') {
		if($order['status'] == 3) {
			return error(-1, '系统已完成， 不能变更状态');
		}
		if($order['status'] == 4) {
			return error(-1, '系统已取消， 不能变更状态');
		}
		if(empty($extra['deliveryer_id'])) {
			return error(-1, '配送员不存在');
		}
		$deliveryer = pdo_get('tiny_wmall_deliveryer', array('uniacid' => $_W['uniacid'], 'id' => $extra['deliveryer_id'] ));
		if(empty($deliveryer)) {
			return error(-1, '配送员不存在');
		}
		if($order['deliveryer_id'] != $deliveryer['id']) {
			return error(-1, '该订单不是您配送，不能确认完成');
		}
		if(empty($extra['code'])) {
			return error(-1, '收获码不能为空');
		}
		if($extra['code'] != $order['code']) {
			return error(-1, '收获码有误');
		}
		$update = array(
			'status' => 3,
			'delivery_status' => 4, //已送达
			'delivery_success_time' => TIMESTAMP
		);
		pdo_update('tiny_wmall_errander_order', $update, array('uniacid' => $_W['uniacid'], 'id' => $id));
		$total_deliveryer_fee = $order['deliveryer_fee'] + $order['delivery_tips'];
		if($total_deliveryer_fee > 0) {
			mload()->model('deliveryer');
			deliveryer_update_credit2($order['deliveryer_id'], $total_deliveryer_fee, 1, $id, '', 'errander');
		}
		errander_order_insert_status_log($id, 'end');
		errander_order_status_notice($id, 'end');
	}
	return true;
}

function errander_order_refund_notice($order_id, $type, $note = '') {
	global $_W;
	$order = errander_order_fetch($order_id);
	if(empty($order)) {
		return error(-1, '订单不存在或已经删除');
	}
	$acc = WeAccount::create($order['acid']);
	if($type == 'apply') {
		$maneger = $_W['we7_wmall']['config']['manager'];
		if(!empty($maneger['openid'])) {
			//通知平台管理员
			$tips = "您的平台有新的【退款申请】, 单号【{$order['refund_out_no']}】,请尽快处理";
			$remark = array(
				"订单类型: 跑腿订单-{$order['order_type_cn']}",
				"退款单号: " . $order['refund_out_no'],
				"支付方式: " . $order['pay_type_cn'],
				"用户姓名: " . $order['accept_username'],
				"联系方式: " . $order['accept_mobile'],
				$note
			);
			$params = array(
				'first' => $tips,
				'reason' => '订单取消, 发起退款流程',
				'refund' => $order['final_fee'],
				'remark' => implode("\n", $remark)
			);
			$send = sys_wechat_tpl_format($params);
			$status = $acc->sendTplNotice($maneger['openid'], $_W['we7_wmall']['config']['notice']['refund_tpl'], $send);
		}
		if(!empty($order['openid'])) {
			$tips = "您发起取消订单流程,已付款项会在1-15工作日内返回到用户的账号, 如有疑问, 请联系平台管理员";
			$remark = array(
				"订单类型: 跑腿订单-{$order['order_type_cn']}",
				"订单　号: {$order['order_sn']}",
				"退款单号: {$order['refund_out_no']}",
				"支付方式: {$order['pay_type_cn']}",
				$note
			);
			$params = array(
				'first' => $tips,
				'reason' => '订单取消, 发起退款流程',
				'refund' => $order['final_fee'],
				'remark' => implode("\n", $remark)
			);
			$send = sys_wechat_tpl_format($params);
			$status = $acc->sendTplNotice($order['openid'], $_W['we7_wmall']['config']['notice']['refund_tpl'], $send);
		}
	} elseif($type == 'success') {
		if(!empty($order['openid'])) {
			$tips = "您的订单已退款成功，如有疑问, 请联系平台管理员 ";
			$remark = array(
				"订单　号: {$order['order_sn']}",
				"退款单号: {$order['refund_out_no']}",
				"支付方式: {$order['pay_type_cn']}",
				"退款渠道: {$order['refund_channel_cn']}",
				"退款账户: {$order['refund_account']}",
				"如有疑问, 请联系平台管理员",
			);
			$params = array(
				'first' => $tips,
				'reason' => '订单取消, 发起退款流程',
				'refund' => $order['final_fee'],
				'remark' => implode("\n", $remark)
			);
			$send = sys_wechat_tpl_format($params);
			$status = $acc->sendTplNotice($order['openid'], $_W['we7_wmall']['config']['notice']['refund_tpl'], $send);
		}
	}
	return true;
}

function errander_order_begin_payrefund($id) {
	global $_W;
	$order = errander_order_fetch($id);
	if(empty($order)) {
		return error(-1, '订单不存在或已删除');
	}
	if($order['refund_status'] == 2) {
		return error(-1, '退款进行中， 请勿重复操作');
	}
	if($order['refund_status'] == 3) {
		return error(-1, '退款已成功, 不能发起退款');
	}
	errander_order_insert_refund_log($order['id'], 'handel');
	if($order['pay_type'] == 'credit') {
		if($order['uid'] > 0) {
			$log = array(
				$order['uid'],
				"外送模块(we7_wmall)订单退款, 订单号:{$order['id']}, 退款金额:{$order['final_fee']}元",
				'we7_wmall'
			);
			load()->model('mc');
			mc_credit_update($order['uid'], 'credit2', $order['final_fee'], $log);
			$update = array(
				'refund_status' => 3,
				'refund_success_time' => TIMESTAMP,
				'refund_account' => '支付用户的平台余额',
				'refund_channel' => 'ORIGINAL'
			);
			pdo_update('tiny_wmall_errander_order', $update, array('uniacid' => $_W['uniacid'], 'id' => $order['id']));
			errander_order_insert_refund_log($order['id'], 'success');
			errander_order_refund_notice($order['id'], 'success');
		}
		return true;
	} elseif($order['pay_type'] == 'wechat') {
		mload()->classs('wxpay');
		$pay = new WxPay();
		$params = array(
			'total_fee' => $order['final_fee'] * 100,
			'refund_fee' => $order['final_fee'] * 100,
			'out_trade_no' => $order['order_sn'],
			'out_refund_no' => $order['refundout__no'],
		);
		$response = $pay->payRefund_build($params);
		if(is_error($response)) {
			return error(-1, $response['message']);
		}
		$update = array(
			'refund_status' => 2,
		);
		pdo_update('tiny_wmall_errander_order', $update, array('uniacid' => $_W['uniacid'], 'id' => $order['id']));
		return true;
	} elseif($order['pay_type'] == 'alipay') {
		mload()->classs('alipay');
		$pay = new AliPay();
		$params = array(
			'refund_fee' => $order['final_fee'],
			'out_trade_no' => $order['order_sn'],
		);
		$response = $pay->payRefund_build($params);
		if(is_error($response)) {
			return error(-1, $response['message']);
		}
		$update = array(
			'refund_status' => 3,
			'refund_success_time' => TIMESTAMP,
			'refund_account' => '支付用户的平台余额',
			'refund_channel' => 'ORIGINAL'
		);
		pdo_update('tiny_wmall_errander_order', $update, array('uniacid' => $_W['uniacid'], 'id' => $order['id']));
		errander_order_insert_refund_log($order['id'], 'success');
		errander_order_refund_notice($order['id'], 'success');
		return true;
	}
}

function errander_order_query_payrefund($id) {
	global $_W;
	$order = errander_order_fetch($id);
	if(empty($order)) {
		return error(-1, '订单不存在或已删除');
	}
	if(empty($order)) {
		return error(-1, '订单不存在或已删除');
	}
	if($order['refund_status'] != 2) {
		return true;
	}
	if($order['refund_status'] == 3) {
		return error(-1, '退款已成功, 不能发起退款');
	}
	if($order['pay_type'] == 'wechat') {
		//只有微信需要查询,余额和支付宝不需要
		mload()->classs('wxpay');
		$pay = new WxPay();
		$response = $pay->payRefund_query(array('out_refund_no' => $order['refund_out_no']));
		if(is_error($response)) {
			return $response;
		}
		$wechat_status = $pay->payRefund_status();
		$update = array(
			'refund_status' => $wechat_status[$response['refund_status_0']]['value'],
		);
		if($response['refund_status_0'] == 'SUCCESS') {
			$update['refund_channel'] = $response['refund_channel_0'];
			$update['refund_account'] = $response['refund_recv_accout_0'];
			$update['refund_success_time'] = TIMESTAMP;
			pdo_update('tiny_wmall_errander_order', $update, array('uniacid' => $_W['uniacid'], 'id' => $order['id']));
			errander_order_insert_refund_log($order['id'], 'success');
			errander_order_refund_notice($order['id'], 'success');
		} else {
			pdo_update('tiny_wmall_errander_order', $update, array('uniacid' => $_W['uniacid'], 'id' => $order['id']));
		}
		return true;
	}
	return true;
}

