<?php
/**
 * 外送系统
 * @author 灯火阑珊
 * @QQ 2471240272
 * @url http://bbs.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$do = 'address';
$this->checkAuth();
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'list';
$title = '我的评论';
if($op == 'list') {
	$comments = pdo_fetchall('select a.id as aid, a.*, b.title from ' . tablename('tiny_wmall_order_comment') . ' as a left join ' . tablename('tiny_wmall_store') . ' as b on a.sid = b.id where a.uniacid = :uniacid and a.uid = :uid order by a.id desc limit 15', array(':uniacid' => $_W['uniacid'], ':uid' => $_W['member']['uid']), 'aid');
	$min = 0;
	if(!empty($comments)) {
		foreach ($comments as &$row) {
			$row['data'] = iunserializer($row['data']);
			$row['score'] = ($row['delivery_service'] + $row['goods_quality']) / 10 * 100;
			$row['thumbs'] = iunserializer($row['thumbs']);
			if(!empty($row['thumbs'])) {
				foreach($row['thumbs'] as &$item) {
					$item = tomedia($item);
				}
			}
		}
		$min = min(array_keys($comments));
	}
}

if($op == 'more') {
	$id = intval($_GPC['id']);
	$comments = pdo_fetchall('select a.id as aid,a.*,b.title from ' . tablename('tiny_wmall_order_comment') . ' as a left join ' . tablename('tiny_wmall_store') . ' as b on a.sid = b.id where a.uniacid = :uniacid and a.uid = :uid and a.id < :id order by a.id desc limit 15', array(':uniacid' => $_W['uniacid'], ':uid' => $_W['member']['uid'], ':id' => $id), 'aid');
	if(!empty($comments)) {
		foreach ($comments as &$row) {
			$row['data'] = iunserializer($row['data']);
			$row['score'] = ($row['delivery_service'] + $row['goods_quality']) / 10 * 100;
			$row['addtime_cn'] = date('Y-m-d H:i', $row['addtime']);
			$row['replytime_cn'] = date('Y-m-d H:i', $row['replytime']);
			$row['thumbs'] = iunserializer($row['thumbs']);
			if(!empty($row['thumbs'])) {
				foreach($row['thumbs'] as &$item) {
					$item = tomedia($item);
				}
			}
		}
		$min = min(array_keys($comments));
	} else {
		$min = 0;
	}
	$comments = array_values($comments);
	$respon = array('error' => 0, 'message' => $comments, 'min' => $min);
	message($respon, '', 'ajax');
}
include $this->template('comment');