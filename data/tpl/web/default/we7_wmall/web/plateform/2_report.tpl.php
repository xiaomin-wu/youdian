<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('public/header', TEMPLATE_INCLUDEPATH)) : (include template('public/header', TEMPLATE_INCLUDEPATH));?>
<ul class="nav nav-tabs">
	<li <?php  if($op == 'list') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('ptfreport', array('op' => 'list'));?>">投诉记录</a></li>
</ul>

<?php  if($op == 'list') { ?>
<div class="main">
	<div class="panel panel-info">
		<div class="panel-heading">筛选</div>
		<div class="panel-body">
			<form action="./index.php" method="get" class="form-horizontal" role="form">
				<input type="hidden" name="c" value="site">
				<input type="hidden" name="a" value="entry">
				<input type="hidden" name="m" value="we7_wmall">
				<input type="hidden" name="do" value="ptfreport"/>
				<input type="hidden" name="op" value="list"/>
				<input type="hidden" name="status" value="<?php  echo $status;?>"/>
				<input type="hidden" name="addtime" value="<?php  echo $addtime;?>"/>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-1 control-label">处理状态</label>
					<div class="col-sm-9 col-xs-9 col-md-9">
						<div class="btn-group">
							<a href="<?php  echo filter_url('status:-1');?>" class="btn <?php  if($status == -1) { ?>btn-primary<?php  } else { ?>btn-default<?php  } ?>">全部投诉</a>
							<a href="<?php  echo filter_url('status:1');?>" class="btn <?php  if($status == 1) { ?>btn-primary<?php  } else { ?>btn-default<?php  } ?>">已处理</a>
							<a href="<?php  echo filter_url('status:0');?>" class="btn <?php  if($status == 0) { ?>btn-primary<?php  } else { ?>btn-default<?php  } ?>">未处理</a>
						</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-1 control-label">投诉时间</label>
					<div class="col-sm-9 col-xs-9 col-md-9">
						<div class="btn-group">
							<a href="<?php  echo filter_url('addtime:-1');?>" class="btn <?php  if($addtime == -1) { ?>btn-primary<?php  } else { ?>btn-default<?php  } ?>">不限</a>
							<a href="<?php  echo filter_url('addtime:7');?>" class="btn <?php  if($addtime == 7) { ?>btn-primary<?php  } else { ?>btn-default<?php  } ?>">一周内</a>
							<a href="<?php  echo filter_url('addtime:15');?>" class="btn <?php  if($addtime == 15) { ?>btn-primary<?php  } else { ?>btn-default<?php  } ?>">半月内</a>
							<a href="<?php  echo filter_url('addtime:31');?>" class="btn <?php  if($addtime == 31) { ?>btn-primary<?php  } else { ?>btn-default<?php  } ?>">一月内</a>
							<a href="<?php  echo filter_url('addtime:93');?>" class="btn <?php  if($addtime == 93) { ?>btn-primary<?php  } else { ?>btn-default<?php  } ?>">三月内</a>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
	<form class="form-horizontal" action="" method="post">
		<div class="panel panel-default">
			<div class="panel-body table-responsive" style="overflow:inherit">
				<table class="table table-hover">
					<thead class="navbar-inner">
					<tr>
						<th>投诉商家</th>
						<th>投诉人手机号</th>
						<th width="400">投诉内容</th>
						<th>处理状态</th>
						<th>投诉时间</th>
						<th class="text-right">操作</th>
					</tr>
					</thead>
					<tbody>
					<?php  if(is_array($reports)) { foreach($reports as $report) { ?>
					<tr>
						<td><?php  echo $stores[$report['sid']]['title'];?></td>
						<td><?php  echo $report['mobile'];?></td>
						<td>
							<span class="label label-danger" style="cursor: pointer" data-toggle="popover" title="投诉详情" data-content="<?php  echo $report['note'];?>">
								<?php  echo $report['title'];?>
							</span>
							<?php  if(!empty($report['thumbs'])) { ?>
								<div style="margin-top: 10px;">
									<?php  if(is_array($report['thumbs'])) { foreach($report['thumbs'] as $thumb) { ?>
										<img src="<?php  echo tomedia($thumb);?>" data-toggle="popover" data-html="true" data-placement="bottom" data-content='<img src="<?php  echo tomedia($thumb);?>">' alt="" width="80" height="80"/>
									<?php  } } ?>
								</div>
							<?php  } ?>
						</td>
						<td>
							<?php  if($report['status'] == 1) { ?>
								<span class="label label-success">
									已处理
								</span>
							<?php  } else { ?>
								<span class="label label-danger">
									未处理
								</span>
							<?php  } ?>
						</td>
						<td>
							<span class="label label-info">
								<?php  echo date('Y-m-d H:i', $report['addtime']);?>
							</span>
						</td>
						<td class="text-right">
							<a href="javascript:;" data-status="1" data-id="<?php  echo $report['id'];?>" class="btn btn-default btn-status">设为已处理</a>
							<a href="javascript:;" data-status="0" data-id="<?php  echo $report['id'];?>" class="btn btn-default btn-status">设为未处理</a>
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
<?php  } ?>
<script>
$(function(){
	$('.btn-status').click(function(){
		if(!confirm('确定变更状态吗')) {
			return false;
		}
		var id = $(this).data('id');
		var status = $(this).data('status');
		var params = {
			id: id,
			status: status
		};
		$.post("<?php  echo $this->createWebUrl('ptfreport', array('op' => 'status'))?>", params, function(data){
			location.reload();
		});
	});
});
</script>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('public/footer', TEMPLATE_INCLUDEPATH)) : (include template('public/footer', TEMPLATE_INCLUDEPATH));?>