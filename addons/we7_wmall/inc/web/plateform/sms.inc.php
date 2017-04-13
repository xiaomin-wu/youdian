<?php
/**
 * 外送系统
 * @author 灯火阑珊
 * @QQ 2471240272
 * @url http://bbs.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$_W['page']['title'] = '短信平台-' . $_W['we7_wmall']['config']['title'];
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'set';
mload()->model('sms');
if($op == 'set') {
	if(checksubmit()) {
		$data = array(
			'status' => intval($_GPC['status']),
			'key' => trim($_GPC['key']),
			'secret' => trim($_GPC['secret']),
			'sign' => trim($_GPC['sign']),
			'verify_code_tpl' => trim($_GPC['verify_code_tpl']),
			'clerk' => array(
				'status' => intval($_GPC['clerk']['status']),
				'tts_code' => trim($_GPC['clerk']['tts_code']),
				'called_show_num' => trim($_GPC['clerk']['called_show_num']),
			),
			'deliveryer' => array(
				'status' => intval($_GPC['deliveryer']['status']),
				'tts_code' => trim($_GPC['deliveryer']['tts_code']),
				'called_show_num' => trim($_GPC['deliveryer']['called_show_num']),
			),
		);
		pdo_update('tiny_wmall_config', array('sms' => iserializer($data)), array('uniacid' => $_W['uniacid']));
		message('短信参数设置成功', $this->createWebUrl('ptfsms'), 'success');
	}
	$sms = $_W['we7_wmall']['config']['sms'];
}

if($op == 'record') {
	$lists = pdo_getall('tiny_wmall_store', array('uniacid' => $_W['uniacid']), array('id', 'logo', 'title', 'sms_use_times'));
}

if($op == 'detail') {
	$sid = intval($_GPC['id']);
	$store = pdo_get('tiny_wmall_store', array('uniacid' => $_W['uniacid'], 'id' => $sid));
	if(empty($store)) {
		message('门店不存在', referer(), 'error');
	}
	mload()->model('sms');
	$types = sms_types();
	$pindex = max(1, intval($_GPC['page']));
	$psize = 50;
	$condition = ' WHERE uniacid = :uniacid AND sid = :sid';
	$params = array(
		':uniacid' => $_W['uniacid'],
		':sid' => $sid,
	);
	$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('tiny_wmall_sms_send_log') . $condition, $params);
	$data = pdo_fetchall('SELECT * FROM ' . tablename('tiny_wmall_sms_send_log') . $condition . ' ORDER BY id DESC LIMIT '.($pindex - 1) * $psize.','.$psize, $params);
	$pager = pagination($total, $pindex, $psize);
}

include $this->template('plateform/sms');
