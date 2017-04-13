<?php
/**
 * 外送系统
 * @author 新睿社区
 * @QQ 10373458 * @url http://www.010xr.com/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'submit';

if($op == 'submit') {
	$mobile = trim($_GPC['mobile']);
	if(empty($mobile)) {
		message(ierror(-1, '手机号不能为空'), '', 'ajax');
	}
	$reg = '/^1[34578][0-9]{9}/';
	if(!preg_match($reg, $mobile)) {
		message(ierror(-1, '手机号格式有误'), '', 'ajax');
	}

	$code = trim($_GPC['code']);
	if(empty($code)) {
		message(ierror(-1, '验证码不能为空'), '', 'ajax');
	}
	$auth = check_verifycode($mobile, $code);
	if(empty($auth)) {
		message(ierror(-1, '验证码错误'), '', 'ajax');
	}

	$member = pdo_get('tiny_wmall_plus_members', array('uniacid' => $_W['uniacid'], 'mobile' => $mobile));
	if(empty($member)) {
		$member = array(
			'uniacid' => $_W['uniacid'],
			'uid' => 55 . random(7, true),
			'mobile' => $mobile,
			'token' => random(32),
			'addtime' => TIMESTAMP,
			'openid' => '',
			'nickname' => '',
			'realname' => '',
			'sex' => '',
			'avatar' => '',
			'is_sys' => 3, //app登陆注册的用户
		);
		pdo_insert('tiny_wmall_plus_members', $member);
	}
	message(ierror(0, '登陆成功', $member), '', 'ajax');
}


