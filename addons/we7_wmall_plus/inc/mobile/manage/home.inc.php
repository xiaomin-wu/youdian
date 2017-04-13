<?php
/**
 * 外送系统
 * @author 新睿社区
 * @QQ 10373458 * @url http://www.010xr.com/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$do = 'mghome';
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'list';
mload()->model('manage');
checkstore();
$sid = intval($_GPC['__mg_sid']);
$title = $_W['we7_wmall_plus']['store']['title'];
$store = $_W['we7_wmall_plus']['store'];

$condition = ' where uniacid = :uniacid and sid = :sid and status = 5 and is_pay = 1 and stat_day = :stat_day';
$params = array(
	':uniacid' => $_W['uniacid'],
	':sid' => $sid,
	':stat_day' => date('Ymd'),
);
$stat['total_order'] = intval(pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_plus_order') . $condition, $params));
$stat['total_fee'] = floatval(pdo_fetchcolumn('select round(sum(total_fee), 2) from ' . tablename('tiny_wmall_plus_order') . $condition, $params));
$stat['final_fee'] = floatval(pdo_fetchcolumn('select round(sum(store_final_fee), 2) from ' . tablename('tiny_wmall_plus_order') . $condition, $params));



include $this->template('manage/home');