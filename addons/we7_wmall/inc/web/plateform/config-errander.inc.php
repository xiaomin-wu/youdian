<?php
/**
 * 外送系统
 * @author 灯火阑珊
 * @QQ 2471240272
 * @url http://bbs.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$_W['page']['title'] = '跑腿设置';
$do = 'errander';
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'set';

if($op == 'set') {
	$config = sys_config();
	if(empty($config['id'])) {
		message('请先进行基本参数设置', $this->createWebUrl('ptfconfig'), 'info');
	} else {
		$config['errander']['map']['lat'] = $config['errander']['map']['location_x'];
		$config['errander']['map']['lng'] = $config['errander']['map']['location_y'];
	}
	if(checksubmit()) {
		$errander = array(
			'map' => array(
				'location_x' => trim($_GPC['map']['lat']),
				'location_y' => trim($_GPC['map']['lng']),
			),
			'city' => trim($_GPC['city']),
			'serve_radius' => intval($_GPC['serve_radius']),
			'pay_time_limit' => intval($_GPC['pay_time_limit']),
			'handel_time_limit' => intval($_GPC['handel_time_limit']),
			'dispatch_mode' => intval($_GPC['dispatch_mode']),
			'deliveryer_fee_type' => intval($_GPC['deliveryer_fee_type']),
			'agreement' => htmlspecialchars_decode($_GPC['agreement']),
		);
		$errander['deliveryer_fee'] = $errander['deliveryer_fee_type'] == 1 ? trim($_GPC['deliveryer_fee_1']) : trim($_GPC['deliveryer_fee_2']);

		$errander['anonymous'] = array();
		if(!empty($_GPC['anonymous'])) {
			foreach($_GPC['anonymous'] as $anonymous) {
				if(empty($anonymous)) continue;
				$errander['anonymous'][] = $anonymous;
			}
		}
		$data['anonymous'] = iserializer($data['anonymous']);
		pdo_update('tiny_wmall_config', array('errander' => iserializer($errander)), array('uniacid' => $_W['uniacid']));
		message('设置跑腿服务参数成功', 'refresh', 'success');
	}
	include $this->template('plateform/config-errander');
}

if($op == 'category_post') {
	$id = intval($_GPC['id']);
	if($id > 0) {
		$item = pdo_get('tiny_wmall_errander_category', array('uniacid' => $_W['uniacid'], 'id' => $id));
		if(empty($item)) {
			message('跑腿类型不存在或已删除', referer(), 'error');
		}
		$item['label'] = iunserializer($item['label']);
	}
	if(checksubmit()) {
		$title = trim($_GPC['title']) ? trim($_GPC['title']) : message('标题不能为空');
		$data = array(
			'uniacid' => $_W['uniacid'],
			'title' => $title,
			'type' => trim($_GPC['type']),
			'thumb' => trim($_GPC['thumb']),
			'start_fee' => trim($_GPC['start_fee']),
			'start_km' => trim($_GPC['start_km']),
			'pre_km_fee' => trim($_GPC['pre_km_fee']),
			'tip_min' => trim($_GPC['tip_min']) < 0 ? 0 : trim($_GPC['tip_min']),
			'tip_max' => trim($_GPC['tip_max']) < 0 ? 200 : trim($_GPC['tip_max']),
			'displayorder' => intval($_GPC['displayorder']),
			'rule' => htmlspecialchars_decode($_GPC['rule']),
		);
		if(!empty($_GPC['label'])) {
			$data['label'] = array();
			foreach($_GPC['label'] as $label) {
				if(empty($label)) continue;
				$data['label'][] = $label;
			}
			$data['label'] = iserializer($data['label']);
		}
		if(empty($item['id'])) {
			pdo_insert('tiny_wmall_errander_category', $data);
		} else {
			pdo_update('tiny_wmall_errander_category', $data, array('uniacid' => $_W['uniacid'], 'id' => $id));
		}
		message('编辑成功', $this->createWebUrl('ptfconfig-errander', array('op' => 'category_list')), 'success');
	}
	include $this->template('plateform/config-errander');
}

if($op == 'category_list') {
	if(checksubmit('submit')) {
		if(!empty($_GPC['ids'])) {
			foreach($_GPC['ids'] as $k => $v) {
				$data = array(
					'title' => trim($_GPC['title'][$k]),
					'displayorder' => intval($_GPC['displayorder'][$k])
				);
				pdo_update('tiny_wmall_errander_category', $data, array('uniacid' => $_W['uniacid'], 'id' => intval($v)));
			}
			message('编辑成功', $this->createWebUrl('ptfconfig-errander', array('op' => 'category_list')), 'success');
		}
	}

	$condition = ' where uniacid = :uniacid';
	$params = array(
		':uniacid' => $_W['uniacid']
	);
	$categorys = pdo_fetchall('SELECT * FROM ' . tablename('tiny_wmall_errander_category') . $condition . ' ORDER BY displayorder DESC,id ASC', $params);
	include $this->template('plateform/config-errander');
}

if($op == 'category_del') {
	$id = intval($_GPC['id']);
	pdo_delete('tiny_wmall_errander_category', array('uniacid' => $_W['uniacid'], 'id' => $id));
	message('删除跑腿分类成功', $this->createWebUrl('ptfconfig-errander', array('op' => 'category_list')), 'success');
}

if($op == 'category_status') {
	$id = intval($_GPC['id']);
	$status = intval($_GPC['status']);
	pdo_update('tiny_wmall_errander_category', array('status' => $status), array('uniacid' => $_W['uniacid'], 'id' => $id));
	exit();
}