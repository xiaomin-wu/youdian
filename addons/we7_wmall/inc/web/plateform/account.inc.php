<?php
/**
 * 外送系统
 * @author 灯火阑珊
 * @QQ 2471240272
 * @url http://bbs.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$title = '设置门店独立账号';
$do = 'pftaccount';
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'list';

if($op == 'list') {
	$stores = pdo_getall('tiny_wmall_store', array('uniacid' => $_W['uniacid']), array('id', 'title', 'logo'), 'id');
	$list = pdo_fetchall('select * from ' . tablename('tiny_wmall_account') . ' where uniacid = :uniacid', array(':uniacid' => $_W['uniacid']));
}

if($op == 'del') {
	$id = intval($_GPC['id']);
	pdo_delete('tiny_wmall_account', array('uniacid' => $_W['uniacid'], 'id' => $id));
	message('删除成功', referer(), 'success');
}

if($op == 'status') {
	$id = intval($_GPC['id']);
	$status = intval($_GPC['status']);
	$account = pdo_get('tiny_wmall_account', array('uniacid' => $_W['uniacid'], 'id' => $id));
	if(!empty($account)) {
		pdo_update('tiny_wmall_account', array('status' => $status), array('uniacid' => $_W['uniacid'], 'id' => $id));
		pdo_update('users', array('status' => $status), array('uniacid' => $_W['uniacid'], 'uid' => $account['uid']));
	}
	message(error(0, ''), '', 'ajax');
}

if($op == 'post') {
	$id = intval($_GPC['id']);
	if($id > 0){
		$sql = 'SELECT * FROM ' . tablename('tiny_wmall_account') . " WHERE id = :id AND uniacid = :uniacid";
		$account = pdo_fetch($sql, array(':id' => $id, ':uniacid' => $_W['uniacid']));
		if(empty($account)) {
			message('账号不存在或已删除', referer(), 'error');
		}
		if($account['uid'] > 0) {
			$user = pdo_get('users', array('uid' => $account['uid']));
			$account['username'] = $user['username'];
			$account['uid'] = $user['uid'];
			$account['salt'] = $user['salt'];
		}
	}
	if(checksubmit()) {
		load()->model('user');
		if(!$account['uid']) {
			$user = array();
			$user['username'] = trim($_GPC['username']);
			if(!preg_match(REGULAR_USERNAME, $user['username'])) {
				message('必须输入用户名，格式为 3-15 位字符，可以包括汉字、字母（不区分大小写）、数字、下划线和句点。');
			}
			if(user_check(array('username' => $user['username']))) {
				message('非常抱歉，此用户名已经被注册，你需要更换注册名称！');
			}
			$user['password'] = trim($_GPC['password']);
			if(istrlen($user['password']) < 6) {
				message('必须输入密码，且密码长度不得低6位。');
			}
			$account['uid'] = user_register($user);
			if(!$account['uid']) {
				message('注册账号失败');
			}
		} else {
			$_GPC['username'] = trim($_GPC['username']);
			if(!preg_match(REGULAR_USERNAME, $_GPC['username'])) {
				message('必须输入用户名，格式为 3-15 位字符，可以包括汉字、字母（不区分大小写）、数字、下划线和句点。');
			}
			$is_exist = pdo_fetchcolumn('SELECT uid FROM ' . tablename('users') . ' WHERE username = :username AND uid != :uid', array(':username' => $_GPC['username'], ':uid' => $account['uid']));
			if(!empty($is_exist)) {
				message('非常抱歉，此用户名已经被注册，你需要更换注册名称！');
			}
			$_GPC['password'] = trim($_GPC['password']);
			if(!empty($_GPC['password']) && istrlen($_GPC['password']) < 6) {
				message('必须输入密码，且密码长度不得低于6位。');
			}
			$record = array();
			$record['uid'] = $account['uid'];
			$record['salt'] = $account['salt'];
			$record['username'] = $_GPC['username'];
			if(!empty($_GPC['password'])) {
				$record['password'] = user_hash($_GPC['password'], $account['salt']);
			}
			pdo_update('users', $record, array('uid' => intval($account['uid'])));
		}

		$permission = 'we7_wmall_menu_store';
		$permission_exist = pdo_get('users_permission', array('uniacid' => $_W['uniacid'], 'uid' => $account['uid'], 'type' => 'we7_wmall'));
		if(empty($permission_exist)) {
			pdo_insert('users_permission', array('uniacid' => $_W['uniacid'], 'uid' => $account['uid'], 'type' => 'we7_wmall', 'permission' => $permission));
		} else {
			pdo_update('users_permission', array('permission' => $permission), array('uniacid' => $_W['uniacid'], 'uid' => $account['uid'], 'type' => 'we7_wmall'));
		}

		$account_user = pdo_get('uni_account_users', array('uniacid' => $_W['uniacid'], 'uid' => $account['uid']));
		if(empty($account_user)) {
			pdo_insert('uni_account_users', array('uniacid' => $_W['uniacid'], 'uid' => $account['uid'], 'role' => 'operator'));
		} else {
			pdo_update('uni_account_users', array('role' => 'operator'), array('uniacid' => $_W['uniacid'], 'uid' => $account['uid']));
		}

		$data = array(
			'uniacid' => $_W['uniacid'],
			'uid' => $account['uid'],
			'username' => trim($_GPC['username']),
			'lastvisit' => TIMESTAMP,
			'lastip' => CLIENT_IP,
			'status' => 2,
			'store_id' => intval($_GPC['store_id']),
		);
		if(empty($account['id'])) {
			$data['joindate'] = TIMESTAMP;
			$data['joinip'] = CLIENT_IP;
			pdo_insert('tiny_wmall_account', $data);
		} else {
			pdo_update('tiny_wmall_account', $data, array('uniacid' => $_W['uniacid'], 'id' => $id));
		}
		message('编辑账号成功', $this->createWebUrl('ptfaccount'), 'success');
	}
	$stores = pdo_getall('tiny_wmall_store', array('uniacid' => $_W['uniacid']), array('id', 'title'));
}

include $this->template('plateform/account');