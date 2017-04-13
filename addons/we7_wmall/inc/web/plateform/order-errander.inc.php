<?php
/**
 * 外送系统
 * @author 灯火阑珊
 * @QQ 2471240272
 * @url http://bbs.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$_W['page']['title'] = '跑腿订单-' . $_W['we7_wmall']['config']['title'];
mload()->model('deliveryer');
mload()->model('errander');
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'list';
$GLOBALS['frames'] = array();

$config = $_W['we7_wmall']['config'];
if($op == 'list') {
	$condition = ' WHERE uniacid = :uniacid';
	$params[':uniacid'] = $_W['uniacid'];

	$status = intval($_GPC['status']);
	if($status > 0) {
		$condition .= ' AND status = :status';
		$params[':status'] = $status;
	}
	$is_pay = isset($_GPC['is_pay']) ? intval($_GPC['is_pay']) : -1;
	if($is_pay >= 0) {
		$condition .= ' AND is_pay = :is_pay';
		$params[':is_pay'] = $is_pay;
	}
	$keyword = trim($_GPC['keyword']);
	if(!empty($keyword)) {
		$condition .= " AND (accept_username LIKE '%{$keyword}%' OR accept_mobile LIKE '%{$keyword}%' OR order_sn LIKE '%{$keyword}%')";
	}
	if(!empty($_GPC['addtime'])) {
		$starttime = strtotime($_GPC['addtime']['start']);
		$endtime = strtotime($_GPC['addtime']['end']) + 86399;
	} else {
		$starttime = strtotime('-15 day');
		$endtime = TIMESTAMP;
	}
	$condition .= " AND addtime > :start AND addtime < :end";
	$params[':start'] = $starttime;
	$params[':end'] = $endtime;

	$pindex = max(1, intval($_GPC['page']));
	$psize = 15;

	$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('tiny_wmall_errander_order') .  $condition, $params);
	$data = pdo_fetchall('SELECT * FROM ' . tablename('tiny_wmall_errander_order') . $condition . ' ORDER BY id DESC LIMIT '.($pindex - 1) * $psize.','.$psize, $params, 'id');

	$pager = pagination($total, $pindex, $psize);
	$pay_types = order_pay_types();
	$errander_types = errander_types();
	$errander_order_status = errander_order_status();
	$deliveryers = deliveryer_fetchall();
}

if($op == 'cancel') {
	$ids = $_GPC['id'];
	if(!is_array($ids)) {
		$ids = array($ids);
	}
	foreach($ids as $id) {
		$id = intval($id);
		$status = errander_order_status_update($id, 'cancel');
		if(is_error($status)) {
			message($status['message'], referer(), 'error');
		}
	}
	message('取消订单成功', referer(), 'success');
}

if($op == 'end') {
	$ids = $_GPC['id'];
	if(!is_array($ids)) {
		$ids = array($ids);
	}
	foreach($ids as $id) {
		$id = intval($id);
		$status = errander_order_status_update($id, 'end');
		if(is_error($status)) {
			message($status['message'], referer(), 'error');
		}
	}
	message('设置订单完成成功', referer(), 'success');
}

if($op == 'del') {
	$ids = $_GPC['id'];
	if(!is_array($ids)) {
		$ids = array($ids);
	}
	foreach($ids as $id) {
		$id = intval($id);
		$order = pdo_get('tiny_wmall_errander_order', array('uniacid' => $_W['uniacid'], 'id' => $id));
		if($order['status'] != 4) {
			message('订单状态有误， 不能删除订单', referer(), 'error');
		}
		pdo_delete('tiny_wmall_errander_order', array('uniacid' => $_W['uniacid'], 'id' => $id));
		pdo_delete('tiny_wmall_errander_order_status_log', array('uniacid' => $_W['uniacid'], 'oid' => $id));
		pdo_delete('tiny_wmall_order_refund_log', array('uniacid' => $_W['uniacid'], 'oid' => $id, 'order_type' => 'errander'));
	}
	message('删除订单成功', referer(), 'success');
}

if($op == 'begin_refund') {
	$id = intval($_GPC['id']);
	$result = errander_order_begin_payrefund($id);
	if(!is_error($result)) {
		$query = errander_order_query_payrefund($id);
		if(is_error($query)) {
			message('发起退款成功, 获取退款状态失败', referer(), 'error');
		} else {
			message('发起退款成功, 退款状态已更新', referer(), 'success');
		}
	} else {
		message($result['message'], referer(), 'error');
	}
}

if($op == 'query_refund') {
	$id = intval($_GPC['id']);
	$query = errander_order_query_payrefund($id);
	if(is_error($query)) {
		message('获取退款状态失败', referer(), 'error');
	} else {
		message('更新退款状态成功', referer(), 'success');
	}
}

if($op == 'detail') {
	$id = intval($_GPC['id']);
	$order = errander_order_fetch($id);
	if(empty($order)) {
		message('订单不存在或已经删除', $this->createWebUrl('manage', array('op' => 'order')), 'error');
	}
	$pay_types = order_pay_types();
	$order_types = errander_types();
	$order_status = errander_order_status();
	$logs = errander_order_fetch_status_log($id);
}

if($op == 'analyse') {
	$id = intval($_GPC['id']);
	$deliveryers = errander_order_analyse($id);
	if(is_error($deliveryers)) {
		message($deliveryers, '', 'ajax');
	}
	message(error(0, $deliveryers), '', 'ajax');
}

if($op == 'dispatch') {
	$order_id = intval($_GPC['order_id']);
	$deliveryer_id = intval($_GPC['deliveryer_id']);
	$status = errander_order_assign_deliveryer($order_id, $deliveryer_id, true);
	if(is_error($status)) {
		message($status, '', 'ajax');
	}
	message(error(0, '分配订单成功'), '', 'ajax');
}

if($op == 'export') {
	load()->model('mc');
	$stores = store_fetchall(array('id', 'title'));
	$pay_types = order_pay_types();

	$condition = ' WHERE uniacid = :uniacid and order_type < 3';
	$params[':uniacid'] = $_W['uniacid'];

	$sid = intval($_GPC['sid']);
	if($sid > 0) {
		$condition .= ' AND sid = :sid';
		$params[':sid'] = $sid;
	}
	$is_pay = isset($_GPC['is_pay']) ? intval($_GPC['is_pay']) : -1;
	if($is_pay >= 0) {
		$condition .= ' AND is_pay = :is_pay';
		$params[':is_pay'] = $is_pay;
	}
	$status = intval($_GPC['status']);
	if($status > 0) {
		$condition .= ' AND status = :status';
		$params[':status'] = $status;
	}
	$keyword = trim($_GPC['keyword']);
	if(!empty($keyword)) {
		$condition .= " AND (ordersn LIKE '%{$keyword}%' or mobile LIKE '%{$keyword}%' or username LIKE '%{$keyword}%')";
	}
	if(!empty($_GPC['addtime'])) {
		$starttime = strtotime($_GPC['addtime']['start']);
		$endtime = strtotime($_GPC['addtime']['end']) + 86399;
	} else {
		$starttime = strtotime('-15 day');
		$endtime = TIMESTAMP;
	}
	$condition .= " AND addtime > :start AND addtime < :end";
	$params[':start'] = $starttime;
	$params[':end'] = $endtime;

	$list = pdo_fetchall('SELECT * FROM ' . tablename('tiny_wmall_order') . $condition . ' ORDER BY id DESC', $params);
	$order_fields = array(
		'id' => array(
			'field' => 'id',
			'title' => '订单ID',
			'width' => '10',
		),
		'ordersn' => array(
			'field' => 'ordersn',
			'title' => '订单编号',
			'width' => '30',
		),
		'uid' => array(
			'field' => 'uid',
			'title' => '下单人UID',
			'width' => '10',
		),
		'openid' => array(
			'field' => 'openid',
			'title' => '粉丝openid',
			'width' => '40',
		),
		'sid' => array(
			'field' => 'sid',
			'title' => '下单门店',
			'width' => '15',
		),
		'username' => array(
			'field' => 'username',
			'title' => '收货人',
			'width' => '15',
		),
		'mobile' => array(
			'field' => 'mobile',
			'title' => '手机号',
			'width' => '20',
		),
		'address' => array(
			'field' => 'address',
			'title' => '收货地址',
			'width' => '40',
		),
		'pay_type' => array(
			'field' => 'pay_type',
			'title' => '支付方式',
			'width' => '15',
		),
		'num' => array(
			'field' => 'num',
			'title' => '份数',
			'width' => '10',
		),
		'total_fee' => array(
			'field' => 'total_fee',
			'title' => '总价',
			'width' => '15',
		),
		'discount_fee' => array(
			'field' => 'discount_fee',
			'title' => '优惠金额',
			'width' => '15',
		),
		'final_fee' => array(
			'field' => 'final_fee',
			'title' => '优惠后价格',
			'width' => '15',
		),
		'addtime' => array(
			'field' => 'addtime',
			'title' => '下单时间',
			'width' => '25',
		),
		'goods' => array(
			'field' => 'goods',
			'title' => '商品信息',
			'width' => '100',
		),
	);

	$_GPC['fields'] = explode('|', $_GPC['fields']);
	if(!empty($_GPC['fields'])) {
		$groups = mc_groups();
		$fields = mc_acccount_fields();
		$user_fields = array();
		foreach($_GPC['fields'] as $field) {
			if(in_array($field, array_keys($fields))) {
				$user_fields[$field] = array(
					'field' => $field,
					'title' => $fields[$field],
					'width' => '25',
				);
			}
		}
		if(!empty($user_fields)) {
			$uids = array();
			foreach($list as $li) {
				if(!in_array($li['uid'], $uids)) {
					$uids[] = $li['uid'];
				}
			}
			$uids = array_unique($uids);
			$uids_str = implode(',', $uids);
			$users = pdo_fetchall('select * from ' . tablename('mc_members') . " where uniacid = :uniacid and uid in ({$uids_str})", array(':uniacid' => $_W['uniacid']), 'uid');
		}
		$header = array_merge($order_fields, $user_fields);
	}
	$ABC = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AV', 'AW', 'AX', 'AY', 'AZ');
	$i = 0;
	foreach($header as $key => $val) {
		$all_fields[$ABC[$i]] = $val;
		$i++;
	}

	include_once(IA_ROOT . '/framework/library/phpexcel/PHPExcel.php');
	$objPHPExcel = new PHPExcel();

	foreach($all_fields as $key => $li) {
		$objPHPExcel->getActiveSheet()->getColumnDimension($key)->setWidth($li['width']);
		$objPHPExcel->getActiveSheet()->getStyle($key)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue($key . '1', $li['title']);
	}
	if(!empty($list)) {
		$oids = array();
		foreach($list as $li) {
			$oids[] = $li['id'];
		}
		$oid_str = implode(',', $oids);
		$goods_temp = pdo_fetchall('select * from ' . tablename('tiny_wmall_order_stat') . " where uniacid = :uniacid and oid in ({$oid_str})", array(':uniacid' => $_W['uniacid']));
		foreach($goods_temp as $row) {
			$goods[$row['oid']][] = $row['goods_title'] . ' X ' . $row['goods_num'] . '份';
		}
		for($i = 0, $length = count($list); $i < $length; $i++) {
			$row = $list[$i];
			$row['addtime'] = date('Y/m/d H:i', $row['addtime']);
			$row['ordersn'] = " {$row['ordersn']}";
			foreach($all_fields as $key => $li) {
				$field = $li['field'];
				if(in_array($field, array_keys($order_fields))) {
					if($field == 'sid') {
						$row[$field] = $stores[$row[$field]]['title'];
					} elseif($field == 'pay_type') {
						$row[$field] = $pay_types[$row[$field]]['text'];
					} elseif($field == 'goods') {
						$row[$field] = implode(", ", $goods[$row['id']]);
					}
				} else {
					$row[$field] = $users[$row['uid']][$field];
					if($field == 'groupid') {
						$row[$field] = $groups[$row['groupid']]['title'];
					}
				}
				$objPHPExcel->getActiveSheet(0)->setCellValue($key . ($i + 2), $row[$field]);
			}
		}
	}
	$objPHPExcel->getActiveSheet()->setTitle('订单数据');
	$objPHPExcel->setActiveSheetIndex(0);

	// 输出
	header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
	header('Content-Disposition: attachment;filename="订单数据.xls"');
	header('Cache-Control: max-age=0');

	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	$objWriter->save('php://output');
	exit();
}

include $this->template('plateform/order-errander');