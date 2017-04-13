<?php
/**
 * 外送系统
 * @author 新睿社区
 * @QQ 10373458 * @url http://www.010xr.com/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$do = 'report';
$this->icheckAuth();
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'index';

if($op == 'index') {
	$sid = intval($_GPC['sid']);
	$store = store_fetch($sid, array('title', 'id'));
	if(empty($store)) {
		message(ierror(-1, '门店不存在或已删除'), '', 'ajax');
	}
	$reports = $_W['we7_wmall_plus']['config']['report'];
	message(ierror(0, '', $reports), '', 'ajax');
}

if($op == 'post') {
	$title = !empty($_GPC['title']) ? trim($_GPC['title']) : message(ierror(-1, '投诉类型有误'), '', 'ajax');
	$data = array(
		'uniacid' => $_W['uniacid'],
		'acid' => $_W['acid'],
		'sid' => intval($_GPC['sid']),
		'uid' => $_W['member']['uid'],
		'openid' => $_W['openid'],
		'title' => $title,
		'note' => trim($_GPC['note']),
		'mobile' => trim($_GPC['mobile']),
		'addtime' => TIMESTAMP,
	);
	$thumbs = array();
	if(!empty($_GPC['thumbs'])) {
		foreach($_GPC['thumbs'] as $row) {
			if(empty($row)) continue;
			$thumbs[] = $row;
		}
		$data['thumbs'] = iserializer($thumbs);
	}
	pdo_insert('tiny_wmall_plus_report', $data);
	message(ierror(0, '投诉成功'), '', 'ajax');
}
