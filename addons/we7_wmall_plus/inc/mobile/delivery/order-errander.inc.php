<?php
/**
 * 外送系统
 * @author 新睿社区
 * @QQ 10373458 * @url http://www.010xr.com/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
mload()->model('errander');
$do = 'dyorder-errander';
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'list';
$title = '跑腿订单管理';

$delivery_type = $_W['we7_wmall_plus']['deliveryer']['type'];
$deliveryer = $_W['we7_wmall_plus']['deliveryer']['user'];
if($delivery_type == 2) {
	$this->imessage('您没有配送订单的权限', $this->createMobileUrl('mine'), 'error', '请联系平台管理员开通权限', '返回');
}
$config_errander = $_W['we7_wmall_plus']['config']['errander'];

if($op == 'list') {
	$condition = ' where uniacid = :uniacid and is_pay = 1 and status != 4';
	$params[':uniacid'] = $_W['uniacid'];
	$status = isset($_GPC['status']) ? intval($_GPC['status']) : 1;
	$condition .= ' and delivery_status = :status';
	$params[':status'] = $status;
	if($status != 1) {
		$condition .= ' and deliveryer_id = :deliveryer_id';
		$params[':deliveryer_id'] = $deliveryer['id'];
		$condition .= ' order by id desc limit 15';
	}
	$orders = pdo_fetchall('SELECT * FROM' . tablename('tiny_wmall_plus_errander_order') . $condition, $params, 'id');
	$min = 0;
	if(!empty($orders)) {
		$types = errander_types();
		$delivery_status = errander_order_delivery_status();
		foreach($orders as &$order) {
			$order['order_type_cn'] = $types[$order['order_type']]['text'];
			$order['order_type_bg'] = $types[$order['order_type']]['bg'];
			$order['delivery_status_cn'] = $delivery_status[$order['delivery_status']]['text'];
			$order['delivery_status_color'] = $delivery_status[$order['delivery_status']]['color'];
			if(empty($order['buy_address'])) {
				$order['buy_address'] = '用户未指定,您可以自由寻找商户购买';
			}
			if(empty($order['goods_price'])) {
				$order['goods_price'] = '未填写,请联系顾客沟通';
			}
		}
		$min = min(array_keys($orders));
	}

	include $this->template('delivery/order-errander-list');
}

if($op == 'more') {
	$id = intval($_GPC['id']);
	$status = intval($_GPC['status']);
	$orders = pdo_fetchall('select * from ' . tablename('tiny_wmall_plus_errander_order') . ' where uniacid = :uniacid and status != 4 and delivery_status = :delivery_status and deliveryer_id = :deliveryer_id and id < :id order by id desc limit 15', array(':uniacid' => $_W['uniacid'], ':delivery_status' => $status, ':deliveryer_id' => $deliveryer['id'], ':id' => $id), 'id');
	if(!empty($orders)) {
		$types = errander_types();
		foreach($orders as &$order) {
			$order['addtime'] = date('Y-m-d H:i',$order['addtime']);
			$order['order_type_cn'] = $types[$order['order_type']]['text'];
			$order['order_type_bg'] = $types[$order['order_type']]['bg'];
			$order['delivery_status_cn'] = $delivery_status[$order['delivery_status']]['text'];
			$order['delivery_status_color'] = $delivery_status[$order['delivery_status']]['color'];
		}
	}
	$min = 0;
	if(!empty($orders)) {
		$min = min(array_keys($orders));
	}
	$orders = array_values($orders);
	$respon = array('error' => 0, 'message' => $orders, 'min' => $min);
	message($respon, '', 'ajax');
}

if($op == 'detail') {
	$title = '订单详情';
	$id = intval($_GPC['id']);
	$order = errander_order_fetch($id);
	if(empty($order)) {
		message('订单不存在或已删除', '', 'error');
	}
	$deliveryer = deliveryer_fetch($order['deliveryer_id']);
	include $this->template('delivery/order-errander-detail');
}

//抢单
if($op == 'collect') {
	$id = intval($_GPC['id']);
	$order = errander_order_fetch($id);
	if(empty($order)) {
		message(error(-1, '订单不存在或已删除'), '', 'ajax');
	}
	$status = errander_order_status_update($id, 'delivery_assign', array('deliveryer_id' => $deliveryer['id']));
	if(is_error($status)) {
		message(error(-1, $status['message']), '', 'ajax');
	}
	message(error(0, '抢单成功'), '', 'ajax');
}

//确认已取货
if($op == 'instore') {
	$id = intval($_GPC['id']);
	$order = errander_order_fetch($id);
	if(empty($order)) {
		message(error(-1, '订单不存在或已删除'), '', 'ajax');
	}
	$status = errander_order_status_update($id, 'delivery_instore', array('deliveryer_id' => $deliveryer['id']));
	if(is_error($status)) {
		message(error(-1, $status['message']), '', 'ajax');
	}
	message(error(0, '确认取货成功'), '', 'ajax');
}

//配送成功
if($op == 'success') {
	$id = intval($_GPC['id']);
	$status = errander_order_status_update($id, 'delivery_success', array('deliveryer_id' => $deliveryer['id'], 'code' => trim($_GPC['code'])));
	if(is_error($status)) {
		message(error(-1, $status['message']), '', 'ajax');
	}
	message(error(0, '确认送达成功'), '', 'ajax');
}





