<?php
/**
 * 外送系统
 * @author 灯火阑珊
 * @QQ 2471240272
 * @url http://bbs.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$do = 'card';
$this->checkAuth();
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'index';
$title = '配送会员卡';

$config = sys_delivery_config();
if($config['card_apply_status'] != 1) {
	$this->imessage('平台未开启会员卡功能', referer(), 'info');
}

if($op == 'index') {
	//删除半小时以前的订单
	pdo_query('delete from ' . tablename('tiny_wmall_delivery_cards_order') . ' where uniacid = :uniacid and is_pay = 0 and addtime < :addtime', array(':uniacid' => $_W['uniacid'], ':addtime' => TIMESTAMP - 3600));

}

if($op == 'apply') {
	$payment = $_W['we7_wmall']['config']['payment'];
	$pay_types = order_pay_types();
	$endtime = strtotime(date('Y-m-d'));
	if($_W['member']['setmeal_endtime'] > 0) {
		$endtime = $_W['member']['setmeal_endtime'];
	}
	$cards = pdo_fetchall('select * from ' . tablename('tiny_wmall_delivery_cards') . ' where uniacid = :uniacid and status = 1 order by displayorder desc, id asc', array(':uniacid' => $_W['uniacid']));
	if(!empty($cards)) {
		foreach($cards as &$row) {
			$row['endtime'] = date('Y-m-d', strtotime("{$row['days']}days", $endtime));
		}
	}
}

if($op == 'pay') {
	$id = intval($_GPC['setmeal_id']);
	$card = pdo_get('tiny_wmall_delivery_cards', array('uniacid' => $_W['uniacid'], 'id' => $id));
	if(empty($card)) {
		message(error(-1, '会员卡套餐不存在'), '', 'ajax');
	}
	$pay_type = trim($_GPC['pay_type']);
	if(!in_array($pay_type, array('alipay', 'wechat', 'credit'))) {
		message(error(-1, '支付方式错误'), '', 'ajax');
	}
	$order = array(
		'uniacid' => $_W['uniacid'],
		'acid' => $_W['acid'],
		'uid' => $_W['member']['uid'],
		'openid' => $_W['openid'],
		'ordersn' => date('YmdH') . random(4, true),
		'card_id' => $card['id'],
		'final_fee' => $card['price'],
		'pay_type' => $pay_type,
		'is_pay' => 0,
		'addtime' => TIMESTAMP,
	);
	pdo_insert('tiny_wmall_delivery_cards_order', $order);
	$id = pdo_insertid();
	message(error(0, $id), '', 'ajax');
}
include $this->template('card');