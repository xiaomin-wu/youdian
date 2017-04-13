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
mload()->model('smember');

$do = 'smember';
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'sync';

if($op == 'sync') {
	if($_W['isajax']) {
		$uid = intval($_GPC['__input']['uid']);
		$member = pdo_get('tiny_wmall_members', array('uid' => $uid));
		if(!empty($member)) {
			$data = array();
			if(strexists($member['avatar'], "/132132")) {
				$data['avatar'] = str_replace('/132132', '/132', $member['avatar']);
			}
			if($member['sex'] == '1' || $member['sex'] == '2') {
				$data['sex'] = ($member['sex'] == '1' ? '男' : '女');
			}
			pdo_update('tiny_wmall_members', $data, array('uid' => $uid));
		}
		$update = array();
		$update['success_num'] = intval(pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_order') . ' where uniacid = :uniacid and uid = :uid and is_pay = 1 and status = 5', array(':uniacid' => $_W['uniacid'], ':uid' => $uid)));
		$update['success_price'] = floatval(pdo_fetchcolumn('select sum(final_fee) from ' . tablename('tiny_wmall_order') . ' where uniacid = :uniacid and uid = :uid and is_pay = 1 and status = 5', array(':uniacid' => $_W['uniacid'], ':uid' => $uid)));
		$update['cancel_num'] = intval(pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_order') . ' where uniacid = :uniacid and uid = :uid and status = 6', array(':uniacid' => $_W['uniacid'], ':uid' => $uid)));
		$update['cancel_price'] = floatval(pdo_fetchcolumn('select sum(final_fee) from ' . tablename('tiny_wmall_order') . ' where uniacid = :uniacid and uid = :uid and status = 6', array(':uniacid' => $_W['uniacid'], ':uid' => $uid)));
		pdo_update('tiny_wmall_members', $update, array('uniacid' => $_W['uniacid'], 'uid' => $uid));
		message(error(0, ''), '', 'ajax');
	}
	$uids = pdo_getall('tiny_wmall_members', array('uniacid' => $_W['uniacid']), array('uid'), 'uid');
	$uids = array_keys($uids);
}

if($op == 'list') {
	$condition = ' where uniacid = :uniacid';
	$params = array(':uniacid' => $_W['uniacid']);
	$keyword = trim($_GPC['keyword']);
	if(!empty($keyword)) {
		$condition .= ' and (realname like :keyword or mobile like :keyword)';
		$params[':keyword'] = "%{$keyword}%";
	}
	$setmeal_status = isset($_GPC['setmeal_status']) ? intval($_GPC['setmeal_status']) : -1;
	if($setmeal_status > 0) {
		$setmeal_id = isset($_GPC['setmeal_id']) ? intval($_GPC['setmeal_id']) : -1;
		if($setmeal_id > 0) {
			$condition .= ' and setmeal_id = :setmeal_id';
			$params[':setmeal_id'] = $setmeal_id;
		} else {
			$condition .= ' and setmeal_id > 0';
		}
		$endtime = isset($_GPC['endtime']) ? intval($_GPC['endtime']) : -1;
		if($endtime >= 0) {
			$condition .= ' and setmeal_endtime <= :setmeal_endtime';
			$params[':setmeal_endtime'] = strtotime("{$endtime}days", strtotime(date('Y-m-d')));
		}
	} elseif($setmeal_status == 0) {
		$condition .= ' and setmeal_id = 0';
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

	$total = pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_members') . $condition, $params);
	$data = pdo_fetchall('select * from ' . tablename('tiny_wmall_members') . $condition . ' LIMIT '.($pindex - 1) * $psize . ',' . $psize, $params);
	$pager = pagination($total, $pindex, $psize);
	$stat = smember_amount_stat($sid, $id);
	$cards = pdo_fetchall('select * from ' . tablename('tiny_wmall_delivery_cards') . ' where uniacid = :uniacid order by displayorder desc, id asc', array(':uniacid' => $_W['uniacid']), 'id');
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
		$data = pdo_fetchall("SELECT * FROM " . tablename('tiny_wmall_members') . 'WHERE uniacid = :uniacid AND first_order_time >= :starttime and first_order_time <= :endtime', array(':uniacid' => $_W['uniacid'], ':starttime' => $start, 'endtime' => $end));
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
	$stat = smember_amount_stat();
}

if($op == 'card_order') {
	$condition = ' where uniacid = :uniacid';
	$params = array(':uniacid' => $_W['uniacid']);
	$keyword = trim($_GPC['keyword']);
	if(!empty($keyword)) {
		$condition .= ' and uid in (select uid from ' . tablename('tiny_wmall_members') . ' where uniacid = :uniacid and (realname like :keyword or mobile like :keyword))';
		$params[':keyword'] = "%{$keyword}%";
	}
	$uid = intval($_GPC['uid']);
	if($uid > 0) {
		$condition .= ' and uid = :uid';
		$params[':uid'] = $uid;
	}
	$setmeal_id = isset($_GPC['setmeal_id']) ? intval($_GPC['setmeal_id']) : -1;
	if($setmeal_id > 0) {
		$condition .= ' and card_id = :setmeal_id';
		$params[':setmeal_id'] = $setmeal_id;
	}
	$paytime = isset($_GPC['paytime']) ? intval($_GPC['paytime']) : -1;
	if($paytime > 0) {
		$condition .= ' and paytime >= :paytime';
		$params[':paytime'] = strtotime("-{$paytime}days", strtotime(date('Y-m-d')));
	}

	$pindex = max(1, intval($_GPC['page']));
	$psize = 40;
	$total = pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_delivery_cards_order') . $condition, $params);
	$orders = pdo_fetchall('select * from ' . tablename('tiny_wmall_delivery_cards_order') . $condition . ' ORDER BY id desc LIMIT '.($pindex - 1) * $psize . ',' . $psize, $params);
	if(!empty($orders)) {
		$uids = array();
		foreach($orders as $order) {
			$uids[] = $order['uid'];
		}
		$uids = implode(',', array_unique($uids));
		$users = pdo_fetchall('select id, uid, realname, avatar from ' . tablename('tiny_wmall_members') . " where uniacid = :uniacid and uid in ({$uids})", array(':uniacid' => $_W['uniacid']), 'uid');
	}
	$pager = pagination($total, $pindex, $psize);
	$cards = pdo_fetchall('select * from ' . tablename('tiny_wmall_delivery_cards') . ' where uniacid = :uniacid order by displayorder desc, id asc', array(':uniacid' => $_W['uniacid']), 'id');
	$pay_types = order_pay_types();
}
include $this->template('plateform/member');