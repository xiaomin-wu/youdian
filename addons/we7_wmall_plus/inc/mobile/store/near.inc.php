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
$lat = trim($_GPC['__lat']);
$lng = trim($_GPC['__lng']);
if(empty($lat) || empty($lng)) {
	message('获取位置失败,请重新获取位置', $this->createMobileUrl('cindex'), 'error');
}
$stores = pdo_fetchall('select id,score,title,location_x,location_y from ' . tablename('tiny_wmall_plus_store') . " where uniacid = :uniacid and status = 1", array(':uniacid' => $_W['uniacid']));
if(empty($stores)) {
	message('平台还没有入驻的商家', referer(), 'error');
}
foreach($stores as &$store) {
	$store['distance'] = distanceBetween($store['location_y'], $store['location_x'], $lng, $lat);
	$store['distance'] = round($store['distance'] / 1000, 2);
}
$$stores = array_sort($stores, 'distance');
$key = trim($_GPC['key']);
$url = $this->createMobileUrl('cgoods', array('sid' => $stores[0]['id'], 'key' => $key));
header('location:' . $url);
die;




