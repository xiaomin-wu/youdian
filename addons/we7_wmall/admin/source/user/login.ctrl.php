<?php
/**
 * [WeEngine System] Copyright (c) 2013 WE7.CC
 */
defined('IN_IA') or exit('Access Denied');
if(checksubmit()) {
	_login($_GPC['referer']);
}
$setting = $_W['setting'];
template('user/login');

function _login($forward = '') {
	global $_GPC, $_W;
	load()->model('user');
	$verify = trim($_GPC['verify']);
	if(empty($verify)) {
		message('请输入验证码');
	}
	$result = checkcaptcha($verify);
	if (empty($result)) {
		message('输入验证码错误');
	}
	$username = trim($_GPC['username']);
	if(empty($username)) {
		message('请输入要登录的用户名');
	}
	$password = trim($_GPC['password']);
	if(empty($password)) {
		message('请输入密码');
	}
	$record = array();
	$temp = pdo_get('tiny_wmall_clerk', array('title' => $username));
	if(!empty($temp)) {
		$password = md5(md5($temp['salt'] . $password) . $temp['salt']);
		if($password == $temp['password']) {
			$record = $temp;
		}
	}
	if(!empty($record)) {
		if($record['status'] == 2) {
			message('您的账号正在审核或是已经被系统禁止，请联系网站管理员解决！');
		}
		if($record['role'] == 'clerk') {
			message('您的申请是店员身份,没有权限管理店铺！');
		}
		if (!empty($_W['siteclose'])) {
			message('站点已关闭，关闭原因：' . $_W['setting']['copyright']['reason']);
		}
		$cookie = array();
		$cookie['clerk_id'] = $record['id'];
		$cookie['hash'] = $password;
		$session = base64_encode(json_encode($cookie));
		isetcookie('__we7_wmall_session', $session, 7 * 86400);
		if(empty($forward)) {
			$forward = $_GPC['forward'];
		}
		if(empty($forward)) {
			$forward = url('site/entry/store', array('m' => 'we7_wmall'));
		}
		message("欢迎回来，{$record['title']}。", $forward);
	} else {
		message('登录失败，请检查您输入的用户名和密码！');
	}
}

