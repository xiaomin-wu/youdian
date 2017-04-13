<?php
/**
 * 外送系统
 * @author 新睿社区
 * @QQ 10373458 * @url http://www.010xr.com/
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
