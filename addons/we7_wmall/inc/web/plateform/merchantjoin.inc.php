<?php
/**
 * 外送系统
 * @author 灯火阑珊
 * @QQ 2471240272
 * @url http://bbs.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$_W['page']['title'] = '商家入驻-' . $_W['we7_wmall']['config']['title'];

$sid = $store['id'];
$do = 'merchantjoin';
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'list';


include $this->template('plateform/merchantjoin');