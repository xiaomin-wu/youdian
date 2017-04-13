<?php
/**
 * 外送系统
 * @author 灯火阑珊
 * @QQ 2471240272
 * @url http://bbs.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$do = 'report';
$this->checkAuth();
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'index';

if($op == 'index') {
	mload()->func('tpl.app');
	$title = '举报商家';
	$sid = intval($_GPC['sid']);
	$store = store_fetch($sid, array('title', 'id'));
	if(empty($store)) {
		$this->imessage('门店不存在或已删除', referer(), 'error');
	}
	$reports = $_W['we7_wmall']['config']['report'];
	include $this->template('report');
}

if($op == 'post') {
	$title = !empty($_GPC['title']) ? trim($_GPC['title']) : message(error(-1, '投诉类型有误'), '', 'ajax');
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
	pdo_insert('tiny_wmall_report', $data);
	message(error(0, '投诉成功'), '', 'ajax');
}
