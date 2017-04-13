<?php
/**
 * [WeEngine System] Copyright (c) 2013 WE7.CC
 * $sn$
 */
defined('IN_IA') or exit('Access Denied');

function card_setmeal_buy_notice($order_id, $note = '') {
	global $_W;
	$order = pdo_get('tiny_wmall_delivery_cards_order', array('uniacid' => $_W['uniacid'], 'id' => $order_id));
	if(empty($order)) {
		return error(-1, '订单不存在');
	}
	$pay_types = order_pay_types();
	$order['pay_type_cn'] = $pay_types[$order['pay_type']]['text'];

	$member = pdo_get('tiny_wmall_members', array('uniacid' => $_W['uniacid'], 'uid' => $order['uid']));

	$card = pdo_get('tiny_wmall_delivery_cards', array('uniacid' => $_W['uniacid'], 'id' => $order['card_id']));
	if(empty($card)) {
		return error(-1, '套餐不存在或已删除');
	}
	$acc = WeAccount::create($order['acid']);
	$maneger = $_W['we7_wmall']['config']['manager'];
	if(!empty($maneger['openid'])) {
		//通知平台管理员
		$tips = "配送会员卡【{$card['title']}】售出通知";
		$remark = array(
			"购卡费用: {$order['final_fee']}元",
			"支付方式: " . $order['pay_type_cn'],
			"购买　人: " . ($member['realname'] ? $member['realname'] : $member['nickname']),
			"联系方式: " . $member['mobile'],
			"购买套餐: " . $card['title'],
			"套餐期限: " . date('Y-m-d', $order['starttime']) . '~' . date('Y-m-d', $order['endtime']),
			"购买时间: " . date('Y-m-d H:i:s', $order['paytime']),
		);
		$params = array(
			'first' => $tips,
			'OrderSn' => $order['ordersn'],
			'OrderStatus' => '已生效',
			'remark' => implode("\n", $remark)
		);
		$send = sys_wechat_tpl_format($params);
		$status = $acc->sendTplNotice($maneger['openid'], $_W['we7_wmall']['config']['public_tpl'], $send);
	}
	if(!empty($order['openid'])) {
		//通知平台管理员
		$tips = "您成功订购了配送会员卡【{$card['title']}】";
		$remark = array(
			"购卡费用: {$order['final_fee']}元",
			"支付方式: " . $order['pay_type_cn'],
			"购买套餐: " . $card['title'],
			"套餐期限: " . date('Y-m-d', $order['starttime']) . '~' . date('Y-m-d', $order['endtime']),
			"购买时间: " . date('Y-m-d H:i:s', $order['paytime']),
		);
		$params = array(
			'first' => $tips,
			'OrderSn' => $order['ordersn'],
			'OrderStatus' => '已生效',
			'remark' => implode("\n", $remark)
		);
		$url = $_W['siteroot'] . 'app' . ltrim(murl('entry', array('do' => 'card', 'm' => 'we7_wmall')), '.');
		$send = sys_wechat_tpl_format($params);
		$status = $acc->sendTplNotice($order['openid'], $_W['we7_wmall']['config']['public_tpl'], $send, $url);
	}
	return true;
}