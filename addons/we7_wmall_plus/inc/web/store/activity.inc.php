<?php
/**
 * 外送系统
 * @author 新睿社区
 * @QQ 10373458 * @url http://www.010xr.com/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$_W['page']['title'] = '营销活动-' . $_W['we7_wmall_plus']['config']['title'];
mload()->model('store');
$store = store_check();
$sid = $store['id'];
$do = 'activity';
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'first_order';

$activity = pdo_get('tiny_wmall_plus_store_activity', array('uniacid' => $_W['uniacid'], 'sid' => $sid));
if(empty($activity)) {
	$insert = array(
		'uniacid' => $_W['uniacid'],
		'sid' => $sid
	);
	pdo_insert('tiny_wmall_plus_store_activity', $insert);
}
$activity = store_fetch_activity($sid);
if($op == 'first_order') {
	if(checksubmit()) {
		$first_order_status = intval($_GPC['first_order_status']);
		$first_order_data = array();
		if(!empty($_GPC['condition'])) {
			foreach ($_GPC['condition'] as $key => $value) {
				$condition = intval($value);
				$back = intval($_GPC['back'][$key]);
				if($condition && $back) {
					$first_order_data[$condition] = array(
						'condition' => $condition,
						'back' => $back,
					);
				}
			}
		}
		if($first_order_status == 1 && empty($first_order_data)) {
			message('没有设置有效的满减活动', '', 'error');
		}
		pdo_update('tiny_wmall_plus_store', array('first_order_status' => $first_order_status), array('uniacid' => $_W['uniacid'], 'id' => $sid));
		pdo_update('tiny_wmall_plus_store_activity', array('first_order_status' => $first_order_status, 'first_order_data' => iserializer($first_order_data)), array('uniacid' => $_W['uniacid'], 'sid' => $sid));
		message('设置新用户满减优惠成功', $this->createWebUrl('activity'), 'success');
	}

	if(empty($activity['first_order_data'])) {
		$activity['first_order_data'] = array(
			array(
				'condition' => '',
				'back' => '',
			)
		);
	}
}

if($op == 'discount') {
	if(checksubmit()) {
		$discount_status = intval($_GPC['discount_status']);
		$discount_data = array();
		if(!empty($_GPC['condition'])) {
			foreach ($_GPC['condition'] as $key => $value) {
				$condition = intval($value);
				$back = intval($_GPC['back'][$key]);
				if($condition && $back) {
					$discount_data[$condition] = array(
						'condition' => $condition,
						'back' => $back,
					);
				}
			}
		}
		if($discount_status == 1 && empty($discount_data)) {
			message('没有设置有效的满减活动', '', 'error');
		}
		pdo_update('tiny_wmall_plus_store', array('discount_status' => $discount_status), array('uniacid' => $_W['uniacid'], 'id' => $sid));
		pdo_update('tiny_wmall_plus_store_activity', array('discount_status' => $discount_status, 'discount_data' => iserializer($discount_data)), array('uniacid' => $_W['uniacid'], 'sid' => $sid));
		message('设置满减优惠成功', $this->createWebUrl('activity', array('op' => 'discount')), 'success');
	}

	if(empty($activity['discount_data'])) {
		$activity['discount_data'] = array(
			array(
				'condition' => '',
				'back' => '',
			)
		);
	}
}

if($op == 'grant') {
	if(checksubmit()) {
		$grant_status = intval($_GPC['grant_status']);
		$grant_data = array();
		if(!empty($_GPC['condition'])) {
			foreach ($_GPC['condition'] as $key => $value) {
				$condition = intval($value);
				$back = trim($_GPC['back'][$key]);
				if($condition && $back) {
					$grant_data[$condition] = array(
						'condition' => $condition,
						'back' => $back,
					);
				}
			}
		}
		if($grant_status == 1 && empty($grant_data)) {
			message('没有设置有效的满赠活动', '', 'error');
		}
		pdo_update('tiny_wmall_plus_store', array('grant_status' => $grant_status), array('uniacid' => $_W['uniacid'], 'id' => $sid));
		pdo_update('tiny_wmall_plus_store_activity', array('grant_status' => $grant_status, 'grant_data' => iserializer($grant_data)), array('uniacid' => $_W['uniacid'], 'sid' => $sid));
		message('设置满赠优惠成功', $this->createWebUrl('activity', array('op' => 'grant')), 'success');
	}
	foreach($activity['grant_data'] as &$row) {
		if(!is_array($row)) {
			continue;
		}
		$data[] = $row;
	}

	$activity['grant_data'] = $data;
	$count = count($activity['grant_data']);
	for($i = 0; $i < 4 - $count; $i++) {
		$activity['grant_data'][] = array(
			'condition' => '',
			'back' => '',
		);
	}
}

include $this->template('store/activity');