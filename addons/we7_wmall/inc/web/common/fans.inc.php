<?php
/**
 * 外送系统
 * @author 灯火阑珊
 * @QQ 2471240272
 * @url http://bbs.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;

$op = trim($_GPC['op']);

if($op == 'list') {
	$key = trim($_GPC['key']);
	$data = pdo_fetchall('select * from ' . tablename('mc_mapping_fans') . ' where uniacid = :uniacid and acid = :acid and nickname like :key order by fanid desc limit 50', array(':uniacid' => $_W['uniacid'], ':acid' => $_W['acid'], ':key' => "%{$key}%"), 'fanid');
	if(!empty($data)) {
		foreach($data as &$row) {
			if (is_base64($row['tag'])){
				$row['tag'] = base64_decode($row['tag']);
			}
			if(is_serialized($row['tag'])) {
				$row['tag'] = @iunserializer($row['tag']);
			}
			if(!empty($row['tag']['headimgurl'])) {
				$row['tag']['avatar'] = tomedia($row['tag']['headimgurl']);
			}
			if($row['tag']['sex'] == 1) {
				$row['tag']['sex'] = '男生';
			} elseif($row['tag']['sex'] == 2) {
				$row['tag']['sex'] = '女生';
			} else {
				$row['tag']['sex'] = '未知';
			}
		}
		$fans = array_values($data);
	}
	message(array('errno' => 0, 'message' => $fans, 'data' => $data), '', 'ajax');
}