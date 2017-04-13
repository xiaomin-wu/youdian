<?php
/**
 * 外送系统
 * @author 新睿社区
 * @QQ 10373458 * @url http://www.010xr.com/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$_W['page']['title'] = '商品列表-' . $_W['we7_wmall_plus']['config']['title'];
mload()->model('store');
load()->func('communication');

$store = store_check();
$sid = $store['id'];
$do = 'goods';
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'index';

if($op == 'index') {
	if(checksubmit()) {
		$category = store_fetchall_goods_category($sid);
		if(empty($category)) {
			$categorys = array('优惠促销', '中西药品', '滋补保健', '维生素钙', '参茸贵细', '医疗器械', '成人用品');
			$i = 50;
			foreach($categorys as $category) {
				$data = array(
					'uniacid' => $_W['uniacid'],
					'sid' => $sid,
					'title' => $category,
					'displayorder' => $i,
				);
				pdo_insert('tiny_wmall_plus_goods_category', $data);
				$i--;
			}
		}
		$file = upload_file($_FILES['file'], 'excel');
		if(is_error($file)) {
			message($file['message'], referer(), 'error');
		}
		$data = read_excel($file);
		if(is_error($data)) {
			message($data['message'], referer(), 'error');
		}
		unset($data[1]);
		if(empty($data)) {
			message('没有要导入的数据', referer(), 'error');
		}
		$goods = array();
		$codes = array();
		foreach($data as $da) {
			if(empty($da['0'])) {
				continue;
			}
			$code = trim($da[5]);
			if(empty($code)) {
				$code = 'custom' . random(10, true);
			}
			$data = array(
				'uniacid' => $_W['uniacid'],
				'sid' => $sid,
				'title' => trim($da[0]),
				'price' => trim($da[6]),
				'code' => $code,
				'total' => intval($da[8]),
				'tytitle' => trim($da[1]),
				'jixing' => trim($da[2]),
				'guige' => trim($da[3]),
				'pzwh' => trim($da[4]),
				'sccj' => trim($da[9]),
			);
			$codes[] = $code;
			$goods[$code] = $data;
		}
		$data = array('code' => $codes, 'goods' => $goods);
		cache_write("we7_wmall_plus_{$_W['uniacid']}_goods", $data);
		header("location:" . $this->createWebUrl('import', array('op' => 'sync')));
	}
}

if($op == 'sync') {
	$goods = cache_read("we7_wmall_plus_{$_W['uniacid']}_goods");
	if($_W['isajax']) {
		$post = $_GPC['__input'];
		$code = trim($post['code']);
		if(empty($code)) {
			exit('商品编码不存在');
		}
		$basic = $goods['goods'][$code];
		if(empty($basic)) {
			exit('商品基本信息不存在');
		}
		$is_custom = (substr($code, 0, 6) == 'custom');
		if($is_custom) {
			$code = '';
			$data = array(
				'uniacid' => $_W['uniacid'],
				'sid' => $sid,
				'title' => $basic['title'],
				'price' => round($basic['price'], 2),
				'code' => $code,
				'total' => intval($basic['total']),
				'flag' => 0,
				'attr' => iserializer(array(
					'tytitle' => array(
						'text' => '通用名称',
						'val' => $basic['tytitle'],
					),
					'jixing' => array(
						'text' => '剂型',
						'val' => $basic['jixing'],
					),
					'guige' => array(
						'text' => '规格',
						'val' => $basic['guige'],
					),
					'pzwh' => array(
						'text' => '批准文号',
						'val' => $basic['pzwh'],
					),
					'sccj' => array(
						'text' => '生产厂家',
						'val' => $basic['sccj'],
					),
				))
			);
			pdo_insert('tiny_wmall_plus_goods', $data);
			exit('code_miss');
		}
		$detail = array();
		$result = ihttp_get('http://182.92.220.224:8084/j/weixin/weixinshop/getYaoInfo?code=' . $code);
		if(!is_error($result)) {
			$content = json_decode($result['content'], true);
			$detail = $content['data'];
		}
		if(empty($detail)) {
			$data = array(
				'uniacid' => $_W['uniacid'],
				'sid' => $sid,
				'title' => $basic['title'],
				'price' => round($basic['price'], 2),
				'code' => $code,
				'total' => intval($basic['total']),
				'flag' => 0,
				'attr' => iserializer(array(
					'tytitle' => array(
						'text' => '通用名称',
						'val' => $basic['tytitle'],
					),
					'jixing' => array(
						'text' => '剂型',
						'val' => $basic['jixing'],
					),
					'guige' => array(
						'text' => '规格',
						'val' => $basic['guige'],
					),
					'pzwh' => array(
						'text' => '批准文号',
						'val' => $basic['pzwh'],
					),
					'sccj' => array(
						'text' => '生产厂家',
						'val' => $basic['sccj'],
					),
				))
			);
			pdo_insert('tiny_wmall_plus_goods', $data);
			exit('detail_miss');
		}
		$ctitle = explode(',', $detail['keywords']);
		$category = pdo_get('tiny_wmall_plus_goods_category', array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'title' => $ctitle[0]));

		$insert = array(
			'uniacid' => $_W['uniacid'],
			'sid' => $sid,
			'title' => $basic['title'],
			'cid' => $category['id'],
			'price' => round($basic['price'], 2),
			'total' => intval($basic['total']),
			'code' => $basic['code'],
			'thumb' => $detail['imgUrlList'][0],
			'slides' => iserializer($detail['imgUrlList']),
			'description' => $detail['instruction']['gnzz'],
			'instruction' => '',
			'flag' => 1,
			'attr' => iserializer(array(
				'tytitle' => array(
					'text' => '通用名称',
					'val' => $basic['tytitle'],
				),
				'jixing' => array(
					'text' => '剂型',
					'val' => $basic['jixing'],
				),
				'guige' => array(
					'text' => '规格',
					'val' => $basic['guige'],
				),
				'pzwh' => array(
					'text' => '批准文号',
					'val' => $basic['pzwh'],
				),
				'sccj' => array(
					'text' => '生产厂家',
					'val' => $basic['sccj'],
				),
			))
		);
		$fields = array(
			'spmc' => '商品名称',
			'cf' => '成分',
			'xz' => '性状',
			'gnzz' => '功能主治',
			'guiGe' => '规格',
			'yfyl' => '用法用量',
			'blfy' => '不良反应',
			'jj' => '禁忌',
			'zysxBr' => '注意事项',
			'ywxhzy' => '药物相互作用',
			'store' => '储藏',
			'packageb' => '包装',
			'validity' => '有效期',
		);
		$instructions = array();
		foreach($fields as $key => $val) {
			if($key == 'spmc') {
				$detail['instruction'][$key] = "{$detail['instruction'][$key]} {$detail['instruction']['tymc']}";
			}
			$instructions[] = "<div  style='font-size: 16px; color: #666; padding: 3px 0'>{$val}</div><ul class='list-paddingleft-2' style='list-style-type: disc;'><li style='color: #999; font-size: .7rem'>{$detail['instruction'][$key]}</li></ul>";
		}
		$instructions = implode('', $instructions);
		$insert['instruction'] = $instructions;
		$is_exist = pdo_get('tiny_wmall_plus_goods',  array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'code' => $insert['code']));
		if(!empty($is_exist)) {
			pdo_update('tiny_wmall_plus_goods', $insert, array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'code' => $insert['code']));
		} else {
			pdo_insert('tiny_wmall_plus_goods', $insert);
		}
		exit('success');
	}
	if(empty($goods)) {
		message('没有要导入的商品', $this->createWebUrl('import'), 'error');
	}
}

include $this->template('store/goods-import');