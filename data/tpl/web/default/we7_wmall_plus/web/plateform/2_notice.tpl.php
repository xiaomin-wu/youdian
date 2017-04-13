<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('public/header', TEMPLATE_INCLUDEPATH)) : (include template('public/header', TEMPLATE_INCLUDEPATH));?>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('plateform/mall-nav', TEMPLATE_INCLUDEPATH)) : (include template('plateform/mall-nav', TEMPLATE_INCLUDEPATH));?>
<div class="panel panel-default">
	<div class="panel-body">
		<ul class="nav nav-pills">
			<li <?php  if($op == 'list') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('ptfnotice');?>"> 公告列表</a></li>
			<li <?php  if($op == 'post' && !$id) { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('ptfnotice', array('op' => 'post'));?>">添加公告</a></li>
			<?php  if($id > 0) { ?><li class="active"><a href="<?php  echo $this->createWebUrl('ptfnotice', array('op' => 'post', 'id' => $id));?>">编辑公告</a></li><?php  } ?>
		</ul>
	</div>
</div>
<?php  if($op == 'post') { ?>
<form class="form-horizontal form" id="form1" action="" method="post" enctype="multipart/form-data">
	<div class="main">
		<div class="panel panel-default">
			<div class="panel-heading">添加公告</div>
			<div class="panel-body">
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="require">* </span>公告名称</label>
					<div class="col-sm-9 col-xs-12">
						<input type="text" class="form-control" name="title" value="<?php  echo $notice['title'];?>">
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">跳转链接</label>
					<div class="col-sm-9 col-xs-12">
						<?php  echo tpl_form_field_tiny_link('link', $notice['link']);?>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="require">* </span>公告内容</label>
					<div class="col-sm-9 col-xs-12">
						<?php  echo tpl_ueditor('content', $notice['content']);?>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="require">* </span>公告排序</label>
					<div class="col-sm-9 col-xs-12">
						<input type="text" class="form-control" name="displayorder" value="<?php  echo $notice['displayorder'];?>">
						<span class="help-block">数字越大越靠前</span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">是否启用</label>
					<div class="col-sm-9 col-xs-12">
						<label class="radio-inline"><input type="radio" name="status" value="1" <?php  if($notice['status'] == 1) { ?>checked<?php  } ?>> 启用</label>
						<label class="radio-inline"><input type="radio" name="status" value="0" <?php  if($notice['status'] == 0) { ?>checked<?php  } ?>> 不启用</label>
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
<?php  } ?>
<?php  if($op == 'list') { ?>
<div class="main">
	<form class="form-horizontal" action="" method="post">
		<div class="panel panel-default">
			<div class="panel-body table-responsive">
				<table class="table table-hover">
					<thead class="navbar-inner">
						<tr>
							<th>公告名称</th>
							<th>跳转链接</th>
							<th>排序</th>
							<th>状态</th>
							<th style="width:150px; text-align:right;">操作</th>
						</tr>
					</thead>
					<tbody>
						<?php  if(is_array($notices)) { foreach($notices as $notice) { ?>
						<tr>
							<td>
								<input type="text" name="titles[]" class="form-control" value="<?php  echo $notice['title'];?>">
								<input type="hidden" name="ids[]" value="<?php  echo $notice['id'];?>">
							</td>
							<td><?php  echo tpl_form_field_tiny_link('links[]', $notice['link']);?></td>
							<td><input type="text" name="displayorders[]" class="form-control" value="<?php  echo $notice['displayorder'];?>"></td>
							<td>
								<input type="checkbox" value="<?php  echo $notice['status'];?>" name="status[]" data-id="<?php  echo $notice['id'];?>" <?php  if($notice['status'] == 1) { ?>checked<?php  } ?>>
							</td>
							<td style="text-align:right;">
								<a href="<?php  echo $this->createWebUrl('ptfnotice', array('op' => 'post', 'id' => $notice['id']))?>" class="btn btn-default btn-sm" title="编辑" data-toggle="tooltip" data-placement="top" ><i class="fa fa-edit"> </i> 编辑</a>
								<a href="<?php  echo $this->createWebUrl('ptfnotice', array('op' => 'del', 'id' => $notice['id']))?>" class="btn btn-default btn-sm" title="删除" data-toggle="tooltip" data-placement="top" onclick="if(!confirm('确定删除吗?')) return false;"><i class="fa fa-times"> </i> 删除</a>
							</td>
						</tr>
						<?php  } } ?>
					</tbody>
				</table>
			</div>
		</div>
		<div class="form-group">
			<div class="col-sm-9 col-xs-9 col-md-9">
				<input type="hidden" name="token" value="<?php  echo $_W['token'];?>">
				<input name="submit" id="" type="submit" value="提交" class="btn btn-primary col-lg-1">
			</div>
		</div>
	</form>
</div>
<?php  } ?>
<script type="text/javascript">
	$(function(){
		$('#form1').submit(function(){
			if($.trim($(':text[name="title"]').val()) == '') {
				util.message('标题不能为空');
				return false;
			}
			return true;
		});
		require(['jquery.qrcode', 'bootstrap.switch'], function($) {
			$(':checkbox[name="status[]"]').bootstrapSwitch();
			$(':checkbox[name="status[]"]').on('switchChange.bootstrapSwitch', function(e, state){
				var status = this.checked ? 1 : 0;
				var id = $(this).data('id');
				$.post("<?php  echo $this->createWebUrl('ptfnotice', array('op' => 'toggle_status'))?>", {status: status, id: id}, function(resp){
					location.reload();
				});
			});
		});
	});
</script>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('public/footer', TEMPLATE_INCLUDEPATH)) : (include template('public/footer', TEMPLATE_INCLUDEPATH));?>