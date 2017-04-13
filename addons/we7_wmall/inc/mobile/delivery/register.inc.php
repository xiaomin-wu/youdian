<?php
/**
 * 外送系统
 * @author 灯火阑珊
 * @QQ 2471240272
 * @url http://bbs.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$fans = mc_oauth_userinfo();
$delivery = pdo_get('tiny_wmall_deliveryer', array('uniacid' => $_W['uniacid'], 'openid' => $fans['openid']));
if(!empty($delivery)) {
	$this->imessage('您已申请过配送员了', $this->createMobileUrl('dyorder'), 'success', '现在去抢单', '去抢单');
}

if($_W['isajax']) {
	$user = array(
		'uniacid' => $_W['uniacid'],
		'openid' => trim($_GPC['openid']) ? trim($_GPC['openid']) : message(error(-1, '获取微信信息失败'), '', 'ajax'),
		'password' => trim($_GPC['password']) ? trim($_GPC['password']) : message(error(-1, '密码不能为空'), '', 'ajax'),
		'mobile' => trim($_GPC['mobile']) ? trim($_GPC['mobile']) : message(error(-1, '手机号不能为空'), '', 'ajax'),
		'title' => trim($_GPC['title']) ? trim($_GPC['title']) : message(error(-1, '真实姓名不能为空'), '', 'ajax'),
		'nickname' => trim($_GPC['nickname']),
		'avatar' => trim($_GPC['avatar']),
		'sex' => trim($_GPC['sex']),
		'age' => intval($_GPC['age']),
		'addtime' => TIMESTAMP
	);
	if($settle_config['mobile_verify_status'] == 1) {
		$code = trim($_GPC['code']);
		$status = check_verifycode($user['mobile'], $code);
		if(!$status) {
			message(error(-1, '验证码错误'), '', 'ajax');
		}
	}
	$is_exist = pdo_fetchcolumn('select id from ' . tablename('tiny_wmall_deliveryer') . ' where uniacid = :uniacid and mobile = :mobile', array(':uniacid' => $_W['uniacid'], ':mobile' => $user['mobile']));
	if(!empty($is_exist)) {
		message(error(-1, '该手机号已绑定其他配送员, 请更换手机号'), '', 'ajax');
	}
	$is_exist = pdo_fetchcolumn('select id from ' . tablename('tiny_wmall_deliveryer') . ' where uniacid = :uniacid and openid = :openid', array(':uniacid' => $_W['uniacid'], ':openid' => $user['openid']));
	if(!empty($is_exist)) {
		message(error(-1, '该微信号已绑定其他配送员, 请更换微信号'), '', 'ajax');
	}
	$user['salt'] = random(6);
	$user['password'] = md5(md5($user['salt'] . $user['password']) . $user['salt']);
	pdo_insert('tiny_wmall_deliveryer', $user);
	deliveryer_all(true);
	message(error(0, '申请配送员成功'), '', 'ajax');

}

include $this->template('delivery/register');