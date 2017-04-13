<?php
/**
 * 外送系统
 * @author 灯火阑珊
 * @QQ 2471240272
 * @url http://bbs.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');
mload()->model('deliveryer');
global $_W, $_GPC;
$do = 'deliveryer';
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'account';

$config = sys_delivery_config();
if($op == 'account') {
	$title = '配送员账户';
	$condition = ' WHERE uniacid = :aid';
	$params[':aid'] = $_W['uniacid'];

	$pindex = max(1, intval($_GPC['page']));
	$psize = 15;

	$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('tiny_wmall_deliveryer') .  $condition, $params);
	$data = pdo_fetchall('SELECT * FROM ' . tablename('tiny_wmall_deliveryer') . $condition . ' ORDER BY id DESC LIMIT '.($pindex - 1) * $psize.','.$psize, $params);

	$pager = pagination($total, $pindex, $psize);
	include $this->template('plateform/deliveryer');
}

if($op == 'post') {
	$title = '编辑配送员';
	$id = intval($_GPC['id']);
	$deliveryer = pdo_get('tiny_wmall_deliveryer', array('uniacid' => $_W['uniacid'], 'id' => $id));
	if($_W['isajax']) {
		$mobile = trim($_GPC['mobile']);
		$is_exist = pdo_fetchcolumn('select id from ' . tablename('tiny_wmall_deliveryer') . ' where uniacid = :uniacid and mobile = :mobile and id != :id', array(':uniacid' => $_W['uniacid'], ':mobile' => $mobile, ':id' => $id));
		if(!empty($is_exist)) {
			message(error(-1, '该手机号已绑定其他配送员, 请更换手机号'), '', 'ajax');
		}
		$openid = trim($_GPC['openid']);
		$is_exist = pdo_fetchcolumn('select id from ' . tablename('tiny_wmall_deliveryer') . ' where uniacid = :uniacid and openid = :openid and id != :id', array(':uniacid' => $_W['uniacid'], ':openid' => $openid, ':id' => $id));
		if(!empty($is_exist)) {
			message(error(-1, '该微信信息已绑定其他配送员, 请更换微信信息'), '', 'ajax');
		}

		$data = array(
			'uniacid' => $_W['uniacid'],
			'mobile' => $mobile,
			'title' => trim($_GPC['title']),
			'openid' => $openid,
			'nickname' => trim($_GPC['nickname']),
			'avatar' => trim($_GPC['avatar']),
			'sex' => trim($_GPC['sex']),
			'age' => intval($_GPC['age']),
		);
		if(!$id) {
			$data['password'] = trim($_GPC['password']) ? trim($_GPC['password']) : message(error(-1, '登陆密码不能为空'), '', 'ajax');
			$data['salt'] = random(6);
			$data['password'] = md5(md5($data['salt'] . $data['password']) . $data['salt']);
			$data['addtime'] = TIMESTAMP;
			pdo_insert('tiny_wmall_deliveryer', $data);
			deliveryer_all(true);
			message(error(0, '添加配送员成功'), '', 'ajax');
		} else {
			$password = trim($_GPC['password']);
			if(!empty($password)) {
				$data['salt'] = random(6);
				$data['password'] = md5(md5($data['salt'].$password) . $data['salt']);
			}
			pdo_update('tiny_wmall_deliveryer', $data, array('uniacid' => $_W['uniacid'], 'id' => $id));
			deliveryer_all(true);
			message(error(0, '编辑配送员成功'), '', 'ajax');
		}
	}
	include $this->template('plateform/deliveryer');
}

if($op == 'del') {
	$id = intval($_GPC['id']);
	pdo_delete('tiny_wmall_deliveryer', array('uniacid' => $_W['uniacid'], 'id' => $id));
	//删除配送员的其他数据
	pdo_delete('tiny_wmall_store_deliveryer', array('uniacid' => $_W['uniacid'], 'deliveryer_id' => $id));
	pdo_delete('tiny_wmall_deliveryer_current_log', array('uniacid' => $_W['uniacid'], 'deliveryer_id' => $id));
	pdo_delete('tiny_wmall_deliveryer_getcash_log', array('uniacid' => $_W['uniacid'], 'deliveryer_id' => $id));
	deliveryer_all(true);
	message('删除配送员成功', referer(), 'success');
}

if($op == 'list') {
	$title = '平台配送员';
	$condition = ' WHERE uniacid = :uniacid and sid = 0';
	$params[':uniacid'] = $_W['uniacid'];

	$pindex = max(1, intval($_GPC['page']));
	$psize = 15;

	$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('tiny_wmall_store_deliveryer') .  $condition, $params);
	$data = pdo_fetchall('SELECT * FROM ' . tablename('tiny_wmall_store_deliveryer') . $condition . ' ORDER BY id DESC LIMIT '.($pindex - 1) * $psize.','.$psize, $params);
	if(!empty($data)) {
		$deliveryers = pdo_getall('tiny_wmall_deliveryer', array('uniacid' => $_W['uniacid']), array(), 'id');
		foreach($data as &$da) {
			$da['deliveryer'] = $deliveryers[$da['deliveryer_id']];
			$da['stores'] = pdo_fetchall('select sid from ' . tablename('tiny_wmall_store_deliveryer') . ' where uniacid = :uniacid and deliveryer_id = :deliveryer_id and sid > 0', array(':uniacid' => $_W['uniacid'], ':deliveryer_id' => $da['deliveryer_id']));
		}
		$stores = pdo_getall('tiny_wmall_store', array('uniacid' => $_W['uniacid']), array('id', 'title'), 'id');
	}
	$pager = pagination($total, $pindex, $psize);
	include $this->template('plateform/deliveryer');
}

if($op == 'account_turncate') {
	if(!$_W['isajax']) {
		return false;
	}
	if(empty($_GPC['ids'])) {
		message(error(-1, '请选择要操作的账户'), '', 'ajax');
	}
	$remark = trim($_GPC['remark']);
	foreach($_GPC['ids'] as $id) {
		$id = intval($id);
		if(!$id) continue;
		$account = pdo_get('tiny_wmall_deliveryer', array('uniacid' => $_W['uniacid'], 'id' => $id));
		if(empty($account) || empty($account['credit2']) || $account['credit2'] == 0) {
			continue;
		}
		deliveryer_update_credit2($id, -$account['credit2'], 3, '', $remark);
	}
	message(error(0, ''), '', 'ajax');
}

if($op == 'add_ptf_deliveryer') {
	if($_W['isajax']) {
		$mobile = trim($_GPC['mobile']);
		if(empty($mobile)) {
			message(error(-1, '手机号不能为空'), '', 'ajax');
		}
		$deliveryer = pdo_get('tiny_wmall_deliveryer', array('uniacid' => $_W['uniacid'], 'mobile' => $mobile));
		if(empty($deliveryer)) {
			message(error(-1, '未找到该手机号对应的配送员'), '', 'ajax');
		}
		$is_exist = pdo_get('tiny_wmall_store_deliveryer', array('uniacid' => $_W['uniacid'], 'deliveryer_id' => $deliveryer['id'], 'sid' => 0));
		if(!empty($is_exist)) {
			message(error(-1, '该手机号对用的配送员已经是平台配送员, 请勿重复添加'), '', 'ajax');
		}
		$data = array(
			'uniacid' => $_W['uniacid'],
			'sid' => 0,
			'deliveryer_id' => $deliveryer['id'],
			'delivery_type' => 2,
			'addtime' => TIMESTAMP,
		);
		pdo_insert('tiny_wmall_store_deliveryer', $data);
		message(error(0, '添加平台配送员成功'), '', 'ajax');
	}
}

if($op == 'del_ptf_deliveryer') {
	$id = intval($_GPC['id']);
	pdo_delete('tiny_wmall_store_deliveryer', array('uniacid' => $_W['uniacid'], 'sid' => 0, 'id' => $id));
	message('取消配送员平台配送权限成功', referer(), 'success');
}

if($op == 'inout') {
	$title = '收支明细';
	$condition = ' WHERE uniacid = :uniacid';
	$params[':uniacid'] = $_W['uniacid'];
	$deliveryer_id = intval($_GPC['deliveryer_id']);
	if($deliveryer_id > 0) {
		$condition .= ' AND deliveryer_id = :deliveryer_id';
		$params[':deliveryer_id'] = $deliveryer_id;
	}
	$trade_type = intval($_GPC['trade_type']);
	if($trade_type > 0) {
		$condition .= ' and trade_type = :trade_type';
		$params[':trade_type'] = $trade_type;
	}
	if(!empty($_GPC['addtime'])) {
		$starttime = strtotime($_GPC['addtime']['start']);
		$endtime = strtotime($_GPC['addtime']['end']) + 86399;
	} else {
		$today = strtotime(date('Y-m-d'));
		$starttime = strtotime('-15 day', $today);
		$endtime = $today + 86399;
	}
	$condition .= " AND addtime > :start AND addtime < :end";
	$params[':start'] = $starttime;
	$params[':end'] = $endtime;

	$pindex = max(1, intval($_GPC['page']));
	$psize = 15;

	$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('tiny_wmall_deliveryer_current_log') .  $condition, $params);
	$records = pdo_fetchall('SELECT * FROM ' . tablename('tiny_wmall_deliveryer_current_log') . $condition . ' ORDER BY id DESC LIMIT '.($pindex - 1) * $psize.','.$psize, $params);
	$order_trade_type = order_trade_type();
	$pager = pagination($total, $pindex, $psize);
	$deliveryers = deliveryer_all(true);
	include $this->template('plateform/deliveryer');
}

if($op == 'stat') {
	$id = intval($_GPC['id']);
	$deliveryer = deliveryer_fetch($id);
	if(empty($deliveryer)) {
		message('配送员不存在', referer(), 'error');
	}

	$start = $_GPC['start'] ? strtotime($_GPC['start']) : strtotime(date('Y-m'));
	$end= $_GPC['end'] ? strtotime($_GPC['end']) + 86399 : (strtotime(date('Y-m-d')) + 86399);
	$day_num = ($end - $start) / 86400;
	if($_W['isajax'] && $_W['ispost']) {
		$days = array();
		$datasets = array(
			'flow1' => array(),
		);
		for($i = 0; $i < $day_num; $i++){
			$key = date('m-d', $start + 86400 * $i);
			$days[$key] = 0;
			$datasets['flow1'][$key] = 0;
		}
		$data = pdo_fetchall("SELECT * FROM " . tablename('tiny_wmall_order') . 'WHERE uniacid = :uniacid AND deliveryer_id = :deliveryer_id AND delivery_type = 2 and status = 5', array(':uniacid' => $_W['uniacid'], ':deliveryer_id' => $id));
		foreach($data as $da) {
			$key = date('m-d', $da['addtime']);
			if(in_array($key, array_keys($days))) {
				$datasets['flow1'][$key]++;
			}
		}
		$shuju['label'] = array_keys($days);
		$shuju['datasets'] = $datasets;
		exit(json_encode($shuju));
	}
	$stat = deliveryer_plateform_order_stat($id);
	include $this->template('plateform/deliveryer');
}

if($op == 'getcashlog') {
	$condition = ' WHERE uniacid = :aid';
	$params[':aid'] = $_W['uniacid'];

	$deliveryer_id = intval($_GPC['deliveryer_id']);
	if($deliveryer_id > 0) {
		$condition .= ' AND deliveryer_id = :deliveryer_id';
		$params[':deliveryer_id'] = $deliveryer_id;
	}
	$status = intval($_GPC['status']);
	if($status > 0) {
		$condition .= ' AND status = :status';
		$params[':status'] = $status;
	}
	if(!empty($_GPC['addtime'])) {
		$starttime = strtotime($_GPC['addtime']['start']);
		$endtime = strtotime($_GPC['addtime']['end']) + 86399;
	} else {
		$today = strtotime(date('Y-m-d'));
		$starttime = strtotime('-15 day', $today);
		$endtime = $today + 86399;
	}
	$condition .= " AND addtime > :start AND addtime < :end";
	$params[':start'] = $starttime;
	$params[':end'] = $endtime;

	$pindex = max(1, intval($_GPC['page']));
	$psize = 15;

	$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('tiny_wmall_deliveryer_getcash_log') .  $condition, $params);
	$records = pdo_fetchall('SELECT * FROM ' . tablename('tiny_wmall_deliveryer_getcash_log') . $condition . ' ORDER BY id DESC LIMIT '.($pindex - 1) * $psize.','.$psize, $params);
	$pager = pagination($total, $pindex, $psize);
	$deliveryers = deliveryer_all(true);
	include $this->template('plateform/deliveryer');
}

if($op == 'transfers') {
	$id = intval($_GPC['id']);
	$log = pdo_get('tiny_wmall_deliveryer_getcash_log', array('uniacid' => $_W['uniacid'], 'id' => $id));
	if(empty($log)) {
		message('提现记录不存在', referer(), 'error');
	}
	if($log['status'] == 1) {
		message('该提现记录已处理', referer(), 'error');
	}
	$deliveryer = pdo_get('tiny_wmall_deliveryer', array('uniacid' => $_W['uniacid'], 'id' => $log['deliveryer_id']));
	if(empty($deliveryer) || empty($deliveryer['title']) || empty($deliveryer['openid'])) {
		message('配送员微信信息不完善,无法进行微信付款', referer(), 'error');
	}
	mload()->classs('wxpay');
	$pay = new WxPay();
	$params = array(
		'partner_trade_no' => $log['trade_no'],
		'openid' => $deliveryer['openid'],
		'check_name' => 'FORCE_CHECK',
		're_user_name' => $deliveryer['title'],
		'amount' => $log['final_fee'] * 100,
		'desc' => "{$deliveryer['title']}" . date('Y-m-d H:i') . "配送费提现申请"
	);
	$response = $pay->mktTransfers($params);
	if(is_error($response)) {
		message($response['message'], referer(), 'error');
	}
	sys_notice_deliveryer_getcash($log['deliveryer_id'], $id, 'success');
	pdo_update('tiny_wmall_deliveryer_getcash_log', array('status' => 1, 'endtime' => TIMESTAMP), array('uniacid' => $_W['uniacid'], 'id' => $id));
	message('打款成功', referer(), 'success');
}








