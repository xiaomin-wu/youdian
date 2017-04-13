<?php
/**
 * 外送系统
 * @author 新睿社区
 * @QQ 10373458 * @url http://www.010xr.com/
 */
defined('IN_IA') or exit('Access Denied');
mload()->model('sms');
global $_W, $_GPC;
$do = 'deliveryer';
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'location';

$this->checkAuth();

if($op == 'location') {
	$id = intval($_GPC['id']);
	$deliveryer = pdo_get('tiny_wmall_plus_deliveryer', array('uniacid' => $_W['uniacid'], 'id' => $id));
	if(empty($deliveryer)) {
		message(error(-1, '配送员不存在或已删除'), '', 'ajax');
	}
	$deliveryer['avatar'] = tomedia($deliveryer['avatar']);
	message(error(0, $deliveryer), '', 'ajax');
}