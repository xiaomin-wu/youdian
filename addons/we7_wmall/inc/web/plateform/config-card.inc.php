<?php
/**
 * 外送系统
 * @author 灯火阑珊
 * @QQ 2471240272
 * @url http://bbs.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$_W['page']['title'] = '配送会员卡-' . $_W['we7_wmall']['config']['title'];
$do = 'card';
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'card_set';

if($op == 'card_set') {
	$config = sys_delivery_config();
	if(checksubmit()) {
		$data = array(
			'uniacid' => $_W['uniacid'],
			'card_apply_status' => intval($_GPC['card_apply_status']),
			'card_agreement' => htmlspecialchars_decode($_GPC['card_agreement'])
		);
		if(!empty($config['id'])) {
			pdo_update('tiny_wmall_delivery_config', $data, array('uniacid' => $_W['uniacid']));
		} else {
			pdo_insert('tiny_wmall_delivery_config', $data);
		}
		message('设置配送会员卡参数成功', referer(), 'success');
	}
	include $this->template('plateform/config-card');
}

if($op == 'card_post') {
	$id = intval($_GPC['id']);
	if(checksubmit()) {
		$data = array(
			'uniacid' => $_W['uniacid'],
			'title' => trim($_GPC['title']),
			'days' => intval($_GPC['days']),
			'price' => intval($_GPC['price']),
			'day_free_limit' => intval($_GPC['day_free_limit']),
			'displayorder' => intval($_GPC['displayorder']),
		);
		if(empty($data['title'])) {
			message('套餐名称不能为空', '', 'info');
		}
		if(empty($data['days'])) {
			message('套餐有效期限不能为空', '', 'info');
		}
		if($id > 0) {
			pdo_update('tiny_wmall_delivery_cards', $data, array('uniacid' => $_W['uniacid'], 'id' => $id));
		} else {
			pdo_insert('tiny_wmall_delivery_cards', $data);
		}
		message('编辑套餐成功', $this->createWebUrl('ptfconfig-card', array('op' => 'card_list')), 'success');
	}
	if($id > 0) {
		$card = pdo_get('tiny_wmall_delivery_cards', array('uniacid' => $_W['uniacid'], 'id' => $id));
		if(empty($card)) {
			message('套餐不存在或已删除', referer(), 'error');
		}
	} else {
		$card = array(
			'day_free_limit' => 2
		);
	}
	include $this->template('plateform/config-card');
}

if($op == 'card_list') {
	if(checksubmit('submit')) {
		if(!empty($_GPC['ids'])) {
			foreach($_GPC['ids'] as $k => $v) {
				$title = trim($_GPC['title'][$k]);
				if(empty($title)) {
					continue;
				}
				$data = array(
					'title' => $title,
					'displayorder' => intval($_GPC['displayorder'][$k])
				);
				pdo_update('tiny_wmall_delivery_cards', $data, array('uniacid' => $_W['uniacid'], 'id' => intval($v)));
			}
			message('编辑成功', $this->createWebUrl('ptfconfig-card', array('op' => 'card_list')), 'success');
		}
	}
	$cards = pdo_fetchall('select * from ' . tablename('tiny_wmall_delivery_cards') . ' where uniacid = :uniacid order by displayorder desc, id asc', array(':uniacid' => $_W['uniacid']));
	include $this->template('plateform/config-card');
}

if($op == 'card_del') {
	$id = intval($_GPC['id']);
	pdo_delete('tiny_wmall_delivery_cards', array('uniacid' => $_W['uniacid'], 'id' => $id));
	message('删除套餐成功', $this->createWebUrl('ptfconfig-card', array('op' => 'card_list')), 'success');
}

if($op == 'card_status') {
	if($_W['isajax']) {
		$id = intval($_GPC['id']);
		$status = intval($_GPC['status']);
		pdo_update('tiny_wmall_delivery_cards', array('status' => $status), array('uniacid' => $_W['uniacid'], 'id' => $id));
		message(error(0, ''), '', 'ajax');
	}
}



