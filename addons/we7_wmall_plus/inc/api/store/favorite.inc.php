<?php
/**
 * 外送系统
 * @author 新睿社区
 * @QQ 10373458 * @url http://www.010xr.com/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$do = 'favorite';
$this->icheckAuth();
mload()->model('store');
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'list';
if($op == 'list') {
	$condition = ' where a.uniacid = :uniacid and a.uid = :uid';
	$params = array(':uniacid' => $_W['uniacid'], ':uid' => $_W['member']['uid']);
	$id = intval($_GPC['id']);
	if($id > 0) {
		$condition .= ' and a.id < :id';
		$params[':id'] = $id;
	}
	$stores = pdo_fetchall('select a.id as aid, b.* from ' . tablename('tiny_wmall_plus_store_favorite') . ' as a left join ' . tablename('tiny_wmall_plus_store') . " as b on a.sid = b.id {$condition} order by a.id desc", $params, 'aid');
	$min = 0;

	if(!empty($stores)) {
		foreach($stores as &$da) {
			$da['business_hours'] = (array)iunserializer($da['business_hours']);
			$da['is_in_business_hours'] = store_is_in_business_hours($da['business_hours']);
			$da['hot_goods'] = pdo_fetchall('select title from ' . tablename('tiny_wmall_plus_goods') . ' where uniacid = :uniacid and sid = :sid and is_hot = 1 limit 3', array(':uniacid' => $_W['uniacid'], 'sid' => $da['id']));
			$da['activity'] = store_fetch_activity($da['id']);
			$da['activity']['activity_num'] += ($da['delivery_free_price'] > 0 ? 1 : 0);
			$da['score_cn'] = round($da['score'] / 5, 2) * 100;
			$da['url'] = store_forward_url($da['id'], $da['forward_mode'], $da['forward_url']);
			if($da['delivery_fee_mode'] == 2) {
				$da['delivery_price'] = iunserializer($da['delivery_price']);
				$da['delivery_price'] = $da['delivery_price']['start_fee'];
			}
			$da['logo'] = tomedia($da['logo']);
		}
		$min = min(array_keys($stores));
	}
	$stores = array_values($stores);
	$data = array(
		'store' => $stores,
		'min_id' => $min,
	);
	message(ierror(0, '', $data), '', 'ajax');
}
