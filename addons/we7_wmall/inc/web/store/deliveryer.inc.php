<?php
/**
 * 外送系统
 * @author 灯火阑珊
 * @QQ 2471240272
 * @url http://bbs.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$_W['page']['title'] = '配送员管理-' . $_W['we7_wmall']['config']['title'];
mload()->model('store');
mload()->model('deliveryer');

$store = store_check();
if($store['account']['delivery_type'] == 2) {
	message('当前门店的配送模式为平台配送员, 您无法进行该操作', referer(), 'error');
}
$sid = $store['id'];
$do = 'deliveryer';
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'list';

if($op == 'add') {
	if($_W['isajax']) {
		$mobile = trim($_GPC['mobile']);
		if(empty($mobile)) {
			message(error(-1, '手机号不能为空'), '', 'ajax');
		}
		$deliveryer = pdo_get('tiny_wmall_deliveryer', array('uniacid' => $_W['uniacid'], 'mobile' => $mobile));
		if(empty($deliveryer)) {
			message(error(-1, '未找到该手机号对应的配送员'), '', 'ajax');
		}
		$is_exist = pdo_get('tiny_wmall_store_deliveryer', array('uniacid' => $_W['uniacid'], 'deliveryer_id' => $deliveryer['id'], 'sid' => $sid));
		if(!empty($is_exist)) {
			message(error(-1, '该手机号对用的配送员已经是平台配送员, 请勿重复添加'), '', 'ajax');
		}
		$data = array(
			'uniacid' => $_W['uniacid'],
			'sid' => $sid,
			'deliveryer_id' => $deliveryer['id'],
			'delivery_type' => 1,
			'addtime' => TIMESTAMP,
		);
		pdo_insert('tiny_wmall_store_deliveryer', $data);
		message(error(0, '添加店内配送员成功'), '', 'ajax');
	}
}

if($op == 'list') {
	$data = pdo_fetchall('SELECT * FROM ' . tablename('tiny_wmall_store_deliveryer') . ' WHERE uniacid = :uniacid AND sid = :sid', array(':uniacid' => $_W['uniacid'], ':sid' => $sid));
	if(!empty($data)) {
		$deliveryers = pdo_getall('tiny_wmall_deliveryer', array('uniacid' => $_W['uniacid']), array(), 'id');
		foreach($data as &$da) {
			$da['deliveryer'] = $deliveryers[$da['deliveryer_id']];
			$da['stat'] = deliveryer_order_stat($sid, $da['deliveryer_id']);
		}
	}
}

if($op == 'del') {
	$id = intval($_GPC['id']);
	pdo_delete('tiny_wmall_store_deliveryer', array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'id' => $id));
	message('删除配送员配送权限成功', referer(), 'success');
}

if($op == 'stat') {
	$id = intval($_GPC['id']);
	$deliveryer = deliveryer_fetch($id);
	if(empty($deliveryer)) {
		message('配送员不存在', referer(), 'error');
	}

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
		$data = pdo_fetchall("SELECT * FROM " . tablename('tiny_wmall_order') . 'WHERE uniacid = :uniacid AND sid = :sid AND deliveryer_id = :deliveryer_id and status = 5', array(':uniacid' => $_W['uniacid'], ':sid' => $sid, ':deliveryer_id' => $id));
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
	$stat = deliveryer_order_stat($sid, $id);
}
include $this->template('store/deliveryer');