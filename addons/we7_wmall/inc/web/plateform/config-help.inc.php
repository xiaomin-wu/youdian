<?php
/**
 * 外送系统
 * @author 灯火阑珊
 * @QQ 2471240272
 * @url http://bbs.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$_W['page']['title'] = '常见问题-' . $_W['we7_wmall']['config']['title'];
$do = 'help';
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'list';

if($op == 'list') {
	if(checksubmit('submit')) {
		if(!empty($_GPC['ids'])) {
			foreach($_GPC['ids'] as $k => $v) {
				$data = array(
					'title' => trim($_GPC['title'][$k]),
					'displayorder' => intval($_GPC['displayorder'][$k])
				);
				pdo_update('tiny_wmall_help', $data, array('uniacid' => $_W['uniacid'], 'id' => intval($v)));
			}
			message('编辑成功', $this->createWebUrl('ptfconfig-help', array('op' => 'list')), 'success');
		}
	}

	$condition = ' WHERE uniacid = :uniacid order by displayorder desc, id asc';
	$params[':uniacid'] = $_W['uniacid'];
	$item = pdo_fetchall('SELECT * FROM ' . tablename('tiny_wmall_help') . $condition ,$params);
}

if($op == 'post') {
	$id = $_GPC['id'];
	$item = pdo_get('tiny_wmall_help', array('id' => $id));
	if(checksubmit('submit')) {
		$data = array(
			'uniacid' => $_W['uniacid'],
			'title' =>trim($_GPC['title']),
			'content' => htmlspecialchars_decode($_GPC['content']),
			'displayorder' => intval($_GPC['displayorder']),
		);
		if($id) {
			pdo_update('tiny_wmall_help', $data, array('uniacid' => $_W['uniacid'], 'id' => $id));
		} else {
			$data['addtime'] = TIMESTAMP;
			pdo_insert('tiny_wmall_help', $data);
		}
		message('编辑问题成功', $this->createWebUrl('ptfconfig-help', array('op' => 'list')), 'success');
	}
}

if($op == 'del') {
	$id = intval($_GPC['id']);
	pdo_delete('tiny_wmall_help', array('uniacid' => $_W['uniacid'], 'id' => $id));
	message('删除内容成功', referer(), 'success');
}

include $this->template('plateform/config-help');