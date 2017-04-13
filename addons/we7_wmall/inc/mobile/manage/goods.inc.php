<?php
/**
 * 外送系统
 * @author 灯火阑珊
 * @QQ 2471240272
 * @url http://bbs.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$do = 'mggoods';
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'list';
mload()->model('manage');
checkstore();
$sid = intval($_GPC['__mg_sid']);
$title = '商品管理';

if($op == 'list') {
	$categorys = store_fetchall_goods_category($sid);

	$condition = ' WHERE uniacid = :uniacid AND sid = :sid';
	$params[':uniacid'] = $_W['uniacid'];
	$params[':sid'] = $sid;

	$status = isset($_GPC['status']) ? intval($_GPC['status']) : -1;
	if($status >= 0) {
		$condition .= ' AND status = :status';
		$params[':status'] = $status;
	}
	$cid_cn = '不限';
	$cid = intval($_GPC['cid']);
	if($cid > 0) {
		$condition .= ' AND cid = :cid';
		$params[':cid'] = $cid;
		$cid_cn = $categorys[$cid]['title'];
	}
	$goods = pdo_fetchall('SELECT * FROM ' . tablename('tiny_wmall_goods') . $condition . ' order by displayorder desc, id asc', $params);
	include $this->template('manage/goods');
}

if($op == 'status') {
	$id = intval($_GPC['id']);
	$value = intval($_GPC['value']);
	pdo_update('tiny_wmall_goods', array('status' => $value), array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'id' => $id));
	message(error(0, ''), '', 'ajax');
}

if($op == 'del') {
	$id = intval($_GPC['id']);
	pdo_delete('tiny_wmall_goods', array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'id' => $id));
	pdo_delete('tiny_wmall_goods_options', array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'goods_id' => $id));
	message(error(0, ''), '', 'ajax');
}

if($op == 'turncate') {
	$id = intval($_GPC['id']);
	pdo_update('tiny_wmall_goods', array('total' => 0), array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'id' => $id));
	message(error(0, ''), '', 'ajax');
}

if($op == 'post') {
	mload()->func('tpl.app');
	$categorys = store_fetchall_goods_category($sid);
	$store_config = $_W['we7_wmall']['config']['store'];
	$id = intval($_GPC['id']);
	if($_W['isajax']) {
		$data = array(
			'sid' => $sid,
			'uniacid' => $_W['uniacid'],
			'title' => trim($_GPC['title']),
			'price' => trim($_GPC['price']),
			'discount_price' => trim($_GPC['discount_price']),
			'unitname' => trim($_GPC['unitname']),
			'total' => intval($_GPC['total']),
			'sailed' => intval($_GPC['sailed']),
			'status' => intval($_GPC['status']),
			'cid' => intval($_GPC['cid']),
			'thumb' => trim($_GPC['thumb']),
			'label' => trim($_GPC['label']),
			'displayorder' => intval($_GPC['displayorder']),
			'description' => htmlspecialchars_decode($_GPC['description']),
			'is_hot' => intval($_GPC['is_hot']),
		);
		if(!$store_config['custom_goods_sailed_status']) {
			unset($data['sailed']);
		}

		if($id) {
			pdo_update('tiny_wmall_goods', $data, array('uniacid' => $_W['uniacid'], 'id' => $id));
		} else {
			pdo_insert('tiny_wmall_goods', $data);
			$id = pdo_insertid();
		}

		//规格
		$options = array();
		$ids = array(0);
		if(!empty($_GPC['options'])) {
			foreach($_GPC['options'] as $val) {
				$title = trim($val['title']);
				$price = trim($val['price']);
				if(empty($title) || empty($price)) {
					continue;
				}
				$options[] = array(
					'id' => intval($val['id']),
					'name' => $title,
					'price' => $price,
					'total' => intval($val['total']),
					'displayorder' => intval($val['displayorder']),
				);
			}

			foreach($options as $val) {
				$option_id = $val['id'];
				if($option_id > 0) {
					pdo_update('tiny_wmall_goods_options', $val, array('uniacid' => $_W['uniacid'], 'id' => $option_id, 'goods_id' => $id));
				} else {
					$val['uniacid'] = $_W['uniacid'];
					$val['sid'] = $sid;
					$val['goods_id'] = $id;
					pdo_insert('tiny_wmall_goods_options', $val);
					$option_id = pdo_insertid();
				}
				$ids[] = $option_id;
				$i++;
			}
		}
		$ids = implode(',', $ids);
		pdo_query('delete from ' . tablename('tiny_wmall_goods_options') . " WHERE uniacid = :aid AND goods_id = :goods_id and id not in ({$ids})", array(':aid' => $_W['uniacid'], ':goods_id' => $id));

		$update = array(
			'is_options' => ($i > 0 ? 1 : 0)
		);
		pdo_update('tiny_wmall_goods', $update, array('uniacid' => $_W['uniacid'], 'id' => $id));

		message(error(0, '编辑商品成功'), '', 'ajax');
	}

	$goods = store_fetch_goods($id);
	if(is_error($goods)) {
		$goods = array(
			'total' => -1,
			'status' => 1
		);
	}
	include $this->template('manage/goods');
}

if($op == 'category_list') {
	$condition = ' uniacid = :uniacid AND sid = :sid';
	$params[':uniacid'] = $_W['uniacid'];
	$params[':sid'] = $sid;
	$status = isset($_GPC['status']) ? intval($_GPC['status']) : -1;
	if($status > -1) {
		$condition .= ' and status = :status';
		$params[':status'] = $status;
	}
	$lists = pdo_fetchall('SELECT * FROM ' . tablename('tiny_wmall_goods_category') . ' WHERE ' . $condition . ' ORDER BY displayorder DESC,id ASC', $params, 'id');
	if(!empty($lists)) {
		$ids = implode(',', array_keys($lists));
		$nums = pdo_fetchall('SELECT count(*) AS num,cid FROM ' . tablename('tiny_wmall_goods') . " WHERE uniacid = :uniacid AND cid IN ({$ids}) GROUP BY cid", array(':uniacid' => $_W['uniacid']), 'cid');
	}
	include $this->template('manage/goods-category');
}

if($op == 'category_post') {
	$id = intval($_GPC['id']);
	$title = trim($_GPC['title']) ? trim($_GPC['title']) : message(error(-1, '分组名称不能为空'), '', 'ajax');
	$displayorder = intval($_GPC['displayorder']);
	if($id > 0) {
		$category = pdo_get('tiny_wmall_goods_category', array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'id' => $id));
		if(empty($category)) {
			message(error(-1, '商品分组不存在'), '', 'ajax');
		}
		pdo_update('tiny_wmall_goods_category', array('title' => $title, 'displayorder' => $displayorder), array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'id' => $id));
	} else {
		$insert = array(
			'uniacid' => $_W['uniacid'],
			'sid' => $sid,
			'title' => $title,
			'displayorder' => $displayorder,
			'status' => 1,
		);
		pdo_insert('tiny_wmall_goods_category', $insert);
	}
	message(error(0, ''), '', 'ajax');
}

if($op == 'category_status') {
	$id = intval($_GPC['id']);
	$status = intval($_GPC['status']);
	pdo_update('tiny_wmall_goods_category', array('status' => $status), array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'id' => $id));
	message(error(0, ''), '', 'ajax');
}

if($op == 'category_del') {
	$id = intval($_GPC['id']);
	pdo_delete('tiny_wmall_goods_category', array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'id' => $id));
	pdo_delete('tiny_wmall_goods', array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'cid' => $id));
	message(error(0, ''), '', 'ajax');
}

