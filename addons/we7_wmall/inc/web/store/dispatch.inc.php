<?php
/**
 * 外送系统
 * @author 灯火阑珊
 * @QQ 2471240272
 * @url http://bbs.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$_W['page']['title'] = '配货中心-' . $_W['we7_wmall']['config']['title'];
mload()->model('store');
$store = store_check();
$sid = $store['id'];
$do = 'dispatch';
$op = trim($_GPC['op']) ?  trim($_GPC['op']) : 'goods';

if($op == 'goods' || $op == 'category') {
	$orders = pdo_fetchall('SELECT id, username, mobile, addtime FROM ' . tablename('tiny_wmall_order') . ' WHERE uniacid = :uniacid AND sid = :sid AND status = 2 ORDER BY id ASC', array(':sid' => $sid, ':uniacid' => $_W['uniacid']), 'id');
	if(!empty($orders)) {
		$str = implode(',', array_keys($orders));
		$stat = pdo_fetchall('SELECT *,SUM(goods_num) AS num, SUM(goods_price) AS price FROM ' . tablename('tiny_wmall_order_stat') . " WHERE uniacid = :uniacid AND sid = :sid AND status = 0 AND oid IN ({$str}) GROUP BY goods_id", array(':uniacid' => $_W['uniacid'], ':sid' => $sid));
		if(!empty($stat)) {
			$goods = array();
			foreach($stat as &$sta) {
				$tmp = pdo_fetchall('SELECT a.id,a.goods_num,a.goods_id,a.oid,a.addtime,b.username FROM ' . tablename('tiny_wmall_order_stat') . " AS a LEFT JOIN ". tablename('tiny_wmall_order')." AS b ON a.oid = b.id WHERE a.uniacid = :uniacid AND a.goods_id = :goods_id  AND a.status = 0 AND a.oid IN ($str) ORDER BY a.id ASC", array(':uniacid' => $_W['uniacid'], ':goods_id' => $sta['goods_id']));
				$arr_detail = array();
				foreach($tmp as $tm) {
					$arr_detail[] = array('username' => $tm['username'], 'id' => $tm['id'], 'oid' => $tm['oid'], 'goods_num' => $sta['goods_num']);
				}
				$goods[$sta['goods_id']] = $arr_detail;
			}
		}
		$order_stat = pdo_fetchall('SELECT * FROM ' . tablename('tiny_wmall_order_stat') . " WHERE uniacid = {$_W['uniacid']} AND oid IN ({$str})");
		$order_goods = array();
		foreach($order_stat as $k => $v) {
			$order_goods[$v['oid']][] = $v;
		}
		//按照商品分类统计
		$categorys = pdo_fetchall('SELECT id, title FROM ' . tablename('tiny_wmall_goods_category') . ' WHERE uniacid = :uniacid AND sid = :sid and status = 1', array(':uniacid' => $_W['uniacid'], ':sid' => $sid));
		$cate_stat = array();
		foreach($stat as $key => $stai) {
			$cate_stat[$stai['goods_cid']][] = $stai;
		}
	}
	if($op == 'goods') {
		include $this->template('store/dispatch-goods');
	} else {
		include $this->template('store/dispatch-category');
	}
	die;
}

if($op == 'order_status') {
	$id = intval($_GPC['id']);
	pdo_update('tiny_wmall_order', array('status' => 3), array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'id' => $id));
	pdo_update('tiny_wmall_order_stat', array('status' => 1), array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'oid' => $id));
	order_insert_status_log($id, $sid, 'delivery_wait');
	order_status_notice($sid, $id, 'delivery_wait');
	order_deliveryer_notice($sid, $id, 'delivery_wait');
	message(error(0, ''), '', 'ajax');
}

if($op == 'goods_status') {
	$id = intval($_GPC['id']);
	$status = intval($_GPC['status']);
	pdo_update('tiny_wmall_order_stat', array('status' => $status), array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'id' => $id));
	$stat = pdo_get('tiny_wmall_order_stat', array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'id' => $id));
	if(!empty($stat)) {
		$others = pdo_get('tiny_wmall_order_stat', array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'oid' => $stat['oid'], 'status' => 0));
		if(empty($others)) {
			pdo_update('tiny_wmall_order', array('status' => 3), array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'id' => $stat['oid']));
			order_insert_status_log($stat['oid'], $sid, 'delivery_wait');
			order_status_notice($sid, $stat['oid'], 'delivery_wait');
			order_deliveryer_notice($sid, $stat['oid'], 'delivery_wait');
		}
	}
	message(error(0, ''), '', 'ajax');
}



