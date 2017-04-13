<?php
/**
 * 外送系统
 * @author 新睿社区
 * @QQ 10373458 * @url http://www.010xr.com/
 */
defined('IN_IA') or exit('Access Denied');
mload()->model('cron');
global $_W, $_GPC;
$do = 'cron';
if($_W['isajax']) {
	set_time_limit(0);
	cron_order();
	exit('success');
}












