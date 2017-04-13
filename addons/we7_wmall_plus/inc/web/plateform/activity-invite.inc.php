<?php
/**
 * 外送系统
 * @author 新睿社区
 * @QQ 10373458 * @url http://www.010xr.com/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;

$_W['page']['title'] = '活动';
$do = 'activity-invite';
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'list';


include $this->template('plateform/activity-invite');