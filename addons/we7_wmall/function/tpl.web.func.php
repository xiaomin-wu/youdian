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
	$s = '';
	if(!defined('TPL_INIT_TINY_COORDINATE')) {
		$s .= '<script type="text/javascript">
				function showCoordinate(elm) {
					$.getScript("../addons/we7_wmall/resource/web/js/tiny.js", function(){
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

