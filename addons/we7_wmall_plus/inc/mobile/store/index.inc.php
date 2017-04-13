<?php
/**
 * 外送系统
 * @author 新睿社区
 * @QQ 10373458 * @url http://www.010xr.com/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$do = 'index';
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'index';
mload()->model('store');
$title = '';
$config = $_W['we7_wmall_plus']['config'];
if($config['version'] == 2) {
	$url = $this->createMobileUrl('goods', array('sid' => $_W['we7_wmall_plus']['config']['default_sid']));
	header('location:' . $url);
	die;
}
$slides = sys_fetch_slide(2);
$categorys = store_fetchall_category();
$categorys_chunk = array_chunk($categorys, 8);
$notices = pdo_fetchall('select id,title,link,displayorder,status from' .tablename('tiny_wmall_plus_notice') ." where uniacid = :uniacid and status = 1 order by displayorder desc", array(':uniacid' => $_W['uniacid']));
$orderbys = store_orderbys();
$discounts = store_discounts();
$recommends = pdo_fetchall('select id,title,logo from' .tablename('tiny_wmall_plus_store') . 'where uniacid = :uniacid and is_recommend = 1 limit 6', array(':uniacid' => $_W['uniacid']));

if($op == 'list') {
	$lat = trim($_GPC['lat']);
	$lng = trim($_GPC['lng']);
	isetcookie('__lat', $lat, 120);
	isetcookie('__lng', $lng, 120);
	$stores = pdo_fetchall('select id,score,title,logo,sailed,score,label,business_hours,is_in_business,delivery_fee_mode,delivery_price,delivery_free_price,send_price,delivery_time,delivery_mode,token_status,invoice_status,location_x,location_y,forward_mode,forward_url,displayorder,click from ' . tablename('tiny_wmall_plus_store') . " where uniacid = :uniacid and status = 1", array(':uniacid' => $_W['uniacid']));
	$min = 0;
	if(!empty($stores)) {
		$store_label = category_store_label();
		foreach($stores as &$da) {
			$da['logo'] = tomedia($da['logo']);
			$da['business_hours'] = (array)iunserializer($da['business_hours']);
			$da['is_in_business_hours'] = ($da['is_in_business'] && store_is_in_business_hours($da['business_hours']));
			$da['hot_goods'] = pdo_fetchall('select title from ' . tablename('tiny_wmall_plus_goods') . ' where uniacid = :uniacid and sid = :sid and is_hot = 1 limit 3', array(':uniacid' => $_W['uniacid'], ':sid' => $da['id']));
			$da['sailed']= pdo_fetchcolumn('select sum(sailed) from ' . tablename('tiny_wmall_plus_goods') . ' where uniacid = :uniacid and sid = :sid ', array(':uniacid' => $_W['uniacid'], ':sid' => $da['id']));
			$da['activity'] = store_fetch_activity($da['id']);
			$da['activity']['activity_num'] += ($da['delivery_free_price'] > 0 ? 1 : 0);
			$da['score_cn'] = round($da['score'] / 5, 2) * 100;
			$da['url'] = store_forward_url($da['id'], $da['forward_mode'], $da['forward_url']);
			if($da['label'] > 0) {
				$da['label_color'] = $store_label[$da['label']]['color'];
				$da['label_cn'] = $store_label[$da['label']]['title'];
			}
			if($da['delivery_fee_mode'] == 2) {
				$da['delivery_price'] = iunserializer($da['delivery_price']);
				$da['delivery_price'] = $da['delivery_price']['start_fee'];
			}
//			if(!empty($lng) && !empty($lat)) {
//				$da['distance'] = distanceBetween($da['location_y'], $da['location_x'], $lng, $lat);
//				$da['distance'] = round($da['distance'] / 1000, 2);
//			} else {
//				$da['distance'] = 0;
//			}
            $da['distance'] = 0;
			if($da['is_in_business_hours'] == 1) {
				$da['is_in_business_hours_'] = 100000;
			}
			$da['displayorder_order'] = $da['displayorder'] + (($da['displayorder'] + 1) * $da['is_in_business_hours_']);
			$da['sailed_order'] = $da['sailed'] + (($da['sailed'] + 1) * $da['is_in_business_hours_']);
			$da['score_order'] = $da['score'] + (($da['score'] + 1) * $da['is_in_business_hours_']);
			$da['click_order'] = $da['click'] + (($da['click'] + 1) * $da['p']);
			$da['distance_order'] = $da['distance'] + $da['distance'] * ($da['is_in_business_hours'] == 1 ? 0 : 100000);
		}
		$min = min(array_keys($stores));
		$order_by_type = $config['store_orderby_type'] ? $config['store_orderby_type'] : 'distance';
		if(empty($lat)) {
			$order_by_type = 'displayorder';
		}
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
	'title' => $_W['we7_wmall_plus']['config']['title'],
	'desc' => $_W['we7_wmall_plus']['config']['content'],
	'link' => murl('entry', array('m' => 'we7_wmall_plus', 'do' => 'index'), true, true),
	'imgUrl' => tomedia($_W['we7_wmall_plus']['config']['thumb'])
);
include $this->template('index');
