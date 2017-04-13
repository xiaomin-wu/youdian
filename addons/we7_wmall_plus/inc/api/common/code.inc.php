<?php
/**
 * 外送系统
 * @author 新睿社区
 * @QQ 10373458 * @url http://www.010xr.com/
 */
defined('IN_IA') or exit('Access Denied');
mload()->model('sms');
global $_W, $_GPC;
$do = 'code';
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'index';

$sid = intval($_GPC['sid']);
$mobile = trim($_GPC['mobile']);
if($mobile == ''){
	message(ierror(-1, '请输入手机号'), '', 'ajax');
}

if(!preg_match(REGULAR_MOBILE, $mobile)) {
	message(ierror(-1, '手机号格式错误'), '', 'ajax');
}

$sql = 'DELETE FROM ' . tablename('uni_verifycode') . ' WHERE `createtime`<' . (TIMESTAMP - 1800);
pdo_query($sql);

$sql = 'SELECT * FROM ' . tablename('uni_verifycode') . ' WHERE `receiver`=:receiver AND `uniacid`=:uniacid';
$pars = array();
$pars[':receiver'] = $mobile;
$pars[':uniacid'] = $_W['uniacid'];
$row = pdo_fetch($sql, $pars);
$record = array();
if(!empty($row)) {
	if($row['total'] >= 5) {
		message(ierror(-1, '您的操作过于频繁, 请稍后再试'), '', 'ajax');
	}
	$code = $row['verifycode'];
	$record['total'] = $row['total'] + 1;
} else {
	$code = random(6, true);
	$record['uniacid'] = $_W['uniacid'];
	$record['receiver'] = $mobile;
	$record['verifycode'] = $code;
	$record['total'] = 1;
	$record['createtime'] = TIMESTAMP;
}
if(!empty($row)) {
	pdo_update('uni_verifycode', $record, array('id' => $row['id']));
} else {
	pdo_insert('uni_verifycode', $record);
}

$type = trim($_GPC['type']);
$routers = array(
	'login' => "{$_W['we7_wmall_plus']['config']['title']}用户登录操作"
);
$product = !empty($routers[$type]) ? $routers[$type] : trim($_GPC['product']);
$content = array(
	'code' => $code,
	'product' => $product
);
$result = sms_send('verify_code_tpl', $mobile, $content, $sid);
if(is_error($result)) {
	message(ierror(-1, $result['message']), '', 'ajax');
}
message(ierror(0, '验证码发送成功'), '', 'ajax');