<?php
/**
 * [WeEngine System] Copyright (c) 2013 WE7.CC
 * $sn$
 */
defined('IN_IA') or exit('Access Denied');

function goods_fetch($id) {
	global $_W;
	$data = pdo_get('tiny_wmall_goods', array('uniacid' => $_W['uniacid'], 'id' => $id));
	if($data['is_options'] == 1) {
		$data['options'] = pdo_fetchall('SELECT * FROM ' . tablename('tiny_wmall_goods_options') . ' WHERE uniacid = :aid AND goods_id = :goods_id ORDER BY displayorder DESC, id ASC', array(':aid' => $_W['uniacid'], ':goods_id' => $id));
	}
	$data['thumb_'] = tomedia($data['thumb']);
	if(!empty($data['slides'])) {
		$data['slides'] = iunserializer($data['slides']);
		foreach($data['slides'] as &$slide) {
			$slide = tomedia($slide);
		}
	} else {
		$data['slides'] = array();
	}
	return $data;
}