<?php
/**
 * 外送系统
 * @author 新睿社区
 * @QQ 10373458 * @url http://www.010xr.com/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC, $_POST;
mload()->model('store');
mload()->model('order');
load()->func('logging');
$_W['we7_wmall_plus']['config'] = sys_config();

