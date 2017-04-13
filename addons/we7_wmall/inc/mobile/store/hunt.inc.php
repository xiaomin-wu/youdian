<?php
/**
 * 外送系统
 * @author 灯火阑珊
 * @QQ 2471240272
 * @url http://bbs.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$do = 'hunt';
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'index';

if($op == 'index') {
	$title = "{$_W['we7_wmall']['config']['title']}-热门搜索";
	$stores = store_fetchall_by_condition('hot');
	if($_W['member']['uid'] > 0) {
		mload()->model('member');
		$member = member_fetch();
	}
	include $this->template('hunt-index');
}

if($op == 'truncate') {
	if($_W['member']['uid'] > 0) {
		pdo_update('tiny_wmall_members', array('search_data' => ''), array('uniacid' => $_W['uniacid'], 'uid' => $_W['member']['uid']));
	}
	exit('success');
}

if($op == 'search_data') {
	if($_W['member']['uid'] > 0) {
		mload()->model('member');
		$key = trim($_GPC['key']);
		$member = member_fetch();
		if(!empty($member)) {
			$num = count($member['search_data']);
			if($num >= 5) {
				array_pop($member['search_data']);
			}
			array_push($member['search_data'], $key);
			$search_data = iserializer(array_unique($member['search_data']));
			pdo_update('tiny_wmall_members', array('search_data' => $search_data), array('uniacid' => $_W['uniacid'], 'uid' => $_W['member']['uid']));
		}
	}
}

if($op == 'search') {
	$key = trim($_GPC['key']);
	$sids = array(0);
	$sids_str = 0;
	$stores = array();
	$goods = pdo_fetchall('select * from ' . tablename('tiny_wmall_goods') . ' where uniacid = :uniacid and status = 1 and title like :key', array(':uniacid' => $_W['uniacid'], ':key' => "%{$key}%"));
	if(!empty($goods)) {
		$store_goods = array();
		foreach($goods as $good) {
			$sids[] = $good['sid'];
			$store_goods[$good['sid']][] = $good;
		}
		$sids_str = implode(',', $sids);
		$stores = pdo_fetchall('select id,title,logo,business_hours,delivery_price,send_price,delivery_time,delivery_mode from ' . tablename('tiny_wmall_store') . " where uniacid = :uniacid and status = 1 and id in ({$sids_str})", array(':uniacid' => $_W['uniacid']), 'id');
	}
	$search_stores = pdo_fetchall('select id,title,logo,business_hours,delivery_price,send_price,delivery_time,delivery_mode,forward_mode from ' . tablename('tiny_wmall_store') . " where uniacid = :uniacid and status = 1 and id not in ({$sids_str}) and title like :key", array(':uniacid' => $_W['uniacid'], ':key' => "%{$key}%"));
	$stores = array_merge($search_stores, $stores);
	foreach($stores as &$row) {
		$row['goods'] = $store_goods[$row['id']];
		$row['activity'] = store_fetch_activity($row['id'], array('discount_status', 'discount_data'));
		$row['url'] = store_forward_url($row['id'], $row['forward_mode']);
	}
	$num = count($stores);
	if($num < 4) {
		$recommend_stores = store_fetchall_by_condition('recommend');
	}
	include $this->template('hunt-search');
}


