<?php
/**
 * 外送系统
 * @author 灯火阑珊
 * @QQ 2471240272
 * @url http://bbs.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$title = '商户入驻';
$do = 'pftsettle';
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'set';

$settle = sys_settle_config();
if($op == 'set') {
	if(checksubmit()) {
		$data = array(
			'uniacid' => $_W['uniacid'],
			'status' => intval($_GPC['status']),
			'audit_status' => intval($_GPC['audit_status']),
			'mobile_verify_status' => intval($_GPC['mobile_verify_status']),
			'get_cash_fee_limit' => intval($_GPC['get_cash_fee_limit']),
			'get_cash_fee_rate' => trim($_GPC['get_cash_fee_rate']),
			'get_cash_fee_min' => intval($_GPC['get_cash_fee_min']),
			'get_cash_fee_max' => intval($_GPC['get_cash_fee_max']),
			'agreement' => htmlspecialchars_decode($_GPC['agreement'])
		);
		if(!empty($settle['id'])) {
			pdo_update('tiny_wmall_store_settle_config', $data, array('uniacid' => $_W['uniacid']));
		} else {
			pdo_insert('tiny_wmall_store_settle_config', $data);
		}
		$sync = intval($_GPC['sync']);
		if($sync == 1) {
			pdo_update('tiny_wmall_store_account', array('fee_limit' => $data['get_cash_fee_limit'], 'fee_rate' => $data['get_cash_fee_rate'], 'fee_min' => $data['get_cash_fee_min'], 'fee_max' => $data['get_cash_fee_max']), array('uniacid' => $_W['uniacid']));
		}
		message('设置商户入驻参数成功', referer(), 'success');
	}
	include $this->template('plateform/settle-set');
}

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
	$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('tiny_wmall_store') . $condition, $params);
	$lists = pdo_fetchall('SELECT * FROM ' . tablename('tiny_wmall_store') . $condition . ' ORDER BY id DESC LIMIT '.($pindex - 1) * $psize.','.$psize, $params);
	if(!empty($lists)) {
		foreach($lists as &$li) {
			$li['user'] = pdo_get('tiny_wmall_clerk', array('sid' => $li['id'], 'role' => 'manager'));
		}
	}
	$store_status = store_status();
	$pager = pagination($total, $pindex, $psize);
	include $this->template('plateform/settle-list');
}

if($op == 'audit') {
	$id = intval($_GPC['id']);
	$store = pdo_get('tiny_wmall_store', array('uniacid' => $_W['uniacid'], 'id' => $id));
	if(empty($store)) {
		message(error(-1, '门店不存在或已删除'), '', 'ajax');
	}
	$clerk = pdo_get('tiny_wmall_clerk', array('sid' => $id, 'role' => 'manager'));
	if(empty($clerk)) {
		message(error(-1, '获取门店申请人失败'), '', 'ajax');
	}
	$status = intval($_GPC['status']);
	pdo_update('tiny_wmall_store', array('status' => $status), array('uniacid' => $_W['uniacid'], 'id' => $id));
	$remark = trim($_GPC['remark']);
	sys_notice_settle($store['id'], 'clerk', $remark);
	message(error(0, ''), '', 'ajax');
}


