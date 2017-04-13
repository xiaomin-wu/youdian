<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('public/header', TEMPLATE_INCLUDEPATH)) : (include template('public/header', TEMPLATE_INCLUDEPATH));?>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('plateform/mall-nav', TEMPLATE_INCLUDEPATH)) : (include template('plateform/mall-nav', TEMPLATE_INCLUDEPATH));?>
<div class="panel panel-default">
	<div class="panel-body">
		<ul class="nav nav-pills">
			<li <?php  if($op == 'list') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('ptfcategory');?>"> 分类列表</a></li>
			<li <?php  if($op == 'post') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('ptfcategory', array('op' => 'post'));?>"><?php  if(empty($_GPC['id'])) { ?>添加分类<?php  } else { ?>编辑分类<?php  } ?></a></li>
		</ul>
	</div>
</div>

<?php  if($op == 'post') { ?>
<form class="form-horizontal form" id="form1" action="" method="post" enctype="multipart/form-data">
	<input type="hidden" name="sid" value="<?php  echo $sid;?>">
	<div class="main">
		<div class="panel panel-default">
			<div class="panel-heading">编辑分类</div>
			<div class="panel-body">
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
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">是否开启幻灯片</label>
					<div class="col-sm-9 col-xs-9 col-md-9">
						<label class="radio-inline"><input type="radio" name="slide_status" value="1" <?php  if($category['slide_status'] == 1) { ?>checked<?php  } ?> onclick="$('#slide-container').removeClass('hide')"> 开启</label>
						<label class="radio-inline"><input type="radio" name="slide_status" value="0" <?php  if(!$category['slide_status']) { ?>checked<?php  } ?> onclick="$('#slide-container').addClass('hide')"> 关闭</label>
					</div>
				</div>
				<div class="form-group <?php  if(!$category['slide_status']) { ?>hide<?php  } ?>" id="slide-container">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">幻灯片列表</label>
					<div class="col-sm-9 col-xs-9 col-md-9">
						<table class="table table-hover table-bordered">
							<thead class="navbar-inner">
							<tr>
								<th>缩略图</th>
								<th>跳转链接</th>
								<th width="200">排序</th>
								<th class="text-right">操作</th>
							</tr>
							</thead>
							<tbody id="tpl-slide-container">
							<?php  if(!empty($category['slide'])) { ?>
								<?php  if(is_array($category['slide'])) { foreach($category['slide'] as $slide) { ?>
									<tr>
										<td >
											<div class="input-group ">
												<div class="input-group-addon">
													<img src="<?php  echo tomedia($slide['thumb']);?>" width="20" height="20">
												</div>
												<input type="text" name="slide_image[]" value="<?php  echo $slide['thumb'];?>" class="form-control" autocomplete="off">
												<span class="input-group-btn">
													<button class="btn btn-default btn-image" type="button">选择图片</button>
												</span>
											</div>
										</td>
										<td>
											<div class="input-group">
												<input type="text" value="<?php  echo $slide['link'];?>" name="slide_links[]" class="form-control " autocomplete="off">
												<span class="input-group-btn">
													<button class="btn btn-default btn-links" type="button">选择链接</button>
												</span>
											</div>
										</td>
										<td><input type="text" name="slide_displayorder[]" class="form-control" value="<?php  echo $slide['displayorder'];?>"></td>
										<td class="text-right">
											<a href="javascript:;" class="btn btn-default btn-del" title="删除" data-toggle="tooltip" data-placement="top"><i class="fa fa-times"> </i></a>
										</td>
									</tr>
								<?php  } } ?>
							<?php  } ?>
							</tbody>
							<tfooter>
								<tr>
									<td colspan="4">
										<a href="javascipt:;" id="edit-add"><i class="fa fa-plus-circle"></i> 继续添加</a>
									</td>
								</tr>
							</tfooter>
						</table>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">是否开启导航栏</label>
					<div class="col-sm-9 col-xs-9 col-md-9">
						<label class="radio-inline"><input type="radio" name="nav_status" value="1" <?php  if($category['nav_status'] == 1) { ?>checked<?php  } ?> onclick="$('#nav-container').removeClass('hide')"> 开启</label>
						<label class="radio-inline"><input type="radio" name="nav_status" value="0" <?php  if(!$category['nav_status']) { ?>checked<?php  } ?> onclick="$('#nav-container').addClass('hide')"> 关闭</label>
					</div>
				</div>
				<div class="form-group <?php  if(!$category['nav_status']) { ?>hide<?php  } ?>" id="nav-container">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">导航栏</label>
					<div class="col-sm-9 col-xs-9 col-md-9">
						<table class="table table-hover table-bordered">
							<thead class="navbar-inner">
							<tr>
								<th>图标</th>
								<th>标题</th>
								<th>副标题</th>
								<th>跳转链接</th>
							</tr>
							</thead>
							<tbody>
							<tr>
								<td>
									<div class="input-group ">
										<div class="input-group-addon">
											<img src="<?php  echo tomedia($category['nav'][0]['thumb'])?>" width="20" height="20">
										</div>
										<input type="text" name="nav_thumb[]" value="<?php  echo $category['nav'][0]['thumb'];?>" class="form-control" autocomplete="off">
										<span class="input-group-btn">
											<button class="btn btn-default btn-image" type="button">选择图片</button>
										</span>
									</div>
								</td>
								<td><input type="text" name="nav_title[]" class="form-control" value="<?php  echo $category['nav'][0]['title'];?>"></td>
								<td><input type="text" name="nav_sub_title[]" class="form-control" value="<?php  echo $category['nav'][0]['sub_title'];?>"></td>
								<td>
									<div class="input-group">
										<input type="text" value="<?php  echo $category['nav'][0]['link'];?>" name="nav_links[]" class="form-control " autocomplete="off">
										<span class="input-group-btn">
											<button class="btn btn-default btn-links" type="button">选择链接</button>
										</span>
									</div>
								</td>
							</tr>
							<tr>
								<td>
									<div class="input-group ">
										<div class="input-group-addon">
											<img src="<?php  echo tomedia($category['nav'][1]['thumb'])?>" width="20" height="20">
										</div>
										<input type="text" name="nav_thumb[]" value="<?php  echo $category['nav'][1]['thumb'];?>" class="form-control" autocomplete="off">
										<span class="input-group-btn">
											<button class="btn btn-default btn-image" type="button">选择图片</button>
										</span>
									</div>
								</td>
								<td><input type="text" name="nav_title[]" class="form-control" value="<?php  echo $category['nav'][1]['title'];?>"></td>
								<td><input type="text" name="nav_sub_title[]" class="form-control" value="<?php  echo $category['nav'][1]['sub_title'];?>"></td>
								<td>
									<div class="input-group">
										<input type="text" value="<?php  echo $category['nav'][1]['link'];?>" name="nav_links[]" class="form-control " autocomplete="off">
										<span class="input-group-btn">
											<button class="btn btn-default btn-links" type="button">选择链接</button>
										</span>
									</div>
								</td>
							</tr>
							</tbody>
						</table>
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
	var html = '<tr>'+
			'	<td>'+
			'		<div class="input-group ">'+
			'			<div class="input-group-addon">'+
			'				<img src="" width="20" height="20">'+
			'			</div>'+
			'			<input type="text" name="slide_image[]" value="" class="form-control" autocomplete="off">'+
			'			<span class="input-group-btn">'+
			'				<button class="btn btn-default btn-image" type="button">选择图片</button>'+
			'			</span>'+
			'		</div>'+
			'	</td>'+
			'	<td>'+
			'		<div class="input-group">'+
			'			<input type="text" value="" name="slide_links[]" class="form-control " autocomplete="off">'+
			'			<span class="input-group-btn">'+
			'				<button class="btn btn-default btn-links " type="button">选择链接</button>'+
			'			</span>'+
			'		</div>'+
			'	</td>'+
			'	<td><input type="text" name="slide_displayorder[]" class="form-control" value=""></td>'+
			'	<td class="text-right">'+
			'		<a href="javascript:;" class="btn btn-default btn-del" title="删除" data-toggle="tooltip" data-placement="top"><i class="fa fa-times"> </i></a>'+
			'	</td>'+
			'</tr>';
	$(document).on('click', '.btn-image', function(){
		var btn = $(this);
		var ipt = btn.parent().prev();
		var val = ipt.val();
		var img = ipt.parent().parent().find(".input-group-addon img");
		util.image(val, function(url){
			if(url.url){
				if(img.length > 0){
					img.get(0).src = url.url;
				}
				ipt.val(url.attachment);
				ipt.attr("filename",url.filename);
				ipt.attr("url",url.url);
			}
		}, null);
	})
	$(document).on('click', '.btn-links', function() {
			var ipt = $(this).parent().prev();
			tiny.linkBrowser(function(href){
				ipt.val(href);
			});
	})
	$(document).on('click', '#edit-add', function(){
		$('#tpl-slide-container').append(html);
	});
	$(document).on('click', '.btn-del', function(){
		$(this).parent().parent().remove();
	});
});
</script>
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
									<a href="<?php  echo $this->createWebUrl('ptfcategory', array('op' => 'post', 'id' => $item['id']))?>" class="btn btn-default btn-sm" title="编辑" data-toggle="tooltip" data-placement="top" ><i class="fa fa-edit"> </i></a>
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
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('public/footer', TEMPLATE_INCLUDEPATH)) : (include template('public/footer', TEMPLATE_INCLUDEPATH));?>
