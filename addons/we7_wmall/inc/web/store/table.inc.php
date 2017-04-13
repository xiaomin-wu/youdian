<?php
/**
 * 外送系统
 * @author 灯火阑珊
 * @QQ 2471240272
 * @url http://bbs.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
mload()->model('table');
$store = store_check();
$title = '桌台管理';
$sid = $store['id'];
$do = 'table';
$colors = array('block-gray', 'block-red', 'block-primary', 'block-success', 'block-orange');
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'category_list';

if($op == 'category_list') {
	$data = pdo_fetchall('select * from ' . tablename('tiny_wmall_tables_category') . ' where uniacid = :uniacid and sid = :sid', array(':uniacid' => $_W['uniacid'], ':sid' => $sid));
	$tables = pdo_fetchall('select *, count(*) as num from ' . tablename('tiny_wmall_tables') . ' where uniacid = :uniacid and sid = :sid group by cid', array(':uniacid' => $_W['uniacid'], ':sid' => $sid), 'cid');
	include $this->template('store/table-category');
}

if($op == 'category_post') {
	$id = intval($_GPC['id']);
	if(checksubmit()) {
		$title = trim($_GPC['title']) ? trim($_GPC['title']) : message('名称不能为空', '', 'error');
		$data = array(
			'uniacid' => $_W['uniacid'],
			'sid' => $sid,
			'title' => $title,
			'limit_price' => trim($_GPC['limit_price']),
			'reservation_price' => trim($_GPC['reservation_price']),
		);
		if(!empty($id)) {
			pdo_update('tiny_wmall_tables_category', $data, array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'id' => $id));
		} else {
			pdo_insert('tiny_wmall_tables_category', $data);
		}
		message('编辑餐桌类型成功', $this->createWebUrl('table', array('op' => 'category_list')), 'success');
	}
	if($id > 0) {
		$item = pdo_get('tiny_wmall_tables_category', array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'id' => $id));
		if(empty($item)) {
			message('餐桌类型不存在或已删除', referer(), 'error');
		}
	}
	include $this->template('store/table-category');
}

if($op == 'category_del') {
	$id = intval($_GPC['id']);
	pdo_delete('tiny_wmall_tables_category', array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'id' => $id));
	message('删除桌台类型成功', referer(), 'success');
}

if($op == 'table_del') {
	$id = intval($_GPC['id']);
	pdo_delete('tiny_wmall_tables', array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'id' => $id));
	message('删除桌台成功', referer(), 'success');
}

if($op == 'table_post') {
	$title = '餐桌管理';
	$categorys = pdo_fetchall('select * from ' . tablename('tiny_wmall_tables_category') . ' where uniacid = :uniacid and sid = :sid', array(':uniacid' => $_W['uniacid'], ':sid' => $sid));
	if(empty($categorys)) {
		message('创建桌台前,请先添加桌台类型', $this->createWebUrl('table', array('op' => 'category_post')), 'info');
	}
	$id = intval($_GPC['id']);
	if(checksubmit()) {
		$title = trim($_GPC['title']) ? trim($_GPC['title']) : message('桌台号不能为空', '', 'error');
		$data = array(
			'uniacid' => $_W['uniacid'],
			'sid' => $sid,
			'title' => $title,
			'guest_num' => intval($_GPC['guest_num']),
			'cid' => intval($_GPC['cid']),
			'displayorder' => intval($_GPC['displayorder']),
		);
		if(!empty($id)) {
			pdo_update('tiny_wmall_tables', $data, array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'id' => $id));
		} else {
			pdo_insert('tiny_wmall_tables', $data);
			$table_id = pdo_insertid();
			message('添加桌台成功, 生成桌台二维码中...', $this->createWebUrl('ptfqrcode', array('op' => 'build', 'store_id' => $sid, 'table_id' => $table_id, 'type' => 'table')), 'success');
		}
		message('编辑桌台成功', $this->createWebUrl('table', array('op' => 'table_list')), 'success');
	}
	if($id > 0) {
		$item = pdo_get('tiny_wmall_tables', array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'id' => $id));
		if(empty($item)) {
			message('桌台不存在或已删除', referer(), 'error');
		}
	}
	include $this->template('store/table');
}

if($op == 'table_list') {
	$_GPC['t'] = $_GPC['t'] ? $_GPC['t'] : 'status';
	$table_status = table_status();
	$condition = 'where uniacid = :uniacid and sid = :sid';
	$params = array(':uniacid' => $_W['uniacid'], ':sid' => $sid);
	$cid = intval($_GPC['cid']);
	if($cid > 0) {
		$condition .= ' and cid = :cid';
		$params[':cid'] = $cid;
	}
	$title = trim($_GPC['title']);
	if(!empty($title)) {
		$condition .= ' and title like :title';
		$params[':title'] = "%{$title}%";
	}
	$data = pdo_fetchall('select * from ' . tablename('tiny_wmall_tables') . " {$condition} order by displayorder desc", $params);
	if(!empty($data)) {
		foreach($data as &$row) {
			$row['sys_url'] = murl('entry', array('m' => 'str_takeout', 'do' => 'dish', 'tid' => $row['id'], 'sid' => $sid, 'mode' => 1, 'f' => 1), true, true);
			if(!empty($row['qrcode'])) {
				$row['qrcode'] = iunserializer($row['qrcode']);
				$row['wx_url'] = $row['qrcode']['url'];
			}
		}
	}
	$categorys = pdo_fetchall('select * from ' . tablename('tiny_wmall_tables_category') . ' where uniacid = :uniacid and sid = :sid', array(':uniacid' => $_W['uniacid'], ':sid' => $sid), 'id');
	$title = '桌台信息';
	include $this->template('store/table');
}

if($op == 'table_status') {
	$id = intval($_GPC['id']);
	$item = pdo_get('tiny_wmall_tables', array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'id' => $id));
	if(empty($item)) {
		exit('桌台不存在或已删除');
	}
	pdo_update('tiny_wmall_tables', array('status' => intval($_GPC['status'])), array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'id' => $id));
	exit('success');
}



