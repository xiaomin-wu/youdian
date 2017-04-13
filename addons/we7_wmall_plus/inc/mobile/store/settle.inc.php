<?php
/**
 * 外送系统
 * @author 新睿社区
 * @QQ 10373458 * @url http://www.010xr.com/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
mload()->model('member');
$do = 'settle';
$title = '申请入驻';
$this->checkAuth();
$_W['fans'] = mc_oauth_userinfo();

$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'account';
$config_settle = $_W['we7_wmall_plus']['config']['settle'];

if($config_settle['status'] != 1) {
	message('暂时不支持商户入驻', referer(), 'errro');
}
$account = pdo_get('tiny_wmall_plus_clerk', array('uniacid' => $_W['uniacid'], 'openid' => $_W['openid'], 'role' => 'manager'));
if($op == 'account') {
	if(!empty($account)) {
		header('location:' . $this->createMobileUrl('settle', array('op' => 'store')));
		die;
	}
	if($_W['isajax']) {
		$user = array(
			'username' => trim($_GPC['username']),
			'password' => trim($_GPC['password']),
			'mobile' => trim($_GPC['mobile']),
			'role' => 'manager',
		);
		if($config_settle['mobile_verify_status'] == 1) {
			$code = trim($_GPC['code']);
			$status = check_verifycode($user['mobile'], $code);
			if(!$status) {
				message(error(-1, '验证码错误'), '', 'ajax');
			}
		}
		mload()->model('clerk');
		$result = clerk_create($user);
		if(is_error($result)) {
			message($result, '', 'ajax');
		} else {
			message(error(0, ''), '', 'ajax');
		}
	}
}

if($op == 'store') {
	if(empty($account)) {
		header('location:' . $this->createMobileUrl('settle', array('op' => 'account')));
		die;
	}
	$store = pdo_get('tiny_wmall_plus_store', array('uniacid' => $_W['uniacid'], 'id' => $account['sid']));
	if(!empty($store)) {
		$mine_url = $this->createMobileUrl('mine');
		$admin_url = $_W['siteroot'].'addons/we7_wmall_plus/admin';
		if($store['status'] <= 1) {
			$this->imessage('商户入驻申请成功', $this->createMobileUrl('mgindex'), 'success', "请拷贝以下链接从电脑登录商户后台管理系统<br>{$admin_url}<br><h3>or</h3>", '去商家手机端');
		} else {
			$this->imessage('商户入驻申请正在审核中！', $mine_url, 'info', '请耐心等待');
		}
	}
	if($_W['isajax']) {
		$title = trim($_GPC['title']) ? trim($_GPC['title']) : message(error(-1, '商户名称不能为空'), '', 'ajax');
		$config_takeout = $_W['we7_wmall_plus']['config']['takeout'];
		$data = array(
			'uniacid' => $_W['uniacid'],
			'title' => $title,
			'address' => trim($_GPC['address']),
			'telephone' => trim($_GPC['telephone']),
			'content' => trim($_GPC['content']),
			'status' => $config_settle['audit_status'],
			'business_hours' => iserializer(array('start' => '8:00', 'end' => '24:00')),
			'remind_time_limit' => 10,
			'remind_reply' => iserializer(array('快递员狂奔在路上,请耐心等待')),
			'addtype' => 2,
			'addtime' => TIMESTAMP,
			'delivery_mode' => $config_takeout['delivery_mode'],
			'delivery_fee_mode' => 1,
			'delivery_price' => $config_takeout['delivery_fee'],
		);
		if($config_takeout['delivery_fee_mode'] == 2) {
			$data['delivery_fee_mode'] = 2;
			$data['delivery_price'] = iserializer($data['delivery_price']);
		}
		$delivery_times = get_config_text('takeout_delivery_time');
		$data['delivery_times'] = iserializer($delivery_times);
		pdo_insert('tiny_wmall_plus_store', $data);
		$store_id = pdo_insertid();

		$store_account = array(
			'uniacid' => $_W['uniacid'],
			'sid' => $store_id,
			'instore_serve_rate' => intval($config_settle['instore_serve_rate']),
			'takeout_serve_rate' => intval($config_settle['takeout_serve_rate']),
			'fee_limit' => $config_settle['get_cash_fee_limit'],
			'fee_rate' => $config_settle['get_cash_fee_rate'],
			'fee_min' => $config_settle['get_cash_fee_min'],
			'fee_max' => $config_settle['get_cash_fee_max'],
		);
		pdo_insert('tiny_wmall_plus_store_account', $store_account);
		pdo_update('tiny_wmall_plus_clerk', array('sid' => $store_id), array('uniacid' => $_W['uniacid'], 'id' => $account['id']));
		sys_notice_settle($store_id, 'clerk', '');
		sys_notice_settle($store_id, 'manager', '');
		message(error(0, ''), '', 'ajax');
	}
}

include $this->template('settle');