<?php
/**
 * 外送系统
 * @author 新睿社区
 * @QQ 10373458 * @url http://www.010xr.com/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'info';

if($op == 'info') {
	$slides = sys_fetch_slide(2);
	if(!empty($slides)) {
		foreach($slides as &$slide) {
			$slide['thumb'] = tomedia($slide['thumb']);
		}
	}
	$categorys = store_fetchall_category();
	$img_navs = $_W['we7_wmall_plus']['config']['imgnav_data'];
	if(!empty($img_navs)) {
		foreach($img_navs as &$img_nav) {
			$img_nav['img'] = tomedia($img_nav['img']);
		}
	}
	$data = array(
		'slides' => $slides,
		'categorys' => $categorys,
		'slides' => $slides,
		'img_navs' => $img_navs,
	);
	message(ierror(0, '', $data), '', 'ajax');
}
