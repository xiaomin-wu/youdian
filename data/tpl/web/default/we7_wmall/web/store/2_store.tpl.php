<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('public/header', TEMPLATE_INCLUDEPATH)) : (include template('public/header', TEMPLATE_INCLUDEPATH));?>
<link rel="stylesheet" href="<?php  echo $_W['siteroot'];?>addons/we7_wmall/resource/web/css/back.css">
<script src="<?php  echo $_W['siteroot'];?>addons/we7_wmall/resource/web/js/tiny.js"></script>
<ul class="nav nav-tabs">
	<li <?php  if($op == 'list') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('store', array('op' => 'list'));?>">门店列表</a></li>
	<?php  if($_W['role'] != 'operator') { ?><li <?php  if($op == 'post' && !$id) { ?> class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('store', array('op' => 'post'));?>">添加门店</a></li><?php  } ?>
	<?php  if($op == 'post' && $id) { ?><li class="active"><a href="<?php  echo $this->createWebUrl('store', array('op' => 'post', 'id' => $id));?>">编辑门店</a></li><?php  } ?>
	<?php  if($_GPC['__sid'] > 0) { ?><li><a href="<?php  echo $this->createWebUrl('switch', array('sid' => $_GPC['__sid']));?>">继续管理 <?php  echo $csotre['title'];?></a></li><?php  } ?>
</ul>
<?php  if($_W['role'] == 'manager' || !empty($_W['isfounder'])) { ?>
	<div class="alert alert-info">
		<i class="fa fa-info-circle"></i> 商户登陆地址:<a href="<?php  echo $_W['siteroot'];?>addons/we7_wmall/admin" target="_blank"><?php  echo $_W['siteroot'];?>addons/we7_wmall/admin</a>
	</div>
<?php  } ?>
<?php  if($op == 'post') { ?>
<form class="form-horizontal form" id="form1" action="" method="post" enctype="multipart/form-data">
<input type="hidden" name="id" value="<?php  echo $id;?>">
<div class="panel panel-default">
	<div class="panel-body">
		<ul class="nav nav-pills">
			<li role="presentation" <?php  if($_GPC['type'] == 'basic' || !$_GPC['type']) { ?>class="active"<?php  } ?>><a href="#basic" aria-controls="basic" role="tab" data-toggle="pill">基本信息</a></li>
			<li role="presentation" <?php  if($_GPC['type'] == 'instore') { ?>class="active"<?php  } ?>><a href="#instore" aria-controls="takeout" role="tab" data-toggle="pill">店内点餐设置</a></li>
			<li role="presentation" <?php  if($_GPC['type'] == 'takeout') { ?>class="active"<?php  } ?>><a href="#takeout" aria-controls="takeout" role="tab" data-toggle="pill">配送费&配送区域</a></li>
			<li role="presentation" <?php  if($_GPC['type'] == 'page') { ?>class="active"<?php  } ?>><a href="#page" aria-controls="page" role="tab" data-toggle="pill">页面设置</a></li>
			<li role="presentation" <?php  if($_GPC['type'] == 'high') { ?>class="active"<?php  } ?>><a href="#high" aria-controls="" role="tab" data-toggle="pill">支付设置</a></li>
			<li role="presentation" <?php  if($_GPC['type'] == 'remind') { ?>class="active"<?php  } ?>><a href="#remind" aria-controls="" role="tab" data-toggle="pill">催单回复</a></li>
			<li role="presentation" <?php  if($_GPC['type'] == 'comment') { ?>class="active"<?php  } ?>><a href="#comment" aria-controls="" role="tab" data-toggle="pill">评价回复</a></li>
			<li role="presentation" <?php  if($_GPC['type'] == 'template') { ?>class="active"<?php  } ?>><a href="#template" aria-controls="" role="tab" data-toggle="pill">模板配置</a></li>
		</ul>
	</div>
</div>
<?php  if($item['id'] > 0) { ?>
	<div class="panel panel-default clip">
		<div class="panel-body">
			<p style="margin: 0px"><strong>门店访问地址 :</strong> <a href="javascript:;" title="点击复制链接"><?php  echo $sys_url;?></a></p>
			<?php  if(!empty($wx_url)) { ?>
			<p style="margin: 0px"><strong>微信二维码链接 :</strong> <a href="javascript:;" title="点击复制链接"><?php  echo $wx_url;?></a> (推荐使用)</p>
			<?php  } ?>
			<p style="margin: 0px" class="text-danger">温馨提示: 您可以使用以上链接,在第三方网址自己生成二维码,用户用户关注并下单.</p>
		</div>
	</div>
<?php  } ?>
<div class="tab-content">
<div class="panel panel-default tab-pane <?php  if($_GPC['type'] == 'basic' || !$_GPC['type']) { ?>active<?php  } ?>" role="tabpanel" id="basic">
	<div class="panel-heading">门店基本信息</div>
	<div class="panel-body">
		<div class="form-group">
			<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="require">* </span>门店名称</label>
			<div class="col-sm-9 col-xs-12">
				<input type="text" class="form-control" name="title" value="<?php  echo $item['title'];?>">
			</div>
		</div>
		<div class="form-group">
			<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="require">* </span>所属分类</label>
			<div class="col-sm-9 col-xs-12">
				<div class="checkbox">
					<?php  if(is_array($categorys)) { foreach($categorys as $category) { ?>
						<?php  if($category['is_sys'] == 1) { ?>
							<label><input type="checkbox" name="cid[]" value="<?php  echo $category['id'];?>" <?php  if(in_array($category['id'], $item['cid'])) { ?>checked<?php  } ?>> <?php  echo $category['title'];?> &nbsp;&nbsp;</label>
						<?php  } ?>
					<?php  } } ?>
				</div>
				<span class="help-block">选择门店分类，可多选</span>
			</div>
		</div>
		<div class="form-group">
			<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="require">* </span>门店LOGO</label>
			<div class="col-sm-9 col-xs-12">
				<?php  echo tpl_form_field_image('logo', $item['logo']);?>
			</div>
		</div>
		<div class="form-group">
			<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="require">* </span>门店描述</label>
			<div class="col-sm-9 col-xs-12">
				<input type="text" class="form-control" name="content" value="<?php  echo $item['content'];?>">
				<div class="help-block">粉丝分享时调用.</div>
			</div>
		</div>
		<div class="form-group">
			<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="require">* </span>门店电话</label>
			<div class="col-sm-9 col-xs-12">
				<input type="text" class="form-control" name="telephone" value="<?php  echo $item['telephone'];?>">
			</div>
		</div>
		<div id="hour">
			<div id="hour-tpl" class="clockpicker">
				<?php  if(!empty($item['business_hours'])) { ?>
					<?php  if(is_array($item['business_hours'])) { foreach($item['business_hours'] as $hour) { ?>
						<div class="form-group hour-item">
							<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="require">* </span>营业时间</label>
							<div class="col-sm-9 col-xs-4 col-md-3">
								<div class="input-group">
									<input type="text" readonly name="business_start_hours[]" class="form-control" placeholder="" value="<?php  echo $hour['s'];?>">
									<span class="input-group-addon">至</span>
									<input type="text" readonly name="business_end_hours[]" class="form-control" placeholder="" value="<?php  echo $hour['e'];?>">
								</div>
							</div>
							<div class="col-sm-9 col-xs-4 col-md-3" style="padding-top:6px">
								<a href="javascript:;" onclick="$(this).parent().parent().remove()"><i class="fa fa-times-circle" title="删除时间段"> </i></a>
							</div>
						</div>
					<?php  } } ?>
				<?php  } else { ?>
					<div class="form-group hour-item">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="require">* </span>营业时间</label>
						<div class="col-sm-9 col-xs-4 col-md-3">
							<div class="input-group">
								<input type="text" readonly name="business_start_hours[]" class="form-control" placeholder="">
								<span class="input-group-addon">至</span>
								<input type="text" readonly name="business_end_hours[]" class="form-control" placeholder="">
							</div>
						</div>
						<div class="col-sm-9 col-xs-4 col-md-3" style="padding-top:6px">
							<a href="javascript:;" onclick="$(this).parent().parent().remove()"><i class="fa fa-times-circle" title="删除时间段"> </i></a>
						</div>
					</div>
				<?php  } ?>
			</div>
		</div>
		<div class="form-group">
			<label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
			<div class="col-sm-9 col-xs-9 col-md-9">
				<a href="javascript:;" id="hour-add"><i class="fa fa-plus-circle"></i> 添加营业时间</a>
				<div class="help-block">请完善营业时间信息。最多可添加3个时间段</div>
			</div>
		</div>
		<div class="form-group">
			<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="require">* </span>门店特色</label>
			<div class="col-sm-9 col-xs-9 col-md-9">
				<?php  echo itpl_ueditor('description', $item['description']);?>
			</div>
		</div>
		<div class="form-group">
			<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="require">* </span>门店实景</label>
			<div class="col-sm-9 col-xs-9 col-md-9 thumbs">
				<a href="javascript:;" class="btn btn-primary" id="selectThumb">选择图片</a>
				<div class="help-block">建议图片尺寸:640*120</div>
				<?php  if(!empty($item['thumbs'])) { ?>
				<?php  if(is_array($item['thumbs'])) { foreach($item['thumbs'] as $slide) { ?>
				<div class="col-lg-3">
					<input type="hidden" name="thumbs[image][]" value="<?php  echo $slide['image'];?>">
					<div class="panel panel-default panel-slide">
						<div class="btnClose" onclick="$(this).parent().parent().remove()"><i class="fa fa-times"></i></div>
						<div class="panel-body">
							<img src="<?php  echo tomedia($slide['image']);?>" alt="" width="100%" height="170">
							<div>
								<input class="form-control last pull-right" placeholder="跳转链接" name="thumbs[url][]" value="<?php  echo $slide['url'];?>">
							</div>
						</div>
					</div>
				</div>
				<?php  } } ?>
				<?php  } ?>
				<div id="slideContainer"></div>
			</div>
		</div>
		<div class="form-group">
			<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="require">* </span>详细地址</label>
			<div class="col-sm-9 col-xs-9 col-md-9">
				<input type="text" name="address" class="form-control" value="<?php  echo $item['address'];?>">
				<div class="help-block">请尽可能详细. 商家自提地址为这里设置的地址.</div>
			</div>
		</div>
		<div class="form-group">
			<label class="col-xs-12 col-sm-3 col-md-2 control-label">地图标识</label>
			<div class="col-sm-9 col-xs-9 col-md-9">
				<?php  echo tpl_form_field_tiny_coordinate('map', $item['map']);?>
			</div>
		</div>
		<div class="form-group">
			<label class="col-xs-12 col-sm-3 col-md-2 control-label">商家QQ</label>
			<div class="col-sm-9 col-xs-9 col-md-9">
				<input type="text" class="form-control" name="sns[qq]" value="<?php  echo $item['sns']['qq'];?>">
			</div>
		</div>
		<div class="form-group">
			<label class="col-xs-12 col-sm-3 col-md-2 control-label">商家微信</label>
			<div class="col-sm-9 col-xs-9 col-md-9">
				<input type="text" class="form-control" name="sns[weixin]" value="<?php  echo $item['sns']['weixin'];?>">
			</div>
		</div>
		<div class="form-group <?php  if($_W['role'] == 'operator') { ?>hide<?php  } ?>">
			<label class="col-xs-12 col-sm-3 col-md-2 control-label">排序</label>
			<div class="col-sm-9 col-xs-9 col-md-9">
				<input type="text" class="form-control" name="displayorder" value="<?php  echo $item['displayorder'];?>">
				<div class="help-block">数字越大，越靠前</div>
			</div>
		</div>
	</div>
</div>
<div class="panel panel-default tab-pane <?php  if($_GPC['type'] == 'instore') { ?>active<?php  } ?>" role="tabpanel" id="instore">
	<div class="panel-heading">店内点餐设置</div>
	<div class="panel-body">
		<div class="form-group">
			<label class="col-xs-12 col-sm-3 col-md-2 control-label">排号功能</label>
			<div class="col-sm-9 col-xs-9 col-md-9">
				<label class="radio-inline"><input type="radio" name="is_assign" value="1" <?php  if($item['is_assign'] == 1) { ?>checked<?php  } ?>> 开启</label>
				<label class="radio-inline"><input type="radio" name="is_assign" value="0" <?php  if(!$item['is_assign']) { ?>checked<?php  } ?>> 关闭</label>
			</div>
		</div>
		<div class="form-group">
			<label class="col-xs-12 col-sm-3 col-md-2 control-label">预定功能</label>
			<div class="col-sm-9 col-xs-9 col-md-9">
				<label class="radio-inline"><input type="radio" name="is_reserve" value="1" <?php  if($item['is_reserve'] == 1) { ?>checked<?php  } ?>> 开启</label>
				<label class="radio-inline"><input type="radio" name="is_reserve" value="0" <?php  if(!$item['is_reserve']) { ?>checked<?php  } ?>> 关闭</label>
			</div>
		</div>
		<div class="form-group">
			<label class="col-xs-12 col-sm-3 col-md-2 control-label">店内点餐</label>
			<div class="col-sm-9 col-xs-9 col-md-9">
				<label class="radio-inline"><input type="radio" name="is_meal" value="1" <?php  if($item['is_meal'] == 1) { ?>checked<?php  } ?>> 开启</label>
				<label class="radio-inline"><input type="radio" name="is_meal" value="0" <?php  if(!$item['is_meal']) { ?>checked<?php  } ?>> 关闭</label>
			</div>
		</div>
		<div class="form-group">
			<label class="col-xs-12 col-sm-3 col-md-2 control-label">点击门店直接跳转到</label>
			<div class="col-sm-9 col-xs-9 col-md-9">
				<label class="radio-inline"><input type="radio" name="forward_mode" value="0" <?php  if(!$item['forward_mode']) { ?>checked<?php  } ?>> 默认页(外卖)</label>
				<label class="radio-inline"><input type="radio" name="forward_mode" value="1" <?php  if($item['forward_mode'] == 1) { ?>checked<?php  } ?>> 门店详情页</label>
				<label class="radio-inline"><input type="radio" name="forward_mode" value="3" <?php  if($item['f orward_mode'] == 3) { ?>checked<?php  } ?>> 排队</label>
				<label class="radio-inline"><input type="radio" name="forward_mode" value="4" <?php  if($item['forward_mode'] == 4) { ?>checked<?php  } ?>> 预定</label>
			</div>
		</div>
		<div class="form-group">
			<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="require">* </span>服务费设置</label>
			<div class="col-sm-6 col-xs-6">
				<div class="input-group">
					<label class="input-group-addon">
						<input type="radio" name="serve_fee[type]" value="1" <?php  if($item['serve_fee']['type'] == 1 || !$item['serve_fee']['type']) { ?>checked<?php  } ?>>
					</label>
					<span class="input-group-addon">每单固定</span>
					<input type="text" class="form-control" name="serve_fee[fee_1]" <?php  if($item['serve_fee']['type'] == 1) { ?>value="<?php  echo $item['serve_fee']['fee'];?>"<?php  } ?>>
					<span class="input-group-addon">元</span>
				</div>
				<br>
				<div class="input-group">
					<label class="input-group-addon">
						<input type="radio" name="serve_fee[type]" value="2" <?php  if($item['serve_fee']['type'] == 2) { ?>checked<?php  } ?>>
					</label>
					<span class="input-group-addon">每单按照订单价格收取</span>
					<input type="text" class="form-control" name="serve_fee[fee_2]" <?php  if($item['serve_fee']['type'] == 2) { ?>value="<?php  echo $item['serve_fee']['fee'];?>"<?php  } ?>>
					<span class="input-group-addon">%</span>
				</div>
				<div class="help-block text-danger">服务费只在店内点餐时候有效, 外卖订单无效</div>
			</div>
		</div>
	</div>
</div>
<div class="panel panel-default tab-pane <?php  if($_GPC['type'] == 'takeout') { ?>active<?php  } ?>" role="tabpanel" id="takeout">
	<div class="panel-heading">外卖设置</div>
	<div class="panel-body">
		<div class="form-group">
			<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="require">* </span>包装费</label>
			<div class="col-sm-9 col-xs-9 col-md-9">
				<div class="input-group">
					<input type="text" class="form-control" name="pack_price" value="<?php  echo $item['pack_price'];?>">
					<span class="input-group-addon">元</span>
				</div>
			</div>
		</div>
		<div class="form-group">
			<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="require">* </span>预计送达时间</label>
			<div class="col-sm-9 col-xs-9 col-md-9">
				<div class="input-group">
					<input type="text" class="form-control" name="delivery_time" value="<?php  echo $item['delivery_time'];?>">
					<span class="input-group-addon">分钟</span>
				</div>
			</div>
		</div>
		<div class="form-group">
			<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="require">* </span>服务半径</label>
			<div class="col-sm-9 col-xs-9 col-md-9">
				<div class="input-group">
					<input type="text" class="form-control" name="serve_radius" value="<?php  echo $item['serve_radius'];?>">
					<span class="input-group-addon">KM</span>
				</div>
			</div>
		</div>
		<div class="form-group">
			<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="require">* </span>收货地址是否自动获取</label>
			<div class="col-sm-9 col-xs-9 col-md-9">
				<label class="radio-inline"><input type="radio" name="auto_get_address" value="1" <?php  if($item['auto_get_address'] == 1) { ?>checked<?php  } ?>> 是, 高德地图自动获取</label>
				<label class="radio-inline"><input type="radio" name="auto_get_address" value="0" <?php  if(!$item['auto_get_address']) { ?>checked<?php  } ?>> 否, 用户自己填写</label>
				<span class="help-block">设置为用户自己填写后, 将不能获取用户的具体位置, 不能实现超出服务范围禁制下单的功能</span>
			</div>
		</div>
		<div class="form-group">
			<label class="col-xs-12 col-sm-3 col-md-2 control-label">在配送半径之外是否允许下单</label>
			<div class="col-sm-9 col-xs-9 col-md-9">
				<label class="radio-inline">
					<input type="radio" name="not_in_serve_radius" value="1" <?php  if($item['not_in_serve_radius'] == 1) { ?>checked<?php  } ?>> 允许
				</label>
				<label class="radio-inline">
					<input type="radio" name="not_in_serve_radius" value="0" <?php  if(!$item['not_in_serve_radius']) { ?>checked<?php  } ?>> 不允许
				</label>
				<div class="help-block"><span class="text-danger">距离大于配送半径时是否允许下单，注意：手机定位精确性受天气、用户终端设备是否开启GPS以及硬件配置等影响很大，若此项设置为不允许下单，可能会导致部分用户无法成功下单.</span></div>
			</div>
		</div>

		<div class="form-group">
			<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="require">* </span>配送区域</label>
			<div class="col-sm-9 col-xs-9 col-md-9">
				<input type="text" class="form-control" name="delivery_area" value="<?php  echo $item['delivery_area'];?>">
			</div>
		</div>
		<div class="form-group">
			<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="require">* </span>配送方式</label>
			<div class="col-sm-9 col-xs-9 col-md-9">
				<label class="radio-inline"><input type="radio" name="delivery_type" value="1" <?php  if($item['delivery_type'] == 1 || !$item['delivery_type']) { ?>checked<?php  } ?>> 仅支持商家配送</label>
				<label class="radio-inline"><input type="radio" name="delivery_type" value="2" <?php  if($item['delivery_type'] == 2) { ?>checked<?php  } ?>> 仅支持到店自提</label>
				<label class="radio-inline"><input type="radio" name="delivery_type" value="3" <?php  if($item['delivery_type'] == 3) { ?>checked<?php  } ?>> 商家配送,到店自提都支持</label>
				<div class="help-block">商家自提地址为门店信息里的地址.</div>
			</div>
		</div>
		<div class="form-group">
			<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="require">* </span>起送价</label>
			<div class="col-sm-9 col-xs-9 col-md-9">
				<div class="input-group">
					<input type="text" class="form-control" name="send_price" value="<?php  echo $item['send_price'];?>">
					<span class="input-group-addon">元</span>
				</div>
			</div>
		</div>
		<?php  if($item['delivery_mode'] == 1) { ?>
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="require">* </span>配送费</label>
				<div class="col-sm-9 col-xs-9 col-md-9">
					<div class="input-group">
						<input type="text" class="form-control" name="delivery_price" value="<?php  echo $item['delivery_price'];?>">
						<span class="input-group-addon">元</span>
					</div>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="require">* </span>满多少元免配送费</label>
				<div class="col-sm-9 col-xs-9 col-md-9">
					<div class="input-group">
						<input type="text" class="form-control" name="delivery_free_price" value="<?php  echo $item['delivery_free_price'];?>">
						<span class="input-group-addon">元</span>
					</div>
					<div class="help-block">当用户订单满**元,不收取配送费</div>
				</div>
			</div>
		<?php  } else { ?>
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="require">* </span>配送费</label>
				<div class="col-sm-9 col-xs-9 col-md-9">
					<p class="form-control-static">当前门店的配送模式为:<strong class="text-danger">平台配送</strong>, 配送费为<strong class="text-danger"><?php  echo $item['delivery_price'];?>元</strong>. 您没有权限修改配送费</p>
				</div>
			</div>
		<?php  } ?>
		<div class="form-group">
			<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="require">* </span>可提前几天点外卖</label>
			<div class="col-sm-9 col-xs-9 col-md-9">
				<input type="text" class="form-control" name="delivery_within_days" value="<?php  echo $item['delivery_within_days'];?>">
				<div class="help-block">单位：天，如果只接受当天订单，请填写0. 最多可提前6天</div>
			</div>
		</div>
		<div class="form-group">
			<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="require">* </span>需提前几天预定外卖</label>
			<div class="col-sm-9 col-xs-9 col-md-9">
				<input type="text" class="form-control" name="delivery_reserve_days" value="<?php  echo $item['delivery_reserve_days'];?>">
				<div class="help-block">单位：天，如果不需要提前预定，请填写0. 如果设置提前1天预定, 用户今天下单后, 能选择明天的配送时间进行配送</div>
			</div>
		</div>
		<div class="form-group">
			<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="require">* </span>配送时间段</label>
			<div class="col-sm-9 col-xs-9 col-md-4">
				<div id="times-container">
					<?php  if(is_array($delivery_times)) { foreach($delivery_times as $delivery_time) { ?>
					<div class="input-group clockpicker" style="margin-bottom: 15px">
						<input type="hidden" class="form-control" name="ids[]" value="<?php  echo $delivery_time['id'];?>">
						<input type="text" class="form-control" name="starttime[]" value="<?php  echo $delivery_time['start'];?>">
						<span class="input-group-addon">至</span>
						<input type="text" class="form-control" name="endtime[]" value="<?php  echo $delivery_time['end'];?>">
						<span class="input-group-addon"> <a href="javascript:;" class="times-del"><i class="fa fa-times"></i></a></span>
					</div>
					<?php  } } ?>
				</div>
				<div><a href="javascript:;" id="times-add"><i class="fa fa-plus-circle"></i> 添加时间段</a></div>
			</div>
		</div>

		<?php  if($_W['we7_wmall']['config']['sms']['status'] == 1) { ?>
		<div class="form-group hide">
			<label class="col-xs-12 col-sm-3 col-md-2 control-label">首次下单短信验证</label>
			<div class="col-sm-9 col-xs-9 col-md-9">
				<label class="radio-inline"><input type="radio" name="mobile_verify[first_verify]" value="1" <?php  if($item['mobile_verify']['first_verify'] == 1) { ?>checked<?php  } ?>> 验证</label>
				<label class="radio-inline"><input type="radio" name="mobile_verify[first_verify]" value="0" <?php  if(!$item['mobile_verify']['first_verify']) { ?>checked<?php  } ?>> 不验证</label>
				<span class="help-block">
					<strong class="text-danger"></strong>
				</span>
			</div>
		</div>
		<?php  } ?>
	</div>
</div>
<div class="panel panel-default tab-pane <?php  if($_GPC['type'] == 'page') { ?>active<?php  } ?>" role="tabpanel" id="page">
	<div class="panel-heading" id="aa">页面设置</div>
	<div class="panel-body">
		<div class="form-group">
			<label class="col-xs-12 col-sm-3 col-md-2 control-label">公告</label>
			<div class="col-sm-9 col-xs-9 col-md-9">
				<input type="text" name="notice" value="<?php  echo $item['notice'];?>" class="form-control">
				<div class="help-block">手机端将以滚动的方式展示</div>
			</div>
		</div>
		<div class="form-group">
			<label class="col-xs-12 col-sm-3 col-md-2 control-label">进入商品列表页提示</label>
			<div class="col-sm-9 col-xs-9 col-md-9">
				<input type="text" name="tips" value="<?php  echo $item['tips'];?>" class="form-control">
				<div class="help-block">用户进入页面后, 将弹框提示设置的内容</div>
			</div>
		</div>
		<div class="form-group">
			<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="require"> </span>自定义订单备注</label>
			<div class="col-sm-6 col-xs-6">
				<?php  if(!empty($item['order_note'])) { ?>
					<?php  if(is_array($item['order_note'])) { foreach($item['order_note'] as $order_note) { ?>
						<div class="btn-group btn-label">
							<input type="hidden" name="order_note[]" value="<?php  echo $order_note;?>">
							<a class="btn btn-default border-radius-4"><?php  echo $order_note;?></a>
							<a class="btn btn-default">
								<span class="fa fa-times-circle label-delete"></span>
							</a>
						</div>
					<?php  } } ?>
				<?php  } ?>
				<a class="btn btn-success label-add"><i class="fa fa-plus-circle"></i> 添加</a>
				<div class="help-block">例如: 带点醋, 不吃辣, 自带零钱, 延时送餐等</div>
			</div>
		</div>
		<div class="form-group">
			<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="require"> </span>是否需要审核评论</label>
			<div class="col-sm-9 col-xs-12">
				<label class="radio-inline">
					<input type="radio" value="1" name="comment_status" <?php  if($item['comment_status'] == 1) { ?>checked<?php  } ?>> 不需要审核
				</label>
				<label class="radio-inline">
					<input type="radio" value="0" name="comment_status" <?php  if($item['comment_status'] == 0) { ?>checked<?php  } ?>> 需要审核
				</label>
				<span class="help-block">设置为"需要审核",用户评论后,需要管理员审核后才能显示到前台</span>
			</div>
		</div>
		<div id="custom-url">
			<?php  if(!empty($item['custom_url'])) { ?>
			<?php  if(is_array($item['custom_url'])) { foreach($item['custom_url'] as $url) { ?>
			<div class="form-group item">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">门店详情页自定义链接</label>
				<div class="col-sm-6">
					<div class="input-group">
						<span class="input-group-addon">链接文字</span>
						<input type="text" class="form-control" name="custom_title[]" value="<?php  echo $url['title'];?>">
						<span class="input-group-addon">链接地址</span>
						<input type="text" class="form-control" name="custom_link[]" value="<?php  echo $url['url'];?>">
					</div>
				</div>
				<div class="col-sm-1" style="margin-top:5px">
					<a href="javascript:;" class="custom-url-del"><i class="fa fa-times-circle"></i> </a>
				</div>
			</div>
			<?php  } } ?>
			<?php  } else { ?>
			<div class="form-group item">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">门店详情页自定义链接</label>
				<div class="col-sm-6">
					<div class="input-group">
						<span class="input-group-addon">链接文字</span>
						<input type="text" class="form-control" name="custom_title[]" value="">
						<span class="input-group-addon">链接地址</span>
						<input type="text" class="form-control" name="custom_link[]" value="">
					</div>
				</div>
				<div class="col-sm-1" style="margin-top:5px">
					<a href="javascript:;" class="custom-url-del"><i class="fa fa-times-circle"></i> </a>
				</div>
			</div>
			<?php  } ?>
		</div>
		<div class="form-group">
			<label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
			<div class="col-sm-9 col-xs-12">
				<a href="javascript:;" id="custom-url-add"><i class="fa fa-plus-circle"></i> 添加门店详情页自定义链接</a>
				<span class="help-block">该链接将在门店详情页面显示</span>
			</div>
		</div>
	</div>
</div>
<div class="panel panel-default tab-pane <?php  if($_GPC['type'] == 'high') { ?>active<?php  } ?>" role="tabpanel" id="high">
	<div class="panel-heading">高级设置</div>
	<div class="panel-body">
		<div class="form-group">
			<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="require">* </span>后台提示音</label>
			<div class="col-sm-9 col-xs-9 col-md-9">
				<label class="radio-inline"><input type="radio" name="pc_notice_status" value="1" <?php  if($item['pc_notice_status'] == 1) { ?>checked<?php  } ?>> 开启</label>
				<label class="radio-inline"><input type="radio" name="pc_notice_status" value="0" <?php  if(!$item['pc_notice_status']) { ?>checked<?php  } ?>> 关闭</label>
				<span class="help-block">开启后, <span class="bg-danger">当有未处理的订单的时候</span>, 后台会有提示音提示</span>
			</div>
		</div>
		<div class="form-group">
			<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="require">* </span>支付后自动接单</label>
			<div class="col-sm-9 col-xs-9 col-md-9">
				<label class="radio-inline"><input type="radio" name="auto_handel_order" value="1" <?php  if($item['auto_handel_order'] == 1) { ?>checked<?php  } ?>> 开启</label>
				<label class="radio-inline"><input type="radio" name="auto_handel_order" value="0" <?php  if(!$item['auto_handel_order']) { ?>checked<?php  } ?>> 关闭</label>
				<span class="help-block">开启后, 用户下单支付后,系统会自动接单(设置订单为处理中.)</span>
			</div>
		</div>
		<div class="form-group">
			<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="require">* </span>支付后自动通知配送员配送</label>
			<div class="col-sm-9 col-xs-9 col-md-9">
				<label class="radio-inline"><input type="radio" name="auto_notice_deliveryer" value="1" <?php  if($item['auto_notice_deliveryer'] == 1) { ?>checked<?php  } ?>> 开启</label>
				<label class="radio-inline"><input type="radio" name="auto_notice_deliveryer" value="0" <?php  if(!$item['auto_notice_deliveryer']) { ?>checked<?php  } ?>> 关闭</label>
				<span class="help-block">开启后, 用户下单支付后,系统会自动通知配送员进行配送(设置订单为待配送.仅对外卖订单有效).</span>
				<span class="help-block"><span class="bg-danger">开启自动通知配送员功能之前, 必须先开启自动接单功能</span></span>
			</div>
		</div>
		<div class="form-group">
			<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="require">* </span>配送中的订单自动完成</label>
			<div class="col-sm-9 col-xs-9 col-md-9">
				<div class="input-group">
					<span class="input-group-addon">配送中的订单在</span>
					<input type="text" class="form-control" name="auto_end_hours" value="<?php  echo $item['auto_end_hours'];?>">
					<span class="input-group-addon">小时后, 自动设置为完成</span>
				</div>
				<span class="help-block">只有"配送中"的订单在超过设置的时间才会被自动设置为完成. </span>
				<span class="help-block">自动完成时间只能是整数, 如果你不需要自动完成, 可以设置为0. </span>
			</div>
		</div>
		<div class="form-group">
			<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="require">* </span>支持开发票</label>
			<div class="col-sm-9 col-xs-9 col-md-9">
				<label class="radio-inline"><input type="radio" name="invoice_status" value="1" <?php  if($item['invoice_status'] == 1) { ?>checked<?php  } ?>> 支持</label>
				<label class="radio-inline"><input type="radio" name="invoice_status" value="0" <?php  if(!$item['invoice_status']) { ?>checked<?php  } ?>> 不支持</label>
				<span class="help-block">选择支持开发票后,用户在提交订单时可选择是否开发票</span>
			</div>
		</div>
		<div class="form-group">
			<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="require">* </span>支持使用代金券抵付现金</label>
			<div class="col-sm-9 col-xs-9 col-md-9">
				<label class="radio-inline"><input type="radio" name="token_status" value="1" <?php  if($item['token_status'] == 1) { ?>checked<?php  } ?>> 支持</label>
				<label class="radio-inline"><input type="radio" name="token_status" value="0" <?php  if(!$item['token_status']) { ?>checked<?php  } ?>> 不支持</label>
				<span class="help-block">选择支持使用代金券抵付现金,用户在满足代金券使用条件时(必须是在线支付才能使用代金券),可选择是否使用代金券</span>
			</div>
		</div>
		<div class="form-group">
			<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="require">* </span>支付方式</label>
			<div class="col-sm-9 col-xs-9 col-md-9">
				<?php  if(!empty($pay['wechat'])) { ?>
				<label class="checkbox-inline">
					<input type="checkbox" name="payment[]" value="wechat" <?php  if(in_array('wechat', $item['payment'])) { ?>checked<?php  } ?>> 微信支付
				</label>
				<?php  } ?>
				<?php  if(!empty($pay['alipay'])) { ?>
				<label class="checkbox-inline">
					<input type="checkbox" name="payment[]" value="alipay" <?php  if(in_array('alipay', $item['payment'])) { ?>checked<?php  } ?>> 支付宝
				</label>
				<?php  } ?>
				<?php  if(!empty($pay['credit'])) { ?>
				<label class="checkbox-inline">
					<input type="checkbox" name="payment[]" value="credit" <?php  if(in_array('credit', $item['payment'])) { ?>checked<?php  } ?>> 余额支付
				</label>
				<?php  } ?>
				<?php  if(!empty($pay['baifubao'])) { ?>
				<label class="checkbox-inline">
					<input type="checkbox" name="payment[]" value="baifubao" <?php  if(in_array('baifubao', $item['payment'])) { ?>checked<?php  } ?>> 百付宝
				</label>
				<?php  } ?>
				<?php  if(!empty($pay['delivery'])) { ?>
				<label class="checkbox-inline">
					<input type="checkbox" name="payment[]" value="delivery" <?php  if(in_array('delivery', $item['payment'])) { ?>checked<?php  } ?>> 货到付款
				</label>
				<?php  } ?>
				<span class="help-block">商户可根据自己的情况选择支付方式.更多支付方式请联系公众号管理员开通</span>
			</div>
		</div>
		<div class="form-group">
			<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="require"> </span>支付时间限制</label>
			<div class="col-sm-9 col-xs-9 col-md-9">
				<div class="input-group">
					<input type="text" class="form-control" name="pay_time_limit" value="<?php  echo $item['pay_time_limit'];?>">
					<span class="input-group-addon">分钟</span>
				</div>
				<span class="help-block">例如:设置为15分钟,那么用户在提交订单后15分钟内未支付,系统会自动取消该订单.如果没有支付时间限制,请设置为0</span>
				<span class="help-block">该设置仅对"外卖订单"有效.店内订单不受此设置影响</span>
			</div>
		</div>
	</div>
</div>
<div class="panel panel-default tab-pane <?php  if($_GPC['type'] == 'remind') { ?>active<?php  } ?>" role="tabpanel" id="remind">
	<div class="panel-heading">催单回复 <small class="text-danger">当用户有催单时,商家在后台可以直接回复这里设置的回复内容</small></div>
	<div class="panel-body">
		<div class="form-group">
			<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="require"> </span>催单时间间隔</label>
			<div class="col-sm-9 col-xs-9 col-md-9">
				<div class="input-group">
					<input type="text" class="form-control" name="remind_time_limit" value="<?php  echo $item['remind_time_limit'];?>">
					<span class="input-group-addon">分钟</span>
				</div>
				<span class="help-block">用户在下单后可进行多次催单,该项可设置催单间隔.如:用户现在进行催单,如果设置了10分钟的间隔,那么用户下次催单只能在10分钟以后</span>
			</div>
		</div>
		<div id="remind-reply">
			<?php  if(!empty($item['remind_reply'])) { ?>
			<?php  if(is_array($item['remind_reply'])) { foreach($item['remind_reply'] as $reply) { ?>
			<div class="form-group item">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">自定义催单回复</label>
				<div class="col-sm-6">
					<input type="text" class="form-control" name="remind_reply[]" value="<?php  echo $reply;?>">
				</div>
				<div class="col-sm-1" style="margin-top:5px">
					<a href="javascript:;" class="remind-reply-del"><i class="fa fa-times-circle"></i> </a>
				</div>
			</div>
			<?php  } } ?>
			<?php  } else { ?>
			<div class="form-group item">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">自定义催单回复</label>
				<div class="col-sm-6">
					<input type="text" class="form-control" name="remind_reply[]" value="<?php  echo $reply;?>">
				</div>
				<div class="col-sm-1" style="margin-top:5px">
					<a href="javascript:;" class="remind-reply-del"><i class="fa fa-times-circle"></i> </a>
				</div>
			</div>
			<?php  } ?>
		</div>
		<div class="form-group">
			<label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
			<div class="col-sm-9 col-xs-12">
				<a href="javascript:;" class="remind-reply-add"><i class="fa fa-plus-circle"></i> 添加自定义催单回复</a>
			</div>
		</div>
	</div>
</div>
<div class="panel panel-default tab-pane <?php  if($_GPC['type'] == 'comment') { ?>active<?php  } ?>" role="tabpanel" id="comment">
	<div class="panel-heading">评价回复 <small class="text-danger">当用户对订单进行评价时,商家在后台可以直接回复这里设置的回复内容</small></div>
	<div class="panel-body">
		<div id="comment-reply">
			<?php  if(!empty($item['comment_reply'])) { ?>
			<?php  if(is_array($item['comment_reply'])) { foreach($item['comment_reply'] as $creply) { ?>
			<div class="form-group item">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">自定义评价回复</label>
				<div class="col-sm-6">
					<textarea class="form-control" name="comment_reply[]"><?php  echo $creply;?></textarea>
				</div>
				<div class="col-sm-1" style="margin-top:5px">
					<a href="javascript:;" class="comment-reply-del"><i class="fa fa-times-circle"></i> </a>
				</div>
			</div>
			<?php  } } ?>
			<?php  } else { ?>
			<div class="form-group item">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">自定义评价回复</label>
				<div class="col-sm-6">
					<textarea class="form-control" name="comment_reply[]"></textarea>
				</div>
				<div class="col-sm-1" style="margin-top:5px">
					<a href="javascript:;" class="comment-reply-del"><i class="fa fa-times-circle"></i> </a>
				</div>
			</div>
			<?php  } ?>
		</div>
		<div class="form-group">
			<label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
			<div class="col-sm-9 col-xs-12">
				<a href="javascript:;" class="comment-reply-add"><i class="fa fa-plus-circle"></i> 添加自定义评价回复</a>
			</div>
		</div>
	</div>
</div>
<div class="panel panel-default tab-pane <?php  if($_GPC['type'] == 'template') { ?>active<?php  } ?>" role="tabpanel" id="template">
	<div class="panel-heading">模板配置 <small class="text-danger"></small></div>
	<div class="panel-body">
		<div class="form-group">
			<label class="col-xs-12 col-sm-3 col-md-2 control-label">商品列表页风格</label>
			<div class="col-sm-9 col-xs-9 col-md-9">
				<a href="<?php  echo $this->createWebUrl('store', array('op' => 'template', 'id' => $item['id'], 't' => 'index'));?>" onclick="if(!confirm('确定更换模块类型吗')) return false;" class="thumbnail <?php  if($item['template'] == 'index' || !$item['template']) { ?>active<?php  } ?>" style="width:200px; float:left; margin-right:20px; border-width: 5px">
					<img src="<?php echo MODULE_URL;?>template/mobile/default/purview/index.png">
				</a>
				<a href="<?php  echo $this->createWebUrl('store', array('op' => 'template', 'id' => $item['id'], 't' => 'market'));?>" onclick="if(!confirm('确定更换模块类型吗')) return false;" class="thumbnail <?php  if($item['template'] == 'market') { ?>active<?php  } ?>" style="width:200px; float:left; margin-right:20px; border-width: 5px">
					<img src="<?php echo MODULE_URL;?>template/mobile/default/purview/market.png">
				</a>
			</div>
		</div>
	</div>
</div>
<div>

	<div class="form-group">
		<div class="col-sm-9 col-xs-9 col-md-9">
			<input type="hidden" name="token" value="<?php  echo $_W['token'];?>">
			<input name="submit" id="submit" type="submit" value="提交" class="btn btn-primary col-lg-1">
		</div>
	</div>
</form>
<script type="text/javascript">
	require(['util', 'clockpicker'], function(u, $){
		$('.clockpicker :text').clockpicker({autoclose: true});

		$('#form1').submit(function(){
			if($.trim($(':text[name="title"]').val()) == '') {
				u.message('请填写门店名称');
				return false;
			}
			if(!$(':checkbox[name="cid[]"]:checked').length()) {
				u.message('请选择所属分类');
				return false;
			}
			if($.trim($(':text[name="logo"]').val()) == '') {
				u.message('请上传门店LOGO');
				return false;
			}
			if($.trim($(':text[name="telephone"]').val()) == '') {
				u.message('请填写门店电话');
				return false;
			}
			if(!$.trim($(':text[name="address"]').val())) {
				u.message("请填写详细地址");
				return false;
			}
			return true;
		});

		$('#selectImage').click(function(){
			util.uploadMultiPictures(function(images){
				var s = '';
				$.each(images, function(){
					s += '<div class="col-lg-3">'+
							'	<input type="hidden" name="thumbs[image][]" value="'+this.filename+'">' +
							'	<div class="panel panel-default panel-slide">'+
							'		<div class="btnClose" onclick="$(this).parent().parent().remove()"><i class="fa fa-times"></i></div>' +
							'		<div class="panel-body">'+
							'			<img src="'+this.url+'" width="100%" height="170">'+
							'			<div>'+
							'				<input class="form-control last pull-right" placeholder="跳转链接" name="thumbs[url][]">'+
							'			</div>'+
							'		</div>'+
							'	</div>'+
							'</div>'
				});
				$('#slideContainer').append(s);
			});
		});

		$('#hour-add').click(function(){
			var hour_length = $('#hour .hour-item').length;
			if(hour_length < 3) {
				var html = '<div class="form-group hour-item clockpicker">' +
						'<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="require">* </span>营业时间</label>'+
						'<div class="col-sm-9 col-xs-4 col-md-3">'+
						'<div class="input-group">'+
						'<input type="text" readonly name="business_start_hours[]" class="form-control" placeholder=""> '+
						'<span class="input-group-addon">至</span>'+
						'<input type="text" readonly name="business_end_hours[]" class="form-control" placeholder=""> '+
						'</div>'+
						'</div>'+
						'<div class="col-sm-9 col-xs-4 col-md-3" style="padding-top:6px">'+
						'<a href="javascript:;" onclick="$(this).parent().parent().remove()"><i class="fa fa-times-circle" title="删除时间段"> </i></a>'+
						'</div>'+
						'</div>';
				$('#hour').append(html);
				$('.clockpicker :text').clockpicker({autoclose: true});
			} else {
				u.message('最多可添加3个时间段', '', 'error');
				return false;
			}
		});

		$('.remind-reply-add').click(function(){
			var html ='	<div class="form-group item">'+
					'	<label class="col-xs-12 col-sm-3 col-md-2 control-label">自定义催单回复</label>'+
					'	<div class="col-sm-6">'+
					'		<input type="text" class="form-control" name="remind_reply[]" value="">'+
					'	</div>'+
					'	<div class="col-sm-1" style="margin-top:5px">'+
					'		<a href="javascript:;" class="remind-reply-del"><i class="fa fa-times-circle"></i> </a>'+
					'	</div>'+
					'</div>';
			if($('#remind-reply .item').size() >= 15) {
				util.message('最多可添加15个自定义催单回复', '', 'error');
				return false;
			}
			$('#remind-reply').append(html);
		});

		$('.comment-reply-add').click(function(){
			var html ='	<div class="form-group item">'+
					'	<label class="col-xs-12 col-sm-3 col-md-2 control-label">自定义评价回复</label>'+
					'	<div class="col-sm-6">'+
					'		<textarea class="form-control" name="comment_reply[]"></textarea>'+
					'	</div>'+
					'	<div class="col-sm-1" style="margin-top:5px">'+
					'		<a href="javascript:;" class="comment-reply-del"><i class="fa fa-times-circle"></i> </a>'+
					'	</div>'+
					'</div>';
			if($('#comment-reply .item').size() >= 15) {
				util.message('最多可添加15个自定义评价回复', '', 'error');
				return false;
			}
			$('#comment-reply').append(html);
		});

		$('#times-add').click(function(){
			var html ='	<div class="input-group clockpicker" style="margin-bottom: 15px">'+
					'		<input type="hidden" class="form-control" name="ids[]" value="">'+
					'		<input type="text" class="form-control" name="starttime[]" value="">'+
					'		<span class="input-group-addon">至</span>'+
					'	<input type="text" class="form-control" name="endtime[]" value="">'+
					'			<span class="input-group-addon"> <a href="javascript:;" class="times-del"><i class="fa fa-times"></i></a></span>'+
					'	</div>';
			$('#times-container').append(html);
			$('.clockpicker :text').clockpicker({autoclose: true});
		});

		$('#custom-url-add').click(function(){
			var html = '<div class="form-group item">'+
						'	<label class="col-xs-12 col-sm-3 col-md-2 control-label">门店详情页自定义链接</label>'+
						'		<div class="col-sm-6">'+
						'			<div class="input-group">'+
						'				<span class="input-group-addon">链接文字</span>'+
						'				<input type="text" class="form-control" name="custom_title[]" value="">'+
						'				<span class="input-group-addon">链接地址</span>'+
						'				<input type="text" class="form-control" name="custom_link[]" value="">'+
						'			</div>'+
						'		</div>'+
						'	<div class="col-sm-1" style="margin-top:5px">'+
						'		<a href="javascript:;" class="custom-url-del"><i class="fa fa-times-circle"></i> </a>'+
						'	</div>'+
						'</div>';
					;
			if($('#custom-url .item').size() >= 3) {
				util.message('最多可添加3个门店详情页自定义链接', '', 'error');
				return false;
			}
			$('#custom-url').append(html);
		});

		$('#selectThumb').click(function(){
			util.uploadMultiPictures(function(images){
				var s = '';
				$.each(images, function(){
					s += '<div class="col-lg-3">'+
							'	<input type="hidden" name="thumbs[image][]" value="'+this.filename+'">' +
							'	<div class="panel panel-default panel-slide">'+
							'		<div class="btnClose" onclick="$(this).parent().parent().remove()"><i class="fa fa-times"></i></div>' +
							'		<div class="panel-body">'+
							'			<img src="'+this.url+'" width="100%" height="170">'+
							'			<div>'+
							'				<input class="form-control last pull-right" placeholder="跳转链接" name="thumbs[url][]">'+
							'			</div>'+
							'		</div>'+
							'	</div>'+
							'</div>'
				});
				$('#slideContainer').append(s);
			});
		});

		$(document).on('click', '.remind-reply-del, .comment-reply-del, .times-del, .custom-url-del', function(){
			$(this).parent().parent().remove();
			return false;
		});
	});
	//订单备注
	$(function(){
		$(document).on('click', '.label-add', function(){
			var $this = $(this);
			tiny.prompt($(this), '', function(data) {
				if(!data) {
					return false;
				}
				var html = '<div class="btn-group btn-label">'+
						'		<input type="hidden" name="order_note[]" value="'+ data +'">'+
						'		<a class="btn btn-default border-radius-4">'+data+'</a>'+
						'		<a class="btn btn-default">'+
						'		    <span class="fa fa-times-circle label-delete"></span>'+
						'	    </a>'+
						'	</div>';
				$this.before(html);
			});
		});

		$(document).on('click', '.label-delete', function(){
			$(this).parents('.btn-group').remove();
		});
	});
</script>
<?php  } else if($op == 'list') { ?>
<div class="clearfix">
	<div class="panel panel-info">
		<div class="panel-heading">筛选</div>
		<div class="panel-body">
			<form action="./index.php" method="get" class="form-horizontal" role="form">
				<input type="hidden" name="c" value="site">
				<input type="hidden" name="a" value="entry">
				<input type="hidden" name="m" value="we7_wmall">
				<input type="hidden" name="do" value="store"/>
				<input type="hidden" name="op" value="list"/>
				<div class="form-group clearfix">
					<label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">所属分类</label>
					<div class="col-sm-7 col-lg-8 col-md-8 col-xs-12">
						<select class="form-control" id="cid" name="cid">
							<option value="0">不限</option>
							<?php  if(is_array($categorys)) { foreach($categorys as $category) { ?>
								<option value="<?php  echo $category['id'];?>" <?php  if($cid == $category['id']) { ?>selected<?php  } ?>><?php  echo $category['title'];?></option>
							<?php  } } ?>
						</select>
					</div>
				</div>
				<div class="form-group clearfix">
					<label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">门店名称</label>
					<div class="col-sm-7 col-lg-8 col-md-8 col-xs-12">
						<input class="form-control" name="keyword" id="" type="text" value="<?php  echo $_GPC['keyword'];?>">
					</div>
					<div class="col-xs-12 col-sm-3 col-md-2 col-lg-1">
						<button class="btn btn-default"><i class="fa fa-search"></i> 搜索</button>
					</div>
				</div>
			</form>
		</div>
	</div>
	<form class="form-horizontal" action="" method="post">
		<div class="panel panel-default">
			<div class="panel-body table-responsive">
				<table class="table table-hover">
					<thead class="navbar-inner">
					<tr>
						<th>门店logo</th>
						<th>门店名称</th>
						<th>所属分类</th>
						<th>店铺状态</th>
						<th>是否显示</th>
						<?php  if($_W['role'] == 'manager' || !empty($_W['isfounder'])) { ?>
							<th>推荐</th>
							<th width="100">热度</th>
							<th>排序</th>
						<?php  } ?>
						<th style="width:400px; text-align:right;">操作</th>
					</tr>
					</thead>
					<tbody>
					<?php  if(is_array($lists)) { foreach($lists as $item) { ?>
					<tr>
						<td><img src="<?php  echo tomedia($item['logo']);?>" width="50"></td>
						<td><?php  echo $item['title'];?></td>
						<td>
							<?php  if(is_array($item['cid'])) { foreach($item['cid'] as $cid) { ?>
								<?php  echo $categorys[$cid]['title'];?>
							<?php  } } ?>
						</td>
						<td>
							<input type="checkbox" value="<?php  echo $item['is_in_business'];?>" name="is_in_business[]" data-id="<?php  echo $item['id'];?>" <?php  if($item['is_in_business'] == 1) { ?>checked<?php  } ?>>
						</td>
						<td>
							<?php  if($item['status'] <= 1) { ?>
								<input type="checkbox" value="<?php  echo $item['status'];?>" name="status[]" data-id="<?php  echo $item['id'];?>" <?php  if($item['status'] == 1) { ?>checked<?php  } ?>>
							<?php  } else { ?>
								<span class="<?php  echo $store_status[$item['status']]['css'];?>"><?php  echo $store_status[$item['status']]['text'];?></span>
							<?php  } ?>
						</td>
						<?php  if($_W['role'] == 'manager' || !empty($_W['isfounder'])) { ?>
							<td>
								<input type="checkbox" value="<?php  echo $item['is_recommend'];?>" name="recommend[]" data-id="<?php  echo $item['id'];?>" <?php  if($item['is_recommend'] == 1) { ?>checked<?php  } ?>>
							</td>
							<td><input type="text" name="click" value="<?php  echo $item['click'];?>" class="form-control form-control-edit" data-sid="<?php  echo $item['id'];?>" data-value="<?php  echo $item['click'];?>" data-type="click" readonly style="width: 70px"/></td>
							<td><input type="text" name="displayorder" value="<?php  echo $item['displayorder'];?>" class="form-control form-control-edit" data-sid="<?php  echo $item['id'];?>" data-value="<?php  echo $item['displayorder'];?>" data-type="displayorder" readonly style="width: 70px"/></td>
						<?php  } ?>
						<td style="text-align:right;">
							<?php  if($_W['role'] == 'manager' || !empty($_W['isfounder'])) { ?>
								<a href="<?php  echo $this->createWebUrl('store', array('op' => 'copy', 'sid' => $item['id']))?>" class="btn btn-default btn-sm" title="复制门店" data-toggle="tooltip" data-placement="top" onclick="if(!confirm('确定复制该门店吗?')) return false;">复制</a>
								<?php  if(empty($item['user'])) { ?>
									<a href="<?php  echo $this->createWebUrl('switch', array('sid' => $item['id'], 'forward' => 'clerk'))?>" class="btn btn-default btn-sm" title="门店" data-toggle="tooltip" data-placement="top">独立账号</a>
								<?php  } ?>
							<?php  } ?>
							<a href="javascript:;" class="btn btn-default btn-sm show-qrcode" data-wx-url="<?php  echo $item['wechat_url'];?>" data-sys-url="<?php  echo $item['sys_url'];?>" data-id="<?php  echo $item['id'];?>" title="二维码&访问地址" data-toggle="tooltip" data-placement="top"><i class="fa fa-qrcode"> </i> 二维码</a>
							<a href="<?php  echo $this->createWebUrl('store', array('op' => 'post', 'id' => $item['id']))?>" class="btn btn-default btn-sm" title="编辑" data-toggle="tooltip" data-placement="top"><i class="fa fa-edit"> </i> 编辑</a>
							<a href="<?php  echo $this->createWebUrl('store', array('op' => 'del', 'id' => $item['id']))?>" class="btn btn-default btn-sm" title="删除" data-toggle="tooltip" data-placement="top" onclick="if(!confirm('删除后将不可恢复，确定删除吗?')) return false;"><i class="fa fa-times"> </i> 删除</a>
							<a href="<?php  echo $this->createWebUrl('switch', array('sid' => $item['id']))?>" class="btn btn-default btn-sm" title="管理门店" data-toggle="tooltip" data-placement="top" style="color:#D9534F;"><i class="fa fa-cog fa-spin"> </i> 管理</a>
						</td>
					</tr>
					<?php  } } ?>
					</tbody>
				</table>
			</div>
		</div>
		<?php  echo $pager;?>
	</form>
</div>

<div class="modal fade" id="qrcode-modal">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h3 class="modal-title">门店二维码&访问地址</h3>
			</div>
			<div class="modal-body">
				<p>访问地址: <a href="javascript:;" class="sys-url"></a></p>
				<div style="text-align: center;">
					<span style="border: 1px solid #CCC; display: inline-block; padding: 20px;" id="wx-qrcode"></span>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
require(['jquery.qrcode', 'bootstrap.switch'], function($){
	$('.show-qrcode').click(function(){
		$('#qrcode-modal .sys-url').html($(this).data('sys-url'));
		var option = {
			render: 'canvas',
			width: 200,
			height: 200,
			colorDark : "#000000",
			colorLight : "#ffffff"
		}
		var wx_url = $(this).data('wx-url');
		if(wx_url) {
			option.text = wx_url;
			$('#wx-qrcode').html('');
			$('#wx-qrcode').qrcode(option);
		} else {
			var sid = $(this).data('id');
			var url = "<?php  echo $this->createWebUrl('ptfqrcode', array('op' => 'build', 'type' => 'store'));?>" + "&store_id=" + sid;
			var html = '<a href="'+url+'" onclick="if(!confirm(\'确定生成吗\')) return false;" class="btn btn-primary">生成微信二维码</a>';
			$('#wx-qrcode').html(html);
		}
		$('#qrcode-modal').modal('show');
	});

	$(':checkbox[name="status[]"]').bootstrapSwitch();
	$(':checkbox[name="status[]"]').on('switchChange.bootstrapSwitch', function(e, state){
		var status = this.checked ? 1 : 0;
		var id = $(this).data('id');
		$.post("<?php  echo $this->createWebUrl('store', array('op' => 'status'))?>", {status: status, id: id}, function(resp){
			setTimeout(function(){
				location.reload();
			}, 500)
		});
	});
	$(':checkbox[name="recommend[]"]').bootstrapSwitch();
	$(':checkbox[name="recommend[]"]').on('switchChange.bootstrapSwitch', function(e, state){
		var recommend = this.checked ? 1 : 0;
		var id = $(this).data('id');
		$.post("<?php  echo $this->createWebUrl('store', array('op' => 'recommend'))?>", {recommend: recommend, id: id}, function(resp){
			setTimeout(function(){
				location.reload();
			}, 500)
		});
	});
	$(':checkbox[name="is_in_business[]"]').bootstrapSwitch();
	$(':checkbox[name="is_in_business[]"]').on('switchChange.bootstrapSwitch', function(e, state){
		var is_in_business = this.checked ? 1 : 0;
		var id = $(this).data('id');
		$.post("<?php  echo $this->createWebUrl('store', array('op' => 'is_in_business'))?>", {is_in_business: is_in_business, id: id}, function(resp){
			setTimeout(function(){
				location.reload();
			}, 500)
		});
	});
	$('.form-control-edit').focus(function(){
		$(this).prop('readonly', false);
	});
	$('.form-control-edit').blur(function(){
		var $this = $(this);
		var sid = $(this).data('sid');
		var value = $(this).data('value');
		var type = $(this).data('type');
		var now_value = parseInt($(this).val());
		if(sid > 0 && type != '' && value != now_value) {
			$.post("<?php  echo $this->createWebUrl('store', array('op' => 'edit'));?>", {sid: sid, type: type, value: now_value}, function(data){
				if(data != 'success') {
					$this.val(value);
				}
			});
		}
		$this.prop('readonly', true);
		return false;
	});
	$('.form-control-edit').keyup(function(e){
		if(e.keyCode == 13) {
			e.preventDefault();
			$('.form-control-edit').blur();
		}
	});
});
</script>
<?php  } ?>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common', TEMPLATE_INCLUDEPATH)) : (include template('common', TEMPLATE_INCLUDEPATH));?>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('public/footer', TEMPLATE_INCLUDEPATH)) : (include template('public/footer', TEMPLATE_INCLUDEPATH));?>