<?php

//decode by QQ:270656184 http://www.yunlu99.com/
defined('IN_IA') or die('Access Denied');
function cloud_w_request($url, $post = '', $extra = array(), $timeout = 60)
{
	load()->func('communication');
	$response = ihttp_request($url, $post, $extra, $timeout);
	if (is_error($response)) {
		return error(-1, "访问公众平台接口失败, 错误: {$response['message']}");
	}
	return $response['content'];
}
function cloud_w_prepare()
{
	global $_W;
}
function cloud_w_query_auth($code, $module)
{
	global $_W;
	$params = array('url' => rtrim($_W['siteroot'], "/"), 'host' => $_SERVER['HTTP_HOST'], 'code' => $code, 'site_id' => $_W['setting']['site']['key'], 'module' => $module, 'method' => 'query_auth');
	$content = cloud_w_request(base64_decode('aHR0cDovL3VwLmhhbzA3MS5jb20vYXBwL2luZGV4LnBocD9pPTEmYz1lbnRyeSZkbz1hdXRoJm09dGlueV9tYW5hZ2U='), $params);
	$result = @json_decode($content, true);
	if (empty($result['message'])) {
		return error(-1, "未知错误");
	}
	return $result['message'];
}
function cloud_w_client_define()
{
	return array('/model/cloud.mod.php');
}
function cloud_w_build_params()
{
	global $_W;
	$cache = cache_read('we7_wmall');
	$pars = array();
	$pars['url'] = $_W['siteroot'];
	$pars['ip'] = CLIENT_IP;
	$pars['family'] = MODULE_FAMILY;
	$pars['version'] = MODULE_VERSION;
	$pars['code'] = $cache['code'];
	$pars['cloud_id'] = $cache['cloud_id'];
	$clients = cloud_w_client_define();
	$string = '';
	foreach ($clients as $cli) {
		$string .= md5_file(MODULE_ROOT . $cli);
	}
	$pars['client'] = md5($string);
	return $pars;
}
function cloud_w_shipping_parse($dat, $file)
{
	if (is_error($dat)) {
		return error(-1, '网络传输错误, 请检查您的cURL是否可用, 或者服务器网络是否正常. ' . $dat['message']);
	}
	$dat_bak = $dat;
	$dat = @json_decode($dat, true);
	if (!is_array($dat)) {
		return error(-1, $dat_bak);
	}
	$dat = $dat['message'];
	if (is_error($dat)) {
		return $dat;
	}
	if (strlen($dat['message']) != 32) {
		return error(-1, '云服务平台向您的服务器传输数据过程中出现错误, 这个错误可能是由于您的授权码和云服务不一致, 请联系模块作者处理. 传输原始数据:' . $dat['meta']);
	}
	$data = @file_get_contents($file);
	if (empty($data)) {
		return error(-1, '没有接收到服务器的传输的数据.');
	}
	@unlink($file);
	$ret = @iunserializer($data);
	if (empty($data) || empty($ret) || $dat['message'] != $ret['secret']) {
		return error(-1, '云服务平台向您的服务器传输的数据校验失败, 可能是因为您的网络不稳定, 或网络不安全, 请稍后重试.');
	}
	$ret = iunserializer($ret['data']);
	return $ret;
}
function cloud_w_build()
{
	$pars = cloud_w_build_params();
	$pars['method'] = 'upgrade';
	$dat = cloud_w_request(base64_decode('aHR0cDovL3VwLmhhbzA3MS5jb20vYXBwL2luZGV4LnBocD9pPTAmYz1lbnRyeSZkbz11cGdyYWRlJm9wPWJ1aWxkJm09dGlueV9tYW5hZ2U='), $pars);
	$file = IA_ROOT . '/data/we7_wmall.build';
	$ret = cloud_w_shipping_parse($dat, $file);
	if (!is_error($ret)) {
		if ($ret['family'] != MODULE_FAMILY) {
			cloud_w_upgrade_version($ret['family'], '5.0.0');
			return error(-2, '你购买的版本和系统当前不一致, 系统已处理这个问题, 请重新运行自动更新程序');
		}
		$files = array();
		if (!empty($ret['files'])) {
			foreach ($ret['files'] as $file) {
				$entry = MODULE_ROOT . $file['path'];
				if (!is_file($entry) || md5_file($entry) != $file['checksum']) {
					$files[] = $file['path'];
				}
			}
		}
		$ret['files'] = $files;
		$schemas = array();
		if (!empty($ret['schemas'])) {
			load()->func('db');
			foreach ($ret['schemas'] as $remote) {
				$name = substr($remote['tablename'], 4);
				$local = db_table_schema(pdo(), $name);
				unset($remote['increment']);
				unset($local['increment']);
				if (empty($local)) {
					$schemas[] = $remote;
				} else {
					$sqls = db_table_fix_sql($local, $remote);
					if (!empty($sqls)) {
						$schemas[] = $remote;
					}
				}
			}
		}
		$ret['schemas'] = $schemas;
		if (!empty($ret['schemas'])) {
			$ret['database'] = array();
			foreach ($ret['schemas'] as $remote) {
				$row = array();
				$row['tablename'] = $remote['tablename'];
				$name = substr($remote['tablename'], 4);
				$local = db_table_schema(pdo(), $name);
				unset($remote['increment']);
				unset($local['increment']);
				if (empty($local)) {
					$row['new'] = true;
				} else {
					$row['new'] = false;
					$row['fields'] = array();
					$row['indexes'] = array();
					$diffs = db_schema_compare($local, $remote);
					if (!empty($diffs['fields']['less'])) {
						$row['fields'] = array_merge($row['fields'], $diffs['fields']['less']);
					}
					if (!empty($diffs['fields']['diff'])) {
						$row['fields'] = array_merge($row['fields'], $diffs['fields']['diff']);
					}
					if (!empty($diffs['indexes']['less'])) {
						$row['indexes'] = array_merge($row['indexes'], $diffs['indexes']['less']);
					}
					if (!empty($diffs['indexes']['diff'])) {
						$row['indexes'] = array_merge($row['indexes'], $diffs['indexes']['diff']);
					}
					$row['fields'] = implode($row['fields'], ' ');
					$row['indexes'] = implode($row['indexes'], ' ');
				}
				$ret['database'][] = $row;
			}
		}
		$ret['upgrade'] = false;
		if (!empty($ret['files']) || !empty($ret['schemas']) || !empty($ret['scripts'])) {
			$ret['upgrade'] = true;
		}
		$upgrade = array();
		$upgrade['upgrade'] = $ret['upgrade'];
		$upgrade['lastupdate'] = TIMESTAMP;
		cache_write('we7_wmall_upgrade', $upgrade);
	}
	return $ret;
}
function cloud_w_build_script($packet)
{
	$scripts = array();
	$updatefiles = array();
	if (!empty($packet['scripts'])) {
		$updatedir = MODULE_ROOT . '/resource/update/';
		load()->func('file');
		rmdirs($updatedir, true);
		mkdirs($updatedir);
		$cfamily = MODULE_FAMILY;
		$cversion = MODULE_VERSION;
		foreach ($packet['scripts'] as $script) {
			if ($script['version'] <= $cversion) {
				continue;
			}
			$fname = "update({$cversion}-{$script['version']}).php";
			$script['script'] = @base64_decode($script['script']);
			if (empty($script['script'])) {
				continue;
			}
			$updatefile = $updatedir . $fname;
			file_put_contents($updatefile, $script['script']);
			$updatefiles[] = $updatefile;
			$s = array_elements(array('message', 'family', 'version'), $script);
			$s['fname'] = $fname;
			$scripts[] = $s;
		}
	}
	return $scripts;
}
function cloud_w_download($path)
{
	$pars = cloud_w_build_params();
	$pars['method'] = 'download';
	$pars['path'] = $path;
	$pars['gz'] = function_exists('gzcompress') && function_exists('gzuncompress') ? 'true' : 'false';
	$headers = array('content-type' => 'application/x-www-form-urlencoded');
	$dat = cloud_w_request(base64_decode('aHR0cDovL3VwLmhhbzA3MS5jb20vYXBwL2luZGV4LnBocD9pPTAmYz1lbnRyeSZkbz11cGdyYWRlJm9wPWRvd25sb2FkJm09dGlueV9tYW5hZ2U='), $pars, $headers, 300);
	if (is_error($dat)) {
		return error(-1, '网络存在错误， 请稍后重试。' . $dat['message']);
	}
	$ret = @json_decode($dat, true);
	if (is_error($ret['message'])) {
		return $ret['message'];
	} else {
		return error(0, 'success');
	}
}
function cloud_w_parse_build($post)
{
	$dat = __secure_decode($post);
	if (!empty($dat)) {
		$secret = random(32);
		$ret = array();
		$ret['data'] = $dat;
		$ret['secret'] = $secret;
		file_put_contents(IA_ROOT . '/data/we7_wmall.build', iserializer($ret));
		return error(0, $secret);
	}
	return error(-1, '文件传输失败');
}
function cloud_w_parse_schema($post)
{
	$dat = __secure_decode($post);
	if (!empty($dat)) {
		$secret = random(32);
		$ret = array();
		$ret['data'] = $dat;
		$ret['secret'] = $secret;
		file_put_contents(IA_ROOT . '/data/application.schema', iserializer($ret));
		die($secret);
	}
}
function cloud_w_parse_download($post)
{
	$data = base64_decode($post);
	if (base64_encode($data) != $post) {
		$data = $post;
	}
	$ret = iunserializer($data);
	$gz = function_exists('gzcompress') && function_exists('gzuncompress');
	$file = base64_decode($ret['file']);
	if ($gz) {
		$file = gzuncompress($file);
	}
	$cache = cache_read('we7_wmall');
	$string = md5($file) . $ret['path'] . $cache['code'];
	if (md5($string) == $ret['sign']) {
		$path = IA_ROOT . $ret['path'];
		load()->func('file');
		@mkdirs(dirname($path));
		file_put_contents($path, $file);
		$sign = md5(md5_file($path) . $ret['path'] . $cache['code']);
		if ($ret['sign'] == $sign) {
			return error(0, 'success');
		}
	}
	return error(-1, '文件校验失败');
}
function cloud_w_run_download()
{
	global $_GPC;
	$post = $_GPC['__input'];
	$ret = cloud_w_download($post['path']);
	if (!is_error($ret)) {
		die('success');
	}
	die;
}
function cloud_w_run_script()
{
	global $_GPC;
	$post = $_GPC['__input'];
	$fname = $post['fname'];
	$entry = MODULE_ROOT . '/resource/update/' . $fname;
	if (is_file($entry) && preg_match('/^update\(\d{1}\.\d{1}\.\d{1}\-\d{1}\.\d{1}\.\d{1}\)\.php$/', $fname)) {
		$evalret = (include $entry);
		if (!empty($evalret)) {
			@unlink($entry);
			die('success');
		}
	}
	die('failed');
}
function cloud_w_run_schemas($packet)
{
	global $_GPC;
	$post = $_GPC['__input'];
	$tablename = $post['table'];
	foreach ($packet['schemas'] as $schema) {
		if (substr($schema['tablename'], 4) == $tablename) {
			$remote = $schema;
			break;
		}
	}
	if (!empty($remote)) {
		load()->func('db');
		$local = db_table_schema(pdo(), $tablename);
		$sqls = db_table_fix_sql($local, $remote);
		$error = false;
		foreach ($sqls as $sql) {
			if (pdo_query($sql) === false) {
				$error = true;
				break;
			}
		}
		if (!$error) {
			die('success');
		}
	}
	die;
}
function __secure_decode($post)
{
	$data = base64_decode($post);
	if (base64_encode($data) != $post) {
		$data = $post;
	}
	$ret = iunserializer($data);
	$cache = cache_read('we7_wmall');
	$string = $ret['data'] . $cache['code'];
	if (md5($string) == $ret['sign']) {
		return $ret['data'];
	}
	return false;
}