<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('public/header', TEMPLATE_INCLUDEPATH)) : (include template('public/header', TEMPLATE_INCLUDEPATH));?>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('store/nav', TEMPLATE_INCLUDEPATH)) : (include template('store/nav', TEMPLATE_INCLUDEPATH));?>
<style type="text/css">
	.order-detail-info>div{margin-bottom:10px; padding-left:15px;}
	.page-trade-order h4{font-size:14px; font-weight:700;}
	.page-trade-order .form-group{margin-bottom:0;}
	.page-trade-order .form-group .control-label{font-weight:normal; color:#999;}
	.page-trade-order .order-infos{border-right:1px solid #ddd;}
	.page-trade-order .parting-line{height:1px;border-top:1px dashed #e5e5e5; margin:3px 0;}
	.page-trade-order .order-state{padding-left:40px; position:relative; margin:20px 0 40px;}
	.page-trade-order .order-state>span{color:#07d; position:absolute; left:0; top:5px; font-size:25px; display:inline-block; width:30px; height:30px; border:1px solid #07d; border-radius:30px; text-align:center; line-height:30px;}
	#close-order ul li{padding:5px 15px; cursor:pointer;}
	#close-order ul li:hover{background:#eee;}
	.fix a.js-order-edit-address{display:none; color:red;}
	.fix:hover a.js-order-edit-address{display:inline;}
	.page-trade-order .col-sm-9{word-break: break-word; overflow:hidden;}
	.table-log>tbody>tr>td{border-top:none;}
</style>
<?php  if($op == 'list') { ?>
<div class="main">
	<div class="panel panel-info">
		<div class="panel-heading">筛选</div>
		<div class="panel-body">
			<form action="./index.php" method="get" class="form-horizontal" role="form">
				<input type="hidden" name="c" value="site">
				<input type="hidden" name="a" value="entry">
				<input type="hidden" name="m" value="we7_wmall_plus">
				<input type="hidden" name="do" value="order"/>
				<input type="hidden" name="op" value="list"/>
				<input type="hidden" name="status" value="<?php  echo $_GPC['status'];?>"/>
				<input type="hidden" name="is_pay" value="<?php  echo $_GPC['is_pay'];?>"/>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-1 control-label">支付状态</label>
					<div class="col-sm-12 col-md-6">
						<div class="btn-group">
							<a href="<?php  echo filter_url('is_pay:-1');?>" class="btn <?php  if($is_pay == -1) { ?>btn-primary<?php  } else { ?>btn-default<?php  } ?>">不限</a>
							<a href="<?php  echo filter_url('is_pay:0');?>" class="btn <?php  if($is_pay == 0) { ?>btn-primary<?php  } else { ?>btn-default<?php  } ?>">未支付</a>
							<a href="<?php  echo filter_url('is_pay:1');?>" class="btn <?php  if($is_pay == 1) { ?>btn-primary<?php  } else { ?>btn-default<?php  } ?>">已支付</a>
						</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-1 control-label">用户信息</label>
					<div class="col-sm-12 col-md-6">
						<input class="form-control" name="keyword" placeholder="输入用户名或手机号" type="text" value="<?php  echo $_GPC['keyword'];?>">
					</div>
				</div>
				<div class="form-group clearfix">
					<label class="col-xs-12 col-sm-3 col-md-1 control-label">下单时间</label>
					<div class="col-sm-7 col-lg-8 col-md-8 col-xs-12">
						<?php  echo tpl_form_field_daterange('addtime', array('start' => date('Y-m-d', $starttime), 'end' => date('Y-m-d', $endtime)));?>
					</div>
					<div class="col-xs-12 col-sm-2 col-md-1 col-lg-1">
						<button class="btn btn-default"><i class="fa fa-search"></i> 搜索</button>
					</div>
				</div>
			</form>
		</div>
	</div>
	<?php  if($wait_total > 0) { ?>
	<div class="alert alert-danger">
		<i class="fa fa-bell"></i> <?php  echo $wait_total;?>个订单未处理, 请尽快处理.
	</div>
	<?php  } ?>
	<form class="form-horizontal" action="<?php  echo $this->createWebUrl('order', array('op' => 'status'));?>" id="form-order" method="post">
		<ul class="order-nav order-nav-tabs">
			<li <?php  if($status == 0) { ?>class="active"<?php  } ?>><a href="<?php  echo filter_url('status:0');?>">所有订单</a></li>
			<li <?php  if($status == 1) { ?>class="active"<?php  } ?>><a href="<?php  echo filter_url('status:1');?>">未处理订单</a></li>
			<li <?php  if($status == 2) { ?>class="active"<?php  } ?>><a href="<?php  echo filter_url('status:2');?>">已确认订单</a></li>
			<li <?php  if($status == 3) { ?>class="active"<?php  } ?>><a href="<?php  echo filter_url('status:3');?>">待配送订单</a></li>
			<li <?php  if($status == 4) { ?>class="active"<?php  } ?>><a href="<?php  echo filter_url('status:4');?>">配送中订单</a></li>
			<li <?php  if($status == 5) { ?>class="active"<?php  } ?>><a href="<?php  echo filter_url('status:5');?>">已完成订单</a></li>
			<li <?php  if($status == 6) { ?>class="active"<?php  } ?>><a href="<?php  echo filter_url('status:6');?>">已取消订单</a></li>
		</ul>
		<div class="panel panel-default">
			<div class="panel-body table-responsive" style="overflow:inherit">
				<table class="table table-hover">
					<thead class="navbar-inner">
						<tr>
							<th width="30"></th>
							<th width="120">编号</th>
							<th>预定人/电话</th>
							<th>订单类型</th>
							<th>支付方式</th>
							<th>订单状态</th>
							<th>打印(份数)</th>
							<th>份数/总价</th>
							<th>最终到账</th>
							<th>下单时间</th>
							<th style="width:250px; text-align:right;">详情</th>
						</tr>
					</thead>
					<tbody>
						<?php  if(is_array($data)) { foreach($data as $dca) { ?>
						<tr>
							<td><input type="checkbox" name="id[]" value="<?php  echo $dca['id'];?>"></td>
							<td>
								<span class="label label-warning">商户号:#<?php  echo $dca['serial_sn'];?></span>
								<br>
								<span class="label label-info label-br">平台号:<?php  echo $dca['id'];?></span>
							</td>
							<td>
								<?php  echo $dca['username'];?>
								<br>
								<?php  echo $dca['mobile'];?>
							</td>
							<td>
								<span class="<?php  echo $order_types[$dca['order_type']]['css'];?>"><?php  echo $order_types[$dca['order_type']]['text'];?></span>
								<br>
								<span class="label label-info label-br">收货码:<?php  echo $dca['code'];?></span>
							</td>
							<td>
								<?php  if(!$dca['is_pay']) { ?>
									<span class="label label-danger">未支付</span>
								<?php  } else { ?>
									<span class="<?php  echo $pay_types[$dca['pay_type']]['css'];?>"><?php  echo $pay_types[$dca['pay_type']]['text'];?></span>
								<?php  } ?>
								<br>
								<span class="label label-info label-br dist hide" data-lat="<?php  echo $dca['location_x'];?>"  data-lng="<?php  echo $dca['location_y'];?>">距离:未知</span>
							</td>
							<td>
								<span class="<?php  echo $order_status[$dca['status']]['css'];?>">
									<?php  echo $order_status[$dca['status']]['text'];?>
								</span>
								<?php  if($dca['refund_status'] > 0) { ?>
									<br>
									<span class="label label-danger label-br">有退款申请</span>
								<?php  } ?>
								<?php  if($dca['deliveryer_id'] > 0) { ?>
									<br>
									<span class="label label-info label-br">配送员: <?php  echo $deliveryers[$dca['deliveryer_id']]['title'];?></span>
								<?php  } ?>
							</td>
							<td><a href="javascript:;" class="btn btn-default btn-sm print" data-id="<?php  echo $dca['id'];?>" title="点我打印订单" data-toggle="tooltip" data-placement="top">
								<i class="fa fa-print"></i> 
								(
									<?php  if($dca['print_nums'] > 0) { ?>
										<span style="color:green"><?php  echo $dca['print_nums'];?></span>
									<?php  } else { ?>
										<span style="color:red"><?php  echo $dca['print_nums'];?></span>
									<?php  } ?>
								)</a>
							</td>
							<td>
								<?php  echo $dca['num'];?>份/<?php  echo $dca['total_fee'];?>元
							</td>
							<td><span class="label label-info"><?php  echo $dca['store_final_fee'];?>元</span></td>
							<td><?php  echo date('Y-m-d H:i', $dca['addtime'])?></td>
							<td style="text-align:right; overflow:visible;">
								<div class="btn-group">
									<?php  if($dca['is_remind']) { ?>
										<a href="javascript:;" data-id="<?php  echo $dca['id'];?>" class="btn btn-danger btn-sm order-reply-remind" title="回复催单请求" data-toggle="tooltip" data-placement="top">有催单</a>
									<?php  } ?>
									<?php  if($dca['status'] < 5) { ?>
										<div class="btn-group">
											<button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="变更订单状态" data-toggle="tooltip" data-placement="top">变更状态 <span class="caret"></span></button>
											<ul class="dropdown-menu">
												<?php  if($dca['status'] == 1) { ?>
													<li><a href="<?php  echo $this->createWeburl('order', array('op' => 'status', 'id' => $dca['id'], 'status' => 2, 'type' => 'handle'));?>" onclick="if(!confirm('确定接单?')) return false;">接单</a></li>
													<li><a href="<?php  echo $this->createWeburl('order', array('op' => 'cancel', 'id' => $dca['id']))?>" class="btn-cancel" data-pay="<?php  echo $dca['is_pay'];?>"  data-pay-type="<?php  echo $dca['pay_type'];?>" title="取消订单" data-toggle="tooltip" data-placement="top">取消订单</a></li>
												<?php  } ?>
												<?php  if($dca['order_type'] == 1) { ?>
													<?php  if($dca['status'] == 2 || $dca['status'] == 3) { ?>
														<li><a href="<?php  echo $this->createWeburl('order', array('op' => 'status', 'id' => $dca['id'], 'status' => 3, 'type' => 'notify_deliveryer_collect'));?>" onclick="if(!confirm('确定通知配送员配送?')) return false;">通知配送员配送</a></li>
														<?php  if($store['delivery_mode'] == 1) { ?>
															<li><a href="<?php  echo $this->createWeburl('order', array('op' => 'status', 'id' => $dca['id'], 'status' => 4, 'type' => 'delivery_ing'));?>" onclick="if(!confirm('确定修改订单状态?')) return false;">设为配送中</a></li>
															<li><a href="javascript:;" class="select-deliveryer" data-type="single" data-id="<?php  echo $dca['id'];?>">指定配送员配送</a><li>
														<?php  } ?>
													<?php  } ?>
													<?php  if($dca['status'] == 4) { ?>
														<li><a href="<?php  echo $this->createWeburl('order', array('op' => 'status', 'id' => $dca['id'], 'status' => 5, 'type' => 'end'));?>" onclick="if(!confirm('确定修改订单状态?')) return false;">设为已完成</a></li>
													<?php  } ?>
												<?php  } else if($dca['order_type'] == 2) { ?>
													<li><a href="<?php  echo $this->createWeburl('order', array('op' => 'status', 'id' => $dca['id'], 'status' => 5, 'type' => 'end'));?>" onclick="if(!confirm('确定修改订单状态?')) return false;">设为已完成</a></li>
												<?php  } ?>
											</ul>
										</div>
									<?php  } ?>
									<?php  if($dca['status'] < 5) { ?>
										<a href="<?php  echo $this->createWeburl('order', array('op' => 'cancel', 'id' => $dca['id']))?>" class="btn btn-default btn-sm btn-cancel" data-pay="<?php  echo $dca['is_pay'];?>"  data-pay-type="<?php  echo $dca['pay_type'];?>" title="取消订单" data-toggle="tooltip" data-placement="top">取消订单</a>
									<?php  } ?>
									<a href="<?php  echo $this->createWeburl('order', array('op' => 'detail', 'id' => $dca['id']))?>" class="btn btn-success btn-sm" title="查看详情" data-toggle="tooltip" data-placement="top">详情</a>
								</div>
							</td>
						</tr>
						<?php  } } ?>
						<?php  if($status > 0 && $status < 5) { ?>
							<tr>
								<td><input type="checkbox" id="selectall"></td>
								<td colspan="11">
									<?php  if($status == 1) { ?>
										<a href="javascript:;" data-id="2" data-type="handel" class="btn btn-primary btn-order">确定接单</a>
									<?php  } ?>
									<?php  if($status == 2 || $status == 3) { ?>
										<a href="javascript:;" data-id="3" data-type="notify_deliveryer_collect" class="btn btn-primary btn-order">通知配送员配送</a>
										<?php  if($store['delivery_mode'] == 1) { ?>
											<a href="javascript:;" data-id="4" data-type="delivery_ing" class="btn btn-primary btn-order">设为配送中</a>
											<a href="javascript:;" class="btn btn-success select-deliveryer" data-type="mutil">指定配送员配送</a>
										<?php  } ?>
									<?php  } ?>
									<?php  if($status == 4) { ?>
										<a href="javascript:;" data-id="5" data-type="end" class="btn btn-primary btn-order">设为已完成</a>
									<?php  } ?>
								</td>
							</tr>
						<?php  } ?>
						<tr>
							<td colspan="11">
								<p class="text-danger">通知配送员配送: 系统将会把选中的订单状态设置为"待配送", 并通知该门店的所有配送员有新的订单需要配送</p>
								<p class="text-danger">指定配送员配送: 系统将会把选中的订单状态设置为"配送中", 并通知指定的配送员有新的订单需要配送</p>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
		<?php  echo $pager;?>
		<input type="hidden" name="status" value="0" id="status">
		<input type="hidden" name="type" value="" id="type">
		<input type="hidden" name="token" value="<?php  echo $_W['token'];?>">
	</form>
</div>
<div id="allmap" class="hide"></div>
<script type="text/javascript" src="http://webapi.amap.com/maps?v=1.3&key=550a3bf0cb6d96c3b43d330fb7d86950"></script>
<script type="text/javascript">
require(['util'], function(u){
	<?php  if($store['location_x'] && $store['location_y']) { ?>
		var lnglat = new AMap.LngLat("<?php  echo $store['location_y'];?>", "<?php  echo $store['location_x'];?>");
		$.each($('.dist'), function(){
			var lat = $(this).data('lat');
			var lng = $(this).data('lng');
			if(lat && lng) {
				$(this).removeClass('hide').html('大约' + ((lnglat.distance([lng, lat]))/1000).toFixed(2)+' 公里');
			}
		});
	<?php  } ?>

	$('#selectall').click(function(){
		$('#form-order :checkbox').prop('checked', $(this).prop('checked'));
	});

	$('.btn-order').click(function(){
		if($('#form-order :checkbox:checked').length == 0) {
			u.message('请选择订单', '', 'error');
			return false;
		}
		if(!confirm('确定修改订单的状态吗')) {
			return false;
		}
		$('#form-order #status').val($(this).data('id'));
		$('#form-order #type').val($(this).data('type'));
		$('#form-order').submit();
		return false;
	});

	$('.btn-cancel').click(function(e){
		var pay = $(this).data('pay');
		var pay_type = $(this).data('pay-type');
		var tip = '确定取消订单吗';
		if(pay == 1) {
			if(pay_type == 'cash') {
				var tip = '该订单为现金支付, 取消订单需要商家自己处理退款, 确定取消订单吗';
			} else if(pay_type != 'delivery') {
				var tip = '该订单为线上支付, 取消订单将发起退款流程, 确定取消订单吗';
			}
		}
		if(!confirm(tip)) {
			e.preventDefault();
		}
		return true;
	});

	$('.print').click(function(){
		if(!confirm('确定打印该订单吗？')) {
			return false;
		}
		var id = $(this).attr('data-id');
		$.post("<?php  echo $this->createWeburl('order', array('op' => 'print'))?>", {'id' : id}, function(data) {
			if(data != 'success') {
				u.message(data, '', 'error');
			} else {
				location.reload();
			}
		});
		return false;
	});
});
</script>
<?php  } else if($op == 'detail') { ?>
<form class="form-horizontal" role="form">
	<div class="page-trade-order">
		<div class="order-list">
			<div class="freight-content">
				<div class="freight-template-item panel panel-default">
					<div class="panel-body clearfix">
						<div class="col-xs-12 col-sm-6 order-infos">
							<h4>订单信息</h4>
							<div class="form-group clearfix">
								<label class="col-md-3 control-label">订单编号：</label>
								<div class="col-md-9 form-control-static">
									<?php  echo $order['ordersn'];?>
								</div>
							</div>
							<div class="form-group clearfix">
								<label class="col-md-3 control-label">下单时间：</label>
								<div class="col-md-9 form-control-static">
									<?php  echo date('Y-m-d H:i', $order['addtime']);?>
								</div>
							</div>
							<div class="form-group clearfix">
								<label class="col-md-3 control-label">订单状态：</label>
								<div class="col-md-9 form-control-static">
									<span class="<?php  echo $order_status[$order['status']]['css'];?>"><?php  echo $order_status[$order['status']]['text'];?></span>
								</div>
							</div>
							<div class="form-group clearfix">
								<label class="col-md-3 control-label">配送方式：</label>
								<div class="col-md-9 form-control-static">
									<span class="<?php  echo $order_types[$order['order_type']]['css'];?>"><?php  echo $order_types[$order['order_type']]['text'];?></span>
								</div>
							</div>
							<div class="form-group clearfix">
								<label class="col-md-3 control-label">配送/自提时间：</label>
								<div class="col-md-9 form-control-static">
									<?php  echo $order['delivery_day'];?>~<?php  echo $order['delivery_time'];?>
								</div>
							</div>
							<div class="form-group clearfix">
								<label class="col-md-3 control-label">付款方式：</label>
								<div class="col-md-9 form-control-static">
									<?php  if(!$order['is_pay']) { ?>
										<span class="label label-danger">未支付</span>
									<?php  } else { ?>
										<span class="<?php  echo $pay_types[$order['pay_type']]['css'];?>"><?php  echo $pay_types[$order['pay_type']]['text'];?></span>
									<?php  } ?>
								</div>
							</div>
							<div class="form-group clearfix">
								<label class="col-md-3 control-label">下单人信息：</label>
								<div class="col-md-9 form-control-static">
									<?php  echo $order['username'];?> <?php  echo $order['mobile'];?>
								</div>
							</div>
							<div class="form-group clearfix">
								<label class="col-md-3 control-label">配送地址：</label>
								<div class="col-md-9 form-control-static">
									<?php  echo $order['address'];?>
								</div>
							</div>
							<div class="parting-line"></div>
							<div class="form-group clearfix">
								<label class="col-md-3 control-label">备注：</label>
								<div class="col-md-9 form-control-static">
									<?php  echo $order['note'];?>
								</div>
							</div>
						</div>

						<div class="col-xs-12 col-sm-6">
							<h4>订单费用</h4>
							<div class="form-group clearfix">
								<label class="col-md-3 control-label">商品价格：</label>
								<div class="col-md-9 form-control-static">
									+￥ <?php  echo $order['price'];?>
								</div>
							</div>
							<div class="form-group clearfix">
								<label class="col-md-3 control-label">餐盒费：</label>
								<div class="col-md-9 form-control-static">
									+￥ <?php  echo $order['box_price'];?>
								</div>
							</div>
							<div class="form-group clearfix">
								<label class="col-md-3 control-label">包装费：</label>
								<div class="col-md-9 form-control-static">
									+￥ <?php  echo $order['pack_fee'];?>
								</div>
							</div>
							<div class="form-group clearfix">
								<label class="col-md-3 control-label">配送费：</label>
								<div class="col-md-9 form-control-static">
									+￥ <?php  echo $order['delivery_fee'];?>
								</div>
							</div>
							<div class="form-group clearfix">
								<label class="col-md-3 control-label">平台抽取佣金：</label>
								<div class="col-md-9 form-control-static">
									-￥ <?php  echo $order['plateform_serve_fee'];?> = (商品小计￥<?php  echo $order['price'];?> + 餐盒费￥<?php  echo $order['box_price'];?> + 包装费￥<?php  echo $order['pack_fee'];?> - 商户优惠活动￥<?php  echo $order['store_discount_fee'];?>)X<?php  echo $order['plateform_serve_rate'];?>%
								</div>
							</div>
							<div class="form-group clearfix">
								<label class="col-md-3 control-label">平台配送费：</label>
								<div class="col-md-9 form-control-static">
									-￥ <?php  echo $order['plateform_delivery_fee'];?>
								</div>
							</div>
							<div class="form-group clearfix">
								<label class="col-md-3 control-label">商户优惠：</label>
								<div class="col-md-9 form-control-static">
									-￥ <?php  echo $order['store_discount_fee'];?>
								</div>
							</div>
							<div class="form-group clearfix">
								<label class="col-md-3 control-label">平台补贴：</label>
								<div class="col-md-9 form-control-static">
									+￥ <?php  echo $order['plateform_discount_fee'];?>
								</div>
							</div>
							<div class="form-group clearfix">
								<label class="col-md-3 control-label">商户实际收入：</label>
								<div class="col-md-9 form-control-static">
									￥ <?php  echo $order['store_final_fee'];?> (本单顾客实际支付:￥ <?php  echo $order['final_fee'];?>)
								</div>
							</div>
							<?php  if($order['discount_fee'] > 0) { ?>
								<?php  if(is_array($discount)) { foreach($discount as $row) { ?>
									<div class="form-group clearfix">
										<label class="col-md-3 control-label"><?php  echo $row['name'];?>：</label>
										<div class="col-md-9 form-control-static">
											- ￥ <?php  echo $row['fee'];?>
										</div>
									</div>
								<?php  } } ?>
							<?php  } ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="panel panel-default">
		<div class="panel-heading">商品信息【共 <strong><?php  echo $order['num'];?></strong> 份,总价 <strong><?php  echo $order['price'];?></strong> 元】</div>
		<div class="panel-body table-responsive">
			<table class="table table-hover">
				<thead class="navbar-inner">
					<tr>
						<th>商品</th>
						<th>份数</th>
						<th class="text-right">小计(元)</th>
						<th class="text-right"></th>
					</tr>
					<?php  if(!empty($order['goods'])) { ?>
						<?php  if(is_array($order['goods'])) { foreach($order['goods'] as $or) { ?>
							<tr>
								<td><?php  echo $or['goods_title'];?>(<?php  echo $or['goods_category_title'];?>)</td>
								<td><?php  echo $or['goods_num'];?> 份</td>
								<td class="text-right">
									<?php  if(!empty($or['bargain_id'])) { ?>
										<span class="text-line-through">￥<?php  echo $or['goods_original_price'];?></span>
									<?php  } ?>
									￥<?php  echo $or['goods_price'];?>
								</td>
								<td class="text-right">
									<?php  if(!empty($or['bargain_id'])) { ?>
										<a class="btn btn-danger" target="_blank" href="<?php  echo $this->createWeburl('bargain', array('op' => 'post', 'id' => $or['bargain_id']));?>">特价活动</a>
									<?php  } ?>
									<a class="btn btn-success" target="_blank" href="<?php  echo $this->createWeburl('goods', array('op' => 'post', 'id' => $or['goods_id']));?>">商品信息</a>
								</td>
							</tr>
						<?php  } } ?>
					<?php  } ?>
				</thead>
			</table>
		</div>
	</div>
	<?php  if($order['is_comment'] == 1) { ?>
		<div class="panel panel-default">
			<div class="panel-heading">订单评价</div>
			<div class="panel-body table-responsive">
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-1 control-label">商品质量:</label>
					<div class="col-sm-9 col-xs-9 col-md-9">
						<p class="form-control-static">
							<?php 
								for($i = 0; $i < $comment['goods_quality']; $i++) {
									echo '<i class="fa fa-star"></i>';
								}
								for($i = $comment['goods_quality']; $i < 5; $i++) {
									echo '<i class="fa fa-star-o"></i>';
								}
							?>
						</p>
					</div>	
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-1 control-label">配送服务:</label>
					<div class="col-sm-9 col-xs-9 col-md-9">
						<p class="form-control-static">
							<?php 
								for($i = 0; $i < $comment['delivery_service']; $i++) {
									echo '<i class="fa fa-star"></i>';
								}
								for($i = $comment['delivery_service']; $i < 5; $i++) {
									echo '<i class="fa fa-star-o"></i>';
								}
							?>
						</p>
					</div>
				</div>
				<?php  if(!empty($comment['data']['good'])) { ?>
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-1 control-label"><i class="fa fa-thumbs-o-up"></i> 点赞商品:</label>
						<div class="col-sm-9 col-xs-9 col-md-9">
							<p class="form-control-static">
								<?php  if(is_array($comment['data']['good'])) { foreach($comment['data']['good'] as $good) { ?>
									<?php  echo $good;?> &nbsp;
								<?php  } } ?>
							</p>
						</div>	
					</div>
				<?php  } ?>
				<?php  if(!empty($comment['data']['bad'])) { ?>
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-1 control-label"><i class="fa fa-thumbs-o-down"></i> 差评菜品:</label>
						<div class="col-sm-9 col-xs-9 col-md-9">
							<p class="form-control-static">
								<?php  if(is_array($comment['data']['bad'])) { foreach($comment['data']['bad'] as $bad) { ?>
									<?php  echo $bad;?> &nbsp;
								<?php  } } ?>
							</p>
						</div>	
					</div>
				<?php  } ?>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-1 control-label">评价:</label>
					<div class="col-sm-9 col-xs-9 col-md-9">
						<p class="form-control-static"><?php  echo $comment['note'];?></p>
					</div>
				</div>
				<?php  if(!empty($comment['thumbs'])) { ?>
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-1 control-label">有图有真相:</label>
						<div class="col-sm-9 col-xs-9 col-md-11">
							<?php  if(is_array($comment['thumbs'])) { foreach($comment['thumbs'] as $thumb) { ?>
								<img src="<?php  echo tomedia($thumb);?>" alt="" class="img-thumbnail" style="width: 200px;"/>
							<?php  } } ?>
						</div>
					</div>
				<?php  } ?>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-1 control-label">审核状态:</label>
					<div class="col-sm-9 col-xs-9 col-md-9">
						<p class="form-control-static">
							<?php  if($comment['status'] == 1) { ?>
								<span class="label label-success">审核通过</span>
							<?php  } else if(!$comment['status']) { ?>
								<span class="label label-danger">审核未通过</span>
							<?php  } else { ?>
								<span class="label label-default">审核中</span>
							<?php  } ?>
						</p>
					</div>
				</div>
			</div>
		</div>
	<?php  } ?>
	<?php  if(!empty($logs)) { ?>
		<div class="panel panel-default">
			<div class="panel-heading">订单日志</div>
			<div class="panel-body table-responsive">
				<table class="table table-hover table-log">
					<?php  if(is_array($logs)) { foreach($logs as $log) { ?>
					<tr>
						<td>
							<p><i class="fa fa-info-circle"></i> <strong><?php  echo date('Y-m-d H:i', $log['addtime']);?> <?php  echo $log['title'];?></strong></p> 
							<p style="padding-left:15px; "><?php  echo $log['note'];?></p> 
						</td>
					</tr>
					<?php  } } ?>
				</table>
			</div>
		</div>
	<?php  } ?>
</form>
<?php  } ?>

<div class="modal fade" id="order-reply-remind-container" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog ">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">催单回复</h4>
			</div>
			<div class="modal-body">
				<div class="input-group">
					<input type="text" name="reply" class="form-control" placeholder="请填写/选择一个催单回复">
					<div class="input-group-btn">
						<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><span class="caret"></span></button>
						<ul class="dropdown-menu dropdown-menu-right" role="menu" style="width:500px">
							<?php  if(is_array($store_['remind_reply'])) { foreach($store_['remind_reply'] as $reply) { ?>
								<li><a href="javascript:;"><?php  echo $reply;?></a></li>
							<?php  } } ?>
						</ul>
					</div>
				</div>
				<span class="help-block">
					<a href="<?php  echo $this->createWebUrl('store', array('op' => 'post', 'id' => $sid, 'type' => 'remind'));?>" target="_blank"><i class="fa fa-plus-circle"></i> 添加催单回复</a>
				</span>
			</div>
			<div class="modal-footer text-center">
				<a class="btn btn-primary js-reply-submit">确定</a>&nbsp;&nbsp;<a class="btn btn-default js-reply-cancel">取消</a>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
$(function(){
	$('.order-reply-remind').click(function(){
		var id = $(this).data('id')
		$container = $('#order-reply-remind-container');
		$container.modal('show');
		$container.find('.dropdown-menu li').click(function(){
			var reply = $(this).text();
			$container.find(':text[name="reply"]').val(reply);
		});
		$container.find('.js-reply-submit').click(function(){
			var content = $container.find(':text[name="reply"]').val();
			if(!content) {
				util.message('请填写回复内容', '', 'info');
				return false;
			}
			$.post("<?php  echo $this->createWebUrl('order', array('op' => 'reply_remind'));?>", {id: id, content: content}, function(data){
				var data = $.parseJSON(data);
				if(data.message.errno != 0) {
					util.message(data.message.message, '', 'info');
					return false;
				} else {
					$container.modal('hide');
					location.reload();
				}
			});
		});
		$container.find('.js-reply-cancel').click(function(){
			$container.modal('hide');
		});
	});
});
</script>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('store/order-js', TEMPLATE_INCLUDEPATH)) : (include template('store/order-js', TEMPLATE_INCLUDEPATH));?>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('public/footer', TEMPLATE_INCLUDEPATH)) : (include template('public/footer', TEMPLATE_INCLUDEPATH));?>
