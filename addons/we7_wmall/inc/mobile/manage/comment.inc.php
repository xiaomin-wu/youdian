<?php
/**
 * 外送系统
 * @author 灯火阑珊
 * @QQ 2471240272
 * @url http://bbs.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$do = 'mgcomment';
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'list';
mload()->model('manage');
mload()->model('order');
checkstore();
$sid = intval($_GPC['__mg_sid']);
$store = $_W['we7_wmall']['store'];
$account = $store['account'];
$title = '评论管理';

if($op == 'list') {
	$condition = ' where uniacid = :uniacid and sid = :sid';
	$params = array(':uniacid' => $_W['uniacid'], ':sid' => $sid);
	$type = intval($_GPC['type']);
	if($type == 1) {
		$condition .= ' and score >= 8';
	} elseif($type == 2) {
		$condition .= ' and score >= 4 and score <= 7';
	} elseif($type == 3) {
		$condition .= ' and score <= 3';
	}
	$id = intval($_GPC['id']);
	if($id > 0) {
		$condition .= " and id < :id";
		$params[':id'] = $id;
	}
	$comments = pdo_fetchall('select * from ' . tablename('tiny_wmall_order_comment') . $condition . ' order by id desc limit 15', $params, 'id');
	$min = 0;
	if(!empty($comments)) {
		$comment_status = order_comment_status();
		foreach ($comments as &$row) {
			$row['data'] = iunserializer($row['data']);
			$row['score'] = ($row['delivery_service'] + $row['goods_quality']) * 10;
			$row['addtime'] = date('Y-m-d H:i', $row['addtime']);
			$row['replytime'] = date('Y-m-d H:i', $row['replytime']);
			$row['avatar'] = tomedia($row['avatar']) ? tomedia($row['avatar']) : '../addons/we7_wmall/resource/app/img/head.png';
			$row['thumbs'] = iunserializer($row['thumbs']);
			if(!empty($row['thumbs'])) {
				foreach($row['thumbs'] as &$item) {
					$item = tomedia($item);
				}
			}
			$row['status_cn'] = $comment_status[$row['status']]['text'];
			$row['status_css'] = $comment_status[$row['status']]['css'];
		}
		$min = min(array_keys($comments));
	}
	if($_W['ispost']) {
		$comments = array_values($comments);
		$respon = array('error' => 0, 'message' => $comments, 'min' => $min);
		message($respon, '', 'ajax');
	}
	include $this->template('manage/comment');
}

if($op == 'status') {
	$id = intval($_GPC['id']);
	$comment = pdo_get('tiny_wmall_order_comment', array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'id' => $id));
	if(empty($comment)) {
		message(error(-1, '评论不存在或已删除'), '', 'ajax');
	}
	$status = intval($_GPC['status']);
	if($status > 2) {
		message(error(-1, '非法访问'), '', 'ajax');
	}
	pdo_update('tiny_wmall_order_comment', array('status' => $status), array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'id' => $id));
	store_comment_stat($comment['sid']);
	message(error(0, ''), '', 'ajax');
}

if($op == 'reply') {
	if(!$_W['isajax']) {
		return false;
	}
	$id = intval($_GPC['id']);
	$comment = pdo_get('tiny_wmall_order_comment', array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'id' => $id));
	if(empty($comment)) {
		message(error(-1, '评论不存在或已删除'), '', 'ajax');
	}
	$content = trim($_GPC['reply']);
	pdo_update('tiny_wmall_order_comment', array('reply' => $content, 'status' => 1, 'replytime' => TIMESTAMP, 'status' => 1), array('uniacid' => $_W['uniacid'], 'id' => $id));
	store_comment_stat($comment['sid']);
	message(error(0, ''), '', 'ajax');
}

