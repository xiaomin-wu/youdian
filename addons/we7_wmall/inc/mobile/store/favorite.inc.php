<?php
/**
 * 外送系统
 * @author 灯火阑珊
 * @QQ 2471240272
 * @url http://bbs.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$do = 'favorite';
$this->checkAuth();
mload()->model('store');
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'list';
$title = '我收藏的门店';
if($op == 'list') {
	$stores = pdo_fetchall('select a.id as aid, b.* from ' . tablename('tiny_wmall_store_favorite') . ' as a left join ' . tablename('tiny_wmall_store') . ' as b on a.sid = b.id where a.uniacid = :uniacid and a.uid = :uid order by a.id desc', array(':uniacid' => $_W['uniacid'], ':uid' => $_W['member']['uid']), 'aid');
	$min = 0;
	if(!empty($stores)) {
		foreach($stores as &$da) {
			$da['business_hours'] = (array)iunserializer($da['business_hours']);
			$da['is_in_business_hours'] = store_is_in_business_hours($da['business_hours']);
			$da['hot_goods'] = pdo_fetchall('select title from ' . tablename('tiny_wmall_goods') . ' where uniacid = :uniacid and sid = :sid and is_hot = 1 limit 3', array(':uniacid' => $_W['uniacid'], 'sid' => $da['id']));
			$da['activity'] = store_fetch_activity($da['id']);
			$da['activity']['activity_num'] += ($da['delivery_free_price'] > 0 ? 1 : 0);
			$da['score_cn'] = round($da['score'] / 5, 2) * 100;
			$da['url'] = store_forward_url($da['id'], $da['forward_mode']);
		}
		$min = min(array_keys($stores));
	}
}

if($op == 'more') {
	$id = intval($_GPC['id']);
	$stores = pdo_fetchall('select a.id as aid, b.* from ' . tablename('tiny_wmall_store_favorite') . ' as a left join ' . tablename('tiny_wmall_store') . ' as b on a.sid = b.id where a.uniacid = :uniacid and a.uid = :uid and a.id < :id order by a.id desc limit 4', array(':uniacid' => $_W['uniacid'], ':uid' => $_W['member']['uid'], ':id' => $id), 'aid');
	if(!empty($stores)) {
		foreach($stores as &$store) {
			$store['href'] = $this->createMobileUrl('store', array('id' => $store['id']));
			$store['logo'] = tomedia($store['logo']);
		}
		$min = min(array_keys($stores));
	} else {
		$min = 0;
	}
	$stores = array_values($stores);
	$respon = array('error' => 0, 'message' => $stores, 'min' => $min);
	message($respon, '', 'ajax');
}

include $this->template('favorite');