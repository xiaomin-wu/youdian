<?php
/**
 * 微擎外送模块
 *
 * @author 新睿社区
 * @url http://www.010xr.com/
 */
defined('IN_IA') or exit('Access Denied');

class we7_wmall_plusModule extends WeModule {
	public function settingsDisplay($settings) {
		global $_W, $_GPC;
		include $this->template('settings');
	}
}
