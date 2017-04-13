<?php
/**
 * 外送系统
 * @author 新睿社区
 * @QQ 10373458 * @url http://www.010xr.com/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
mload()->model('store');
mload()->model('order');
mload()->func('tpl.web');
$config = sys_config();
$_W['we7_wmall_plus']['config'] = $config;
$_W['setting']['copyright']['footerleft'] = $config['copyright']['footerleft'] ? htmlspecialchars_decode($config['copyright']['footerleft']) : $_W['setting']['copyright']['footerleft'];
$_W['setting']['copyright']['footerright'] = $config['copyright']['footerright'] ? htmlspecialchars_decode($config['copyright']['footerright']) : $_W['setting']['copyright']['footerright'];

if($_W['role'] == 'operator') {
	define('IS_OPERATOR', 1);
	$GLOBALS['frames'] = operator_menu();
}


