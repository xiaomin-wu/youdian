<?php
/**
 * 外送系统
 * @author 新睿社区
 * @QQ 10373458 * @url http://www.010xr.com/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$do = 'mgtrade';
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'getcash';
mload()->model('manage');
checkstore();
$sid = intval($_GPC['__mg_sid']);
$store = $_W['we7_wmall_plus']['store'];
$account = $store['account'];
$title = '申请提现';

if($op == 'getcash') {
	if($_W['isajax']) {
		if(empty($account['wechat']['openid']) || empty($account['wechat']['realname'])) {
			message(error(-1, '提现账户不完善, 无法提现'), '', 'ajax');
		}
		$get_fee = floatval($_GPC['fee']);
		if(!$get_fee) {
			message(error(-1, '提现金额有误'), '', 'ajax');
		}
		if($get_fee < $account['fee_limit']) {
			message(error(-1, '提现金额小于最低提现金额限制'), '', 'ajax');
		}
		if($get_fee > $account['amount']) {
			message(error(-1, '提现金额大于账户可用余额'), '', 'ajax');
		}
		$take_fee = round($get_fee * ($account['fee_rate'] / 100), 2);
		$take_fee = max($take_fee, $account['fee_min']);
		if($account['fee_max'] > 0) {
			$take_fee = min($take_fee, $account['fee_max']);
		}
		$final_fee = $get_fee - $take_fee;
		if($final_fee <= 0)  {
			message(error(-1, '实际到账金额小于0元'), '', 'ajax');
		}

		$data = array(
			'uniacid' => $_W['uniacid'],
			'sid' => $sid,
			'trade_no' => date('YmdHis') . random(10, true),
			'get_fee' => $get_fee,
			'take_fee' => $take_fee,
			'final_fee' => $final_fee,
			'account' => iserializer($account['wechat']),
			'status' => 2,
			'addtime' => TIMESTAMP,
		);
		pdo_insert('tiny_wmall_plus_store_getcash_log', $data);
		$getcash_id = pdo_insertid();
		store_update_account($sid, -$get_fee, 2, $getcash_id);
		//提现通知
		sys_notice_store_getcash($sid, $getcash_id, 'apply');
		message(error(0, '申请提现成功'), '', 'ajax');
	}
	if(empty($account)) {
		$this->imessage('门店账户不存在', referer(), 'error', '请联系平台管理员');
	}
	include $this->template('manage/getcash');
}

