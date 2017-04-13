<?php
/**
 * 外送系统
 * @author 新睿社区
 * @QQ 10373458 * @url http://www.010xr.com/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$do = 'notice';
$id = intval($_GPC['id']);
$notice = pdo_get('tiny_wmall_plus_notice', array('id' => $id, 'uniacid' => $_W['uniacid']));
include $this->template('notice');
