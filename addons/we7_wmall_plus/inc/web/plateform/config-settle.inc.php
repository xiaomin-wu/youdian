<?php
/**
 * 外送系统
 * @author 新睿社区
 * @QQ 10373458 * @url http://www.010xr.com/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$_W['page']['title'] = '商家入驻设置';
$do = 'settle';
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'set';

if($op == 'set') {
	$config = sys_config();
	if(empty($config['id'])) {
		message('请先进行基本参数设置', $this->createWebUrl('ptfconfig'), 'info');
	}
	$config_settle = $config['settle'];
	$agreement_settle = get_config_text('agreement_settle');

	if(checksubmit()) {
		$settle = array(
			'status' => intval($_GPC['status']),
			'audit_status' => intval($_GPC['audit_status']),
			'mobile_verify_status' => intval($_GPC['mobile_verify_status']),
			'instore_serve_rate' => intval($_GPC['instore_serve_rate']),
			'takeout_serve_rate' => intval($_GPC['takeout_serve_rate']),
			'get_cash_fee_limit' => intval($_GPC['get_cash_fee_limit']),
			'get_cash_fee_rate' => trim($_GPC['get_cash_fee_rate']),
			'get_cash_fee_min' => intval($_GPC['get_cash_fee_min']),
			'get_cash_fee_max' => intval($_GPC['get_cash_fee_max']),
			'store_label_new' => intval($_GPC['store_label_new']),
		);
		set_config_text('agreement_settle', htmlspecialchars_decode($_GPC['agreement_settle']));
		pdo_update('tiny_wmall_plus_config', array('settle' => iserializer($settle)) , array('uniacid' => $_W['uniacid']));
		$sync = intval($_GPC['sync']);
		if($sync == 1) {
			$update = array(
				'instore_serve_rate' => $settle['instore_serve_rate'],
				'takeout_serve_rate' => $settle['takeout_serve_rate'],
				'fee_rate' => $settle['get_cash_fee_rate'],
				'fee_min' => $settle['get_cash_fee_min'],
				'fee_max' => $settle['get_cash_fee_max'],
			);
			pdo_update('tiny_wmall_plus_store_account', $update, array('uniacid' => $_W['uniacid']));
		}
		message('设置商户入驻参数成功', referer(), 'success');
	}
}
include $this->template('plateform/config-settle');