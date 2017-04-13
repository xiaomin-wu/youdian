<?php
/**
 * 外送系统
 * @author 灯火阑珊
 * @QQ 2471240272
 * @url http://bbs.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;

$_W['page']['title'] = '门店分类';
$do = 'category';
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'list';

if($op == 'list') {
	$condition = ' uniacid = :aid';
	$params[':aid'] = $_W['uniacid'];
	$pindex = max(1, intval($_GPC['page']));
	$psize = 20;

	$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('tiny_wmall_store_category') . ' WHERE ' . $condition, $params);
	$lists = pdo_fetchall('SELECT * FROM ' . tablename('tiny_wmall_store_category') . ' WHERE ' . $condition . ' ORDER BY displayorder DESC,id ASC LIMIT '.($pindex - 1) * $psize.','.$psize, $params, 'id');
	if(!empty($lists)) {
		$ids = implode(',', array_keys($lists));
		$nums = pdo_fetchall('SELECT count(*) AS num,cid FROM ' . tablename('tiny_wmall_store') . " WHERE uniacid = :aid AND cid IN ({$ids}) GROUP BY cid", array(':aid' => $_W['uniacid']), 'cid');
	}
	$pager = pagination($total, $pindex, $psize);
	if(checksubmit('submit')) {
		if(!empty($_GPC['ids'])) {
			foreach($_GPC['ids'] as $k => $v) {
				$data = array(
					'title' => trim($_GPC['title'][$k]),
					'displayorder' => intval($_GPC['displayorder'][$k])
				);
				pdo_update('tiny_wmall_store_category', $data, array('uniacid' => $_W['uniacid'], 'id' => intval($v)));
			}
			message('编辑成功', $this->createWebUrl('ptfcategory'), 'success');
		}
	}
} 

if($op == 'post') {
	if(checksubmit('submit')) {
		if(!empty($_GPC['title'])) {
			foreach($_GPC['title'] as $k => $v) {
				$v = trim($v);
				if(empty($v)) continue;
				$data = array(
					'uniacid' => $_W['uniacid'],
					'title' => $v,
					'thumb' => trim($_GPC['thumb'][$k]),
					'link' => trim($_GPC['link'][$k]),
					'displayorder' => intval($_GPC['displayorder'][$k]),
				);
				pdo_insert('tiny_wmall_store_category', $data);
			}
		}
		message('添加门店分类成功', $this->createWebUrl('ptfcategory'), 'success');
	}
} 

if($op == 'del') {
	$id = intval($_GPC['id']);
	pdo_delete('tiny_wmall_store_category', array('uniacid' => $_W['uniacid'], 'id' => $id));
	message('删除门店分类成功', $this->createWebUrl('ptfcategory'), 'success');
}

if($op == 'edit') {
	$id = intval($_GPC['id']);
	$category = pdo_get('tiny_wmall_store_category', array('uniacid' => $_W['uniacid'], 'id' => $id));
	if(checksubmit('submit')) {
		$title = trim($_GPC['title']) ? trim($_GPC['title']) : message('分类名称不能为空');
		$update = array(
			'title' => $title,
			'thumb' => trim($_GPC['thumb']),
			'link' => trim($_GPC['link']),
			'displayorder' => intval($_GPC['displayorder']),
		);
		pdo_update('tiny_wmall_store_category', $update, array('uniacid' => $_W['uniacid'], 'id' => $id));
		message('编辑门店分类成功', $this->createWebUrl('ptfcategory'), 'success');
	}
}

include $this->template('plateform/category');

