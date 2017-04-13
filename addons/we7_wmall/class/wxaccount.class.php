<?php
/**
 * [WeEngine System] Copyright (c) 2013 WE7.CC
 * $sn$
 */
defined('IN_IA') or exit('Access Denied');
load()->func('communication');

class WxAccount{
	public function __construct() {

	}

	public function asscess_token_get() {
		global $_W;

		$acc =  WeAccount::create($_W['acid']);
		if(is_error($acc)) {
			return $acc;
		}
		$token = $acc->getAccessToken();
		return $token;
	}

	public function media_download($media_id) {
		global $_W;
		$access_token = $this->asscess_token_get();
		if(is_error($access_token)) {
			return $access_token;
		}

		$url = "http://file.api.weixin.qq.com/cgi-bin/media/get?access_token={$access_token}&media_id={$media_id}";
		$response = ihttp_get($url);
		if(is_error($response)) {
			return error(-1, "访问公众平台接口失败, 错误: {$response['message']}");
		}
		$result = @json_decode($response['content'], true);
		if(!empty($result['errcode'])) {
			return error(-1, "访问微信接口错误, 错误代码: {$result['errcode']}, 错误信息: {$result['errmsg']}");
		}
		load()->func('file');
		$path = "images/{$_W['uniacid']}/" . date('Y/m/');
		$filename = file_random_name(ATTACHMENT_ROOT . '/' . $path, 'jpg');
		$filename = $path . $filename;
		$status = file_write($filename, $response['content']);
		if(!$status) {
			return error(-1, '保存图片失败');
		}
		$status = file_remote_upload($filename);
		if(is_error($status)) {
			return error(-1, '上传到远程失败');
		}
		return $filename;
	}
}
