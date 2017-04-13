<?php
/**
 * 外送系统
 * @author 新睿社区
 * @QQ 10373458 * @url http://www.010xr.com/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$_W['page']['title'] = '天天特价-' . $_W['we7_wmall_plus']['config']['title'];
mload()->model('bargain');
$store = store_check();
$sid = $store['id'];
$do = 'bargain';
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'list';
bargain_update_status();

if($op == 'post') {
	$id = intval($_GPC['id']);
	if(checksubmit('submit')) {
		$title = !empty($_GPC['title']) ? trim($_GPC['title']) : message('活动主题不能为空');
		$goods = array();
		if(!empty($_GPC['goods_id'])) {
			foreach($_GPC['goods_id'] as $key => $goods_id) {
				$temp = pdo_get('tiny_wmall_plus_goods', array('uniacid' => $_W['uniacid'], 'id' => $goods_id), array('id', 'price'));
				if(empty($temp)) {
					continue;
				}
				$row = array(
					'goods_id' => $goods_id,
					'discount_price' => floatval($_GPC['discount_price'][$key]),
					'max_buy_limit' => intval($_GPC['max_buy_limit'][$key]),
					'poi_user_type' => trim($_GPC['poi_user_type'][$key]) == 'all' ? 'all' : 'new',
					'discount_total' => intval($_GPC['discount_total'][$key]),
					'discount_available_total' => intval($_GPC['discount_available_total'][$key]),
				);
				$goods[$goods_id] = $row;
			}
		}
		if(empty($goods)) {
			message('请选择参与活动的商品');
		}

		$data = array(
			'uniacid' => $_W['uniacid'],
			'sid' => $store['id'],
			'title' => $title,
			'content' => trim($_GPC['content']),
			'order_limit' => intval($_GPC['order_limit']),
			'goods_limit' => intval($_GPC['goods_limit']),
			'starttime' => strtotime($_GPC['time']['start']),
			'endtime' => strtotime($_GPC['time']['end']),
			'starthour' => str_replace(':', '', trim($_GPC['starthour'])),
			'endhour' => str_replace(':', '', trim($_GPC['endhour'])),
			'use_limit' => intval($_GPC['use_limit']),
			'addtime' => TIMESTAMP,
			'total_updatetime' => strtotime(date('Y-m-d')) + 86400,
		);
		if($id > 0) {
			pdo_update('tiny_wmall_plus_activity_bargain', $data,  array('uniacid' => $_W['uniacid'], 'sid' => $store['id'], 'id' => $id));
		} else {
			pdo_insert('tiny_wmall_plus_activity_bargain', $data);
			$id = pdo_insertid();
		}
		foreach($goods as $row) {
			$data = array(
				'uniacid' => $_W['uniacid'],
				'bargain_id' => $id,
				'sid' => $store['id'],
				'goods_id' => $row['goods_id'],
				'discount_price' => $row['discount_price'],
				'max_buy_limit' => $row['max_buy_limit'],
				'poi_user_type' => $row['poi_user_type'],
				'discount_total' => $row['discount_total'],
				'discount_available_total' => $row['discount_available_total'],
			);
			$is_exist = pdo_get('tiny_wmall_plus_activity_bargain_goods', array('bargain_id' => $id, 'goods_id' => $row['goods_id']));
			if(empty($is_exist)) {
				pdo_insert('tiny_wmall_plus_activity_bargain_goods', $data);
			} else {
				pdo_update('tiny_wmall_plus_activity_bargain_goods', $data,  array('uniacid' => $_W['uniacid'], 'bargain_id' => $id, 'goods_id' => $row['goods_id']));
			}
		}
		$goods_ids = implode(',', array_keys($goods));
		pdo_query('delete from ' . tablename('tiny_wmall_plus_activity_bargain_goods') . " where uniacid = :uniacid and sid = :sid and bargain_id = :bargain_id and goods_id not in ({$goods_ids})", array(':uniacid' => $_W['uniacid'], ':sid' => $store['id'], ':bargain_id' => $id));
		message('编辑特价活动成功', $this->createWebUrl('bargain', array('op' => 'list')), 'success');
	}
	if($id > 0) {
		$bargain = pdo_get('tiny_wmall_plus_activity_bargain', array('uniacid' => $_W['uniacid'], 'sid' => $store['id'], 'id' => $id));
		if(empty($bargain)) {
			message('特价活动不存在或已删除', referer(), 'error');
		}
		if(strlen($bargain['starthour']) < 4) {
			$bargain['starthour'] = "0{$bargain['starthour']}";
		}
		if(strlen($bargain['endhour']) < 4) {
			$bargain['endhour'] = "0{$bargain['endhour']}";
		}
		$bargain['starthour'] = trim(chunk_split($bargain['starthour'], 2, ':'), ':');
		$bargain['endhour'] = trim(chunk_split($bargain['endhour'], 2, ':'), ':');
		$row = pdo_fetchall('select a.*,b.title,b.price,b.thumb from ' . tablename('tiny_wmall_plus_activity_bargain_goods') . ' as a left join ' . tablename('tiny_wmall_plus_goods') . ' as b on a.goods_id = b.id where bargain_id = :bargain_id order by a.displayorder desc', array(':bargain_id' => $bargain['id']), 'goods_id');
		$bargain['goods'] = $row;

	}
	if(empty($bargain)) {
		$bargain = array(
			'starttime' => TIMESTAMP,
			'endtime' => TIMESTAMP + 86400 * 15,
			'starthour' => '08:00',
			'endhour' => '12:00',
			'goods_limit' => 1,
			'order_limit' => 1,
			'goods' => array()
		);
	}
}

if($op == 'list') {
	$pindex = max(1, intval($_GPC['page']));
	$psize = 20;
	$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('tiny_wmall_plus_activity_bargain') . ' where uniacid = :uniacid and  sid = :sid', array(':uniacid' => $_W['uniacid'], ':sid' => $store['id']));
	$bargains = pdo_fetchall('select * from' . tablename('tiny_wmall_plus_activity_bargain') . ' where uniacid = :uniacid and sid = :sid order by id desc limit ' . ($pindex - 1) * $psize . ',' . $psize, array(':uniacid' => $_W['uniacid'], ':sid' => $store['id']),'id');
	$pager = pagination($total, $pindex, $psize);
	if(!empty($bargains)) {
		$bargain_status = bargain_status();
		foreach($bargains as &$row) {
			if(strlen($row['starthour']) < 4) {
				$row['starthour'] = "0{$row['starthour']}";
			}
			if(strlen($row['endhour']) < 4) {
				$row['endhour'] = "0{$row['endhour']}";
			}
			$row['starthour'] = trim(chunk_split($row['starthour'], 2, ':'), ':');
			$row['endhour'] = trim(chunk_split($row['endhour'], 2, ':'), ':');
		}
	}
}

if($op == 'del') {
	$id = intval($_GPC['id']);
	pdo_delete('tiny_wmall_plus_activity_bargain', array('uniacid' => $_W['uniacid'], 'id' => $id ,'sid' => $sid));
	pdo_delete('tiny_wmall_plus_activity_bargain_goods', array('uniacid' => $_W['uniacid'], 'bargain_id' => $id ,'sid' => $sid));
	message('删除活动成功', referer(), 'success');
}

include $this->template('store/bargain');