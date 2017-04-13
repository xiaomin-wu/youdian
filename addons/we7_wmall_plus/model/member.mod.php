<?php

defined('IN_IA') or die('Access Denied');
function member_fetchall_address($filter = array())
{
	global $_W;
	$data = pdo_fetchall("SELECT * FROM " . tablename('tiny_wmall_plus_address') . ' WHERE uniacid = :uniacid AND uid = :uid AND type = 1 ORDER BY is_default DESC,id DESC', array(':uniacid' => $_W['uniacid'], ':uid' => $_W['member']['uid']));
	if (!empty($filter['serve_radius']) && !empty($filter['location_x']) && $filter['location_y']) {
		$available = array();
		$dis_available = array();
		foreach ($data as $li) {
			if (!empty($li['location_x']) && !empty($li['location_y'])) {
				$dist = distanceBetween($li['location_y'], $li['location_x'], $filter['location_y'], $filter['location_x']);
				if ($dist > $filter['serve_radius'] * 1000) {
					$dis_available[] = $li;
				} else {
					$available[] = $li;
				}
			} else {
				$dis_available[] = $li;
			}
		}
		return array('available' => $available, 'dis_available' => $dis_available);
	}
	return $data;
}
function member_fetch_address($id)
{
	global $_W;
	$data = pdo_fetch("SELECT * FROM " . tablename('tiny_wmall_plus_address') . ' WHERE uniacid = :uniacid AND id = :id AND type = 1', array(':uniacid' => $_W['uniacid'], ':id' => $id));
	return $data;
}
function member_fetch_available_address($sid)
{
	global $_W, $_GPC;
	$store = store_fetch($sid);
	$address = array();
	if (!$_GPC['r']) {
		if ($_GPC['__aid'] > 0) {
			$temp = pdo_get('tiny_wmall_plus_address', array('uniacid' => $_W['uniacid'], 'uid' => $_W['member']['uid'], 'id' => intval($_GPC['__aid'])));
			if ($store['order_address_limit'] > 1) {
				if (!empty($temp['location_y']) && !empty($temp['location_x'])) {
					$dist = distanceBetween($temp['location_y'], $temp['location_x'], $store['location_y'], $store['location_x']);
					if ($store['order_address_limit'] == 2 && $dist <= $store['serve_radius'] * 1000 || $store['order_address_limit'] == 3) {
						$temp['distance'] = $dist / 1000;
						$address = $temp;
					}
				}
			} else {
				$address = $temp;
			}
		}
		if (empty($address)) {
			$temp = pdo_get('tiny_wmall_plus_address', array('uniacid' => $_W['uniacid'], 'uid' => $_W['member']['uid'], 'type' => 1, 'is_default' => 1));
			if ($store['order_address_limit'] > 1) {
				if (!empty($temp['location_y']) && !empty($temp['location_x'])) {
					$dist = distanceBetween($temp['location_y'], $temp['location_x'], $store['location_y'], $store['location_x']);
					if ($store['order_address_limit'] == 2 && $dist <= $store['serve_radius'] * 1000 || $store['order_address_limit'] == 3) {
						$temp['distance'] = $dist / 1000;
						$address = $temp;
					}
				}
			} else {
				$address = $temp;
			}
		}
		if (empty($address)) {
			$addresses = member_fetchall_address();
			foreach ($addresses as $li) {
				if ($store['order_address_limit'] > 1) {
					if (!empty($li['location_x']) && !empty($li['location_y'])) {
						$dist = distanceBetween($li['location_y'], $li['location_x'], $store['location_y'], $store['location_x']);
						if ($store['order_address_limit'] == 2 && $dist <= $store['serve_radius'] * 1000 || $store['order_address_limit'] == 3) {
							$li['distance'] = $dist / 1000;
							$address = $li;
							break;
						}
					}
				} else {
					$address = $li;
					break;
				}
			}
		}
	} else {
		$address_id = intval($_GPC['address_id']);
		$temp = member_fetch_address($address_id);
		if ($store['order_address_limit'] > 1) {
			if (!empty($temp['location_y']) && !empty($temp['location_x'])) {
				$dist = distanceBetween($temp['location_y'], $temp['location_x'], $store['location_y'], $store['location_x']);
				if ($store['order_address_limit'] == 2 && $dist <= $store['serve_radius'] * 1000 || $store['order_address_limit'] == 3) {
					$temp['distance'] = $dist / 1000;
					$address = $temp;
				}
			}
		} else {
			$address = $temp;
		}
	}
	return $address;
}
function member_amount_stat($sid)
{
	global $_W;
	$stat = array();
	$today_starttime = strtotime(date('Y-m-d'));
	$yesterday_starttime = $today_starttime - 86400;
	$month_starttime = strtotime(date('Y-m'));
	$stat['yesterday_num'] = intval(pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_plus_store_members') . ' where uniacid = :uniacid and sid = :sid and first_order_time >= :starttime and first_order_time <= :endtime', array(':uniacid' => $_W['uniacid'], ':sid' => $sid, ':starttime' => $yesterday_starttime, ':endtime' => $today_starttime)));
	$stat['today_num'] = intval(pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_plus_store_members') . ' where uniacid = :uniacid and sid = :sid and first_order_time >= :starttime', array(':uniacid' => $_W['uniacid'], ':sid' => $sid, ':starttime' => $today_starttime)));
	$stat['month_num'] = intval(pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_plus_store_members') . ' where uniacid = :uniacid and sid = :sid and first_order_time >= :starttime', array(':uniacid' => $_W['uniacid'], ':sid' => $sid, ':starttime' => $month_starttime)));
	$stat['total_num'] = intval(pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_plus_store_members') . ' where uniacid = :uniacid and sid = :sid', array(':uniacid' => $_W['uniacid'], ':sid' => $sid)));
	return $stat;
}
function member_fetch($uid = 0)
{
	global $_W;
	if (!$uid) {
		$uid = $_W['member']['uid'];
	}
	$member = pdo_get('tiny_wmall_plus_members', array('uniacid' => $_W['uniacid'], 'uid' => $uid));
	if (!empty($member)) {
		$member['search_data'] = iunserializer($member['search_data']);
		if (!is_array($member['search_data'])) {
			$member['search_data'] = array();
		}
	}
	return $member;
}
function member_fetchall_serve_address($filter = array())
{
	global $_W;
	$data = pdo_fetchall("SELECT * FROM " . tablename('tiny_wmall_plus_address') . ' WHERE uniacid = :uniacid AND uid = :uid AND type = 2 ORDER BY is_default DESC,id DESC', array(':uniacid' => $_W['uniacid'], ':uid' => $_W['member']['uid']));
	if (!empty($filter['serve_radius']) && !empty($filter['location_x']) && $filter['location_y']) {
		$available = array();
		$dis_available = array();
		foreach ($data as $li) {
			if (!empty($li['location_x']) && !empty($li['location_y'])) {
				$dist = distanceBetween($li['location_y'], $li['location_x'], $filter['location_y'], $filter['location_x']);
				if ($dist > $filter['serve_radius'] * 1000) {
					$dis_available[] = $li;
				} else {
					$available[] = $li;
				}
			} else {
				$dis_available[] = $li;
			}
		}
		return array('available' => $available, 'dis_available' => $dis_available);
	}
	return $data;
}
function member_fetch_serve_address($id)
{
	global $_W;
	$data = pdo_fetch("SELECT * FROM " . tablename('tiny_wmall_plus_address') . ' WHERE uniacid = :uniacid AND id = :id AND type = 2', array(':uniacid' => $_W['uniacid'], ':id' => $id));
	return $data;
}