<?php
/**
 * 外送系统
 * @author 灯火阑珊
 * @QQ 2471240272
 * @url http://bbs.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');
mload()->model('cloud');
global $_W, $_GPC;
$do = 'cloud';
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'auth';
$_W['page']['title'] = "云服务-{$_W['we7_wmall']['config']['title']}";

if(empty($_W['isfounder'])) {
	if(!$_W['isajax']) {
		message('您没有权限进行该操作', referer(), 'error');
	} else {
		message(error(-1, '您没有权限进行该操作'), '', 'ajax');
	}
}

if($op == 'auth') {
	$params = array(
		'url' => $_W['siteroot'],
		'host' => $_SERVER['HTTP_HOST'],
		'ip' => gethostbyname($_SERVER['HTTP_HOST']),
		'site_id' => $_W['setting']['site']['key'],
		'code' => '',
	);
	$cache = cache_read('we7_wmall');
	if(checksubmit()) {
		$url = trim($_GPC['url']);
		if(empty($url)) {
			message('站点url不能为空', referer(), 'error');
		}
		$ip = trim($_GPC['ip']);
		if(empty($ip)) {
			message('站点ip不能为空', referer(), 'error');
		}
		$code = trim($_GPC['code']);
		if(empty($code) && 0) {
			message('授权码不能为空', referer(), 'error');
		}
		$status = cloud_w_query_auth($code, 'we7_wmall');
		if(is_error($status)) {
			message($status['message'], referer(), 'error');
		} else {
			$data = array(
				'cloud_id' => $status['message']['cloud_id'],
				'code' => $status['message']['code'],
				'code_status' => 1,
			);
			cache_write('we7_wmall', $data);
			message('您的站点已授权', referer(), 'success');
		}
	}
}

if($op == 'upgrade')  {
	if(checksubmit('submit')) {
		$upgrade = cloud_w_build();
		if (is_error($upgrade)) {
			message($upgrade['message'], '', 'error');
		}
		if($upgrade['upgrade']) {
			message("检测到新版本: <strong>{$upgrade['version']}</strong>, 请立即更新.", 'refresh');
		} else {
			message('检查结果: 恭喜, 你的程序已经是最新版本. ', 'refresh');
		}
	}

	$familys = module_familys();
	$now_family = $familys[MODULE_FAMILY]['title'];
	cache_load('we7_wmall_upgrade');
	if(!empty($_W['cache']['we7_wmall_upgrade'])) {
		$upgrade = $_W['cache']['we7_wmall_upgrade'];
	}
	if(empty($upgrade) || TIMESTAMP - $upgrade['lastupdate'] >= 3600 * 24 || $upgrade['upgrade']) {
		$upgrade = cloud_w_build();
		if(is_error($upgrade)) {
			if($upgrade['errno'] == -2) {
				message($upgrade['message'], $this->createWebUrl('ptfcloud', array('op' => 'upgrade')), 'error');
			}
			message($upgrade['message'], '', 'error');
		}
	}
}

if($op == 'process') {
	$step = trim($_GPC['step']) ? trim($_GPC['step']) : 'files';
	if ($step == 'files' && $_W['ispost']) {
		cloud_w_run_download();
	}

	if ($step == 'scripts' && $_W['ispost']) {
		cloud_w_run_script();
	}

	$packet = cloud_w_build();
	if (is_error($packet)) {
		message($packet['message'], '', 'error');
	}
	if($step == 'schemas' && $_W['ispost']) {
		cloud_w_run_schemas($packet);
	}
	if(!empty($packet) && !empty($packet['upgrade'])) {
		$schemas = array();
		if(!empty($packet['schemas'])) {
			foreach ($packet['schemas'] as $schema) {
				$schemas[] = substr($schema['tablename'], 4);
			}
		}
		$scripts = cloud_w_build_script($packet);
	} else {
		cache_delete('checkupgrade:we7_wmall');
		message('更新已完成. ', $this->createWebUrl('ptfcloud', array('op' => 'upgrade')), 'info');
	}
}
if($op == 'check') {
	$lock = cache_load('checkupgrade:we7_wmall');
	if(empty($lock) || (TIMESTAMP - 1800 > $lock['lastupdate'])) {
		$upgrade = cloud_w_build();
		if(!is_error($upgrade) && !empty($upgrade['upgrade'])) {
			$upgrade = array('version' => $upgrade['version'], 'upgrade' => 1, 'lastupdate' => TIMESTAMP);
			cache_write('checkupgrade:we7_wmall', $upgrade);
			message($upgrade, '', 'ajax');
		} else {
			$upgrade = array('lastupdate' => TIMESTAMP);
			cache_write('checkupgrade:we7_wmall', $upgrade);
		}
	} else {
		message($lock, '', 'ajax');
	}
}
include $this->template('plateform/cloud');
