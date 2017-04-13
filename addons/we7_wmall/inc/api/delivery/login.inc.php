<?php
/**
 * 外送系统
 * @author 灯火阑珊
 * @QQ 2471240272
 * @url http://bbs.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC, $_POST;
$mobile = trim($_POST['mobile']);
$password = trim($_POST['password']);
if(empty($mobile) || empty($password)) {
	message(ierror(-1, '手机号或密码为空'), '', 'ajax');
}
$deliveryer = deliveryer_login($mobile, $password);
message($deliveryer, '', 'ajax');

