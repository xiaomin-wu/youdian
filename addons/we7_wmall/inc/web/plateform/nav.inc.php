<?php
/**
 * 外送系统
 * @author 灯火阑珊
 * @QQ 2471240272
 * @url http://bbs.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$title = '首页导航图片设置';
$do = 'pftnav';
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'post';

if($op == 'post') {
	if(checksubmit()) {
		$update = array(
			'imgnav_status' => intval($_GPC['imgnav_status']),
		);
		$update['imgnav_data'] = array();
		if(!empty($_GPC['title'])) {
			foreach($_GPC['title'] as $key=>$title) {
				$title = trim($title);
				$tips = trim($_GPC['tips'][$key]);
				$link = trim($_GPC['link'][$key]);
				$img = trim($_GPC['img'][$key]);
				if($title) {
					$update['imgnav_data'][] = array(
						'title' => $title,
						'tips' => $tips,
						'link' => $link,
						'img' => $img,
					);
				}
			}
			$update['imgnav_data'] = iserializer($update['imgnav_data']);
		}
		pdo_update('tiny_wmall_config',$update,  array('uniacid' => $_W['uniacid']));
		message('设置首页导航图片成功', $this->createWebUrl('ptfnav'), 'success');
	}
	$config = $_W['we7_wmall']['config'];
	$data = array();
	foreach($config['imgnav_data'] as &$row) {
		if(!is_array($row)) {
			continue;
		}
		$data[] = $row;
	}
	$config['imgnav_data'] = $data;
	$count = count($config['imgnav_data']);
	for($i = 0; $i < 4 - $count; $i++) {
		$config['imgnav_data'][] = array(
			'title' => '',
			'tips' => '',
			'img' => '',
			'link' => '',
		);
	}
}
include $this->template('plateform/imgnav');
