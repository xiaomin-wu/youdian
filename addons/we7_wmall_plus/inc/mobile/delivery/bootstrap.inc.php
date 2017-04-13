<?php
/**
 * 外送系统
 * @author 新睿社区
 * @QQ 10373458 * @url http://www.010xr.com/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
mload()->model('store');
mload()->model('order');
mload()->model('deliveryer');

if($do != 'register') {
	if(empty($_W['openid'])) {
		$this->imessage('获取身份信息错误', referer(), 'error', '', '返回');
	}
	$deliveryer = pdo_get('tiny_wmall_plus_deliveryer', array('uniacid' => $_W['uniacid'], 'openid' => $_W['openid']));
	if(empty($deliveryer)) {
		$this->imessage('您没有配送订单的权限', $this->createMobileUrl('mine'), 'error', '请联系店铺管理员开通权限', '返回');
	}
	$sids = pdo_fetchall('select sid from ' . tablename('tiny_wmall_plus_store_deliveryer') . ' where uniacid = :uniacid and deliveryer_id = :deliveryer_id and (sid = 0 or (delivery_type = 1 and sid > 0))', array(':uniacid' => $_W['uniacid'], ':deliveryer_id' => $deliveryer['id']), 'sid');
	$sids = array_unique(array_keys($sids));
	if(empty($sids)) {
		$this->imessage('您已申请过配送员了', $this->createMobileUrl('index'), 'info', '请联系平台管理员或店铺管理员分配接单权限', '去首页逛逛');
	}

	$_W['we7_wmall_plus']['deliveryer']['user'] = $deliveryer;
	$_W['we7_wmall_plus']['deliveryer']['store'] = $sids;
	$_W['we7_wmall_plus']['deliveryer']['type'] = 1; //平台配送员.
	if(!in_array(0, $sids)) {
		$_W['we7_wmall_plus']['deliveryer']['type'] = 2; //店内配送员
	} else {
		if(count($sids) > 1) {
			$_W['we7_wmall_plus']['deliveryer']['type'] = 3;
		}
	}
}

$config_takeout = $_W['we7_wmall_plus']['config']['takeout'];
$config_delivery = $_W['we7_wmall_plus']['config']['delivery'];

