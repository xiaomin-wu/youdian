<?php

defined('IN_IA') or die('Access Denied');
function order_fetch($id)
{
	global $_W;
	$id = intval($id);
	$order = pdo_fetch('SELECT * FROM ' . tablename('tiny_wmall_plus_order') . ' WHERE uniacid = :aid AND id = :id', array(':aid' => $_W['uniacid'], ':id' => $id));
	if (empty($order)) {
		return false;
	}
	$order_status = order_status();
	$pay_types = order_pay_types();
	$order_types = order_types();
	$order['order_type_cn'] = $order_types[$order['order_type']]['text'];
	$order['status_cn'] = $order_status[$order['status']]['text'];
	if (empty($order['is_pay'])) {
		$order['pay_type_cn'] = '未支付';
	} else {
		$order['pay_type_cn'] = !empty($pay_types[$order['pay_type']]['text']) ? $pay_types[$order['pay_type']]['text'] : '其他支付方式';
	}
	if (empty($order['delivery_time'])) {
		$order['delivery_time'] = '尽快送出';
	}
	if ($order['order_type'] == 3) {
		$table = pdo_get('tiny_wmall_plus_tables', array('uniacid' => $_W['uniacid'], 'id' => $order['table_id']));
		$order['table'] = $table;
	} elseif ($order['order_type'] == 4) {
		$reserve_type = order_reserve_type();
		$order['reserve_type_cn'] = $reserve_type[$order['reserve_type']]['text'];
		$category = pdo_get('tiny_wmall_plus_tables_category', array('uniacid' => $_W['uniacid'], 'id' => $order['table_cid']));
		$order['table_category'] = $category;
	}
	$order['pay_type_class'] = '';
	if ($order['is_pay'] == 1) {
		$order['pay_type_class'] = 'have-pay';
		if ($order['pay_type'] == 'delivery') {
			$order['pay_type_class'] = 'delivery-pay';
		}
	}
	return $order;
}
function order_fetch_goods($oid, $print_lable = '')
{
	global $_W;
	$oid = intval($oid);
	$condition = 'WHERE uniacid = :uniacid AND oid = :oid';
	if (!empty($print_lable)) {
		$condition .= " AND print_label in ({$print_lable})";
	}
	$params = array(':uniacid' => $_W['uniacid'], ':oid' => $oid);
	$data = pdo_fetchall('SELECT * FROM ' . tablename('tiny_wmall_plus_order_stat') . $condition, $params);
	return $data;
}
function order_fetch_discount($id)
{
	global $_W;
	$data = pdo_getall('tiny_wmall_plus_order_discount', array('uniacid' => $_W['uniacid'], 'oid' => $id));
	return $data;
}
function order_place_again($sid, $order_id)
{
	global $_W;
	$order = order_fetch($order_id);
	if (empty($order)) {
		return false;
	}
	$isexist = pdo_fetchcolumn('SELECT id FROM ' . tablename('tiny_wmall_plus_order_cart') . " WHERE uniacid = :aid AND sid = :sid AND uid = :uid", array(':aid' => $_W['uniacid'], ':sid' => $sid, ':uid' => $_W['member']['uid']));
	$data = array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'uid' => $_W['member']['uid'], 'groupid' => $_W['member']['groupid'], 'num' => $order['num'], 'price' => $order['price'], 'original_data' => $order['data'], 'addtime' => TIMESTAMP);
	if (empty($isexist)) {
		pdo_insert('tiny_wmall_plus_order_cart', $data);
	} else {
		pdo_update('tiny_wmall_plus_order_cart', $data, array('uniacid' => $_W['uniacid'], 'id' => $isexist, 'uid' => $_W['member']['uid']));
	}
	$data['original_data'] = iunserializer($order['data']);
	return $data;
}
function order_insert_discount($id, $sid, $discount_data)
{
	global $_W;
	if (empty($discount_data)) {
		return false;
	}
	if (!empty($discount_data['token'])) {
		pdo_update('tiny_wmall_plus_activity_coupon_record', array('status' => 2, 'usetime' => TIMESTAMP, 'order_id' => $id), array('uniacid' => $_W['uniacid'], 'id' => $discount_data['token']['recordid']));
	}
	foreach ($discount_data as $data) {
		$insert = array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'oid' => $id, 'type' => $data['type'], 'name' => $data['name'], 'icon' => $data['icon'], 'note' => $data['text'], 'fee' => $data['value']);
		pdo_insert('tiny_wmall_plus_order_discount', $insert);
	}
	return true;
}
function order_update_bill($order_id)
{
	global $_W;
	$order = pdo_get('tiny_wmall_plus_order', array('uniacid' => $_W['uniacid'], 'id' => $order_id));
	if (empty($order)) {
		return error(-1, '订单不存在或已删除');
	}
	$total_fee = $order['price'] + $order['box_price'] + $order['pack_fee'] + $order['serve_fee'] + $order['delivery_fee'];
	$store_order_fee = $order['price'] + $order['box_price'] + $order['pack_fee'] + $order['serve_fee'];
	if ($order['delivery_type'] == 1) {
		$store_order_fee += $order['delivery_fee'];
	}
	$store_discount_fee = $order['discount_fee'];
	$platform_discount_fee = 0;
	if ($order['vip_free_delivery_fee'] == 1) {
		$platform_discount_fee = $order['delivery_fee'];
		$store_discount_fee -= $order['delivery_fee'];
	}
	$store_order_fee -= $store_discount_fee;
	$account = store_account($order['sid']);
	$plateform_delivery_fee = 0;
	if ($order['order_type'] <= 2) {
		$plateform_serve_rate = $account['takeout_serve_rate'];
		if ($order['delivery_type'] == 2) {
			$plateform_delivery_fee = $order['delivery_fee'];
		}
	} else {
		$plateform_serve_rate = $account['instore_serve_rate'];
	}
	$platform_serve_fee = round($store_order_fee * ($plateform_serve_rate / 100), 2);
	$store_final_fee = $store_order_fee - $platform_serve_fee;
	$data = array('store_discount_fee' => $store_discount_fee, 'plateform_discount_fee' => $platform_discount_fee, 'plateform_delivery_fee' => $plateform_delivery_fee, 'plateform_deliveryer_fee' => 0, 'plateform_serve_fee' => $platform_serve_fee, 'store_final_fee' => $store_final_fee, 'plateform_serve_rate' => $plateform_serve_rate, 'stat_year' => date('Y', $order['addtime']), 'stat_month' => date('Ym', $order['addtime']), 'stat_day' => date('Ymd', $order['addtime']));
	pdo_update('tiny_wmall_plus_order', $data, array('uniacid' => $_W['uniacid'], 'id' => $order['id']));
	return true;
}
function order_insert_status_log($id, $type, $note = '')
{
	global $_W;
	if (empty($type)) {
		return false;
	}
	mload()->model('store');
	$order = order_fetch($id);
	$config_takeout = $_W['we7_wmall_plus']['config']['takeout'];
	$notes = array('place_order' => array('status' => 1, 'title' => '订单提交成功', 'note' => "单号:{$order['ordersn']},请耐心等待商家确认", 'ext' => array(array('key' => 'pay_time_limit', 'title' => '待支付', 'note' => "请在订单提交后{$config_takeout['pay_time_limit']}分钟内完成支付"))), 'handle' => array('status' => 2, 'title' => '商户已经确认订单', 'note' => '正在为您准备商品'), 'delivery_wait' => array('status' => 3, 'title' => '商品已准备就绪', 'note' => '商品已准备就绪,正在分配配送员'), 'delivery_ing' => array('status' => 3, 'title' => '商品已准备就绪', 'note' => '商品已准备就绪,商家正在为您配送中'), 'delivery_assign' => array('status' => 4, 'title' => '已分配配送员', 'note' => ''), 'delivery_instore' => array('status' => 12, 'title' => '配送员已到店', 'note' => '配送员已到店, 取货完成, 骑士将骑上战马为您急速送达'), 'end' => array('status' => 5, 'title' => '订单已完成', 'note' => '任何意见和吐槽,都欢迎联系我们'), 'cancel' => array('status' => 6, 'title' => '订单已取消', 'note' => ''), 'pay' => array('status' => 7, 'title' => '订单已支付', 'note' => '支付成功.付款时间:' . date('Y-m-d H:i:s'), 'ext' => array(array('key' => 'handle_time_limit', 'title' => '等待商户接单', 'note' => "{$config_takeout['handle_time_limit']}分钟内商户未接单,将自动取消订单"))), 'remind' => array('status' => 8, 'title' => '商家已收到催单', 'note' => '商家会尽快回复您的催单请求'), 'remind_reply' => array('status' => 9, 'title' => '商家回复了您的催单', 'note' => ''), 'delivery_success' => array('status' => 10, 'title' => '订单配送完成', 'note' => ''), 'delivery_fail' => array('status' => 10, 'title' => '订单配送失败', 'note' => ''));
	$title = $notes[$type]['title'];
	$note = $note ? $note : $notes[$type]['note'];
	$data = array('uniacid' => $_W['uniacid'], 'oid' => $id, 'status' => $notes[$type]['status'], 'type' => $type, 'title' => $title, 'note' => $note, 'addtime' => TIMESTAMP);
	pdo_insert('tiny_wmall_plus_order_status_log', $data);
	if (!empty($notes[$type]['ext'])) {
		foreach ($notes[$type]['ext'] as $val) {
			if ($val['key'] == 'pay_time_limit' && !$config_takeout['pay_time_limit']) {
				unset($val['note']);
			}
			if ($val['key'] == 'handle_time_limit' && !$config_takeout['handle_time_limit']) {
				unset($val['note']);
			}
			$data = array('uniacid' => $_W['uniacid'], 'oid' => $id, 'title' => $val['title'], 'note' => $val['note'], 'addtime' => TIMESTAMP);
			pdo_insert('tiny_wmall_plus_order_status_log', $data);
		}
	}
	return true;
}
function order_fetch_status_log($id)
{
	global $_W;
	$data = pdo_fetchall("SELECT * FROM " . tablename('tiny_wmall_plus_order_status_log') . ' WHERE uniacid = :uniacid and oid = :oid order by id asc', array(':uniacid' => $_W['uniacid'], ':oid' => $id), 'id');
	return $data;
}
function order_fetch_refund_log($id)
{
	global $_W;
	$data = pdo_fetchall("SELECT * FROM " . tablename('tiny_wmall_plus_order_refund_log') . ' WHERE uniacid = :uniacid and oid = :oid and order_type = :order_type order by id asc', array(':uniacid' => $_W['uniacid'], ':oid' => $id, ':order_type' => 'order'), 'id');
	return $data;
}
function order_print($id)
{
	global $_W;
	$order = order_fetch($id);
	if (empty($order)) {
		return error(-1, '订单不存在');
	}
	mload()->model('store');
	$sid = intval($order['sid']);
	$store = store_fetch($order['sid'], array('title'));
	$prints = pdo_fetchall('SELECT * FROM ' . tablename('tiny_wmall_plus_printer') . ' WHERE uniacid = :aid AND sid = :sid AND status = 1', array(':aid' => $_W['uniacid'], ':sid' => $sid));
	if (empty($prints)) {
		return error(-1, '没有有效的打印机');
	}
	mload()->model('print');
	$num = 0;
	foreach ($prints as $li) {
		if (!empty($li['print_no'])) {
			$content = array('title' => "<CB>#{$order['serial_sn']} {$store['title']}</CB>");
			if ($order['order_type'] >= 3) {
				$content[] = "<CB>{$order['table']['title']}桌</CB>";
			}
			if ($li['is_print_all'] == 1) {
				if (!empty($li['print_header'])) {
					$content[] = $li['print_header'];
				}
				$content[] = '名称　　　数量　　单价　　金额';
				$content[] = '******************************';
				$order['goods'] = order_fetch_goods($order['id'], $li['print_label']);
				foreach ($order['goods'] as $di) {
					if ($order['order_type'] >= 3) {
						$content[] = "<L>{$di['goods_title']}</L>";
					} else {
						$content[] = "{$di['goods_title']}";
					}
					$str = '';
					$str .= '　　　　　X' . str_pad($di['goods_num'], '7', ' ', STR_PAD_RIGHT);
					$str .= '' . str_pad($di['goods_unit_price'], '7', ' ', STR_PAD_RIGHT);
					$str .= ' ' . str_pad($di['goods_price'], '5', ' ', STR_PAD_RIGHT);
					$content[] = $str;
				}
				$content[] = '******************************';
				$content[] = "订单类型:{$order['order_type_cn']}";
				$content[] = "订单　号:{$order['ordersn']}";
				$content[] = "支付方式:{$order['pay_type_cn']}";
				if ($order['box_price'] > 0) {
					$content[] = "餐盒　费:{$order['box_price']}元";
				}
				if ($order['pack_fee'] > 0) {
					$content[] = "包装　费:{$order['pack_fee']}元";
				}
				if ($order['delivery_fee'] > 0) {
					$content[] = "配送　费:{$order['delivery_fee']}元";
				}
				if ($order['order_type'] >= 3) {
					$content[] = '';
					$content[] = "合　　计:<L>{$order['total_fee']}元</L>";
					$content[] = '';
					if ($order['discount_fee'] > 0) {
						$content[] = "线上优惠:<L>-{$order['discount_fee']}元</L>";
						$content[] = '';
						$content[] = "实际支付:<L>{$order['final_fee']}元</L>";
						$content[] = '';
					}
				} else {
					$content[] = "合　　计:{$order['total_fee']}元";
					if ($order['discount_fee'] > 0) {
						$content[] = "线上优惠:-{$order['discount_fee']}元";
						$content[] = "实际支付:{$order['final_fee']}元";
					}
				}
				if ($order['order_type'] == 1) {
					$content[] = "下单　人:{$order['username']}";
					$content[] = "联系电话:{$order['mobile']}";
					$content[] = "配送地址:{$order['address']}";
					$content[] = "配送时间:{$order['delivery_day']} {$order['delivery_time']}";
				} elseif ($order['order_type'] == 2) {
					$content[] = "下单　人:{$order['username']}";
					$content[] = "联系电话:{$order['mobile']}";
				} elseif ($order['order_type'] == 3) {
					$content[] = "来客人数:{$order['person_num']}";
				} elseif ($order['order_type'] == 4) {
					$content[] = "预定时间:{$order['reserve_time']}";
					$content[] = "桌台类型:{$order['table_category']['title']}~{$order['table']['title']}桌";
				}
				$content[] = "下单时间:" . date('Y-m-d H:i', $order['addtime']);
				if (!empty($order['invoice'])) {
					$content[] = "发票信息:{$order['invoice']}";
				}
				if (!empty($order['note'])) {
					$content[] = "备　　注:{$order['note']}";
				}
				if (!empty($li['print_footer'])) {
					$content[] = $li['print_footer'];
				}
				if ($li['qrcode_type'] == 'delivery_assign') {
					$li['qrcode_link'] = murl('entry', array('m' => 'we7_wmall_plus', 'do' => 'dyorder', 'id' => $order['id'], 'op' => 'detail', 'r' => 'collect'), true, true);
				}
				if (!empty($li['qrcode_link'])) {
					$content['qrcode'] = "<QR>{$li['qrcode_link']}</QR>";
				}
				$content[] = implode('', array("\x1b", "\x64", "\x01", "\x1b", "\x70", "\x30", "\x1e", "\x78"));
				if (($li['type'] == 'feiyin' || $li['type'] == 'AiPrint') && $li['print_nums'] > 0) {
					for ($i = 0; $i < $li['print_nums']; $i++) {
						$status = print_add_order($li['type'], $li['print_no'], $li['key'], $li['member_code'], $li['api_key'], $content, $li['print_nums'], $order['ordersn'] . random(10, true));
						if (!is_error($status)) {
							$num++;
						}
					}
				} else {
					$status = print_add_order($li['type'], $li['print_no'], $li['key'], $li['member_code'], $li['api_key'], $content, $li['print_nums'], $order['ordersn'] . random(10, true));
					if (!is_error($status)) {
						$num += $li['print_nums'];
					}
				}
			} else {
				$content = array("订单　号:{$order['ordersn']}", "下单时间:" . date('Y-m-d H:i', $order['addtime']));
				if ($order['order_type'] == 1) {
					$content[] = "订单类型:外卖单";
					$content[] = "配送时间:{$order['delivery_day']} {$order['delivery_time']}";
				} elseif ($order['order_type'] == 2) {
					$content[] = "订单类型:自提单";
					$content[] = "自提时间:{$order['delivery_day']} {$order['delivery_time']}";
				} elseif ($order['order_type'] == 3) {
					$content[] = "订单类型:堂食单";
					$content[] = "桌　　号:{$order['table']['title']}桌";
				} elseif ($order['order_type'] == 4) {
					$content[] = "订单类型:预定单";
					$content[] = "预定时间:{$order['reserve_time']}";
				}
				$content[] = '名称　　　数量　　单价　　金额';
				$content[] = '******************************';
				$order['goods'] = order_fetch_goods($order['id'], $li['print_label']);
				foreach ($order['goods'] as $di) {
					$str = '';
					$str .= '　　　　　X' . str_pad($di['goods_num'], '7', ' ', STR_PAD_RIGHT);
					$str .= '' . str_pad($di['goods_unit_price'], '7', ' ', STR_PAD_RIGHT);
					$str .= ' ' . str_pad($di['goods_price'], '5', ' ', STR_PAD_RIGHT);
					$goods_info = array($di['goods_title'], $str, '******************************');
					$content_merge = array_merge($content, $goods_info);
					if (($li['type'] == 'feiyin' || $li['type'] == 'AiPrint') && $li['print_nums'] > 0) {
						for ($i = 0; $i < $li['print_nums']; $i++) {
							$status = print_add_order($li['type'], $li['print_no'], $li['key'], $li['member_code'], $li['api_key'], $content_merge, $li['print_nums'], $order['ordersn'] . random(10, true));
							if (!is_error($status)) {
								$num++;
							}
						}
					} else {
						$status = print_add_order($li['type'], $li['print_no'], $li['key'], $li['member_code'], $li['api_key'], $content_merge, $li['print_nums'], $order['ordersn'] . random(10, true));
						if (!is_error($status)) {
							$num += $li['print_nums'];
						}
					}
				}
			}
		}
	}
	if ($num > 0) {
		pdo_query('UPDATE ' . tablename('tiny_wmall_plus_order') . " SET print_nums = print_nums + {$num} WHERE uniacid = {$_W['uniacid']} AND id = {$order['id']}");
	} else {
		return error(-1, '发送打印指令失败。没有有效的打印机或没有开启打印机');
	}
	return true;
}
function order_status()
{
	$data = array('0' => array('css' => '', 'text' => '所有', 'color' => ''), '1' => array('css' => 'label label-default', 'text' => '待确认', 'color' => '', 'color' => ''), '2' => array('css' => 'label label-info', 'text' => '处理中', 'color' => 'color-primary'), '3' => array('css' => 'label label-warning', 'text' => '待配送', 'color' => 'color-warning'), '4' => array('css' => 'label label-warning', 'text' => '配送中', 'color' => 'color-warning'), '5' => array('css' => 'label label-success', 'text' => '已完成', 'color' => 'color-success'), '6' => array('css' => 'label label-danger', 'text' => '已取消', 'color' => 'color-danger'));
	return $data;
}
function order_trade_status()
{
	$data = array('1' => array('css' => 'label label-success', 'text' => '交易成功'), '2' => array('css' => 'label label-warning', 'text' => '交易进行中'), '3' => array('css' => 'label label-danger', 'text' => '交易失败'), '4' => array('css' => 'label label-default', 'text' => '交易关闭'));
	return $data;
}
function order_trade_type()
{
	$data = array('1' => array('css' => 'label label-success', 'text' => '订单入账'), '2' => array('css' => 'label label-danger', 'text' => '申请提现'), '3' => array('css' => 'label label-default', 'text' => '其他变动'));
	return $data;
}
function order_delivery_status()
{
	$data = array('0' => array('css' => '', 'text' => '', 'color' => ''), '3' => array('css' => 'label label-warning', 'text' => '待配送', 'color' => 'color-warning'), '4' => array('css' => 'label label-warning', 'text' => '配送中', 'color' => 'color-warning'), '5' => array('css' => 'label label-success', 'text' => '配送成功', 'color' => 'color-success'), '6' => array('css' => 'label label-danger', 'text' => '配送失败', 'color' => 'color-danger'), '7' => array('css' => 'label label-danger', 'text' => '待取货', 'color' => 'color-danger'));
	return $data;
}
function order_types()
{
	$data = array('1' => array('css' => 'label label-success', 'text' => '外卖', 'color' => 'color-success'), '2' => array('css' => 'label label-danger', 'text' => '自提', 'color' => 'color-danger'), '3' => array('css' => 'label label-warning', 'text' => '店内', 'color' => 'color-info'), '4' => array('css' => 'label label-info', 'text' => '预定', 'color' => 'color-info'));
	return $data;
}
function order_reserve_type()
{
	$data = array('table' => array('css' => 'label label-success', 'text' => '只订座', 'color' => 'color-success'), 'order' => array('css' => 'label label-danger', 'text' => '预定商品', 'color' => 'color-danger'));
	return $data;
}
function order_status_notice($id, $status, $note = '')
{
	global $_W;
	$status_arr = array('handle', 'delivery_assign', 'delivery_instore', 'delivery_ing', 'end', 'cancel', 'pay', 'remind', 'reply_remind', 'delivery_notice');
	if (!in_array($status, $status_arr)) {
		return false;
	}
	$type = $status;
	$order = order_fetch($id);
	if (empty($order)) {
		return error(-1, '订单不存在');
	}
	$store = store_fetch($order['sid'], array('title'));
	$deliveryer = array();
	if (!empty($order['deliveryer_id'])) {
		$deliveryer = pdo_get('tiny_wmall_plus_deliveryer', array('id' => $order['deliveryer_id']));
	}
	$config_sms = $_W['we7_wmall_plus']['config']['sms'];
	if ($config_sms['notify_member_order']['status'] == 1 && !empty($order['mobile'])) {
		if (empty($note)) {
			$note = array();
		}
		$router = array('pay' => '已支付', 'handle' => '商家已接单,正在为您准备商品中', 'delivery_assign' => "已分配配送员,配送员:{$deliveryer['title']},手机号:{$deliveryer['mobile']}", 'delivery_instore' => "配送员已取餐,配送员:{$deliveryer['title']},手机号:{$deliveryer['mobile']}", 'end' => '已完成', 'cancel' => '已取消', 'delivery_notice' => "已到达你下单时填写的送货地址,配送员手机号:【{$deliveryer['mobile']}】, 请注意接听配送员来电");
		$content = array('username' => $order['username'], 'store' => $store['title'], 'status' => $router[$type]);
		mload()->model('sms');
		$result = sms_send($config_sms['notify_member_order']['tpl'], $order['mobile'], $content, $order['sid']);
	}
	if (!empty($order['openid'])) {
		$acc = WeAccount::create($order['acid']);
		if ($type == 'pay') {
			$title = '您的订单已付款';
			$remark = array("门店名称: {$store['title']}", "订单类型: {$order['order_type_cn']}", "支付方式: {$order['pay_type_cn']}", "支付时间: " . date('Y-m-d H: i', time()));
		}
		if ($type == 'handle') {
			$title = '商家已接单,正在准备商品中...';
			$remark = array("门店名称: {$store['title']}", "订单类型: {$order['order_type_cn']}", "处理时间: " . date('Y-m-d H: i', time()));
		}
		if ($type == 'delivery_assign') {
			$title = '您的订单正在为您配送中';
			$remark = array("门店名称: {$store['title']}", "订单类型: {$order['order_type_cn']}");
			$end_remark = "配送员已抢单, 正赶至商家取货, 骑士将骑上战马为您急速送达, 请保持电话畅通";
		}
		if ($type == 'delivery_instore') {
			$title = '配送员已取货，正在配送中';
			$remark = array("门店名称: {$store['title']}", "订单类型: {$order['order_type_cn']}");
			$end_remark = "配送员已取货，正在为您配送中。";
		}
		if ($type == 'delivery_notice') {
			$title = "配送员【{$deliveryer['title']}】已达到你下单时填写的送货地址, 配送员手机号:【{$deliveryer['mobile']}】, 请注意接听配送员来电";
			$remark = array("门店名称: {$store['title']}", "配送员: {$deliveryer['title']}", "手机号: {$deliveryer['mobile']}");
			unset($note);
		}
		if ($type == 'end') {
			$title = '订单处理完成';
			$remark = array("门店名称: {$store['title']}", "订单类型: {$order['order_type_cn']}", "完成时间: " . date('Y-m-d H: i', time()));
			$end_remark = "您的订单已处理完成, 如对商品有不满意或投诉请联系客服:{$_W['we7_wmall_plus']['config']['mobile']},欢迎您下次光临.戳这里记得给我们的服务评价.";
		}
		if ($type == 'cancel') {
			$title = '订单已取消';
			$remark = array("门店名称: {$store['title']}", "订单类型: {$order['order_type_cn']}", "取消时间: " . date('Y-m-d H: i', time()));
		}
		if ($type == 'reply_remind') {
			$title = '订单催单有新的回复';
			$remark = array("门店名称: {$store['title']}", "订单类型: {$order['order_type_cn']}", "回复时间: " . date('Y-m-d H: i', time()));
		}
		if ($type == 'reserve_order_pay') {
			$title = '你的预定单已支付';
			$remark = array("门店名称: {$store['title']}", "订单类型: {$order['order_type_cn']}", "支付方式: {$order['pay_type_cn']}", "预定时间: {$order['reserve_time']}", "预定桌台: {$order['table_category']['title']}", "预定类型: {$order['reserve_type_cn']}");
		}
		if (!empty($note)) {
			if (!is_array($note)) {
				$remark[] = $note;
			} else {
				$remark[] = implode("\n", $note);
			}
		}
		if (!empty($end_remark)) {
			$remark[] = $end_remark;
		}
		$url = $_W['siteroot'] . 'app' . ltrim(murl('entry', array('do' => 'order', 'm' => 'we7_wmall_plus', 'op' => 'detail', 'sid' => $order['sid'], 'id' => $order['id'])), '.');
		$remark = implode("\n", $remark);
		$send = tpl_format($title, $order['ordersn'], $order['status_cn'], $remark);
		$status = $acc->sendTplNotice($order['openid'], $_W['we7_wmall_plus']['config']['public_tpl'], $send, $url);
		return $status;
	}
	return true;
}
function order_clerk_notice($id, $type, $note = '')
{
	global $_W;
	$order = order_fetch($id);
	if (empty($order)) {
		return error(-1, '订单不存在或已删除');
	}
	$store = store_fetch($order['sid'], array('title', 'id'));
	mload()->model('clerk');
	$clerks = clerk_fetchall($order['sid']);
	if (empty($clerks)) {
		return false;
	}
	$acc = WeAccount::create($order['acid']);
	if ($type == 'place_order') {
		$title = '您的店铺有新的订单,订单号: ' . $order['ordersn'];
		$remark = array("门店名称: {$store['title']}", "下单时间: " . date('Y-m-d H:i', $order['addtime']), "总金　额: {$order['final_fee']}", "支付状态: {$order['pay_type_cn']}", "订单类型: {$order['order_type_cn']}");
	} elseif ($type == 'remind') {
		$title = '该订单有催单, 请请尽快回复';
		$remark = array("门店名称: {$store['title']}", "订单类型: {$order['order_type_cn']}", "下单时间: " . date('Y-m-d H:i', $order['addtime']));
	} elseif ($type == 'collect') {
		$title = "您订单号为: {$order['ordersn']}的外卖单已被配送员接单";
		$remark = array("门店名称: {$store['title']}", "下单时间: " . date('Y-m-d H:i', $order['addtime']));
	} elseif ($type == 'store_order_place') {
		$title = '您的店铺有新的店内订单,订单号: ' . $order['ordersn'];
		$remark = array("门店名称: {$store['title']}", "下单时间: " . date('Y-m-d H:i', $order['addtime']), "桌　　号: " . $order['table']['title'] . '桌', "客人数量: " . $order['person_num'] . '人');
	} elseif ($type == 'store_order_pay') {
		$title = "订单号为{$order['ordersn']}的订单已付款";
		$remark = array("门店名称: {$store['title']}", "下单时间: " . date('Y-m-d H:i', $order['addtime']), "桌　　号: " . $order['table']['title'] . '桌', "客人数量: " . $order['person_num'] . '人');
	} elseif ($type == 'reserve_order_pay') {
		$title = "您有新的预定订单,订单号{$order['ordersn']}, 已付款, 支付方式:{$order['pay_type_cn']}";
		$remark = array("门店名称: {$store['title']}", "下单时间: " . date('Y-m-d H:i', $order['addtime']), "预定时间: " . $order['reserve_time'], "预定类型: " . $order['reserve_type_cn'], "预定桌台: " . $order['table_category']['title'], "预定　人: " . $order['username'], "手机　号: " . $order['mobile']);
	}
	if (!empty($note)) {
		if (!is_array($note)) {
			$remark[] = $note;
		} else {
			$remark[] = implode("\n", $note);
		}
	}
	$url = $_W['siteroot'] . 'app' . ltrim(murl('entry', array('do' => 'mgorder', 'm' => 'we7_wmall_plus', 'op' => 'detail', 'sid' => $order['sid'], 'id' => $order['id'])), '.');
	$remark = implode("\n", $remark);
	$send = tpl_format($title, $order['ordersn'], $order['status_cn'], $remark);
	mload()->model('sms');
	foreach ($clerks as $clerk) {
		if (!empty($clerk['mobile']) && in_array($type, array('place_order', 'store_order_place'))) {
			sms_singlecall($clerk['mobile'], array('name' => $clerk['title'], 'store' => $store['title'], 'price' => $order['final_fee']), 'clerk');
		}
		$acc->sendTplNotice($clerk['openid'], $_W['we7_wmall_plus']['config']['public_tpl'], $send, $url);
	}
	return true;
}
function order_deliveryer_notice($id, $type, $deliveryer_id = 0, $note = '')
{
	global $_W;
	$order = order_fetch($id);
	if (empty($order)) {
		return error(-1, '订单不存在或已删除');
	}
	mload()->model('deliveryer');
	$store = store_fetch($order['sid'], array('title', 'id', 'delivery_mode'));
	if ($deliveryer_id > 0) {
		$deliveryers[] = deliveryer_fetch($deliveryer_id);
	} else {
		if ($store['delivery_mode'] == 2) {
			$config_takeout = $_W['we7_wmall_plus']['config']['takeout'];
			$filter = array();
			if (!$config_takeout['over_collect_max_notify'] && $config_takeout['deliveryer_collect_max'] > 0) {
				$filter = array('order_takeout_num' => $config_takeout['deliveryer_collect_max']);
			}
			$deliveryers = deliveryer_fetchall(0, $filter);
		} else {
			if ($deliveryer_id > 0) {
				$deliveryers[] = deliveryer_fetch($deliveryer_id);
			} else {
				$deliveryers = deliveryer_fetchall($order['sid']);
			}
		}
	}
	if (empty($deliveryers)) {
		return false;
	}
	$acc = WeAccount::create($order['acid']);
	if ($type == 'new_delivery') {
		$title = "店铺{$store['title']}有新的外卖配送订单, 配送地址为{$order['address']}, 快去处理吧";
		$remark = array("门店名称: {$store['title']}", "下单时间: " . date('Y-m-d H:i', $order['addtime']), "总金　额: {$order['final_fee']}", "支付状态: {$order['pay_type_cn']}", "下单　人: {$order['username']}", "联系手机: {$order['mobile']}", "送货地址: {$order['address']}", "订单类型: " . ($store['delivery_mode'] == 1 ? '店内配送单' : '平台配送单'));
		if (!empty($note)) {
			$remark[] = $note;
		}
		$remark = implode("\n", $remark);
		$url = $_W['siteroot'] . 'app' . ltrim(murl('entry', array('do' => 'dyorder', 'm' => 'we7_wmall_plus', 'op' => 'detail', 'id' => $order['id'])), '.');
	} else {
		if ($type == 'delivery_wait') {
			$title = "店铺{$store['title']}有新的配送订单, 配送地址为{$order['address']}, 快去抢单吧";
			$remark = array("门店名称: {$store['title']}", "下单时间: " . date('Y-m-d H:i', $order['addtime']), "下单　人: {$order['username']}", "联系手机: {$order['mobile']}", "送货地址: {$order['address']}", "订单类型: " . ($store['delivery_mode'] == 1 ? '店内配送单' : '平台配送单'));
			if (!empty($note)) {
				$remark[] = $note;
			}
			$remark = implode("\n", $remark);
			$url = $_W['siteroot'] . 'app' . ltrim(murl('entry', array('do' => 'dyorder', 'm' => 'we7_wmall_plus', 'op' => 'list')), '.');
			Jpush_deliveryer_send('您有新的外卖待抢订单', $title, array('voice_text' => $title, 'redirect_type' => 'takeout', 'redirect_extra' => 3));
		} else {
			if ($type == 'cancel') {
				$title = "收货地址为{$order['address']}, 收货人为{$order['username']}的订单已取消,请及时调整配送顺序";
				$remark = array("门店名称: {$store['title']}", "下单时间: " . date('Y-m-d H:i', $order['addtime']), "下单　人: {$order['username']}", "联系手机: {$order['mobile']}", "送货地址: {$order['address']}", "订单类型: " . ($store['delivery_mode'] == 1 ? '店内配送单' : '平台配送单'));
				if (!empty($note)) {
					$remark[] = $note;
				}
				$remark = implode("\n", $remark);
				$url = $_W['siteroot'] . 'app' . ltrim(murl('entry', array('do' => 'dyorder', 'm' => 'we7_wmall_plus', 'op' => 'detail', 'id' => $order['id'])), '.');
			}
		}
	}
	$send = tpl_format($title, $order['ordersn'], $order['status_cn'], $remark);
	mload()->model('sms');
	foreach ($deliveryers as $deliveryer) {
		if (!empty($deliveryer['deliveryer']['mobile'])) {
			sms_singlecall($deliveryer['deliveryer']['mobile'], array('name' => $deliveryer['deliveryer']['title'], 'store' => $store['title']), 'deliveryer');
		}
		$acc->sendTplNotice($deliveryer['deliveryer']['openid'], $_W['we7_wmall_plus']['config']['public_tpl'], $send, $url);
		if ($type == 'new_delivery') {
			if (!empty($deliveryer['deliveryer']['token'])) {
				$audience = array('alias' => array($deliveryer['deliveryer']['token']));
				Jpush_deliveryer_send('您有新的外卖配送订单', $title, array('voice_text' => $title, 'redirect_type' => 'takeout', 'redirect_extra' => 7), $audience);
			}
		} elseif ($type == 'cancel') {
			if (!empty($deliveryer['deliveryer']['token'])) {
				$audience = array('alias' => array($deliveryer['deliveryer']['token']));
				Jpush_deliveryer_send('订单取消通知', $title, array('voice_text' => $title, 'redirect_type' => 'takeout', 'redirect_extra' => 3), $audience);
			}
		}
	}
	return true;
}
function order_refund_fetch($order_id)
{
	global $_W;
	$refund = pdo_get('tiny_wmall_plus_order_refund', array('uniacid' => $_W['uniacid'], 'order_id' => $order_id));
	if (empty($refund)) {
		return error(-1, '退款记录不存在');
	}
	$refund_status = order_refund_status();
	$refund_channel = order_refund_channel();
	$refund['status_cn'] = $refund_status[$refund['status']]['text'];
	$refund['channel_cn'] = $refund_channel[$refund['channel']]['text'];
	return $refund;
}
function order_refund_notice($order_id, $type, $note = '')
{
	global $_W;
	$order = order_fetch($order_id);
	if (empty($order)) {
		return error(-1, '订单不存在或已删除');
	}
	$store = store_fetch($order['sid'], array('title', 'id'));
	$refund = order_refund_fetch($order_id);
	$acc = WeAccount::create($order['acid']);
	mload()->model('clerk');
	$clerks = clerk_fetchall($order['sid']);
	if ($type == 'apply') {
		$maneger = $_W['we7_wmall_plus']['config']['manager'];
		if (!empty($maneger['openid'])) {
			$tips = "您的平台有新的【退款申请】, 单号【{$refund['out_refund_no']}】,请尽快处理";
			$remark = array("申请门店: " . $store['title'], "退款单号: " . $refund['out_refund_no'], "支付方式: " . $order['pay_type_cn'], "用户姓名: " . $order['username'], "联系方式: " . $order['mobile'], $note);
			$params = array('first' => $tips, 'reason' => '订单取消, 发起退款流程', 'refund' => $order['final_fee'], 'remark' => implode("\n", $remark));
			$send = sys_wechat_tpl_format($params);
			$status = $acc->sendTplNotice($maneger['openid'], $_W['we7_wmall_plus']['config']['notice']['refund_tpl'], $send);
		}
		if (!empty($clerks)) {
			$tips = "您的【退款申请】已提交,单号【{$refund['out_refund_no']}】,平台会尽快处理";
			$remark = array("申请门店: " . $store['title'], "退款单号: " . $refund['out_refund_no'], "支付方式: " . $order['pay_type_cn'], "用户姓名: " . $order['username'], "联系方式: " . $order['mobile'], "已付款项会在1-15工作日内返回到用户的账号, 如有疑问, 请联系平台管理员");
			$params = array('first' => $tips, 'reason' => '订单取消, 发起退款流程', 'refund' => $order['final_fee'], 'remark' => implode("\n", $remark));
			$send = sys_wechat_tpl_format($params);
			foreach ($clerks as $clerk) {
				$status = $acc->sendTplNotice($clerk['openid'], $_W['we7_wmall_plus']['config']['notice']['refund_tpl'], $send);
			}
		}
	} elseif ($type == 'success') {
		if (!empty($clerks)) {
			$tips = "您店铺单号为【{$refund['out_refund_no']}】的退款已退款成功";
			$remark = array("申请门店: " . $store['title'], "支付方式: " . $order['pay_type_cn'], "用户姓名: " . $order['username'], "联系方式: " . $order['mobile'], "退款渠道: " . $refund['channel_cn'], "退款账户: " . $refund['account'], "如有疑问, 请联系平台管理员");
			$params = array('first' => $tips, 'reason' => '订单取消, 发起退款流程', 'refund' => $order['final_fee'], 'remark' => implode("\n", $remark));
			$send = sys_wechat_tpl_format($params);
			foreach ($clerks as $clerk) {
				$status = $acc->sendTplNotice($clerk['openid'], $_W['we7_wmall_plus']['config']['notice']['refund_tpl'], $send);
			}
		}
		if (!empty($order['openid'])) {
			$tips = "您订单号为【{$order['ordersn']}】的退款已退款成功";
			$remark = array("下单门店: " . $store['title'], "支付方式: " . $order['pay_type_cn'], "退款渠道: " . $refund['channel_cn'], "退款账户: " . $refund['account'], "如有疑问, 请联系平台管理员");
			$params = array('first' => $tips, 'reason' => '订单取消, 发起退款流程', 'refund' => $order['final_fee'], 'remark' => implode("\n", $remark));
			$send = sys_wechat_tpl_format($params);
			$status = $acc->sendTplNotice($order['openid'], $_W['we7_wmall_plus']['config']['notice']['refund_tpl'], $send);
		}
	}
	return true;
}
function order_pay_types()
{
	$pay_types = array('' => '未支付', 'alipay' => array('css' => 'label label-info', 'text' => '支付宝'), 'wechat' => array('css' => 'label label-success', 'text' => '微信支付'), 'credit' => array('css' => 'label label-warning', 'text' => '余额支付'), 'delivery' => array('css' => 'label label-primary', 'text' => '货到付款'), 'baifubao' => array('css' => 'label label-primary', 'text' => '百付宝支付'), 'cash' => array('css' => 'label label-primary', 'text' => '现金支付'));
	return $pay_types;
}
function order_insert_member_cart($sid, $ignore_bargain = false)
{
	global $_W, $_GPC;
	if (!empty($_GPC['goods'])) {
		$_GPC['goods'] = str_replace('&nbsp;', '#nbsp;', $_GPC['goods']);
		$_GPC['goods'] = json_decode(str_replace('#nbsp;', '&nbsp;', html_entity_decode(urldecode($_GPC['goods']))), true);
		$ids_str = implode(',', array_keys($_GPC['goods']));
		$goods_info = pdo_fetchall('SELECT * FROM ' . tablename('tiny_wmall_plus_goods') . " WHERE uniacid = :uniacid AND sid = :sid AND id IN ({$ids_str})", array(':uniacid' => $_W['uniacid'], ':sid' => $sid), 'id');
		if (!$ignore_bargain) {
			mload()->model('bargain');
			bargain_update_status();
			$bargains = pdo_getall('tiny_wmall_plus_activity_bargain', array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'status' => 'ongoing'), array(), 'id');
			if (!empty($bargains)) {
				$bargain_ids = implode(',', array_keys($bargains));
				$bargain_goods = pdo_fetchall('select * from ' . tablename('tiny_wmall_plus_activity_bargain_goods') . " where uniacid = :uniacid and sid = :sid and bargain_id in ({$bargain_ids})", array(':uniacid' => $_W['uniacid'], ':sid' => $sid));
				$bargain_goods_group = array();
				if (!empty($bargain_goods)) {
					foreach ($bargain_goods as &$row) {
						$row['available_buy_limit'] = $row['max_buy_limit'];
						$bargain_goods_group[$row['bargain_id']][$row['goods_id']] = $row;
					}
				}
				foreach ($bargains as &$row) {
					$row['available_goods_limit'] = $row['goods_limit'];
					$row['goods'] = $bargain_goods_group[$row['id']];
				}
			} else {
				$bargains = array();
			}
		}
		$total_num = 0;
		$total_original_price = 0;
		$total_price = 0;
		$total_box_price = 0;
		$cart_bargain = array();
		foreach ($_GPC['goods'] as $k => $v) {
			$k = intval($k);
			$goods = $goods_info[$k];
			if (empty($goods)) {
				continue;
			}
			$goods_box_price = $goods['box_price'];
			if (!$goods['is_options']) {
				$item = $v['options'][0];
				$num = intval($item['num']);
				if ($num > 0) {
					$cart_item = array('title' => $goods_info[$k]['title'], 'num' => $num, 'price' => $goods_info[$k]['price'], 'discount_price' => $goods_info[$k]['price'], 'discount_num' => 0, 'price_num' => $num, 'total_price' => round($goods_info[$k]['price'] * $num, 2), 'total_discount_price' => round($goods_info[$k]['price'] * $num, 2), 'bargain_id' => 0);
					if ($item['bargain_id'] > 0 && $item['discount_num'] > 0) {
						$bargain = $bargains[$item['bargain_id']];
						$bargain_goods = $bargain['goods'][$k];
						if ($item['discount_num'] > $bargain_goods['max_buy_limit']) {
							$item['discount_num'] = $bargain_goods['max_buy_limit'];
						}
						$params = array(':uniacid' => $_W['uniacid'], ':uid' => $_W['member']['uid'], ':stat_day' => date('Ymd'), ':bargain_id' => $bargain['id']);
						$num = pdo_fetchcolumn('select count(distinct(oid))  from ' . tablename('tiny_wmall_plus_order_stat') . ' where uniacid = :uniacid and uid = :uid and bargain_id = :bargain_id and stat_day = :stat_day', $params);
						$num = intval($num);
						if ($bargain['order_limit'] > $num && $bargain['available_goods_limit'] > 0 && $bargain_goods['available_buy_limit'] > 0) {
							for ($i = 0; $i < $item['discount_num']; $i++) {
								if ($bargain_goods['poi_user_type'] == 'new' && empty($_W['member']['is_newmember'])) {
									break;
								}
								if (($bargain_goods['discount_available_total'] == -1 || $bargain_goods['discount_available_total'] > 0) && $bargain_goods['available_buy_limit'] > 0) {
									$cart_item['discount_price'] = $bargain_goods['discount_price'];
									$cart_item['discount_num']++;
									$cart_item['bargain_id'] = $bargain['id'];
									$cart_bargain[] = $bargain['use_limit'];
									if ($cart_item['price_num'] > 0) {
										$cart_item['price_num']--;
									}
									$bargain_goods['discount_available_total']--;
									$bargain['available_buy_limit']--;
								} else {
									break;
								}
							}
							if ($cart_item['discount_num'] > 0) {
								$bargain['available_goods_limit']--;
							}
							$cart_item['total_discount_price'] = $cart_item['discount_num'] * $bargain_goods['discount_price'] + $cart_item['price_num'] * $goods_info[$k]['price'];
							$cart_item['total_discount_price'] = round($cart_item['total_discount_price'], 2);
						}
					}
					$total_num += $num;
					$total_price += $cart_item['total_discount_price'];
					$total_original_price += $cart_item['total_price'];
					$total_box_price += $goods_box_price * $num;
					$cart_goods[$k][0] = $cart_item;
				}
			} else {
				foreach ($v['options'] as $key => $val) {
					$key = intval($key);
					if ($key > 0) {
						$option = pdo_get('tiny_wmall_plus_goods_options', array('uniacid' => $_W['uniacid'], 'id' => $key));
						if (empty($option)) {
							continue;
						}
						$cart_goods[$k][$key] = array('title' => $goods_info[$k]['title'] . "({$option['name']})", 'num' => $val['num'], 'price' => $option['price'], 'discount_price' => $option['price'], 'discount_num' => 0, 'price_num' => $num, 'total_price' => round($option['price'] * $val['num'], 2), 'total_discount_price' => round($option['price'] * $val['num'], 2), 'bargain_id' => 0);
						$total_num += $val['num'];
						$total_price += $option['price'] * $val['num'];
						$total_original_price += $option['price'] * $val['num'];
						$total_box_price += $goods_box_price * $val['num'];
					}
				}
			}
		}
		$isexist = pdo_fetchcolumn('SELECT id FROM ' . tablename('tiny_wmall_plus_order_cart') . " WHERE uniacid = :aid AND sid = :sid AND uid = :uid", array(':aid' => $_W['uniacid'], ':sid' => $sid, ':uid' => $_W['member']['uid']));
		$data = array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'uid' => $_W['member']['uid'], 'groupid' => $_W['member']['groupid'], 'num' => $total_num, 'price' => $total_price, 'box_price' => round($total_box_price, 2), 'data' => iserializer($cart_goods), 'original_data' => iserializer($_GPC['goods']), 'addtime' => TIMESTAMP, 'bargain_use_limit' => 0);
		if (!empty($cart_bargain)) {
			$cart_bargain = array_unique($cart_bargain);
			if (in_array(1, $cart_bargain)) {
				$data['bargain_use_limit'] = 1;
			}
			if (in_array(2, $cart_bargain)) {
				$data['bargain_use_limit'] = 2;
			}
		}
		if (empty($isexist)) {
			pdo_insert('tiny_wmall_plus_order_cart', $data);
		} else {
			pdo_update('tiny_wmall_plus_order_cart', $data, array('uniacid' => $_W['uniacid'], 'id' => $isexist, 'uid' => $_W['member']['uid']));
		}
		$data['data'] = $cart_goods;
		return $data;
	} else {
		return error(-1, '商品信息错误');
	}
	return true;
}
function order_check_member_cart($sid)
{
	global $_W;
	$cart = pdo_fetch('SELECT * FROM ' . tablename('tiny_wmall_plus_order_cart') . " WHERE uniacid = :aid AND sid = :sid AND uid = :uid", array(':aid' => $_W['uniacid'], ':sid' => $sid, ':uid' => $_W['member']['uid']));
	if (empty($cart)) {
		return error(-1, '购物车为空');
	}
	$cart['data'] = iunserializer($cart['data']);
	if (empty($cart['data'])) {
		return error(-1, '购物车为空');
	}
	$errno = 0;
	$errmessage = '';
	$err_sku = array();
	$goods_ids = implode(',', array_keys($cart['data']));
	$goods_info = pdo_fetchall('SELECT * FROM ' . tablename('tiny_wmall_plus_goods') . " WHERE uniacid = :uniacid AND sid = :sid AND id IN ({$goods_ids})", array(':uniacid' => $_W['uniacid'], ':sid' => $sid), 'id');
	foreach ($cart['data'] as $goods_id => $cart_item) {
		if (!empty($errno)) {
			break;
		}
		$goods = $goods_info[$goods_id];
		if (!$goods_info[$goods_id]['is_options']) {
			$option_item = $cart_item[0];
			if ($option_item['discount_num'] > 0) {
				$bargain = pdo_get('tiny_wmall_plus_activity_bargain', array('uniacid' => $_W['uniacid'], 'id' => $option_item['bargain_id'], 'sid' => $sid, 'status' => 'ongoing'));
				if (empty($bargain)) {
					$errno = -3;
					$errmessage = "特价活动{$bargain['title']}已结束！";
					break;
				}
				$bargain_goods = pdo_get('tiny_wmall_plus_activity_bargain_goods', array('uniacid' => $_W['uniacid'], 'bargain_id' => $option_item['bargain_id'], 'goods_id' => $goods_id));
				if ($bargain_goods['discount_available_total'] != -1 && $option_item['discount_num'] > $bargain_goods['discount_available_total']) {
					$errno = -4;
					$errmessage = "参与特价活动{$bargain['title']}的{$goods['title']}库存不足！";
					break;
				}
			} else {
				if ($goods['total'] != -1 && $option_item['num'] > $goods['total']) {
					$errno = -2;
					$errmessage = "{$option_item['title']}库存不足！";
					break;
				}
			}
		} else {
			foreach ($cart_item as $option_id => $option_item) {
				$option = pdo_get('tiny_wmall_plus_goods_options', array('uniacid' => $_W['uniacid'], 'id' => $option_id));
				if (empty($option)) {
					continue;
				}
				if ($option['total'] != -1 && $cart_item['num'] > $option['total']) {
					$errno = -2;
					$errmessage = "{$option_item['title']}库存不足！";
					break;
				}
			}
		}
	}
	if (!empty($errno)) {
		return error($errno, $errmessage);
	}
	return $cart;
}
function order_insert_cart($sid)
{
	global $_W, $_GPC;
	if (!empty($_GPC['goods'])) {
		$num = 0;
		$price = 0;
		$box_price = 0;
		$ids_str = implode(',', array_keys($_GPC['goods']));
		$goods_info = pdo_fetchall('SELECT * FROM ' . tablename('tiny_wmall_plus_goods') . " WHERE uniacid = :aid AND sid = :sid AND id IN ({$ids_str})", array(':aid' => $_W['uniacid'], ':sid' => $sid), 'id');
		foreach ($_GPC['goods'] as $k => $v) {
			$k = intval($k);
			$goods_box_price = $goods_info[$k]['box_price'];
			if (!$goods_info[$k]['is_options']) {
				$v = intval($v['options'][0]);
				if ($v > 0) {
					$goods[$k][0] = array('title' => $goods_info[$k]['title'], 'num' => $v, 'price' => $goods_info[$k]['price']);
					$num += $v;
					$price += $goods_info[$k]['price'] * $v;
					$box_price += $goods_box_price * $v;
				}
			} else {
				foreach ($v['options'] as $key => $val) {
					$key = intval($key);
					$val = intval($val);
					if ($key > 0 && $val > 0) {
						$option = pdo_get('tiny_wmall_plus_goods_options', array('uniacid' => $_W['uniacid'], 'id' => $key));
						if (empty($option)) {
							continue;
						}
						$goods[$k][$key] = array('title' => $goods_info[$k]['title'] . "({$option['name']})", 'num' => $val, 'price' => $option['price']);
						$num += $val;
						$price += $option['price'] * $val;
						$box_price += $goods_box_price * $val;
					}
				}
			}
		}
		$isexist = pdo_fetchcolumn('SELECT id FROM ' . tablename('tiny_wmall_plus_order_cart') . " WHERE uniacid = :aid AND sid = :sid AND uid = :uid", array(':aid' => $_W['uniacid'], ':sid' => $sid, ':uid' => $_W['member']['uid']));
		$data = array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'uid' => $_W['member']['uid'], 'groupid' => $_W['member']['groupid'], 'num' => $num, 'price' => $price, 'box_price' => $box_price, 'data' => iserializer($goods), 'addtime' => TIMESTAMP);
		if (empty($isexist)) {
			pdo_insert('tiny_wmall_plus_order_cart', $data);
		} else {
			pdo_update('tiny_wmall_plus_order_cart', $data, array('uniacid' => $_W['uniacid'], 'id' => $isexist, 'uid' => $_W['member']['uid']));
		}
		$data['data'] = $goods;
		return $data;
	} else {
		return error(-1, '商品信息错误');
	}
	return true;
}
function order_fetch_member_cart($sid)
{
	global $_W, $_GPC;
	$cart = pdo_fetch('SELECT * FROM ' . tablename('tiny_wmall_plus_order_cart') . " WHERE uniacid = :aid AND sid = :sid AND uid = :uid", array(':aid' => $_W['uniacid'], ':sid' => $sid, ':uid' => $_W['member']['uid']));
	if (empty($cart)) {
		return false;
	}
	if (TIMESTAMP - $cart['addtime'] > 7 * 86400) {
		pdo_delete('tiny_wmall_plus_order_cart', array('id' => $cart['id']));
	}
	$cart['data'] = iunserializer($cart['data']);
	$cart['original_data'] = (array) iunserializer($cart['original_data']);
	return $cart;
}
function order_del_member_cart($sid)
{
	global $_W;
	pdo_delete('tiny_wmall_plus_order_cart', array('sid' => $sid, 'uid' => $_W['member']['uid']));
	return true;
}
function order_update_goods_info($order_id, $sid, $cart = array())
{
	global $_W;
	if (empty($cart)) {
		$cart = order_fetch_member_cart($sid);
	}
	if (empty($cart['data'])) {
		return false;
	}
	$categorys = pdo_getall('tiny_wmall_plus_goods_category', array('uniacid' => $_W['uniacid']), array(), 'id');
	$ids_str = implode(',', array_keys($cart['data']));
	$goods_info = pdo_fetchall('SELECT id,cid,title,price,total,total_update_type,print_label FROM ' . tablename('tiny_wmall_plus_goods') . " WHERE uniacid = :aid AND sid = :sid AND id IN ({$ids_str})", array(':aid' => $_W['uniacid'], ':sid' => $sid), 'id');
	foreach ($cart['data'] as $goods_id => $options) {
		$goods = $goods_info[$goods_id];
		if (empty($goods)) {
			continue;
		}
		foreach ($options as $option_id => $item) {
			if ($goods['total_update_type'] == 1) {
				if (!$option_id) {
					if ($goods['total'] != -1 && $goods['total'] > 0) {
						pdo_query('UPDATE ' . tablename('tiny_wmall_plus_goods') . " set total = total - {$item['num']} WHERE uniacid = :aid AND id = :id", array(':aid' => $_W['uniacid'], ':id' => $goods_id));
					}
					if ($item['bargain_id'] > 0 && $item['discount_num'] > 0) {
						$bargain_goods = pdo_get('tiny_wmall_plus_activity_bargain_goods', array('uniacid' => $_W['uniacid'], 'bargain_id' => $item['bargain_id'], 'goods_id' => $goods_id));
						if ($bargain_goods['discount_available_total'] != -1 && $bargain_goods['discount_available_total'] > 0) {
							pdo_query('UPDATE ' . tablename('tiny_wmall_plus_activity_bargain_goods') . " set discount_available_total = discount_available_total - {$item['discount_num']} WHERE uniacid = :uniacid AND bargain_id = :bargain_id and goods_id = :goods_id", array(':uniacid' => $_W['uniacid'], ':bargain_id' => $bargain['id'], ':goods_id' => $goods_id));
						}
					}
				} else {
					$option = pdo_get('tiny_wmall_plus_goods_options', array('uniacid' => $_W['uniacid'], 'id' => $option_id));
					if (!empty($option) && $option['total'] != -1 && $option['total'] > 0) {
						pdo_query('UPDATE ' . tablename('tiny_wmall_plus_goods_options') . " set total = total - {$item['num']} WHERE uniacid = :aid AND id = :id", array(':aid' => $_W['uniacid'], ':id' => $option_id));
					}
				}
			}
			$stat = array();
			if ($item['num'] > 0) {
				$stat['oid'] = $order_id;
				$stat['uniacid'] = $_W['uniacid'];
				$stat['sid'] = $sid;
				$stat['uid'] = $_W['member']['uid'];
				$stat['goods_id'] = $goods_id;
				$stat['goods_cid'] = $goods['cid'];
				$stat['option_id'] = $option_id;
				$stat['goods_category_title'] = $categorys[$goods['cid']]['title'];
				$stat['goods_title'] = $item['title'];
				$stat['goods_num'] = $item['num'];
				$stat['goods_discount_num'] = $item['discount_num'];
				$stat['goods_unit_price'] = $goods['price'];
				$stat['goods_price'] = $item['total_discount_price'];
				$stat['goods_original_price'] = $item['total_price'];
				$stat['bargain_id'] = $item['bargain_id'];
				$stat['total_update_status'] = $goods['total_update_type'] == 2 ? 0 : 1;
				$stat['print_label'] = $goods['print_label'];
				$stat['addtime'] = TIMESTAMP;
				$stat['stat_year'] = date('Y');
				$stat['stat_month'] = date('Ym');
				$stat['stat_day'] = date('Ymd');
				$stat['stat_week'] = date('YW');
				pdo_insert('tiny_wmall_plus_order_stat', $stat);
			}
		}
	}
	return true;
}
function order_stat_member($sid)
{
	global $_W;
	$is_exist = pdo_get('tiny_wmall_plus_store_members', array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'uid' => $_W['member']['uid']));
	if (empty($is_exist)) {
		$insert = array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'uid' => $_W['member']['uid'], 'openid' => $_W['openid'], 'first_order_time' => TIMESTAMP, 'last_order_time' => TIMESTAMP);
		pdo_insert('tiny_wmall_plus_store_members', $insert);
	} else {
		$update = array('last_order_time' => TIMESTAMP);
		pdo_update('tiny_wmall_plus_store_members', $update, array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'uid' => $_W['member']['uid']));
	}
	return false;
}
function order_amount_stat($sid)
{
	global $_W;
	$stat = array();
	$today_starttime = strtotime(date('Y-m-d'));
	$month_starttime = strtotime(date('Y-m'));
	$stat['today_total'] = intval(pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_plus_order') . ' where uniacid = :uniacid and sid = :sid and status = 5 and is_pay = 1 and addtime >= :starttime', array(':uniacid' => $_W['uniacid'], ':sid' => $sid, ':starttime' => $today_starttime)));
	$stat['today_price'] = floatval(pdo_fetchcolumn('select sum(final_fee) from ' . tablename('tiny_wmall_plus_order') . ' where uniacid = :uniacid and sid = :sid and status = 5 and is_pay = 1 and addtime >= :starttime', array(':uniacid' => $_W['uniacid'], ':sid' => $sid, ':starttime' => $today_starttime)));
	$stat['month_total'] = intval(pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_plus_order') . ' where uniacid = :uniacid and sid = :sid and status = 5 and is_pay = 1 and addtime >= :starttime', array(':uniacid' => $_W['uniacid'], ':sid' => $sid, ':starttime' => $month_starttime)));
	$stat['month_price'] = floatval(pdo_fetchcolumn('select sum(final_fee) from ' . tablename('tiny_wmall_plus_order') . ' where uniacid = :uniacid and sid = :sid and status = 5 and is_pay = 1 and addtime >= :starttime', array(':uniacid' => $_W['uniacid'], ':sid' => $sid, ':starttime' => $month_starttime)));
	return $stat;
}
function order_avtivitys()
{
	$data = array('first_order' => array('text' => '新用户优惠', 'icon_b' => 'xin_b.png'), 'discount' => array('text' => '满减优惠', 'icon_b' => 'jian_b.png'), 'grant' => array('text' => '满赠优惠', 'icon_b' => 'zeng_b.png'));
	return $data;
}
function order_count_activity($sid, $cart, $recordid = 0, $delivery_price = 0)
{
	global $_W, $_GPC;
	$activityed = array('list' => '', 'total' => 0, 'activity' => 0, 'token' => 0);
	if ($_GPC['do'] == 'submit') {
		$store = store_fetch($sid, array('delivery_mode'));
		if ($store['delivery_mode'] == 2 && !empty($delivery_price)) {
			if ($_W['member']['setmeal_id'] > 0 && $_W['member']['setmeal_endtime'] >= TIMESTAMP) {
				$nums = pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_plus_order') . ' where uniacid = :uniacid and uid = :uid and vip_free_delivery_fee = 1 and status != 6 and addtime >= :addtime', array(':uniacid' => $_W['uniacid'], ':uid' => $_W['member']['uid'], ':addtime' => strtotime(date('Y-m-d'))));
				if ($nums < $_W['member']['setmeal_day_free_limit']) {
					$activityed['list']['delivery'] = array('text' => "-￥{$delivery_price}", 'value' => $delivery_price, 'type' => 'delivery', 'name' => '会员免配送费', 'icon' => 'mian_b.png');
					$activityed['total'] += $delivery_price;
					$activityed['activity'] += $delivery_price;
				}
			}
		}
	}
	if ($cart['bargain_use_limit'] == 2) {
		return $activityed;
	}
	$iscan_use_coupon = 0;
	if ($recordid > 0) {
		$record = pdo_get('tiny_wmall_plus_activity_coupon_record', array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'uid' => $_W['member']['uid'], 'status' => 1, 'id' => $recordid));
		if (!empty($record)) {
			$coupon = pdo_get('tiny_wmall_plus_activity_coupon', array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'id' => $record['couponid']));
			if (!empty($coupon) && $coupon['starttime'] <= TIMESTAMP && $coupon['endtime'] >= TIMESTAMP && $cart['price'] >= $coupon['condition']) {
				$iscan_use_coupon = 1;
			}
		}
	}
	if ($iscan_use_coupon == 1) {
		if ($coupon['use_limit'] == 2) {
			$activityed['list'] = array('token' => array('text' => "-￥{$coupon['discount']}", 'value' => $coupon['discount'], 'type' => 'token', 'name' => '代金券优惠', 'icon' => 'coupon_b.png', 'recordid' => $recordid));
			$activityed['total'] = $coupon['discount'];
			$activityed['token'] = $coupon['discount'];
			return $activityed;
		} else {
			$activityed['list']['token'] = array('text' => "-￥{$coupon['discount']}", 'value' => $coupon['discount'], 'type' => 'token', 'name' => '代金券优惠', 'icon' => 'coupon_b.png', 'recordid' => $recordid, 'coupon' => $coupon);
			$activityed['total'] += $coupon['discount'];
			$activityed['token'] = $coupon['discount'];
		}
	}
	$activity = store_fetch_activity($sid);
	if (!empty($activity)) {
		if (!empty($activity['first_order_status'])) {
			if (empty($_W['member']['is_newmember'])) {
				$discount = array_compare($cart['price'], $activity['first_order_data']);
				if (!empty($discount)) {
					$activityed['list']['first_order'] = array('text' => "-￥{$discount['back']}", 'value' => $discount['back'], 'type' => 'first_order', 'name' => '新用户优惠', 'icon' => 'xin_b.png');
					$activityed['total'] += $discount['back'];
					$activityed['activity'] += $discount['back'];
				}
			}
		}
		if (empty($activityed['list']['first_order']) && !empty($activity['discount_status'])) {
			$discount = array_compare($cart['price'], $activity['discount_data']);
			if (!empty($discount)) {
				$activityed['list']['discount'] = array('text' => "-￥{$discount['back']}", 'value' => $discount['back'], 'type' => 'discount', 'name' => '满减优惠', 'icon' => 'jian_b.png');
				$activityed['total'] += $discount['back'];
				$activityed['activity'] += $discount['back'];
			}
		}
		if (!empty($activity['grant_status'])) {
			$discount = array_compare($cart['price'], $activity['grant_data']);
			if (!empty($discount)) {
				$activityed['list']['grant'] = array('text' => "{$discount['back']}", 'value' => 0, 'type' => 'grant', 'name' => '满赠优惠', 'icon' => 'zeng_b.png');
				$activityed['total'] += 0;
				$activityed['activity'] += 0;
			}
		}
	}
	return $activityed;
}
function order_check_payment($sid)
{
	global $_W;
	$setting = uni_setting($_W['uniacid'], array('payment'));
	$pay = $setting['payment'];
	if (empty($pay)) {
		return error(-1, '公众号没有设置支付方式,请先设置支付方式');
	}
	if (!empty($pay['credit']['switch'])) {
		$dos[] = 'credit';
	}
	if (!empty($pay['alipay']['switch'])) {
		$dos[] = 'alipay';
	}
	if (!empty($pay['wechat']['switch'])) {
		$dos[] = 'wechat';
	}
	if (!empty($pay['delivery']['switch'])) {
		$dos[] = 'delivery';
	}
	if (!empty($pay['unionpay']['switch'])) {
		$dos[] = 'unionpay';
	}
	if (!empty($pay['baifubao']['switch'])) {
		$dos[] = 'baifubao';
	}
	if (empty($dos)) {
		return error(-1, '公众号没有设置支付方式,请先设置支付方式');
	}
	if (empty($store['payment'])) {
		message('店铺没有设置有效的支付方式', referer(), 'error');
	}
	return false;
}
function order_coupon_available($sid, $price)
{
	global $_W;
	$condition = ' on a.couponid = b.id where a.uniacid = :uniacid and a.sid = :sid and a.uid = :uid and a.status = 1 and b.condition <= :price and b.starttime <= :time and b.endtime >= :time';
	$params = array(':uniacid' => $_W['uniacid'], ':sid' => $sid, ':price' => $price, ':uid' => $_W['member']['uid'], ':time' => TIMESTAMP);
	$coupons = pdo_fetchall('select a.*,b.title,b.starttime,b.endtime,b.use_limit,b.discount,b.condition from ' . tablename('tiny_wmall_plus_activity_coupon_record') . ' as a left join ' . tablename('tiny_wmall_plus_activity_coupon') . ' as b ' . $condition, $params);
	return $coupons;
}
function order_insert_refund_log($id, $type, $note = '')
{
	global $_W;
	if (empty($type)) {
		return false;
	}
	$notes = array('apply' => array('status' => 1, 'title' => '提交退款申请', 'note' => ""), 'handle' => array('status' => 2, 'title' => "{$_W['we7_wmall_plus']['config']['title']}接受退款申请", 'note' => ''), 'success' => array('status' => 3, 'title' => "退款成功", 'note' => ''), 'fail' => array('status' => 4, 'title' => "退款失败", 'note' => ''));
	$title = $notes[$type]['title'];
	$note = $note ? $note : $notes[$type]['note'];
	$data = array('uniacid' => $_W['uniacid'], 'oid' => $id, 'order_type' => 'order', 'status' => $notes[$type]['status'], 'type' => $type, 'title' => $title, 'note' => $note, 'addtime' => TIMESTAMP);
	pdo_insert('tiny_wmall_plus_order_refund_log', $data);
	return true;
}
function order_begin_payrefund($order_id)
{
	global $_W;
	$refund = pdo_get('tiny_wmall_plus_order_refund', array('uniacid' => $_W['uniacid'], 'order_id' => $order_id));
	if (empty($refund)) {
		return error(-1, '退款申请不存在或已删除');
	}
	if ($refund['status'] == 2) {
		return error(-1, '退款进行中, 不能发起退款');
	}
	if ($refund['status'] == 3) {
		return error(-1, '退款已成功, 不能发起退款');
	}
	if ($refund['pay_type'] == 'credit') {
		if ($refund['uid'] > 0) {
			$log = array($refund['uid'], "外送模块(we7_wmall_plus)订单退款, 订单号:{$refund['order_sn']}, 退款金额:{$refund['fee']}元", 'we7_wmall_plus');
			load()->model('mc');
			mc_credit_update($refund['uid'], 'credit2', $refund['fee'], $log);
			$refund_update = array('status' => 3, 'account' => '支付用户的平台余额', 'channel' => 'ORIGINAL', 'handle_time' => TIMESTAMP, 'success_time' => TIMESTAMP);
			pdo_update('tiny_wmall_plus_order_refund', $refund_update, array('uniacid' => $_W['uniacid'], 'id' => $refund['id']));
			pdo_update('tiny_wmall_plus_order', array('refund_status' => 3), array('uniacid' => $_W['uniacid'], 'id' => $refund['order_id']));
			order_insert_refund_log($refund['order_id'], 'handle');
			order_insert_refund_log($refund['order_id'], 'success');
		}
		return error(0, '退款成功,支付金额已退款至顾客的平台余额');
	} elseif ($refund['pay_type'] == 'wechat') {
		mload()->classs('wxpay');
		$pay = new WxPay();
		$params = array('total_fee' => $refund['fee'] * 100, 'refund_fee' => $refund['fee'] * 100, 'out_trade_no' => $refund['out_trade_no'], 'out_refund_no' => $refund['out_refund_no']);
		$response = $pay->payRefund_build($params);
		if (is_error($response)) {
			return error(-1, $response['message']);
		}
		pdo_update('tiny_wmall_plus_order', array('refund_status' => 2), array('uniacid' => $_W['uniacid'], 'id' => $refund['order_id']));
		pdo_update('tiny_wmall_plus_order_refund', array('status' => 2, 'handle_time' => TIMESTAMP), array('uniacid' => $_W['uniacid'], 'id' => $refund['id']));
		order_insert_refund_log($refund['order_id'], 'handle');
		$query = order_query_payrefund($refund['order_id']);
		return $query;
	} elseif ($refund['pay_type'] == 'alipay') {
		mload()->classs('alipay');
		$pay = new AliPay();
		$params = array('refund_fee' => $refund['fee'], 'out_trade_no' => $refund['out_trade_no']);
		$response = $pay->payRefund_build($params);
		if (is_error($response)) {
			return error(-1, $response['message']);
		}
		$refund_update = array('status' => 3, 'account' => $response['buyer_logon_id'], 'channel' => 'ORIGINAL', 'handle_time' => TIMESTAMP, 'success_time' => TIMESTAMP);
		pdo_update('tiny_wmall_plus_order_refund', $refund_update, array('uniacid' => $_W['uniacid'], 'id' => $refund['id']));
		pdo_update('tiny_wmall_plus_order', array('refund_status' => 3), array('uniacid' => $_W['uniacid'], 'id' => $refund['order_id']));
		order_insert_refund_log($refund['order_id'], 'handle');
		order_insert_refund_log($refund['order_id'], 'success');
		return error(0, "退款成功,支付金额已退款至顾客的支付宝账户:{$response['buyer_logon_id']}");
	}
}
function order_query_payrefund($order_id)
{
	global $_W;
	$refund = pdo_get('tiny_wmall_plus_order_refund', array('uniacid' => $_W['uniacid'], 'order_id' => $order_id));
	if (empty($refund)) {
		return error(-1, '退款申请不存在或已删除');
	}
	if ($refund['status'] != 2) {
		return error(-1, '退款已处理');
	}
	if ($refund['pay_type'] == 'wechat') {
		mload()->classs('wxpay');
		$pay = new WxPay();
		$response = $pay->payRefund_query(array('out_refund_no' => $refund['out_refund_no']));
		if (is_error($response)) {
			return $response;
		}
		$wechat_status = $pay->payRefund_status();
		$update = array('status' => $wechat_status[$response['refund_status_0']]['value']);
		if ($response['refund_status_0'] == 'SUCCESS') {
			$update['channel'] = $response['refund_channel_0'];
			$update['account'] = $response['refund_recv_accout_0'];
			$update['success_time'] = TIMESTAMP;
			pdo_update('tiny_wmall_plus_order_refund', $update, array('uniacid' => $_W['uniacid'], 'id' => $refund['id']));
			pdo_update('tiny_wmall_plus_order', array('refund_status' => 3), array('uniacid' => $_W['uniacid'], 'id' => $refund['order_id']));
			order_insert_refund_log($refund['order_id'], 'success');
			return error(0, "退款成功,支付金额已退款至顾客的微信账号:{$response['refund_recv_accout_0']}");
		} else {
			pdo_update('tiny_wmall_plus_order', array('refund_status' => $update['status']), array('uniacid' => $_W['uniacid'], 'id' => $refund['order_id']));
			pdo_update('tiny_wmall_plus_order_refund', $update, array('uniacid' => $_W['uniacid'], 'id' => $refund['id']));
			return error(0, '退款进行中, 请耐心等待');
		}
	}
	return true;
}
function order_refund_status()
{
	$refund_status = array('1' => array('css' => 'label label-info', 'text' => '退款申请中'), '2' => array('css' => 'label label-warning', 'text' => '退款处理中'), '3' => array('css' => 'label label-success', 'text' => '退款成功'), '4' => array('css' => 'label label-danger', 'text' => '退款失败'), '5' => array('css' => 'label label-default', 'text' => '退款状态未确定'));
	return $refund_status;
}
function order_refund_channel()
{
	$refund_channel = array('ORIGINAL' => array('css' => 'label label-warning', 'text' => '原路返回'), 'BALANCE' => array('css' => 'label label-danger', 'text' => '退回余额'));
	return $refund_channel;
}
function order_comment_status()
{
	$status = array('0' => array('css' => 'color-primary', 'text' => '待审核'), '1' => array('css' => 'color-success', 'text' => '审核通过'), '2' => array('css' => 'color-danger', 'text' => '审核未通过'));
	return $status;
}
function order_status_update($id, $type, $extra = array())
{
	global $_W;
	$order = order_fetch($id);
	if (empty($order)) {
		return error(-1, '订单不存在或已删除');
	}
	$store = pdo_get('tiny_wmall_plus_store', array('uniacid' => $_W['uniacid'], 'id' => $order['sid']), array('delivery_mode', 'auto_handel_order', 'auto_notice_deliveryer'));
	$config_takeout = $_W['we7_wmall_plus']['config']['takeout'];
	if ($type == 'handle') {
		if ($order['status'] != 1) {
			return error(-1, '订单状态不是待处理状态,不能接单');
		}
		if (!$order['is_pay'] && $order['order_type'] <= 2) {
			return error(-1, '该订单属于外卖单,并且未支付,不能接单');
		}
		$update = array('status' => 2, 'handletime' => TIMESTAMP);
		pdo_update('tiny_wmall_plus_order', $update, array('uniacid' => $_W['uniacid'], 'id' => $order['id']));
		pdo_update('tiny_wmall_plus_order_stat', array('status' => 2), array('uniacid' => $_W['uniacid'], 'oid' => $order['id']));
		order_insert_status_log($order['id'], 'handle');
		order_status_notice($order['id'], 'handle');
		return error(0, '接单成功');
	} elseif ($type == 'notify_deliveryer_collect') {
		if ($order['order_type'] > 1) {
			return error(-1, '订单类型不是外卖单,不需要通知配送员抢单');
		}
		if ($order['status'] > 3) {
			return error(-1, '订单状态有误');
		}
		$update = array('status' => 3, 'delivery_status' => 3, 'delivery_type' => $store['delivery_mode']);
		pdo_update('tiny_wmall_plus_order', $update, array('uniacid' => $_W['uniacid'], 'id' => $order['id']));
		pdo_update('tiny_wmall_plus_order_stat', array('status' => 3), array('uniacid' => $_W['uniacid'], 'oid' => $order['id']));
		order_insert_status_log($order['id'], 'delivery_wait');
		if ($config_takeout['dispatch_mode'] <= 1 || $store['delivery_mode'] == 1) {
			order_deliveryer_notice($order['id'], 'delivery_wait');
		} elseif ($config['errander']['dispatch_mode'] == 2) {
			order_manager_notice($order['id'], 'new_delivery');
		} else {
			$order = order_dispatch_analyse($id);
			if (is_error($order)) {
				order_manager_notice($order['id'], 'dispatch_error', "失败原因：{$order['message']}");
			}
			$deliveryer = array_shift($order['deliveryers']);
			$status = order_assign_deliveryer($id, $deliveryer['deliveryer']['id']);
		}
		return error(0, '通知配送员抢单成功,请耐心等待配送员接单');
	} elseif ($type == 'cancel') {
		if ($order['status'] == 5) {
			return error(-1, '系统已完成， 不能取消订单');
		}
		if ($order['status'] == 6) {
			return error(-1, '系统已取消， 不能取消订单');
		}
		if ($order['delivery_status'] >= 4 && empty($extra['force_cancel'])) {
			return error(-1, '配送员已取货，正在配送中， 不能取消订单');
		}
		$is_refund = 0;
		pdo_update('tiny_wmall_plus_order_stat', array('status' => 6), array('uniacid' => $_W['uniacid'], 'oid' => $order['id']));
		if (!$order['is_pay'] || $order['final_fee'] <= 0 || ($order['is_pay'] == 1 && $order['pay_type'] == 'delivery' || $order['pay_type'] == 'cash')) {
			pdo_update('tiny_wmall_plus_order', array('status' => 6, 'delivery_status' => 6), array('uniacid' => $_W['uniacid'], 'id' => $order['id']));
			order_insert_status_log($order['id'], 'cancel', $extra['note']);
			order_status_notice($order['id'], 'cancel', $extra['note']);
		} else {
			if ($order['refund_status'] > 0) {
				return error(-1, '退款申请处理中, 请勿重复发起');
			}
			$update = array('status' => 6, 'delivery_status' => 6, 'refund_status' => 1, 'refund_fee' => $order['final_fee']);
			pdo_update('tiny_wmall_plus_order', $update, array('uniacid' => $_W['uniacid'], 'id' => $order['id']));
			order_insert_status_log($order['id'], 'cancel', $extra['note']);
			$refund = array('uniacid' => $order['uniacid'], 'acid' => $order['acid'], 'sid' => $order['sid'], 'uid' => $order['uid'], 'order_id' => $order['id'], 'order_sn' => $order['ordersn'], 'pay_type' => $order['pay_type'], 'fee' => $order['final_fee'], 'status' => 1, 'out_trade_no' => $order['out_trade_no'], 'out_refund_no' => date('YmdHis') . random(10, true), 'apply_time' => TIMESTAMP, 'reason' => $extra['note'] ? $extra['note'] : '订单取消，发起退款');
			pdo_insert('tiny_wmall_plus_order_refund', $refund);
			$is_refund = 1;
			order_insert_refund_log($order['id'], 'apply');
			$note = array("取消原因: {$extra['note']}", "退款金额: {$order['final_fee']}元", "已付款项会在1-15工作日内返回您的账号");
			order_status_notice($order['id'], 'cancel', $note);
			order_refund_notice($order['id'], 'apply');
			if ($order['deliveryer_id'] > 0) {
				order_deliveryer_notice($order['id'], 'cancel', $order['deliveryer_id']);
			}
		}
		return error(0, array('is_refund' => $is_refund));
	} elseif ($type == 'end') {
		if ($order['status'] == 5) {
			return error(-1, '系统已完成， 请勿重复操作');
		}
		if ($order['status'] == 6) {
			return error(-1, '系统已取消， 不能在进行其他操作');
		}
		$update = array('status' => 5, 'delivery_status' => 5, 'endtime' => TIMESTAMP, 'delivery_success_time' => TIMESTAMP, 'delivery_success_location_x' => $extra['delivery_success_location_x'], 'delivery_success_location_y' => $extra['delivery_success_location_y']);
		pdo_update('tiny_wmall_plus_order', $update, array('uniacid' => $_W['uniacid'], 'id' => $order['id']));
		pdo_update('tiny_wmall_plus_order_stat', array('status' => 5), array('uniacid' => $_W['uniacid'], 'oid' => $order['id']));
		if ($order['delivery_type'] == 2 && $order['plateform_deliveryer_fee'] > 0) {
			mload()->model('deliveryer');
			deliveryer_update_credit2($order['deliveryer_id'], $order['plateform_deliveryer_fee'], 1, $order['id']);
			if ($order['pay_type'] == 'delivery') {
				$note = "{$order['id']}属于货到支付单,您线下收取客户{$order['final_fee']}元,平台从您的账户扣除该费用";
				deliveryer_update_credit2($order['deliveryer_id'], -$order['final_fee'], 3, $order['id'], $note);
			}
		}
		if ($order['is_pay'] == 1) {
			if (in_array($order['pay_type'], array('wechat', 'alipay', 'credit')) || $order['delivery_type'] == 2 && $order['pay_type'] == 'delivery') {
				store_update_account($order['sid'], $order['store_final_fee'], 1, $order['id']);
			} else {
				$remark = "编号为{$order['id']}的订单属于线下支付,平台需要扣除{$order['plateform_serve_fee']}元服务费";
				store_update_account($order['sid'], -$order['plateform_serve_fee'], 3, $order['id'], $remark);
			}
		}
		$credit1_config = $config_takeout['grant_credit']['credit1'];
		if ($credit1_config['status'] == 1 && $credit1_config['grant_num'] > 0) {
			if ($order['uid'] > 0) {
				$credit1 = $credit1_config['grant_num'];
				if ($credit1_config['grant_type'] == 2) {
					$credit1 = round($order['final_fee'] * $credit1_config['grant_num'], 2);
				}
				if ($credit1 > 0) {
					load()->model('mc');
					mc_credit_update($order['uid'], 'credit1', $credit1, array(0, "外送模块订单完成, 赠送{$credit1}积分"));
				}
			}
		}
		order_insert_status_log($order['id'], 'end');
		order_status_notice($order['id'], 'end');
		return error(0, '完成订单成功');
	} elseif ($type == 'delivery_ing') {
		if ($order['status'] == 5) {
			return error(-1, '系统已完成， 请勿重复操作');
		}
		if ($order['status'] == 6) {
			return error(-1, '系统已取消， 不能在进行其他操作');
		}
		if ($store['delivery_mode'] == 2) {
			return error(-1, '门店配送模式为平台配送， 不能直接设置为配送中');
		}
		if ($order['deliveryer_id'] > 0) {
			return error(-1, '该订单已有配送员接单, 不能直接设置为配送中');
		}
		$update = array('status' => 4, 'delivery_status' => 4);
		pdo_update('tiny_wmall_plus_order', $update, array('uniacid' => $_W['uniacid'], 'id' => $order['id']));
		pdo_update('tiny_wmall_plus_order_stat', array('status' => 4), array('uniacid' => $_W['uniacid'], 'oid' => $order['id']));
		order_insert_status_log($order['id'], 'delivery_ing');
		order_status_notice($order['id'], 'delivery_ing');
		return error(0, '变更订单状态成功');
	} elseif ($type == 'reply') {
		pdo_update('tiny_wmall_plus_order', array('is_remind' => 0), array('uniacid' => $_W['uniacid'], 'id' => $id));
		$reply = trim($extra['reply']);
		order_insert_status_log($id, 'remind_reply', $reply);
		order_status_notice($id, 'reply_remind', "回复内容：" . $reply);
		return error(0, '回复顾客催单成功');
	} elseif ($type == 'notify_clerk_handle') {
		order_clerk_notice($id, 'place_order', '平台管理员催促您尽快处理该订单');
		return error(0, '通知商户接单成功');
	} elseif ($type == 'pay') {
		if ($order['is_pay'] == 1) {
			return error(-1, '订单已支付，请勿重复支付');
		}
		$update = array('is_pay' => 1, 'pay_type' => 'cash', 'paytime' => TIMESTAMP);
		pdo_update('tiny_wmall_plus_order', $update, array('uniacid' => $_W['uniacid'], 'id' => $order['id']));
		order_insert_status_log($order['id'], 'pay');
		order_status_notice($order['id'], 'pay');
		return error(0, '设置订单支付成功');
	}
}
function order_dispatch_analyse($id)
{
	$order = order_fetch($id);
	if (empty($order)) {
		return error(-1, '订单不存在或已删除');
	}
	$store = pdo_get('tiny_wmall_plus_store', array('id' => $order['sid']), array('location_x', 'location_y'));
	$order['store'] = $store;
	mload()->model('deliveryer');
	$deliveryers = deliveryer_fetchall();
	if (!empty($deliveryers)) {
		foreach ($deliveryers as &$deliveryer) {
			$deliveryer['order_id'] = $id;
			if (empty($order['location_x']) || empty($order['location_y']) || empty($deliveryer['deliveryer']['location_y']) || empty($deliveryer['deliveryer']['location_x'])) {
				$deliveryer['store2deliveryer_distance'] = '未知';
				$deliveryer['store2user_distance'] = '未知';
			} else {
				$deliveryer['store2user_distance'] = distanceBetween($order['location_y'], $order['buy_location_x'], $store['location_y'], $store['location_x']);
				$deliveryer['store2user_distance'] = round($deliveryer['store2user_distance'], 2) . 'km';
				$deliveryer['store2deliveryer_distance'] = distanceBetween($store['location_y'], $store['location_x'], $deliveryer['deliveryer']['location_y'], $deliveryer['deliveryer']['location_x']);
				$deliveryer['store2deliveryer_distance'] = round($deliveryer['store2deliveryer_distance'], 2) . 'km';
			}
		}
		$deliveryers = array_sort($deliveryers, 'store2deliveryer_distance');
		$order['deliveryers'] = $deliveryers;
	} else {
		return error(-1, '没有平台配送员，无法进行自动调度');
	}
	return $order;
}
function order_assign_deliveryer($order_id, $deliveryer_id, $update_deliveryer = false, $note = '')
{
	global $_W;
	$order = order_fetch($order_id);
	if (empty($order)) {
		return error(-1, '订单不存在或已删除');
	}
	if ($order['status'] == 5) {
		return error(-1, '系统已完成， 请勿重复操作');
	}
	if ($order['status'] == 6) {
		return error(-1, '系统已取消， 不能在进行其他操作');
	}
	if ($order['deliveryer_id'] > 0 && !$update_deliveryer) {
		return error(-1, '该订单已经分配给其他配送员，不能重新指定配送员');
	}
	mload()->model('deliveryer');
	$deliveryer = pdo_get('tiny_wmall_plus_deliveryer', array('uniacid' => $_W['uniacid'], 'id' => $deliveryer_id));
	if (empty($deliveryer)) {
		return error(-1, '配送员不存在或已经删除,请指定其他配送员配送');
	}
	$permission = pdo_getall('tiny_wmall_plus_store_deliveryer', array('uniacid' => $_W['uniacid'], 'deliveryer_id' => $deliveryer_id), array('sid'), 'sid');
	if (empty($permission)) {
		return error(-1, "配送员{$deliveryer['title']}没有配送订单的权限");
	}
	$store = pdo_get('tiny_wmall_plus_store', array('uniacid' => $_W['uniacid'], 'id' => $order['sid']), array('delivery_mode'));
	if ($store['delivery_mode'] == 1) {
		if (!in_array($order['sid'], array_keys($permission))) {
			return error(-1, "门店配送模式为店内配送，该配送员没有该门店的配送权限");
		}
	} else {
		if (!in_array(0, array_keys($permission))) {
			return error(-1, "该配送员没有平台订单的配送权限");
		}
	}
	$update = array('status' => 4, 'delivery_status' => 4, 'deliveryer_id' => $deliveryer_id, 'delivery_type' => $store['delivery_mode'], 'delivery_assign_time' => TIMESTAMP);
	if ($store['delivery_mode'] == 2) {
		$config_takeout = $_W['we7_wmall_plus']['config']['takeout'];
		$update['plateform_deliveryer_fee'] = floatval($config_takeout['deliveryer_fee']);
		if ($config_takeout['deliveryer_fee_type'] == 2) {
			$update['plateform_deliveryer_fee'] = round($order['delivery_fee'] * $config_takeout['deliveryer_fee'] / 100, 2);
		}
	}
	pdo_update('tiny_wmall_plus_order', $update, array('uniacid' => $_W['uniacid'], 'id' => $order['id']));
	pdo_update('tiny_wmall_plus_order_stat', array('status' => 4), array('uniacid' => $_W['uniacid'], 'oid' => $order['id']));
	if ($order['deliveryer_id'] > 0) {
		deliveryer_order_num_update($order['deliveryer_id']);
	}
	deliveryer_order_num_update($deliveryer_id);
	$note = "配送员：{$deliveryer['title']}, 手机号：{$deliveryer['mobile']}";
	order_insert_status_log($order['id'], 'delivery_assign', $note);
	$remark = array("配送员：{$deliveryer['title']}", "手机号：{$deliveryer['mobile']}");
	order_status_notice($order['id'], 'delivery_assign', $remark);
	order_deliveryer_notice($order['id'], 'new_delivery', $deliveryer['id'], $note);
	return error(0, '订单分派配送员成功');
}
function order_system_status_update($id, $type, $extra = array())
{
	global $_W;
	set_time_limit(0);
	$order = order_fetch($id);
	if (empty($order)) {
		return error(-1, '订单不存在或已删除');
	}
	$store = pdo_get('tiny_wmall_plus_store', array('uniacid' => $_W['uniacid'], 'id' => $order['sid']), array('delivery_mode', 'auto_handel_order', 'auto_notice_deliveryer'));
	if ($type == 'pay') {
		if ($order['is_pay'] == 1) {
			return error(-1, '订单已支付，请勿重复支付');
		}
		$update = array('is_pay' => 1, 'pay_type' => $extra['type'], 'final_fee' => $extra['card_fee'], 'is_pay' => 1, 'paytime' => TIMESTAMP, 'transaction_id' => $extra['tag']['transaction_id'], 'out_trade_no' => $extra['uniontid']);
		if ($order['order_type'] <= 2) {
			if ($store['auto_handel_order'] == 1) {
				$update['status'] = 2;
				$update['handletime'] = TIMESTAMP;
				if ($order['order_type'] == 2) {
					$update['status'] = 4;
				}
				if ($store['auto_notice_deliveryer'] == 1 && $order['order_type'] == 1) {
					$update['delivery_type'] = $store['delivery_mode'];
					$update['status'] = 3;
					$update['delivery_status'] = 3;
				}
				pdo_update('tiny_wmall_plus_order', $update, array('id' => $order['id'], 'uniacid' => $_W['uniacid']));
				order_insert_status_log($order['id'], 'pay');
				order_insert_status_log($order['id'], 'handle');
				if ($store['auto_notice_deliveryer'] == 1) {
					order_insert_status_log($order['id'], 'delivery_wait');
				}
				order_status_notice($order['id'], 'handle');
				order_clerk_notice($order['id'], 'place_order');
				if ($store['auto_notice_deliveryer'] == 1) {
					order_deliveryer_notice($order['id'], 'delivery_wait');
				}
			} else {
				pdo_update('tiny_wmall_plus_order', $update, array('id' => $order['id'], 'uniacid' => $_W['uniacid']));
				order_insert_status_log($order['id'], 'pay');
				order_status_notice($order['id'], 'pay');
				order_clerk_notice($order['id'], 'place_order');
			}
		} elseif ($order['order_type'] == 3) {
			mload()->model('table');
			$update['status'] = 2;
			pdo_update('tiny_wmall_plus_order', $update, array('id' => $order['id'], 'uniacid' => $_W['uniacid']));
			table_order_update($order['table_id'], $order['id'], 4);
			order_insert_status_log($order['id'], 'pay');
			order_status_notice($order['id'], 'pay');
			order_clerk_notice($order['id'], 'store_order_pay');
		} elseif ($order['order_type'] == 4) {
			$update['status'] = 2;
			pdo_update('tiny_wmall_plus_order', $update, array('id' => $order['id'], 'uniacid' => $_W['uniacid']));
			order_insert_status_log($order['id'], 'pay');
			order_status_notice($order['id'], 'pay');
			order_clerk_notice($order['id'], 'reserve_order_pay');
		}
		$stat = pdo_getall('tiny_wmall_plus_order_stat', array('uniacid' => $_W['uniacid'], 'oid' => $order['id']), array('id', 'goods_id', 'option_id', 'goods_num', 'goods_discount_num', 'bargain_id', 'total_update_status'));
		if (!empty($stat)) {
			foreach ($stat as $row) {
				pdo_query('UPDATE ' . tablename('tiny_wmall_plus_goods') . " set sailed = sailed + {$row['goods_num']} WHERE uniacid = :aid AND id = :id", array(':aid' => $_W['uniacid'], ':id' => $row['goods_id']));
				pdo_query('UPDATE ' . tablename('tiny_wmall_plus_store') . " set sailed = sailed + {$row['goods_num']} WHERE uniacid = :uniacid AND id = :id", array(':uniacid' => $_W['uniacid'], ':id' => $row['sid']));
				if (!$row['total_update_status']) {
					if (!$row['option_id']) {
						$goods = pdo_get('tiny_wmall_plus_goods', array('uniacid' => $_W['uniacid'], 'id' => $row['goods_id']));
						if ($goods['total'] != -1 && $goods['total'] > 0) {
							pdo_query('UPDATE ' . tablename('tiny_wmall_plus_goods') . " set total = total - {$row['goods_num']} WHERE uniacid = :aid AND id = :id", array(':aid' => $_W['uniacid'], ':id' => $row['goods_id']));
						}
						if ($row['bargain_id'] > 0 && $row['goods_discount_num'] > 0) {
							$bargain_goods = pdo_get('tiny_wmall_plus_activity_bargain_goods', array('uniacid' => $_W['uniacid'], 'bargain_id' => $row['bargain_id'], 'goods_id' => $row['goods_id']));
							if ($bargain_goods['discount_available_total'] != -1 && $bargain_goods['discount_available_total'] > 0) {
								pdo_query('UPDATE ' . tablename('tiny_wmall_plus_activity_bargain_goods') . " set discount_available_total = discount_available_total - {$row['goods_discount_num']} WHERE uniacid = :uniacid AND bargain_id = :bargain_id and goods_id = :goods_id", array(':uniacid' => $_W['uniacid'], ':bargain_id' => $row['bargain_id'], ':goods_id' => $row['goods_id']));
							}
						}
					} else {
						$option = pdo_get('tiny_wmall_plus_goods_options', array('uniacid' => $_W['uniacid'], 'id' => $row['option_id']));
						if (!empty($option) && $option['total'] != -1 && $option['total'] > 0) {
							pdo_query('UPDATE ' . tablename('tiny_wmall_plus_goods_options') . " set total = total - {$row['goods_num']} WHERE uniacid = :uniacid AND id = :id", array(':uniacid' => $_W['uniacid'], ':id' => $row['option_id']));
						}
					}
					pdo_update('tiny_wmall_plus_order_stat', array('total_update_status' => 1), array('id' => $stat['id']));
				}
			}
		}
		order_print($order['id']);
		return error(0, '订单支付成功');
	}
}
function order_deliveryer_update_status($id, $type, $extra = array())
{
	global $_W;
	$order = order_fetch($id);
	if (empty($order)) {
		return error(-1, '订单不存在或已删除');
	}
	$config_takeout = $_W['we7_wmall_plus']['config']['takeout'];
	if ($type == 'delivery_assign') {
		if ($order['status'] == 5) {
			return error(-1, '系统已完成， 不能抢单或分配订单');
		}
		if ($order['status'] == 6) {
			return error(-1, '系统已取消， 不能抢单或分配订单');
		}
		if ($order['deliveryer_id'] > 0) {
			return error(-1, '来迟了, 该订单已被别人接单');
		}
		if (empty($extra['deliveryer_id'])) {
			return error(-1, '配送员id不存在');
		}
		$deliveryer = pdo_get('tiny_wmall_plus_deliveryer', array('uniacid' => $_W['uniacid'], 'id' => $extra['deliveryer_id']));
		if (empty($deliveryer)) {
			return error(-1, '配送员不存在');
		}
		if ($order['delivery_type'] == 2) {
			if ($config_takeout['deliveryer_collect_max'] > 0) {
				$params = array(':uniacid' => $_W['uniacid'], ':deliveryer_id' => $deliveryer['id']);
				$num = pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_plus_order') . ' where uniacid = :uniacid and deliveryer_id = :deliveryer_id and (delivery_status = 7 or delivery_status = 4)', $params);
				$num = intval($num);
				if ($num >= $config_takeout['deliveryer_collect_max']) {
					return error(-1, "每人最多可抢{$config_takeout['deliveryer_collect_max']}个外卖单");
				}
			}
		}
		$update = array('status' => 4, 'delivery_status' => 7, 'deliveryer_id' => $extra['deliveryer_id'], 'delivery_assign_time' => TIMESTAMP);
		if ($order['delivery_type'] == 2) {
			$update['plateform_deliveryer_fee'] = floatval($config_takeout['deliveryer_fee']);
			if ($config_takeout['deliveryer_fee_type'] == 2) {
				$update['plateform_deliveryer_fee'] = round($order['delivery_fee'] * $config_takeout['deliveryer_fee'] / 100, 2);
			}
		}
		pdo_update('tiny_wmall_plus_order', $update, array('uniacid' => $_W['uniacid'], 'id' => $order['id']));
		pdo_update('tiny_wmall_plus_order_stat', array('status' => 4), array('uniacid' => $_W['uniacid'], 'oid' => $order['id']));
		mload()->model('deliveryer');
		if ($order['deliveryer_id'] > 0) {
			deliveryer_order_num_update($order['deliveryer_id']);
		}
		deliveryer_order_num_update($deliveryer['id']);
		$note = "配送员：{$deliveryer['title']}, 手机号：{$deliveryer['mobile']}";
		order_insert_status_log($order['id'], 'delivery_assign', $note);
		$remark = array("配送员：{$deliveryer['title']}", "手机号：{$deliveryer['mobile']}");
		order_status_notice($order['id'], 'delivery_assign', $remark);
		order_clerk_notice($order['id'], 'collect', $remark);
		return error(0, '抢单成功');
	} elseif ($type == 'delivery_instore') {
		if ($order['status'] == 5) {
			return error(-1, '系统已完成， 不能抢单或分配订单');
		}
		if ($order['status'] == 6) {
			return error(-1, '系统已取消， 不能抢单或分配订单');
		}
		if (empty($extra['deliveryer_id'])) {
			return error(-1, '配送员不存在');
		}
		$deliveryer = pdo_get('tiny_wmall_plus_deliveryer', array('uniacid' => $_W['uniacid'], 'id' => $extra['deliveryer_id']));
		if (empty($deliveryer)) {
			return error(-1, '配送员不存在');
		}
		if ($order['deliveryer_id'] != $deliveryer['id']) {
			return error(-1, '该订单不是您配送，不能确认取货');
		}
		$update = array('delivery_status' => 4, 'delivery_instore_time' => TIMESTAMP, 'delivery_handle_type' => !empty($extra['delivery_handle_type']) ? $extra['delivery_handle_type'] : 'wechat');
		pdo_update('tiny_wmall_plus_order', $update, array('uniacid' => $_W['uniacid'], 'id' => $order['id']));
		order_insert_status_log($order['id'], 'delivery_instore');
		order_status_notice($order['id'], 'delivery_instore');
		return error(0, '确认到店成功');
	} elseif ($type == 'delivery_success') {
		$result = order_status_update($order['id'], 'end', $extra);
		if (is_error($result)) {
			return $result;
		}
		mload()->model('deliveryer');
		deliveryer_order_num_update($order['deliveryer_id']);
		return error(0, '确认送达成功');
	}
}
function order_manager_notice($order_id, $type, $note = '')
{
	global $_W;
	$maneger = $_W['we7_wmall_plus']['config']['manager'];
	if (empty($maneger)) {
		return error(-1, '管理员信息不完善');
	}
	$order = order_fetch($order_id);
	if (empty($order)) {
		return error(-1, '订单不存在或已经删除');
	}
	$store = store_fetch($order['sid'], array('id', 'title'));
	$acc = WeAccount::create($order['acid']);
	if ($type == 'new_delivery') {
		$title = '平台有新的外卖订单，请尽快登陆后台调度处理';
		$remark = array("门店名称: {$store['title']}", "订单类型: {$order['order_type_cn']}", "支付方式: {$order['pay_type_cn']}", "支付时间: " . date('Y-m-d H:i', $order['paytime']));
	}
	if ($type == 'dispatch_error') {
		$title = '平台有新的外卖订单，系统自动调度失败，请登录后台人工调度';
		$remark = array("门店名称: {$store['title']}", "订单类型: {$order['order_type_cn']}", "支付方式: {$order['pay_type_cn']}", "支付时间: " . date('Y-m-d H:i', $order['paytime']));
	}
	if (!empty($note)) {
		if (!is_array($note)) {
			$remark[] = $note;
		} else {
			$remark[] = implode("\n", $note);
		}
	}
	if (!empty($end_remark)) {
		$remark[] = $end_remark;
	}
	$remark = implode("\n", $remark);
	$send = tpl_format($title, $order['ordersn'], $order['status_cn'], $remark);
	$status = $acc->sendTplNotice($maneger['openid'], $_W['we7_wmall_plus']['config']['public_tpl'], $send);
	return $status;
}