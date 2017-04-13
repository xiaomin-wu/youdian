<?php
/**
 * 外送系统
 * @author 灯火阑珊
 * @QQ 2471240272
 * @url http://bbs.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$do = 'mgindex';
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'list';
if(empty($_W['openid'])) {
	$this->imessage('获取身份信息错误', referer(), 'error', '', '返回');
}
$sid = pdo_getall('tiny_wmall_clerk', array('uniacid' => $_W['uniacid'], 'openid' => $_W['openid']), array(), 'sid');
if(empty($sid)) {
	$this->imessage('您没有管理店铺的权限', $this->createMobileUrl('mine'), 'error', '请联系店铺管理员开通权限', '返回');
}
$sid_str = implode(', ', array_unique(array_keys($sid)));
$stores = pdo_fetchall('select id, title, logo from ' . tablename('tiny_wmall_store') . " where uniacid = :uniacid and id in ({$sid_str})", array(':uniacid' => $_W['uniacid']));
include $this->template('manage/index');