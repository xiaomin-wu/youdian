<?php
/**
 * 外送系统
 * @author 新睿社区
 * @QQ 10373458 * @url http://www.010xr.com/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;

$op = trim($_GPC['op']);

if($op == 'list') {
	$key = trim($_GPC['key']);
	$data = pdo_fetchall('select id, title, logo, addtime from ' . tablename('tiny_wmall_plus_store') . ' where uniacid = :uniacid and title like :key order by id desc limit 50', array(':uniacid' => $_W['uniacid'], ':key' => "%{$key}%"), 'id');
	if(!empty($data)) {
		foreach($data as &$row) {
			$row['logo'] = tomedia($row['logo']);
			if(empty($row['addtime'])) {
				$row['addtime'] = '未知';
			} else {
				$row['addtime'] = date('Y-m-d H:i');
			}
		}
		$stores = array_values($data);
	}
	message(array('errno' => 0, 'message' => $stores, 'data' => $data), '', 'ajax');
}