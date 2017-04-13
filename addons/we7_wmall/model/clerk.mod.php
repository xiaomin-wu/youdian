<?php
/**
 * [WeEngine System] Copyright (c) 2013 WE7.CC
 * $sn$
 */
defined('IN_IA') or exit('Access Denied');

//get_clerks
function clerk_fetchall($sid) {
	global $_W;
	$data = pdo_fetchall("SELECT * FROM " . tablename('tiny_wmall_clerk') . ' WHERE uniacid = :uniacid AND sid = :sid', array(':uniacid' => $_W['uniacid'], ':sid' => $sid));
	return $data;
}

//get_clerk
function clerk_fetch($id) {
	global $_W;
	$data = pdo_fetch("SELECT * FROM " . tablename('tiny_wmall_clerk') . ' WHERE uniacid = :uniacid AND id = :id', array(':uniacid' => $_W['uniacid'], ':id' => $id));
	return $data;
}

//check_clerk
function clerk_check($sid = 0) {
	global $_W;
	$data = array();
	if(!empty($_W['openid'])) {
		$where = array('uniacid' => $_W['uniacid'], 'openid' => $_W['openid']);
		if($sid > 0) {
			$where['sid'] = $sid;
		}
		$data = pdo_get('tiny_wmall_clerk', $where);
	}
	if(empty($data)) {
		message('您没有管理店铺的权限', '', 'error');
	}
	return false;
}

function clerk_create($user) {
	global $_W;
	if(empty($user['username'])) {
		return error(-1, '用户名不能为空');
	}
	if(empty($user['password'])) {
		return error(-1, '密码不能为空');
	}
	$account = pdo_get('tiny_wmall_clerk', array('uniacid' => $_W['uniacid'], 'openid' => $_W['openid'], 'role' => 'manager'));
	if(!empty($account)) {
		return error(-1, '不能重复申请门店');
	}
	$is_exist = pdo_get('tiny_wmall_clerk', array('uniacid' => $_W['uniacid'], 'title' => $user['username']));
	if(!empty($is_exist)) {
		return error(-1, '用户名已存在');
	}
	$data = array(
		'uniacid' => $_W['uniacid'],
		'openid' => $_W['openid'],
		'nickname' => $_W['fans']['nickname'],
		'title' => $user['username'],
		'mobile' => $user['mobile'],
		'role' => $user['role'] ? $user['role'] : 'clerk',
		'salt' => random(6),
		'status' => 1,
		'addtime' => TIMESTAMP
	);
	$data['password'] = md5(md5($data['salt'] . $user['password']) . $data['salt']);
	pdo_insert('tiny_wmall_clerk', $data);
	return pdo_insertid();
}


