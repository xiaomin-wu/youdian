<?php
/**
 * 外送系统
 * @author 灯火阑珊
 * @QQ 2471240272
 * @url http://bbs.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$do = 'index';
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'index';
mload()->model('store');
$title = '';
$config = $_W['we7_wmall']['config'];
if($config['version'] == 2) {
	$url = $this->createMobileUrl('goods', array('sid' => $_W['we7_wmall']['config']['default_sid']));
	header('location:' . $url);
	die;
}


$slides = sys_fetch_slide(2);
$categorys = store_fetchall_category();
$categorys_chunk = array_chunk($categorys, 8);

$orderbys = store_orderbys();
$discounts = store_discounts();
if($op == 'list') {
	$lat = trim($_GPC['lat']);
	$lng = trim($_GPC['lng']);
	isetcookie('__lat', $lat, 120);
	isetcookie('__lng', $lng, 120);
	$stores = pdo_fetchall('select id,score,title,logo,sailed,score,business_hours,is_in_business,delivery_price,delivery_free_price,send_price,delivery_time,delivery_mode,token_status,invoice_status,location_x,location_y,forward_mode,displayorder,click from ' . tablename('tiny_wmall_store') . " where uniacid = :uniacid and status = 1", array(':uniacid' => $_W['uniacid']));
	$min = 0;
	if(!empty($stores)) {
		foreach($stores as &$da) {
			$da['logo'] = tomedia($da['logo']);
			$da['business_hours'] = (array)iunserializer($da['business_hours']);
			$da['is_in_business_hours'] = ($da['is_in_business'] && store_is_in_business_hours($da['business_hours']));
			$da['hot_goods'] = pdo_fetchall('select title from ' . tablename('tiny_wmall_goods') . ' where uniacid = :uniacid and sid = :sid and is_hot = 1 limit 3', array(':uniacid' => $_W['uniacid'], ':sid' => $da['id']));
			$da['activity'] = store_fetch_activity($da['id']);
			$da['activity']['activity_num'] += ($da['delivery_free_price'] > 0 ? 1 : 0);
			$da['score_cn'] = round($da['score'] / 5, 2) * 100;
			$da['url'] = store_forward_url($da['id'], $da['forward_mode']);
			$da['distance'] = distanceBetween($da['location_y'], $da['location_x'], $lng, $lat);
			$da['distance'] = round($da['distance'] / 1000, 2);
			if($da['is_in_business_hours'] == 1) {
				$da['is_in_business_hours_'] = 100000;
			}
			$da['displayorder_order'] = $da['displayorder'] + (($da['displayorder'] + 1) * $da['is_in_business_hours_']);
			$da['sailed_order'] = $da['sailed'] + (($da['sailed'] + 1) * $da['is_in_business_hours_']);
			$da['score_order'] = $da['score'] + (($da['score'] + 1) * $da['is_in_business_hours_']);
			$da['click_order'] = $da['click'] + (($da['click'] + 1) * $da['is_in_business_hours_']);
			$da['distance_order'] = $da['distance'] + $da['distance'] * ($da['is_in_business_hours'] == 1 ? 0 : 100000);
		}
		$min = min(array_keys($stores));
		$order_by_type = $config['store_orderby_type'] ? $config['store_orderby_type'] : 'distance';
		if(in_array($order_by_type, array('distance'))) {
			$stores = array_sort($stores, "{$order_by_type}_order", SORT_ASC);
		} else {
			$stores = array_sort($stores, "{$order_by_type}_order", SORT_DESC);
		}
	}
	$stores = array_values($stores);
	$respon = array('error' => 0, 'message' => $stores, 'min' => $min);
	message($respon, '', 'ajax');
}

$address_id = intval($_GPC['aid']);
if($address_id > 0) {
	isetcookie('__aid', $address_id, 1800);
}

$_share = array(
	'title' => $_W['we7_wmall']['config']['title'],
	'desc' => $_W['we7_wmall']['config']['content'],
	'link' => murl('entry', array('m' => 'we7_wmall', 'do' => 'index'), true, true),
	'imgUrl' => tomedia($_W['we7_wmall']['config']['thumb'])
);
include $this->template('index');



