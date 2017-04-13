<?php
/**
 * 外送系统
 * @author 灯火阑珊
 * @QQ 2471240272
 * @url http://bbs.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
mload()->model('store');
mload()->model('order');
mload()->func('tpl.web');
$config = sys_config();
$_W['we7_wmall']['config'] = $config;
$_W['setting']['copyright']['footerleft'] = $config['copyright']['footerleft'] ? htmlspecialchars_decode($config['copyright']['footerleft']) : $_W['setting']['copyright']['footerleft'];
$_W['setting']['copyright']['footerright'] = $config['copyright']['footerright'] ? htmlspecialchars_decode($config['copyright']['footerright']) : $_W['setting']['copyright']['footerright'];
sys_store_cron();

if($_W['role'] == 'operator') {
	define('IS_OPERATOR', 1);
	$GLOBALS['frames'] = operator_menu();
}


