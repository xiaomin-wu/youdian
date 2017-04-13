<?php
/**
 * 外送系统
 * @author 新睿社区
 * @QQ 10373458 * @url http://www.010xr.com/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$title = '商户入驻';
$do = 'pftsettle';
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'list';

if($op == 'list') {
	$condition = ' where uniacid = :uniacid and addtype = 2';
	$params[':uniacid'] = $_W['uniacid'];
	$status = isset($_GPC['status']) ? intval($_GPC['status']) : -1;
	if($status > 0) {
		$condition .= " AND status = :status";
		$params[':status'] = $status;
	}
	$pindex = max(1, intval($_GPC['page']));
	$psize = 20;
	$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('tiny_wmall_plus_store') . $condition, $params);
	$lists = pdo_fetchall('SELECT * FROM ' . tablename('tiny_wmall_plus_store') . $condition . ' ORDER BY id DESC LIMIT '.($pindex - 1) * $psize.','.$psize, $params);
	if(!empty($lists)) {
		foreach($lists as &$li) {
			$li['user'] = pdo_get('tiny_wmall_plus_clerk', array('sid' => $li['id'], 'role' => 'manager'));
		}
	}
	$store_status = store_status();
	$pager = pagination($total, $pindex, $psize);
	include $this->template('plateform/settle-list');
}

if($op == 'audit') {
	$id = intval($_GPC['id']);
	$store = pdo_get('tiny_wmall_plus_store', array('uniacid' => $_W['uniacid'], 'id' => $id));
	if(empty($store)) {
		message(error(-1, '门店不存在或已删除'), '', 'ajax');
	}
	$clerk = pdo_get('tiny_wmall_plus_clerk', array('sid' => $id, 'role' => 'manager'));
	if(empty($clerk)) {
		message(error(-1, '获取门店申请人失败'), '', 'ajax');
	}
	$status = intval($_GPC['status']);
	pdo_update('tiny_wmall_plus_store', array('status' => $status), array('uniacid' => $_W['uniacid'], 'id' => $id));
	$remark = trim($_GPC['remark']);
	sys_notice_settle($store['id'], 'clerk', $remark);
	message(error(0, ''), '', 'ajax');
}


