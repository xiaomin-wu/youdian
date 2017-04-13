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
	$condition = ' where uniacid = :uniacid';
	$params = array(':uniacid' => $_W['uniacid']);
	$sid = intval($_GPC['sid']);
	if($sid > 0) {
		$condition .= ' and sid = :sid';
		$params[':sid'] = $sid;
	}
	$key = trim($_GPC['key']);
	if(!empty($key)) {
		$condition .= ' and title like :key';
		$params[':key'] = "%{$key}%";
	}
	$data = pdo_fetchall('select id, title, thumb, price, total from ' . tablename('tiny_wmall_plus_goods') . $condition, $params, 'id');
	if(!empty($data)) {
		foreach($data as &$row) {
			$row['thumb'] = tomedia($row['thumb']);
			if($row['total'] == -1) {
				$row['total'] = '无限';
			}
		}
		$goods = array_values($data);
	}
	message(array('errno' => 0, 'message' => $goods, 'data' => $data), '', 'ajax');
}