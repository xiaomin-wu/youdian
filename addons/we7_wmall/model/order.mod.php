<?php

//decode by QQ:270656184 http://www.yunlu99.com/
defined('IN_IA') or die('Access Denied');
function order_fetch($id)
{
	global $_W;
	$id = intval($id);
	$order = pdo_fetch('SELECT * FROM ' . tablename('tiny_wmall_order') . ' WHERE uniacid = :aid AND id = :id', array(':aid' => $_W['uniacid'], ':id' => $id));
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
		$table = pdo_get('tiny_wmall_tables', array('uniacid' => $_W['uniacid'], 'id' => $order['table_id']));
		$order['table'] = $table;
	} elseif ($order['order_type'] == 4) {
		$reserve_type = order_reserve_type();
		$order['reserve_type_cn'] = $reserve_type[$order['reserve_type']]['text'];
		$category = pdo_get('tiny_wmall_tables_category', array('uniacid' => $_W['uniacid'], 'id' => $order['table_cid']));
		$order['table_category'] = $category;
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
	$data = pdo_fetchall('SELECT * FROM ' . tablename('tiny_wmall_order_stat') . $condition, $params);
	return $data;
}
function order_fetch_discount($id)
{
	global $_W;
	$data = pdo_getall('tiny_wmall_order_discount', array('uniacid' => $_W['uniacid'], 'oid' => $id));
	return $data;
}
function order_place_again($sid, $order_id)
{
	global $_W;
	$order = order_fetch($order_id);
	if (empty($order)) {
		return false;
	}
	$isexist = pdo_fetchcolumn('SELECT id FROM ' . tablename('tiny_wmall_order_cart') . " WHERE uniacid = :aid AND sid = :sid AND uid = :uid", array(':aid' => $_W['uniacid'], ':sid' => $sid, ':uid' => $_W['member']['uid']));
	$data = array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'uid' => $_W['member']['uid'], 'groupid' => $_W['member']['groupid'], 'num' => $order['num'], 'price' => $order['price'], 'data' => $order['data'], 'addtime' => TIMESTAMP);
	if (empty($isexist)) {
		pdo_insert('tiny_wmall_order_cart', $data);
	} else {
		pdo_update('tiny_wmall_order_cart', $data, array('uniacid' => $_W['uniacid'], 'id' => $isexist, 'uid' => $_W['member']['uid']));
	}
	$data['data'] = iunserializer($order['data']);
	return $data;
}
function order_insert_discount($id, $sid, $discount_data)
{
	global $_W;
	if (empty($discount_data)) {
		return false;
	}
	if (!empty($discount_data['token'])) {
		pdo_update('tiny_wmall_activity_coupon_record', array('status' => 2, 'usetime' => TIMESTAMP, 'order_id' => $id), array('uniacid' => $_W['uniacid'], 'id' => $discount_data['token']['recordid']));
	}
	foreach ($discount_data as $data) {
		$insert = array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'oid' => $id, 'type' => $data['type'], 'name' => $data['name'], 'icon' => $data['icon'], 'note' => $data['text'], 'fee' => $data['value']);
		pdo_insert('tiny_wmall_order_discount', $insert);
	}
	return true;
}
function order_insert_status_log($id, $sid, $type, $note = '')
{
	global $_W;
	if (empty($type)) {
		return false;
	}
	mload()->model('store');
	$order = order_fetch($id);
	$store = store_fetch($order['sid'], array('pay_time_limit'));
	$notes = array('place_order' => array('status' => 1, 'title' => '订单提交成功', 'note' => "单号:{$order['ordersn']},请耐心等待商家确认", 'ext' => array(array('key' => 'pay_time_limit', 'title' => '待支付', 'note' => "请在订单提交后{$store['pay_time_limit']}分钟内完成支付"))), 'handel' => array('status' => 2, 'title' => '商户已经确认订单', 'note' => '正在为您准备商品'), 'delivery_wait' => array('status' => 3, 'title' => '商品已准备就绪', 'note' => '商品已准备就绪,正在分配配送员'), 'delivery_ing' => array('status' => 4, 'title' => '已分配配送员', 'note' => ''), 'delivery_instore' => array('status' => 12, 'title' => '配送员已到店', 'note' => '配送员已到店, 取货完成, 骑士将骑上战马为您急速送达'), 'end' => array('status' => 5, 'title' => '订单已完成', 'note' => '任何意见和吐槽,都欢迎联系我们'), 'cancel' => array('status' => 6, 'title' => '订单已取消', 'note' => ''), 'pay' => array('status' => 7, 'title' => '订单已支付', 'note' => '支付成功.付款时间:' . date('Y-m-d H:i:s')), 'remind' => array('status' => 8, 'title' => '商家已收到催单', 'note' => '商家会尽快回复您的催单请求'), 'remind_reply' => array('status' => 9, 'title' => '商家回复了您的催单', 'note' => ''), 'delivery_success' => array('status' => 10, 'title' => '订单配送完成', 'note' => ''), 'delivery_fail' => array('status' => 10, 'title' => '订单配送失败', 'note' => ''));
	$title = $notes[$type]['title'];
	$note = $note ? $note : $notes[$type]['note'];
	$data = array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'oid' => $id, 'status' => $notes[$type]['status'], 'type' => $type, 'title' => $title, 'note' => $note, 'addtime' => TIMESTAMP);
	pdo_insert('tiny_wmall_order_status_log', $data);
	if (!empty($notes[$type]['ext'])) {
		foreach ($notes[$type]['ext'] as $val) {
			if ($val['key'] == 'pay_time_limit' && !$store['pay_time_limit']) {
				unset($val['note']);
			}
			$data = array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'oid' => $id, 'title' => $val['title'], 'note' => $val['note'], 'addtime' => TIMESTAMP);
			pdo_insert('tiny_wmall_order_status_log', $data);
		}
	}
	return true;
}
function order_fetch_status_log($id)
{
	global $_W;
	$data = pdo_fetchall("SELECT * FROM " . tablename('tiny_wmall_order_status_log') . ' WHERE uniacid = :uniacid and oid = :oid order by id asc', array(':uniacid' => $_W['uniacid'], ':oid' => $id), 'id');
	return $data;
}
function order_fetch_refund_status_log($id)
{
	global $_W;
	$data = pdo_fetchall("SELECT * FROM " . tablename('tiny_wmall_order_refund_log') . ' WHERE uniacid = :uniacid and oid = :oid and order_type = :order_type order by id asc', array(':uniacid' => $_W['uniacid'], ':oid' => $id, ':order_type' => 'order'), 'id');
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
	$prints = pdo_fetchall('SELECT * FROM ' . tablename('tiny_wmall_printer') . ' WHERE uniacid = :aid AND sid = :sid AND status = 1', array(':aid' => $_W['uniacid'], ':sid' => $sid));
	if (empty($prints)) {
		return error(-1, '没有有效的打印机');
	}
	mload()->model('print');
	$num = 0;
	foreach ($prints as $li) {
		if (!empty($li['print_no'])) {
			$content = array('title' => "<CB>{$store['title']}</CB>");
			if ($li['is_print_all'] == 1) {
				if (!empty($li['print_header'])) {
					$content[] = $li['print_header'];
				}
				$content[] = '名称　　　单价　　数量　　金额';
				$content[] = '******************************';
				$order['goods'] = order_fetch_goods($order['id'], $li['print_label']);
				foreach ($order['goods'] as $di) {
					$content[] = $di['goods_title'];
					$str = '';
					$str .= '　　　　　' . str_pad($di['goods_unit_price'], '8', ' ', STR_PAD_RIGHT);
					$str .= 'X' . str_pad($di['goods_num'], '4', ' ', STR_PAD_RIGHT);
					$str .= ' ' . str_pad($di['goods_price'], '5', ' ', STR_PAD_RIGHT);
					$content[] = $str;
				}
				$content[] = '******************************';
				$content[] = "订单类型:{$order['order_type_cn']}";
				$content[] = "订单　号:{$order['ordersn']}";
				$content[] = "支付方式:{$order['pay_type_cn']}";
				$content[] = "包装　费:{$order['pack_fee']}元";
				$content[] = "配送　费:{$order['delivery_fee']}元";
				$content[] = "合　　计:{$order['total_fee']}元";
				if ($order['discount_fee'] > 0) {
					$content[] = "线上优惠:-{$order['discount_fee']}元";
					$content[] = "实际支付:{$order['final_fee']}元";
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
					$content[] = "桌　　号:{$order['table']['title']}桌";
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
				if (!empty($li['qrcode_link'])) {
					$content['qrcode'] = "<QR>{$li['qrcode_link']}</QR>";
				}
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
				$content[] = '名称　　　单价　　数量　　金额';
				$content[] = '******************************';
				$order['goods'] = order_fetch_goods($order['id'], $li['print_label']);
				foreach ($order['goods'] as $di) {
					$str = '';
					$str .= '　　　　　' . str_pad($di['goods_unit_price'], '8', ' ', STR_PAD_RIGHT);
					$str .= 'X' . str_pad($di['goods_num'], '4', ' ', STR_PAD_RIGHT);
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
		pdo_query('UPDATE ' . tablename('tiny_wmall_order') . " SET print_nums = print_nums + {$num} WHERE uniacid = {$_W['uniacid']} AND id = {$order['id']}");
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
function order_status_notice($sid, $id, $status, $note = '')
{
	global $_W;
	$status_arr = array('handel', 'delivery_ing', 'delivery_instore', 'end', 'cancel', 'pay', 'remind', 'reply_remind', 'delivery_notice');
	if (!in_array($status, $status_arr)) {
		return false;
	}
	$type = $status;
	$store = store_fetch($sid, array('title'));
	$order = order_fetch($id);
	$acc = WeAccount::create($order['acid']);
	order_fetch_fansinfo();
	if (!empty($order['openid'])) {
		if ($type == 'pay') {
			$title = '您的订单已付款';
			$remark = array("门店名称: {$store['title']}", "订单类型: {$order['order_type_cn']}", "支付方式: {$order['pay_type_cn']}", "支付时间: " . date('Y-m-d H: i', time()));
		}
		if ($type == 'handel') {
			$title = '商家已接单,正在准备商品中...';
			$remark = array("门店名称: {$store['title']}", "订单类型: {$order['order_type_cn']}", "处理时间: " . date('Y-m-d H: i', time()));
		}
		if ($type == 'delivery_ing') {
			$title = '您的订单正在为您配送中';
			$remark = array("门店名称: {$store['title']}", "订单类型: {$order['order_type_cn']}");
			$end_remark = "配送员已抢单, 正赶至商家取货, 骑士将骑上战马为您急速送达, 请保持电话畅通";
		}
		if ($type == 'delivery_notice') {
			$title = "配送员【{$note['title']}】已达到你下单时填写的送货地址, 配送员手机号:【{$note['mobile']}】, 请注意接听配送员来电";
			$remark = array("门店名称: {$store['title']}", "配送员: {$note['title']}", "手机号: {$note['mobile']}");
			unset($note);
		}
		if ($type == 'end') {
			$title = '订单处理完成';
			$remark = array("门店名称: {$store['title']}", "订单类型: {$order['order_type_cn']}", "完成时间: " . date('Y-m-d H: i', time()));
			$end_remark = "您的订单已处理完成, 如对商品有不满意或投诉请联系客服:{$_W['we7_wmall']['config']['mobile']},欢迎您下次光临.戳这里记得给我们的服务评价.";
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
		$url = $_W['siteroot'] . 'app' . ltrim(murl('entry', array('do' => 'order', 'm' => 'we7_wmall', 'op' => 'detail', 'sid' => $order['sid'], 'id' => $order['id'])), '.');
		$remark = implode("\n", $remark);
		$send = tpl_format($title, $order['ordersn'], $order['status_cn'], $remark);
		$status = $acc->sendTplNotice($order['openid'], $_W['we7_wmall']['config']['public_tpl'], $send, $url);
		return $status;
	}
	return true;
}
function order_clerk_notice($sid, $id, $type, $note = '')
{
	global $_W;
	mload()->model('store');
	mload()->model('order');
	$store = store_fetch($sid, array('title', 'id'));
	$order = order_fetch($id);
	if (empty($store) || empty($order)) {
		return false;
	}
	mload()->model('clerk');
	$clerks = clerk_fetchall($sid);
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
	$url = $_W['siteroot'] . 'app' . ltrim(murl('entry', array('do' => 'mgorder', 'm' => 'we7_wmall', 'op' => 'detail', 'sid' => $order['sid'], 'id' => $order['id'])), '.');
	$remark = implode("\n", $remark);
	$send = tpl_format($title, $order['ordersn'], $order['status_cn'], $remark);
	mload()->model('sms');
	foreach ($clerks as $clerk) {
		if (!empty($clerk['mobile']) && in_array($type, array('place_order', 'store_order_place'))) {
			sms_singlecall($clerk['mobile'], array('name' => $clerk['title'], 'store' => $store['title'], 'price' => $order['final_fee']), 'clerk');
		}
		$acc->sendTplNotice($clerk['openid'], $_W['we7_wmall']['config']['public_tpl'], $send, $url);
	}
	return true;
}
function order_deliveryer_notice($sid, $id, $type, $deliveryer_id = 0, $note = '')
{
	global $_W;
	$store = store_fetch($sid, array('title', 'id'));
	$order = order_fetch($id);
	$account = store_account($sid);
	if (empty($store) || empty($order) || empty($account)) {
		return false;
	}
	mload()->model('deliveryer');
	if ($account['delivery_type'] == 2) {
		$deliveryers = deliveryer_fetchall(0);
	} else {
		if ($deliveryer_id > 0) {
			$deliveryers[] = deliveryer_fetch($deliveryer_id);
		} else {
			$deliveryers = deliveryer_fetchall($sid);
		}
	}
	if (empty($deliveryers)) {
		return false;
	}
	$acc = WeAccount::create($order['acid']);
	if ($type == 'new_delivery') {
		$title = '您有新的配送订单,订单号: ' . $order['ordersn'];
		$remark = array("门店名称: {$store['title']}", "下单时间: " . date('Y-m-d H:i', $order['addtime']), "总金　额: {$order['final_fee']}", "支付状态: {$order['pay_type_cn']}", "下单　人: {$order['username']}", "联系手机: {$order['mobile']}", "送货地址: {$order['address']}", "订单类型: " . ($account['delivery_type'] == 1 ? '店内配送单' : '平台配送单'));
		$remark = implode("\n", $remark);
		$url = $_W['siteroot'] . 'app' . ltrim(murl('entry', array('do' => 'dyorder', 'm' => 'we7_wmall', 'op' => 'detail', 'id' => $order['id'])), '.');
		$title = "店铺{$store['title']}有新的外卖配送订单, 配送地址为{$order['address']}, 快去处理吧";
		Jpush_deliveryman_send('您有新的外卖配送订单', $title, array('voice_play_nums' => 2, 'voice_text' => $title));
	} else {
		if ($type == 'delivery_wait') {
			$title = "{$store['title']}有新的配送单, 快去抢单...";
			$remark = array("门店名称: {$store['title']}", "下单时间: " . date('Y-m-d H:i', $order['addtime']), "下单　人: {$order['username']}", "联系手机: {$order['mobile']}", "送货地址: {$order['address']}", "订单类型: " . ($account['delivery_type'] == 1 ? '店内配送单' : '平台配送单'));
			$remark = implode("\n", $remark);
			$url = $_W['siteroot'] . 'app' . ltrim(murl('entry', array('do' => 'dyorder', 'm' => 'we7_wmall', 'op' => 'list')), '.');
			$title = "店铺{$store['title']}有新的配送订单, 配送地址为{$order['address']}, 快去抢单吧";
			Jpush_deliveryman_send('您有新的外卖待抢订单', $title, array('voice_play_nums' => 2, 'voice_text' => $title));
		}
	}
	$send = tpl_format($title, $order['ordersn'], $order['status_cn'], $remark);
	mload()->model('sms');
	foreach ($deliveryers as $deliveryer) {
		if (!empty($deliveryer['deliveryer']['mobile'])) {
			sms_singlecall($deliveryer['deliveryer']['mobile'], array('name' => $deliveryer['deliveryer']['title'], 'store' => $store['title']), 'deliveryer');
		}
		$acc->sendTplNotice($deliveryer['deliveryer']['openid'], $_W['we7_wmall']['config']['public_tpl'], $send, $url);
	}
	return true;
}
function order_current_fetch($order_id)
{
	global $_W;
	$data = pdo_get('tiny_wmall_order_current_log', array('uniacid' => $_W['uniacid'], 'orderid' => $order_id));
	if (!empty($data)) {
		$pay_types = order_pay_types();
		$refund_status = order_refund_status();
		$refund_channel = order_refund_channel();
		$data['pay_type_cn'] = $pay_types[$data['pay_type']]['text'];
		$data['refund_status_cn'] = $refund_status[$data['refund_status']]['text'];
		$data['refund_channel_cn'] = $refund_channel[$data['refund_channel']]['text'];
	}
	return $data;
}
function order_refund_notice($sid, $order_id, $type, $note = '')
{
	global $_W;
	$store = store_fetch($sid, array('title', 'id'));
	$current = order_current_fetch($order_id);
	if (empty($store) || empty($current)) {
		return false;
	}
	$acc = WeAccount::create($current['acid']);
	mload()->model('clerk');
	$clerks = clerk_fetchall($sid);
	if ($type == 'apply') {
		$maneger = $_W['we7_wmall']['config']['manager'];
		if (!empty($maneger['openid'])) {
			$tips = "您的平台有新的【退款申请】, 单号【{$current['out_refund_no']}】,请尽快处理";
			$remark = array("申请门店: " . $store['title'], "退款单号: " . $current['out_refund_no'], "支付方式: " . $current['pay_type_cn'], "用户姓名: " . $current['username'], "联系方式: " . $current['mobile'], $note);
			$params = array('first' => $tips, 'reason' => '订单取消, 发起退款流程', 'refund' => $current['fee'], 'remark' => implode("\n", $remark));
			$send = sys_wechat_tpl_format($params);
			$status = $acc->sendTplNotice($maneger['openid'], $_W['we7_wmall']['config']['notice']['refund_tpl'], $send);
		}
		if (!empty($clerks)) {
			$tips = "您的【退款申请】已提交,单号【{$current['out_refund_no']}】,平台会尽快处理";
			$remark = array("申请门店: " . $store['title'], "退款单号: " . $current['out_refund_no'], "支付方式: " . $current['pay_type_cn'], "用户姓名: " . $current['username'], "联系方式: " . $current['mobile'], "已付款项会在1-15工作日内返回到用户的账号, 如有疑问, 请联系平台管理员");
			$params = array('first' => $tips, 'reason' => '订单取消, 发起退款流程', 'refund' => $current['fee'], 'remark' => implode("\n", $remark));
			$send = sys_wechat_tpl_format($params);
			foreach ($clerks as $clerk) {
				$status = $acc->sendTplNotice($clerk['openid'], $_W['we7_wmall']['config']['notice']['refund_tpl'], $send);
			}
		}
	} elseif ($type == 'success') {
		if (!empty($clerks)) {
			$tips = "您单号为【{$current['out_refund_no']}】的退款申请【{$current['refund_status_cn']}】";
			$remark = array("申请门店: " . $store['title'], "支付方式: " . $current['pay_type_cn'], "用户姓名: " . $current['username'], "联系方式: " . $current['mobile'], "退款渠道: " . $current['refund_channel_cn'], "退款账户: " . $current['refund_account'], "如有疑问, 请联系平台管理员");
			$params = array('first' => $tips, 'reason' => '订单取消, 发起退款流程', 'refund' => $current['fee'], 'remark' => implode("\n", $remark));
			$send = sys_wechat_tpl_format($params);
			foreach ($clerks as $clerk) {
				$status = $acc->sendTplNotice($clerk['openid'], $_W['we7_wmall']['config']['notice']['refund_tpl'], $send);
			}
		}
		if (!empty($current['openid'])) {
			$tips = "您订单号为【{$current['orderid']}】的退款申请【{$current['refund_status_cn']}】";
			$remark = array("下单门店: " . $store['title'], "支付方式: " . $current['pay_type_cn'], "退款渠道: " . $current['refund_channel_cn'], "退款账户: " . $current['refund_account'], "如有疑问, 请联系平台管理员");
			$params = array('first' => $tips, 'reason' => '订单取消, 发起退款流程', 'refund' => $current['fee'], 'remark' => implode("\n", $remark));
			$send = sys_wechat_tpl_format($params);
			$status = $acc->sendTplNotice($current['openid'], $_W['we7_wmall']['config']['notice']['refund_tpl'], $send);
		}
	}
	return true;
}
function order_pay_types()
{
	$pay_types = array('' => '未支付', 'alipay' => array('css' => 'label label-info', 'text' => '支付宝'), 'wechat' => array('css' => 'label label-success', 'text' => '微信支付'), 'credit' => array('css' => 'label label-warning', 'text' => '余额支付'), 'delivery' => array('css' => 'label label-primary', 'text' => '货到付款'), 'baifubao' => array('css' => 'label label-primary', 'text' => '百付宝支付'), 'cash' => array('css' => 'label label-primary', 'text' => '现金支付'));
	return $pay_types;
}
function order_insert_member_cart($sid)
{
	global $_W, $_GPC;
	if (!empty($_GPC['goods'])) {
		$num = 0;
		$price = 0;
		$ids_str = implode(',', array_keys($_GPC['goods']));
		$goods_info = pdo_fetchall('SELECT * FROM ' . tablename('tiny_wmall_goods') . " WHERE uniacid = :aid AND sid = :sid AND id IN ({$ids_str})", array(':aid' => $_W['uniacid'], ':sid' => $sid), 'id');
		$num_data = array();
		foreach ($_GPC['goods'] as $k => $v) {
			$k = intval($k);
			if (!$goods_info[$k]['is_options']) {
				$v = intval($v['options'][0]);
				if ($v > 0) {
					$goods[$k][0] = array('title' => $goods_info[$k]['title'], 'num' => $v, 'price' => $goods_info[$k]['price']);
					$num_data[$k] = $v;
					$num += $v;
					$price += $goods_info[$k]['price'] * $v;
				}
			} else {
				foreach ($v['options'] as $key => $val) {
					$key = intval($key);
					$val = intval($val);
					if ($key > 0 && $val > 0) {
						$option = pdo_get('tiny_wmall_goods_options', array('uniacid' => $_W['uniacid'], 'id' => $key));
						if (empty($option)) {
							continue;
						}
						$goods[$k][$key] = array('title' => $goods_info[$k]['title'] . "({$option['name']})", 'num' => $val, 'price' => $option['price']);
						$num_data[$k] += $val;
						$num += $val;
						$price += $option['price'] * $val;
					}
				}
			}
		}
		$isexist = pdo_fetchcolumn('SELECT id FROM ' . tablename('tiny_wmall_order_cart') . " WHERE uniacid = :aid AND sid = :sid AND uid = :uid", array(':aid' => $_W['uniacid'], ':sid' => $sid, ':uid' => $_W['member']['uid']));
		$data = array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'uid' => $_W['member']['uid'], 'groupid' => $_W['member']['groupid'], 'num' => $num, 'price' => $price, 'data' => iserializer($goods), 'num_data' => iserializer($num_data), 'addtime' => TIMESTAMP);
		if (empty($isexist)) {
			pdo_insert('tiny_wmall_order_cart', $data);
		} else {
			pdo_update('tiny_wmall_order_cart', $data, array('uniacid' => $_W['uniacid'], 'id' => $isexist, 'uid' => $_W['member']['uid']));
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
	$cart = pdo_fetch('SELECT * FROM ' . tablename('tiny_wmall_order_cart') . " WHERE uniacid = :aid AND sid = :sid AND uid = :uid", array(':aid' => $_W['uniacid'], ':sid' => $sid, ':uid' => $_W['member']['uid']));
	if (empty($cart)) {
		return false;
	}
	if (TIMESTAMP - $cart['addtime'] > 7 * 86400) {
		pdo_delete('tiny_wmall_order_cart', array('id' => $cart['id']));
	}
	$cart['data'] = iunserializer($cart['data']);
	$cart['num_data'] = iunserializer($cart['num_data']);
	return $cart;
}
function order_del_member_cart($sid)
{
	global $_W;
	pdo_delete('tiny_wmall_order_cart', array('sid' => $sid, 'uid' => $_W['member']['uid']));
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
	$ids_str = implode(',', array_keys($cart['data']));
	$goods_info = pdo_fetchall('SELECT id,cid,title,price,total,print_label FROM ' . tablename('tiny_wmall_goods') . " WHERE uniacid = :aid AND sid = :sid AND id IN ({$ids_str})", array(':aid' => $_W['uniacid'], ':sid' => $sid), 'id');
	foreach ($cart['data'] as $k => $v) {
		foreach ($v as $k1 => $v1) {
			pdo_query('UPDATE ' . tablename('tiny_wmall_goods') . " set sailed = sailed + {$v1['num']} WHERE uniacid = :aid AND id = :id", array(':aid' => $_W['uniacid'], ':id' => $k));
			if (!$k1) {
				if ($goods_info[$k]['total'] != -1 && $goods_info[$k]['total'] > 0) {
					pdo_query('UPDATE ' . tablename('tiny_wmall_goods') . " set total = total - {$v1['num']} WHERE uniacid = :aid AND id = :id", array(':aid' => $_W['uniacid'], ':id' => $k));
				}
			} else {
				$option = pdo_get('tiny_wmall_goods_options', array('uniacid' => $_W['uniacid'], 'id' => $k1));
				if (!empty($option) && $option['total'] != -1 && $option['total'] > 0) {
					pdo_query('UPDATE ' . tablename('tiny_wmall_goods') . " set total = total - {$v1['num']} WHERE uniacid = :aid AND id = :id", array(':aid' => $_W['uniacid'], ':id' => $k1));
				}
			}
			$stat = array();
			if ($k && $v1) {
				$stat['oid'] = $order_id;
				$stat['uniacid'] = $_W['uniacid'];
				$stat['sid'] = $sid;
				$stat['goods_id'] = $k;
				$stat['goods_cid'] = $goods_info[$k]['cid'];
				$stat['goods_num'] = $v1['num'];
				$stat['goods_title'] = $v1['title'];
				$stat['goods_unit_price'] = $v1['price'];
				$stat['goods_price'] = $v1['num'] * $v1['price'];
				$stat['print_label'] = $goods_info[$k]['print_label'];
				$stat['addtime'] = TIMESTAMP;
				pdo_insert('tiny_wmall_order_stat', $stat);
			}
		}
	}
	pdo_query('UPDATE ' . tablename('tiny_wmall_store') . " set sailed = sailed + {$cart['num']} WHERE uniacid = :uniacid AND id = :id", array(':uniacid' => $_W['uniacid'], ':id' => $cart['sid']));
	return true;
}
function order_stat_member($sid)
{
	global $_W;
	$is_exist = pdo_get('tiny_wmall_store_members', array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'uid' => $_W['member']['uid']));
	if (empty($is_exist)) {
		$insert = array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'uid' => $_W['member']['uid'], 'openid' => $_W['openid'], 'first_order_time' => TIMESTAMP, 'last_order_time' => TIMESTAMP);
		pdo_insert('tiny_wmall_store_members', $insert);
	} else {
		$update = array('last_order_time' => TIMESTAMP);
		pdo_update('tiny_wmall_store_members', $update, array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'uid' => $_W['member']['uid']));
	}
	return false;
}
function order_amount_stat($sid)
{
	global $_W;
	$stat = array();
	$today_starttime = strtotime(date('Y-m-d'));
	$month_starttime = strtotime(date('Y-m'));
	$stat['today_total'] = intval(pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_order') . ' where uniacid = :uniacid and sid = :sid and status = 5 and is_pay = 1 and addtime >= :starttime', array(':uniacid' => $_W['uniacid'], ':sid' => $sid, ':starttime' => $today_starttime)));
	$stat['today_price'] = floatval(pdo_fetchcolumn('select sum(final_fee) from ' . tablename('tiny_wmall_order') . ' where uniacid = :uniacid and sid = :sid and status = 5 and is_pay = 1 and addtime >= :starttime', array(':uniacid' => $_W['uniacid'], ':sid' => $sid, ':starttime' => $today_starttime)));
	$stat['month_total'] = intval(pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_order') . ' where uniacid = :uniacid and sid = :sid and status = 5 and is_pay = 1 and addtime >= :starttime', array(':uniacid' => $_W['uniacid'], ':sid' => $sid, ':starttime' => $month_starttime)));
	$stat['month_price'] = floatval(pdo_fetchcolumn('select sum(final_fee) from ' . tablename('tiny_wmall_order') . ' where uniacid = :uniacid and sid = :sid and status = 5 and is_pay = 1 and addtime >= :starttime', array(':uniacid' => $_W['uniacid'], ':sid' => $sid, ':starttime' => $month_starttime)));
	return $stat;
}
function order_avtivitys()
{
	$data = array('first_order' => array('text' => '新用户优惠', 'icon_b' => 'xin_b.png'), 'discount' => array('text' => '满减优惠', 'icon_b' => 'jian_b.png'), 'grant' => array('text' => '满赠优惠', 'icon_b' => 'zeng_b.png'));
	return $data;
}
function order_count_activity($sid, $cart, $recordid = 0)
{
	global $_W, $_GPC;
	$activityed = array('list' => '', 'total' => 0, 'activity' => 0, 'token' => 0);
	$iscan_use_coupon = 0;
	if ($recordid > 0) {
		$record = pdo_get('tiny_wmall_activity_coupon_record', array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'uid' => $_W['member']['uid'], 'status' => 1, 'id' => $recordid));
		if (!empty($record)) {
			$coupon = pdo_get('tiny_wmall_activity_coupon', array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'id' => $record['couponid']));
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
			$is_first = pdo_get('tiny_wmall_store_members', array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'uid' => $_W['member']['uid']));
			if (empty($is_first)) {
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
		if (!empty($activity['amount_status'])) {
			$goods_ids = array_keys($cart['data']);
			if (!empty($goods_ids) && !empty($activity['amount_data']['goods'])) {
				$intersect = array_intersect($activity['amount_data']['goods'], $goods_ids);
				if (!empty($intersect)) {
					$total_num = 0;
					foreach ($intersect as $val) {
						$total_num += $cart['num_data'][$val];
					}
					$discount = array_compare($total_num, $activity['amount_data']['data']);
					if (!empty($discount)) {
						$activityed['list']['amount'] = array('text' => "-￥{$discount['back']}", 'value' => $discount['back'], 'type' => 'amount', 'name' => '数量满减', 'icon' => 'shu_b.png');
						$activityed['total'] += $discount['back'];
						$activityed['activity'] += $discount['back'];
					}
				}
			}
		}
	}
	if ($_GPC['do'] == 'submit') {
		$store = store_fetch($sid, array('delivery_price', 'delivery_mode'));
		if ($store['delivery_mode'] == 2 && $store['delivery_price'] > 0) {
			if ($_W['member']['setmeal_id'] > 0 && $_W['member']['setmeal_endtime'] >= TIMESTAMP) {
				$nums = pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_order') . ' where uniacid = :uniacid and uid = :uid and vip_free_delivery_fee = 1 and status != 6 and addtime >= :addtime', array(':uniacid' => $_W['uniacid'], ':uid' => $_W['member']['uid'], ':addtime' => strtotime(date('Y-m-d'))));
				if ($nums < $_W['member']['setmeal_day_free_limit']) {
					$activityed['list']['delivery'] = array('text' => "-￥{$store['delivery_price']}", 'value' => $store['delivery_price'], 'type' => 'delivery', 'name' => '会员免配送费', 'icon' => 'mian_b.png');
					$activityed['total'] += $store['delivery_price'];
					$activityed['activity'] += $store['delivery_price'];
				}
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
	$coupons = pdo_fetchall('select a.*,b.title,b.starttime,b.endtime,b.use_limit,b.discount,b.condition from ' . tablename('tiny_wmall_activity_coupon_record') . ' as a left join ' . tablename('tiny_wmall_activity_coupon') . ' as b ' . $condition, $params);
	return $coupons;
}
function order_insert_current_log($order_id, $sid, $price, $pay_type, $out_trade_no)
{
	global $_W;
	$log = pdo_get('tiny_wmall_order_current_log', array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'orderid' => $order_id));
	if (!empty($log)) {
		return true;
	}
	$order = pdo_get('tiny_wmall_order', array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'id' => $order_id));
	if (empty($order)) {
		return false;
	}
	$data = array('uniacid' => $order['uniacid'], 'acid' => $order['acid'], 'sid' => $sid, 'uid' => $order['uid'], 'openid' => $order['openid'], 'username' => $order['username'], 'mobile' => $order['mobile'], 'orderid' => $order_id, 'fee' => $price, 'final_fee' => $price, 'is_pay' => empty($pay_type) ? 0 : 1, 'pay_type' => $pay_type, 'order_status' => $order['status'], 'trade_status' => 2, 'out_trade_no' => $out_trade_no, 'addtime' => TIMESTAMP);
	pdo_insert('tiny_wmall_order_current_log', $data);
	return true;
}
function order_update_current_pay_type($order_id, $pay_type, $out_trade_no)
{
	global $_W;
	pdo_update('tiny_wmall_order_current_log', array('is_pay' => 1, 'pay_type' => $pay_type, 'out_trade_no' => $out_trade_no), array('uniacid' => $_W['uniacid'], 'orderid' => $order_id));
	return true;
}
function order_update_current_log($order_id, $status)
{
	global $_W;
	$log = pdo_get('tiny_wmall_order_current_log', array('uniacid' => $_W['uniacid'], 'orderid' => $order_id));
	if (empty($log)) {
		return false;
	}
	if ($log['trade_status'] == 4) {
		return false;
	}
	$update = array('order_status' => $status);
	if ($status == 5) {
		$update['trade_status'] = 1;
	} elseif ($status == 6) {
		$update['trade_status'] = 4;
	}
	pdo_update('tiny_wmall_order_current_log', $update, array('uniacid' => $_W['uniacid'], 'orderid' => $order_id));
	if ($status == 5 && $log['trade_status'] != 1) {
		$deliveryer_fee = 0;
		$store_deliveryer_fee = 0;
		$remark = "订单总金额为{$log['fee']}元";
		if (!$log['is_pay']) {
			$remark .= ",未支付, 属于线下交易";
			$log['fee'] = 0;
		} else {
			if ($log['pay_type'] == 'delivery' || $log['pay_type'] == 'cash') {
				$remark .= ",支付方式为{$log['pay_type']}, 属于线下交易";
				$log['fee'] = 0;
			}
		}
		if ($log['delivery_type'] == 2) {
			if ($log['store_deliveryer_fee'] > 0) {
				$remark .= ",使用平台配送, 需支付配送费{$log['store_deliveryer_fee']}元";
				$store_deliveryer_fee = $log['store_deliveryer_fee'];
			}
			$deliveryer_fee = $log['deliveryer_fee'];
			if ($deliveryer_fee > 0) {
				mload()->model('deliveryer');
				deliveryer_update_credit2($log['deliveryer_id'], $deliveryer_fee, 1, $log['orderid']);
			}
		}
		$final_fee = $log['fee'] - $store_deliveryer_fee;
		if ($final_fee != 0) {
			$remark .= ",实际到账{$final_fee}元";
			store_update_account($log['sid'], $final_fee, 1, $log['orderid'], $remark);
		}
		$credit1_config = $_W['we7_wmall']['config']['credit']['credit1'];
		if ($credit1_config['status'] == 1 && $credit1_config['grant_num'] > 0) {
			if ($log['uid'] > 0) {
				$credit1 = $credit1_config['grant_num'];
				if ($credit1_config['grant_type'] == 2) {
					$credit1 = round($log['fee'] * $credit1_config['grant_num'], 2);
				}
				if ($credit1 > 0) {
					load()->model('mc');
					mc_credit_update($log['uid'], 'credit1', $credit1, array(0, "外送模块订单完成, 赠送{$credit1}积分"));
				}
			}
		}
		pdo_update('tiny_wmall_order_current_log', array('final_fee' => $final_fee), array('uniacid' => $_W['uniacid'], 'orderid' => $order_id));
	}
	return true;
}
function order_build_payrefund($id)
{
	global $_W;
	$order = order_fetch($id);
	if (empty($order)) {
		return error(-1, '订单不存在或已删除');
	}
	if ($order['status'] >= 5) {
		return error(-1, '订单已关闭, 不能发起退款申请');
	}
	$current = pdo_get('tiny_wmall_order_current_log', array('uniacid' => $_W['uniacid'], 'orderid' => $order['id']));
	if (empty($current)) {
		return error(-1, '交易记录不存在, 不能发起退款申请');
	}
	if ($current['pay_type'] == 'delivery') {
		return error(-1, '支付方式为货到付款, 不能发起退款申请');
	}
	if ($current['refund_status'] > 0) {
		return error(-1, '退款申请处理中, 请勿重复发起');
	}
	if ($order['final_fee'] <= 0) {
		return error(-1, '订单支付金额为0, 不能发起退款申请');
	}
	pdo_update('tiny_wmall_order_current_log', array('refund_status' => 1, 'refund_time' => TIMESTAMP, 'out_refund_no' => date('YmdHis') . random(10, true)), array('uniacid' => $_W['uniacid'], 'orderid' => $order['id']));
	order_insert_order_refund_log($current['id'], $order['sid'], 'apply');
	return pdo_insertid();
}
function order_insert_order_refund_log($id, $sid, $type, $note = '')
{
	global $_W;
	if (empty($type)) {
		return false;
	}
	$notes = array('apply' => array('status' => 1, 'title' => '提交退款申请', 'note' => ""), 'handel' => array('status' => 2, 'title' => "{$_W['we7_wmall']['config']['title']}接受退款申请", 'note' => ''), 'success' => array('status' => 3, 'title' => "退款成功", 'note' => ''), 'fail' => array('status' => 4, 'title' => "退款失败", 'note' => ''));
	$title = $notes[$type]['title'];
	$note = $note ? $note : $notes[$type]['note'];
	$data = array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'oid' => $id, 'status' => $notes[$type]['status'], 'type' => $type, 'title' => $title, 'note' => $note, 'addtime' => TIMESTAMP);
	pdo_insert('tiny_wmall_order_refund_log', $data);
	return true;
}
function order_begin_payrefund($id)
{
	global $_W;
	$refund = pdo_get('tiny_wmall_order_current_log', array('uniacid' => $_W['uniacid'], 'id' => $id));
	if (empty($refund)) {
		return error(-1, '退款申请不存在或已删除');
	}
	if ($refund['refund_status'] == 3) {
		return error(-1, '退款已成功, 不能发起退款');
	}
	if ($refund['pay_type'] == 'credit') {
		if ($refund['uid'] > 0) {
			$log = array($refund['uid'], "外送模块(we7_wmall)订单退款, 订单号:{$refund['orderid']}, 退款金额:{$refund['fee']}元", 'we7_wmall');
			load()->model('mc');
			mc_credit_update($refund['uid'], 'credit2', $refund['fee'], $log);
			pdo_update('tiny_wmall_order_current_log', array('trade_status' => 4, 'refund_status' => 3, 'refund_time' => TIMESTAMP, 'refund_account' => '支付用户的平台余额', 'refund_channel' => 'ORIGINAL'), array('uniacid' => $_W['uniacid'], 'id' => $refund['id']));
		}
		return true;
	} elseif ($refund['pay_type'] == 'wechat') {
		mload()->classs('wxpay');
		$pay = new WxPay();
		$params = array('total_fee' => $refund['fee'] * 100, 'refund_fee' => $refund['fee'] * 100, 'out_trade_no' => $refund['out_trade_no'], 'out_refund_no' => $refund['out_refund_no']);
		$response = $pay->payRefund_build($params);
		if (is_error($response)) {
			return error(-1, $response['message']);
		}
		pdo_update('tiny_wmall_order_current_log', array('refund_status' => 2), array('uniacid' => $_W['uniacid'], 'id' => $refund['id']));
		return true;
	} elseif ($refund['pay_type'] == 'alipay') {
		mload()->classs('alipay');
		$pay = new AliPay();
		$params = array('refund_fee' => $refund['fee'], 'out_trade_no' => $refund['out_trade_no']);
		$response = $pay->payRefund_build($params);
		if (is_error($response)) {
			return error(-1, $response['message']);
		}
		pdo_update('tiny_wmall_order_current_log', array('trade_status' => 4, 'refund_status' => 3, 'refund_time' => TIMESTAMP, 'refund_account' => $response['buyer_logon_id'], 'refund_channel' => 'ORIGINAL'), array('uniacid' => $_W['uniacid'], 'id' => $refund['id']));
		return true;
	}
}
function order_query_payrefund($id)
{
	global $_W;
	$refund = pdo_get('tiny_wmall_order_current_log', array('uniacid' => $_W['uniacid'], 'id' => $id));
	if (empty($refund)) {
		return error(-1, '退款申请不存在或已删除');
	}
	if ($refund['refund_status'] != 2) {
		return true;
	}
	if ($refund['pay_type'] == 'wechat') {
		mload()->classs('wxpay');
		$pay = new WxPay();
		$response = $pay->payRefund_query(array('out_refund_no' => $refund['out_refund_no']));
		if (is_error($response)) {
			return $response;
		}
		$wechat_status = $pay->payRefund_status();
		$update = array('refund_status' => $wechat_status[$response['refund_status_0']]['value']);
		if ($response['refund_status_0'] == 'SUCCESS') {
			$update['refund_channel'] = $response['refund_channel_0'];
			$update['refund_account'] = $response['refund_recv_accout_0'];
			$update['refund_time'] = TIMESTAMP;
			$update['trade_status'] = 4;
			pdo_update('tiny_wmall_order_current_log', $update, array('uniacid' => $_W['uniacid'], 'id' => $refund['id']));
		} else {
			pdo_update('tiny_wmall_order_current_log', $update, array('uniacid' => $_W['uniacid'], 'id' => $refund['id']));
		}
		return true;
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