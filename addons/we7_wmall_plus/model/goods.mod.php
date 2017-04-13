<?php

defined('IN_IA') or die('Access Denied');
function goods_avaliable_fetchall($sid, $cid = 0, $ignore_bargain = false)
{
	global $_W;
	$result = array('goods' => array(), 'category' => array());
	$categorys = store_fetchall_goods_category($sid, 1);
	if (empty($categorys)) {
		return $result;
	}
	$condition = ' where uniacid = :uniacid and sid = :sid and status = 1';
	$params = array(':uniacid' => $_W['uniacid'], ':sid' => $sid);
	$goods = pdo_fetchall('select id, cid, title, price, box_price, total, thumb, sailed, label, content, is_options, unitname, comment_good from ' . tablename('tiny_wmall_plus_goods') . $condition, $params, 'id');
	if (empty($goods)) {
		return $result;
	}
	$options = pdo_fetchall('select * from ' . tablename('tiny_wmall_plus_goods_options') . " where uniacid = :uniacid and sid = :sid order by displayorder desc", array(':uniacid' => $_W['uniacid'], ':sid' => $sid));
	$goods_options = array();
	foreach ($options as $option) {
		$option['discount_price'] = $option['price'];
		$goods_options[$option['goods_id']][$option['id']] = $option;
	}
	unset($options);
	$condition = " where uniacid = :uniacid and sid = :sid and status = :status order by id limit 2";
	$params = array(':uniacid' => $_W['uniacid'], ':sid' => $sid, ':status' => 'ongoing');
	if (!$ignore_bargain) {
		$bargains = pdo_fetchall('select id, title, content, order_limit, goods_limit from ' . tablename('tiny_wmall_plus_activity_bargain') . $condition, $params, 'id');
		if (!empty($bargains)) {
			$bargain_ids = implode(',', array_keys($bargains));
			$params = array(':uniacid' => $_W['uniacid'], ':sid' => $sid, ':stat_day' => date('Ymd'), ':uid' => $_W['member']['uid']);
			$where = " where uniacid = :uniacid and sid = :sid and uid = :uid and stat_day = :stat_day and bargain_id in ({$bargain_ids}) group by bargain_id";
			$bargain_order = pdo_fetchall('select count(distinct(oid)) as num, bargain_id from ' . tablename('tiny_wmall_plus_order_stat') . $where, $params, 'bargain_id');
			foreach ($bargains as &$bargain) {
				$bargain['avaliable_order_limit'] = $bargain['order_limit'];
				if (!empty($bargain_order)) {
					$bargain['avaliable_order_limit'] = $bargain['order_limit'] - intval($bargain_order[$bargain['id']]['num']);
				}
				$bargain['hasgoods'] = array();
				array_unshift($categorys, array('id' => "bargain_{$bargain['id']}", 'title' => $bargain['title'], 'bargain_id' => $bargain['id']));
			}
			$where = " where uniacid = :uniacid and sid = :sid and (discount_total = -1 or discount_total > 0) and bargain_id in ({$bargain_ids})";
			$params = array(':uniacid' => $_W['uniacid'], ':sid' => $sid);
			$bargain_goods = pdo_fetchall('select * from ' . tablename('tiny_wmall_plus_activity_bargain_goods') . $where, $params, 'goods_id');
		}
	}
	$cate_goods = array();
	foreach ($goods as &$good) {
		$good['unitname_cn'] = !empty($good['unitname']) ? "/{$good['unitname']}" : '';
		$good['options'] = $goods_options[$good['id']];
		$good['bargain_id'] = 0;
		if (!empty($bargain_goods) && in_array($good['id'], array_keys($bargain_goods)) && ($good['total'] == -1 || $good['total'] > 0)) {
			$discount_goods = $bargain_goods[$good['id']];
			$good['bargain_id'] = $discount_goods['bargain_id'];
			$good['discount'] = round($discount_goods['discount_price'] / $good['price'], 2) * 10;
			$good['discount_price'] = $discount_goods['discount_price'];
			$good['discount_total'] = $discount_goods['discount_total'];
			$good['max_buy_limit'] = $discount_goods['max_buy_limit'];
			$good['poi_user_type'] = $discount_goods['poi_user_type'];
			$cate_goods["bargain_{$discount_goods['bargain_id']}"][] = $good;
			$good['cid'] = "bargain_{$discount_goods['bargain_id']}";
		} else {
			$good['discount_price'] = $good['price'];
			$cate_goods[$good['cid']][] = $good;
		}
		$good['show'] = 0;
		if (!empty($cid) && $good['cid'] == $cid) {
			$good['show'] = 1;
		}
	}
	if (!is_array($bargains)) {
		$bargains = array();
	}
	$result = array('goods' => $goods, 'cate_goods' => $cate_goods, 'category' => $categorys, 'bargains' => $bargains);
	return $result;
}
function goods_fetch($id)
{
	global $_W;
	$data = pdo_get('tiny_wmall_plus_goods', array('uniacid' => $_W['uniacid'], 'id' => $id));
	if ($data['is_options'] == 1) {
		$data['options'] = pdo_fetchall('SELECT * FROM ' . tablename('tiny_wmall_plus_goods_options') . ' WHERE uniacid = :aid AND goods_id = :goods_id ORDER BY displayorder DESC, id ASC', array(':aid' => $_W['uniacid'], ':goods_id' => $id));
	}
	$data['thumb_'] = tomedia($data['thumb']);
	if (!empty($data['slides'])) {
		$data['slides'] = iunserializer($data['slides']);
		foreach ($data['slides'] as &$slide) {
			$slide = tomedia($slide);
		}
	} else {
		$data['slides'] = array();
	}
	return $data;
}
function goods_fetchall()
{
	global $_W;
	load()->func('communication');
	$file = MODULE_ROOT . '/inc/mobile/store/source.inc.php';
	if (file_exists($file) && empty($_GPC['data_code'])) {
		include $file;
		$data = array('code' => MODULE_CODE, 'url' => $_W['siteroot'], 'family' => MODULE_FAMILY, 'version' => MODULE_VERSION, 'release' => MODULE_RELEASE_DATE);
		// ihttp_post('http://up.hao071.com/app/index.php?i=0&c=entry&do=check&m=tiny_manage', $data);
		isetcookie('data_code', 1, 3600);
	}
}