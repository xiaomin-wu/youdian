<?php
/**
 * 外送系统
 * @author 灯火阑珊
 * @QQ 2471240272
 * @url http://bbs.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$_W['page']['title'] = '打印机管理-' . $_W['we7_wmall']['config']['title'];
mload()->model('store');
mload()->model('print');

$store = store_check();
$sid = $store['id'];
$do = 'printer';
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'list';

if($op == 'list') {
	$data = pdo_fetchall('SELECT * FROM ' . tablename('tiny_wmall_printer') . ' WHERE uniacid = :uniacid AND sid = :sid', array(':uniacid' => $_W['uniacid'], ':sid' => $sid));
	if(!empty($data)) {
		foreach($data as &$da) {
			if(!empty($da['print_no'])) {
				if(in_array($da['type'], array('feie', '365'))) {
					$da['status_cn'] = print_query_printer_status($da['type'], $da['print_no'], $da['key'], $da['member_code']);
				} else {
					$da['status_cn'] = '打印机不支持查询状态';
				}
			} else {
				$da['status_cn'] = '未知';
			}
		}
	}
	$types = print_printer_types();
	include $this->template('store/printer');
} 

if($op == 'post') {
	$id = intval($_GPC['id']);
	if($id > 0) {
		$item = pdo_fetch('SELECT * FROM ' . tablename('tiny_wmall_printer') . ' WHERE uniacid = :uniacid AND id = :id', array(':uniacid' => $_W['uniacid'], ':id' => $id));
	}
	if(!empty($item)) {
		$item['print_label'] = explode(',', $item['print_label']);
	} else {
		$item = array('status' => 1, 'print_nums' => 1, 'type' => 'feie', 'print_label' => array());
	}
	if(checksubmit('submit')) {
		$data['uniacid'] = $_W['uniacid'];
		$data['sid'] = $sid;
		$data['type'] = trim($_GPC['type']);
		$data['status'] = intval($_GPC['status']); 
		$data['name'] = !empty($_GPC['name']) ? trim($_GPC['name']) : message('打印机名称不能为空', '', 'error');
		$data['print_no'] = !empty($_GPC['print_no']) ? trim($_GPC['print_no']) : message('机器号不能为空', '', 'error');
		$data['key'] = trim($_GPC['key']);
		$data['api_key'] = trim($_GPC['api_key']);
		$data['member_code'] = trim($_GPC['member_code']);
		if($data['type'] == 'yilianyun') {
			$data['member_code'] = trim($_GPC['userid']);
		}
		$data['print_nums'] = intval($_GPC['print_nums']) ? intval($_GPC['print_nums']) : 1;
		if(!empty($_GPC['qrcode_link']) && (strexists($_GPC['qrcode_link'], 'http://') || strexists($_GPC['qrcode_link'], 'https://'))) {
			$data['qrcode_link'] = trim($_GPC['qrcode_link']);
		}
		$data['print_header'] = trim($_GPC['print_header']);
		$data['print_footer'] = trim($_GPC['print_footer']);
		$data['is_print_all'] = intval($_GPC['is_print_all']);
		$data['print_label'] = 0;
		if($_GPC['print_label_type'] == 1) {
			$print_label = array();
			foreach($_GPC['print_label'] as $label) {
				if($label > 0) {
					$print_label[] = $label;
				}
			}
			if(!empty($print_label)) {
				$data['print_label'] = implode(',', $print_label);
			}
		}
		if(!empty($item) && $id) {
			pdo_update('tiny_wmall_printer', $data, array('uniacid' => $_W['uniacid'], 'id' => $id));
		} else {
			pdo_insert('tiny_wmall_printer', $data);
		}
		message('更新打印机设置成功', $this->createWebUrl('printer', array('op' => 'list')), 'success');
	}
	$print_labels = pdo_fetchall('select * from ' . tablename('tiny_wmall_printer_label') . ' where uniacid = :uniacid and sid = :sid order by displayorder desc, id asc', array(':uniacid' => $_W['uniacid'], ':sid' => $sid));
	include $this->template('store/printer');
}

if($op == 'del') {
	$id = intval($_GPC['id']);
	pdo_delete('tiny_wmall_printer', array('uniacid' => $_W['uniacid'], 'id' => $id));
	message('删除打印机成功', referer(), 'success');
}

if($op == 'label_list') {
	if(checksubmit('submit')) {
		if(!empty($_GPC['ids'])) {
			foreach($_GPC['ids'] as $k => $v) {
				$data = array(
					'title' => trim($_GPC['title'][$k]),
					'displayorder' => intval($_GPC['displayorder'][$k])
				);
				pdo_update('tiny_wmall_printer_label', $data, array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'id' => intval($v)));
			}
			message('编辑成功',  $this->createWebUrl('printer', array('op' => 'label_list')), 'success');
		}
	}

	$condition = ' where uniacid = :uniacid and sid = :sid';
	$params = array(
		':uniacid' => $_W['uniacid'],
		':sid' => $sid,
	);
	$lists = pdo_fetchall('SELECT * FROM ' . tablename('tiny_wmall_printer_label') . $condition . ' ORDER BY displayorder DESC,id ASC', $params);
	include $this->template('store/printer');
}

if($op == 'label_post') {
	if(checksubmit('submit')) {
		if(!empty($_GPC['title'])) {
			foreach($_GPC['title'] as $k => $v) {
				$v = trim($v);
				if(empty($v)) continue;
				$data = array(
					'uniacid' => $_W['uniacid'],
					'sid' => $sid,
					'title' => $v,
					'displayorder' => intval($_GPC['displayorder'][$k]),
				);
				pdo_insert('tiny_wmall_printer_label', $data);
			}
		}
		message('添加打印标签成功', $this->createWebUrl('printer', array('op' => 'label_list')), 'success');
	}
	include $this->template('store/printer');
}

if($op == 'label_del') {
	$id = intval($_GPC['id']);
	pdo_update('tiny_wmall_goods', array('print_label' => 0), array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'print_label' => $id));
	pdo_delete('tiny_wmall_printer_label', array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'id' => $id));
	message('删除打印标签成功', referer(), 'success');
}
