<?php
/**
 * 外送系统
 * @author 灯火阑珊
 * @QQ 2471240272
 * @url http://bbs.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$do = 'mgstore';
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'index';
mload()->model('manage');
checkstore();
$sid = intval($_GPC['__mg_sid']);
$store = $_W['we7_wmall']['store'];

if($op == 'index') {

}

if($op == 'is_in_business') {
	if($_W['isajax']) {
		$is_in_business =  intval($_GPC['is_in_business']);
		pdo_update('tiny_wmall_store', array('is_in_business' => $is_in_business), array('uniacid' => $_W['uniacid'], 'id' => $sid));
		$info = array('关店成功', '开店成功');
		message(error(0, $info[$is_in_business]), '', 'ajax');
	}
}
include $this->template('manage/store-index');