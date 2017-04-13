<?php
/**
 * 外送系统
 * @author 新睿社区
 * @QQ 10373458 * @url http://www.010xr.com/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$_W['page']['title'] = '首页幻灯片-超级外卖';
$sid = $store['id'];
$do = 'slide';
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'list';

if($op == 'post') {
	$id = intval($_GPC['id']);
	if($id > 0) {
		$slide = pdo_get('tiny_wmall_plus_slide', array('uniacid' => $_W['uniacid'], 'id' => $id));
		if(empty($slide)) {
			message('幻灯片不存在或已删除', referer(), 'error');
		}
	} 
	if(checksubmit()) {
		$title = trim($_GPC['title']) ? trim($_GPC['title']) : message('标题不能为空');
		$data = array(
			'uniacid' => $_W['uniacid'],
			'title' => $title,
			'thumb' => trim($_GPC['thumb']),
			'link' => trim($_GPC['link']),
			'displayorder' => intval($_GPC['displayorder']),
			'type' => 2,
			'status' => intval($_GPC['status']),
		);
		if(!empty($slide)) {
			pdo_update('tiny_wmall_plus_slide', $data, array('uniacid' => $_W['uniacid'], 'id' => $id));
		} else {
			pdo_insert('tiny_wmall_plus_slide', $data);
		}
		message('更新幻灯片成功', $this->createWebUrl('ptfslide'), 'success');
	}
}

if($op == 'list') {
	$slides = pdo_fetchall('select * from' . tablename('tiny_wmall_plus_slide') . 'where uniacid = :uniacid and type = 2 order by displayorder desc', array(':uniacid' => $_W['uniacid']));
}

if($op == 'del') {
	$id = intval($_GPC['id']);
	pdo_delete('tiny_wmall_plus_slide', array('uniacid' => $_W['uniacid'], 'id' => $id));
	message('删除幻灯片成功', $this->createWebUrl('ptfslide'), 'success');
}

include $this->template('plateform/slide');