<?php
/**
 * 外送系统
 * @author 灯火阑珊
 * @QQ 2471240272
 * @url http://bbs.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
mload()->model('store');
mload()->model('order');

$config = sys_config();
$_W['we7_wmall']['config'] = $config;
sys_store_cron();

function cloud_w_upgrade_version($family, $version) {
	$verfile = MODULE_ROOT . '/version.php';
	$verdat = <<<VER
<?php
/**
 * 外送系统
 * @author 灯火阑珊
 * @QQ 2471240272
 * @url http://bbs.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');
define('MODULE_FAMILY', '{$family}');
define('MODULE_VERSION', '{$version}');
VER;
	file_put_contents($verfile, trim($verdat));
}
