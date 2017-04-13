<?php
/**
 * 外送系统
 * @author 灯火阑珊
 * @QQ 2471240272
 * @url http://bbs.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$do = 'dymine';
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'inout';
$title = '我的配送';
$deliveryer = $_W['we7_wmall']['deliveryer']['user'];
$deliveryer_type = $_W['we7_wmall']['deliveryer']['type'];

if($op == 'inout') {
	$condition = ' WHERE uniacid = :uniacid AND deliveryer_id = :deliveryer_id';
	$params = array(
		':uniacid' => $_W['uniacid'],
		':deliveryer_id' => $deliveryer['id'],
	);
	$trade_type = intval($_GPC['trade_type']);
	if($trade_type > 0) {
		$condition .= ' and trade_type = :trade_type';
		$params[':trade_type'] = $trade_type;
	}
	$id = intval($_GPC['id']);
	if($id > 0) {
		$condition .= " and id < :id";
		$params[':id'] = $id;
	}
	$records = pdo_fetchall('SELECT * FROM ' . tablename('tiny_wmall_deliveryer_current_log') . $condition . ' ORDER BY id DESC LIMIT 20', $params, 'id');
	$min = 0;
	if(!empty($records)) {
		foreach($records as &$row) {
			$row['addtime_cn'] = date('Y-m-d H:i', $row['addtime']);
		}
		$min = min(array_keys($records));
	}
	if($_W['isajax']) {
		$records = array_values($records);
		$respon = array('error' => 0, 'message' => $records, 'min' => $min);
		message($respon, '', 'ajax');
	}
	include $this->template('delivery/inout');
}

if($op == 'getcash') {
	if($_W['isajax']) {
		if($deliveryer_type == 2) {
			message(error(-1, '非法访问'), '', 'ajax');
		}
		if(empty($deliveryer['openid']) || empty($deliveryer['title'])) {
			message(error(-1, '配送员账户不完善, 无法提现'), '', 'ajax');
		}
		$get_fee = floatval($_GPC['fee']);
		if(!$get_fee) {
			message(error(-1, '提现金额有误'), '', 'ajax');
		}
		if($get_fee < $dy_config['get_cash_fee_limit']) {
			message(error(-1, '提现金额小于最低提现金额限制'), '', 'ajax');
		}
		if($get_fee > $deliveryer['credit2']) {
			message(error(-1, '提现金额大于账户可用余额'), '', 'ajax');
		}
		$take_fee = round($get_fee * ($dy_config['get_cash_fee_rate'] / 100), 2);
		$take_fee = max($take_fee, $dy_config['get_cash_fee_min']);
		$take_fee = min($take_fee, $dy_config['get_cash_fee_max']);
		$final_fee = $get_fee - $take_fee;
		if($final_fee < 0)  {
			$final_fee = 0;
		}
		$data = array(
			'uniacid' => $_W['uniacid'],
			'deliveryer_id' => $deliveryer['id'],
			'trade_no' => date('YmdHis') . random(10, true),
			'get_fee' => $get_fee,
			'take_fee' => $take_fee,
			'final_fee' => $final_fee,
			'status' => 2,
			'addtime' => TIMESTAMP,
		);
		pdo_insert('tiny_wmall_deliveryer_getcash_log', $data);
		$getcash_id = pdo_insertid();
		$remark = date('Y-m-d H:i:s') . "申请提现,提现金额{$get_fee}元, 手续费{$take_fee}元, 实际到账{$final_fee}元";
		deliveryer_update_credit2($deliveryer['id'], -$get_fee, 2, $getcash_id, $remark);
		//提现通知
		sys_notice_deliveryer_getcash($deliveryer['id'], $getcash_id, 'apply');
		message(error(0, '申请提现成功'), '', 'ajax');
	}
	include $this->template('delivery/getcash');
}

