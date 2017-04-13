<?php
/**
 * 外送系统
 * @author 新睿社区
 * @QQ 10373458 * @url http://www.010xr.com/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$_W['page']['title'] = '订单列表-' . $_W['we7_wmall_plus']['config']['title'];
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'stat';

header('location: ' . $this->createWebUrl('ptforder-takeout'));
die;
include $this->template('plateform/order-stat');