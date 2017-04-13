<?php
/**
 * 外送系统
 * @author 灯火阑珊
 * @QQ 2471240272
 * @url http://bbs.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$_W['page']['title'] = '订单列表-' . $_W['we7_wmall']['config']['title'];
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'stat';

header('location: ' . $this->createWebUrl('ptforder-takeout'));
die;
include $this->template('plateform/order-stat');