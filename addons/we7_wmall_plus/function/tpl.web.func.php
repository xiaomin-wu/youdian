<?php
/**
 * [WeEngine System] Copyright (c) 2013 WE7.CC
 * $sn$
 */
defined('IN_IA') or exit('Access Denied');
/**
 * 【表单控件】: 地理位置(经度纬度)控件
 *
 * @param string $field
 * 		表单中地理位置对应的input的name
 * @param array $value
 * 		地理位置的经度和纬度，默认为空
 * 		$value['lat'] = ''，$value['lng'] = ''
 * @return form input string
 */
function tpl_form_field_fans($name, $value = array('openid' => '', 'nickname' => '', 'avatar' => '')) {
	global $_W;
	if (empty($default)) {
		$default = './resource/images/nopic.jpg';
	}
	$s = '';
	if (!defined('TPL_INIT_TINY_FANS')) {
		$s = '
		<script type="text/javascript">
			function showFansDialog(elm) {
				var btn = $(elm);
				var openid = btn.parent().prev();
				var avatar = btn.parent().prev().prev();
				var nickname = btn.parent().prev().prev().prev();
				var img = btn.parent().parent().next().find("img");
				tiny.selectfan(function(fans){
					if(fans.tag.avatar){
						if(img.length > 0){
							img.get(0).src = fans.tag.avatar;
						}
						openid.val(fans.openid);
						avatar.val(fans.tag.avatar);
						nickname.val(fans.nickname);
					}
				});
			}
		</script>';
		define('TPL_INIT_TINY_FANS', true);
	}

	$s .= '
		<div class="input-group">
			<input type="text" name="' . $name . '[nickname]" value="' . $value['nickname'] . '" class="form-control" readonly>
			<input type="hidden" name="' . $name . '[avatar]" value="' . $value['avatar'] . '">
			<input type="hidden" name="' . $name . '[openid]" value="' . $value['openid'] . '">
			<span class="input-group-btn">
				<button class="btn btn-default" type="button" onclick="showFansDialog(this);">选择粉丝</button>
			</span>
		</div>
		<div class="input-group" style="margin-top:.5em;">
			<img src="' . $value['avatar'] . '" onerror="this.src=\'' . $default . '\'; this.title=\'头像未找到.\'" class="img-responsive img-thumbnail" width="150" />
		</div>';
	return $s;
}

/**
 * 【表单控件】: 模块链接选择器
 * @param string $name 表单input名称
 * @param string $value 表单input值
 * @param array $options 选择器样式配置信息
 * @return string
 */
function tpl_form_field_tiny_link($name, $value = '', $options = array()) {
	global $_GPC;
	$s = '';
	if (!defined('TPL_INIT_TINY_LINK')) {
		$s = '
		<script type="text/javascript">
			function showTinyLinkDialog(elm) {
				require(["jquery"], function($){
					var ipt = $(elm).parent().prev();
					tiny.linkBrowser(function(href){
						ipt.val(href);
					});
				});
			}
		</script>';
		define('TPL_INIT_TINY_LINK', true);
	}
	$s .= '
	<div class="input-group">
		<input type="text" value="'.$value.'" name="'.$name.'" class="form-control ' . $options['css']['input'] . '" autocomplete="off">
		<span class="input-group-btn">
			<button class="btn btn-default ' . $options['css']['btn'] . '" type="button" onclick="showTinyLinkDialog(this);">选择链接</button>
		</span>
	</div>
	';
	return $s;
}

function tpl_form_field_tiny_coordinate($field, $value = array()) {
	global $_W;
	$s = '';
	if(!defined('TPL_INIT_TINY_COORDINATE')) {
		$s .= '<script type="text/javascript">
				function showCoordinate(elm) {
					$.getScript("'. $_W['siteroot'] .'/addons/we7_wmall_plus/resource/web/js/tiny.js", function(){
						var val = {};
						val.lng = parseFloat($(elm).parent().prev().prev().find(":text").val());
						val.lat = parseFloat($(elm).parent().prev().find(":text").val());
						tiny.map(val, function(r){
							$(elm).parent().prev().prev().find(":text").val(r.lng);
							$(elm).parent().prev().find(":text").val(r.lat);
						});
					});
				}
			</script>';
		define('TPL_INIT_TINY_COORDINATE', true);
	}
	$s .= '
		<div class="row row-fix">
			<div class="col-xs-4 col-sm-4">
				<input type="text" name="' . $field . '[lng]" value="'.$value['lng'].'" placeholder="地理经度"  class="form-control" />
			</div>
			<div class="col-xs-4 col-sm-4">
				<input type="text" name="' . $field . '[lat]" value="'.$value['lat'].'" placeholder="地理纬度"  class="form-control" />
			</div>
			<div class="col-xs-4 col-sm-4">
				<button onclick="showCoordinate(this);" class="btn btn-default" type="button">选择坐标</button>
			</div>
		</div>';
	return $s;
}

function cloud_w_upgrade_version($family, $version, $release) {
	$verfile = MODULE_ROOT . '/version.php';
	$verdat = <<<VER
<?php
/**
 * 外送系统
 * @author 新睿社区
 * @QQ 10373458 * @url http://www.010xr.com/
 */
defined('IN_IA') or exit('Access Denied');
define('MODULE_FAMILY', '{$family}');
define('MODULE_VERSION', '{$version}');
define('MODULE_RELEASE_DATE', '{$release}');
VER;
	file_put_contents($verfile, trim($verdat));
}


function tpl_select2($name, $data, $value = 0, $filter = array('id', 'title'), $default = '请选择') {
	$element_id = "select2-{$name}";
	$json_data = array();
	foreach($data as $da) {
		$json_data[] = array(
			'id' => $da[$filter[0]],
			'text' => $da[$filter[1]],
		);
	}
	$json_data = json_encode($json_data);
	$html = '<select name="' . $name. '" class="form-control" id="' . $element_id . '"></select>';
	$html .= '<script type="text/javascript">
					require(["jquery", "select2"], function($) {
						$("#'. $element_id .'").select2({
							placeholder: "'. $default .'",
							data: '. $json_data .',
							val: '. $value.'
						});
					});
			  </script>';
	return $html;
}

function tpl_form_field_tiny_image($name, $value = '') {
	global $_W;
	$default = '';
	$val = $default;
	if (!empty($value)) {
		$val = tomedia($value);
	}
	if (!empty($options['global'])) {
		$options['global'] = true;
	} else {
		$options['global'] = false;
	}
	if (empty($options['class_extra'])) {
		$options['class_extra'] = '';
	}
	if (isset($options['dest_dir']) && !empty($options['dest_dir'])) {
		if (!preg_match('/^\w+([\/]\w+)?$/i', $options['dest_dir'])) {
			exit('图片上传目录错误,只能指定最多两级目录,如: "we7_store","we7_store/d1"');
		}
	}
	$options['direct'] = true;
	$options['multiple'] = false;
	if (isset($options['thumb'])) {
		$options['thumb'] = !empty($options['thumb']);
	}
	$s = '';
	if (!defined('TPL_INIT_TINY_IMAGE')) {
		$s = '
		<script type="text/javascript">
			function showImageDialog(elm, opts, options) {
				require(["util"], function(util){
					var btn = $(elm);
					var ipt = btn.parent().prev();
					var val = ipt.val();
					var img = ipt.parent().parent().find(".input-group-addon img");
					options = '.str_replace('"', '\'', json_encode($options)).';
					util.image(val, function(url){
						if(url.url){
							if(img.length > 0){
								img.get(0).src = url.url;
							}
							ipt.val(url.attachment);
							ipt.attr("filename",url.filename);
							ipt.attr("url",url.url);
						}
						if(url.media_id){
							if(img.length > 0){
								img.get(0).src = "";
							}
							ipt.val(url.media_id);
						}
					}, null, options);
				});
			}
			function deleteImage(elm){
				require(["jquery"], function($){
					$(elm).prev().attr("src", "./resource/images/nopic.jpg");
					$(elm).parent().prev().find("input").val("");
				});
			}
		</script>';
		define('TPL_INIT_TINY_IMAGE', true);
	}

	$s .= '
		<div class="input-group ' . $options['class_extra'] . '">
			<div class="input-group-addon">
				<img src="' . $val . '" onerror="this.src=\'' . $default . '\'; this.title=\'图片未找到.\'" width="20" height="20" />
			</div>
			<input type="text" name="' . $name . '" value="' . $value . '" class="form-control" autocomplete="off">
			<span class="input-group-btn">
				<button class="btn btn-default" type="button" onclick="showImageDialog(this);">选择图片</button>
			</span>
		</div>';
	return $s;
}

function tpl_form_field_store($name, $value = '', $option = array('mutil' => 0)) {
	global $_W;
	if (empty($default)) {
		$default = './resource/images/nopic.jpg';
	}
	if(!is_array($value)) {
		$value = intval($value);
		$value = array($value);
	}
	$value_ids = implode(',', $value);
	$stores = pdo_fetchall('select id, title, logo from ' . tablename('tiny_wmall_plus_store') . " where uniacid = :uniacid and id in ({$value_ids})" , array(':uniacid' => $_W['uniacid']));
	if(!empty($stores)) {
		foreach($stores as &$row) {
			$row['logo'] = tomedia($row['logo']);
		}
	}

	$definevar = 'TPL_INIT_TINY_STORE';
	$function = 'showStoreDialog';
	if(!empty($option['mutil'])) {
		$definevar = 'TPL_INIT_TINY_MUTIL_STORE';
		$function = 'showMutilStoreDialog';
	}
	$s = '';
	if (!defined($definevar)) {
		$option_json = json_encode($option);
		$s = '
		<script type="text/javascript">
			function '. $function .'(elm) {
				var btn = $(elm);
				var value_cn = btn.parent().prev();
				var logo = btn.parent().parent().next().find("img");
				tiny.selectstore(function(stores, option){
					if(option.mutil == 1) {
						$.each(stores, function(idx, store){
							$(elm).parent().parent().next().append(\'<div class="multi-item"><img onerror="this.src=\\\'./resource/images/nopic.jpg\\\'; this.title=\\\'图片未找到.\\\'" src="\'+store.logo+\'" class="img-responsive img-thumbnail"><input type="hidden" name="\'+name+\'[]" value="\'+store.id+\'"><em class="close" title="删除该门店" onclick="deleteStore(this)">×</em><span>\'+store.title+\'</span></div>\');
						});
					} else {
						value_cn.val(stores.title);
						logo[0].src = stores.logo;
						logo.prev().val(stores.id);
						logo.next().removeClass("hide").html(stores.title);
					}
				}, ' . $option_json . ');
			}

			function deleteMutilStore(elm){
				$(elm).parent().remove();
			}
		</script>';
		define($definevar, true);
	}

	$s .= '
		<div class="input-group">
			<input type="text" class="form-control store-cn" readonly value="' . $stores[0]['title'] . '">
			<span class="input-group-btn">
				<button class="btn btn-default" type="button" onclick="' . $function . '(this);">选择商家</button>
			</span>
		</div>';
	if(empty($option['mutil'])) {
		$s .='
		<div class="input-group single-item" style="margin-top:.5em;">
			<input type="hidden" name="'. $name .'" value="'. $value[0] .'">
			<img src="' . $stores[0]['logo'] . '" onerror="this.src=\'' . $default . '\'; this.title=\'图片未找到.\'" class="img-responsive img-thumbnail" width="150" />
		';
		if(empty($stores[0]['title'])) {
			$s .= '<span class="hide"></span>';
		} else {
			$s .= '<span>' . $stores[0]['title'] . '</span>';
		}
		$s .= '</div>';
	} else {
		$s .= '<div class="input-group multi-img-details">';
		foreach ($stores as $store) {
			$s .= '
			<div class="multi-item">
				<img src="' . $store['logo'] . '" title="'. $store['title'] .'" onerror="this.src=\'./resource/images/nopic.jpg\'; this.title=\'图片未找到.\'" class="img-responsive img-thumbnail">
				<input type="hidden" name="' . $name . '[]" value="' . $store['id'] . '">
				<em class="close" title="删除该门店" onclick="deleteMutilStore()">×</em>
				<span>' . $store['title'] . '</span>
			</div>';
		}
		$s .= '</div>';
	}
	return $s;
}

function tpl_form_field_mutil_store($name, $value = '') {
	return tpl_form_field_store($name, $value, $option = array('mutil' => 1));
}

function tpl_form_field_goods($name, $value = '', $option = array('mutil' => 0, 'sid' => 0, 'ignore' => array())) {
	global $_W;
	if(!isset($option['mutil'])) {
		$option['mutil'] = 0;
	}
	if (empty($default)) {
		$default = './resource/images/nopic.jpg';
	}
	if(!is_array($value)) {
		$value = intval($value);
		$value = array($value);
	}
	$condition = ' where uniacid = :uniacid';
	$params = array(':uniacid' => $_W['uniacid']);
	$value_ids = implode(',', $value);
	$condition .= " and id in ({$value_ids})";
	$goods = pdo_fetchall('select id, title, thumb from ' . tablename('tiny_wmall_plus_goods') . "{$condition}", $params);
	if(!empty($goods)) {
		foreach($goods as &$row) {
			$row['thumb'] = tomedia($row['thumb']);
		}
	}

	$definevar = 'TPL_INIT_TINY_GOODS';
	$function = 'showGoodsDialog';
	if(!empty($option['mutil'])) {
		$definevar = 'TPL_INIT_TINY_MUTIL_GOODS';
		$function = 'showMutilGoodsDialog';
	}
	$s = '';
	if (!defined($definevar)) {
		$option_json = json_encode($option);
		$s = '
		<script type="text/javascript">
			function '. $function .'(elm) {
				var btn = $(elm);
				var value_cn = btn.parent().prev();
				var thumb = btn.parent().parent().next().find("img");
				tiny.selectgoods(function(goods, option){
					if(option.mutil == 1) {
						$.each(goods, function(idx, good){
							$(elm).parent().parent().next().append(\'<div class="multi-item"><img onerror="this.src=\\\'./resource/images/nopic.jpg\\\'; this.title=\\\'图片未找到.\\\'" src="\'+store.good+\'" class="img-responsive img-thumbnail"><input type="hidden" name="\'+name+\'[]" value="\'+good.id+\'"><em class="close" title="删除该商品" onclick="deleteStore(this)">×</em><span>\'+good.title+\'</span></div>\');
						});
					} else {
						value_cn.val(goods.title);
						thumb[0].src = goods.thumb;
						thumb.prev().val(goods.id);
						thumb.next().removeClass("hide").html(goods.title);
					}
				}, ' . $option_json . ');
			}

			function deleteMutilGoods(elm){
				$(elm).parent().remove();
			}
		</script>';
		define($definevar, true);
	}

	$s .= '
		<div class="input-group">
			<input type="text" class="form-control store-cn" readonly value="' . $goods[0]['title'] . '">
			<span class="input-group-btn">
				<button class="btn btn-default" type="button" onclick="' . $function . '(this);">选择商品</button>
			</span>
		</div>';
	if(empty($option['mutil'])) {
		$s .='
		<div class="input-group single-item" style="margin-top:.5em;">
			<input type="hidden" name="'. $name .'" value="'. $value[0] .'">
			<img src="' . $goods[0]['thumb'] . '" onerror="this.src=\'' . $default . '\'; this.title=\'图片未找到.\'" class="img-responsive img-thumbnail" width="150" />
		';
		if(empty($goods[0]['title'])) {
			$s .= '<span class="hide"></span>';
		} else {
			$s .= '<span>' . $goods[0]['title'] . '</span>';
		}
		$s .= '</div>';
	} else {
		$s .= '<div class="input-group multi-img-details">';
		foreach ($goods as $good) {
			$s .= '
			<div class="multi-item">
				<img src="' . $good['thumb'] . '" title="'. $good['title'] .'" onerror="this.src=\'./resource/images/nopic.jpg\'; this.title=\'图片未找到.\'" class="img-responsive img-thumbnail">
				<input type="hidden" name="' . $name . '[]" value="' . $good['id'] . '">
				<em class="close" title="删除该商品" onclick="deleteMutilStore()">×</em>
				<span>' . $good['title'] . '</span>
			</div>';
		}
		$s .= '</div>';
	}
	return $s;
}
function tpl_form_field_mutil_goods($name, $value = '', $option = array('sid' => 0, 'ignore' => array())) {
	if(!isset($option['mutil'])) {
		$option['mutil'] = 1;
	}
	return tpl_form_field_goods($name, $value, $option);
}




