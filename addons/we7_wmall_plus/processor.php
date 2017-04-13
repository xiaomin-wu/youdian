<?php
/**
 * 外送系统
 * @author 新睿社区
 * @QQ 10373458 * @url http://www.010xr.com/
 */
defined('IN_IA') or exit('Access Denied');

class we7_wmall_plusModuleProcessor extends WeModuleProcessor {
	public function respond() {
		global $_W;
		$rid = $this->rule;
		$sql = "SELECT * FROM " . tablename('tiny_wmall_plus_reply') . " WHERE `rid`=:rid LIMIT 1";
		$row = pdo_fetch($sql, array(':rid' => $rid));
		if(empty($row)) {
			return '';
		}
		$store = pdo_get('tiny_wmall_plus_store', array('uniacid' => $_W['uniacid'], 'id' => $row['sid']));
		if(empty($store)) {
			return '';
		}
		$sid = $store['id'];
		if($row['type'] == 'store') {
			//商家二维码
			$url = murl('entry', array('m' => 'we7_wmall_plus', 'do' => 'goods', 'sid' => $sid), true, true);
			$news = array();
			$news[] = array(
				'title' => $store['title'],
				'description' => $store['content'],
				'picurl' => tomedia($store['logo']),
				'url' => $url
			);
			return $this->respNews($news);
		} elseif($row['type'] == 'assign') {
			//排号二维码
			if(!$store['is_assign']) {
				return $this->respText("{$store['title']} 已关闭排号功能,请联系商家");
			}
			$url = murl('entry', array('m' => 'we7_wmall_plus', 'do' => 'assign', 'sid' => $sid), true, true);
			$news = array();
			$news[] = array(
				'title' => $store['title'] . "-点击进入排号",
				'description' => $store['content'],
				'picurl' => $store['logo'],
				'url' => $url
			);
			return $this->respNews($news);
		} elseif($row['type'] == 'table') {
			//扫桌号
			$table = pdo_get('tiny_wmall_plus_tables', array('uniacid' => $_W['uniacid'], 'id' => $row['table_id']));
			if(empty($table)) {
				return '';
			}
			$fans = mc_fansinfo($_W['openid']);
			$data = array(
				'uniacid' => $_W['uniacid'],
				'sid' => $row['sid'],
				'table_id' => $row['table_id'],
				'openid' => $_W['openid'],
				'nickname' => $fans['nickname'],
				'avatar' => $fans['tag']['avatar'],
				'createtime' => TIMESTAMP,
			);
			pdo_insert('tiny_wmall_plus_tables_scan', $data);
			pdo_update('tiny_wmall_plus_tables', array('scan_num' => $table['scan_num'] + 1), array('uniacid' => $_W['uniacid'], 'id' => $row['table_id']));
			$url = murl('entry', array('m' => 'we7_wmall_plus', 'do' => 'table', 'table_id' => $row['table_id'], 'sid' => $sid), true, true);
			$news = array();
			$news[] = array(
				'title' => $store['title'] . "-{$table['title']}号桌",
				'description' => "欢迎光临{$store['title']}, 您当前在{$table['title']}号桌点餐",
				'picurl' => $store['logo'],
				'url' => $url
			);
			return $this->respNews($news);
		}
	}
}
