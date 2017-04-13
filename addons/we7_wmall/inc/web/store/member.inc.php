<?php
/**
 * 外送系统
 * @author 灯火阑珊
 * @QQ 2471240272
 * @url http://bbs.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$_W['page']['title'] = '顾客管理-' . $_W['we7_wmall']['config']['title'];
mload()->model('store');
mload()->model('member');

$store = store_check();
$sid = $store['id'];
$do = 'member';
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'sync';
if($op == 'sync') {
	if($_W['isajax']) {
		$uid = intval($_GPC['__input']['uid']);
		$update = array();
		$update['success_num'] = intval(pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_order') . ' where uniacid = :uniacid and sid = :sid and uid = :uid and is_pay = 1 and status = 5', array(':uniacid' => $_W['uniacid'], ':sid' => $sid, ':uid' => $uid)));
		$update['success_price'] = floatval(pdo_fetchcolumn('select sum(final_fee) from ' . tablename('tiny_wmall_order') . ' where uniacid = :uniacid and sid = :sid and uid = :uid and is_pay = 1 and status = 5', array(':uniacid' => $_W['uniacid'], ':sid' => $sid, ':uid' => $uid)));
		$update['cancel_num'] = intval(pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_order') . ' where uniacid = :uniacid and sid = :sid and uid = :uid and status = 6', array(':uniacid' => $_W['uniacid'], ':sid' => $sid, ':uid' => $uid)));
		$update['cancel_price'] = floatval(pdo_fetchcolumn('select sum(final_fee) from ' . tablename('tiny_wmall_order') . ' where uniacid = :uniacid and sid = :sid and uid = :uid and status = 6', array(':uniacid' => $_W['uniacid'], ':sid' => $sid, ':uid' => $uid)));
		pdo_update('tiny_wmall_store_members', $update, array('uniacid' => $_W['uniacid'], 'uid' => $uid));
		message(error(0, ''), '', 'ajax');
	}
	$uids = pdo_getall('tiny_wmall_store_members', array('uniacid' => $_W['uniacid'], 'sid' => $sid), array('uid'), 'uid');
	$uids = array_keys($uids);
}

if($op == 'list') {
	$condition = ' where uniacid = :uniacid and sid = :sid';
	$params = array(':uniacid' => $_W['uniacid'], ':sid' => $sid);
	$keyword = trim($_GPC['keyword']);
	if(!empty($keyword)) {
		$condition .= ' and uid in (select uid from ' . tablename('tiny_wmall_members') . ' where realname like :keyword or mobile like :keyword)';
		$params[':keyword'] = "%{$keyword}%";
	}
	$sort = trim($_GPC['sort']);
	$sort_val = intval($_GPC['sort_val']);
	if(!empty($sort)) {
		if($sort_val == 1) {
			$condition .= " ORDER BY {$sort} DESC";
		} else {
			$condition .= " ORDER BY {$sort} ASC";
		}
	}
	$pindex = max(1, intval($_GPC['page']));
	$psize = 40;

	$total = pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_store_members') . $condition, $params);
	$data = pdo_fetchall('select * from ' . tablename('tiny_wmall_store_members') . $condition . ' LIMIT '.($pindex - 1) * $psize . ',' . $psize, $params);
	if(!empty($data)) {
		$users = array();
		foreach($data as $da) {
			$users[] = $da['uid'];
		}
		$users = implode(',', $users);
		$users = pdo_fetchall('select * from ' . tablename('tiny_wmall_members') . " where uniacid = :uniacid and uid in ({$users})", array(':uniacid' => $_W['uniacid']), 'uid');
	}
	$pager = pagination($total, $pindex, $psize);
	$stat = member_amount_stat($sid);
}

if($op == 'stat') {
	$start = $_GPC['start'] ? strtotime($_GPC['start']) : strtotime(date('Y-m'));
	$end= $_GPC['end'] ? strtotime($_GPC['end']) + 86399 : (strtotime(date('Y-m-d')) + 86399);
	$day_num = ($end - $start) / 86400;
	//新增人数
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
		$data = pdo_fetchall("SELECT * FROM " . tablename('tiny_wmall_store_members') . 'WHERE uniacid = :uniacid AND sid = :sid AND first_order_time >= :starttime and first_order_time <= :endtime', array(':uniacid' => $_W['uniacid'], ':sid' => $sid, ':starttime' => $start, 'endtime' => $end));
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
	$stat = member_amount_stat($sid);
}

include $this->template('store/member');