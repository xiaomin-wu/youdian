<?php
/**
 * 外送系统
 * @author 新睿社区
 * @QQ 10373458 * @url http://www.010xr.com/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$this->icheckAuth();
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'list';

if($op == 'list') {
	$condition = ' where a.uniacid = :uniacid and a.uid = :uid';
	$params = array(':uniacid' => $_W['uniacid'], ':uid' => $_W['member']['uid']);
	$id = intval($_GPC['id']);
	if($id > 0) {
		$condition .= ' and a.id < :id';
		$params[':id'] = $id;
	}
	$comments = pdo_fetchall('select a.id as aid,a.*,b.title from ' . tablename('tiny_wmall_plus_order_comment') . ' as a left join ' . tablename('tiny_wmall_plus_store') . " as b on a.sid = b.id {$condition} order by a.id desc limit 15", $params, 'aid');
	$min = 0;
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
	}
	$comments = array_values($comments);
	$data = array(
		'comment' => $comments,
		'min_id' => $min,
	);
	message(ierror(0, '', $data), '', 'ajax');
}