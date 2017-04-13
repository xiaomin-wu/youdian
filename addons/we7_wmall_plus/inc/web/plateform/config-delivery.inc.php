<?php
/**
 * 外送系统
 * @author 新睿社区
 * @QQ 10373458 * @url http://www.010xr.com/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$_W['page']['title'] = '配送员设置';
$do = 'delivery';
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'set';

if($op == 'set') {
	$config = sys_config();
	if(empty($config['id'])) {
		message('请先进行基本参数设置', $this->createWebUrl('ptfconfig'), 'info');
	}
	$config_delivery = $config['delivery'];
	$agreement_delivery = get_config_text('agreement_delivery');

	if(checksubmit('submit')) {
		$delivery = array(
			'uniacid' => $_W['uniacid'],
			'get_cash_fee_limit' => intval($_GPC['get_cash_fee_limit']),
			'get_cash_fee_rate' => trim($_GPC['get_cash_fee_rate']),
			'get_cash_fee_min' => intval($_GPC['get_cash_fee_min']),
			'get_cash_fee_max' => intval($_GPC['get_cash_fee_max']),
			'mobile_verify_status' => intval($_GPC['mobile_verify_status']),
		);
		set_config_text('agreement_delivery', htmlspecialchars_decode($_GPC['agreement_delivery']));
		pdo_update('tiny_wmall_plus_config', array('delivery' => iserializer($delivery)) , array('uniacid' => $_W['uniacid']));
		message('配送员设置成功', referer(), 'success');
	}
}
include $this->template('plateform/config-delivery');