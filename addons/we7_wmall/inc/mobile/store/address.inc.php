<?php
/**
 * 外送系统
 * @author 灯火阑珊
 * @QQ 2471240272
 * @url http://bbs.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
mload()->model('member');
$do = 'address';
$title = '我的收货地址';
$this->checkAuth();
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'list';
$sid = intval($_GPC['sid']);
$init_hide = 0;
$store['serve_radius'] = 0;
$store['auto_get_address'] = 1;
if($sid > 0) {
	$store = store_fetch($sid);
	if(!$store['not_in_serve_radius'] && !empty($store['location_x']) && !empty($store['location_y']) && $store['serve_radius'] > 0 && $store['auto_get_address'] == 1) {
		$init_hide = 1;
	}
}
$redirect_type = trim($_GPC['redirect_type']);
$redirect_input = trim($_GPC['redirect_input']);
$routes = array(
	'order' => $this->createMobileUrl('submit', array('sid' => $_GPC['sid'], 'r' => 1, 'op' => 'index', 'recordid' => $_GPC['recordid'])) . "&address_id=" ,
	'errander' => $this->createMobileUrl('errander-index', array('op' => 'submit', 'id' => $_GPC['errander_id'])) . "&{$redirect_input}="
);
$redirect_url = $routes[$redirect_type];
if($op == 'list') {
	$addresses = member_fetchall_address();
	if($init_hide == 1) {
		$available = array();
		$dis_available = array();
		foreach($addresses as $li) {
			if(!empty($li['location_x']) && !empty($li['location_y'])) {
				$dist = distanceBetween($li['location_y'], $li['location_x'], $store['location_y'], $store['location_x']);
				if($dist > ($store['serve_radius'] * 1000)) {
					$dis_available[] = $li;
				} else {
					$available[] = $li;
				}
			} else {
				$dis_available[] = $li;
			}
		}
	}
}

if($op == 'post') {
	$id = intval($_GPC['id']);
	if($id > 0) {
		$address = member_fetch_address($id);
		if(empty($address)) {
			message('地址不存在或已经删除', referer(), 'error');
		}
	} else {
		$address = array(
			'mobile' => $_W['member']['mobile'],
			'realname' => $_W['member']['realname'],
		);
	}
	if($_GPC['d'] == 1) {
		$address['location_x'] = trim($_GPC['lat']);
		$address['location_y'] = trim($_GPC['lng']);
		$address['address'] = trim($_GPC['address']);
	}
	if($_W['ispost']) {
		if(empty($_GPC['realname']) || empty($_GPC['mobile'])) {
			message(error(-1, '信息有误'), '', 'ajax');
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
			'type' => 1
		);
		if($redirect_type == 'errander') {
			$config = $_W['we7_wmall']['config']['errander'];
			$distance = distanceBetween($data['location_y'], $data['location_x'], $config['map']['location_y'], $config['map']['location_x']);
			if($distance > ($config['serve_radius'] * 1000)) {
				message(error(-1, '该地址不在跑腿服务范围内'), '', 'ajax');
			}
		}
		if(!empty($address['id'])) {
			pdo_update('tiny_wmall_address', $data, array('uniacid' => $_W['uniacid'], 'id' => $id));
		} else {
			pdo_insert('tiny_wmall_address', $data);
			$id = pdo_insertid();
		}
		message(error(0, $id), '', 'ajax');
	}
}

if($op == 'del') {
	if(!$_W['isajax']) {
		exit();
	}
	$id = intval($_GPC['id']);
	pdo_delete('tiny_wmall_address', array('uniacid' => $_W['uniacid'], 'id' => $id));
	message(error(0, ''), '', 'ajax');
}

if($op == 'default') {
	$id = intval($_GPC['id']);
	pdo_update('tiny_wmall_address', array('is_default' => 0), array('uniacid' => $_W['uniacid'], 'uid' => $_W['member']['uid'], 'type' => 1));
	pdo_update('tiny_wmall_address', array('is_default' => 1), array('uniacid' => $_W['uniacid'], 'id' => $id));
	exit();
}

if($op == 'serve_address') {
	$data = array(
		'uniacid' => $_W['uniacid'],
		'uid' => $_W['member']['uid'],
		'name' => trim($_GPC['address']),
		'address' => trim($_GPC['name']),
		'location_x' => trim($_GPC['location_x']),
		'location_y' => trim($_GPC['location_y']),
		'number' => trim($_GPC['number']),
		'type' => 2,
	);
	if(empty($data['name']) || empty($data['location_x'])) {
		message(error(-1, '地址信息不完善'), '', 'ajax');
	}
	pdo_insert('tiny_wmall_address', $data);
	$id = pdo_insertid();
	message(error(0, $id), '', 'ajax');
}
include $this->template('address');


