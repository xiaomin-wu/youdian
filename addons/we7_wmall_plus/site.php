<?php

defined('IN_IA') or die('Access Denied');
include 'version.php';
include 'model.php';
class we7_wmall_plusModuleSite extends WeModuleSite
{
	private $cache = array();
	public function __construct()
	{
	}
	public function checkAuth()
	{
		global $_W, $_GPC;
		load()->model('mc');
		if (!empty($_W['openid'])) {
			if (empty($_W['member']['uid'])) {
				$fan = mc_fansinfo($_W['openid'], $_W['acid'], $_W['uniacid']);
				if (!empty($fan)) {
					_mc_login(array('uid' => $fan['uid']));
				}
			}
			$data = pdo_get('tiny_wmall_plus_members', array('uniacid' => $_W['uniacid'], 'openid' => $_W['openid']));
			if (empty($data)) {
				if (empty($_W['member']['uid'])) {
					$fans = mc_oauth_userinfo();
					if (!empty($fans)) {
						$data = array('uniacid' => $_W['uniacid'], 'uid' => 55 . random(7, true), 'openid' => $fans['openid'], 'nickname' => $fans['nickname'], 'realname' => $fans['nickname'], 'sex' => $fans['sex'] == 1 ? '男' : '女', 'avatar' => rtrim(rtrim($fans['headimgurl'], '0'), 132) . 132, 'is_sys' => 2, 'addtime' => TIMESTAMP);
						pdo_insert('tiny_wmall_plus_members', $data);
					}
					$_W['member']['uid'] = $data['uid'];
				} else {
					$fans = mc_oauth_userinfo();
					$data = array('uniacid' => $_W['uniacid'], 'uid' => $_W['member']['uid'], 'openid' => $_W['openid'], 'nickname' => $fans['nickname'], 'realname' => $_W['member']['realname'], 'mobile' => $_W['member']['mobile'], 'sex' => $fans['sex'] == 1 ? '男' : '女', 'avatar' => rtrim(rtrim($fans['headimgurl'], '0'), 132) . 132, 'is_sys' => 1, 'addtime' => TIMESTAMP);
					pdo_insert('tiny_wmall_plus_members', $data);
				}
			} else {
				if (empty($_W['member']['uid'])) {
					$_W['member'] = array('uid' => $data['uid']);
				} else {
					if ($data['uid'] != $_W['member']['uid']) {
						$tables = array('tiny_wmall_plus_activity_coupon_grant_log', 'tiny_wmall_plus_activity_coupon_record', 'tiny_wmall_plus_address', 'tiny_wmall_plus_order', 'tiny_wmall_plus_order_comment', 'tiny_wmall_plus_order_current_log');
						pdo_update('tiny_wmall_plus_members', array('uid' => $_W['member']['uid'], 'is_sys' => 1), array('uniacid' => $_W['uniacid'], 'uid' => $data['uid']));
						foreach ($tables as $table) {
							pdo_update($table, array('uid' => $_W['member']['uid']), array('uniacid' => $_W['uniacid'], 'uid' => $data['uid']));
						}
					}
				}
			}
			if ($_W['member']['uid'] > 0) {
				$data['is_newmember'] = 0;
				if ($_GPC['sid'] > 0) {
					$is_exist = pdo_get('tiny_wmall_plus_store_members', array('uniacid' => $_W['uniacid'], 'sid' => intval($_GPC['sid']), 'uid' => $_W['member']['uid']));
					if (empty($is_exist)) {
						$data['is_newmember'] = 1;
					}
				}
				$data['credit1'] = $_W['member']['credit1'];
				$data['credit2'] = $_W['member']['credit2'];
				$data['groupid'] = $_W['member']['groupid'];
				$_W['member'] = $data;
				return true;
			}
		}
		$forward = base64_encode($_SERVER['QUERY_STRING']);
		if ($_W['ispost']) {
			$result = array();
			$result['url'] = url('auth/login', array('forward' => $forward), true);
			$result['act'] = 'redirect';
			die(json_encode($result));
		} else {
			header('location: ' . url('auth/login', array('forward' => $forward)), true);
		}
		die;
	}
	public function icheckAuth()
	{
		global $_W, $_GPC;
		load()->model('mc');
		$token = trim($_GPC['token']);
		if (empty($token)) {
			message(ierror(-1, '身份验证失败, 请重新登陆'), '', 'ajax');
		}
		$member = pdo_get('tiny_wmall_plus_members', array('uniacid' => $_W['uniacid'], 'token' => $token));
		if (empty($member)) {
			message(ierror(-1, '身份验证失败, 请重新登陆'), '', 'ajax');
		}
		$_W['member'] = $member;
	}
	public function template($filename, $flag = TEMPLATE_DISPLAY)
	{
		global $_W;
		if (defined('IN_MOBILE')) {
			$dirs = explode('/', $filename);
			if (is_array($dirs) && in_array($dirs[0], array('manage', 'delivery', 'common', 'public')) && !empty($dirs[1])) {
				$filename = $filename;
			} else {
				$filename = 'default/' . $filename;
			}
		} else {
			$dir = substr($filename, 0, 7);
			if ($dir != 'common/') {
				if (file_exists(MODULE_ROOT . '/template/web/new/' . $filename . '.html') && 0) {
					$filename = 'web/new/' . $filename;
				} else {
					$filename = 'web/' . $filename;
				}
			}
		}
		$compile = parent::template($filename);
		switch ($flag) {
			case TEMPLATE_DISPLAY:
			default:
				extract($GLOBALS, EXTR_SKIP);
				return $compile;
				break;
			case TEMPLATE_FETCH:
				extract($GLOBALS, EXTR_SKIP);
				ob_flush();
				ob_clean();
				ob_start();
				include $compile;
				$contents = ob_get_contents();
				ob_clean();
				return $contents;
				break;
			case TEMPLATE_INCLUDEPATH:
				return $compile;
				break;
		}
	}
	public function __call($name, $arguments)
	{
		global $_W;
		$isWeb = stripos($name, 'doWeb') === 0;
		$isMobile = stripos($name, 'doMobile') === 0;
		$isApi = stripos($name, 'doApi') === 0;
		$_W['__config'] = $this->module['config'];
		if ($isWeb || $isMobile) {
			$dir = IA_ROOT . '/addons/' . $this->modulename . '/inc/';
			if ($isWeb) {
				require $dir . 'web/__init.php';
				$do = strtolower(substr($name, 5));
				$sys = substr($do, 0, 3);
				if ($sys == 'ptf') {
					$do = substr($do, 3);
					$dir .= 'web/plateform/';
				} elseif ($sys == 'cmn') {
					$do = substr($do, 3);
					$dir .= 'web/common/';
				} else {
					$dir .= 'web/store/';
				}
				$fun = $do;
			} else {
				require $dir . 'mobile/__init.php';
				$do = strtolower(substr($name, 8));
				$sys = substr($do, 0, 3);
				if ($sys == 'cmn') {
					$do = substr($do, 3);
					$dir .= 'mobile/common/';
				} else {
					$sys = substr($do, 0, 2);
					if ($sys == 'mg') {
						$do = substr($do, 2);
						$dir .= 'mobile/manage/';
						require $dir . 'bootstrap.inc.php';
					} elseif ($sys == 'dy') {
						$do = substr($do, 2);
						$dir .= 'mobile/delivery/';
						require $dir . 'bootstrap.inc.php';
					} else {
						$dir .= 'mobile/store/';
					}
				}
				$fun = $do;
			}
			$file = $dir . $fun . '.inc.php';
			if (file_exists($file)) {
				require $file;
				die;
			}
		} else {
			$dir = IA_ROOT . '/addons/' . $this->modulename . '/inc/';
			require $dir . 'api/__init.php';
			$do = strtolower(substr($name, 5));
			$sys = substr($do, 0, 2);
			if ($sys == 'mg') {
				$do = substr($do, 2);
				$dir .= 'api/manage/';
				require $dir . 'bootstrap.inc.php';
			} elseif ($sys == 'dy') {
				$do = substr($do, 2);
				$dir .= 'api/delivery/';
				require $dir . 'bootstrap.inc.php';
			} elseif ($sys == 'cm') {
				$do = substr($do, 3);
				$dir .= 'api/common/';
				require $dir . 'bootstrap.inc.php';
			} else {
				$dir .= 'api/store/';
				require $dir . 'bootstrap.inc.php';
			}
			$fun = $do;
			$file = $dir . $fun . '.inc.php';
			if (file_exists($file)) {
				require $file;
				die;
			}
		}
		trigger_error("访问的方法 {$name} 不存在.", E_USER_WARNING);
		return null;
	}
	public function imessage($msg, $redirect = '', $type = '', $tip = '', $btn_text = '确定')
	{
		global $_W;
		if ($redirect == 'refresh') {
			$redirect = $_W['script_name'] . '?' . $_SERVER['QUERY_STRING'];
		} elseif (!empty($redirect) && !strexists($redirect, 'http://')) {
			$urls = parse_url($redirect);
			$redirect = $_W['siteroot'] . 'app/index.php?' . $urls['query'];
		}
		if ($redirect == '') {
			$type = in_array($type, array('success', 'error', 'info', 'ajax')) ? $type : 'info';
		} else {
			$type = in_array($type, array('success', 'error', 'info', 'ajax')) ? $type : 'success';
		}
		include $this->template('public/message', TEMPLATE_INCLUDEPATH);
		die;
	}
	public function payResult($params)
	{
		global $_W;
		$config = sys_config();
		$_W['we7_wmall_plus']['config'] = $config;
		if ($params['result'] == 'success' && $params['from'] == 'notify' || $params['from'] == 'return' && in_array($params['type'], array('delivery'))) {
			mload()->model('order');
			$record = pdo_get('tiny_wmall_plus_paylog', array('uniacid' => $_W['uniacid'], 'order_sn' => $params['tid']));
			if (!empty($record)) {
				pdo_update('tiny_wmall_plus_paylog', array('status' => 1, 'paytime' => TIMESTAMP), array('id' => $record['id']));
			}
			$data = array('pay_type' => $params['type'], 'final_fee' => $params['card_fee'], 'is_pay' => 1, 'paytime' => TIMESTAMP);
			if ($record['order_type'] == 'order') {
				order_system_status_update($record['order_id'], 'pay', $params);
			} elseif ($record['order_type'] == 'card') {
				mload()->model('card');
				card_setmeal_buy($record['order_id']);
			} elseif ($record['order_type'] == 'errander') {
				$data['out_trade_no'] = $params['uniontid'];
				mload()->model('errander');
				$order = pdo_get('tiny_wmall_plus_errander_order', array('id' => $record['order_id'], 'uniacid' => $_W['uniacid']));
				if (!empty($order) && !$order['is_pay']) {
					pdo_update('tiny_wmall_plus_errander_order', $data, array('id' => $order['id'], 'uniacid' => $_W['uniacid']));
					errander_order_status_update($order['id'], 'pay');
					errander_order_status_update($order['id'], 'dispatch');
				}
			}
		}
		if ($params['from'] == 'return') {
			$record = pdo_get('tiny_wmall_plus_paylog', array('uniacid' => $_W['uniacid'], 'order_sn' => $params['tid']));
			if ($record['order_type'] == 'order') {
				$url = $this->createMobileUrl('order', array('op' => 'detail', 'id' => $record['order_id']));
			} elseif ($record['order_type'] == 'card') {
				$url = $this->createMobileUrl('card');
			} else {
				$url = $this->createMobileUrl('errander-order', array('op' => 'detail', 'id' => $record['order_id']));
			}
			if ($params['type'] == 'credit') {
				message('下单成功', $url, 'success');
			} elseif ($params['type'] == 'delivery') {
				header('location:' . $url);
				die;
			} else {
				header('location:' . '../../app/' . $url);
				die;
			}
		}
	}
	protected function pay($params = array(), $mine = array())
	{
		global $_W;
		if (!$this->inMobile) {
			message('支付功能只能在手机上使用');
		}
		$params['module'] = $this->module['name'];
		$pars = array();
		$pars[':uniacid'] = $_W['uniacid'];
		$pars[':module'] = $params['module'];
		$pars[':tid'] = $params['tid'];
		if ($params['fee'] <= 0) {
			$pars['from'] = 'return';
			$pars['result'] = 'success';
			$pars['type'] = '';
			$pars['tid'] = $params['tid'];
			$site = WeUtility::createModuleSite($pars[':module']);
			$method = 'payResult';
			if (method_exists($site, $method)) {
				die($site->{$method}($pars));
			}
		}
		$sql = 'SELECT * FROM ' . tablename('core_paylog') . ' WHERE `uniacid`=:uniacid AND `module`=:module AND `tid`=:tid';
		$log = pdo_fetch($sql, $pars);
		if (!empty($log) && $log['status'] == '1') {
			message('这个订单已经支付成功, 不需要重复支付.');
		}
		$payment = $_W['we7_wmall_plus']['config']['payment'];
		if (!is_array($payment)) {
			message('没有有效的支付方式, 请联系网站管理员.');
		}
		if (empty($_W['member']['uid'])) {
			$payment['credit'] = false;
		}
		if (!empty($payment['credit'])) {
			$credtis = mc_credit_fetch($_W['member']['uid']);
		}
		include $this->template('public/paycenter');
	}
}