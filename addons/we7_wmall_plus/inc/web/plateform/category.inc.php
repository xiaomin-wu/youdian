<?php
/**
 * 外送系统
 * @author 新睿社区
 * @QQ 10373458 * @url http://www.010xr.com/
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
	$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('tiny_wmall_plus_store_category') . ' WHERE ' . $condition, $params);
	$lists = pdo_fetchall('SELECT * FROM ' . tablename('tiny_wmall_plus_store_category') . ' WHERE ' . $condition . ' ORDER BY displayorder DESC,id ASC LIMIT '.($pindex - 1) * $psize.','.$psize, $params, 'id');
	if(!empty($lists)) {
		$ids = implode(',', array_keys($lists));
		$nums = pdo_fetchall('SELECT count(*) AS num,cid FROM ' . tablename('tiny_wmall_plus_store') . " WHERE uniacid = :aid AND cid IN ({$ids}) GROUP BY cid", array(':aid' => $_W['uniacid']), 'cid');
	}
	$pager = pagination($total, $pindex, $psize);
	if(checksubmit('submit')) {
		if(!empty($_GPC['ids'])) {
			foreach($_GPC['ids'] as $k => $v) {
				$data = array(
					'title' => trim($_GPC['title'][$k]),
					'displayorder' => intval($_GPC['displayorder'][$k])
				);
				pdo_update('tiny_wmall_plus_store_category', $data, array('uniacid' => $_W['uniacid'], 'id' => intval($v)));
			}
			message('编辑成功', $this->createWebUrl('ptfcategory'), 'success');
		}
	}
} 

if($op == 'del') {
	$id = intval($_GPC['id']);
	pdo_delete('tiny_wmall_plus_store_category', array('uniacid' => $_W['uniacid'], 'id' => $id));
	message('删除门店分类成功', $this->createWebUrl('ptfcategory'), 'success');
}

if($op == 'post') {
	$id = intval($_GPC['id']);
	if($id > 0) {
		$category = pdo_get('tiny_wmall_plus_store_category', array('uniacid' => $_W['uniacid'], 'id' => $id));
		if(!empty($category)) {
			$category['nav'] = (array)iunserializer($category['nav']);
			$category['slide'] = (array)iunserializer($category['slide']);
			if(!empty($category['slide'])) {
				$category['slide'] = array_sort($category['slide'], 'displayorder' ,SORT_DESC);
			}
		}
	}
	if(empty($category)) {
		$category = array(
			'slide_status' => 0,
			'slide' => array(),
			'nav_status' => 0,
			'nav' => array(),
		);
	}
	if(checksubmit('submit')) {
		$title = trim($_GPC['title']) ? trim($_GPC['title']) : message('分类名称不能为空');
		$nav = array();
		if(!empty($_GPC['nav_thumb'])) {
			foreach($_GPC['nav_thumb'] as $k => $v){
				if(empty($_GPC['nav_title'][$k])){
					continue;
				}
				$nav[]=array(
					'title' => trim($_GPC['nav_title'][$k]),
					'sub_title' => trim($_GPC['nav_sub_title'][$k]),
					'link' => trim($_GPC['nav_links'][$k]),
					'thumb' => trim($v),
				);
			}
		}
		$slide = array();
		if(!empty($_GPC['slide_image'])) {
			foreach($_GPC['slide_image'] as $k => $v){
				if(empty($v)){
					continue;
				}
				$slide[] = array(
					'thumb' => trim($v),
					'link' => trim($_GPC['slide_links'][$k]),
					'displayorder' => trim($_GPC['slide_displayorder'][$k]),
				);
			}
		}

		$data = array(
			'uniacid' => $_W['uniacid'],
			'title' => $title,
			'thumb' => trim($_GPC['thumb']),
			'link' => trim($_GPC['link']),
			'displayorder' => intval($_GPC['displayorder']),
			'nav_status' => intval($_GPC['nav_status']),
			'slide_status' => intval($_GPC['slide_status']),
			'nav' => iserializer($nav),
			'slide' => iserializer($slide),
		);
		if(empty($_GPC['id'])){
			pdo_insert('tiny_wmall_plus_store_category', $data);
		}else{
			pdo_update('tiny_wmall_plus_store_category', $data, array('uniacid' => $_W['uniacid'], 'id' => $id));
		}
		message('编辑门店分类成功', $this->createWebUrl('ptfcategory'), 'success');
	}
}
include $this->template('plateform/category');

