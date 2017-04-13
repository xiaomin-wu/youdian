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
	$_W['page']['title'] = '商家活动专题列表';
	$activitys = pdo_getall('tiny_wmall_plus_activity_page', array('uniacid' => $_W['uniacid'], 'type' => 'store'), array('id','title','bgcolor','addtime'));
}

if($op == 'post') {
	$_W['page']['title'] = '添加商家活动专题';
	if(!empty($_GPC['id'])){
		$id = intval($_GPC['id']);
		$activity = pdo_get('tiny_wmall_plus_activity_page', array('uniacid' => $_W['uniacid'], 'id' => $id));
		if(!empty($activity['item'])){
			$item = iunserializer($activity['item']);
			$item = implode(',',$item);
		}
		$activity['item'] = pdo_fetchall('select id,logo,title,addtime from' . tablename('tiny_wmall_plus_store') . " where id in({$item}) and uniacid = :uniacid", array(':uniacid' => $_W['uniacid']));
		if(!empty($activity['share'])){
			$activity['share'] = iunserializer($activity['share']);
		}
	}
	if(checksubmit('submit')) {
		if(!empty($_GPC['item_ids'])){
			$item = iserializer($_GPC['item_ids']);
		}
		$share = array(
			'title' => trim($_GPC['share_title']),
			'thumb' => trim($_GPC['share_thumb']),
			'content' => trim($_GPC['share_content']),
		);
		if(!empty($share)){
			$share = iserializer($share);
		}
		$data = array(
			'uniacid' => $_W['uniacid'],
			'title' => trim($_GPC['title']),
			'bgcolor' => trim($_GPC['bgcolor']),
			'thumb' => trim($_GPC['thumb']),
			'content' => htmlspecialchars_decode($_GPC['content']),
			'item' => $item,
			'share' => $share,
			'addtime' => TIMESTAMP,
		);
		if(empty($_GPC['id'])){
			pdo_insert('tiny_wmall_plus_activity_page', $data);
		}else{
			$id = intval($_GPC['id']);
			pdo_update('tiny_wmall_plus_activity_page', $data, array('id' => $id, 'uniacid' => $_W['uniacid']));
		}
		message('编辑专题成功', $this->createWebUrl('ptfpage-store'), 'success');
	}
}

if($op == 'del'){
	$id = intval($_GPC['id']);
	pdo_delete('tiny_wmall_plus_activity_page', array('uniacid' => $_W['uniacid'], 'id' => $id));
	message('删除活动专题成功', $this->createWebUrl('ptfpage-store'), 'success');
}
include $this->template('plateform/page-store');