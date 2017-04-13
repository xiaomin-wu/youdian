<?php
/**
 * 外送系统
 * @author 灯火阑珊
 * @QQ 2471240272
 * @url http://bbs.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$do = 'mghome';
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'list';
mload()->model('manage');
checkstore();
$sid = intval($_GPC['__mg_sid']);
$title = $_W['we7_wmall']['store']['title'];
$store = $_W['we7_wmall']['store'];

$today_starttime = strtotime(date('Y-m-d'));
$month_starttime = strtotime(date('Y-m'));
$stat['today_total'] = intval(pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_order') . ' where uniacid = :uniacid and sid = :sid and status = 5 and is_pay = 1 and addtime >= :starttime', array(':uniacid' => $_W['uniacid'], ':sid' => $sid, ':starttime' => $today_starttime)));
$stat['today_price'] = floatval(pdo_fetchcolumn('select sum(final_fee) from ' . tablename('tiny_wmall_order') . ' where uniacid = :uniacid and sid = :sid and status = 5 and is_pay = 1 and addtime >= :starttime', array(':uniacid' => $_W['uniacid'], ':sid' => $sid, ':starttime' => $today_starttime)));
$stat['today_member'] = intval(pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_store_members') . ' where uniacid = :uniacid and sid = :sid and first_order_time >= :starttime', array(':uniacid' => $_W['uniacid'], ':sid' => $sid, ':starttime' => $month_starttime)));
$stat['month_price'] = floatval(pdo_fetchcolumn('select sum(final_fee) from ' . tablename('tiny_wmall_order') . ' where uniacid = :uniacid and sid = :sid and status = 5 and is_pay = 1 and addtime >= :starttime', array(':uniacid' => $_W['uniacid'], ':sid' => $sid, ':starttime' => $month_starttime)));


include $this->template('manage/home');