<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('public/header', TEMPLATE_INCLUDEPATH)) : (include template('public/header', TEMPLATE_INCLUDEPATH));?>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('store/nav', TEMPLATE_INCLUDEPATH)) : (include template('store/nav', TEMPLATE_INCLUDEPATH));?>
<div class="panel panel-default">
	<div class="panel-body">
		<ul class="nav nav-pills">
			<li <?php  if($_GPC['op'] == 'list' || !$_GPC['op']) { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('printer', array('op' => 'list'));?>">打印机列表</a></li>
			<li <?php  if($_GPC['op'] == 'post') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('printer', array('op' => 'post'));?>"><?php  if($id > 0) { ?>编辑<?php  } else { ?>添加<?php  } ?>打印机</a></li>
			<li <?php  if($_GPC['op'] == 'label_list') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('printer', array('op' => 'label_list'));?>">打印标签</a></li>
			<li <?php  if($_GPC['op'] == 'label_post') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('printer', array('op' => 'label_post'));?>">添加打印标签</a></li>
		</ul>
	</div>
</div>
<?php  if($op == 'post') { ?>
<form class="form-horizontal form" id="form1" action="" method="post" enctype="multipart/form-data">
	<div class="main">
		<div class="panel panel-default">
			<div class="panel-heading">添加打印机</div>
			<div class="panel-body">
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="require"> </span>是否启用打印机</label>
					<div class="col-sm-9 col-xs-12">
						<label class="radio-inline">
							<input type="radio" value="1" name="status" <?php  if($item['status'] == 1) { ?>checked<?php  } ?>> 启用
						</label>
						<label class="radio-inline">
							<input type="radio" value="0" name="status" <?php  if($item['status'] == 0) { ?>checked<?php  } ?>> 不启用
						</label>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="require">* </span>打印机名称</label>
					<div class="col-sm-9 col-xs-12">
						<input type="text" class="form-control" name="name" value="<?php  echo $item['name'];?>" placeholder="填写打印机名称">
						<div class="help-block">方便区分打印机</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="require"> </span>打印机类型</label>
					<div class="col-sm-9 col-xs-12">
						<label class="radio-inline">
							<input type="radio" value="feie" class="printer-type" name="type" <?php  if($item['type'] == 'feie') { ?>checked<?php  } ?>> 飞鹅定制打印机
							<span class="label label-success">推荐</span>
						</label>
						<label class="radio-inline">
							<input type="radio" value="yilianyun" class="printer-type" name="type" <?php  if($item['type'] == 'yilianyun') { ?>checked<?php  } ?>> 易联云定制打印机(不推荐)
						</label>
						<label class="radio-inline">
							<input type="radio" value="365" class="printer-type" name="type" <?php  if($item['type'] == '365') { ?>checked<?php  } ?>> 365定制打印机(不推荐)
						</label>
						<label class="radio-inline">
							<input type="radio" value="feiyin" class="printer-type" name="type" <?php  if($item['type'] == 'feiyin') { ?>checked<?php  } ?>> 飞印打印机(不推荐,后期将停止更新)
						</label>
						<label class="radio-inline">
							<input type="radio" value="AiPrint" class="printer-type" name="type" <?php  if($item['type'] == 'AiPrint') { ?>checked<?php  } ?>> AiPrint打印机(不推荐,后期将停止更新)
						</label>
						<div class="help-block"><span class="text-danger">平台所有打印机都属于定制打印机，如需购买打印机请联系平台管理员（价格低，质量好），自行购买可能会有不兼容等问题, 因自行购买打印机造成的损失客户自己承担。</strong></span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="require">* </span>机器号</label>
					<div class="col-sm-9 col-xs-12">
						<input type="text" class="form-control" name="print_no" value="<?php  echo $item['print_no'];?>" placeholder="填写机器号">
						<div class="help-block">打印机底部标签信息中获取</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">打印机key</label>
					<div class="col-sm-9 col-xs-12">
						<input type="text" class="form-control" name="key" value="<?php  echo $item['key'];?>" placeholder="填写打印机key">
						<div class="help-block">
							如果你的打印机是飞鹅打印机, 需要到<a href="http://www.feieyun.com/login.jsp" target="_blank">"飞鹅云官网"</a>注册账号并添加打印机获取
							<br>
							如果你的打印机是易联云打印机, 可在打印机底部标签信息中获取
						</div>
					</div>
				</div>
				<div class="form-group <?php  if($item['type'] != 'feiyin' && $item['type'] != 'AiPrint') { ?>hide<?php  } ?> text-feiyin">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">商户编号</label>
					<div class="col-sm-9 col-xs-12">
						<input type="text" class="form-control" name="member_code" value="<?php  echo $item['member_code'];?>" placeholder="填写商户编号">
						<div class="help-block">
							如果你的打印机是飞印打印机, 需要到<a href="http://my.feyin.net" target="_blank">"飞印中心"</a>注册账号并添加打印机获取
						</div>
					</div>
				</div>
				<div class="<?php  if($item['type'] != 'yilianyun') { ?>hide<?php  } ?> text-yilianyun">
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">用户ID</label>
						<div class="col-sm-9 col-xs-12">
							<input type="text" class="form-control" name="userid" value="<?php  echo $item['member_code'];?>" placeholder="填写用户id">
							<div class="help-block">请到<a href="http://yilianyun.10ss.net/" target="_blank">"易联云"</a>管理中心系统集成里默取</div>
						</div>
					</div>
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">apikey</label>
						<div class="col-sm-9 col-xs-12">
							<input type="text" class="form-control" name="api_key" value="<?php  echo $item['api_key'];?>" placeholder="apikey">
							<div class="help-block">请到<a href="http://yilianyun.10ss.net/" target="_blank">"易联云"</a>管理中心系统集成里默取</div>
						</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">打印联数</label>
					<div class="col-sm-9 col-xs-12">
						<input type="text" class="form-control" name="print_nums" value="<?php  echo $item['print_nums'];?>">
						<div class="help-block">默认为1</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">打印指定标签</label>
					<div class="col-sm-9 col-xs-12">
						<div class="radio">
							<label><input type="radio" name="print_label_type" value="0" <?php  if(in_array(0, $item['print_label'])) { ?>checked<?php  } ?> onclick="$('.print_label-containter').addClass('hide')"> 打印所有的商品 &nbsp;&nbsp;</label>
							<label><input type="radio" name="print_label_type" value="1" <?php  if(!in_array(0, $item['print_label'])) { ?>checked<?php  } ?> onclick="$('.print_label-containter').removeClass('hide')"> 打印指定标签 &nbsp;&nbsp;</label>
						</div>
						<div class="checkbox print_label-containter <?php  if(in_array(0, $item['print_label'])) { ?>hide<?php  } ?>" >
							<?php  if(is_array($print_labels)) { foreach($print_labels as $label) { ?>
								<label><input type="checkbox" name="print_label[]" value="<?php  echo $label['id'];?>" <?php  if(in_array($label['id'], $item['print_label'])) { ?>checked<?php  } ?>> <?php  echo $label['title'];?> &nbsp;&nbsp;</label>
							<?php  } } ?>
						</div>
						<div class="help-block">当设置了打印指定标签，该打印机只打印包含【指定标签内的商品(ps: 添加商品的时候，可设置商品的打印标签)】的订单</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">打印类型</label>
					<div class="col-sm-9 col-xs-12">
						<div class="radio">
							<label><input type="radio" name="is_print_all" value="1" <?php  if($item['is_print_all'] == 1) { ?>checked<?php  } ?>> 整单打印 &nbsp;&nbsp;</label>
							<label><input type="radio" name="is_print_all" value="0" <?php  if(!$item['is_print_all']) { ?>checked<?php  } ?>> 分单打印 &nbsp;&nbsp;</label>
						</div>
						<div class="help-block">
							整单打印为： 打印订单的全部商品条目信息。 分单打印为： 订单里的全部商品每个打印一次
						</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">头部自定义信息</label>
					<div class="col-sm-9 col-xs-12">
						<input type="text" class="form-control" name="print_header" value="<?php  echo $item['print_header'];?>">
						<div class="help-block">建议少于20个字</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">尾部自定义信息</label>
					<div class="col-sm-9 col-xs-12">
						<input type="text" class="form-control" name="print_footer" value="<?php  echo $item['print_footer'];?>">
						<div class="help-block">建议少于20个字</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="require">*</span>二维码类型</label>
					<div class="col-sm-9 col-xs-12">
						<div class="btn-group" data-toggle="buttons">
							<label class="btn btn-default <?php  if($item['qrcode_type'] == 'delivery_assign') { ?>active<?php  } ?>" onclick="$('.qrcode-custom').hide();">
								<input type="radio" name="qrcode_type" value="delivery_assign" <?php  if($item['qrcode_type'] == 'delivery_assign') { ?>checked<?php  } ?>> 配送员接单二维码
							</label>
							<label class="btn btn-default <?php  if($item['qrcode_type'] == 'custom' || !$item['qrcode_type']) { ?>active<?php  } ?>" onclick="$('.qrcode-custom').show();">
								<input type="radio" name="qrcode_type" value="custom" <?php  if($item['qrcode_type'] == 'custom' || !$item['qrcode_type']) { ?>checked<?php  } ?>> 自定义二维码链接
							</label>
						</div>
						<div class="help-block" style="margin-bottom: -10px">
							配送员接单二维码: 打印机自动打印该订单的接单二维码,配送员可直接扫码该二维码接单。
						</div>
					</div>
				</div>
				<div class="form-group qrcode-custom" <?php  if($item['qrcode_type'] != 'custom' && $item['qrcode_type'] != '') { ?>style="display:none"<?php  } ?>>
					<label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
					<div class="col-sm-9 col-xs-12">
						<input type="text" class="form-control" name="qrcode_link" value="<?php  echo $item['qrcode_link'];?>">
						<div class="help-block text-danger">该店铺手机端地址为:<a target="_blank" href="<?php  echo $_W['siteroot'];?>app<?php  echo ltrim($this->createMobileUrl('goods', array('sid' => $sid), true), '.');?>"><?php  echo $_W['siteroot'];?>app<?php  echo ltrim($this->createMobileUrl('goods', array('sid' => $sid), true), '.');?></a> 您可以用该地址转为短链接作为二维码的链接地址。</div>
					</div>
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
<?php  } else if($op == 'list') { ?>
<div class="clearfix">
	<form class="form-horizontal" action="" method="post">
		<div class="panel panel-default">
			<div class="panel-body table-responsive">
				<table class="table table-hover">
					<thead class="navbar-inner">
						<tr>
							<th>打印机品牌</th>
							<th>打印机名称</th>
							<th>打印联数</th>
							<th>打印机状态</th>
							<th>启用?</th>
							<th style="width:150px; text-align:right;">状态/修改/删除</th>
						</tr>
					</thead>
					<tbody>
						<?php  if(is_array($data)) { foreach($data as $item) { ?>
						<tr>
							<td>
								<span class="<?php  echo $types[$item['type']]['css'];?>"><?php  echo $types[$item['type']]['text'];?></span>
							</td>
							<td><?php  echo $item['name'];?></td>
							<td><?php  echo $item['print_nums'];?></td>
							<td>
								<span class="label label-info"><?php  echo $item['status_cn'];?></span>
							</td>
							<td>
								<?php  if($item['status'] == 1) { ?>
									<span class="label label-success">启用</span>
								<?php  } else { ?>
									<span class="label label-danger">停用</span>
								<?php  } ?>
							</td>
							<td style="text-align:right;">
								<a href="<?php  echo $this->createWebUrl('printer', array('op' => 'post', 'id' => $item['id']))?>" class="btn btn-default btn-sm" title="编辑" data-toggle="tooltip" data-placement="top" ><i class="fa fa-edit"> </i></a>
								<a href="<?php  echo $this->createWebUrl('printer', array('op' => 'del', 'id' => $item['id']))?>" class="btn btn-default btn-sm" title="删除" data-toggle="tooltip" data-placement="top" onclick="if(!confirm('删除后将不可恢复，确定删除吗?')) return false;"><i class="fa fa-times"> </i></a>
							</td>
						</tr>
						<?php  } } ?>
					</tbody>
				</table>
			</div>
		</div>
	</form>
</div>
<?php  } else if($op == 'label_post') { ?>
<div class="clearfix">
	<form class="form-horizontal form" id="form1" action="" method="post" enctype="multipart/form-data">
		<div class="main">
			<div class="panel panel-default">
				<div class="panel-heading">添加打印标签</div>
				<div class="panel-body">
					<div id="tpl">
						<div class="form-group">
							<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="require">* </span>标签名称</label>
							<div class="col-sm-9 col-xs-12">
								<input type="text" class="form-control" name="title[]" value="">
							</div>
						</div>
						<div class="form-group">
							<label class="col-xs-12 col-sm-3 col-md-2 control-label">分类排序</label>
							<div class="col-sm-9 col-xs-12">
								<input type="text" class="form-control" name="displayorder[]" value="">
							</div>
						</div>
						<hr>
					</div>
					<div id="tpl-container"></div>
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
						<div class="col-sm-9 col-xs-12" style="padding-top:7px">
							<a href="javascipt:;" id="post-add"><i class="fa fa-plus-circle"></i> 继续添加</a>
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
</div>
<?php  } else if($op == 'label_list') { ?>
<div class="clearfix">
	<form class="form-horizontal" action="" method="post">
		<div class="panel panel-default">
			<div class="panel-body table-responsive">
				<table class="table table-hover">
					<thead class="navbar-inner">
					<tr>
						<th>标签名称</th>
						<th>排序</th>
						<th style="width:150px; text-align:right;">操作</th>
					</tr>
					</thead>
					<tbody>
					<?php  if(is_array($lists)) { foreach($lists as $item) { ?>
						<tr>
							<input type="hidden" name="ids[]" value="<?php  echo $item['id'];?>">
							<td><input type="text" style="width:130px" name="title[]" class="form-control" value="<?php  echo $item['title'];?>"></td>
							<td><input type="text" style="width:100px" name="displayorder[]" class="form-control" value="<?php  echo $item['displayorder'];?>"></td>
							<td style="text-align:right;">
								<a href="<?php  echo $this->createWebUrl('printer', array('op' => 'label_del', 'id' => $item['id']))?>" class="btn btn-default btn-sm" title="删除" data-toggle="tooltip" data-placement="top" onclick="if(!confirm('确定删除吗?')) return false;"><i class="fa fa-times"> </i></a>
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
<?php  } ?>
<script>
$(function(){
	$('.printer-type').click(function(){
		if($(this).val() == 'yilianyun') {
			$('.text-feiyin').addClass('hide');
			$('.text-yilianyun').removeClass('hide');
		} else if($(this).val() == 'feiyin' || $(this).val() == 'AiPrint') {
			$('.text-yilianyun').addClass('hide');
			$('.text-feiyin').removeClass('hide');
		} else {
			$('.text-feiyin').addClass('hide');
			$('.text-yilianyun').addClass('hide');
		}
	});

	$('#post-add').click(function(){
		$('#tpl-container').append($('#tpl').html());
	});
});
</script>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('public/footer', TEMPLATE_INCLUDEPATH)) : (include template('public/footer', TEMPLATE_INCLUDEPATH));?>