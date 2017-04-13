<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('public/header', TEMPLATE_INCLUDEPATH)) : (include template('public/header', TEMPLATE_INCLUDEPATH));?>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('store/nav', TEMPLATE_INCLUDEPATH)) : (include template('store/nav', TEMPLATE_INCLUDEPATH));?>
<?php  if($op == 'post') { ?>
<form class="form-horizontal form" id="form1" action="" method="post" enctype="multipart/form-data">
	<input type="hidden" name="sid" value="<?php  echo $sid;?>">
	<div class="main">
		<div class="panel panel-default">
			<div class="panel-heading">添加商品分类</div>
			<div class="panel-body">
				<div id="tpl">
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="require">* </span>分类名称</label>
						<div class="col-sm-9 col-xs-12">
							<input type="text" class="form-control" name="title[]" value="">
						</div>
					</div>
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="require">* </span>分类内最低消费金额</label>
						<div class="col-sm-9 col-xs-12">
							<div class="input-group">
								<input type="text" class="form-control" name="min_fee[]" value="0">
								<div class="input-group-addon">元</div>
							</div>
							<div class="help-block">
								限制在该分类内， 购买的商品不能少于多少元。适用场景：快餐分类，这个分类内的商品，下单金额必须满足元才能下单。该设置仅对外卖有效。消费金额不包括餐盒费
							</div>
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
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">分类排序</label>
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
<script type="text/javascript">
	require(['util'], function(u){
		$('#post-add').click(function(){
			$('#tpl-container').append($('#tpl').html());
		});
	});
</script>
<?php  } else if($op == 'list') { ?>
<div class="main">
	<div class="panel panel-default">
		<div class="panel-body">
			<a class="btn btn-primary" href="<?php  echo $this->createWebUrl('category', array('op' => 'post'));?>"/><i class="fa fa-plus-circle"> </i> 添加商品分类</a>
			<a class="btn btn-success" href="javascript:;" onclick="$('.file-container').slideToggle()">批量导入分类</a>
		</div>
	</div>
	<div class="panel panel-default file-container">
		<div class="panel-body">
			<form action="<?php  echo $this->createWebUrl('category', array('op' => 'export'));?>" method="post" class="form-inline form-file" enctype="multipart/form-data">
				<div class="form-group">
					<input type="file" name="file" value="">
				</div>
				<input type="submit" name="submit" value="导入" class="btn btn-primary"/>
				<input type="hidden" name="token" value="<?php  echo $_W['token'];?>"/>
				<a class="btn btn-primary" href="<?php  echo $_W['siteroot'];?>/addons/we7_wmall_plus/resource/excel/goods_category.xls">下载导入模板</a>
			</form>
		</div>
	</div>
	<form class="form-horizontal" action="" method="post">
		<div class="panel panel-default">
			<div class="panel-body table-responsive">
				<table class="table table-hover">
					<thead class="navbar-inner">
						<tr>
							<th>分类名称</th>
							<th>最低消费金额（元）</th>
							<th>排序</th>
							<th>商品数</th>
							<th>是否显示</th>
							<th style="width:150px; text-align:right;">操作</th>
						</tr>
					</thead>
					<tbody>
						<?php  if(is_array($lists)) { foreach($lists as $item) { ?>
						<tr>
							<input type="hidden" name="ids[]" value="<?php  echo $item['id'];?>">
							<td><input type="text" style="width:130px" name="title[]" class="form-control" value="<?php  echo $item['title'];?>"></td>
							<td><input type="text" style="width:100px" name="min_fee[]" class="form-control" value="<?php  echo $item['min_fee'];?>"></td>
							<td><input type="text" style="width:100px" name="displayorder[]" class="form-control" value="<?php  echo $item['displayorder'];?>"></td>
							<td><?php  echo intval($nums[$item['id']]['num'])?></td>
							<td>
								<input type="checkbox" value="<?php  echo $item['status'];?>" name="status[]" data-id="<?php  echo $item['id'];?>" <?php  if($item['status'] == 1) { ?>checked<?php  } ?>>
							</td>
							<td style="text-align:right;">
								<a href="<?php  echo $this->createWebUrl('goods', array('op' => 'list', 'cid' => $item['id']))?>" class="btn btn-default btn-sm" title="查看商品" data-toggle="tooltip" data-placement="top"><i class="fa fa-eye"> </i></a>
								<a href="<?php  echo $this->createWebUrl('goods', array('op' => 'post', 'cid' => $item['id']))?>" class="btn btn-default btn-sm" title="添加商品" data-toggle="tooltip" data-placement="top" ><i class="fa fa-plus"> </i></a>
								<a href="<?php  echo $this->createWebUrl('category', array('op' => 'del', 'id' => $item['id']))?>" class="btn btn-default btn-sm" title="删除" data-toggle="tooltip" data-placement="top" onclick="if(!confirm('删除后该分类下的商品也会删除，确定删除吗?')) return false;"><i class="fa fa-times"> </i></a>
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
		<?php  echo $pager;?>
	</form>
</div>
<script type="text/javascript">
require(['bootstrap.switch'], function($){
	$('.form-file').submit(function(){
		if(!$(':file[name="file"]').val()) {
			util.message('请先上传要导入的文件', '', 'error');
			return false;
		}
	});

	$(':checkbox[name="status[]"]').bootstrapSwitch();
	$(':checkbox[name="status[]"]').on('switchChange.bootstrapSwitch', function(e, state){
		var status = this.checked ? 1 : 0;
		var id = $(this).data('id');
		$.post("<?php  echo $this->createWebUrl('category', array('op' => 'status'))?>", {status: status, id: id}, function(resp){
			setTimeout(function(){
				location.reload();
			}, 500)
		});
	});
});
</script>
<?php  } ?>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('public/footer', TEMPLATE_INCLUDEPATH)) : (include template('public/footer', TEMPLATE_INCLUDEPATH));?>