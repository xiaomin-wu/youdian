<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('public/header', TEMPLATE_INCLUDEPATH)) : (include template('public/header', TEMPLATE_INCLUDEPATH));?>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('plateform/config-nav', TEMPLATE_INCLUDEPATH)) : (include template('plateform/config-nav', TEMPLATE_INCLUDEPATH));?>
<div class="panel panel-default">
	<div class="panel-body">
		<ul class="nav nav-pills">
			<li <?php  if($op == 'set') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('ptfconfig-errander', array('op' => 'set'));?>"> 跑腿设置</a></li>
			<li <?php  if($op == 'category_list') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('ptfconfig-errander', array('op' => 'category_list'));?>"> 跑腿类型设置</a></li>
			<li <?php  if($_GPC['op'] == 'category_post') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('ptfconfig-errander', array('op' => 'category_post'));?>"><?php  if($id > 0) { ?>编辑<?php  } else { ?>添加<?php  } ?>跑腿类型</a></li>
		</ul>
	</div>
</div>
<?php  if($op == 'set') { ?>
<form class="form-horizontal form" id="form1" action="" method="post" enctype="multipart/form-data">
	<div class="main">
		<div class="panel panel-default">
			<div class="panel-heading">跑腿设置</div>
			<div class="panel-body">
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="require">* </span>跑腿中心点</label>
					<div class="col-sm-9 col-xs-12">
						<?php  echo tpl_form_field_tiny_coordinate('map', $config['errander']['map']);?>
						<div class="help-block">设置跑腿服务中心点</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="require">* </span>跑腿服务半径</label>
					<div class="col-sm-9 col-xs-12">
						<div class="input-group">
							<input type="text" class="form-control" name="serve_radius" value="<?php  echo $config['errander']['serve_radius'];?>">
							<span class="input-group-addon">KM</span>
						</div>
						<div class="help-block">设置服务半径</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="require">* </span>跑腿服务城市(省/市)</label>
					<div class="col-sm-9 col-xs-12">
						<input type="text" class="form-control" name="city" value="<?php  echo $config['errander']['city'];?>">
						<div class="help-block">填写跑腿服务所属的"市"或"省". 比如:你在县城里做跑腿, 需要填写该县城所属的"市"或"省".</div>
						<div class="help-block">该项的作用是:用户在搜索地址的时候, 只返回该"省"或"市"内的相关地址</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="require">* </span>支付时间限制</label>
					<div class="col-sm-9 col-xs-12">
						<div class="input-group">
							<input type="text" class="form-control" name="pay_time_limit" value="<?php  echo $config['errander']['pay_time_limit'];?>">
							<span class="input-group-addon">分钟</span>
						</div>
						<div class="help-block">例如:设置为15分钟,那么用户在提交订单后15分钟内未支付,系统会自动取消该订单.如果没有支付时间限制,请设置为0</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="require">* </span>接单时间限制</label>
					<div class="col-sm-9 col-xs-12">
						<div class="input-group">
							<input type="text" class="form-control" name="handle_time_limit" value="<?php  echo $config['errander']['handle_time_limit'];?>">
							<span class="input-group-addon">分钟</span>
						</div>
						<div class="help-block">例如:设置为10分钟,那么用户在付款10分钟后仍然没有配送员接单,系统会自动取消该订单.如果没有接单时间限制,请设置为0</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="require">* </span>跑腿单派单模式</label>
					<div class="col-sm-9 col-xs-12">
						<label class="radio-inline">
							<input type="radio" value="1" name="dispatch_mode" <?php  if($config['errander']['dispatch_mode'] == '1') { ?>checked<?php  } ?>> 抢单模式
						</label>
						<label class="radio-inline">
							<input type="radio" value="2" name="dispatch_mode" <?php  if($config['errander']['dispatch_mode'] == '2') { ?>checked<?php  } ?>> 管理员派单
						</label>
						<label class="radio-inline">
							<input type="radio" value="3" name="dispatch_mode" <?php  if($config['errander']['dispatch_mode'] == '3') { ?>checked<?php  } ?>> 系统分配
						</label>
						<div class="help-block">
							<strong class="text-danger">
								系统分配算法需要配送员使用app接单,如果你没有授权配送员app,请不要选择该模式。<br>
								系统分配算法：当跑腿订单有购买地址时， 系统把订单分配给离购买地址最近的配送员。当跑腿订单没有购买地址时，系统把订单分配给离收货地址最近的配送员
							</strong>
						</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="require">* </span>配送员同时最多可抢</label>
					<div class="col-sm-9 col-xs-12">
						<div class="input-group">
							<input type="text" class="form-control" name="deliveryer_collect_max" value="<?php  echo $config['errander']['deliveryer_collect_max'];?>">
							<span class="input-group-addon">单</span>
						</div>
						<div class="help-block">
							设置配送员同一时间最多可抢几单,超出后将不能在抢。0为不限制
							<br>
							<strong class="text-danger">注意：此设置仅对配送员主动抢单有效。平台配送员分配订单和自动分配不受此设置限制</strong>
						</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="require">* </span>超出最多可抢单后,是否还通知配送员</label>
					<div class="col-sm-9 col-xs-12">
						<label class="radio-inline">
							<input type="radio" value="1" name="over_collect_max_notify" <?php  if($config['errander']['over_collect_max_notify'] == 1) { ?>checked<?php  } ?>> 通知
						</label>
						<label class="radio-inline">
							<input type="radio" value="0" name="over_collect_max_notify" <?php  if(!$config['errander']['over_collect_max_notify']) { ?>checked<?php  } ?>> 不通知
						</label>
						<span class="help-block">设置当配送员已抢单数超过最多可抢单数后,是否继续通知配送员有新的待配送订单</span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="require">* </span>平台给配送员每单支付金额(跑腿单)</label>
					<div class="col-sm-9 col-xs-12">
						<div class="input-group">
							<label class="input-group-addon">
								<input type="radio" name="deliveryer_fee_type" value="1" <?php  if($config['errander']['deliveryer_fee_type'] == 1 || !$config['errander']['deliveryer_fee_type']) { ?>checked<?php  } ?>>
							</label>
							<span class="input-group-addon">每单固定</span>
							<input type="text" class="form-control" name="deliveryer_fee_1" <?php  if($config['errander']['deliveryer_fee_type'] == 1) { ?>value="<?php  echo $config['errander']['deliveryer_fee'];?>"<?php  } ?>>
							<span class="input-group-addon">元</span>
						</div>
						<br>
						<div class="input-group">
							<label class="input-group-addon">
								<input type="radio" name="deliveryer_fee_type" value="2" <?php  if($config['errander']['deliveryer_fee_type'] == 2) { ?>checked<?php  } ?>>
							</label>
							<span class="input-group-addon">每单按照订单配送费提成</span>
							<input type="text" class="form-control" name="deliveryer_fee_2" <?php  if($config['errander']['deliveryer_fee_type'] == 2) { ?>value="<?php  echo $config['errander']['deliveryer_fee'];?>"<?php  } ?>>
							<span class="input-group-addon">%</span>
						</div>
						<div class="help-block text-danger"></div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="require"> </span>匿名自定义</label>
					<div class="col-sm-6 col-xs-6">
						<?php  if(!empty($config['errander']['anonymous'])) { ?>
							<?php  if(is_array($config['errander']['anonymous'])) { foreach($config['errander']['anonymous'] as $anonymous) { ?>
								<div class="btn-group btn-label">
									<input type="hidden" name="anonymous[]" value="<?php  echo $anonymous;?>">
									<a class="btn btn-default border-radius-4"><?php  echo $anonymous;?></a>
									<a class="btn btn-default">
										<span class="fa fa-times-circle label-delete"></span>
									</a>
								</div>
							<?php  } } ?>
						<?php  } ?>
						<a class="btn btn-success label-add"><i class="fa fa-plus-circle"></i> 添加</a>
						<div class="help-block">例如: 范冰冰，李冰冰，章子怡等</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="require">* </span>跑腿服务用户协议</label>
					<div class="col-sm-9 col-xs-12">
						<?php  echo itpl_ueditor('agreement', $agreement_errander);?>
					</div>
				</div>
			</div>
		</div>
		<div class="form-group">
			<div class="col-sm-9 col-xs-9 col-md-9">
				<input type="hidden" name="token" value="<?php  echo $_W['token'];?>">
				<input name="submit" id="submit" type="submit" value="提交" class="btn btn-primary col-lg-1">
			</div>
		</div>
	</div>
</form>
<script type="text/javascript">
$('#form1').submit(function(){
	if(!$(':text[name="map[lng]"]').val()) {
		util.message('请设置跑腿服务中心点', '', 'error');
		return false;
	}
	if(!$(':text[name="serve_radius"]').val()) {
		util.message('请设置服务半径', '', 'error');
		return false;
	}
	return true;
});
$(function(){
	$(document).on('click', '.label-add', function(){
		var $this = $(this);
		tiny.prompt($(this), '', function(data) {
			if(!data) {
				return false;
			}
			var html = '<div class="btn-group btn-label">'+
					'		<input type="hidden" name="anonymous[]" value="'+ data +'">'+
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
<?php  } ?>

<?php  if($op == 'category_post') { ?>
<form class="form-horizontal form" id="form1" action="" method="post" enctype="multipart/form-data">
	<div class="main">
		<div class="panel panel-default">
			<div class="panel-heading">跑腿类型</div>
			<div class="panel-body">
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="require">* </span>跑腿类型</label>
					<div class="col-sm-6 col-xs-6">
						<label class="radio-inline"><input type="radio" name="type" value="buy" <?php  if($item['type'] == 'buy' || !$item['type']) { ?>checked<?php  } ?>> 随意购</label>
						<label class="radio-inline"><input type="radio" name="type" value="delivery" <?php  if($item['type'] == 'delivery') { ?>checked<?php  } ?>> 快速送</label>
						<label class="radio-inline"><input type="radio" name="type" value="pickup" <?php  if($item['type'] == 'pickup') { ?>checked<?php  } ?>> 快速取</label>
						<div class="help-block">随意购: 帮顾客购买商品. 例如: 香烟,咖啡,早餐等.</div>
						<div class="help-block">快速送: 帮顾客送货.</div>
						<div class="help-block">快速取: 帮顾客取货.</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="require">* </span>标题</label>
					<div class="col-sm-6 col-xs-6">
						<input type="text" class="form-control" name="title" value="<?php  echo $item['title'];?>">
						<div class="help-block">例如: 买香烟</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="require">* </span>图标</label>
					<div class="col-sm-6 col-xs-6">
						<?php  echo tpl_form_field_image('thumb', $item['thumb']);?>
						<div class="help-block">设置分类图标</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="require"> </span>商品类型</label>
					<div class="col-sm-6 col-xs-6">
						<?php  if(!empty($item['label'])) { ?>
							<?php  if(is_array($item['label'])) { foreach($item['label'] as $label) { ?>
								<div class="btn-group btn-label">
									<input type="hidden" name="label[]" value="<?php  echo $label;?>">
									<a class="btn btn-default"><?php  echo $label;?></a>
									<a class="btn btn-default">
										<span class="fa fa-times-circle label-delete"></span>
									</a>
								</div>
							<?php  } } ?>
						<?php  } ?>
						<a class="btn btn-success label-add"><i class="fa fa-plus-circle"></i> 添加</a>
						<div class="help-block">例如: 设置的标题是买香烟, 商品标签可设置为: 中南海, 红塔山, 中华, 玉溪等</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="require">* </span>配送费设置</label>
					<div class="col-sm-6 col-xs-6">
						<div class="input-group">
							<span class="input-group-addon">起步价</span>
							<input type="text" class="form-control" name="start_fee" value="<?php  echo $item['start_fee'];?>">
							<span class="input-group-addon">元包含</span>
							<input type="text" class="form-control" name="start_km" value="<?php  echo $item['start_km'];?>">
							<span class="input-group-addon">公里</span>
						</div>
						<br>
						<div class="input-group">
							<span class="input-group-addon">每增加1公里加</span>
							<input type="text" class="form-control" name="pre_km_fee" value="<?php  echo $item['pre_km_fee'];?>">
							<span class="input-group-addon">元</span>
						</div>
						<div class="help-block"></div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="require">* </span>小费设置</label>
					<div class="col-sm-6 col-xs-6">
						<div class="input-group">
							<span class="input-group-addon">最低</span>
							<input type="text" class="form-control" name="tip_min" value="<?php  echo $item['tip_min'];?>">
							<span class="input-group-addon">最高</span>
							<input type="text" class="form-control" name="tip_max" value="<?php  echo $item['tip_max'];?>">
							<span class="input-group-addon">元</span>
						</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="require">* </span>排序</label>
					<div class="col-sm-6 col-xs-6">
						<input type="text" class="form-control" name="displayorder" value="<?php  echo $item['displayorder'];?>">
						<div class="help-block">数字越大越靠前</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="require">* </span>跑腿规则</label>
					<div class="col-sm-6 col-xs-6">
						<?php  echo itpl_ueditor('rule', $item['rule']);?>
					</div>
				</div>
			</div>
		</div>
		<div class="form-group">
			<div class="col-sm-9 col-xs-9 col-md-9">
				<input type="hidden" name="token" value="<?php  echo $_W['token'];?>">
				<input name="submit" id="submit" type="submit" value="提交" class="btn btn-primary col-lg-1">
			</div>
		</div>
	</div>
</form>
<script type="text/javascript">
$(function(){
	$(document).on('click', '.label-add', function(){
		var $this = $(this);
		tiny.prompt($(this), '', function(data) {
			if(!data) {
				return false;
			}
			var html = '<div class="btn-group btn-label">'+
					'		<input type="hidden" name="label[]" value="'+ data +'">'+
					'		<a class="btn btn-default">'+data+'</a>'+
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

	$('#form1').submit(function(){
		var title = $.trim($(':text[name="title"]').val());
		if(!title) {
			util.message('名称不能为空', '', 'error');
			return false;
		}
		var thumb = $.trim($(':text[name="thumb"]').val());
		if(!thumb) {
			util.message('图标不能为空', '', 'error');
			return false;
		}
		var start_fee = $.trim($(':text[name="start_fee"]').val());
		if(!start_fee) {
			util.message('请设置起步价', '', 'error');
			return false;
		}
		var start_km = $.trim($(':text[name="start_km"]').val());
		if(!start_km) {
			util.message('请设置起步价包含的公里', '', 'error');
			return false;
		}
		return true;
	});
});
</script>
<?php  } ?>

<?php  if($op == 'category_list') { ?>
<div class="main">
	<form class="form-horizontal" action="" method="post">
		<div class="panel panel-default">
			<div class="panel-body table-responsive">
				<table class="table table-hover">
					<thead class="navbar-inner">
					<tr>
						<th width="70">图标</th>
						<th>分类名称</th>
						<th>排序</th>
						<th>收费标准</th>
						<th>小费设置</th>
						<th>状态</th>
						<th style="width:150px; text-align:right;">操作</th>
					</tr>
					</thead>
					<tbody>
					<?php  if(is_array($categorys)) { foreach($categorys as $item) { ?>
					<tr>
						<input type="hidden" name="ids[]" value="<?php  echo $item['id'];?>">
						<td><img src="<?php  echo tomedia($item['thumb']);?>" width="50"></td>
						<td><input type="text" style="width:130px" name="title[]" class="form-control" value="<?php  echo $item['title'];?>"></td>
						<td><input type="text" style="width:100px" name="displayorder[]" class="form-control" value="<?php  echo $item['displayorder'];?>"></td>
						<td>
							<span class="label label-info">起步价<?php  echo $item['start_fee'];?>元包含<?php  echo $item['start_km'];?>公里</span>
							<br>
							<span class="label label-default label-br">每超过1公里增加<?php  echo $item['pre_km_fee'];?>元</span>
						</td>
						<td>
							<span class="label label-danger">最低<?php  echo $item['tip_min'];?>元, 最高<?php  echo $item['tip_max'];?>元</span>
						</td>
						<td>
							<input type="checkbox" value="<?php  echo $item['status'];?>" name="status[]" data-id="<?php  echo $item['id'];?>" <?php  if($item['status'] == 1) { ?>checked<?php  } ?>>
						</td>
						<td style="text-align:right;">
							<a href="<?php  echo $this->createWebUrl('ptfconfig-errander', array('op' => 'category_post', 'id' => $item['id']))?>" class="btn btn-default btn-sm" title="编辑" data-toggle="tooltip" data-placement="top" ><i class="fa fa-edit"> </i></a>
							<a href="<?php  echo $this->createWebUrl('ptfconfig-errander', array('op' => 'category_del', 'id' => $item['id']))?>" class="btn btn-default btn-sm" title="删除" data-toggle="tooltip" data-placement="top" onclick="if(!confirm('确定删除吗?')) return false;"><i class="fa fa-times"> </i></a>
						</td>
					</tr>
					<?php  } } ?>
					</tbody>
				</table>
			</div>
		</div>
		<div class="form-group">
			<div class="col-sm-12">
				<input name="token" type="hidden" value="<?php  echo $_W['token'];?>" />
				<input type="submit" class="btn btn-primary col-lg-1" name="submit" value="提交" />
			</div>
		</div>
	</form>
</div>
<script>
	require(['bootstrap.switch'], function($){
		$(':checkbox[name="status[]"]').bootstrapSwitch();
		$(':checkbox[name="status[]"]').on('switchChange.bootstrapSwitch', function(e, state){
			var status = this.checked ? 1 : 0;
			var id = $(this).data('id');
			$.post("<?php  echo $this->createWebUrl('ptfconfig-errander', array('op' => 'category_status'))?>", {status: status, id: id}, function(resp){
				setTimeout(function(){
					location.reload();
				}, 500)
			});
		});
	});
</script>
<?php  } ?>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('public/footer', TEMPLATE_INCLUDEPATH)) : (include template('public/footer', TEMPLATE_INCLUDEPATH));?>