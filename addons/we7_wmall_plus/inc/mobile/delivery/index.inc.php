<?php
/**
 * 外送系统
 * @author 新睿社区
 * @QQ 10373458 * @url http://www.010xr.com/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
mload()->model('deliveryer');
$do = 'index';
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'list';
checkdeliveryer();
header('location:' . $this->createMobileUrl('dyorder'));
die;