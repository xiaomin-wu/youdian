<?php
/**
 * 外送系统
 * @author 灯火阑珊
 * @QQ 2471240272
 * @url http://bbs.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
mload()->model('table');
$do = 'goods';
$this->checkAuth();
$sid = intval($_GPC['sid']);
$store = store_fetch($sid);
if(empty($store)) {
	message('门店不存在或已经删除', referer(), 'error');
}
$title = '微信排号';
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'index';

if(!$store['is_assign']) {
	$this->imessage('商家已经关闭排号功能', $this->createMobileUrl('store', array('sid' => $sid)), 'info');
}

if($op == 'index') {
	if($_GPC['f'] == 'dish') {
		$cart = set_order_cart($sid);
	}
	$mine = pdo_get('tiny_wmall_assign_board', array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'uid' => $_W['member']['uid'], 'status' => 1));
	if(!empty($mine)) {
		header('Location:' . $this->createMobileUrl('assign', array('op' => 'mine', 'sid' => $sid)));
		die;
	}
	$data = pdo_fetchall('select * from ' . tablename('tiny_wmall_assign_queue') . ' where uniacid = :uniacid and sid = :sid and status = 1 order by guest_num asc', array(':uniacid' => $_W['uniacid'], ':sid' => $sid));
	include $this->template('assign');
}

if($op == 'post') {
	$data = pdo_fetchall('select * from ' . tablename('tiny_wmall_assign_queue') . ' where uniacid = :uniacid and sid = :sid and status = 1 order by guest_num asc', array(':uniacid' => $_W['uniacid'], ':sid' => $sid), 'id');
	$queue_ids = array();
	if(!empty($data)) {
		foreach($data as $key => &$row) {
			if(strtotime($row['starttime']) > TIMESTAMP || strtotime($row['endtime']) < TIMESTAMP) {
				unset($data[$key]);
			}
		}
		$queue_ids = array_keys($data);
	}

	if($_W['isajax']) {
		$guest_num = intval($_GPC['guest_num']) ? intval($_GPC['guest_num']) : message(error(-1, '客人数量错误'), '', 'ajax');
		$mobile = trim($_GPC['mobile']) ? trim($_GPC['mobile']) : message(error(-1, '手机号错误'), '', 'ajax');
		if($store['assign_mode'] == 2) {
			$queue_id = intval($_GPC['queue_id']) ? intval($_GPC['queue_id']) : message(error(-1, '队列错误'), '', 'ajax');
		} else {
			foreach($data as $da) {
				if($da['guest_num'] < $guest_num) {
					continue;
				} else {
					if(!$queue_id) {
						$queue_id = $da['id'];
					}
				}
			}
		}
		if(!in_array($queue_id, $queue_ids)) {
			message(error(-1, '不合法的队列'), '', 'ajax');
		}
		$queue = $data[$queue_id];
		$today = strtotime(date('Y-m-d'));
		if($queue['updatetime'] < $today) {
			pdo_update('tiny_wmall_assign_queue', array('position' => 1, 'updatetime' => TIMESTAMP), array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'id' => $queue_id));
			$queue['position'] = 1;
		}
		$data = array(
			'uniacid' => $_W['uniacid'],
			'sid' => $sid,
			'uid' => $_W['member']['uid'],
			'queue_id' => $queue_id,
			'openid' => $_W['openid'],
			'mobile' => $mobile,
			'guest_num' => $guest_num,
			'position' => $queue['position'],
			'number' => $queue['prefix'] . str_pad($queue['position'], 3, '0', STR_PAD_LEFT),
			'status' => 1,
			'is_notify' => 0,
			'createtime' => TIMESTAMP,
		);
		pdo_insert('tiny_wmall_assign_board', $data);
		$board_id = pdo_insertid();
		pdo_update('tiny_wmall_assign_queue', array('position' => $queue['position'] + 1, 'updatetime' => TIMESTAMP), array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'id' => $queue_id));
		assign_notice($sid, $board_id, 1);
		assign_notice_clerk($sid, $board_id);
		message(error(0, 'success'), 'ajax');
	}
	include $this->template('assign');
}

if($op == 'mine') {
	$mine = pdo_get('tiny_wmall_assign_board', array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'uid' => $_W['member']['uid'], 'status' => 1));
	if(empty($mine)) {
		header('Location:' . $this->createMobileUrl('assign', array('op' => 'index', 'sid' => $sid)));
		die;
	}
	$data = pdo_fetchall('select * from ' . tablename('tiny_wmall_assign_queue') . ' where uniacid = :uniacid and sid = :sid and status = 1 order by guest_num asc', array(':uniacid' => $_W['uniacid'], ':sid' => $sid), 'id');
	if(!empty($data)) {
		$wait = pdo_fetchall('select count(*) as num, queue_id from ' . tablename('tiny_wmall_assign_board') . ' where uniacid = :uniacid and sid = :sid and status = 1 group by queue_id', array(':uniacid' => $_W['uniacid'], ':sid' => $sid), 'queue_id');
	}

	$queue = $data[$mine['queue_id']];

	$now_number = pdo_fetchcolumn('select number from ' . tablename('tiny_wmall_assign_board') . ' where uniacid = :uniacid and sid = :sid and status = 1 and queue_id = :queue_id and  id < :id order by id desc', array(':uniacid' => $_W['uniacid'], ':sid' => $sid, ':queue_id' => $mine['queue_id'], ':id' => $mine['id']));
	if(empty($now_number)) {
		$now_number = $mine['number'];
	}
	$count = pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_assign_board') . ' where uniacid = :uniacid and sid = :sid and status = 1 and id < :id and queue_id = :queue_id', array(':uniacid' => $_W['uniacid'], ':sid' => $sid, ':queue_id' => $mine['queue_id'], ':id' => $mine['id']));
	include $this->template('assign');
}

if($op == 'cancel') {
	if($_W['isajax']) {
		$id = intval($_GPC['id']);
		$board = assign_board_fetch($id);
		if(empty($board)) {
			exit('排队不存在');
		}
		pdo_update('tiny_wmall_assign_board', array('status' => 4), array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'id' => $id));
		assign_notice($sid, $id, 4);
		assign_notice_queue($board['id'], $board['queue_id']);
		exit('success');
	}
}

if($op == 'goods') {
	$title = '商品列表';
	$activity = store_fetch_activity($sid);
	$is_favorite = pdo_get('tiny_wmall_store_favorite', array('uniacid' => $_W['uniacid'], 'uid' => $_W['member']['uid'], 'sid' => $sid));
	$categorys = store_fetchall_goods_category($sid, 1);
	$dish = pdo_fetchall('SELECT * FROM ' . tablename('tiny_wmall_goods') . ' WHERE uniacid = :aid AND sid = :sid AND status = 1 ORDER BY displayorder DESC, id ASC', array(':aid' => $_W['uniacid'], ':sid' => $sid));
	$cate_dish = array();
	foreach($dish as &$di) {
		if($di['is_options']) {
			$di['options'] = pdo_getall('tiny_wmall_goods_options', array('uniacid' => $_W['uniacid'], 'goods_id' => $di['id']));
		}
		$cate_dish[$di['cid']][] = $di;
	}
	//获取优惠券
	mload()->model('coupon');
	$tokens = coupon_fetchall_user_available($sid, $_W['member']['uid']);
	if(!empty($tokens)) {
		$token_nums = count($tokens);
		$token = $tokens[0];
	}

	$cart = order_fetch_member_cart($sid);
	include $this->template('assign-goods');
}

if($op == 'goods_post') {
	$cart = order_insert_member_cart($sid);
	header('location:' . $this->createMobileUrl('assign', array('sid' => $sid, 'op' => 'mine')));
	die;
}


