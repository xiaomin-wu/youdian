<?php
/**
 * 外送系统
 * @author 新睿社区
 * @QQ 10373458 * @url http://www.010xr.com/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'list';

if($op == 'list') {
	$lat = trim($_GPC['lat']);
	$lng = trim($_GPC['lng']);
	if(empty($lat) || empty($lng)) {
		message(ierror(-1, '请提交用户位置'), '', 'ajax');
	}
	$discounts = store_discounts();
	$orderbys = store_orderbys();
	$condition = ' where uniacid = :uniacid and status = 1';
	$params = array(':uniacid' => $_W['uniacid']);
	if($_GPC['cid'] > 0) {
		$condition .= ' and cid like :cid';
		$params[':cid'] = "%|{$_GPC['cid']}|%";
	}

	$dis = trim($_GPC['dis']);
	if(!empty($dis)) {
		$condition .= " and {$dis} = {$discounts[$dis]['val']}";
	}
	$stores = pdo_fetchall('select id,title,logo,sailed,score,template,business_hours,delivery_price,delivery_free_price,send_price,delivery_time,delivery_mode,token_status,invoice_status,location_x,location_y,forward_mode from ' . tablename('tiny_wmall_plus_store') . $condition, $params);
	$min = 0;
	if(!empty($stores)) {
		foreach($stores as &$row) {
			$row['logo'] = tomedia($row['logo']);
			$row['business_hours'] = (array)iunserializer($row['business_hours']);
			$row['is_in_business_hours'] = store_is_in_business_hours($row['business_hours']);
			$row['hot_goods'] = pdo_fetchall('select title from ' . tablename('tiny_wmall_plus_goods') . ' where uniacid = :uniacid and sid = :sid and is_hot = 1 limit 3', array(':uniacid' => $_W['uniacid'], ':sid' => $row['id']));
			$row['activity'] = store_fetch_activity($row['id']);
			$row['activity']['activity_num'] += ($row['delivery_free_price'] > 0 ? 1 : 0);
			$row['score_cn'] = round($row['score'] / 5, 2) * 100;
			$row['url'] = store_forward_url($row['id'], $row['forward_mode']);

			$row['distance'] = distanceBetween($row['location_y'], $row['location_x'], $lng, $lat);
			$row['distance'] = round($row['distance'] / 1000, 2);
			if($row['is_in_business_hours'] == 1) {
				$row['is_in_business_hours_'] = 100000;
			}
			$row['displayorder_order'] = $row['displayorder'] + (($row['displayorder'] + 1) * $row['is_in_business_hours_']);
			$row['sailed_order'] = $row['sailed'] + (($row['sailed'] + 1) * $row['is_in_business_hours_']);
			$row['score_order'] = $row['score'] + (($row['score'] + 1) * $row['is_in_business_hours_']);
			$row['click_order'] = $row['click'] + (($row['click'] + 1) * $row['is_in_business_hours_']);
			$row['distance_order'] = $row['distance'] + ($row['distance'] + 1) * ($row['is_in_business_hours'] == 1 ? 0 : 100000);
			$row['send_price_order'] = $row['send_price'] + ($row['send_price'] + 1) * ($row['is_in_business_hours'] == 1 ? 0 : 1000);
			$row['delivery_time_order'] = $row['delivery_time'] + ($row['delivery_time'] + 1) * ($row['is_in_business_hours'] == 1 ? 0 : 1000);
		}
		$min = min(array_keys($stores));

		$order_by_type = trim($_GPC['order']) ? trim($_GPC['order']) : 'distance';
		if(in_array($order_by_type, array('distance', 'send_price', 'delivery_time'))) {
			$stores = array_sort($stores, "{$order_by_type}_order", SORT_ASC);
		} else {
			$stores = array_sort($stores, "{$order_by_type}_order", SORT_DESC);
		}
	}
	$stores = array_values($stores);
	$data = array(
		'list' => $stores,
		'max_id' => $max,
		'min_id' => $min,
		'more' => $more
	);
	message(ierror(0, '', $data), '', 'ajax');
}

if($op == 'detail') {
	$sid = intval($_GPC['sid']);
	$store = store_fetch($sid);
	if(empty($store)) {
		message(ierror(-1, '门店不存在或已删除'), '', 'ajax');
	}
	message(ierror(0, '', $store), '', 'ajax');
}
