<?php
/**
 * 外送系统
 * @author 灯火阑珊
 * @QQ 2471240272
 * @url http://bbs.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
mload()->model('store');
$do = 'mine';
$this->checkAuth();
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'index';
if($_W['fans']['follow'] != 1) {
	$_W['fans'] = mc_oauth_userinfo();
}

$user = $_W['member'];
$user['nickname'] = $_W['fans']['nickname'];
$user['avatar'] = $_W['fans']['avatar'] ? $_W['fans']['avatar'] : $_W['fans']['tag']['avatar'];
$title = "{$_W['we7_wmall']['config']['title']}-会员中心";

if($op == 'index') {
	$settle_config = sys_settle_config();
	$delivery_config = sys_delivery_config();
	$favorite = intval(pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_store_favorite') . ' where uniacid = :uniacid and uid = :uid', array(':uniacid' => $_W['uniacid'], ':uid' => $_W['member']['uid'])));
	$coupon_condition = ' on a.couponid = b.id where a.uniacid = :uniacid and a.uid = :uid and a.status = 1 and b.endtime >= :time';
	$coupon_params = array(':uniacid' => $_W['uniacid'], ':uid' => $_W['member']['uid'], ':time' => TIMESTAMP);
	$coupon_nums = intval(pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_activity_coupon_record') . ' as a left join ' . tablename('tiny_wmall_activity_coupon') . ' as b ' . $coupon_condition, $coupon_params));
}

if($op == 'favorite') {
	$id = intval($_GPC['id']);
	$type = trim($_GPC['type']);
	if($type == 'star') {
		$store = store_fetch($id, array('id', 'title'));
		if(empty($store)) {
			message(error(-1, '门店不存在'), '', 'ajax');
		}
		$data = array(
			'uniacid' => $_W['uniacid'],
			'uid' => $_W['member']['uid'],
			'sid' => $id,
			'addtime' => TIMESTAMP,
		);
		$is_exist = pdo_get('tiny_wmall_store_favorite', array('uniacid' => $_W['uniacid'], 'uid' => $_W['member']['uid'], 'sid' => $id));
		if(empty($is_exist)) {
			pdo_insert('tiny_wmall_store_favorite', $data);
		}
	} else {
		pdo_delete('tiny_wmall_store_favorite', array('uniacid' => $_W['uniacid'], 'uid' => $_W['member']['uid'], 'sid' => $id));
	}
	message(error(0, ''), '', 'ajax');
}
include $this->template('mine');


