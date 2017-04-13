<?php
/**
 * 外送系统
 * @author 新睿社区
 * @QQ 10373458 * @url http://www.010xr.com/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$do = 'index';
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'list';
mload()->model('store');
$title = '';

$slides = sys_fetch_slide(2);
$categorys = store_fetchall_category();
$categorys_chunk = array_chunk($categorys, 8);

if($op == 'list') {
	if($_W['isajax']) {
		$lat = trim($_GPC['lat']);
		$lng = trim($_GPC['lng']);
		isetcookie('__lat', $lat, 600);
		isetcookie('__lng', $lng, 600);
		exit();
	}
	$goods = pdo_fetchall('select * from ' . tablename('tiny_wmall_plate_goods') . " where uniacid = :uniacid order by displayorder desc", array(':uniacid' => $_W['uniacid']));
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
include $this->template('cindex');



