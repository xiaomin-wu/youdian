<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('public/header', TEMPLATE_INCLUDEPATH)) : (include template('public/header', TEMPLATE_INCLUDEPATH));?>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('plateform/nav', TEMPLATE_INCLUDEPATH)) : (include template('plateform/nav', TEMPLATE_INCLUDEPATH));?>
<ul class="nav nav-tabs">
	<li <?php  if($op == 'list') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('ptfcategory');?>"> 分类列表</a></li>
	<li <?php  if($op == 'post') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('ptfcategory', array('op' => 'post'));?>">添加分类</a></li>
</ul>
<?php  if($op == 'post') { ?>
<form class="form-horizontal form" id="form1" action="" method="post" enctype="multipart/form-data">
	<input type="hidden" name="sid" value="<?php  echo $sid;?>">
	<div class="main">
		<div class="panel panel-default">
			<div class="panel-heading">添加分类</div>
			<div class="panel-body">
				<div id="tpl">
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="require">* </span>分类名称</label>
						<div class="col-sm-9 col-xs-12">
							<input type="text" class="form-control" name="title[]" value="">
						</div>
					</div>
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="require">* </span>图标</label>
						<div class="col-sm-9 col-xs-12">
							<?php  echo tpl_form_field_image('thumb[]', '');?>
						</div>
					</div>
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">跳转链接(选填)</label>
						<div class="col-sm-9 col-xs-9 col-md-9">
							<?php  echo tpl_form_field_tiny_link('link[]', $nav['link']);?>
							<span class="help-block">
								如果设置了跳转链接，客户点击分类， 将直接跳转到设置的链接。<storng class="text-danger">同时， 商家在后台选择店铺所属分类时，该分类不会显示在可选项中。</storng>
							</span>
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
<script type="text/javascript">
	require(['util'], function(u){
		$('#post-add').click(function(){
			$('#tpl-container').append($('#tpl').html());
		});
	});
</script>
<?php  } ?>

<?php  if($op == 'edit') { ?>
<form class="form-horizontal form" id="form1" action="" method="post" enctype="multipart/form-data">
	<input type="hidden" name="sid" value="<?php  echo $sid;?>">
	<div class="main">
		<div class="panel panel-default">
			<div class="panel-heading">编辑分类</div>
			<div class="panel-body">
				<div id="tpl">
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="require">* </span>分类名称</label>
						<div class="col-sm-9 col-xs-12">
							<input type="text" class="form-control" name="title" value="<?php  echo $category['title'];?>">
						</div>
					</div>
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="require">* </span>图标</label>
						<div class="col-sm-9 col-xs-12">
							<?php  echo tpl_form_field_image('thumb', $category['thumb']);?>
						</div>
					</div>
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">跳转链接(选填)</label>
						<div class="col-sm-9 col-xs-9 col-md-9">
							<?php  echo tpl_form_field_tiny_link('link', $category['link']);?>
							<span class="help-block">
								如果设置了跳转链接，客户点击分类， 将直接跳转到设置的链接。<storng class="text-danger">同时， 商家在后台选择店铺所属分类时，该分类不会显示在可选项中。</storng>
							</span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">分类排序</label>
						<div class="col-sm-9 col-xs-12">
							<input type="text" class="form-control" name="displayorder" value="<?php  echo $category['displayorder'];?>">
						</div>
					</div>
					<hr>
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
							<th width="70">图标</th>
							<th>分类名称</th>
							<th>分类类型</th>
							<th>排序</th>
							<th>门店数</th>
							<th style="width:150px; text-align:right;">操作</th>
						</tr>
					</thead>
					<tbody>
						<?php  if(is_array($lists)) { foreach($lists as $item) { ?>
							<tr>
								<input type="hidden" name="ids[]" value="<?php  echo $item['id'];?>">
								<td><img src="<?php  echo tomedia($item['thumb']);?>" width="50"></td>
								<td><input type="text" style="width:130px" name="title[]" class="form-control" value="<?php  echo $item['title'];?>"></td>
								<td>
									<?php  if(empty($item['link'])) { ?>
										<span class="label label-success">系统链接</span>
									<?php  } else { ?>
										<span class="label label-danger">自定义链接</span>
									<?php  } ?>
								</td>
								<td><input type="text" style="width:100px" name="displayorder[]" class="form-control" value="<?php  echo $item['displayorder'];?>"></td>
								<td><?php  echo $nums[$item['id']]['num'];?></td>
								<td style="text-align:right;">
									<a href="<?php  echo $this->createWebUrl('ptfcategory', array('op' => 'edit', 'id' => $item['id']))?>" class="btn btn-default btn-sm" title="编辑" data-toggle="tooltip" data-placement="top" ><i class="fa fa-edit"> </i></a>
									<a href="<?php  echo $this->createWebUrl('ptfcategory', array('op' => 'del', 'id' => $item['id']))?>" class="btn btn-default btn-sm" title="删除" data-toggle="tooltip" data-placement="top" onclick="if(!confirm('确定删除吗?')) return false;"><i class="fa fa-times"> </i></a>
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
<?php  } ?>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common', TEMPLATE_INCLUDEPATH)) : (include template('common', TEMPLATE_INCLUDEPATH));?>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('public/footer', TEMPLATE_INCLUDEPATH)) : (include template('public/footer', TEMPLATE_INCLUDEPATH));?>