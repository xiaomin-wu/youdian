<?php
/**
 * 外送系统
 * @author 灯火阑珊
 * @QQ 2471240272
 * @url http://bbs.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$_W['page']['title'] = '店员管理-' . $_W['we7_wmall']['config']['title'];
mload()->model('store');
mload()->model('clerk');

$store = store_check();
$sid = $store['id'];
$do = 'clerk';
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'list';

if($op == 'post') {
	$id = intval($_GPC['id']);
	$clerk = clerk_fetch($id);

	if($_W['ispost']) {
		$insert['uniacid'] = $_W['uniacid'];
		$insert['sid'] = $sid;
		$insert['title'] = trim($_GPC['title']) ? trim($_GPC['title']) : exit('登陆账号不能为空');

		$is_exist = pdo_get('tiny_wmall_clerk', array('uniacid' => $_W['uniacid'], 'title' => $insert['title']));
		if(!empty($is_exist) && $is_exist['id'] != $id) {
			exit('用户名已存在');
		}
		if(!$id) {
			$insert['password'] = trim($_GPC['password']) ? trim($_GPC['password']) : exit('登陆账号不能为空');
			$insert['salt'] = random(6);
			$insert['password'] = md5(md5($insert['salt'].$insert['password']) . $insert['salt']);
		} else {
			$password = trim($_GPC['password']);
			if(!empty($password)) {
				$insert['salt'] = random(6);
				$insert['password'] = md5(md5($insert['salt'].$password) . $insert['salt']);
			}
		}
		$role = trim($_GPC['role']);
		if($role == 'manager' || $clerk['role'] == 'manager') {
			$manager = pdo_get('tiny_wmall_clerk', array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'role' => 'manager'));
			if(empty($manager)) {
				$insert['role'] = 'manager';
			}
		} else {
			$insert['role'] = 'clerk';
		}
		$insert['mobile'] = trim($_GPC['mobile']);
		$insert['nickname'] = trim($_GPC['nickname']);
		$insert['openid'] = trim($_GPC['openid']);
		if(empty($insert['openid'])) {
			exit('粉丝openid和店员邮箱必须填写一项');
		}
		if($id > 0) {
			pdo_update('tiny_wmall_clerk', $insert, array('uniacid' => $_W['uniacid'], 'id' => $id));
		} else {
			$insert['addtime'] = TIMESTAMP;
			pdo_insert('tiny_wmall_clerk', $insert);
		}
		exit('success');
	}
	include $this->template('store/clerk');
}

if($op == 'fetch_openid') {
	$acid = $_W['acid'];
	$nickname = trim($_GPC['nickname']);
	$openid = trim($_GPC['openid']);
	if(!empty($openid)) {
		$data = pdo_fetch('SELECT openid,nickname FROM ' . tablename('mc_mapping_fans') . ' WHERE uniacid = :uniacid AND acid = :acid AND openid = :openid ', array(':uniacid' => $_W['uniacid'], ':acid' => $acid, ':openid' => $openid));
	}
	if(empty($data)) {
		if(!empty($nickname)) {
			$data = pdo_fetch('SELECT openid,nickname FROM ' . tablename('mc_mapping_fans') . ' WHERE uniacid = :uniacid AND acid = :acid AND nickname = :nickname ', array(':uniacid' => $_W['uniacid'], ':acid' => $acid, ':nickname' => $nickname));
			if(empty($data)) {
				exit('error');
			} else {
				exit(json_encode($data));
			}
		} else {
			exit('error');
		}
	} else {
		exit(json_encode($data));
	}
}

if($op == 'list') {
	$data = pdo_fetchall('SELECT * FROM ' . tablename('tiny_wmall_clerk') . ' WHERE uniacid = :aid AND sid = :id', array(':aid' => $_W['uniacid'], ':id' => $sid));
	include $this->template('store/clerk');
}

if($op == 'del') {
	$id = intval($_GPC['id']);
	$clerk = clerk_fetch($id);
	if($clerk['role'] != 'manager') {
		pdo_delete('tiny_wmall_clerk', array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'id' => $id));
	}
	message('删除店员成功', referer(), 'success');
}
