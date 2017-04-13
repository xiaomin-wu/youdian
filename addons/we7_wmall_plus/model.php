<?php
defined('IN_IA') or exit('Access Denied');

function p($data) {
	echo '<pre>';
	print_r($data);
}

function mload() {
	static $mloader;
	if(empty($mloader)) {
		$mloader = new Mloader();
	}
	return $mloader;
}
class Mloader {
	private $cache = array();
	function func($name) {
		if (isset($this->cache['func'][$name])) {
			return true;
		}
		$file = IA_ROOT . '/addons/we7_wmall_plus/function/' . $name . '.func.php';
		if (file_exists($file)) {
			include $file;
			$this->cache['func'][$name] = true;
			return true;
		} else {
			trigger_error('Invalid Helper Function /addons/we7_wmall_plus/function/' . $name . '.func.php', E_USER_ERROR);
			return false;
		}
	}

	function model($name) {
		if (isset($this->cache['model'][$name])) {
			return true;
		}
		$file = IA_ROOT . '/addons/we7_wmall_plus/model/' . $name . '.mod.php';
		if (file_exists($file)) {
			include $file;
			$this->cache['model'][$name] = true;
			return true;
		} else {
			trigger_error('Invalid Model /addons/we7_wmall_plus/model/' . $name . '.mod.php', E_USER_ERROR);
			return false;
		}
	}

	function classs($name) {
		if (isset($this->cache['class'][$name])) {
			return true;
		}
		$file = IA_ROOT . '/addons/we7_wmall_plus/class/' . $name . '.class.php';
		if (file_exists($file)) {
			include $file;
			$this->cache['class'][$name] = true;
			return true;
		} else {
			trigger_error('Invalid Class /addons/we7_wmall_plus/class/' . $name . '.class.php', E_USER_ERROR);
			return false;
		}
	}
}

function module_familys() {
	return array(
		'basic' => array(
			'title' => '外送基础版',
			'css' => 'label label-success'
		),
		'errander' => array(
			'title' => '外送+跑腿',
			'css' => 'label label-success'
		),
		'errander_deliveryerApp' => array(
			'title' => '外送+跑腿+配送员app',
			'css' => 'label label-success'
		),
		'vip' => array(
			'title' => 'vip版',
			'css' => 'label label-success'
		),
	);
}

/*
 * get_config
 * */
function sys_config($uniacid = 0) {
	global $_W;
	$uniacid = intval($uniacid);
	if(!$uniacid) {
		$uniacid = $_W['uniacid'];
	}
	$data = pdo_fetch("SELECT * FROM " . tablename('tiny_wmall_plus_config') . ' WHERE uniacid = :uniacid', array(':uniacid' => $uniacid));
	if(empty($data)) {
		$data = array(
			'version' => 1,
		);
	} else {
		$se_fileds = array('sms', 'copyright', 'notice', 'payment', 'manager', 'credit', 'report', 'takeout', 'errander', 'app', 'delivery', 'settle');
		foreach($se_fileds as $se_filed) {
			$data[$se_filed] = (array)iunserializer($data[$se_filed]);
		}
		if(!empty($data['imgnav_data'])) {
			$data['imgnav_data'] = (array)iunserializer($data['imgnav_data']);
		}
		if(empty($data['settle']) || !is_array($data['settle'])) {
			$data['settle'] = array(
				'status' => 1,
				'audit_status' => 2,
				'mobile_verify_status' => 2,
				'instore_serve_rate' => 0,
				'takeout_serve_rate' => 0,
				'get_cash_fee_limit' => 1,
				'get_cash_fee_rate' => 0,
				'get_cash_fee_min' => 0,
				'get_cash_fee_max' => 0,
			);
		}
		if(empty($data['takeout']) || !is_array($data['takeout'])) {
			$data['takeout'] = array(
				'delivery_mode' => 2,
				'delivery_fee_mode' => 1,
				'delivery_fee' => 3,
				'deliveryer_fee_type' => 1,
				'deliveryer_fee' => 1,
			);
		}
		$map = array(
			'map' => array('location_x' => '39.90923', 'location_y' => '116.397428'),
			'city' => '全国',
			'serve_radius' => 0,
		);
		$data['takeout'] = array_merge($map, $data['takeout']);
	}
	return $data;
}

/*
 * $type (1:广告页, 2:首页幻灯片)
 * get_index_slide
 * */
function sys_fetch_slide($type = 1) {
	global $_W;
	$slides = pdo_fetchall('select * from' . tablename('tiny_wmall_plus_slide') .'where uniacid = :uniacid and type = :type and status = 1 order by displayorder desc' ,array(':uniacid' => $_W['uniacid'] ,':type' => $type));
	if($type == 1) {
		shuffle($slides);
	}
	return $slides;
}

function tpl_format($title, $ordersn, $orderstatus, $remark = '') {
	$send = array(
		'first' => array(
			'value' => $title,
			'color' => '#ff510'
		),
		'OrderSn' => array(
			'value' => $ordersn,
			'color' => '#ff510'
		),
		'OrderStatus' => array(
			'value' => $orderstatus,
			'color' => '#ff510'
		),
		'remark' => array(
			'value' => $remark,
			'color' => '#ff510'
		),
	);
	return $send;
}

function array_compare($key, $array) {
	$keys = array_keys($array);
	$keys[] = $key;
	asort($keys);
	$values = array_values($keys);
	$index = array_search($key, $values);
	if($index >= 0) {
		$now = $values[$index];
		$next = $values[$index + 1];
		if($now == $next) {
			$next = intval($next);
			return $array[$next];
		}
		$index = $values[$index - 1];
		return $array[$index];
	}
	return false;
}

function store_orderbys() {
	return array(
		'distance' => array(
			'title' => '离我最近',
			'key' => 'distance',
			'val' => 'asc',
			'css' => 'icon-b distance',
		),
		'sailed' => array(
			'title' => '销量最高',
			'key' => 'sailed',
			'val' => 'desc',
			'css' => 'icon-b sailed-num',
		),
		'score' => array(
			'title' => '评分最高',
			'key' => 'score',
			'val' => 'desc',
			'css' => 'icon-b score',
		),
		'send_price' => array(
			'title' => '起送价最低',
			'key' => 'send_price',
			'val' => 'asc',
			'css' => 'icon-b send-price',
		),
		'delivery_time' => array(
			'title' => '送单速度最快',
			'key' => 'delivery_time',
			'val' => 'asc',
			'css' => 'icon-b delivery-time',
		),
	);
} 

function store_discounts() {
	return array(
		'first_order_status' => array(
			'title' => '新用户立减',
			'key' => 'first_order_status',
			'val' => 1,
			'css' => 'icon-b xin',
		),
		'discount_status' => array(
			'title' => '立减优惠',
			'key' => 'discount_status',
			'val' => 1,
			'css' => 'icon-b jian',
		),
		'grant_status' => array(
			'title' => '下单满赠',
			'key' => 'grant_status',
			'val' => 1,
			'css' => 'icon-b zeng',
		),
		'delivery_price' => array(
			'title' => '免配送费',
			'key' => 'delivery_price',
			'val' => 0,
			'css' => 'icon-b mian',
		),
/*		'bargain_price_status' => array(
			'title' => '特价优惠',
			'key' => 'bargain_price_status',
			'val' => 1,
			'css' => 'icon-b te',
		),
		'reserve_status' => array(
			'title' => '预定优惠',
			'key' => 'reserve_status',
			'val' => 1,
			'css' => 'icon-b yuding',
		),*/
		'collect_coupon_status' => array(
			'title' => '进店领券',
			'key' => 'collect_coupon_status',
			'val' => 1,
			'css' => 'icon-b coupon',
		),
		'grant_coupon_status' => array(
			'title' => '下单返券',
			'key' => 'grant_coupon_status',
			'val' => 1,
			'css' => 'icon-b fan',
		),
		'invoice_status' => array(
			'title' => '支持开发票',
			'key' => 'invoice_status',
			'val' => 1,
			'css' => 'icon-b invoice',
		),
/*		'token_status' => array(
			'title' => '支持代金券',
			'key' => 'token_status',
			'val' => 1,
			'css' => 'icon-b coupon',
		),*/
	);
}

function upload_file($file, $type, $name = '') {
	global $_W;
	if (empty($file['name'])) {
		return error(-1, '上传失败, 请选择要上传的文件！');
	}
	if ($file['error'] != 0) {
		return error(-1, '上传失败, 请重试.');
	}
	load()->func('file');
	$pathinfo = pathinfo($file['name']);
	$ext = strtolower($pathinfo['extension']);
	$basename = strtolower($pathinfo['basename']);
	if($name != '') {
		$basename = $name;
	}
	$path = "attachment/{$type}s/{$_W['uniacid']}/";
	mkdirs(MODULE_ROOT . '/' . $path);
	if (!strexists($basename, $ext)) {
		$basename .= '.' . $ext;
	}

	if (!file_move($file['tmp_name'],  MODULE_ROOT . '/' . $path . $basename)) {
		return error(-1, '保存上传文件失败');
	}
	return $path . $basename;
}

function read_excel($filename) {
	include_once (IA_ROOT . '/framework/library/phpexcel/PHPExcel.php');
	$filename = MODULE_ROOT . '/' . $filename;
	if(!file_exists($filename)) {
		return error(-1, '文件不存在或已经删除');
	}
	$ext = pathinfo($filename, PATHINFO_EXTENSION);
	if($ext == 'xlsx') {
		$objReader = PHPExcel_IOFactory::createReader('Excel2007');
	} else {
		$objReader = PHPExcel_IOFactory::createReader('Excel5');
	}

	$objReader->setReadDataOnly(true);
	$objPHPExcel = $objReader->load($filename);
	$objWorksheet = $objPHPExcel->getActiveSheet();
	$highestRow = $objWorksheet->getHighestRow();
	$highestColumn = $objWorksheet->getHighestColumn();
	$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
	$excelData = array();
	for ($row = 1; $row <= $highestRow; $row++) {
		for ($col = 0; $col < $highestColumnIndex; $col++) {
			$excelData[$row][] =(string)$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
		}
	}
	return $excelData;
}

function sub_day($staday) {
	$value = TIMESTAMP - $staday;
	if($value < 0) {
		return '';
	} elseif($value >= 0 && $value < 59) {
		return ($value+1)."秒";
	} elseif($value >= 60 && $value < 3600) {
		$min = intval($value / 60);
		return $min." 分钟";
	} elseif($value >=3600 && $value < 86400) {
		$h = intval($value / 3600);
		return $h." 小时";
	} elseif($value >= 86400 && $value < 86400*30) {
		$d = intval($value / 86400);
		return intval($d)." 天";
	} elseif($value >= 86400*30 && $value < 86400*30*12) {
		$mon  = intval($value / (86400*30));
		return $mon." 月";
	} else {
		$y = intval($value / (86400*30*12));
		return $y." 年";
	}
}

function operator_menu() {
	global $_W, $_GPC;
	$sid = intval($_GPC['__sid']);
	if($sid > 0) {
		$store = pdo_get('tiny_wmall_plus_store', array('uniacid' => $_W['uniacid'], 'id' => $sid), array('id', 'title'));
		$menu[] = array(
			'title' => "当前门店:{$store['title']}",
			'items' => array(
				array(
					'title' => '门店信息',
					'url' => url('site/entry', array('m' => 'we7_wmall_plus', 'do' => 'store', 'op'=> 'post', 'id' => $sid)),
				),
				array(
					'title' => '订单管理',
					'url' => url('site/entry', array('m' => 'we7_wmall_plus', 'do' => 'order')),
				),
				array(
					'title' => '配货中心',
					'url' => url('site/entry', array('m' => 'we7_wmall_plus', 'do' => 'dispatch')),
				),
				array(
					'title' => '商品分类',
					'url' => url('site/entry', array('m' => 'we7_wmall_plus', 'do' => 'category')),
				),
				array(
					'title' => '商品列表',
					'url' => url('site/entry', array('m' => 'we7_wmall_plus', 'do' => 'goods')),
				),
				array(
					'title' => '打印机管理',
					'url' => url('site/entry', array('m' => 'we7_wmall_plus', 'do' => 'printer')),
				),
				array(
					'title' => '店员管理',
					'url' => url('site/entry', array('m' => 'we7_wmall_plus', 'do' => 'clerk')),
				),
				array(
					'title' => '配送员管理',
					'url' => url('site/entry', array('m' => 'we7_wmall_plus', 'do' => 'deliveryer')),
				),
				array(
					'title' => '评价管理',
					'url' => url('site/entry', array('m' => 'we7_wmall_plus', 'do' => 'comment')),
				),
				array(
					'title' => '顾客管理',
					'url' => url('site/entry', array('m' => 'we7_wmall_plus', 'do' => 'member', 'op' => 'list')),
				),
				array(
					'title' => '营销活动',
					'url' => url('site/entry', array('m' => 'we7_wmall_plus', 'do' => 'activity')),
				),
				array(
					'title' => '订单统计',
					'url' => url('site/entry', array('m' => 'we7_wmall_plus', 'do' => 'stat')),
				),
				array(
					'title' => '顾客统计',
					'url' => url('site/entry', array('m' => 'we7_wmall_plus', 'do' => 'member', 'op' => 'stat')),
				),
			),
		);
	}
	return $menu;
}

function mine_current_frames(&$frames) {
	global $controller, $action;
	if(!empty($frames) && is_array($frames)) {
		foreach($frames as &$frame) {
			if(empty($frame['items'])) continue;
			foreach($frame['items'] as &$fr) {
				$query = parse_url($fr['url'], PHP_URL_QUERY);
				parse_str($query, $urls);
				if(empty($urls)) continue;
				$get = $_GET;
				$get['c'] = $controller;
				$get['a'] = $action;
				if(!empty($do)) {
					$get['do'] = $do;
				}
				if(!empty($_GET['op'])) {
					$get['op'] = $_GET['op'];
				}
				$diff = array_diff_assoc($urls, $get);
				if(empty($diff)) {
					$fr['active'] = ' active';
				}
			}
		}
	}
}

function check_verifycode($mobile, $code) {
	global $_W;
	$isexist = pdo_fetch('select * from ' . tablename('uni_verifycode') . ' where uniacid = :uniacid and receiver = :receiver and verifycode = :verifycode and createtime >= :createtime', array(':uniacid' => $_W['uniacid'], ':receiver' => $mobile, ':verifycode' => $code, ':createtime' => time()-1800));
	if(!empty($isexist)) {
		return true;
	}
	return false;
}

function sys_notice_settle($sid, $type = 'clerk', $note= '') {
	global $_W;
	$store = store_fetch($sid, array('id', 'title', 'addtime', 'status', 'address'));
	if(empty($store)) {
		return error(-1, '门店不存在');
	}
	$store['manager'] = pdo_get('tiny_wmall_plus_clerk', array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'role' => 'manager'));
	$store_status = array(
		1 => '审核通过',
		2 => '审核中',
		3 => '审核未通过',
	);
	$acc = WeAccount::create($_W['acid']);
	order_fetch_fansinfo();
	if($type == 'clerk') {
		if(empty($store['manager']) || empty($store['manager']['openid'])) {
			return error(-1, '门店申请人信息不完善');
		}
		//通知申请人
		$tips = "【{$store['title']}】申请入驻【{$_W['we7_wmall_plus']['config']['title']}】进度通知";
		$remark = array(
			"申请时间: " . date('Y-m-d H: i', $store['addtime']),
			"审核时间: " . date('Y-m-d H: i', time()),
			"登陆账号: " . $store['manager']['title'],
			$note
		);
		$remark = implode("\n", $remark);
		$send = array(
			'first' => array(
				'value' => $tips,
				'color' => '#ff510'
			),
			'keyword1' => array(
				'value' => $store['title'],
				'color' => '#ff510'
			),
			'keyword2' => array(
				'value' => $store_status[$store['status']],
				'color' => '#ff510'
			),
			'remark' => array(
				'value' => $remark,
				'color' => '#ff510'
			),
		);
		$status = $acc->sendTplNotice($store['manager']['openid'], $_W['we7_wmall_plus']['config']['notice']['settle_tpl'], $send);
	} elseif($type == 'manager') {
		$maneger = $_W['we7_wmall_plus']['config']['manager'];
		if(empty($maneger['openid'])) {
			return error(-1, '平台管理员信息不存在');
		}
		$tips = "尊敬的【{$maneger['nickname']}】，有新的商家提交了入驻请求。请登录电脑进行审核";
		$remark = array(
			"商家地址: {$store['address']}",
			"申请人手机号: {$store['manager']['mobile']}",
			$note
		);
		$remark = implode("\n", $remark);
		$send = array(
			'first' => array(
				'value' => $tips,
				'color' => '#ff510'
			),
			'keyword1' => array(
				'value' => $store['manager']['title'],
				'color' => '#ff510'
			),
			'keyword2' => array(
				'value' => $store['title'],
				'color' => '#ff510'
			),
			'keyword3' => array(
				'value' => date('Y-m-d H:i', time()),
				'color' => '#ff510',
			),
			'remark' => array(
				'value' => $remark,
				'color' => '#ff510'
			),
		);
		$status = $acc->sendTplNotice($maneger['openid'], $_W['we7_wmall_plus']['config']['notice']['settle_apply_tpl'], $send);
	}
	return $status;
}

function long2short($longurl) {
	global $_W;
	load()->func('communication');
	$longurl = trim($longurl);
	$token = WeAccount::token(WeAccount::TYPE_WEIXIN);
	$url = "https://api.weixin.qq.com/cgi-bin/shorturl?access_token={$token}";
	$send = array();
	$send['action'] = 'long2short';
	$send['long_url'] = $longurl;
	$response = ihttp_request($url, json_encode($send));
	if(is_error($response)) {
		$result = error(-1, "访问公众平台接口失败, 错误: {$response['message']}");
	}
	$result = @json_decode($response['content'], true);
	if(empty($result)) {
		$result =  error(-1, "接口调用失败, 元数据: {$response['meta']}");
	} elseif(!empty($result['errcode'])) {
		$result = error(-1, "访问微信接口错误, 错误代码: {$result['errcode']}, 错误信息: {$result['errmsg']}");
	}
	if(is_error($result)) {
		exit(json_encode(array('errcode' => -1, 'errmsg' => $result['message'])));
	}
	return $result['short_url'];
}


function ifile_put_contents($filename, $data) {
	global $_W;
	$filename = MODULE_ROOT . '/' . $filename;
	mkdirs(dirname($filename));
	file_put_contents($filename, $data);
	@chmod($filename, $_W['config']['setting']['filemode']);
	return is_file($filename);
}

function sys_notice_store_getcash($sid, $getcash_log_id , $type = 'apply', $note = '') {
	global $_W;
	$store = store_fetch($sid, array('id', 'title', 'addtime', 'status', 'address'));
	if(empty($store)) {
		return error(-1, '门店不存在');
	}
	$store['manager'] = pdo_get('tiny_wmall_plus_clerk', array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'role' => 'manager'));
	$log = pdo_get('tiny_wmall_plus_store_getcash_log', array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'id' => $getcash_log_id));
	if(empty($log)) {
		return error(-1, '提现记录不存在');
	}
	$log['account'] = iunserializer($log['account']);
	$acc = WeAccount::create($_W['acid']);
	order_fetch_fansinfo();
	if($type == 'apply') {
		if(!empty($store['manager']) && !empty($store['manager']['openid'])) {
			//通知申请人
			$tips = "您好,【{$store['manager']['nickname']}】,【{$store['title']}】账户余额提现申请已提交,请等待管理员审核";
			$remark = array(
				"申请门店: " . $store['title'],
				"账户类型: 微信",
				"真实姓名: " . $log['account']['realname'],
				$note
			);
			$params = array(
				'first' => $tips,
				'money' => $log['final_fee'],
				'timet' => date('Y-m-d H:i', TIMESTAMP),
				'remark' => implode("\n", $remark)
			);
			$send = sys_wechat_tpl_format($params);
			$status = $acc->sendTplNotice($store['manager']['openid'], $_W['we7_wmall_plus']['config']['notice']['getcash_apply_tpl'], $send);
		}
		$maneger = $_W['we7_wmall_plus']['config']['manager'];
		if(!empty($maneger['openid'])) {
			//通知平台管理员
			$tips = "您好,【{$maneger['nickname']}】,【{$store['title']}】申请提现,请尽快处理";
			$remark = array(
				"申请门店: " . $store['title'],
				"账户类型: 微信",
				"真实姓名: " . $log['account']['realname'],
				"提现总金额: " . $log['get_fee'],
				"手续　费: " . $log['take_fee'],
				"实际到账: " . $log['final_fee'],
				$note
			);
			$params = array(
				'first' => $tips,
				'money' => $log['final_fee'],
				'timet' => date('Y-m-d H:i', TIMESTAMP),
				'remark' => implode("\n", $remark)
			);
			$send = sys_wechat_tpl_format($params);
			$status = $acc->sendTplNotice($maneger['openid'], $_W['we7_wmall_plus']['config']['notice']['getcash_apply_tpl'], $send);
		}
	} elseif($type == 'success') {
		if(empty($store['manager']) || empty($store['manager']['openid'])) {
			return error(-1, '门店管理员信息不完善');
		}
		$tips = "您好,【{$store['manager']['nickname']}】,【{$store['title']}】账户余额提现已处理";
		$remark = array(
			"处理时间: " . date('Y-m-d H:i', $log['endtime']),
			"申请门店: " . $store['title'],
			"账户类型: 微信",
			"真实姓名: " . $log['account']['realname'],
			'如有疑问请及时联系平台管理人员'
		);
		$params = array(
			'first' => $tips,
			'money' => $log['final_fee'],
			'timet' => date('Y-m-d H:i', $log['addtime']),
			'remark' => implode("\n", $remark)
		);
		$send = sys_wechat_tpl_format($params);
		$status = $acc->sendTplNotice($store['manager']['openid'], $_W['we7_wmall_plus']['config']['notice']['getcash_success_tpl'], $send);
	} elseif($type == 'fail') {
		if(empty($store['manager']) || empty($store['manager']['openid'])) {
			return error(-1, '门店管理员信息不完善');
		}
		$tips = "您好,【{$store['manager']['nickname']}】, 【{$store['title']}】账户余额提现已处理, 提现未成功";
		$remark = array(
			"处理时间: " . date('Y-m-d H:i', $log['endtime']),
			"申请门店: " . $store['title'],
			"账户类型: 微信",
			"真实姓名: " . $log['account']['realname'],
			'如有疑问请及时联系平台管理人员'
		);
		$params = array(
			'first' => $tips,
			'money' => $log['final_fee'],
			'time' => date('Y-m-d H:i', $log['addtime']),
			'remark' => implode("\n", $remark)
		);
		$send = sys_wechat_tpl_format($params);
		$status = $acc->sendTplNotice($store['manager']['openid'], $_W['we7_wmall_plus']['config']['notice']['getcash_fail_tpl'], $send);
	}
	return $status;
}

function sys_wechat_tpl_format($params) {
	$send = array();
	foreach($params as $key => $param) {
		$send[$key] = array(
			'value' => $param,
			'color' => '#ff510',
		);
	}
	return $send;
}

/**
 * 计算两个坐标之间的距离(米)
 * @param float $fP1Lat 起点(纬度)
 * @param float $fP1Lon 起点(经度)
 * @param float $fP2Lat 终点(纬度)
 * @param float $fP2Lon 终点(经度)
 * @return int
 */
function distanceBetween($longitude1, $latitude1, $longitude2, $latitude2) {
	$radLat1 = radian ( $latitude1 );
	$radLat2 = radian ( $latitude2 );
	$a = radian ( $latitude1 ) - radian ( $latitude2 );
	$b = radian ( $longitude1 ) - radian ( $longitude2 );
	$s = 2 * asin ( sqrt ( pow ( sin ( $a / 2 ), 2 ) + cos ( $radLat1 ) *
			cos ( $radLat2 ) * pow ( sin ( $b / 2 ), 2 ) ) );
	$s = $s * 6378.137; //乘上地球半径，单位为公里
	$s = round ( $s * 10000 ) / 10000; //单位为公里(km)
	return $s * 1000; //单位为km
}

function radian($d) {
	return $d * 3.1415926535898 / 180.0;
}

function array_order($value, $array) {
	$array[] = $value;
	asort($array);
	$array = array_values($array);
	$index = array_search($value, $array);
	return $array[$index + 1];
}

function itpl_ueditor($id, $value = '', $options = array(), $isNews = false) {
	global $_W;
	$s = '';
	if (!defined('TPL_INIT_UEDITOR')) {
		$s .= '<script type="text/javascript" src="'.$_W['siteroot'].'/web/resource/components/ueditor/ueditor.config.js"></script><script type="text/javascript" src="'.$_W['siteroot'].'/web/resource/components/ueditor/ueditor.all.min.js"></script><script type="text/javascript" src="'.$_W['siteroot'].'/web/resource/components/ueditor/lang/zh-cn/zh-cn.js"></script>';
	}
	$options['height'] = empty($options['height']) ? 200 : $options['height'];
	$s .= !empty($id) ? "<textarea id=\"{$id}\" name=\"{$id}\" type=\"text/plain\" style=\"height:{$options['height']}px;\">{$value}</textarea>" : '';
	$s .= "
	<script type=\"text/javascript\">
			var ueditoroption = {
				'autoClearinitialContent' : false,
				'toolbars' : [['fullscreen', 'source', 'preview', '|', 'bold', 'italic', 'underline', 'strikethrough', 'forecolor', 'backcolor', '|',
					'justifyleft', 'justifycenter', 'justifyright', '|', 'insertorderedlist', 'insertunorderedlist', 'blockquote', 'emotion',
					'link', 'removeformat', '|', 'rowspacingtop', 'rowspacingbottom', 'lineheight','indent', 'paragraph', 'fontsize', '|',
					'inserttable', 'deletetable', 'insertparagraphbeforetable', 'insertrow', 'deleterow', 'insertcol', 'deletecol',
					'mergecells', 'mergeright', 'mergedown', 'splittocells', 'splittorows', 'splittocols', '|', 'anchor', 'map', 'print', 'drafts']],
				'elementPathEnabled' : false,
				'initialFrameHeight': {$options['height']},
				'focus' : false,
				'maximumWords' : 9999999999999
			};
			var opts = {
				type :'image',
				direct : false,
				multiple : true,
				tabs : {
					'upload' : 'active',
					'browser' : '',
					'crawler' : ''
				},
				path : '',
				dest_dir : '',
				global : false,
				thumb : false,
				width : 0
			};
			UE.registerUI('myinsertimage',function(editor,uiName){
				editor.registerCommand(uiName, {
					execCommand:function(){
						require(['fileUploader'], function(uploader){
							uploader.show(function(imgs){
								if (imgs.length == 0) {
									return;
								} else if (imgs.length == 1) {
									editor.execCommand('insertimage', {
										'src' : imgs[0]['url'],
										'_src' : imgs[0]['attachment'],
										'width' : '100%',
										'alt' : imgs[0].filename
									});
								} else {
									var imglist = [];
									for (i in imgs) {
										imglist.push({
											'src' : imgs[i]['url'],
											'_src' : imgs[i]['attachment'],
											'width' : '100%',
											'alt' : imgs[i].filename
										});
									}
									editor.execCommand('insertimage', imglist);
								}
							}, opts);
						});
					}
				});
				var btn = new UE.ui.Button({
					name: '插入图片',
					title: '插入图片',
					cssRules :'background-position: -726px -77px',
					onclick:function () {
						editor.execCommand(uiName);
					}
				});
				editor.addListener('selectionchange', function () {
					var state = editor.queryCommandState(uiName);
					if (state == -1) {
						btn.setDisabled(true);
						btn.setChecked(false);
					} else {
						btn.setDisabled(false);
						btn.setChecked(state);
					}
				});
				return btn;
			}, 19);
			UE.registerUI('myinsertvideo',function(editor,uiName){
				editor.registerCommand(uiName, {
					execCommand:function(){
						require(['fileUploader'], function(uploader){
							uploader.show(function(video){
								if (!video) {
									return;
								} else {
									var videoType = video.isRemote ? 'iframe' : 'video';
									editor.execCommand('insertvideo', {
										'url' : video.url,
										'width' : 300,
										'height' : 200
									}, videoType);
								}
							}, {type : 'video', isNews : '".$isNews."'});
						});
					}
				});
				var btn = new UE.ui.Button({
					name: '插入视频',
					title: '插入视频',
					cssRules :'background-position: -320px -20px',
					onclick:function () {
						editor.execCommand(uiName);
					}
				});
				editor.addListener('selectionchange', function () {
					var state = editor.queryCommandState(uiName);
					if (state == -1) {
						btn.setDisabled(true);
						btn.setChecked(false);
					} else {
						btn.setDisabled(false);
						btn.setChecked(state);
					}
				});
				return btn;
			}, 20);
			".(!empty($id) ? "
				$(function(){
					var ue = UE.getEditor('{$id}', ueditoroption);
					$('#{$id}').data('editor', ue);
					$('#{$id}').parents('form').submit(function() {
						if (ue.queryCommandState('source')) {
							ue.execCommand('source');
						}
					});
				});" : '')."
	</script>";
	return $s;
}

function sys_notice_deliveryer_getcash($deliveryer_id, $getcash_log_id , $type = 'apply', $note = '') {
	global $_W;
	$deliveryer = pdo_get('tiny_wmall_plus_deliveryer',  array('uniacid' => $_W['uniacid'], 'id' => $deliveryer_id));
	if(empty($deliveryer)) {
		return error(-1, '配送员不存在');
	}
	$log = pdo_get('tiny_wmall_plus_deliveryer_getcash_log', array('uniacid' => $_W['uniacid'], 'deliveryer_id' => $deliveryer_id, 'id' => $getcash_log_id));
	if(empty($log)) {
		return error(-1, '提现记录不存在');
	}
	$acc = WeAccount::create($_W['acid']);
	if($type == 'apply') {
		if(!empty($deliveryer['openid'])) {
			//通知申请人
			$tips = "您好,【{$deliveryer['title']}】, 您的账户余额提现申请已提交,请等待管理员审核";
			$remark = array(
				"申请　人: " . $deliveryer['title'],
				"手机　号: " . $deliveryer['mobile'],
				"手续　费: " . $log['take_fee'],
				"实际到账: " . $log['final_fee'],
				$note
			);
			$params = array(
				'first' => $tips,
				'money' => $log['get_fee'],
				'timet' => date('Y-m-d H:i', TIMESTAMP),
				'remark' => implode("\n", $remark)
			);
			$send = sys_wechat_tpl_format($params);
			$status = $acc->sendTplNotice($deliveryer['openid'], $_W['we7_wmall_plus']['config']['notice']['getcash_apply_tpl'], $send);
		}
		$maneger = $_W['we7_wmall_plus']['config']['manager'];
		if(!empty($maneger['openid'])) {
			//通知平台管理员
			$tips = "您好,【{$maneger['nickname']}】,配送员【{$deliveryer['title']}】申请提现,请尽快处理";
			$remark = array(
				"申请　人: " . $deliveryer['title'],
				"手机　号: " . $deliveryer['mobile'],
				"手续　费: " . $log['take_fee'],
				"实际到账: " . $log['final_fee'],
				$note
			);
			$params = array(
				'first' => $tips,
				'money' => $log['get_fee'],
				'timet' => date('Y-m-d H:i', TIMESTAMP),
				'remark' => implode("\n", $remark)
			);
			$send = sys_wechat_tpl_format($params);
			$status = $acc->sendTplNotice($maneger['openid'], $_W['we7_wmall_plus']['config']['notice']['getcash_apply_tpl'], $send);
		}
	} elseif($type == 'success') {
		if(empty($deliveryer['openid'])) {
			return error(-1, '配送员信息不完善');
		}
		$tips = "您好,【{$deliveryer['title']}】,您的账户余额提现已处理";
		$remark = array(
			"处理时间: " . date('Y-m-d H:i', $log['endtime']),
			"真实姓名: " . $deliveryer['title'],
			"手续　费: " . $log['take_fee'],
			"实际到账: " . $log['final_fee'],
			'如有疑问请及时联系平台管理人员'
		);
		$params = array(
			'first' => $tips,
			'money' => $log['take_fee'],
			'timet' => date('Y-m-d H:i', $log['addtime']),
			'remark' => implode("\n", $remark)
		);
		$send = sys_wechat_tpl_format($params);
		$status = $acc->sendTplNotice($deliveryer['openid'], $_W['we7_wmall_plus']['config']['notice']['getcash_success_tpl'], $send);
	} elseif($type == 'fail') {
		if(empty($deliveryer['openid'])) {
			return error(-1, '配送员信息不完善');
		}
		$tips = "您好,【{$deliveryer['title']}】, 您的账户余额提现已处理, 提现未成功";
		$remark = array(
			"处理时间: " . date('Y-m-d H:i', $log['endtime']),
			"真实姓名: " . $deliveryer['title'],
			"手续　费: " . $log['take_fee'],
			"实际到账: " . $log['final_fee'],
			'如有疑问请及时联系平台管理人员'
		);
		$params = array(
			'first' => $tips,
			'money' => $log['get_fee'],
			'time' => date('Y-m-d H:i', $log['addtime']),
			'remark' => implode("\n", $remark)
		);
		$send = sys_wechat_tpl_format($params);
		$status = $acc->sendTplNotice($deliveryer['openid'], $_W['we7_wmall_plus']['config']['notice']['getcash_fail_tpl'], $send);
	}
	return $status;
}

function date2week($timestamp) {
$weekdays = array('周日', '周一', '周二', '周三', '周四', '周五', '周六');
	$week = date('w', $timestamp);
	return $weekdays[$week];
}

function media_id2url($media_id) {
	mload()->classs('wxaccount');
	$acc = new WxAccount();
	$data = $acc->media_download($media_id);
	if(is_error($data)) {
		return $data;
	}
	return $data;
}

function ierror($result_code, $result_message = '调用接口成功', $data = array('resultCode' => '')) {
	$result = array(
		'resultCode' => $result_code,
		'resultMessage' => $result_message,
		'data' => $data,
	);
	return $result;
}

function Jpush_deliveryer_send($title, $alert, $extras = array(), $audience = '', $platform = 'all') {
	global $_W;
	$config = $_W['we7_wmall_plus']['config'];
	if(empty($config['app']['deliveryer']['push_key']) || empty($config['app']['deliveryer']['push_secret'])) {
		return error(-1, 'key或secret不完善');
	}
	if(empty($config['app']['deliveryer']['serial_sn'])) {
		return error(-1, 'app序列号不完善');
	}
	$tag = trim($config['app']['deliveryer']['serial_sn']);
	if(empty($audience)) {
		$audience = array(
			'tag' => array(
				$tag
			)
		);
	}
	$extras_orginal = array(
		'voice_play_nums' => 1,
		'voice_text' => '',
		'redirect_type' => 'order',
		'redirect_extra' => '',
	);
	$extras = array_merge($extras_orginal, $extras);
	$jpush_andriod = array(
		'platform' => 'android',
		'audience' => $audience,
		'notification' => array(
			'alert' => $alert,
			'android' => array(
				'alert' => $alert,
				'title' => $title,
				'builder_id' => 1,
				'extras' => $extras
			)
		),
	);
	$jpush_ios = array(
		'platform' => 'ios',
		'audience' => $audience,
		'notification' => array(
			'alert' => $alert,
			'ios' => array(
				'alert' => $alert,
				'sound' => 'default',
				'badge' => '+1',
				'extras' => $extras
			),
		),
		'options' => array(
			'apns_production' => 1
		),
	);
	load()->func('communication');
	$extra = array(
		'Authorization' => "Basic " . base64_encode("{$config['app']['deliveryer']['push_key']}:{$config['app']['deliveryer']['push_secret']}")
	);
	$response = ihttp_request('https://api.jpush.cn/v3/push', json_encode($jpush_andriod), $extra);
	if(empty($config['app']['deliveryer']['ios_build_type'])) {
		$extra = array('Authorization' => "Basic ZWQ4YzE3YmM3YjJlOWYzMGEyYWZlMThiOjIxZmM5ZjBiOGU4ODRmNDUzOTMxN2MyZQ==");
	}
	$response = ihttp_request('https://api.jpush.cn/v3/push', json_encode($jpush_ios), $extra);
	if(is_error($response)) {
		return $response;
	}
	$result = @json_decode($response['content'], true);
	if(!empty($result['error'])) {
		return error(-1, "错误代码: {$result['error']['code']}, 错误信息: {$result['error']['message']}");
	}
	return true;
}

function array_sort($array, $sort_key, $sort_order = SORT_ASC) {
	if(is_array($array)){
		foreach ($array as $row_array){
			$key_array[] = $row_array[$sort_key];
		}
		array_multisort($key_array, $sort_order, $array);
		return $array;
	}
	return false;
}

function set_config_text($name, $value = '') {
	global $_W;
	$config = pdo_get('tiny_wmall_plus_text', array('uniacid' => $_W['uniacid'], 'name' => $name));
	if(empty($config)) {
		$data = array(
			'uniacid' => $_W['uniacid'],
			'name' => $name,
			'value' => $value,
		);
		pdo_insert('tiny_wmall_plus_text', $data);
	} else {
		$data = array(
			'uniacid' => $_W['uniacid'],
			'value' => $value,
		);
		pdo_update('tiny_wmall_plus_text', $data, array('uniacid' => $_W['uniacid'], 'name' => $name));
	}
	return true;
}

function get_config_text($name) {
	global $_W;
	$config = pdo_get('tiny_wmall_plus_text', array('uniacid' => $_W['uniacid'], 'name' => $name));
	if($name = 'takeout_delivery_time') {
		$config['value'] = iunserializer($config['value']);
	}
	return $config['value'];
}

function category_store_label() {
	global $_W;
	$data = pdo_fetchall('select id, title, alias,  color, is_system, displayorder from' . tablename('tiny_wmall_plus_category') . ' where uniacid = :uniacid and type = :type order by is_system desc, displayorder desc', array(':uniacid' => $_W['uniacid'], ':type' => 'TY_store_label'), 'id');
	return $data;
}

function sys_store_cron() {}


