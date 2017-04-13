<?php
/**
 * 外送系统
 * @author 灯火阑珊
 * @QQ 2471240272
 * @url http://bbs.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
mload()->model('deliveryer');
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'all';

if($op == 'all') {
	$datas = deliveryer_fetchall();
	$datas = array_values($datas);
	message(error(0, $datas), '', 'ajax');
}
