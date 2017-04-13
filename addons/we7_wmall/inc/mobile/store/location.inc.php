<?php
/**
 * 外送系统
 * @author 灯火阑珊
 * @QQ 2471240272
 * @url http://bbs.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$do = 'location';
$title = '我的位置';
$sid = intval($_GPC['sid']);
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'index';

if($op == 'index') {
	if($_W['member']['uid'] > 0) {
		$addresses = pdo_getall('tiny_wmall_address', array('uniacid' => $_W['uniacid'], 'uid' => $_W['member']['uid']));
	}
}

if($op == 'black') {
	order_insert_fansinfo();
}

if($op == 'suggestion') {
	load()->func('communication');
	$key = trim($_GPC['key']);
	$query = array(
		'keywords' => $key,
		'city' => '全国',
		'output' => 'json',
		'key' => '37bb6a3b1656ba7d7dc8946e7e26f39b',
	);
	$type = trim($_GPC['type']);
	if($type == 'errander' && !empty($_W['we7_wmall']['config']['errander']['city'])) {
		$query['city'] = $_W['we7_wmall']['config']['errander']['city'];
	}
	$query = http_build_query($query);
	$result = ihttp_get('http://restapi.amap.com/v3/assistant/inputtips?' . $query);
	if(is_error($result)) {
		message(error(-1, '访问出错'), '', 'ajax');
	}
	$result = @json_decode($result['content'], true);
	if(!empty($result['tips'])) {
		$distance_sort = 0;
		if($type == 'errander' && $_W['we7_wmall']['config']['errander']['serve_radius'] > 0 && !empty($_W['we7_wmall']['config']['errander']['map']['location_x']) && !empty($_W['we7_wmall']['config']['errander']['map']['location_y'])) {
			$distance_sort = 1;
		}

		foreach($result['tips'] as $key => &$val) {
			$val['distance'] = 10000000;
			$val['distance_available'] = 0;
			$val['address_available'] = 1;
			if(is_array($val['location'])) {
				unset($val[$key]);
			} else {
				$location = explode(',', $val['location']);
				$val['lng'] = $location[0];
				$val['lat'] = $location[1];
				if($distance_sort == 1) {
					$val['distance'] = distanceBetween($val['lng'], $val['lat'], $_W['we7_wmall']['config']['errander']['map']['location_y'], $_W['we7_wmall']['config']['errander']['map']['location_x']);
					$val['distance'] = round($val['distance'] / 1000, 2);
					$val['distance_available'] = 1;
					if($val['distance'] > $_W['we7_wmall']['config']['errander']['serve_radius']) {
						$val['address_available'] = 0;
					}
				}
			}
			if(!is_array($val['address'])) {
				$val['address'] = $val['district'] . $val['address'];
			} else {
				$val['address'] = $val['district'];
			}
		}
		if($distance_sort == 1) {
			$result['tips'] = array_sort($result['tips'], 'distance');
		}
	}

	message(error(0, $result['tips']), '', 'ajax');
}

include $this->template('location');
