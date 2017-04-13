<?php
/**
 * 外送系统
 * @author 新睿社区
 * @QQ 10373458 * @url http://www.010xr.com/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$callback = $_GPC['callback'];
$discounts = store_discounts();
$data = array();
$data['sys'] = array(
	'title' => '平台链接',
	'items' => array(
		array(
			'title' => '平台首页',
			'url' => murl('entry', array('m' => 'we7_wmall_plus', 'do' => 'index'), true, false)
		),
		array(
			'title' => '我的订单',
			'url' => murl('entry', array('m' => 'we7_wmall_plus', 'do' => 'order'), true, false)
		),
		array(
			'title' => '我的收货地址',
			'url' => murl('entry', array('m' => 'we7_wmall_plus', 'do' => 'address'), true, false)
		),
		array(
			'title' => '我的收藏',
			'url' => murl('entry', array('m' => 'we7_wmall_plus', 'do' => 'favorite'), true, false)
		),
		array(
			'title' => '我的评价',
			'url' => murl('entry', array('m' => 'we7_wmall_plus', 'do' => 'comment'), true, false)
		),
		array(
			'title' => '配送会员卡',
			'url' => murl('entry', array('m' => 'we7_wmall_plus', 'do' => 'card'), true, false)
		),
		array(
			'title' => '领券中心',
			'url' => murl('entry', array('m' => 'we7_wmall_plus', 'do' => 'coupon-channel'), true, false)
		),
	)
);

$data['dis'] = array(
	'title' => '优惠活动',
	'items' => array()
);
foreach($discounts as $row) {
	$data['dis']['items'][] = array(
		'title' => $row['title'],
		'url' => murl('entry', array('m' => 'we7_wmall_plus', 'do' => 'search', 'dis' => $row['key']), true, false)
	);
}

include $this->template('public/link');
