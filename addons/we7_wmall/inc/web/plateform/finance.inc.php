<?php
/**
 * 外送系统
 * @author 灯火阑珊
 * @QQ 2471240272
 * @url http://bbs.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$_W['page']['title'] = '财务统计-' . $_W['we7_wmall']['config']['title'];
mload()->model('finance');
$do = 'finance';
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'list';

$stores = pdo_getall('tiny_wmall_store', array('uniacid' => $_W['uniacid']), array('id', 'title'), 'id');
$stores = array(0 => array('id' => 0, 'title' => '所有门店')) + $stores;

if($op == 'list') {
	$condition = " WHERE uniacid = :aid AND is_pay = 1 and status = 5";
	$params[':aid'] = $_W['uniacid'];
	$sid = intval($_GPC['sid']);
	if($sid > 0) {
		$condition .= ' and sid = :sid';
		$params[':sid'] = $sid;
	}
	if(!empty($_GPC['addtime'])) {
		$starttime = strtotime($_GPC['addtime']['start']);
		$endtime = strtotime($_GPC['addtime']['end']) + 86399;
	} else {
		$starttime = strtotime(date('Y-m'));
		$endtime = TIMESTAMP;
	}
	$condition .= " AND addtime > :start AND addtime < :end";
	$params[':start'] = $starttime;
	$params[':end'] = $endtime;

	$count = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('tiny_wmall_order') .  $condition, $params);
	$data = pdo_fetchall('SELECT * FROM ' . tablename('tiny_wmall_order') . $condition, $params);
	$total = array();
	if(!empty($data)) {
		foreach($data as &$da) {
			$total_price = $da['final_fee'];
			$key = date('Y-m-d', $da['addtime']);
			$return[$key]['price'] += $total_price;
			$return[$key]['count'] += 1;
			$total['total_price'] += $total_price;
			$total['total_count'] += 1;
			if($da['pay_type'] == 'alipay') {
				$return[$key]['alipay'] += $total_price;
				$total['total_alipay'] += $total_price;
			} elseif($da['pay_type'] == 'wechat') {
				$return[$key]['wechat'] += $total_price;
				$total['total_wechat'] += $total_price;
			} elseif($da['pay_type'] == 'credit') {
				$return[$key]['credit'] += $total_price;
				$total['total_credit'] += $total_price;
			} elseif($da['pay_type'] == 'delivery') {
				$return[$key]['delivery'] += $total_price;
				$total['total_delivery'] += $total_price;
			} else {
				$return[$key]['cash'] += $total_price;
				$total['total_cash'] += $total_price;
			}
		}
	}
	//订单统计
	$stat = finance_amount_stat($sid);
}

if($op == 'order_num') {
	$start = $_GPC['start'] ? strtotime($_GPC['start']) : strtotime(date('Y-m'));
	$end = $_GPC['end'] ? strtotime($_GPC['end']) + 86399 : (strtotime(date('Y-m-d')) + 86399);
	$day_num = ($end - $start) / 86400;

	if($_W['isajax'] && $_W['ispost']) {
		$days = array();
		$datasets = array(
			'flow1' => array(),
		);
		for($i = 0; $i < $day_num; $i++){
			$key = date('m-d', $start + 86400 * $i);
			$days[$key] = 0;
			$datasets['flow1'][$key] = 0;
		}
		$condition = " WHERE uniacid = :uniacid AND is_pay = 1 and status = 5 and addtime >= :starttime and addtime <= :endtime";
		$params = array(':uniacid' => $_W['uniacid'], ':starttime' => $start, ':endtime' => $end);
		$sid = intval($_GPC['sid']);
		if($sid > 0) {
			$condition .= ' and sid = :sid';
			$params[':sid'] = $sid;
		}

		$data = pdo_fetchall("SELECT id, final_fee, addtime FROM " . tablename('tiny_wmall_order') . $condition, $params);
		foreach($data as $da) {
			$key = date('m-d', $da['addtime']);
			if(in_array($key, array_keys($days))) {
				$datasets['flow1'][$key]++;
			}
		}
		$shuju['label'] = array_keys($days);
		$shuju['datasets'] = $datasets;
		exit(json_encode($shuju));
	}
}

if($op == 'order_price') {
	$start = $_GPC['start'] ? strtotime($_GPC['start']) : strtotime(date('Y-m'));
	$end= $_GPC['end'] ? strtotime($_GPC['end']) + 86399 : (strtotime(date('Y-m-d')) + 86399);
	$day_num = ($end - $start) / 86400;

	if($_W['isajax'] && $_W['ispost']) {
		$days = array();
		$datasets = array(
			'flow1' => array(),
		);
		for($i = 0; $i < $day_num; $i++){
			$key = date('m-d', $start + 86400 * $i);
			$days[$key] = 0;
			$datasets['flow1'][$key] = 0;
		}
		$condition = " WHERE uniacid = :uniacid AND is_pay = 1 and status = 5 and addtime >= :starttime and addtime <= :endtime";
		$params = array(':uniacid' => $_W['uniacid'], ':starttime' => $start, ':endtime' => $end);
		$sid = intval($_GPC['sid']);
		if($sid > 0) {
			$condition .= ' and sid = :sid';
			$params[':sid'] = $sid;
		}
		$data = pdo_fetchall("SELECT id, final_fee, addtime FROM " . tablename('tiny_wmall_order') . $condition, $params);
		foreach($data as $da) {
			$key = date('m-d', $da['addtime']);
			if(in_array($key, array_keys($days))) {
				$datasets['flow1'][$key] += $da['final_fee'];
			}
		}
		$shuju['label'] = array_keys($days);
		$shuju['datasets'] = $datasets;
		exit(json_encode($shuju));
	}
}

if($op == 'day_order_price') {
	$start = $_GPC['start'] ? strtotime($_GPC['start']) : strtotime(date('Y-m'));
	$end= $_GPC['end'] ? strtotime($_GPC['end']) + 86399 : (strtotime(date('Y-m-d')) + 86399);
	if($_W['isajax'] && $_W['ispost']) {
		$datasets = array(
			'wechat' => array('name' => '微信支付', 'value' => 0),
			'alipay' => array('name' => '支付宝支付', 'value' => 0),
			'credit' => array('name' => '余额支付', 'value' => 0),
			'cash' => array('name' => '现金支付', 'value' => 0),
			'delivery' => array('name' => '货到付款', 'value' => 0)
		);
		$condition = 'WHERE uniacid = :uniacid and status = 5 and is_pay = 1 and addtime >= :starttime and addtime <= :endtime';
		$params = array(':uniacid' => $_W['uniacid'], ':starttime' => $start, 'endtime' => $end);
		$sid = intval($_GPC['sid']);
		if($sid > 0) {
			$condition .= ' and sid = :sid';
			$params[':sid'] = $sid;
		}
		$data = pdo_fetchall("SELECT * FROM " . tablename('tiny_wmall_order') . $condition, $params);
		foreach($data as $da) {
			if(in_array($da['pay_type'], array_keys($datasets))) {
				$datasets[$da['pay_type']]['value'] += $da['final_fee'];
			}
		}
		$datasets = array_values($datasets);
		message(error(0, $datasets), '', 'ajax');
	}
}

if($op == 'day_order_num') {
	$start = $_GPC['start'] ? strtotime($_GPC['start']) : strtotime(date('Y-m'));
	$end= $_GPC['end'] ? strtotime($_GPC['end']) + 86399 : (strtotime(date('Y-m-d')) + 86399);
	if($_W['isajax'] && $_W['ispost']) {
		$datasets = array(
			'wechat' => array('name' => '微信支付', 'value' => 0),
			'alipay' => array('name' => '支付宝支付', 'value' => 0),
			'credit' => array('name' => '余额支付', 'value' => 0),
			'cash' => array('name' => '现金支付', 'value' => 0),
			'delivery' => array('name' => '货到付款', 'value' => 0)
		);
		$condition = 'WHERE uniacid = :uniacid and status = 5 and is_pay = 1 and addtime >= :starttime and addtime <= :endtime';
		$params = array(':uniacid' => $_W['uniacid'], ':starttime' => $start, 'endtime' => $end);
		$sid = intval($_GPC['sid']);
		if($sid > 0) {
			$condition .= ' and sid = :sid';
			$params[':sid'] = $sid;
		}
		$data = pdo_fetchall("SELECT * FROM " . tablename('tiny_wmall_order') . $condition, $params);
		foreach($data as $da) {
			if(in_array($da['pay_type'], array_keys($datasets))) {
				$datasets[$da['pay_type']]['value'] += 1;
			}
		}
		$datasets = array_values($datasets);
		message(error(0, $datasets), '', 'ajax');
	}
}
include $this->template('plateform/finance');
