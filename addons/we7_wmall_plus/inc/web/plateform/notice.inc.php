<?php
/**
 * 外送系统
 * @author 新睿社区
 * @QQ 10373458 * @url http://www.010xr.com/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$_W['page']['title'] = '公告管理';
$do = 'notice';
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'list';
if($op == 'list'){
	if(checksubmit('submit')) {
		if(!empty($_GPC['ids'])) {
			foreach ($_GPC['ids'] as $k => $v) {
				$data = array(
					'title' => trim($_GPC['titles'][$k]),
					'link' => trim($_GPC['links'][$k]),
					'displayorder' => intval($_GPC['displayorders'][$k]),
				);
				pdo_update('tiny_wmall_plus_notice', $data, array('uniacid' => $_W['uniacid'], 'id' => intval($v)));
			}
		}
		message('编辑成功', $this->createWebUrl('ptfnotice'), 'success');
	}
	$notices = pdo_fetchall('select * from' . tablename('tiny_wmall_plus_notice') . 'where uniacid = :uniacid order by displayorder desc', array(':uniacid' => $_W['uniacid']));
}

if($op == 'post'){
	$id = intval($_GPC['id']);
	if($id > 0) {
		$notice = pdo_get('tiny_wmall_plus_notice', array('uniacid' => $_W['uniacid'], 'id' => $id));
	}
	if(empty($notice)) {
		$notice = array(
			'status' => 1,
		);
	}
	if(checksubmit()) {
		$data = array(
			'uniacid' => $_W['uniacid'],
			'title' => trim($_GPC['title']),
			'content' => htmlspecialchars_decode($_GPC['content']),
			'link' => trim($_GPC['link']),
			'displayorder' => intval($_GPC['displayorder']),
			'status' => intval($_GPC['status']),
			'addtime' => TIMESTAMP,
		);
		if(!empty($notice['id'])) {
			pdo_update('tiny_wmall_plus_notice', $data, array('uniacid' => $_W['uniacid'], 'id' => $id));
		} else {
			pdo_insert('tiny_wmall_plus_notice', $data);
		}
		message('更新公告成功', $this->createWebUrl('ptfnotice'), 'success');
	}
}

if($op == 'del'){
	$id = intval($_GPC['id']);
	pdo_delete('tiny_wmall_plus_notice', array('uniacid' => $_W['uniacid'], 'id' => $id));
	message('删除公告成功', $this->createWebUrl('ptfnotice'), 'success');
}

if($op == 'toggle_status'){
	if($_W['isajax']) {
		$id = intval($_GPC['id']);
		$data = array(
			'status' => intval($_GPC['status']),
		);
		pdo_update('tiny_wmall_plus_notice', $data, array('id' => $id));
	}
}
include $this->template('plateform/notice');