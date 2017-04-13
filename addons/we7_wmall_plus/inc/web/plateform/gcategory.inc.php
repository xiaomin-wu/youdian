<?php
/**
 * 外送系统
 * @author 新睿社区
 * @QQ 10373458 * @url http://www.010xr.com/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
mload()->model('build');
$_W['page']['title'] = '标签分组设置';
$do = 'gcategory';
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'TY_store_label';

if($op == 'TY_store_label') {
	if(checksubmit()) {
		$ids = array(0);
		if(!empty($_GPC['add_title'])) {
			foreach($_GPC['add_title'] as $k => $v) {
				$title = trim($v);
				$color = trim($_GPC['add_color'][$k]);
				if(empty($title) || empty($color)) {
					continue;
				}
				$insert = array(
					'uniacid' => $_W['uniacid'],
					'title' => $title,
					'color' => $color,
					'displayorder' => intval($_GPC['add_displayorder'][$k]),
					'is_system' => 0,
					'type' => 'TY_store_label'
				);
				pdo_insert('tiny_wmall_plus_category', $insert);
				$ids[] = pdo_insertid();
			}
		}
		if(!empty($_GPC['id'])) {
			foreach($_GPC['id'] as $k => $v) {
				$id = intval($v);
				$title = trim($_GPC['title'][$k]);
				$color = trim($_GPC['color'][$k]);
				if($id > 0 && empty($title) || empty($color)) {
					$ids[] = $id;
					continue;
				}
				$update = array(
					'title' => $title,
					'color' => $color,
					'displayorder' => intval($_GPC['displayorder'][$k]),
				);
				pdo_update('tiny_wmall_plus_category', $update, array('uniacid' => $_W['uniacid'], 'type' => 'TY_store_label', 'id' => $id));
				$ids[] = $id;
			}
		}
		$ids = implode(',', $ids);
		pdo_query('delete from ' . tablename('tiny_wmall_plus_category') . " where uniacid = {$_W['uniacid']} and type = 'TY_store_label' and is_system = 0 and id not in ({$ids})");
	}
	build_category('TY_store_label');
	$labels = category_store_label();
	include $this->template('plateform/category-store-label');
}

