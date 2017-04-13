<?php
/**
 * 外送系统
 * @author 新睿社区
 * @QQ 10373458 * @url http://www.010xr.com/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'password';

if($op == 'password') {
	$old_password = trim($_GPC['oldpassword']);
	if(empty($old_password)) {
		message(ierror(-1, '原密码不能为空'), '', 'ajax');
	}
	$password = md5(md5($deliveryer['salt'] . $old_password) . $deliveryer['salt']);
	if($password != $deliveryer['password']) {
		message(ierror(-1, '原密码有误, 请重新输入'), '', 'ajax');
	}
	$new_password = trim($_GPC['password']);
	$length = strlen($new_password);
	if($length < 6) {
		message(ierror(-1, '密码长度不能小于6位'), '', 'ajax');
	}
	$password = md5(md5($deliveryer['salt'] . $new_password) . $deliveryer['salt']);
	pdo_update('tiny_wmall_plus_deliveryer', array('password' => $password), array('uniacid' => $_W['uniacid'], 'id' => $deliveryer['id']));
	message(ierror(0, '修改密码成功'), '', 'ajax');
}

if($op == 'update') {
	$client = trim($_GPC['client']) ? trim($_GPC['client']) : 'android';
	$config_app = $_W['we7_wmall_plus']['config']['app']['deliveryer'];
	$update = array(
		'version' => $config_app['version'][$client],
		'downloadUrl' => MODULE_URL . "/resource/apps/{$_W['uniacid']}/{$client}/deliveryman_1.0.apk"
	);
	message(ierror(0, '', $update), '', 'ajax');
}

if($op == 'work_status') {
	$status = intval($_GPC['work_status']);
	$tips = array(
		'0' => '休息中',
		'1' => '接单中',
	);
	if(!in_array($status, array_keys($tips))) {
		message(ierror(-1, '工作状态有误'), '', 'ajax');
	}
	pdo_update('tiny_wmall_plus_deliveryer', array('work_status' => $status), array('uniacid' => $_W['uniacid'], 'id' => $deliveryer['id']));
	message(ierror(0, '', array('work_status_cn' => $tips[$status])), '', 'ajax');
}

if($op == 'location') {
	$location_x = floatval($_GPC['location_x']);
	$location_y = floatval($_GPC['location_y']);
	if(empty($location_x) || empty($location_y)) {
		message(ierror(-1, '地理位置不完善'), '', 'ajax');
	}
	pdo_update('tiny_wmall_plus_deliveryer', array('location_x' => $location_x, 'location_y' => $location_y), array('uniacid' => $_W['uniacid'], 'id' => $deliveryer['id']));
	$data = array(
		'uniacid' => $_W['uniacid'],
		'deliveryer_id' => $deliveryer['id'],
		'location_x' => $location_x,
		'location_y' => $location_y,
		'addtime' => TIMESTAMP
	);
	pdo_insert('tiny_wmall_plus_deliveryer_location_log', $data);
	message(ierror(0, ''), '', 'ajax');
}




