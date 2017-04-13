<?php
/**
 * 外送系统
 * @author 灯火阑珊
 * @QQ 2471240272
 * @url http://bbs.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$do = 'help';

$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'index';
if($op == 'index') {
	$helps = pdo_fetchall('select * from ' . tablename('tiny_wmall_help') . ' where uniacid = :uniacid order by displayorder desc, id asc', array(':uniacid' => $_W['uniacid']));

}

include $this->template('help');
