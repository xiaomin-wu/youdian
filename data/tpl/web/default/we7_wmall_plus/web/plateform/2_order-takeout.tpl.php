<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('public/header', TEMPLATE_INCLUDEPATH)) : (include template('public/header', TEMPLATE_INCLUDEPATH));?>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('plateform/order-nav', TEMPLATE_INCLUDEPATH)) : (include template('plateform/order-nav', TEMPLATE_INCLUDEPATH));?>
<?php  if($op == 'list') { ?>
<div class="main">
	<div class="panel panel-info">
		<div class="panel-heading clearfix">
			筛选
			<div class="pull-right">
				<label class="checkbox-inline btn-refresh" data-type="status_order_refresh"><input type="checkbox" value="1" <?php  if($_GPC['_status_order_refresh'] == 1) { ?>checked<?php  } ?>><span id="time-count"><span>20</span>秒</span>自动刷新</label>
				<label class="checkbox-inline btn-notice" data-type="status_order_notice"><input type="checkbox" value="1" <?php  if($_GPC['_status_order_notice'] == 1) { ?>checked<?php  } ?>>语音提示</label>
			</div>
		</div>
		<div class="panel-body">
			<form action="" class="form-horizontal search-container " id="order-takeout">
				<input type="hidden" name="c" value="site">
				<input type="hidden" name="a" value="entry">
				<input type="hidden" name="m" value="we7_wmall_plus">
				<input type="hidden" name="do" value="ptforder-takeout"/>
				<input type="hidden" name="op" value="list"/>
				<input type="hidden" name="fields" value=""/>
				<input type="hidden" name="status" value="<?php  echo $_GPC['status'];?>"/>
				<input type="hidden" name="is_pay" value="<?php  echo $_GPC['is_pay'];?>"/>
				<input type="hidden" name="refund_status" value="<?php  echo $re_status;?>"/>
				<div class="form-group clearfix">
					<label class="col-xs-12 col-sm-2 col-md-1 control-label">订单状态</label>
					<div class=" col-md-10 col-xs-12">
						<div class="btn-group">
							<a href="<?php  echo filter_url('status:0');?>" class="btn <?php  if($status == 0) { ?>btn-primary<?php  } else { ?>btn-default<?php  } ?>">所有订单</a>
							<a href="<?php  echo filter_url('status:1');?>" class="btn <?php  if($status == 1) { ?>btn-primary<?php  } else { ?>btn-default<?php  } ?>">未处理</a>
							<a href="<?php  echo filter_url('status:2');?>" class="btn <?php  if($status == 2) { ?>btn-primary<?php  } else { ?>btn-default<?php  } ?>">已确认</a>
							<a href="<?php  echo filter_url('status:3');?>" class="btn <?php  if($status == 3) { ?>btn-primary<?php  } else { ?>btn-default<?php  } ?>">待配送</a>
							<a href="<?php  echo filter_url('status:4');?>" class="btn <?php  if($status == 4) { ?>btn-primary<?php  } else { ?>btn-default<?php  } ?>">配送中</a>
							<a href="<?php  echo filter_url('status:5');?>" class="btn <?php  if($status == 5) { ?>btn-primary<?php  } else { ?>btn-default<?php  } ?>">已完成</a>
							<a href="<?php  echo filter_url('status:6');?>" class="btn <?php  if($status == 6) { ?>btn-primary<?php  } else { ?>btn-default<?php  } ?>">已取消</a>
						</div>
					</div>
				</div>
				<div class="form-group clearfix">
					<label class="col-xs-12 col-sm-2 col-md-1 control-label">退款订单</label>
					<div class="col-sm-10 col-lg-8 col-md-10 col-xs-12">
						<div class="btn-group">
							<a href="<?php  echo filter_url('refund_status:0');?>" class="btn <?php  if($re_status == 0) { ?>btn-primary<?php  } else { ?>btn-default<?php  } ?>">所有订单</a>
							<a href="<?php  echo filter_url('refund_status:1');?>" class="btn <?php  if($re_status == 1) { ?>btn-primary<?php  } else { ?>btn-default<?php  } ?>">待审核</a>
							<a href="<?php  echo filter_url('refund_status:2');?>" class="btn <?php  if($re_status == 2) { ?>btn-primary<?php  } else { ?>btn-default<?php  } ?>">退款中</a>
							<a href="<?php  echo filter_url('refund_status:3');?>" class="btn <?php  if($re_status == 3) { ?>btn-primary<?php  } else { ?>btn-default<?php  } ?>">退款成功</a>
							<a href="<?php  echo filter_url('refund_status:4');?>" class="btn <?php  if($re_status == 4) { ?>btn-primary<?php  } else { ?>btn-default<?php  } ?>">退款失败</a>
						</div>
					</div>
				</div>
				<div class="form-group clearfix">
					<label class="col-xs-12 col-sm-2 col-md-1 control-label">其他搜索</label>
					<div class=" col-md-10 col-xs-12">
						<div class="input-group">
							<select name="sid" class="form-control">
								<option value="0" <?php  if($sid == 0) { ?>select<?php  } ?>>所属门店</option>
								<?php  if(is_array($stores)) { foreach($stores as $store) { ?>
								<option value="<?php  echo $store['id'];?>" <?php  if($sid == $store['id']) { ?>selected<?php  } ?>><?php  echo $store['title'];?></option>
								<?php  } } ?>
							</select>
							<select name="is_pay" class="form-control">
								<option value="-1" <?php  if($is_pay == -1) { ?>selected<?php  } ?>>支付状态</option>
								<option value="1" <?php  if($is_pay == 1) { ?>selected<?php  } ?>>已支付</option>
								<option value="0" <?php  if($is_pay == 0) { ?>selected<?php  } ?>>未支付</option>
							</select>
							<span class="input-group-btn border-no-radius">
								<?php  echo tpl_form_field_daterange('addtime', array('start' => date('Y-m-d', $starttime), 'end' => date('Y-m-d', $endtime)));?>
							</span>
							<input type="text" name="keyword" value="<?php  echo $keyword;?>" class="form-control" placeholder="输入用户名/手机号/订单编号">
							<span class="input-group-btn">
								<button class="btn btn-success"><i class="fa fa-search"></i> 搜 索</button>
								<a class="btn btn-primary btn-export" href="javascript:;"><i class="fa fa-download"></i> 导出订单</a>
							</span>
						</div>
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
	<form class="form-horizontal" style="margin-top: 20px;" action="<?php  echo $this->createWebUrl('order', array('op' => 'status'));?>" id="form-order" method="post">
		<div class="panel panel-default">
			<div class="panel-body table-responsive" style="overflow:inherit">
				<table class="table table-hover">
					<thead class="navbar-inner">
						<tr>
							<th width="120">订单ID</th>
							<th>门店</th>
							<th>预定人/电话</th>
							<th>订单类型</th>
							<th>支付方式</th>
							<th>订单状态</th>
							<th>份数/总价</th>
							<th>优惠金额</th>
							<th>优惠后价格</th>
							<th class="text-right">下单时间</th>
						</tr>
					</thead>
					<tbody>
						<?php  if(is_array($data)) { foreach($data as $dca) { ?>
						<tr>
							<td>
								<span class="label label-warning">商户号:#<?php  echo $dca['serial_sn'];?></span>
								<br>
								<span class="label label-info label-br">平台号:<?php  echo $dca['id'];?></span>
							</td>
							<td><b><?php  echo $stores[$dca['sid']]['title'];?></b></td>
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
									<span class="label label-danger label-br"><?php  echo $refund_status[$dca['refund_status']]['text'];?></span>
								<?php  } ?>
								<?php  if($dca['deliveryer_id'] > 0) { ?>
									<br>
									<span class="label label-info label-br">配送员: <?php  echo $deliveryers[$dca['deliveryer_id']]['title'];?></span>
								<?php  } ?>
							</td>
							<td>
								<?php  echo $dca['num'];?>份/<?php  echo $dca['total_fee'];?>元
							</td>
							<td><?php  echo $dca['discount_fee'];?>元</td>
							<td><span class="label label-info"><?php  echo $dca['final_fee'];?>元</span></td>
							<td class="text-right"><?php  echo date('Y-m-d H:i', $dca['addtime'])?></td>
						</tr>
						<tr class="no-border">
							<td colspan="10" class="text-right">
								<?php  if($dca['status'] < 5) { ?>
									<?php  if($dca['status'] == 1) { ?>
										<a href="javascript:;" class="btn btn-default order-status" data-type="handle" data-id="<?php  echo $dca['id'];?>">接受订单</a>
										<a href="javascript:;" class="btn btn-default order-status" data-type="notify_clerk_handle" data-id="<?php  echo $dca['id'];?>">通知商户接单</a>
									<?php  } ?>
									<?php  if($dca['order_type'] == 1) { ?>
										<?php  if($dca['status'] == 2 || $dca['status'] == 3) { ?>
											<a href="javascript:;" class="btn btn-default order-status" data-type="notify_deliveryer_collect" data-id="<?php  echo $dca['id'];?>">通知配送员抢单</a>
											<?php  if($dca['delivery_type'] == 2) { ?>
												<a href="javascript:;" class="btn btn-default btn-dispatch" data-id="<?php  echo $dca['id'];?>">调度</a>
											<?php  } ?>
										<?php  } ?>
										<?php  if($dca['status'] == 4) { ?>
											<a href="javascript:;" class="btn btn-default btn-dispatch" data-id="<?php  echo $dca['id'];?>">重新调度</a>
											<a href="javascript:;" class="btn btn-default order-status" data-type="end" data-id="<?php  echo $dca['id'];?>">完成订单</a>
										<?php  } ?>
									<?php  } else if($dca['order_type'] == 2) { ?>
										<a href="javascript:;" class="btn btn-default order-status" data-type="end" data-id="<?php  echo $dca['id'];?>">完成订单</a>
									<?php  } ?>
									<?php  if($dca['is_pay'] == 1 && $dca['pay_type'] != 'delivery') { ?>
										<a href="javascript:;" class="btn btn-default order-cancel" data-refund="1" data-id="<?php  echo $dca['id'];?>">取消订单并退款</a>
									<?php  } else { ?>
										<a href="javascript:;" class="btn btn-default order-cancel" data-refund="0" data-id="<?php  echo $dca['id'];?>">取消订单</a>
									<?php  } ?>
								<?php  } ?>
								<?php  if($dca['refund_status'] == 1) { ?>
									<a href="javascript:;" class="btn btn-default order-refund-handle" data-id="<?php  echo $dca['id'];?>">发起退款</a>
								<?php  } else if($dca['refund_status'] == 2) { ?>
									<a href="javascript:;" class="btn btn-default order-refund-query" data-id="<?php  echo $dca['id'];?>">查询退款进度</a>
								<?php  } ?>
								<a href="<?php  echo $this->createWebUrl('ptforder-takeout', array('op' => 'detail', 'id' => $dca['id']));?>" target="_blank" class="btn btn-default">详情</a>
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

<div class="modal fade" id="order-export" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog ">
		<form action="">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">导出订单</h4>
				</div>
				<div class="modal-body">
					<div class="form-group">
						<label>附加会员字段</label>
						<br/>
						<?php  if(is_array($fields)) { foreach($fields as $key => $field) { ?>
						<label class="checkbox-inline">
							<input type="checkbox" name="fields[]" value="<?php  echo $key;?>"> <?php  echo $field;?>
						</label>
						<?php  } } ?>
					</div>
				</div>
				<div class="modal-footer text-center">
					<input type="hidden" name="token" value="<?php  echo $_W['token'];?>">
					<a class="btn btn-default" data-dismiss="modal" aria-label="Close">取消</a>
					<a class="btn btn-primary btn-export-submit" href="javascript:;">确定导出</a>
				</div>
			</div>
		</form>
	</div>
</div>

<div class="modal fade" id="order-dispatch">
	<div class="modal-dialog" style="width: 85%;">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h3 class="modal-title">订单调度</h3>
			</div>
			<div class="modal-body" style="min-height: 530px">
				<form action="">
					<div class="col-lg-9">
						<div id="allmap" style="height: 500px">
						</div>
					</div>
					<div class="col-lg-3 table-responsive">
						<table class="table table-hover table-bordered">
							<thead>
							<th width="100"></th>
							<th>配送员</th>
							<th>操作</th>
							</thead>
							<tbody class="deliveryer-list">
							</tbody>
						</table>
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
			</div>
		</div>
	</div>
</div>

<script id="tpl-deliveryer-list" type="text/html">
	<{# for(var i = 0, len = d.length; i < len; i++){ }>
	<tr>
		<td>
			<img src="<{d[i]['deliveryer'].avatar}>" class="thumbnail" alt=""/>
		</td>
		<td>
			<strong><{d[i]['deliveryer'].title}></strong>
			<br/>
			外卖单:<{d[i]['deliveryer'].order_takeout_num}>
			<br/>
			跑腿单:<{d[i]['deliveryer'].order_errander_num}>
		</td>
		<td>
			<a href="javascript:;" data-deliveryer-id="<{d[i]['deliveryer'].id}>" data-order-id="<{d[i].order_id}>" class="btn btn-primary btn-dispatch-submit">分配</a>
		</td>
	</tr>
	<tr>
		<td colspan="3">配送员-<strong class="text-danger"><{d[i].store2deliveryer_distance}></strong>-门店-<strong class="text-danger"><{d[i].store2user_distance}></strong>-收货人</td>
	</tr>
	<{# } }>
</script>
<script type="text/javascript" src="http://webapi.amap.com/maps?v=1.3&key=550a3bf0cb6d96c3b43d330fb7d86950&plugin=AMap.Driving,AMap.Geocoder"></script>
<script>
	var config = <?php  echo json_encode($_W['we7_wmall_plus']['config']['takeout']);?>;
	var map = new AMap.Map('allmap', {
		resizeEnable: true,
		zoom: 13,
		center: [config.map.location_y, config.map.location_x]
	});
	var driving = new AMap.Driving({
		policy:AMap.DrivingPolicy.LEAST_TIME,
		map: map
	});
	require(['trade', 'bootstrap'], function(trade){
		trade.init();
		$('.btn-refresh, .btn-notice').click(function(){
			var type = $(this).data('type');
			var value = $(this).find(':checkbox').prop('checked') ? 0 : 1;
			$.post(location.href, {type: type, value: value}, function(){
				location.reload();
			});
			return false;
		});
		<?php  if($_GPC['_status_order_refresh'] == 1) { ?>
			setInterval(function(){
				var time = parseInt($('#time-count span').html());
				if(time > 1) {
					time--;
					var html = '<span>' + time + '</span>'  + '秒后';
					$('#time-count').html(html);
				} else {
					location.reload();
				}
			}, 1000);
		<?php  } ?>
		$('.order-status').click(function(){
			var id = $(this).data('id');
			var type = $(this).data('type');
			var tips = {
				handle: '确定接单吗?',
				notify_deliveryer_collect: '确定通知配送员抢单吗?',
				notify_clerk_handle: '确定通知商户接单吗?',
				end: '确定该订单已完成?',
				cancel: '确定取消该订单吗?'
			}
			var info = tips[type];
			tiny.confirm($(this), info, function(){
				util.loading();
				$.post("<?php  echo $this->createWebUrl('ptforder-takeout', array('op' => 'status'))?>", {id: id, type: type}, function(data){
					util.loaded();
					var result = $.parseJSON(data);
					if(result.message.errno == -1) {
						util.message(result.message.message, '', 'error');
						return false;
					}
					util.message(result.message.message, location.href, 'success');
				});
			});
		});

		$('.order-cancel').click(function(){
			var id = $(this).data('id');
			var refund = $(this).data('refund');
			var info = '确定取消该订单?';
			if(refund == 1) {
				info = '确定取消该订单并发起退款?';
			}
			tiny.confirm($(this), info, function(){
				util.loading();
				$.post("<?php  echo $this->createWebUrl('ptforder-takeout', array('op' => 'cancel'))?>", {id: id}, function(data){
					util.loaded();
					var result = $.parseJSON(data);
					if(result.message.errno == -1) {
						util.message(result.message.message, '', 'error');
						return false;
					}
					util.message(result.message.message, location.href, 'success');
				});
			});
		});

		$('.order-refund-handle').click(function(){
			var id = $(this).data('id');
			tiny.confirm($(this), '确定发起退款吗', function(){
				$.post("<?php  echo $this->createWebUrl('ptforder-takeout', array('op' => 'refund_handle'))?>", {id: id}, function(data){
					var result = $.parseJSON(data);
					if(result.message.errno == -1) {
						util.message(result.message.message, '', 'error');
						return false;
					}
					util.message(result.message.message, location.href, 'success');
				});
			});
		});

		$('.order-refund-query').click(function(){
			var id = $(this).data('id');
			$.post("<?php  echo $this->createWebUrl('ptforder-takeout', array('op' => 'refund_query'))?>", {id: id}, function(data){
				var result = $.parseJSON(data);
				if(result.message.errno == -1) {
					util.message(result.message.message, '', 'error');
					return false;
				}
				util.message(result.message.message, location.href, 'success');
			});
		});

		$('.btn-export').click(function(){
			$('#order-export').modal('show');
			$('.btn-export-submit').click(function(){
				var fields = [];
				$(':checkbox[name="fields[]"]:checked').each(function(){
					if($(this).val()) {
						fields.push($(this).val());
					}
				});
				fields = fields.join('|');
				$('#order-takeout input[name="fields"]').val(fields);
				$('#order-takeout input[name="op"]').val('export');
				$('#order-takeout').submit();
				$('#order-takeout input[name="op"]').val('list');
				$('#order-export').modal('hide');
			});
		});

		$(document).on('click', '.btn-dispatch', function(){
			var id = $(this).data('id');
			$.post("<?php  echo $this->createWebUrl('ptforder-takeout', array('op' => 'analyse'))?>", {id: id}, function(data){
				var result = $.parseJSON(data);
				if(result.message.errno != 0) {
					util.message(result.message.message);
					return false;
				}
				var order = result.message.message;
				var gettpl = $('#tpl-deliveryer-list').html();
				laytpl(gettpl).render(order.deliveryers, function(html){
					$('#order-dispatch').find('.deliveryer-list').html(html);
				});
				if(order.location_y && order.location_x) {
					driving.search(new AMap.LngLat(order['store'].location_y, order['store'].location_x), new AMap.LngLat(order.location_y, order.location_x));
				} else {
					marker = new AMap.Marker({
						position: [order.store.location_y, order.store.location_x],
						offset: new AMap.Pixel(-27, -74),
						content: '<div class="marker-start-route"></div>'
					});
					marker.setMap(map);

					var geocoder = new AMap.Geocoder({
						city: config.city
					});
					geocoder.getLocation(order.address, function(status, result) {
						if (status === 'complete' && result.info === 'OK') {
							var position = result.geocodes[0].location;
							if(position) {
								marker = new AMap.Marker({
									position: [position.lng, position.lat],
									offset: new AMap.Pixel(-27, -74),
									content: '<div class="marker-end-route"></div>'
								});
								marker.setMap(map);
							}
						}
					});
					map.setFitView();
				}
				$.each(order.deliveryers, function(k, v){
					var deliveryer = v.deliveryer;
					if(deliveryer.location_x && deliveryer.location_y) {
						marker = new AMap.Marker({
							position: [deliveryer.location_y, deliveryer.location_x],
							offset: new AMap.Pixel(-26, -80),
							content: '<div class="marker-deliveyer-route"><img src="'+ v.deliveryer.avatar +'" alt=""/></div>'
						});
						marker.setMap(map);
					}
				});
				$('#order-dispatch').modal('show');
			});
		});

		$(document).on('click', '.btn-dispatch-submit', function(){
			var order_id = $(this).data('order-id');
			var deliveryer_id = $(this).data('deliveryer-id');
			if(!order_id || !deliveryer_id) {
				return false;
			}
			util.loading();
			$.post("<?php  echo $this->createWebUrl('ptforder-takeout', array('op' => 'dispatch'))?>", {order_id: order_id, deliveryer_id: deliveryer_id}, function(data){
				var result = $.parseJSON(data);
				util.loaded();
				if(result.message.errno != 0) {
					util.message(result.message.message);
					return false;
				} else {
					location.reload();
				}
				$('#order-dispatch').modal('hide');
			});
		});

		$(document).on('click', '.item-deliveryer', function(){
			var deliveryer = $(this).data('info');
			if(!deliveryer) {
				util.message('配送员信息错误');
				return false;
			}
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
						<th>小计(元)</th>
						<th></th>
					</tr>
					<?php  if(!empty($order['goods'])) { ?>
						<?php  if(is_array($order['goods'])) { foreach($order['goods'] as $or) { ?>
							<tr>
								<td><?php  echo $or['goods_title'];?></td>
								<td><?php  echo $or['goods_num'];?> 份</td>
								<td><?php  echo $or['goods_price'];?> 元</td>
								<td>
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
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('public/footer', TEMPLATE_INCLUDEPATH)) : (include template('public/footer', TEMPLATE_INCLUDEPATH));?>
