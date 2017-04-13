<?php
/**
 * 外送系统
 * @author 灯火阑珊
 * @QQ 2471240272
 * @url http://bbs.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$do = 'dymine';
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'index';
$title = '我的配送';
$deliveryer = $_W['we7_wmall']['deliveryer']['user'];
$deliveryer_type = $_W['we7_wmall']['deliveryer']['type'];
if($deliveryer_type != 2) {
	$pft_stat = deliveryer_plateform_order_stat($deliveryer['id']);
}

if($deliveryer_type != 1) {
	$sids = $_W['we7_wmall']['deliveryer']['store'];
	$sids_str = implode(',', $sids);
	$stores = pdo_fetchall('select id,title,logo from ' . tablename('tiny_wmall_store') . " where uniacid = :uniacid and id in ({$sids_str})", array(':uniacid' => $_W['uniacid']));
	$stat = pdo_fetchall('select count(*) as num, sid from ' . tablename('tiny_wmall_order') . " where uniacid = :uniacid and deliveryer_id = :deliveryer_id and delivery_type = 1 and status =5 and sid in ({$sids_str}) group by sid", array(':uniacid' => $_W['uniacid'], ':deliveryer_id' => $deliveryer['id']), 'sid');
}


include $this->template('delivery/mine');