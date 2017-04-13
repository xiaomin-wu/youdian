<?php
/**
 * 外送系统
 * @author 灯火阑珊
 * @QQ 2471240272
 * @url http://bbs.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$_W['page']['title'] = '广告管理-超级外卖';
$sid = $store['id'];
$do = 'ad';
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'list';

if($op == 'post') {
	$id = intval($_GPC['id']);
	if($id > 0) {
		$slide = pdo_get('tiny_wmall_slide', array('uniacid' => $_W['uniacid'], 'id' => $id, 'type' => 1));
		if(empty($slide)) {
			message('广告不存在或已删除', referer(), 'error');
		}
	} 
	if(checksubmit()) {
		$title = trim($_GPC['title']) ? trim($_GPC['title']) : message('标题不能为空');
		$data = array(
			'uniacid' => $_W['uniacid'],
			'title' => $title,
			'thumb' => trim($_GPC['thumb']),
			'link' => trim($_GPC['link']),
			'type' => 1,
			'status' => intval($_GPC['status']),
		);
		if(!empty($slide)) {
			pdo_update('tiny_wmall_slide', $data, array('uniacid' => $_W['uniacid'], 'id' => $id));
		} else {
			pdo_insert('tiny_wmall_slide', $data);
		}
		message('更新广告成功', $this->createWebUrl('ptfad'), 'success');
	}
}

if($op == 'list') {
	$slides = pdo_getall('tiny_wmall_slide', array('uniacid' => $_W['uniacid'], 'type' => 1));
}

if($op == 'del') {
	$id = intval($_GPC['id']);
	pdo_delete('tiny_wmall_slide', array('uniacid' => $_W['uniacid'], 'id' => $id));
	message('删除广告成功', $this->createWebUrl('ptfad'), 'success');
}

include $this->template('plateform/ad');