<?php
/**
 * 外送系统
 * @author 灯火阑珊
 * @QQ 2471240272
 * @url http://bbs.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$_W['page']['title'] = '订单统计-' . $_W['we7_wmall']['config']['title'];
mload()->model('store');

$store = store_check();
$sid = $store['id'];
$do = 'stat';
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'list';

if($op == 'list') {
	$condition = " WHERE uniacid = :aid AND sid = :sid AND is_pay = 1 and status = 5";
	$params[':aid'] = $_W['uniacid'];
	$params[':sid'] = $sid;
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
	$stat = order_amount_stat($sid);
	include $this->template('store/stat');
}

if($op == 'order_num') {
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
		$data = pdo_fetchall("SELECT * FROM " . tablename('tiny_wmall_order') . 'WHERE uniacid = :uniacid AND sid = :sid and status = 5 and is_pay = 1 and addtime >= :starttime and addtime <= :endtime', array(':uniacid' => $_W['uniacid'], ':sid' => $sid, ':starttime' => $start, 'endtime' => $end));
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
		$data = pdo_fetchall("SELECT * FROM " . tablename('tiny_wmall_order') . 'WHERE uniacid = :uniacid AND sid = :sid and status = 5 and is_pay = 1 and addtime >= :starttime and addtime <= :endtime', array(':uniacid' => $_W['uniacid'], ':sid' => $sid, ':starttime' => $start, 'endtime' => $end));
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

if($op == 'day') {
	$orderby = trim($_GPC['orderby']) ? trim($_GPC['orderby']) : 'num';
	if($orderby == 'num') {
		$order_by = ' ORDER BY num DESC';
	} else {
		$order_by = ' ORDER BY price DESC';
	}

	$starttime = strtotime($_GPC['addtime']['start']);
	if(empty($_GPC['addtime']['end'])) {
		$endtime = $starttime + 86399;
	} else {
		$endtime = strtotime($_GPC['addtime']['end']) + 86399;
	}
	$data = array();
	$orders = pdo_fetchall('SELECT * FROM ' . tablename('tiny_wmall_order') . " WHERE uniacid = :aid AND sid = :sid AND is_pay = 1 and status = 5 and addtime >= :start AND addtime < :end", array(':sid' => $sid, ':aid' => $_W['uniacid'], ':start' => $starttime, ':end' => $endtime), 'id');
	$count = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('tiny_wmall_order') . " WHERE uniacid = :aid AND sid = :sid AND is_pay = 1 and status = 5  and addtime >= :start AND addtime < :end", array(':sid' => $sid, ':aid' => $_W['uniacid'], ':start' => $starttime, ':end' => $endtime));
	if(!empty($orders)) {
		$str = implode(',', array_keys($orders));
		$data = pdo_fetchall('SELECT *,SUM(goods_num) AS num, SUM(goods_price) AS price FROM ' . tablename('tiny_wmall_order_stat') . " WHERE uniacid = :aid AND sid = :sid AND oid IN ({$str}) GROUP BY goods_id" . $order_by, array(':aid' => $_W['uniacid'], ':sid' => $sid), 'goods_id');
		$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('tiny_wmall_order_stat') . " WHERE uniacid = :aid AND sid = :sid AND oid IN ({$str})", array(':aid' => $_W['uniacid'], ':sid' => $sid));
		$price = pdo_fetchcolumn('SELECT SUM(final_fee) FROM ' . tablename('tiny_wmall_order') . " WHERE uniacid = :aid AND sid = :sid AND id IN ({$str})", array(':aid' => $_W['uniacid'], ':sid' => $sid));
	}
	if(!empty($orders)) {
		foreach($orders as &$da) {
			$total_price = $da['final_fee'];
			if($da['pay_type'] == 'alipay') {
				$return['alipay']['price'] += $total_price;
				$return['alipay']['num'] += 1;
			} elseif($da['pay_type'] == 'wechat') {
				$return['wechat']['price'] += $total_price;
				$return['wechat']['num'] += 1;
			} elseif($da['pay_type'] == 'credit') {
				$return['credit']['price'] += $total_price;
				$return['credit']['num'] += 1;
			} elseif($da['pay_type'] == 'delivery') {
				$return['delivery']['price'] += $total_price;
				$return['delivery']['num'] += 1;
			} else {
				$return['cash']['price'] += $total_price;
				$return['cash']['num'] += 1;
			}
		}
	}
	include $this->template('store/stat-day');
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
		$data = pdo_fetchall("SELECT * FROM " . tablename('tiny_wmall_order') . 'WHERE uniacid = :uniacid AND sid = :sid and status = 5 and is_pay = 1 and addtime >= :starttime and addtime <= :endtime', array(':uniacid' => $_W['uniacid'], ':sid' => $sid, ':starttime' => $start, 'endtime' => $end));
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
		$data = pdo_fetchall("SELECT * FROM " . tablename('tiny_wmall_order') . 'WHERE uniacid = :uniacid AND sid = :sid and status = 5 and is_pay = 1 and addtime >= :starttime and addtime <= :endtime', array(':uniacid' => $_W['uniacid'], ':sid' => $sid, ':starttime' => $start, 'endtime' => $end));
		foreach($data as $da) {
			if(in_array($da['pay_type'], array_keys($datasets))) {
				$datasets[$da['pay_type']]['value'] += 1;
			}
		}
		$datasets = array_values($datasets);
		message(error(0, $datasets), '', 'ajax');
	}
}


