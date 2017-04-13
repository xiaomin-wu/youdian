<?php
/**
 * 外送系统
 * @author 新睿社区
 * @QQ 10373458 * @url http://www.010xr.com/
 */
defined('IN_IA') or exit('Access Denied');
load()->func('file');
global $_W, $_GPC;
$_W['page']['title'] = 'app设置';
$do = 'app';
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'set';

mkdirs(MODULE_ROOT . "/resource/apps/{$_W['uniacid']}/ios");
mkdirs(MODULE_ROOT . "/resource/apps/{$_W['uniacid']}/android");
$downurls = array(
	'deliveryer' => array(
		'ios' => MODULE_URL . "/resource/apps/{$_W['uniacid']}/ios/deliveryman_1.0.apk",
		'android' => MODULE_URL . "/resource/apps/{$_W['uniacid']}/android/deliveryman_1.0.apk",
	),
);
if($op == 'set') {
	$config = sys_config();
	if(empty($config['id'])) {
		message('请先进行基本参数设置', $this->createWebUrl('ptfconfig'), 'info');
	}
	$app = $config['app'];
	if(checksubmit()) {
		$data = array(
			'deliveryer' => array(
				'serial_sn' => trim($_GPC['deliveryer']['serial_sn']),
				'push_key' => trim($_GPC['deliveryer']['push_key']),
				'push_secret' => trim($_GPC['deliveryer']['push_secret']),
				'ios_build_type' => intval($_GPC['deliveryer']['ios_build_type']),
				'version' => array(
					'ios' => trim($_GPC['deliveryer']['version']['ios']),
					'android' => trim($_GPC['deliveryer']['version']['android']),
				),
			)
		);
		pdo_update('tiny_wmall_plus_config', array('app' => iserializer($data)), array('uniacid' => $_W['uniacid']));
		message('设置app参数成功', 'refresh', 'success');
	}
}
include $this->template('plateform/config-app');

