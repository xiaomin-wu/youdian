<?php
/**
 * 外送系统
 * @author 新睿社区
 * @QQ 10373458 * @url http://www.010xr.com/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$_W['page']['title'] = '推荐商品';
$do = 'goods';
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'list';

if($op == 'list') {
	if(checksubmit('submit')) {
		if(!empty($_GPC['ids'])) {
			foreach($_GPC['ids'] as $k => $v) {
				$data = array(
					'title' => trim($_GPC['title'][$k]),
					'displayorder' => intval($_GPC['displayorder'][$k])
				);
				pdo_update('tiny_wmall_plate_goods', $data, array('uniacid' => $_W['uniacid'], 'id' => intval($v)));
			}
			message('编辑成功', $this->createWebUrl('ptfcgoods'), 'success');
		}
	}

	$condition = ' uniacid = :uniacid';
	$params[':uniacid'] = $_W['uniacid'];
	$pindex = max(1, intval($_GPC['page']));
	$psize = 20;
	$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('tiny_wmall_plate_goods') . ' WHERE ' . $condition, $params);
	$goods = pdo_fetchall('SELECT * FROM ' . tablename('tiny_wmall_plate_goods') . ' WHERE ' . $condition . ' ORDER BY displayorder DESC,id ASC LIMIT '.($pindex - 1) * $psize.','.$psize, $params, 'id');
	$pager = pagination($total, $pindex, $psize);
}

if($op == 'post') {
	$id = intval($_GPC['id']);
	$goods = pdo_get('tiny_wmall_plate_goods', array('uniacid' => $_W['uniacid'], 'id' => $id));
	if(checksubmit('submit')) {
		$title = trim($_GPC['title']) ? trim($_GPC['title']) : message('名称不能为空');
		$data = array(
			'title' => $title,
			'uniacid' => $_W['uniacid'],
			'thumb' => trim($_GPC['thumb']),
			'keyword' => trim($_GPC['keyword']),
			'displayorder' => intval($_GPC['displayorder']),
		);
		if(empty($goods['id'])) {
			pdo_insert('tiny_wmall_plate_goods', $data);
		} else {
			pdo_update('tiny_wmall_plate_goods', $data, array('uniacid' => $_W['uniacid'], 'id' => $id));
		}
		message('编辑商品成功', $this->createWebUrl('ptfcgoods'), 'success');
	}
}

if($op == 'del') {
	$id = intval($_GPC['id']);
	pdo_delete('tiny_wmall_plate_goods', array('uniacid' => $_W['uniacid'], 'id' => $id));
	message('删除门店分类成功', $this->createWebUrl('ptfcgoods'), 'success');
}

include $this->template('plateform/cgoods');

