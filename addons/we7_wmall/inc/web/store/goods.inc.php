<?php
/**
 * 外送系统
 * @author 灯火阑珊
 * @QQ 2471240272
 * @url http://bbs.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$_W['page']['title'] = '商品列表-' . $_W['we7_wmall']['config']['title'];
mload()->model('store');

$store = store_check();
$sid = $store['id'];
$do = 'goods';
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'list';

if($op == 'post') {
	load()->func('tpl');
	$category = pdo_fetchall('SELECT title, id FROM ' . tablename('tiny_wmall_goods_category') . ' WHERE uniacid = :aid AND sid = :sid ORDER BY displayorder DESC, id ASC', array(':aid' => $_W['uniacid'], ':sid' => $sid));
	$id = intval($_GPC['id']);
	if($id) {
		$item = pdo_fetch('SELECT * FROM ' . tablename('tiny_wmall_goods') . ' WHERE uniacid = :aid AND id = :id', array(':aid' => $_W['uniacid'], ':id' => $id));
		if(empty($item)) {
			message('商品不存在或已删除', $this->createWebUrl('goods'), 'success');
		}
		if($item['is_options']) {
			$item['options'] = pdo_fetchall('SELECT * FROM ' . tablename('tiny_wmall_goods_options') . ' WHERE uniacid = :aid AND goods_id = :goods_id ORDER BY displayorder DESC, id ASC', array(':aid' => $_W['uniacid'], ':goods_id' => $id));
		}
		$item['slides']=iunserializer($item['slides']);
	} else {
		$item['total'] = -1;
		$item['unitname'] = '份';
	}
	$store_config = $_W['we7_wmall']['config']['store'];
	if(checksubmit('submit')) {
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
			'min_buy_limit' => intval($_GPC['min_buy_limit']),
			'box_price' => trim($_GPC['box_price']),
			'thumb' => trim($_GPC['thumb']),
			'label' => trim($_GPC['label']),
			'displayorder' => intval($_GPC['displayorder']),
			'description' => htmlspecialchars_decode($_GPC['description']),
			'is_options' => intval($_GPC['is_options']),
			'is_hot' => intval($_GPC['is_hot']),
			'print_label' => intval($_GPC['print_label']),
		);
		$data['slides'] = array();
		if(!empty($_GPC['slides'])) {
			foreach($_GPC['slides'] as $slides) {
				if(empty($slides)) continue;
				$data['slides'][] = $slides;
			}
		}
		$data['slides'] = iserializer($data['slides']);
		if(!$store_config['custom_goods_sailed_status']) {
			unset($data['sailed']);
		}
		if($data['is_options'] == 1) {
			$options = array();
			foreach($_GPC['options']['name'] as $key => $val) {
				$val = trim($val);
				$price = trim($_GPC['options']['price'][$key]);
				if(empty($val) || empty($price)) {
					continue;
				}
				$options[] = array(
					'id' => intval($_GPC['options']['id'][$key]),
					'name' => $val,
					'price' => $price,
					'total' => intval($_GPC['options']['total'][$key]),
					'displayorder' => intval($_GPC['options']['displayorder'][$key]),
				);
			}
			if(empty($options)) {
				message('没有设置有效的规格项');
			}
		}
		if($id) {
			pdo_update('tiny_wmall_goods', $data, array('uniacid' => $_W['uniacid'], 'id' => $id));
		} else {
			pdo_insert('tiny_wmall_goods', $data);
			$id = pdo_insertid();
		}
		$ids = array(0);
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
		}
		$ids = implode(',', $ids);
		pdo_query('delete from ' . tablename('tiny_wmall_goods_options') . " WHERE uniacid = :aid AND goods_id = :goods_id and id not in ({$ids})", array(':aid' => $_W['uniacid'], ':goods_id' => $id));
		message('编辑商品成功', $this->createWebUrl('goods'), 'success');
	}
	$print_labels = pdo_fetchall('select * from ' . tablename('tiny_wmall_printer_label') . ' where uniacid = :uniacid and sid = :sid order by displayorder desc, id asc', array(':uniacid' => $_W['uniacid'], ':sid' => $sid));
}

if($op == 'list') {
	$condition = ' uniacid = :aid AND sid = :sid';
	$params[':aid'] = $_W['uniacid'];
	$params[':sid'] = $sid;
	if(!empty($_GPC['keyword'])) {
		$condition .= " AND title LIKE '%{$_GPC['keyword']}%'";
	}
	if(!empty($_GPC['cid'])) {
		$condition .= " AND cid = :cid";
		$params[':cid'] = intval($_GPC['cid']);
	}

	$pindex = max(1, intval($_GPC['page']));
	$psize = 20;

	$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('tiny_wmall_goods') . ' WHERE ' . $condition, $params);
	$lists = pdo_fetchall('SELECT * FROM ' . tablename('tiny_wmall_goods') . ' WHERE ' . $condition . ' ORDER BY displayorder DESC,id ASC LIMIT '.($pindex - 1) * $psize.','.$psize, $params);
	if(!empty($lists)) {
	}
	$pager = pagination($total, $pindex, $psize);
	$category = pdo_fetchall('SELECT title, id FROM ' . tablename('tiny_wmall_goods_category') . ' WHERE uniacid = :aid AND sid = :sid', array(':aid' => $_W['uniacid'], ':sid' => $sid), 'id');
}

if($op == 'status') {
	$id = intval($_GPC['id']);
	$status = intval($_GPC['status']);
	$state = pdo_update('tiny_wmall_goods', array('status' => $status), array('uniacid' => $_W['uniacid'], 'id' => $id));
	if($state !== false) {
		exit('success');
	}
	exit('error');
}

if($op == 'del') {
	$id = intval($_GPC['id']);
	pdo_delete('tiny_wmall_goods', array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'id' => $id));
	pdo_delete('tiny_wmall_goods_options', array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'goods_id' => $id));
	message('删除菜品成功', $this->createWebUrl('goods'), 'success');
}

if($op == 'batch_del') {
	if(checksubmit()) {
		foreach($_GPC['id'] as $id) {
			$id = intval($id);
			pdo_delete('tiny_wmall_goods', array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'id' => $id));
			pdo_delete('tiny_wmall_goods_options', array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'goods_id' => $id));
		}
		message('删除菜品成功', $this->createWebUrl('goods'), 'success');
	}
}

if($op == 'export') {
	if(checksubmit()) {
		$file = upload_file($_FILES['file'], 'excel');
		if(is_error($file)) {
			message($file['message'], $this->createWebUrl('goods'), 'error');
		}
		$data = read_excel($file);
		if(is_error($data)) {
			message($data['message'], $this->createWebUrl('goods'), 'error');
		}
		unset($data[1]);
		if(empty($data)) {
			message('没有要导入的数据', $this->createWebUrl('goods'), 'error');
		}
		foreach($data as $da) {
			if(empty($da['0']) || empty($da['1'])) {
				continue;
			}
			$insert = array(
				'uniacid' => $_W['uniacid'],
				'sid' => $sid,
				'title' => trim($da[0]),
				'cid' => intval(pdo_fetchcolumn('select id from ' . tablename('tiny_wmall_goods_category') . ' where uniacid = :uniacid and sid = :sid and title = :title', array(':uniacid' => $_W['uniacid'], ':sid' => $sid, ':title' => $da[1]))),
				'unitname' => trim($da[2]),
				'price' => trim($da[3]),
				'label' => trim($da[4]),
				'total' => intval($da[5]),
				'sailed' => trim($da[6]),
				'thumb' => trim($da[7]),
				'displayorder' => intval($da[8]),
				'description' => trim($da[9]),
			);
			pdo_insert('tiny_wmall_goods', $insert);
			$goods_id = pdo_insertid();

			if(!empty($da[10])) {
				$options = str_replace('，', ',', $da[10]);
				$options = explode(',', $options);
				if(!empty($options)) {
					foreach($options as $option) {
						$option = explode('|', $option);
						if(count($option) == 4) {
							$insert = array(
								'uniacid' => $_W['uniacid'],
								'sid' => $sid,
								'goods_id' => $goods_id,
								'name' => trim($option[0]),
								'price' => trim($option[1]),
								'total' => intval($option[2]),
								'displayorder' => intval($option[3]),
							);
							pdo_insert('tiny_wmall_goods_options', $insert);
							$i++;
						}
					}
					if($i > 0) {
						pdo_update('tiny_wmall_goods', array('is_options' => 1), array('id' => $goods_id));
					}
				}
			}
		}
		message('导入商品成功', $this->createWebUrl('goods'), 'success');
	}
}

if($op == 'copy') {
	$id = intval($_GPC['id']);
	$goods = pdo_get('tiny_wmall_goods', array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'id' => $id));
	if(empty($goods)) {
		message('商品不存在或已删除', referer(), 'error');
	}
	if($goods['is_options']) {
		$options = pdo_getall('tiny_wmall_goods_options', array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'goods_id' => $id));
	}
	unset($goods['id']);
	$goods['title'] = $goods['title'] . '-复制';
	pdo_insert('tiny_wmall_goods', $goods);
	$goods_id = pdo_insertid();
	if(!empty($options) && $goods_id) {
		foreach($options as $option) {
			unset($option['id']);
			$option['goods_id'] = $goods_id;
			pdo_insert('tiny_wmall_goods_options', $option);
		}
	}
	message('复制商品成功, 现在进入编辑页', $this->createWebUrl('goods', array('op' => 'post', 'id' => $goods_id)), 'success');
}
include $this->template('store/goods');