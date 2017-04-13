<?php
/**
 * 外送系统
 * @author 新睿社区
 * @QQ 10373458 * @url http://www.010xr.com/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$_W['page']['title'] = '门店列表-' . $_W['we7_wmall_plus']['config']['title'];
mload()->model('store');

$sid = $store['id'];
$do = 'store';
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'list';
if($_GPC['__sid'] > 0) {
	$csotre = pdo_get('tiny_wmall_plus_store', array('uniacid' => $_W['uniacid'], 'id' => $_GPC['__sid']), array('title'));
	if(empty($csotre)) {
		$_GPC['__sid'] = 0;
	}
}
if($_W['role'] == 'operator') {
	$id = intval($_GPC['id']);
	if($_W['we7_wmall_plus']['store']['id'] != $id && $id > 0) {
		message('您没有该门店的管理权限', referer(), 'error');
	}
}

if($op == 'post') {
	$id = intval($_GPC['id']);
	if($id) {
		$item = store_fetch($id);
		if(empty($item)) {
			message('门店信息不存在或已删除', 'referer', 'error');
		} else {
			$item['map'] = array('lat' => $item['location_x'], 'lng' => $item['location_y']);
			$item['cid'] = array_filter(explode('|', $item['cid']));
		}
		$sys_url = murl('entry', array('m' => 'we7_wmall_plus', 'do' => 'store', 'sid' => $item['id']), true, true);
		$wx_url = $item['wechat_qrcode']['url'];
	} else {
		if($_W['role'] == 'operator') {
			message('您没有该添加门店的权限', referer(), 'error');
		}
		$item['business_hours'] = array(array('s' => '8:00', 'e' => '24:00'));
		$item['sns'] = array();
		$item['mobile_verify'] = array();
		$item['payment'] = array();
		$item['remind_time_limit'] = 10;
		$item['status'] = 1;
		$item['remind_reply'] = array(
			'快递员狂奔在路上,请耐心等待'
		);
		$item['delivery_mode'] = 1;
		$item['delivery_fee_mode'] = 1;
		$item['qualification'] = array();
	}
	if(checksubmit('submit')) {
		$data = array(
			'title' => trim($_GPC['title']),
			'logo' => trim($_GPC['logo']),
			'telephone' => trim($_GPC['telephone']),
			'description' => htmlspecialchars_decode($_GPC['description']),
			'pack_price' =>trim($_GPC['pack_price']),
			'delivery_area' => trim($_GPC['delivery_area']),
			'address' =>  trim($_GPC['address']),
			'location_x' => $_GPC['map']['lat'],
			'location_y' => $_GPC['map']['lng'],
			'displayorder' => intval($_GPC['displayorder']),
			'notice' => trim($_GPC['notice']),
			'tips' => trim($_GPC['tips']),
			'content' => trim($_GPC['content']),
			'sns' => iserializer(array(
				'qq' => trim($_GPC['sns']['qq']),
				'weixin' => trim($_GPC['sns']['weixin']),
			)),
			'invoice_status' => intval($_GPC['invoice_status']),
			'pc_notice_status' => intval($_GPC['pc_notice_status']),
			'token_status' => intval($_GPC['token_status']),
			'comment_status' => intval($_GPC['comment_status']),
			'payment' => iserializer($_GPC['payment']),
			'remind_time_limit' => intval($_GPC['remind_time_limit']),
			'delivery_type' => intval($_GPC['delivery_type']),
			'delivery_within_days' => intval($_GPC['delivery_within_days']),
			'delivery_reserve_days' => intval($_GPC['delivery_reserve_days']),
			'auto_handel_order' => intval($_GPC['auto_handel_order']),
			'auto_notice_deliveryer' => intval($_GPC['auto_notice_deliveryer']),
			'is_meal' => intval($_GPC['is_meal']),
			'is_assign' => intval($_GPC['is_assign']),
			'is_reserve' => intval($_GPC['is_reserve']),
			'forward_mode' => intval($_GPC['forward_mode']),
			'forward_url' => trim($_GPC['forward_url']),
			'qualification' => iserializer(array(
				'business' => array(
					'thumb' => trim($_GPC['qualification']['business']),
				),
				'service' => array(
					'thumb' => trim($_GPC['qualification']['service']),
				)
			)),
		);
		if($data['forward_mode'] == 5 && empty($data['forward_url'])) {
			$data['forward_mode'] = 0;
		}
		$cids = array();
		if(!empty($_GPC['cid'])) {
			foreach($_GPC['cid'] as $cid) {
				$cid = intval($cid);
				if($cid > 0) {
					$cids[] = $cid;
				}
			}
		}
		$cids = implode('|', $cids);
		$data['cid'] = "|{$cids}|";

		$serve_fee = array(
			'type' => intval($_GPC['serve_fee']['type']),
			'fee' => 0
		);
		if($serve_fee['type'] == 1) {
			$serve_fee['fee'] = trim($_GPC['serve_fee']['fee_1']);
		} else {
			$serve_fee['fee'] = trim($_GPC['serve_fee']['fee_2']);
		}
		$data['serve_fee'] = iserializer($serve_fee);
		if($item['delivery_mode'] == 1) {
			$data['delivery_fee_mode'] = intval($_GPC['delivery_fee_mode']);
			$data['delivery_price'] = intval($_GPC['delivery_price']);
			$data['delivery_free_price'] = intval($_GPC['delivery_free_price']);
			$data['auto_get_address'] = intval($_GPC['auto_get_address']);
			$data['send_price'] = intval($_GPC['send_price']);
			$data['pack_price'] = trim($_GPC['pack_price']);
			$data['delivery_time'] = intval($_GPC['delivery_time']);
			$data['serve_radius'] = intval($_GPC['serve_radius']);
			$data['not_in_serve_radius'] = intval($_GPC['not_in_serve_radius']);
			if(!$data['not_in_serve_radius']) {
				$data['auto_get_address'] = 1;
				if(empty($data['serve_radius'])) {
					message('您设置了超出配送费范围不允许下单, 此项设置需要设置门店的的配送半径', '', 'info');
				}
			}

			if($data['delivery_fee_mode'] == 1) {
				$data['delivery_price'] = trim($_GPC['delivery_price']);
			} else {
				$data['auto_get_address'] = 1;
				$data['not_in_serve_radius'] = intval($_GPC['not_in_serve_radius']);
				$data['delivery_price'] = iserializer(array(
					'start_fee' => trim($_GPC['start_fee']),
					'start_km' => trim($_GPC['start_km']),
					'pre_km_fee' => trim($_GPC['pre_km_fee']),
				));
			}
			$times = array();
			if(!empty($_GPC['times']['start'])) {
				foreach($_GPC['times']['start'] as $key => $val) {
					$start = trim($val);
					$end = trim($_GPC['times']['end'][$key]);
					if(empty($start) || empty($end)) {
						continue;
					}
					$times[] = array(
						'start' => $start,
						'end' => $end,
						'status' => intval($_GPC['times']['status'][$key]),
						'fee' => intval($_GPC['times']['fee'][$key])
					);
				}
				$data['delivery_times'] = iserializer($times);
			}
		}
		$hour = array();
		if(!empty($_GPC['business_start_hours'])) {
			$hour = array();
			foreach($_GPC['business_start_hours'] as $k => $v) {
				$v = str_replace('：', ':', trim($v));
				if(!strexists($v, ':')) {
					$v .= ':00';
				}
				$end = str_replace('：', ':', trim($_GPC['business_end_hours'][$k]));
				if(!strexists($end, ':')) {
					$end.= ':00';
				}
				$hour[] = array('s' => $v, 'e' => $end);
			}
		}
		$data['business_hours'] = iserializer($hour);

		if(!empty($_GPC['thumbs']['image'])) {
			$thumbs = array();
			foreach($_GPC['thumbs']['image'] as $key => $image) {
				if(empty($image)) {
					continue;
				}
				$thumbs[] = array(
					'image' => $image,
					'url' => trim($_GPC['thumbs']['url'][$key]),
				);
			}
			$data['thumbs'] = iserializer($thumbs);
		} else {
			$data['thumbs'] = '';
		}
		if(!empty($_GPC['remind_reply'])) {
			$remind_reply = array();
			foreach($_GPC['remind_reply'] as $reply) {
				$reply = trim($reply);
				if(empty($reply)) {
					continue;
				}
				$remind_reply[] = $reply;
			}
			$data['remind_reply'] = iserializer($remind_reply);
		} else {
			$data['remind_reply'] = '';
		}
		if(!empty($_GPC['comment_reply'])) {
			$remind_reply = array();
			foreach($_GPC['comment_reply'] as $reply) {
				$reply = trim($reply);
				if(empty($reply)) {
					continue;
				}
				$comment_reply[] = $reply;
			}
			$data['comment_reply'] = iserializer($comment_reply);
		} else {
			$data['comment_reply'] = iserializer(array());
		}

		$data['order_note'] = array();
		if(!empty($_GPC['order_note'])) {
			foreach($_GPC['order_note'] as $order_note) {
				if(empty($order_note)) continue;
				$data['order_note'][] = $order_note;
			}
		}
		$data['order_note'] = iserializer($data['order_note']);

		if(!empty($_GPC['custom_title'])) {
			$custom_url = array();
			foreach($_GPC['custom_title'] as $key => $title) {
				$title = trim($title);
				$url = trim($_GPC['custom_link'][$key]);
				if(empty($title) || empty($url)) {
					continue;
				}
				$custom_url[] = array('title' => $title, 'url' => $url);
			}
			$data['custom_url'] = iserializer($custom_url);
		} else {
			$data['custom_url'] = iserializer(array());
		}
		if($id) {
			pdo_update('tiny_wmall_plus_store', $data, array('uniacid' => $_W['uniacid'], 'id' => $id));
			$sid = $id;
		} else {
			$data['uniacid'] = $_W['uniacid'];
			$data['addtime'] = TIMESTAMP;
			pdo_insert('tiny_wmall_plus_store', $data);
			$sid = pdo_insertid();

			//添加门店账户数据
			$config_settle = $_W['we7_wmall_plus']['config']['settle'];
			$config_takeout = $_W['we7_wmall_plus']['config']['takeout'];
			$store_account = array(
				'uniacid' => $_W['uniacid'],
				'sid' => $sid,
				'instore_serve_rate' => intval($config_settle['instore_serve_rate']),
				'takeout_serve_rate' => intval($config_settle['takeout_serve_rate']),
				'fee_limit' => $config_settle['get_cash_fee_limit'],
				'fee_rate' => $config_settle['get_cash_fee_rate'],
				'fee_min' => $config_settle['get_cash_fee_min'],
				'fee_max' => $config_settle['get_cash_fee_max'],
			);
			$id = pdo_insert('tiny_wmall_plus_store_account', $store_account);

			$update = array(
				'delivery_mode' => $config_takeout['delivery_mode'],
				'delivery_fee_mode' => 1,
				'delivery_price' => $config_takeout['delivery_fee'],
			);
			if($config_takeout['delivery_fee_mode'] == 2) {
				$update['delivery_fee_mode'] = 2;
				$update['delivery_price'] = iserializer($update['delivery_price']);
			}
			pdo_update('tiny_wmall_plus_store', $update, array('uniacid' => $_W['uniacid'], 'id' => $sid));
		}
		store_delivery_times($sid, true);
		message('编辑门店信息成功', $this->createWebUrl('store', array('op' => 'list')), 'success');
	}
	
	$categorys = store_fetchall_category();
	$pay = $_W['we7_wmall_plus']['config']['payment'];
	if(empty($pay)) {
		message('公众号没有设置支付方式,请先设置支付方式', $this->createWebUrl('ptfconfig', array('op' => 'pay')), 'info');
	}
}

if($op == 'list') {
	$store_label = category_store_label();
	$condition = ' uniacid = :uniacid';
	$params[':uniacid'] = $_W['uniacid'];
	$cid = intval($_GPC['cid']);
	if($cid > 0) {
		$condition .= " AND cid LIKE :cid";
		$params[':cid'] = "%|{$cid}|%";
	}
	$label = intval($_GPC['label']);
	if($label > 0) {
		$condition .= " AND label = :label";
		$params[':label'] = $label;
	}
	if($_W['role'] == 'operator') {
		$condition .= " AND id = :id";
		$params[':id'] = $_W['we7_wmall_plus']['store']['id'];
	}
	if(!empty($_GPC['keyword'])) {
		$condition .= " AND title LIKE '%{$_GPC['keyword']}%'";
	}

	$pindex = max(1, intval($_GPC['page']));
	$psize = 20;

	$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('tiny_wmall_plus_store') . ' WHERE ' . $condition, $params);
	$lists = pdo_fetchall('SELECT * FROM ' . tablename('tiny_wmall_plus_store') . ' WHERE ' . $condition . ' ORDER BY displayorder DESC LIMIT '.($pindex - 1) * $psize.','.$psize, $params);
	$pager = pagination($total, $pindex, $psize);
	if(!empty($lists)) {
		foreach($lists as &$li) {
			$li['cid'] = explode('|', $li['cid']);
			$li['address'] = str_replace('+', ' ', $li['district']) . ' ' . $li['address'];
			$li['sys_url'] = murl('entry', array('m' => 'we7_wmall_plus', 'do' => 'store', 'sid' => $li['id']), true, true);
			$li['wechat_qrcode'] = (array)iunserializer($li['wechat_qrcode']);
			$li['wechat_url'] = $li['wechat_qrcode']['url'];
			$li['user'] = pdo_get('tiny_wmall_plus_clerk', array('uniacid' => $_W['uniacid'], 'sid' => $li['id'], 'role' => 'manager'));
		}
	}
	$categorys = store_fetchall_category();
	$store_status = store_status();
}

if($op == 'template') {
	$sid = intval($_GPC['id']);
	$template = trim($_GPC['t']) ? trim($_GPC['t']) : 'index';
	pdo_update('tiny_wmall_plus_store', array('template' => $template), array('uniacid' => $_W['uniacid'], 'id' => $sid));
	message('设置页面风格成功', referer(), 'success');
}

if($op == 'status') {
	if($_W['isajax']) {
		$sid = intval($_GPC['id']);
		$status = intval($_GPC['status']);
		pdo_update('tiny_wmall_plus_store', array('status' => $status), array('uniacid' => $_W['uniacid'], 'id' => $sid));
		exit();
	}
}

if($op == 'recommend') {
	if($_W['isajax']) {
		$sid = intval($_GPC['id']);
		$recommend = intval($_GPC['recommend']);
		pdo_update('tiny_wmall_plus_store', array('is_recommend' => $recommend), array('uniacid' => $_W['uniacid'], 'id' => $sid));
		exit();
	}
}

if($op == 'label') {
	if($_W['isajax']) {
		$sid = intval($_GPC['id']);
		$label = intval($_GPC['label']);
		pdo_update('tiny_wmall_plus_store', array('label' => $label), array('uniacid' => $_W['uniacid'], 'id' => $sid));
		exit();
	}
}

if($op == 'is_in_business') {
	if($_W['isajax']) {
		$sid = intval($_GPC['id']);
		$is_in_business = intval($_GPC['is_in_business']);
		pdo_update('tiny_wmall_plus_store', array('is_in_business' => $is_in_business), array('uniacid' => $_W['uniacid'], 'id' => $sid));
		exit();
	}
}

if($op == 'edit') {
	if($_W['role'] != 'manager' && empty($_W['isfounder']) && !$_W['isajax']) {
		exit('error');
	}
	$type = trim($_GPC['type']);
	if(!in_array($type, array('displayorder', 'click'))) {
		exit('error');
	}
	$sid = intval($_GPC['sid']);
	$value = intval($_GPC['value']);
	pdo_update('tiny_wmall_plus_store', array($type => $value), array('uniacid' => $_W['uniacid'], 'id' => $sid));
	exit('success');
}

if($op == 'copy') {
	set_time_limit(0);
	if($_W['role'] != 'manager' && empty($_W['isfounder'])) {
		message('您没有复制门店的权限', referer(), 'error');
	}
	$sid = intval($_GPC['sid']);
	$store = pdo_get('tiny_wmall_plus_store', array('uniacid' => $_W['uniacid'], 'id' => $sid));
	if(empty($store)) {
		message('门店不存在或已删除', referer(), 'error');
	}
	$store['title'] = $store['title'] . "-复制";
	unset($store['id'], $store['wechat_qrcode'], $store['assign_qrcode']);
	pdo_insert('tiny_wmall_plus_store', $store);
	$store_id = pdo_insertid();

	//门店账户
	$config_settle = $_W['we7_wmall_plus']['config']['settle'];
	$store_account = array(
		'uniacid' => $_W['uniacid'],
		'sid' => $store_id,
		'fee_limit' => $config_settle['get_cash_fee_limit'],
		'fee_rate' => $config_settle['get_cash_fee_rate'],
		'fee_min' => $config_settle['get_cash_fee_min'],
		'fee_max' => $config_settle['get_cash_fee_max'],
	);
	pdo_insert('tiny_wmall_plus_store_account', $store_account);

	//配送员数据
	$deliveryers = pdo_getall('tiny_wmall_plus_store_deliveryer', array('uniacid' => $_W['uniacid'], 'sid' => $sid));
	if(!empty($deliveryers)) {
		foreach($deliveryers as $deliveryer) {
			unset($deliveryer['id']);
			$deliveryer['sid'] = $store_id;
			pdo_insert('tiny_wmall_plus_store_deliveryer', $deliveryer);
		}
	}
	
	//门店优惠活动
	$activity = pdo_get('tiny_wmall_plus_store_activity', array('uniacid' => $_W['uniacid'], 'sid' => $sid));
	if(!empty($activity)) {
		unset($activity['id']);
		$activity['sid'] = $store_id;
		pdo_insert('tiny_wmall_plus_store_activity', $activity);
	}

	//复制菜品分类
	$goods_categorys = pdo_getall('tiny_wmall_plus_goods_category', array('uniacid' => $_W['uniacid'], 'sid' => $sid));
	if(!empty($goods_categorys)) {
		foreach($goods_categorys as $category) {
			$cid = $category['id'];
			unset($category['id']);
			$category['sid'] = $store_id;
			pdo_insert('tiny_wmall_plus_goods_category', $category);
			$category_id = pdo_insertid();
			$goods = pdo_getall('tiny_wmall_plus_goods', array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'cid' => $cid));
			if(!empty($goods)) {
				foreach($goods as $good) {
					$goods_id = $good['id'];
					unset($good['id']);
					$good['sid'] = $store_id;
					$good['cid'] = $category_id;
					pdo_insert('tiny_wmall_plus_goods', $good);
					$new_goods_id = pdo_insertid();
					if($good['is_options'] == 1) {
						$options = pdo_getall('tiny_wmall_plus_goods_options', array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'goods_id' => $goods_id));
						if(!empty($options)) {
							foreach($options as $option) {
								unset($option['id']);
								$option['sid'] = $store_id;
								$option['goods_id'] = $new_goods_id;
								pdo_insert('tiny_wmall_plus_goods_options', $option);
							}
						}
					}
				}
			}
		}
	}

	//复制桌台类型
	$table_categorys = pdo_getall('tiny_wmall_plus_tables_category', array('uniacid' => $_W['uniacid'], 'sid' => $sid));
	if(!empty($table_categorys)) {
		foreach($table_categorys as $category) {
			$cid = $category['id'];
			unset($category['id']);
			$category['sid'] = $store_id;
			pdo_insert('tiny_wmall_plus_tables_category', $category);
			$category_id = pdo_insertid();
			$tables = pdo_getall('tiny_wmall_plus_tables', array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'cid' => $cid));
			if(!empty($tables)) {
				foreach($tables as $table) {
					unset($table['id']);
					unset($table['qrcode']);
					$table['sid'] = $store_id;
					$table['cid'] = $category_id;
					pdo_insert('tiny_wmall_plus_tables', $table);
				}
			}
			//复制预定
			$reserves = pdo_getall('tiny_wmall_plus_reserve', array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'table_cid' => $cid));
			if(!empty($reserves)) {
				foreach($reserves as $reserve) {
					unset($reserve['id']);
					$reserve['sid'] = $store_id;
					$reserve['table_cid'] = $category_id;
					pdo_insert('tiny_wmall_plus_reserve', $reserve);
				}
			}
		}
	}

	//复制排号
	$assigns = pdo_getall('tiny_wmall_plus_assign_queue', array('uniacid' => $_W['uniacid'], 'sid' => $sid));
	if(!empty($assigns)) {
		foreach($assigns as $assign) {
			unset($assign['id']);
			$assign['sid'] = $store_id;
			pdo_insert('tiny_wmall_plus_assign_queue', $assign);
		}
	}
	message('复制门店成功', referer(), 'success');
}

if($op == 'del') {
	$id = intval($_GPC['id']);
	pdo_delete('tiny_wmall_plus_store', array('uniacid' => $_W['uniacid'], 'id' => $id));
	$tables = array(
		'tiny_wmall_plus_activity_coupon',
		'tiny_wmall_plus_activity_coupon_grant_log',
		'tiny_wmall_plus_activity_coupon_record',
		'tiny_wmall_plus_clerk',
		'tiny_wmall_plus_goods',
		'tiny_wmall_plus_goods_category',
		'tiny_wmall_plus_goods_options',
		'tiny_wmall_plus_order',
		'tiny_wmall_plus_order_cart',
		'tiny_wmall_plus_order_comment',
		'tiny_wmall_plus_order_current_log',
		'tiny_wmall_plus_order_discount',
		'tiny_wmall_plus_order_refund_log',
		'tiny_wmall_plus_order_stat',
		'tiny_wmall_plus_printer',
		'tiny_wmall_plus_reply',
		'tiny_wmall_plus_sms_send_log',
		'tiny_wmall_plus_store_account',
		'tiny_wmall_plus_store_activity',
		'tiny_wmall_plus_store_current_log',
		'tiny_wmall_plus_store_favorite',
		'tiny_wmall_plus_store_getcash_log',
		'tiny_wmall_plus_store_deliveryer',
		'tiny_wmall_plus_store_members',
		'tiny_wmall_plus_tables',
		'tiny_wmall_plus_tables_category',
		'tiny_wmall_plus_reserve',
		'tiny_wmall_plus_assign_board',
		'tiny_wmall_plus_assign_queue',
	);
	foreach($tables as $table) {
		pdo_delete($table, array('uniacid' => $_W['uniacid'], 'sid' => $id));
	}
	message('删除门店成功', referer(), 'success');
}
include $this->template('store/store');