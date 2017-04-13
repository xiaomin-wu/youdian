<?php
/**
 * 外送系统
 * @author 灯火阑珊
 * @QQ 2471240272
 * @url http://bbs.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
mload()->model('store');
$do = 'store';
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'index';

$id = $sid = intval($_GPC['sid']);
$store = store_fetch($id);
$_share = array(
	'title' => $store['title'],
	'desc' => $store['content'],
	'imgUrl' => tomedia($store['logo'])
);

if($_W['member']['uid'] > 0) {
	$is_favorite = pdo_get('tiny_wmall_store_favorite', array('uniacid' => $_W['uniacid'], 'uid' => $_W['member']['uid'], 'sid' => $id));
}
$activity = store_fetch_activity($sid);
if($op == 'index') {
	$title = "{$store['title']}-商户详情";
	include $this->template('store-info');
}

if($op == 'comment') {
	$stat = store_comment_stat($sid);
	$title = "{$store['title']}-评价列表";
	$stat['all'] = intval(pdo_fetchcolumn('select count(*) as num from ' . tablename('tiny_wmall_order_comment') . ' where uniacid = :uniacid and sid = :sid and status = 1', array(':uniacid' => $_W['uniacid'], ':sid' => $sid)));
	$stat['good'] = intval(pdo_fetchcolumn('select count(*) as num from ' . tablename('tiny_wmall_order_comment') . ' where uniacid = :uniacid and sid = :sid and status = 1 and score >= 8', array(':uniacid' => $_W['uniacid'], ':sid' => $sid)));
	$stat['middle'] = intval(pdo_fetchcolumn('select count(*) as num from ' . tablename('tiny_wmall_order_comment') . ' where uniacid = :uniacid and sid = :sid and status = 1 and score >= 4 and score <= 7', array(':uniacid' => $_W['uniacid'], ':sid' => $sid)));
	$stat['bad'] = intval(pdo_fetchcolumn('select count(*) as num from ' . tablename('tiny_wmall_order_comment') . ' where uniacid = :uniacid and sid = :sid and status = 1 and score <= 3', array(':uniacid' => $_W['uniacid'], ':sid' => $sid)));
	
	$condition = ' where a.uniacid = :uniacid and a.sid = :sid and a.status = 1';
	$params = array(':uniacid' => $_W['uniacid'], ':sid' => $sid);
	$type = intval($_GPC['type']);
	if($type == 1) {
		$condition .= ' and a.score >= 8';
	} elseif($type == 2) {
		$condition .= ' and a.score >= 4 and a.score <= 7';
	} elseif($type == 3) {
		$condition .= ' and a.score <= 3';
	}
	$note = intval($_GPC['note']);
	if($note > 0) {
		$condition .= " and a.note != ''";
	}
	$aid = intval($_GPC['aid']);
	if($aid > 0) {
		$condition .= " and a.id < :id";
		$params[':id'] = $aid;
	}
	$comments = pdo_fetchall('select a.id as aid, a.*, b.title from ' . tablename('tiny_wmall_order_comment') . ' as a left join ' . tablename('tiny_wmall_store') . ' as b on a.sid = b.id ' . $condition . ' order by a.id desc limit 15', $params, 'aid');
	$min = 0;
	if(!empty($comments)) {
		foreach ($comments as &$row) {
			$row['data'] = iunserializer($row['data']);
			$row['score'] = ($row['delivery_service'] + $row['goods_quality']) * 10;
			$row['mobile'] = str_replace(substr($row['mobile'], 4, 4), '****', $row['mobile']);
			$row['addtime'] = date('Y-m-d H:i', $row['addtime']);
			$row['replytime'] = date('Y-m-d H:i', $row['replytime']);
			$row['avatar'] = tomedia($row['avatar']) ? tomedia($row['avatar']) : '../addons/we7_wmall/resource/app/img/head.png';
			$row['thumbs'] = iunserializer($row['thumbs']);
			if(!empty($row['thumbs'])) {
				foreach($row['thumbs'] as &$item) {
					$item = tomedia($item);
				}
			}
		}
		$min = min(array_keys($comments));
	}
	if($_W['ispost']) {
		$comments = array_values($comments);
		$respon = array('error' => 0, 'message' => $comments, 'min' => $min);
		message($respon, '', 'ajax');
	}
	include $this->template('store-comment');
}


