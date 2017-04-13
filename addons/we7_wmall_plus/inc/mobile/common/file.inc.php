<?php
/**
 * 外送系统
 * @author 新睿社区
 * @QQ 10373458 * @url http://www.010xr.com/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$do = 'file';
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'image';

if($op == 'image') {
	$media_id = trim($_GPC['media_id']);
	$status = media_id2url($media_id);
	if(is_error($status)) {
		message($status, '', 'ajax');
	}
	$data = array(
		'errno' => 0,
		'message' => $status,
		'url' => tomedia($status),
	);
	message($data, '', 'ajax');
}

