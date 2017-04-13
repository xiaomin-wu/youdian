<?php
/**
 * 外送系统
 * @author 新睿社区
 * @QQ 10373458 * @url http://www.010xr.com/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
mload()->model('member');
$do = 'address';
$title = '我的收货地址';
$this->icheckAuth();
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'list';

if($op == 'list') {
	$addresses = member_fetchall_address();
	message(ierror(0, '', $addresses), '', 'ajax');
}

if($op == 'detail') {
	$id = intval($_GPC['id']);
	$address = member_fetch_address($id);
	if(empty($address)) {
		message(ierror(-1, '地址不存在或已删除'), '', 'ajax');
	}
	message(ierror(0, '', $address), '', 'ajax');
}

if($op == 'post') {
	$id = intval($_GPC['id']);
	if(empty($_GPC['realname'])) {
		message(ierror(-1, '联系人不能为空'), '', 'ajax');
	}
	if(empty($_GPC['mobile'])) {
		message(ierror(-1, '手机号不能为空'), '', 'ajax');
	}
	if(empty($_GPC['location_x']) || empty($_GPC['location_y'])) {
		message(ierror(-1, '经纬度不能为空'), '', 'ajax');
	}
	$data = array(
		'uniacid' => $_W['uniacid'],
		'uid' => $_W['member']['uid'],
		'realname' => trim($_GPC['realname']),
		'sex' => trim($_GPC['sex']),
		'mobile' => trim($_GPC['mobile']),
		'address' => trim($_GPC['address']),
		'number' => trim($_GPC['number']),
		'location_x' => trim($_GPC['location_x']),
		'location_y' => trim($_GPC['location_y']),
	);
	if(!empty($id)) {
		pdo_update('tiny_wmall_plus_address', $data, array('uniacid' => $_W['uniacid'], 'id' => $id));
	} else {
		pdo_insert('tiny_wmall_plus_address', $data);
		$id = pdo_insertid();
	}
	$address = member_fetch_address($id);
	message(ierror(0, '编辑收货地址成功', $address), '', 'ajax');
}

if($op == 'del') {
	$id = intval($_GPC['id']);
	pdo_delete('tiny_wmall_plus_address', array('uniacid' => $_W['uniacid'], 'id' => $id));
	message(ierror(0, '删除收货地址成功'), '', 'ajax');
}

if($op == 'default') {
	$id = intval($_GPC['id']);
	pdo_update('tiny_wmall_plus_address', array('is_default' => 0), array('uniacid' => $_W['uniacid'], 'uid' => $_W['member']['uid']));
	pdo_update('tiny_wmall_plus_address', array('is_default' => 1), array('uniacid' => $_W['uniacid'], 'id' => $id));
	message(ierror(0, '设置默认收货地址成功'), '', 'ajax');
}

