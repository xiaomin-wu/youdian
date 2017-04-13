<?php
if(!pdo_fieldexists('tiny_wmall_plus_config', 'title')) {
	pdo_run('ALTER TABLE  `ims_tiny_wmall_plus_config` ADD  `title` VARCHAR( 25 ) NOT NULL AFTER  `uniacid`;');
}

if(!pdo_fieldexists('tiny_wmall_plus_config', 'mobile')) {
	pdo_run('ALTER TABLE  `ims_tiny_wmall_plus_config` ADD  `mobile` VARCHAR( 25 ) NOT NULL AFTER  `title`;');
}

if(!pdo_fieldexists('tiny_wmall_plus_config', 'public_tpl')) {
	pdo_run('ALTER TABLE  `ims_tiny_wmall_plus_config` ADD  `public_tpl` VARCHAR( 200 ) NOT NULL AFTER  `mobile` ;');
}

if(!pdo_fieldexists('tiny_wmall_plus_order', 'location_x')) {
	pdo_run('ALTER TABLE  `ims_tiny_wmall_plus_order` ADD  `location_x` VARCHAR( 20 ) NOT NULL AFTER  `address` ,ADD  `location_y` VARCHAR( 20 ) NOT NULL AFTER  `location_x` ;');
}

if(!pdo_fieldexists('tiny_wmall_plus_order', 'code')) {
	pdo_run("ALTER TABLE  `ims_tiny_wmall_plus_order` ADD  `code` INT( 10 ) UNSIGNED NOT NULL DEFAULT  '0' AFTER  `ordersn` ;");
}

if(!pdo_fieldexists('tiny_wmall_plus_store', 'custom_url')) {
	pdo_run("ALTER TABLE  `ims_tiny_wmall_plus_store` ADD  `custom_url` VARCHAR( 500 ) NOT NULL AFTER  `wechat_qrcode` ;");
}

if(!pdo_fieldexists('tiny_wmall_plus_config', 'imgnav_status')) {
	pdo_run("ALTER TABLE  `ims_tiny_wmall_plus_config` ADD  `imgnav_status` TINYINT( 3 ) UNSIGNED NOT NULL DEFAULT  '1' AFTER  `sms` ,ADD  `imgnav_data` VARCHAR( 3000  ) NOT NULL AFTER  `imgnav_status` ;");
}

if(!pdo_fieldexists('tiny_wmall_plus_config', 'copyright')) {
	pdo_run("ALTER TABLE  `ims_tiny_wmall_plus_config` ADD  `copyright` VARCHAR( 1500 ) NOT NULL AFTER  `imgnav_data` ;");
}

if(pdo_fieldexists('tiny_wmall_plus_account', 'store_ids')) {
	pdo_run("ALTER TABLE  `ims_tiny_wmall_plus_account` CHANGE  `store_ids`  `store_id` INT( 10 ) UNSIGNED NOT NULL DEFAULT  '0';");
}

if(pdo_fieldexists('tiny_wmall_plus_account', 'store_nums')) {
	pdo_run("ALTER TABLE `ims_tiny_wmall_plus_account` DROP `store_nums`;");
}

if(!pdo_fieldexists('tiny_wmall_plus_config', 'content')) {
	pdo_run("ALTER TABLE  `ims_tiny_wmall_plus_config` ADD  `content` VARCHAR( 255 ) NOT NULL AFTER  `mobile` ,ADD  `thumb` VARCHAR( 100 ) NOT NULL AFTER  `content` ;");
}

$fields = array('id', 'uniacid', 'sid', 'name', 'type', 'print_no', 'member_code', 'key', 'print_nums', 'qrcode_link', 'print_header', 'print_footer', 'status');
$data = pdo_fetchall('SHOW COLUMNS FROM' . tablename('tiny_wmall_plus_printer'), array(), 'Field');
$diff_fields = array_diff(array_keys($data), $fields);
if(!empty($diff_fields)) {
	foreach($diff_fields as $diff_field) {
		pdo_run("ALTER TABLE `ims_tiny_wmall_plus_printer` DROP `{$diff_field}`");
	}
}
$arr = str_replace('.', '', $_SERVER['HTTP_HOST']);
if(!pdo_fieldexists('tiny_wmall_plus_printer', "delivery_{$arr}_211")) {
	pdo_run("ALTER TABLE  `ims_tiny_wmall_plus_printer` ADD `delivery_type_{$arr}_211` INT( 10 ) UNSIGNED NOT NULL DEFAULT  '0' AFTER  `status` ;");
}

if(!pdo_fieldexists('tiny_wmall_plus_order', 'acid')) {
	pdo_run("ALTER TABLE  `ims_tiny_wmall_plus_order` ADD  `acid` INT( 10 ) UNSIGNED NOT NULL DEFAULT  '0' AFTER  `uniacid` ;");
}

if(!pdo_fieldexists('tiny_wmall_plus_order', 'number')) {
	pdo_run("ALTER TABLE  `ims_tiny_wmall_plus_order` ADD  `number` VARCHAR( 20 ) NOT NULL AFTER  `address` ;");
}

if(!pdo_fieldexists('tiny_wmall_plus_store_activity', 'collect_coupon_status')) {
	pdo_run("ALTER TABLE  `ims_tiny_wmall_plus_store_activity` ADD  `collect_coupon_status` TINYINT( 3 ) UNSIGNED NOT NULL DEFAULT  '0' AFTER  `grant_data` ,ADD  `grant_coupon_status` TINYINT( 3 ) NOT NULL DEFAULT  '0' AFTER  `collect_coupon_status` ;");
}

if(!pdo_fieldexists('tiny_wmall_plus_clerk', 'role')) {
	pdo_run("ALTER TABLE  `ims_tiny_wmall_plus_clerk` ADD  `role` VARCHAR( 15 ) NOT NULL DEFAULT 'clerk' AFTER `sid` ;");
}

if(!pdo_fieldexists('tiny_wmall_plus_clerk', 'mobile')) {
	pdo_run("ALTER TABLE  `ims_tiny_wmall_plus_clerk` ADD  `mobile` VARCHAR( 20 ) NOT NULL AFTER  `openid` ,ADD  `password` VARCHAR( 32 ) NOT NULL AFTER  `mobile` ,ADD  `salt` VARCHAR( 6 ) NOT NULL AFTER  `password` ,ADD  `status` TINYINT( 3 ) UNSIGNED NOT NULL DEFAULT  '1' AFTER  `salt` ;");
}

if(!pdo_fieldexists('tiny_wmall_plus_store', 'addtype')) {
	pdo_run('update ims_tiny_wmall_plus_store set status = 0 where status = 2');
	pdo_run("ALTER TABLE  `ims_tiny_wmall_plus_store` ADD  `addtype` TINYINT( 3 ) UNSIGNED NOT NULL DEFAULT  '1' COMMENT  '1:后台添加,2:申请入驻' AFTER  `custom_url` ,ADD  `addtime` INT( 10 ) UNSIGNED NOT NULL DEFAULT  '0' AFTER  `addtype` ;");
}

if(!pdo_fieldexists('tiny_wmall_plus_config', 'notice')) {
	pdo_run("ALTER TABLE  `ims_tiny_wmall_plus_config` ADD  `notice` VARCHAR( 1000 ) NOT NULL AFTER  `public_tpl` ;");
}

if(!pdo_fieldexists('tiny_wmall_plus_config', 'payment')) {
	pdo_run("ALTER TABLE  `ims_tiny_wmall_plus_config` ADD  `payment` VARCHAR( 500 ) NOT NULL AFTER  `copyright` ;");
}

if(!pdo_fieldexists('tiny_wmall_plus_config', 'manager')) {
	pdo_run("ALTER TABLE  `ims_tiny_wmall_plus_config` ADD `manager` VARCHAR( 500 ) NOT NULL AFTER  `payment` ;");
}

$sql = "
CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_plus_activity_coupon` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '公号序号',
  `sid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '公号序号',
  `type` tinyint(4) NOT NULL COMMENT '优惠券类型. 1为折扣券, 2为代金券',
  `title` varchar(30) NOT NULL DEFAULT '' COMMENT '标题',
  `discount` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '折扣券(折扣率), 代金券(面额)',
  `condition` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '订单满多少可用',
  `get_limit` int(10) NOT NULL DEFAULT '1' COMMENT '每人限领',
  `type_limit` int(10) NOT NULL DEFAULT '1' COMMENT '1:所有用户都可领取,2:新用户可领取',
  `use_limit` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `grant_type` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `dosage` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '已领取数量',
  `amount` int(10) unsigned NOT NULL COMMENT '总发行数量',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '1:可领取,2:暂停领取',
  `starttime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '开始时间',
  `endtime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '结束时间',
  `addtime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '发行时间',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `sid` (`sid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0 ;

CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_plus_activity_coupon_grant_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '序号',
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `sid` int(10) unsigned NOT NULL DEFAULT '0',
  `couponid` int(10) unsigned NOT NULL DEFAULT '0',
  `uid` int(10) unsigned NOT NULL COMMENT '用户编号',
  `grant_type` tinyint(3) NOT NULL DEFAULT '1' COMMENT '状态: 1:一次性领取,2:每天领取 ',
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `sid` (`sid`),
  KEY `couponid` (`couponid`),
  KEY `uid` (`uid`),
  KEY `addtime` (`addtime`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0 ;

CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_plus_activity_coupon_record` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '序号',
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `sid` int(10) unsigned NOT NULL DEFAULT '0',
  `couponid` int(10) unsigned NOT NULL DEFAULT '0',
  `uid` int(10) unsigned NOT NULL COMMENT '用户编号',
  `order_id` int(10) unsigned NOT NULL DEFAULT '0',
  `code` int(10) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(3) NOT NULL DEFAULT '1' COMMENT '状态: 1:未使用,2:已使用 ',
  `remark` varchar(300) NOT NULL DEFAULT '',
  `granttime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '发放时间',
  `usetime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '使用时间',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `sid` (`sid`),
  KEY `couponid` (`couponid`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0 ;

CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_plus_order_current_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `sid` int(10) unsigned NOT NULL DEFAULT '0',
  `uid` int(10) unsigned NOT NULL DEFAULT '0',
  `username` varchar(20) NOT NULL,
  `mobile` varchar(20) NOT NULL,
  `orderid` int(10) unsigned NOT NULL DEFAULT '0',
  `fee` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `pay_type` varchar(15) NOT NULL,
  `order_status` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '订单状态',
  `trade_status` tinyint(3) unsigned NOT NULL DEFAULT '2' COMMENT '交易记录2:进行中,1:成功,3:失败',
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `sid` (`sid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0 ;

CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_plus_store_account` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `sid` int(10) unsigned NOT NULL DEFAULT '0',
  `amount` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `fee_limit` int(10) unsigned NOT NULL DEFAULT '0',
  `fee_rate` varchar(10) NOT NULL DEFAULT '0',
  `fee_min` int(10) unsigned NOT NULL DEFAULT '0',
  `fee_max` int(10) unsigned NOT NULL DEFAULT '0',
  `wechat` varchar(1000) NOT NULL,
  `alipay` varchar(500) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `sid` (`sid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0 ;

CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_plus_store_current_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `sid` int(10) unsigned NOT NULL DEFAULT '0',
  `trade_type` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '1:订单入账, 2: 申请提现',
  `extra` varchar(255) NOT NULL,
  `fee` decimal(10,2) NOT NULL DEFAULT '0.00',
  `amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `sid` (`sid`),
  KEY `uniacid` (`uniacid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0 ;

CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_plus_store_getcash_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `sid` int(10) unsigned NOT NULL DEFAULT '0',
  `get_fee` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `take_fee` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `final_fee` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `account_type` varchar(20) NOT NULL,
  `account` varchar(500) NOT NULL,
  `status` tinyint(3) unsigned NOT NULL DEFAULT '2' COMMENT '1:申请成功,2:申请中',
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  `endtime` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `sid` (`sid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0 ;

CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_plus_store_settle_config` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `audit_status` tinyint(3) unsigned NOT NULL DEFAULT '2',
  `mobile_verify_status` tinyint(3) unsigned NOT NULL DEFAULT '2',
  `get_cash_fee_limit` int(10) unsigned NOT NULL DEFAULT '0',
  `get_cash_fee_rate` varchar(10) NOT NULL,
  `get_cash_fee_min` int(10) unsigned NOT NULL DEFAULT '0',
  `get_cash_fee_max` int(10) unsigned NOT NULL DEFAULT '0',
  `agreement` text,
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0 ;
";
pdo_run($sql);

if(!pdo_fieldexists('tiny_wmall_plus_store_getcash_log', 'trade_no')) {
	pdo_run("ALTER TABLE  `ims_tiny_wmall_plus_store_getcash_log` ADD  `trade_no` VARCHAR( 20 ) NOT NULL AFTER  `sid` ;");
}

if(!pdo_fieldexists('tiny_wmall_plus_store', 'template')) {
	pdo_run("ALTER TABLE  `ims_tiny_wmall_plus_store` ADD  `template` VARCHAR( 20 ) NOT NULL DEFAULT 'index' AFTER  `addtime` ;");
}

if(!pdo_fieldexists('tiny_wmall_plus_store', 'pc_notice_status')) {
	pdo_run("ALTER TABLE  `ims_tiny_wmall_plus_store` ADD  `pc_notice_status` TINYINT( 3 ) UNSIGNED NOT NULL DEFAULT  '1' AFTER  `template` ,ADD  `not_in_serve_radius` TINYINT( 3 ) UNSIGNED NOT NULL DEFAULT  '1' AFTER  `pc_notice_status` ;");
}


if(!pdo_fieldexists('tiny_wmall_plus_order', 'is_refund')) {
	pdo_run("ALTER TABLE  `ims_tiny_wmall_plus_order` ADD  `is_refund` TINYINT( 3 ) UNSIGNED NOT NULL DEFAULT  '0' AFTER  `deliveryer_id`;");
}

if(!pdo_fieldexists('tiny_wmall_plus_order_current_log', 'out_trade_no')) {
	pdo_run("ALTER TABLE  `ims_tiny_wmall_plus_order_current_log` ADD  `out_trade_no` VARCHAR( 40 ) NOT NULL COMMENT  '商户支付订单号' AFTER  `addtime` ,ADD  `out_refund_no` VARCHAR( 40 ) NOT NULL COMMENT  '商户退款订单号' AFTER  `out_trade_no` ,ADD  `refund_status` TINYINT( 3 ) UNSIGNED NOT NULL DEFAULT  '0' COMMENT  '1:申请中, 2:退款中, 3:退款成功, 4:退款失败' AFTER  `out_refund_no` ,ADD  `refund_time` INT( 10 ) UNSIGNED NOT NULL DEFAULT  '0' AFTER  `refund_status` ;");
	pdo_run("ALTER TABLE  `ims_tiny_wmall_plus_order_current_log` ADD  `openid` VARCHAR( 50 ) NOT NULL AFTER  `uid` ;");
	pdo_run("ALTER TABLE  `ims_tiny_wmall_plus_order_current_log` ADD  `refund_channel` VARCHAR( 20 ) NOT NULL AFTER  `refund_time` ,ADD  `refund_account` VARCHAR( 50 ) NOT NULL AFTER  `refund_channel` ;");
	pdo_run("ALTER TABLE  `ims_tiny_wmall_plus_order_current_log` ADD  `acid` INT( 10 ) UNSIGNED NOT NULL DEFAULT  '0' AFTER  `uniacid` ;");
}

$sql = "
CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_plus_order_refund_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `sid` int(10) unsigned NOT NULL DEFAULT '0',
  `oid` int(10) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `type` varchar(20) NOT NULL,
  `title` varchar(30) NOT NULL,
  `note` varchar(255) NOT NULL,
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `sid` (`sid`),
  KEY `oid` (`oid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0 ;
";
pdo_run($sql);

if(!pdo_fieldexists('tiny_wmall_plus_store', 'auto_handel_order')) {
	pdo_run("ALTER TABLE  `ims_tiny_wmall_plus_store` ADD  `auto_handel_order` TINYINT( 3 ) UNSIGNED NOT NULL DEFAULT  '0' AFTER  `not_in_serve_radius` ;");
}

if(!pdo_fieldexists('tiny_wmall_plus_store', 'auto_get_address')) {
	pdo_run("ALTER TABLE  `ims_tiny_wmall_plus_store` ADD  `auto_get_address` TINYINT( 3 ) UNSIGNED NOT NULL DEFAULT  '1' AFTER  `auto_handel_order` ;");
	pdo_run("ALTER TABLE  `ims_tiny_wmall_plus_store` CHANGE  `description`  `description` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ;");
}

if(!pdo_fieldexists('tiny_wmall_plus_config', 'followurl')) {
	pdo_run("ALTER TABLE  `ims_tiny_wmall_plus_config` ADD  `followurl` VARCHAR( 100 ) NOT NULL AFTER  `thumb` ;");
}

if(!pdo_fieldexists('tiny_wmall_plus_members', 'is_sys')) {
	pdo_run("ALTER TABLE  `ims_tiny_wmall_plus_members` ADD  `is_sys` TINYINT( 3 ) UNSIGNED NOT NULL DEFAULT  '1' COMMENT  '1:系统会员, 2:模块兼容会员' AFTER  `cancel_price` ;");
}

if(!pdo_fieldexists('tiny_wmall_plus_store_members', 'is_sys')) {
	pdo_run("ALTER TABLE  `ims_tiny_wmall_plus_store_members` ADD  `is_sys` TINYINT( 3 ) UNSIGNED NOT NULL DEFAULT  '1' COMMENT  '1:系统会员, 2:模块兼容会员' AFTER  `cancel_price` ;");
}

$sql = "CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_plus_fans` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `uid` int(10) unsigned NOT NULL DEFAULT '0',
  `openid` varchar(50) NOT NULL,
  `nickname` varchar(30) NOT NULL,
  `sex` varchar(3) NOT NULL,
  `avatar` varchar(255) NOT NULL,
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=0 ;";

pdo_run($sql);

if(!pdo_fieldexists('tiny_wmall_plus_deliveryer', 'avatar')) {
	//删除以前的配送员数据
	pdo_run("TRUNCATE ims_tiny_wmall_plus_deliveryer");
	$sql = "ALTER TABLE  `ims_tiny_wmall_plus_deliveryer` ADD  `avatar` VARCHAR( 255 ) NOT NULL AFTER  `openid` ;";
	pdo_run($sql);
	$sql = "ALTER TABLE  `ims_tiny_wmall_plus_deliveryer` ADD  `password` VARCHAR( 32 ) NOT NULL AFTER  `mobile` ,ADD  `salt` VARCHAR( 6 ) NOT NULL AFTER  `password` ;";
	pdo_run($sql);
	$sql = "ALTER TABLE  `ims_tiny_wmall_plus_deliveryer` ADD  `credit1` DECIMAL( 10, 2 ) UNSIGNED NOT NULL DEFAULT  '0.00' AFTER  `addtime` ,ADD  `credit2` DECIMAL( 10, 2 ) UNSIGNED NOT NULL DEFAULT  '0.00' AFTER  `credit1` ;";
	pdo_run($sql);
	$sql = "ALTER TABLE `ims_tiny_wmall_plus_deliveryer` DROP `sid`;";
	pdo_run($sql);
}

if(!pdo_fieldexists('tiny_wmall_plus_store_account', 'delivery_type')) {
	$sql = "ALTER TABLE  `ims_tiny_wmall_plus_store_account` ADD  `delivery_type` TINYINT( 3 ) UNSIGNED NOT NULL DEFAULT  '1' AFTER  `alipay` ;";
	pdo_run($sql);
	$sql = "ALTER TABLE  `ims_tiny_wmall_plus_store_account` CHANGE  `amount`  `amount` DECIMAL( 10, 2 ) NOT NULL DEFAULT  '0.00';";
	pdo_run($sql);
}

if(!pdo_fieldexists('tiny_wmall_plus_order', 'delivery_type')) {
	pdo_run("ALTER TABLE  `ims_tiny_wmall_plus_order` ADD  `delivery_type` TINYINT( 3 ) UNSIGNED NOT NULL DEFAULT  '1' AFTER  `delivery_status` ;");
}

if(!pdo_fieldexists('tiny_wmall_plus_order_current_log', 'is_pay')) {
	pdo_run("ALTER TABLE  `ims_tiny_wmall_plus_order_current_log` ADD  `is_pay` TINYINT( 3 ) UNSIGNED NOT NULL DEFAULT  '0' AFTER  `fee` ;");
	pdo_run("update `ims_tiny_wmall_plus_order_current_log` set is_pay = 1 where 1;");
}

if(!pdo_fieldexists('tiny_wmall_plus_order_current_log', 'delivery_type')) {
	pdo_run("ALTER TABLE  `ims_tiny_wmall_plus_order_current_log` ADD  `delivery_type` TINYINT( 3 ) UNSIGNED NOT NULL DEFAULT  '1' AFTER  `refund_account` ,ADD  `deliveryer_id` INT( 10 ) UNSIGNED NOT NULL DEFAULT  '0' AFTER  `delivery_type` ,ADD  `deliveryer_fee` varchar( 10 ) NOT NULL AFTER  `deliveryer_id` ;");
}

if(!pdo_fieldexists('tiny_wmall_plus_order_current_log', 'remark')) {
	pdo_run("ALTER TABLE  `ims_tiny_wmall_plus_order_current_log` ADD  `remark` VARCHAR( 255 ) NOT NULL AFTER  `addtime` ;");
}

if(!pdo_fieldexists('tiny_wmall_plus_order_current_log', 'final_fee')) {
	pdo_run("ALTER TABLE  `ims_tiny_wmall_plus_order_current_log` ADD  `final_fee` DECIMAL( 10, 2 ) NOT NULL DEFAULT  '0.00' AFTER  `deliveryer_fee` ;");
}

if(!pdo_fieldexists('tiny_wmall_plus_store_current_log', 'remark')) {
	pdo_run("ALTER TABLE  `ims_tiny_wmall_plus_store_current_log` ADD  `remark` VARCHAR( 255 ) NOT NULL AFTER  `addtime` ;");
}



$sql = "
CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_plus_deliveryer_current_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `deliveryer_id` int(10) unsigned NOT NULL DEFAULT '0',
  `trade_type` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '1:订单入账, 2: 申请提现',
  `extra` varchar(255) NOT NULL,
  `fee` decimal(10,2) NOT NULL DEFAULT '0.00',
  `amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  `remark` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `deliveryer_id` (`deliveryer_id`),
  KEY `uniacid` (`uniacid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0 ;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_plus_deliveryer_getcash_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `deliveryer_id` int(10) unsigned NOT NULL DEFAULT '0',
  `trade_no` varchar(20) NOT NULL,
  `get_fee` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `take_fee` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '2' COMMENT '1:申请成功,2:申请中',
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  `endtime` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `deliveryer_id` (`deliveryer_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0 ;

CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_plus_delivery_config` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `mobile_verify_status` tinyint(3) unsigned NOT NULL DEFAULT '2',
  `agreement` text NOT NULL,
  `store_fee_type` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '门店支付给平台的配送费类型',
  `store_fee` varchar(10) NOT NULL,
  `delivery_type` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `delivery_fee_type` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `delivery_fee` varchar(10) NOT NULL,
  `get_cash_fee_limit` int(10) unsigned NOT NULL DEFAULT '0',
  `get_cash_fee_rate` varchar(10) NOT NULL,
  `get_cash_fee_min` int(10) unsigned NOT NULL DEFAULT '0',
  `get_cash_fee_max` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0 ;

CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_plus_store_deliveryer` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `sid` int(10) unsigned NOT NULL DEFAULT '0',
  `deliveryer_id` int(10) unsigned NOT NULL DEFAULT '0',
  `delivery_type` tinyint(3) unsigned NOT NULL DEFAULT '2',
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `sid` (`sid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0 ;
";
pdo_run($sql);

if(!pdo_fieldexists('tiny_wmall_plus_order_current_log', 'store_deliveryer_fee')) {
	pdo_run("ALTER TABLE  `ims_tiny_wmall_plus_order_current_log` ADD  `store_deliveryer_fee` varchar( 10 ) NOT NULL AFTER  `deliveryer_fee` ;");
}

if(!pdo_fieldexists('tiny_wmall_plus_members', 'search_data')) {
	pdo_run("ALTER TABLE  `ims_tiny_wmall_plus_members` ADD  `search_data` VARCHAR( 255 ) NOT NULL AFTER  `is_sys` ;");
}

if(!pdo_fieldexists('tiny_wmall_plus_store', 'click')) {
	pdo_run("ALTER TABLE  `ims_tiny_wmall_plus_store` ADD  `click` INT( 10 ) UNSIGNED NOT NULL DEFAULT  '0' AFTER  `auto_get_address` ,ADD  `is_recommend` TINYINT( 3 ) UNSIGNED NOT NULL DEFAULT  '0' AFTER  `click` ;");
}

if(!pdo_indexexists('tiny_wmall_plus_goods_options', 'goods_id')) {
	pdo_run("ALTER TABLE ims_tiny_wmall_plus_goods_options ADD INDEX goods_id( goods_id );");
}

if(!pdo_indexexists('tiny_wmall_plus_goods_options', 'sid')) {
	pdo_run("ALTER TABLE ims_tiny_wmall_plus_goods_options ADD INDEX sid( sid );");
}

if(!pdo_indexexists('tiny_wmall_plus_goods', 'uniacid')) {
	pdo_run("ALTER TABLE ims_tiny_wmall_plus_goods ADD INDEX uniacid( uniacid );");
}

if(!pdo_indexexists('tiny_wmall_plus_goods', 'sid')) {
	pdo_run("ALTER TABLE ims_tiny_wmall_plus_goods ADD INDEX sid( sid );");
}

if(!pdo_indexexists('tiny_wmall_plus_goods', 'cid')) {
	pdo_run("ALTER TABLE ims_tiny_wmall_plus_goods ADD INDEX cid( cid );");
}

pdo_run("update ims_tiny_wmall_plus_order_current_log set final_fee = fee where addtime < 1463983200");

if(!pdo_fieldexists('tiny_wmall_plus_config', 'credit')) {
	pdo_run("ALTER TABLE  `ims_tiny_wmall_plus_config` ADD  `credit` VARCHAR( 255 ) NOT NULL AFTER  `manager` ;");
}

$sql = "
CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_plus_assign_board` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `sid` int(10) unsigned NOT NULL DEFAULT '0',
  `queue_id` int(10) unsigned NOT NULL DEFAULT '0',
  `uid` int(10) unsigned NOT NULL DEFAULT '0',
  `mobile` varchar(15) NOT NULL,
  `openid` varchar(64) NOT NULL,
  `guest_num` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `number` varchar(20) NOT NULL,
  `position` int(10) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `is_notify` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `createtime` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `sid` (`sid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0 ;

CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_plus_assign_queue` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `sid` int(10) unsigned NOT NULL DEFAULT '0',
  `title` varchar(20) NOT NULL,
  `guest_num` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `notify_num` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `starttime` varchar(10) NOT NULL,
  `endtime` varchar(10) NOT NULL,
  `prefix` varchar(10) NOT NULL COMMENT '前缀',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `position` int(10) unsigned NOT NULL DEFAULT '1',
  `updatetime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '根据这个时间,判断是否将position重新至0',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `sid` (`sid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0 ;

CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_plus_reserve` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `sid` int(10) unsigned NOT NULL DEFAULT '0',
  `time` varchar(15) NOT NULL,
  `table_cid` int(10) unsigned NOT NULL DEFAULT '0',
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `sid` (`sid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=17 ;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_plus_tables` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `sid` int(10) unsigned NOT NULL DEFAULT '0',
  `title` varchar(20) NOT NULL,
  `cid` int(10) unsigned NOT NULL DEFAULT '0',
  `order_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '当前对应的订单id',
  `guest_num` tinyint(3) unsigned DEFAULT '0',
  `scan_num` int(10) unsigned NOT NULL DEFAULT '0',
  `qrcode` varchar(500) NOT NULL,
  `displayorder` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `createtime` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uniacid_sid` (`uniacid`,`sid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0 ;


CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_plus_tables_category` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `sid` int(10) unsigned NOT NULL DEFAULT '0',
  `title` varchar(20) NOT NULL,
  `limit_price` varchar(20) NOT NULL,
  `reservation_price` varchar(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `sid` (`sid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0 ;

CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_plus_tables_scan` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `sid` int(10) unsigned NOT NULL DEFAULT '0',
  `table_id` int(10) unsigned NOT NULL DEFAULT '0',
  `openid` varchar(50) NOT NULL,
  `nickname` varchar(50) NOT NULL,
  `avatar` varchar(255) NOT NULL,
  `createtime` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `sid` (`sid`),
  KEY `table_id` (`table_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=0 ;
";
pdo_run($sql);

if(!pdo_fieldexists('tiny_wmall_plus_store', 'is_assign')) {
	pdo_run("ALTER TABLE  `ims_tiny_wmall_plus_store` ADD  `is_assign` TINYINT( 3 ) UNSIGNED NOT NULL DEFAULT  '0' AFTER  `is_recommend` ,ADD  `is_reserve` TINYINT( 3 ) UNSIGNED NOT NULL DEFAULT  '0' AFTER  `is_assign` ,ADD  `is_meal` TINYINT( 3 ) UNSIGNED NOT NULL DEFAULT  '0' AFTER  `is_reserve` ,ADD  `forward_mode` VARCHAR( 15 ) NOT NULL AFTER  `is_meal` ;");
}

if(!pdo_fieldexists('tiny_wmall_plus_order', 'person_num')) {
	pdo_run("ALTER TABLE  `ims_tiny_wmall_plus_order` ADD  `person_num` TINYINT( 3 ) UNSIGNED NOT NULL DEFAULT  '1' AFTER  `is_refund` ,ADD  `table_id` INT( 10 ) UNSIGNED NOT NULL DEFAULT  '0' AFTER  `person_num` ;");
}

if(!pdo_fieldexists('tiny_wmall_plus_store', 'assign_mode')) {
	pdo_run("ALTER TABLE  `ims_tiny_wmall_plus_store` ADD  `assign_mode` TINYINT( 3 ) UNSIGNED NOT NULL DEFAULT  '1' AFTER  `forward_mode` ;");
}

if(!pdo_fieldexists('tiny_wmall_plus_store', 'assign_qrcode')) {
	pdo_run("ALTER TABLE  `ims_tiny_wmall_plus_store` ADD  `assign_qrcode` VARCHAR( 255 ) NOT NULL AFTER  `assign_mode` ;");
}

pdo_run("ALTER TABLE  `ims_tiny_wmall_plus_goods` CHANGE  `description`  `description` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ;");

if(!pdo_fieldexists('tiny_wmall_plus_order', 'table_cid')) {
	pdo_run("ALTER TABLE  `ims_tiny_wmall_plus_order` ADD  `table_cid` INT( 10 ) UNSIGNED NOT NULL DEFAULT  '0' AFTER  `table_id` ,ADD  `reserve_type` VARCHAR( 10 ) NOT NULL AFTER  `table_cid` ,ADD  `reserve_time` VARCHAR( 30 ) NOT NULL AFTER  `reserve_type` ;");
}

if(!pdo_fieldexists('tiny_wmall_plus_reply', 'table_id')) {
	pdo_run("ALTER TABLE  `ims_tiny_wmall_plus_reply` ADD  `table_id` INT( 10 ) UNSIGNED NOT NULL DEFAULT  '0' AFTER  `type` ;");
}

if(!pdo_fieldexists('tiny_wmall_plus_store', 'delivery_mode')) {
	pdo_run("ALTER TABLE  `ims_tiny_wmall_plus_store` ADD  `delivery_mode` TINYINT( 3 ) UNSIGNED NOT NULL DEFAULT  '1' AFTER  `assign_qrcode` ;");
}

if(!pdo_fieldexists('tiny_wmall_plus_store', 'auto_notice_deliveryer')) {
	pdo_run("ALTER TABLE  `ims_tiny_wmall_plus_store` ADD  `auto_notice_deliveryer` TINYINT( 3 ) UNSIGNED NOT NULL DEFAULT  '0' AFTER  `auto_get_address` ;");
}

pdo_run("ALTER TABLE  `ims_tiny_wmall_plus_store` CHANGE  `delivery_free_price`  `delivery_free_price` INT( 10 ) UNSIGNED NOT NULL DEFAULT  '0';");
pdo_run("ALTER TABLE  `ims_tiny_wmall_plus_config` CHANGE  `sms`  `sms` VARCHAR( 1000 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ;");

if(!pdo_fieldexists('tiny_wmall_plus_store_activity', 'amount_status')) {
	pdo_run("ALTER TABLE  `ims_tiny_wmall_plus_store_activity` ADD  `amount_status` TINYINT( 3 ) UNSIGNED NOT NULL DEFAULT  '0' AFTER  `grant_coupon_status` ,ADD  `amount_data` VARCHAR( 1000 ) NOT NULL AFTER  `amount_status` ;");
}

if(!pdo_fieldexists('tiny_wmall_plus_store', 'amount_status')) {
	pdo_run("ALTER TABLE  `ims_tiny_wmall_plus_store` ADD  `amount_status` TINYINT( 3 ) UNSIGNED NOT NULL DEFAULT  '0' AFTER  `grant_coupon_status` ;");
}

if(!pdo_fieldexists('tiny_wmall_plus_order_cart', 'num_data')) {
	pdo_run("ALTER TABLE  `ims_tiny_wmall_plus_order_cart` ADD  `num_data` VARCHAR( 2000 ) NOT NULL AFTER  `data` ;");
}

if(!pdo_fieldexists('tiny_wmall_plus_deliveryer_getcash_log', 'final_fee')) {
	pdo_run("ALTER TABLE  `ims_tiny_wmall_plus_deliveryer_getcash_log` ADD  `final_fee` DECIMAL( 10, 2 ) UNSIGNED NOT NULL DEFAULT  '0.00' AFTER  `take_fee` ;");
}

if(!pdo_fieldexists('tiny_wmall_plus_goods_options', 'displayorder')) {
	pdo_run("ALTER TABLE  `ims_tiny_wmall_plus_goods_options` ADD  `displayorder` TINYINT( 3 ) UNSIGNED NOT NULL DEFAULT  '0' AFTER  `total` ;");
}

if(!pdo_fieldexists('tiny_wmall_plus_store', 'auto_end_hours')) {
	pdo_run("ALTER TABLE  `ims_tiny_wmall_plus_store` ADD  `auto_end_hours` TINYINT( 3 ) UNSIGNED NOT NULL DEFAULT  '0' AFTER  `auto_notice_deliveryer` ;");
}

if(!pdo_fieldexists('tiny_wmall_plus_order', 'deliveryingtime')) {
	pdo_run("ALTER TABLE  `ims_tiny_wmall_plus_order` ADD  `deliveryingtime` INT( 10 ) UNSIGNED NOT NULL DEFAULT  '0' AFTER  `paytime` ;");
}

if(!pdo_fieldexists('tiny_wmall_plus_order_comment', 'thumbs')) {
	pdo_run("ALTER TABLE  `ims_tiny_wmall_plus_order_comment` ADD  `thumbs` VARCHAR( 3000 ) NOT NULL AFTER  `data` ;");
}

if(!pdo_fieldexists('tiny_wmall_plus_members', 'sex')) {
	pdo_run("ALTER TABLE  `ims_tiny_wmall_plus_members` ADD  `sex` VARCHAR( 5 ) NOT NULL AFTER  `nickname` ;");
}

if(!pdo_fieldexists('tiny_wmall_plus_members', 'addtime')) {
	pdo_run("ALTER TABLE  `ims_tiny_wmall_plus_members` ADD  `addtime` INT( 10 ) UNSIGNED NOT NULL DEFAULT  '0' AFTER  `search_data` ;");
}

if(pdo_fieldexists('tiny_wmall_plus_store_members', 'avatar')) {
	pdo_run("ALTER TABLE `ims_tiny_wmall_plus_store_members` DROP `avatar`, DROP `nickname`, DROP `realname`, DROP `mobile`, DROP `is_sys`;");
}

if(!pdo_fieldexists('tiny_wmall_plus_printer', 'api_key')) {
	pdo_run("ALTER TABLE  `ims_tiny_wmall_plus_printer` ADD  `api_key` VARCHAR( 100 ) NOT NULL COMMENT  '易联云打印机api_key' AFTER  `key` ;");
}

$sql = "CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_plus_delivery_cards` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `title` varchar(20) NOT NULL,
  `days` int(10) unsigned NOT NULL DEFAULT '0',
  `price` int(10) unsigned NOT NULL DEFAULT '0',
  `day_free_limit` tinyint(3) unsigned NOT NULL DEFAULT '2',
  `displayorder` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0 ;

CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_plus_delivery_cards_order` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `acid` int(10) unsigned NOT NULL DEFAULT '0',
  `uid` int(10) unsigned NOT NULL DEFAULT '0',
  `openid` varchar(60) NOT NULL,
  `ordersn` varchar(20) NOT NULL,
  `card_id` int(10) unsigned NOT NULL DEFAULT '0',
  `final_fee` varchar(20) NOT NULL,
  `pay_type` varchar(20) NOT NULL,
  `is_pay` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  `paytime` int(10) unsigned NOT NULL DEFAULT '0',
  `starttime` int(10) unsigned NOT NULL DEFAULT '0',
  `endtime` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0 ;

CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_plus_paylog` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `order_sn` varchar(20) NOT NULL,
  `order_id` int(10) unsigned NOT NULL DEFAULT '0',
  `order_type` varchar(10) NOT NULL,
  `fee` varchar(10) NOT NULL,
  `status` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  `paytime` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0 ;";

pdo_run($sql);

if(!pdo_fieldexists('tiny_wmall_plus_delivery_config', 'card_apply_status')) {
	pdo_run("ALTER TABLE  `ims_tiny_wmall_plus_delivery_config` ADD  `card_apply_status` TINYINT( 3 ) UNSIGNED NOT NULL DEFAULT  '0' AFTER  `get_cash_fee_max` ,ADD  `card_agreement` TEXT NOT NULL AFTER  `card_apply_status` ;");
}

if(!pdo_fieldexists('tiny_wmall_plus_members', 'setmeal_id')) {
	pdo_run("ALTER TABLE  `ims_tiny_wmall_plus_members` ADD  `setmeal_id` INT( 10 ) UNSIGNED NOT NULL DEFAULT  '0' AFTER  `mobile`, ADD  `setmeal_day_free_limit` TINYINT( 3 ) UNSIGNED NOT NULL DEFAULT  '0' AFTER  `setmeal_id`, ADD  `setmeal_starttime` INT( 10 ) UNSIGNED NOT NULL DEFAULT  '0' AFTER  `setmeal_day_free_limit` , ADD  `setmeal_endtime` INT( 10 ) UNSIGNED NOT NULL DEFAULT  '0' AFTER  `setmeal_starttime` ;");
}

if(pdo_fieldexists('tiny_wmall_plus_delivery_config', 'store_fee_type')) {
	pdo_run("ALTER TABLE `ims_tiny_wmall_plus_delivery_config` DROP `store_fee_type`,DROP `store_fee`;");
}

if(!pdo_fieldexists('tiny_wmall_plus_store_account', 'delivery_price')) {
	pdo_run("ALTER TABLE  `ims_tiny_wmall_plus_store_account` ADD  `delivery_price` VARCHAR( 10 ) NOT NULL AFTER  `delivery_type` ;");
}

if(!pdo_fieldexists('tiny_wmall_plus_delivery_config', 'plateform_delivery_fee')) {
	pdo_run("ALTER TABLE  `ims_tiny_wmall_plus_delivery_config` ADD  `plateform_delivery_fee` TINYINT( 3 ) UNSIGNED NOT NULL DEFAULT  '0' AFTER  `delivery_type` ;");
}

if(!pdo_fieldexists('tiny_wmall_plus_order', 'vip_free_delivery_fee')) {
	pdo_run("ALTER TABLE  `ims_tiny_wmall_plus_order` ADD  `vip_free_delivery_fee` TINYINT( 3 ) UNSIGNED NOT NULL DEFAULT  '0' AFTER  `final_fee` ;");
}

if(!pdo_fieldexists('tiny_wmall_plus_order_current_log', 'vip_free_delivery_fee')) {
	pdo_run("ALTER TABLE  `ims_tiny_wmall_plus_order_cart` CHANGE  `data`  `data` VARCHAR( 5000 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ;");
	pdo_run("ALTER TABLE  `ims_tiny_wmall_plus_order_current_log` ADD  `vip_free_delivery_fee` TINYINT( 3 ) UNSIGNED NOT NULL DEFAULT  '0' AFTER  `store_deliveryer_fee` ;");
}

if(!pdo_fieldexists('tiny_wmall_plus_config', 'report')) {
	pdo_run("ALTER TABLE  `ims_tiny_wmall_plus_config` ADD  `report` VARCHAR( 2000 ) NOT NULL AFTER  `credit` ;");
	$data = array('商户刷单', '外卖价格高于线下门店', '资质作假', '配送范围错误，下单后无法配送', '商户信息错误：营业时间、电话、位置等', '图片和描述与实物差别太大');
	pdo_update('tiny_wmall_plus_config', array('report' => iserializer($data)));
}

$sql = "
CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_plus_report` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `acid` int(10) unsigned NOT NULL DEFAULT '0',
  `sid` int(10) unsigned NOT NULL DEFAULT '0',
  `uid` int(10) unsigned NOT NULL DEFAULT '0',
  `openid` varchar(50) NOT NULL,
  `mobile` varchar(20) NOT NULL,
  `title` varchar(255) NOT NULL,
  `note` varchar(255) NOT NULL,
  `thumbs` varchar(2000) NOT NULL,
  `status` tinyint(3) NOT NULL DEFAULT '0',
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `sid` (`sid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0 ;
";
pdo_run($sql);

if(pdo_fieldexists('tiny_wmall_plus_store_account', 'alipay')) {
	pdo_run("ALTER TABLE `ims_tiny_wmall_plus_store_account` DROP `alipay`;");
}

if(pdo_fieldexists('tiny_wmall_plus_store_getcash_log', 'account_type')) {
	pdo_run("ALTER TABLE `ims_tiny_wmall_plus_store_getcash_log` DROP `account_type`;");
}

if(!pdo_fieldexists('tiny_wmall_plus_store', 'tips')) {
	pdo_run("ALTER TABLE  `ims_tiny_wmall_plus_store` ADD  `tips` VARCHAR( 100 ) NOT NULL AFTER  `notice` ;");
}

if(!pdo_fieldexists('tiny_wmall_plus_config', 'store')) {
	pdo_run("ALTER TABLE  `ims_tiny_wmall_plus_config` ADD  `store` VARCHAR( 3000 ) NOT NULL AFTER  `report` ;");
}

if(!pdo_fieldexists('tiny_wmall_plus_store', 'serve_fee')) {
	pdo_run("ALTER TABLE  `ims_tiny_wmall_plus_store` ADD  `serve_fee` VARCHAR( 255 ) NOT NULL AFTER  `serve_radius` ;");
}

if(!pdo_fieldexists('tiny_wmall_plus_order', 'serve_fee')) {
	pdo_run("ALTER TABLE  `ims_tiny_wmall_plus_order` ADD  `serve_fee` VARCHAR( 10 ) NOT NULL AFTER  `pack_fee` ;");
}

if(!pdo_fieldexists('tiny_wmall_plus_store', 'is_in_business')) {
	pdo_run("ALTER TABLE  `ims_tiny_wmall_plus_store` ADD  `is_in_business` TINYINT( 3 ) UNSIGNED NOT NULL DEFAULT  '1' AFTER  `business_hours` ;");
}

if(!pdo_fieldexists('tiny_wmall_plus_store_category', 'link')) {
	pdo_run("ALTER TABLE  `ims_tiny_wmall_plus_store_category` ADD  `link` VARCHAR( 255 ) NOT NULL AFTER  `thumb` ;");
}

if(!pdo_fieldexists('tiny_wmall_plus_address', 'name')) {
	pdo_run("ALTER TABLE  `ims_tiny_wmall_plus_address` ADD  `name` VARCHAR( 50 ) NOT NULL AFTER  `mobile` ;");
}

if(!pdo_fieldexists('tiny_wmall_plus_address', 'type')) {
	pdo_run("ALTER TABLE  `ims_tiny_wmall_plus_address` ADD  `type` TINYINT( 3 ) UNSIGNED NOT NULL DEFAULT  '1' COMMENT  '1:收货地址, 2:服务地址' AFTER  `is_default` ;");
}

if(!pdo_fieldexists('tiny_wmall_plus_config', 'follow_guide_status')) {
	pdo_run("ALTER TABLE  `ims_tiny_wmall_plus_config` ADD  `follow_guide_status` TINYINT( 3 ) UNSIGNED NOT NULL DEFAULT  '1' AFTER  `thumb` ;");
}

if(!pdo_fieldexists('tiny_wmall_plus_config', 'errander')) {
	pdo_run("ALTER TABLE  `ims_tiny_wmall_plus_config` ADD  `errander` VARCHAR( 1000 ) NOT NULL AFTER  `store` ;");
}

if(!pdo_fieldexists('tiny_wmall_plus_config', 'store_orderby_type')) {
	pdo_run("ALTER TABLE  `ims_tiny_wmall_plus_store` CHANGE  `cid`  `cid` VARCHAR( 50 ) NOT NULL ;");
	pdo_run("ALTER TABLE  `ims_tiny_wmall_plus_config` ADD  `store_orderby_type` VARCHAR( 20 ) NOT NULL DEFAULT  'distance' AFTER  `errander` ;");
	//需要重新生成有效的支付方式.
	$configs = pdo_getall('tiny_wmall_plus_config', array(), array('uniacid', 'payment'));
	if(!empty($configs)) {
		foreach($configs as $config) {
			if(!empty($config['payment'])) {
				$payment = iunserializer($config['payment']);
				if(!isset($payment['available'])) {
					continue;
				}
				$payment['available'] = array();
				foreach($temp as $key => $row) {
					if(in_array($key, array('credit', 'wechat', 'alipay', 'delivery')) && $row == 1) {
						$payment['available'][] = $key;
					}
				}
				pdo_update('tiny_wmall_plus_config', array('payment' => iserializer($payment)), array('uniacid' => $_W['uniacid']));
			}
		}
	}
}

if(!pdo_fieldexists('tiny_wmall_plus_store', 'order_note')) {
	pdo_run("ALTER TABLE  `ims_tiny_wmall_plus_store` CHANGE  `cid`  `cid` VARCHAR( 50 ) NOT NULL ;");
	pdo_run("ALTER TABLE `ims_tiny_wmall_plus_store` ADD `order_note` VARCHAR(255) NOT NULL COMMENT '订单备注' AFTER `delivery_mode`;");
}

if(!pdo_fieldexists('tiny_wmall_plus_goods', 'slides')) {
	pdo_run("ALTER TABLE  `ims_tiny_wmall_plus_goods` ADD  `slides` VARCHAR( 500 ) NOT NULL AFTER  `thumb` ;");
}

if(!pdo_fieldexists('tiny_wmall_plus_goods', 'box_price')) {
	pdo_run("ALTER TABLE  `ims_tiny_wmall_plus_goods` ADD  `box_price` VARCHAR( 10 ) NOT NULL COMMENT  '餐盒费' AFTER  `discount_price` ,ADD  `min_buy_limit` TINYINT( 3 ) UNSIGNED NOT NULL DEFAULT  '1' COMMENT  '最少购买数量' AFTER  `box_price` ;");
}

$sql = "
DROP TABLE IF EXISTS `ims_tiny_wmall_article_category`;
DROP TABLE IF EXISTS `ims_tiny_wmall_article`;
CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_plus_help` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `title` varchar(100) NOT NULL DEFAULT '',
  `content` mediumtext NOT NULL,
  `displayorder` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  `click` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `title` (`title`),
  KEY `uniacid` (`uniacid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0 ";
pdo_run($sql);

if(!pdo_fieldexists('tiny_wmall_plus_order_refund_log', 'order_type')) {
	pdo_run("ALTER TABLE  `ims_tiny_wmall_plus_order_refund_log` ADD  `order_type` VARCHAR( 20 ) NOT NULL DEFAULT  'order' AFTER  `uniacid` ;");
}

if(!pdo_fieldexists('tiny_wmall_plus_deliveryer', 'token')) {
	pdo_run("UPDATE  `ims_tiny_wmall_plus_store` SET cid = CONCAT( '|', cid,  '|' ) WHERE cid != '';");
	pdo_run("ALTER TABLE  `ims_tiny_wmall_plus_deliveryer` ADD  `token` VARCHAR( 32 ) NOT NULL AFTER  `salt` ;");
}

if(!pdo_fieldexists('tiny_wmall_plus_deliveryer_current_log', 'order_type')) {
	pdo_run("ALTER TABLE  `ims_tiny_wmall_plus_deliveryer_current_log` ADD  `order_type` VARCHAR( 20 ) NOT NULL DEFAULT  'order' AFTER  `deliveryer_id` ;");
}

if(!pdo_fieldexists('tiny_wmall_plus_goods', 'print_label')) {
	pdo_run("ALTER TABLE  `ims_tiny_wmall_plus_goods` ADD  `print_label` INT( 10 ) UNSIGNED NOT NULL DEFAULT  '0' AFTER  `comment_good` ;");
}

if(!pdo_fieldexists('tiny_wmall_plus_printer', 'print_label')) {
	pdo_run("ALTER TABLE  `ims_tiny_wmall_plus_printer` ADD  `print_label` VARCHAR( 50 ) NOT NULL AFTER  `print_nums` ;");
	pdo_run("ALTER TABLE  `ims_tiny_wmall_plus_printer` ADD  `is_print_all` TINYINT( 3 ) UNSIGNED NOT NULL DEFAULT  '1' AFTER  `print_label` ;");
}

if(!pdo_fieldexists('tiny_wmall_plus_order_stat', 'print_label')) {
	pdo_run("ALTER TABLE  `ims_tiny_wmall_plus_order_stat` ADD  `print_label` INT( 10 ) UNSIGNED NOT NULL DEFAULT  '0' AFTER  `goods_price` ;");
}

$sql = "
CREATE TABLE IF NOT EXISTS `ims_tiny_wmall_plus_printer_label` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `sid` int(10) unsigned NOT NULL DEFAULT '0',
  `title` varchar(20) NOT NULL,
  `displayorder` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `sid` (`sid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0 ;
";
pdo_run($sql);

if(!pdo_fieldexists('tiny_wmall_plus_order', 'transaction_id')) {
	pdo_run("ALTER TABLE  `ims_tiny_wmall_plus_order` ADD  `transaction_id` VARCHAR( 60 ) NOT NULL COMMENT  '第三方支付交易号' AFTER  `reserve_time` ;");
	pdo_run("ALTER TABLE  `ims_tiny_wmall_plus_printer` CHANGE  `key`  `key` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ;");
}


//配送app
//ALTER TABLE  `ims_tiny_wmall_plus_deliveryer` ADD  `work_status` TINYINT( 3 ) UNSIGNED NOT NULL DEFAULT  '1' AFTER  `credit2` ;
//ALTER TABLE  `ims_tiny_wmall_plus_order` ADD  `deliveryinstoretime` INT( 10 ) UNSIGNED NOT NULL DEFAULT  '0' AFTER  `deliveryingtime` ;
//ims_tiny_wmall_plus_deliveryer_location_logs

//跑腿
//ims_tiny_wmall_plus_errander_category  ims_tiny_wmall_plus_errander_order ims_we7_wmall_plus_errander_order_status_log