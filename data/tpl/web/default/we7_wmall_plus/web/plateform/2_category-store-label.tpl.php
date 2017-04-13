<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('public/header', TEMPLATE_INCLUDEPATH)) : (include template('public/header', TEMPLATE_INCLUDEPATH));?>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('plateform/config-nav', TEMPLATE_INCLUDEPATH)) : (include template('plateform/config-nav', TEMPLATE_INCLUDEPATH));?>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('plateform/category-nav', TEMPLATE_INCLUDEPATH)) : (include template('plateform/category-nav', TEMPLATE_INCLUDEPATH));?>
<form class="form-horizontal form" id="form1" action="" method="post" enctype="multipart/form-data">
	<div class="main">
		<div class="panel panel-default">
			<div class="panel-body table-responsive" style="overflow:inherit">
				<table class="table table-hover">
					<thead class="navbar-inner">
					<tr>
						<th width="300">标签名称</th>
						<th>颜色</th>
						<th>排序</th>
						<th>类型</th>
						<th style="width:100px; text-align:right;">操作</th>
					</tr>
					</thead>
					<tbody>
					<?php  if(!empty($labels)) { ?>
						<?php  if(is_array($labels)) { foreach($labels as $label) { ?>
							<tr>
								<td>
									<input type="hidden" name="id[]" value="<?php  echo $label['id'];?>"/>
									<input type="text" name="title[]" value="<?php  echo $label['title'];?>" class="form-control"/>
								</td>
								<td>
									<div class="input-group">
										<input class="form-control" type="text" name="color[]" value="<?php  echo $label['color'];?>" placeholder="请选择颜色">
										<span class="input-group-addon" style="width:35px;border-left:none;background-color:<?php  echo $label['color'];?>"></span>
										<span class="input-group-btn">
											<button class="btn btn-default btn-colorpicker" type="button">选择颜色 <i class="fa fa-caret-down"></i></button>
										</span>
									</div>
								</td>
								<td>
									<input type="text" name="displayorder[]" value="<?php  echo $label['displayorder'];?>" class="form-control" style="width:100px"/>
								</td>
								<td>
									<?php  if($label['is_system'] == 1) { ?>
										<span class="label label-danger">系统标签</span>
									<?php  } else { ?>
										<span class="label label-success">自定义</span>
									<?php  } ?>
								</td>
								<td class="text-right">
									<?php  if(!$label['is_system']) { ?>
										<a href="javascript:;" class="btn btn-danger btn-sm btn-delete"><i class="fa fa-times"></i></a>
									<?php  } ?>
								</td>
							</tr>
						<?php  } } ?>
					<?php  } ?>
					<tr>
						<td colspan="5">
							<a href="javascript:;" id="label-add"><i class="fa fa-plus-circle"></i> 添加商户标签</a>
						</td>
					</tr>
					</tbody>
				</table>
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
<script>
$(function(){
	$('#label-add').click(function(){
		var html = '	<tr>'+
					'		<td>'+
					'			<input type="text" name="add_title[]" class="form-control" placeholder="两个字,例如:品牌"/>'+
					'		</td>'+
					'		<td>'+
					'			<div class="input-group">'+
					'				<input class="form-control" type="text" name="add_color[]" placeholder="请选择颜色">'+
					'				<span class="input-group-addon" style="width:35px;border-left:none;"></span>'+
					'				<span class="input-group-btn">'+
					'					<button class="btn btn-default btn-colorpicker" type="button">选择颜色 <i class="fa fa-caret-down"></i></button>'+
					'				</span>'+
					'			</div>'+
					'		</td>'+
					'		<td>'+
					'			<input type="text" name="add_displayorder[]" class="form-control" style="width:100px"/>'+
					'		</td>'+
					'		<td>'+
					'			<lable class="label label-success">自定义</lable>'+
					'		</td>'+
					'		<td class="text-right">'+
					'			<a href="javascript:;" class="btn btn-danger btn-sm btn-delete"><i class="fa fa-times"></i></a>'+
					'		</td>'+
					'	</tr>';
				;
		$(this).parents('tr').before(html);
		var elm = $(this).parents('tr').prev().find('.btn-colorpicker').get(0);
		util.colorpicker(elm, function(color){
			$(elm).parent().prev().prev().val(color.toHexString());
			$(elm).parent().prev().css("background-color", color.toHexString());
		});
	});
	$(document).on('click', '.btn-delete', function(){
		$(this).parents('tr').remove();
	});
	$('.btn-colorpicker').each(function(){
		var elm = this;
		util.colorpicker(elm, function(color){
			$(elm).parent().prev().prev().val(color.toHexString());
			$(elm).parent().prev().css("background-color", color.toHexString());
		});
	});
});
</script>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('public/footer', TEMPLATE_INCLUDEPATH)) : (include template('public/footer', TEMPLATE_INCLUDEPATH));?>