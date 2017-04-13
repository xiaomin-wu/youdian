<?php
/**
 * 外送系统
 * @author 灯火阑珊
 * @QQ 2471240272
 * @url http://bbs.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$_W['page']['title'] = '评价管理-' . $_W['we7_wmall']['config']['title'];
mload()->model('store');

$store = store_check();
$sid = $store['id'];
$do = 'comment';
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'list';

if($op == 'list') {
	$pindex = max(1, intval($_GPC['page']));
	$psize = 20;
	$condition = ' WHERE a.uniacid = :uniacid AND a.sid = :sid';
	$params = array(
		':uniacid' => $_W['uniacid'],
		':sid' => $sid,
	);
	$status = isset($_GPC['status']) ? intval($_GPC['status']) : -1;
	if($status >= 0) {
		$condition .= " AND a.status = :status";
		$params[':status'] = $status;
	}
	$oid = intval($_GPC['oid']);
	if($oid > 0) {
		$condition .= " AND a.oid = :oid";
		$params[':oid'] = $oid;
	}

	if(!empty($_GPC['addtime'])) {
		$starttime = strtotime($_GPC['addtime']['start']);
		$endtime = strtotime($_GPC['addtime']['end']) + 86399;
	} else {
		$starttime = strtotime('-15 day');
		$endtime = TIMESTAMP;
	}
	$condition .= " AND a.addtime > :start AND a.addtime < :end";
	$params[':start'] = $starttime;
	$params[':end'] = $endtime;

	$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('tiny_wmall_order_comment') . ' AS a '.  $condition, $params);
	$data = pdo_fetchall('SELECT a.*, b.uid,b.openid,b.addtime FROM ' . tablename('tiny_wmall_order_comment') . ' AS a LEFT JOIN ' . tablename('tiny_wmall_order') . ' AS b ON a.oid = b.id ' . $condition . ' ORDER BY a.addtime DESC LIMIT '.($pindex - 1) * $psize.','.$psize, $params);
	$pager = pagination($total, $pindex, $psize);
	$store_ = store_fetch($sid, array('comment_reply'));
	include $this->template('store/comment');
}

if($op == 'status') {
	$id = intval($_GPC['id']);
	$comment = pdo_get('tiny_wmall_order_comment', array('uniacid' => $_W['uniacid'], 'id' => $id));
	if(empty($comment)) {
		message('评论不存在或已删除', $this->createWebUrl('comment'), 'success');
	}
	pdo_update('tiny_wmall_order_comment', array('status' => intval($_GPC['status'])), array('uniacid' => $_W['uniacid'], 'id' => $id));
	store_comment_stat($comment['sid']);
	message('设置评论状态成功', $this->createWebUrl('comment'), 'success');
}

if($op == 'reply') {
	if(!$_W['isajax']) {
		return false;
	}
	$id = intval($_GPC['id']);
	$order = order_fetch($id);
	if(empty($order)) {
		message(error(-1, '订单不存在或已经删除'), '', 'ajax');
	}
	$content = trim($_GPC['content']);
	pdo_update('tiny_wmall_order_comment', array('reply' => $content, 'replytime' => TIMESTAMP, 'status' => 1), array('uniacid' => $_W['uniacid'], 'oid' => $id));
	store_comment_stat($order['sid']);
	message(error(0, ''), '', 'ajax');
}

if($op == 'detail') {
	if(!$_W['isajax']) {
		return false;
	}
	$id = intval($_GPC['id']);
	$comment = pdo_fetch('SELECT * FROM ' . tablename('tiny_wmall_order_comment') .' WHERE uniacid = :aid AND id = :id', array(':aid' => $_W['uniacid'], ':id' => $id));
	if(!empty($comment)) {
		$comment['data'] = iunserializer($comment['data']);
	} else {
		message(error(-1, '评价不存在或已经删除'), '', 'ajax');
	}
	$_W['comment'] = $comment;
	$html = $this->template('order-modal', TEMPLATE_FETCH);
	message(error(0, $html), '', 'ajax');
}



