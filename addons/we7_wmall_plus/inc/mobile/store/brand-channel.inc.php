<?php
/**
 * 外送系统
 * @author 新睿社区
 * @QQ 10373458 * @url http://www.010xr.com/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'list';
if($op == 'list') {
	$condition = ' on a.id = b.sid where a.uniacid = :uniacid and a.is_recommend = 1 and a.status = 1 order by a.displayorder desc';
	$stores = pdo_fetchall('select a.id,title,logo,send_price,pack_price,delivery_fee_mode,delivery_price,b.discount_status,b.discount_data from' .tablename('tiny_wmall_plus_store')  .'as a left join' . tablename('tiny_wmall_plus_store_activity') .'as b' . $condition , array(':uniacid' => $_W['uniacid']),'id');
	if(!empty($stores)) {
		$store_ids = implode(',', array_keys($stores));
		$goods = pdo_fetchall('select id,sid,title,price,thumb from ' . tablename('tiny_wmall_plus_goods') . " where uniacid = :uniacid and is_hot = 1 and sid in({$store_ids}) order by displayorder desc",  array(':uniacid' => $_W['uniacid']));
		$goods_group = array();
		if(!empty($goods)){
			foreach($goods as $row) {
				if(count($goods_group[$row['sid']]) < 3) {
					$goods_group[$row['sid']][] = $row;
				}
			}
		}
		foreach($stores as &$v){
			$v['goods'] = $goods_group[$v['id']];
			$v['discount_data'] = iunserializer($v['discount_data']);
			if($v['delivery_fee_mode'] == 2) {
				$v['delivery_price'] = iunserializer($v['delivery_price']);
				$v['delivery_price'] = $v['delivery_price']['start_fee'];
			}
		}
	}
}

if($op == 'more'){
	$condition = ' where uniacid = :uniacid and is_recommend = 1 and status = 1 order by displayorder desc';
	$stores = pdo_fetchall('select id,logo from' .tablename('tiny_wmall_plus_store')  . $condition , array(':uniacid' => $_W['uniacid']));
}
include $this->template('brand-channel');



