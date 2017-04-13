<?php
/**
 * 外送系统
 * @author 新睿社区
 * @QQ 10373458 * @url http://www.010xr.com/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$_W['page']['title'] = '订单统计-' . $_W['we7_wmall_plus']['config']['title'];
mload()->model('store');

$store = store_check();
$sid = $store['id'];
$do = 'stat';
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'order';

if($op == 'order') {
	$condition = ' WHERE uniacid = :uniacid AND sid = :sid';
	$params = array(
		':uniacid' => $_W['uniacid'],
		':sid' => $sid,
	);
	$days = isset($_GPC['days']) ? intval($_GPC['days']) : 0;
	if($days == -1) {
		$starttime = str_replace('-', '', trim($_GPC['stat_day']['start']));
		$endtime = str_replace('-', '', trim($_GPC['stat_day']['end']));
		$condition .= ' and stat_day >= :start_day and stat_day <= :end_day';
		$params[':start_day'] = $starttime;
		$params[':end_day'] = $endtime;
	} else {
		$todaytime = strtotime(date('Y-m-d'));
		$starttime = date('Ymd', strtotime("-{$days} days", $todaytime));
		$endtime = date('Ymd', $todaytime + 86399);
		$condition .= ' and stat_day >= :stat_day';
		$params[':stat_day'] = $starttime;
	}
	if($_W['isajax']) {
		$stat = array();
		$stat['total_fee'] = floatval(pdo_fetchcolumn('select round(sum(total_fee), 2) from ' . tablename('tiny_wmall_plus_order') . $condition . ' and status = 5 and is_pay = 1', $params));
		$stat['store_final_fee'] = floatval(pdo_fetchcolumn('select round(sum(store_final_fee), 2) from ' . tablename('tiny_wmall_plus_order') . $condition . ' and status = 5 and is_pay = 1', $params));
		$stat['total_success_order'] = pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_plus_order') . $condition . ' and status = 5 and is_pay = 1', $params);
		$stat['total_cancel_order'] = pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_plus_order') . $condition . ' and status = 6', $params);
		$stat['total_cancel_fee'] = floatval(pdo_fetchcolumn('select round(sum(total_fee), 2) from ' . tablename('tiny_wmall_plus_order') . $condition . ' and status = 6', $params));
		$stat['avg_pre_order'] = floatval($stat['total_success_order'] > 0 ? ($stat['total_fee'] / $stat['total_success_order']) : 0);

		$chart = array(
			'stat' => $stat,
			'fields' => array('total_success_order', 'total_fee', 'store_final_fee', 'store_discount_fee', 'plateform_discount_fee', 'plateform_serve_fee', 'plateform_delivery_fee', 'total_cancel_order', 'total_cancel_fee'),
			'titles' => array('有效订单量','营业总额','总收入','商家补贴','平台补贴','平台服务费','平台配送费','无效订单量','损失营业额'),
		);
		for($i = $starttime; $i <= $endtime;) {
			$chart['days'][] = $i;
			foreach($chart['fields'] as $field) {
				$chart[$field][$i] = 0;
			}
			$i = date('Ymd', strtotime($i) + 86400);
		}
		$records = pdo_fetchall('SELECT stat_day, count(*) as total_success_order,round(sum(total_fee), 2) as total_fee, round(sum(final_fee), 2) as final_fee, round(sum(store_final_fee), 2) as store_final_fee, round(sum(plateform_discount_fee), 2) as plateform_discount_fee, round(sum(store_discount_fee), 2) as store_discount_fee, round(sum(plateform_serve_fee), 2) as plateform_serve_fee, round(sum(plateform_delivery_fee), 2) as plateform_delivery_fee
		FROM ' . tablename('tiny_wmall_plus_order') . $condition . ' and status = 5 and is_pay = 1 group by stat_day', $params);
		if(!empty($records)) {
			foreach($records as $record) {
				if(in_array($record['stat_day'], $chart['days'])) {
					foreach($chart['fields'] as $field) {
						$chart[$field][$record['stat_day']] += $record[$field];
					}
				}
			}
		}
		$cancel_records = pdo_fetchall('SELECT stat_day, count(*) as total_cancel_order, sum(total_fee) as total_cancel_fee
		FROM ' . tablename('tiny_wmall_plus_order') . $condition . ' and status = 6 group by stat_day', $params);
		if(!empty($cancel_records)) {
			foreach($cancel_records as $record) {
				if(in_array($record['stat_day'], $chart['days'])) {
					foreach($chart['fields'] as $field) {
						$chart[$field][$record['stat_day']] += $record[$field];
					}
				}
			}
		}
		foreach($chart['fields'] as $field) {
			$chart[$field] = array_values($chart[$field]);
		}
		message(error(0, $chart), '', 'ajax');
	}

	$records_temp = pdo_fetchall('SELECT stat_day, count(*) as total_success_order, round(sum(final_fee), 2) as final_fee, round(sum(store_final_fee), 2) as store_final_fee, round(sum(plateform_discount_fee), 2) as plateform_discount_fee, round(sum(store_discount_fee), 2) as store_discount_fee, round(sum(plateform_serve_fee), 2) as plateform_serve_fee, round(sum(plateform_delivery_fee), 2) as plateform_delivery_fee
	 FROM ' . tablename('tiny_wmall_plus_order') . $condition . ' and status = 5 and is_pay = 1 group by stat_day', $params, 'stat_day');
	$cancel_records = pdo_fetchall('SELECT stat_day, count(*) as total_cancel_order, round(sum(store_final_fee), 2) as store_final_fee
	 FROM ' . tablename('tiny_wmall_plus_order') . $condition . ' and status = 6 group by stat_day', $params, 'stat_day');
	$records = array();
	for($i = $endtime; $i >= $starttime;) {
		if(empty($records_temp[$i])) {
			$records[] = array(
				'stat_day' => $i,
				'total_success_order' => 0,
				'final_fee' => 0,
				'store_final_fee' => 0,
				'plateform_discount_fee' => 0,
				'store_discount_fee' => 0,
				'plateform_serve_fee' => 0,
				'plateform_delivery_fee' => 0,
			);
		} else {
			$records[] = $records_temp[$i];
		}
		$i = date('Ymd', strtotime($i) - 86400);
	}
	include $this->template('store/stat-order');
}

if($op == 'goods') {
	$condition = ' WHERE uniacid = :uniacid AND sid = :sid and status = 5';
	$params = array(
		':uniacid' => $_W['uniacid'],
		':sid' => $sid,
	);
	$days = isset($_GPC['days']) ? intval($_GPC['days']) : 0;
	if($days == -1) {
		$starttime = str_replace('-', '', trim($_GPC['stat_day']['start']));
		$endtime = str_replace('-', '', trim($_GPC['stat_day']['end']));
		$condition .= ' and stat_day >= :start_day and stat_day <= :end_day';
		$params[':start_day'] = $starttime;
		$params[':end_day'] = $endtime;
	} else {
		$todaytime = strtotime(date('Y-m-d'));
		$starttime = date('Ymd', strtotime("-{$days} days", $todaytime));
		$endtime = date('Ymd', $todaytime + 86399);
		$condition .= ' and stat_day >= :stat_day';
		$params[':stat_day'] = $starttime;
	}
	$orderby = trim($_GPC['orderby']) ? trim($_GPC['orderby']) : 'total_goods_price';
	$stat = array();
	$stat['total_goods_num'] = intval(pdo_fetchcolumn('select sum(goods_num) from ' . tablename('tiny_wmall_plus_order_stat') . $condition, $params));
	$stat['total_goods_price'] = floatval(pdo_fetchcolumn('select round(sum(goods_price), 2) from ' . tablename('tiny_wmall_plus_order_stat') . $condition, $params));
	$records = pdo_fetchall('select stat_day, goods_id, goods_title, sum(goods_price) as total_goods_price, sum(goods_num) as total_goods_num from ' . tablename('tiny_wmall_plus_order_stat') . $condition . " group by goods_id order by {$orderby} desc", $params);
	$chart = array(
		'field' => array('goods_num', 'goods_price'),
		'title' => array(),
		'data' => array()
	);
	if(!empty($records)) {
		foreach($records as &$row) {
			$row['pre_goods_price'] = round($row['total_goods_price'] / $stat['total_goods_price'], 2) * 100 . '%';
			$row['pre_goods_num'] = round($row['total_goods_num'] / $stat['total_goods_num'], 2) * 100 . '%';
			$chart['title'][] = $row['goods_title'];
			$chart['data']['goods_price'][] = array(
				'name' => $row['goods_title'],
				'value' => $row['total_goods_price']
			);
			$chart['data']['goods_num'][] = array(
				'name' => $row['goods_title'],
				'value' => $row['total_goods_num']
			);
		}
	}
	if($_W['isajax']) {
		message(error(0, $chart), '', 'ajax');
	}
	include $this->template('store/stat-goods');
}
