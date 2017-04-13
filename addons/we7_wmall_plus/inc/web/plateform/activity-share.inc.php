<?php
/**
 * 外送系统
 * @author 新睿社区
 * @QQ 10373458 * @url http://www.010xr.com/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$_W['page']['title'] = '分享有礼-平台营销活动';
$do = 'activity-share';
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'post';

if($op == 'list') {
	$shares = pdo_getall('tiny_wmall_plus_activity_share', array('uniacid' => $_W['uniacid']));

}

if($op == 'post') {
	$id = intval($_GPC['id']);
	if($id > 0) {
		$share = pdo_get('tiny_wmall_plus_activity_share', array('uniacid' => $_W['uniacid'], 'id' => $id));
		$share['share'] = iunserializer($share['share']);
	} else {
		$share = array(
			'share' => array(
				'title' => '快来领15元红包,我只想任性的请你吃顿饭',
				'content' => '友谊的小船说翻就翻,请你吃顿饭就是这么任性,快来领红包吧'
			),
			'starttime'=> time(),
			'endtime'=> strtotime('7days'),
		);
	}
	if(checksubmit('submit')) {
		$title = !empty($_GPC['title']) ? trim($_GPC['title']) : message('活动名称不能为空', '', 'error');
		$data = array(
			'uniacid' => $_W['uniacid'],
			'title' => $title,
			'formuser_redpacket_min' => intval($_GPC['formuser_redpacket_min']),
			'formuser_redpacket_max' => intval($_GPC['formuser_redpacket_max']),
			'formuser_redpacket_time_limit' => intval($_GPC['formuser_redpacket_time_limit']),
			'touser_redpacket_min' => intval($_GPC['touser_redpacket_min']),
			'touser_redpacket_max' => intval($_GPC['touser_redpacket_max']),
			'touser_redpacket_time_limit' => intval($_GPC['touser_redpacket_time_limit']),
			'starttime' => strtotime($_GPC['time']['start']),
			'endtime' => strtotime($_GPC['time']['end']),
			'addtime' => TIMESTAMP,
			'share' => iserializer($_GPC['share']),
			'agreement' => htmlspecialchars_decode($_GPC['agreement']),
		);
		if(!empty($share['id'])) {
			pdo_update('tiny_wmall_plus_activity_share', $data, array('uniacid' => $_W['uniacid'], 'id' => $id));
		} else {
			pdo_insert('tiny_wmall_plus_activity_share', $data);
		}
		message('编辑活动成功', $this->createWebUrl('ptfactivity-share', array('op' => 'list')), 'success');
	}
}

if($op == 'del') {
	$id = intval($_GPC['id']);
	pdo_delete('tiny_wmall_plus_activity_share', array('uniacid' => $_W['uniacid'], 'id' => $id));
	message('删除活动成功', referer(), 'success');
}

if($op == 'toggle_status') {
	$id = intval($_GPC['id']);
	$status = intval($_GPC['status']);
	$other = pdo_fetch('select * from ' . tablename('tiny_wmall_plus_activity_share') . ' where uniacid = :uniacid and status = 1 and id != :id', array(':uniacid' => $_W['uniacid'], ':id' => $id));
	if(!empty($other)) {
		message(error(-1, '同一时间只能开启一个分享活动'), '', 'ajax');
	}
	pdo_update('tiny_wmall_plus_activity_share', array('status' => $status), array('uniacid' => $_W['uniacid'], 'id' => $id));
	message(error(0, ''), '', 'ajax');
}
include $this->template('plateform/activity-share');