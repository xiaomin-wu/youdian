<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('public/header', TEMPLATE_INCLUDEPATH)) : (include template('public/header', TEMPLATE_INCLUDEPATH));?>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('plateform/trade-nav', TEMPLATE_INCLUDEPATH)) : (include template('plateform/trade-nav', TEMPLATE_INCLUDEPATH));?>
<?php  if($op == 'account') { ?>
<div class="clearfix">
	<div class="panel panel-info">
		<div class="panel-heading">筛选</div>
		<div class="panel-body">
			<form action="" class="form-inline search-container pull-left">
				<input type="hidden" name="c" value="site">
				<input type="hidden" name="a" value="entry">
				<input type="hidden" name="m" value="we7_wmall_plus">
				<input type="hidden" name="do" value="ptftrade"/>
				<input type="hidden" name="op" value="account"/>
				<div class="input-group">
					<input type="text" name="keyword" value="<?php  echo $keyword;?>" class="form-control" placeholder="输入商户名称">
					<span class="input-group-btn">
						<button class="btn btn-success"><i class="fa fa-search"></i> 搜 索</button>
					</span>
				</div>
			</form>
		</div>
	</div>
	<form class="form-horizontal" action="" method="post" id="form-account">
		<div class="panel panel-default">
			<div class="panel-body table-responsive">
				<table class="table table-hover">
					<thead class="navbar-inner">
					<tr>
						<th>门店</th>
						<th>账户余额</th>
						<th>配送模式</th>
						<th>提现费率</th>
						<th>最低提现</th>
						<th>手续费最低</th>
						<th>手续费最高</th>
						<th style="width:250px; text-align:right;">操作</th>
					</tr>
					</thead>
					<tbody>
					<?php  if(is_array($accounts)) { foreach($accounts as $account) { ?>
					<tr>
						<td>
							<label>
								<input type="checkbox" name="sid[]" value="<?php  echo $account['sid'];?>">
								&nbsp;
								<?php  echo $stores[$account['sid']]['title'];?>
							</label>
						</td>
						<td>
							<span class="label label-warning"><?php  echo $account['amount'];?></span>
						</td>
						<td>
							<span class="<?php  echo $delivery_modes[$account['delivery_mode']]['css'];?>">
								<?php  echo $delivery_modes[$account['delivery_mode']]['text'];?>
							</span>
							<?php  if($account['delivery_mode'] == 2) { ?>
								<br/>
								<?php  if($account['delivery_fee_mode'] == 1) { ?>
									<span class="label label-success label-br">固定配送费: <?php  echo $account['delivery_price'];?>元</span>
								<?php  } else { ?>
									<span class="label label-danger label-br">按距离收取配送费</span>
									<br/>
									<span class="label label-info label-br"><?php  echo $account['delivery_price']['start_fee'];?>元包含<?php  echo $account['delivery_price']['start_km'];?>公里</span>
									<br/>
									<span class="label label-info label-br">超过<?php  echo $account['delivery_price']['start_km'];?>公里每公里加<?php  echo $account['delivery_price']['pre_km_fee'];?>元</span>
								<?php  } ?>
							<?php  } ?>
						</td>
						<td><?php  echo $account['fee_rate'];?>%</td>
						<td><?php  echo $account['fee_limit'];?>元</td>
						<td><?php  echo $account['fee_min'];?>元</td>
						<td><?php  echo $account['fee_max'];?>元</td>
						<td style="text-align:right;">
							<a href="javascript:;" class="btn btn-danger btn-sm account-turncate-item" data-type="single" data-sid="<?php  echo $account['sid'];?>">账户清零</a>
							<a href="<?php  echo $this->createWebUrl('ptftrade', array('op' => 'set', 'id' => $account['sid']))?>" class="btn btn-default btn-sm" title="账户设置" data-toggle="tooltip" data-placement="top">账户设置</a>
							<a href="<?php  echo $this->createWebUrl('trade', array('op' => 'inout', '_sid' => $account['sid']))?>" class="btn btn-default btn-sm" title="账户明细" data-toggle="tooltip" data-placement="top" target="_blank">账户明细</a>
						</td>
					</tr>
					<?php  } } ?>
					<tr>
						<td colspan="8">
							<input type="checkbox" id="select-all" data-scope="#form-account">
							&nbsp;
							<a href="javascript:;" data-type="mutil" class="btn btn-danger account-turncate-item">账户清零</a>
						</td>
					</tr>
					</tbody>
				</table>
			</div>
		</div>
		<?php  echo $pager;?>
	</form>
</div>

<div class="modal fade" id="modal-account-turncate">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">账户清零原因</h4>
			</div>
			<div class="modal-body">
				<form action="">
					<div class="form-group">
						<textarea class="form-control" name="remark" placeholder="请输入账户清零原因" rows="7"></textarea>
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
				<button type="button" class="btn btn-primary account-turncate-submit">提交</button>
			</div>
		</div>
	</div>
</div>

<script>
$(function(){
	$(document).on('click', '.account-turncate-item', function(){
		var $this = $(this);
		var type = $this.data('type');
		var ids = [];
		if(type == 'single') {
			var sid = $(this).data('sid');
			ids.push(sid);
		} else {
			$('#form-account :checkbox[name="sid[]"]:checked').each(function(){
				var id = $(this).val();
				if(id) {
					ids.push(id);
				}
			});
		}
		if(ids.length == 0) {
			util.message('请选择要操作的账户', '', 'error');
			return false;
		}

		$('#modal-account-turncate').modal('show');
		$(document).on('click', '.account-turncate-submit', function(){
			var remark = $.trim($('#modal-account-turncate textarea[name="remark"]').val());
			if(!remark) {
				util.message('账户清零原因不能为空', '', 'error');
				return false;
			}
			$.post("<?php  echo $this->createWebUrl('ptftrade', array('op' => 'account_turncate'));?>", {ids: ids, remark: remark}, function(data){
				var result = $.parseJSON(data);
				if(result.message.errno != 0) {
					util.message(util.message.message, '', 'error');
				} else {
					$('#modal-account-turncate').modal('hide');
					util.message('清空账户余额成功', location.href, 'success');
				}
			});
		});
	});
});
</script>
<?php  } ?>

<?php  if($op == 'set') { ?>
<div class="clearfix">
	<form class="form-horizontal form" action="" method="post">
		<div class="panel panel-default panel-config-store">
			<div class="panel-heading">配送设置</div>
			<div class="panel-body">
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="require">*</span>当前门店</label>
					<div class="col-sm-9 col-xs-12">
						<p class="form-control-static"><strong><?php  echo $store['title'];?></strong></p>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="require">*</span>配送员模式</label>
					<div class="col-sm-9 col-xs-12">
						<label class="radio-inline">
							<input type="radio" value="1" name="delivery_mode" <?php  if($store['delivery_mode'] == 1 || !$store['delivery_mode']) { ?>checked<?php  } ?> onclick="$('.delivery-mode-2').hide()"> 店内配送员
						</label>
						<label class="radio-inline">
							<input type="radio" value="2" name="delivery_mode" <?php  if($store['delivery_mode'] == 2) { ?>checked<?php  } ?> onclick="$('.delivery-mode-2').show()"> 平台配送员
						</label>
					</div>
				</div>
				<div class="delivery-mode-2" <?php  if($store['delivery_mode'] != 2) { ?>style="display:none"<?php  } ?>>
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="require">* </span>起送价</label>
						<div class="col-sm-9 col-xs-9 col-md-9">
							<div class="input-group">
								<input type="text" class="form-control" name="send_price" value="<?php  echo $store['send_price'];?>">
								<span class="input-group-addon">元</span>
							</div>
						</div>
					</div>
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="require">* </span>满多少元免配送费</label>
						<div class="col-sm-9 col-xs-9 col-md-9">
							<div class="input-group">
								<input type="text" class="form-control" name="delivery_free_price" value="<?php  echo $store['delivery_free_price'];?>">
								<span class="input-group-addon">元</span>
							</div>
							<div class="help-block">当用户订单满**元,不收取配送费</div>
						</div>
					</div>
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="require">* </span>预计送达时间</label>
						<div class="col-sm-9 col-xs-9 col-md-9">
							<div class="input-group">
								<input type="text" class="form-control" name="delivery_time" value="<?php  echo $store['delivery_time'];?>">
								<span class="input-group-addon">分钟</span>
							</div>
						</div>
					</div>
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="require">* </span>服务半径</label>
						<div class="col-sm-9 col-xs-9 col-md-9">
							<div class="input-group">
								<input type="text" class="form-control" name="serve_radius" value="<?php  echo $store['serve_radius'];?>">
								<span class="input-group-addon">KM</span>
							</div>
						</div>
					</div>
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="require">* </span>收货地址是否自动获取</label>
						<div class="col-sm-9 col-xs-9 col-md-9">
							<label class="radio-inline"><input type="radio" name="auto_get_address" value="1" <?php  if($store['auto_get_address'] == 1) { ?>checked<?php  } ?>> 是, 高德地图自动获取</label>
							<label class="radio-inline"><input type="radio" name="auto_get_address" value="0" <?php  if(!$store['auto_get_address']) { ?>checked<?php  } ?>> 否, 用户自己填写</label>
							<span class="help-block">设置为用户自己填写后, 将不能获取用户的具体位置, 不能实现超出服务范围禁制下单的功能</span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">在配送半径之外是否允许下单</label>
						<div class="col-sm-9 col-xs-9 col-md-9">
							<label class="radio-inline">
								<input type="radio" name="not_in_serve_radius" value="1" <?php  if($store['not_in_serve_radius'] == 1) { ?>checked<?php  } ?>> 允许
							</label>
							<label class="radio-inline">
								<input type="radio" name="not_in_serve_radius" value="0" <?php  if(!$store['not_in_serve_radius']) { ?>checked<?php  } ?>> 不允许
							</label>
							<div class="help-block"><span class="text-danger">距离大于配送半径时是否允许下单，注意：手机定位精确性受天气、用户终端设备是否开启GPS以及硬件配置等影响很大，若此项设置为不允许下单，可能会导致部分用户无法成功下单.</span></div>
						</div>
					</div>
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="require">*</span>配送费</label>
						<div class="col-sm-9 col-xs-12">
							<div class="btn-group" data-toggle="buttons">
								<label class="btn btn-default <?php  if($store['delivery_fee_mode'] == 1 || !$store['delivery_fee_mode']) { ?>active<?php  } ?>" onclick="$('.delivery-fee-mode').hide();$('.delivery-fee-mode-1').show();">
									<input type="radio" name="delivery_fee_mode" value="1" <?php  if($store['delivery_fee_mode'] == 1 || !$store['delivery_fee_mode']) { ?>checked<?php  } ?>> 固定金额
								</label>
								<label class="btn btn-default <?php  if($store['delivery_fee_mode'] == 2) { ?>active<?php  } ?>" onclick="$('.delivery-fee-mode').hide();$('.delivery-fee-mode-2').show();">
									<input type="radio" name="delivery_fee_mode" value="2" <?php  if($store['delivery_fee_mode'] == 2) { ?>checked<?php  } ?>> 按距离收取
								</label>
							</div>
						</div>
					</div>
					<div class="form-group delivery-fee-mode delivery-fee-mode-1" <?php  if($store['delivery_fee_mode'] == 1 || !$store['delivery_fee_mode']) { ?>style="display: block"<?php  } ?>>
						<label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
						<div class="col-sm-9 col-xs-12">
							<div class="input-group">
								<div class="input-group-addon">每单</div>
								<input type="text" name="delivery_price" value="<?php  echo $store['delivery_price'];?>" class="form-control"/>
								<div class="input-group-addon">元</div>
							</div>
						</div>
					</div>
					<div class="form-group delivery-fee-mode delivery-fee-mode-2" <?php  if($store['delivery_fee_mode'] == 2) { ?>style="display: block"<?php  } ?>>
						<label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
						<div class="col-sm-9 col-xs-12">
							<div class="input-group">
								<span class="input-group-addon">起步价</span>
								<input type="text" class="form-control" name="start_fee" value="<?php  echo $store['delivery_price_extra']['start_fee'];?>">
								<span class="input-group-addon">元包含</span>
								<input type="text" class="form-control" name="start_km" value="<?php  echo $store['delivery_price_extra']['start_km'];?>">
								<span class="input-group-addon">公里</span>
							</div>
							<br>
							<div class="input-group">
								<span class="input-group-addon">每增加1公里加</span>
								<input type="text" class="form-control" name="pre_km_fee" value="<?php  echo $store['delivery_price_extra']['pre_km_fee'];?>">
								<span class="input-group-addon">元</span>
							</div>
							<div class="help-block">
								<strong class="text-danger">
									特别注意: 设置按照"按距离收取"配送费后,系统会自动变更使用"平台配送"模式商家的某些配置。包括:收货地址被设置为自动获取, 超过配送范围是仍可下单
								</strong>
								<br/>
							</div>
						</div>
					</div>
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="require">*</span>配送时间段</label>
						<div class="col-sm-9 col-xs-12">
							<div class="input-group">
								<span class="input-group-addon">间隔</span>
								<input type="text" class="form-control" name="pre_delivery_time_minute" value="<?php  echo $account_takeout['pre_delivery_time_minute'];?>">
								<span class="input-group-addon">分钟</span>
								<div class="input-group-btn btn-build-delivery-time">
									<a href="javascript:;" class="btn btn-primary" >生成配送时间段</a>
								</div>
							</div>
						</div>
					</div>
					<div id="delivery-times" class="delivery-times">
						<div class="form-group">
							<label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
							<div class="col-sm-9 col-xs-12 containter">
								<?php  if(is_array($store['delivery_times'])) { foreach($store['delivery_times'] as $time) { ?>
									<div class="col-sm-6">
										<div class="input-group">
											<span class="input-group-addon"><?php  echo $time['start'];?> ~ <?php  echo $time['end'];?></span>
											<span class="input-group-addon">增加附加费</span>
											<input type="text" class="form-control" name="times[fee][]" value="<?php  echo $time['fee'];?>" placeholder="增加附加费">
											<input type="hidden" name="times[start][]" value="<?php  echo $time['start'];?>"/>
											<input type="hidden" name="times[end][]" value="<?php  echo $time['end'];?>"/>
											<input type="hidden" name="times[status][]" value="<?php  echo $time['status'];?>">
											<span class="input-group-addon">元</span>
											<div class="input-group-btn">
												<a href="javascript:;" class="btn btn-delivery-time-item <?php  if($time['status'] == 1) { ?>btn-success<?php  } else { ?>btn-default<?php  } ?>"><?php  if($time['status'] == 1) { ?>使用中<?php  } else { ?>已弃用<?php  } ?></a>
											</div>
										</div>
									</div>
								<?php  } } ?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="panel panel-default">
			<div class="panel-heading">提现设置</div>
			<div class="panel-body">
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="require">*</span>外卖单服务费</label>
					<div class="col-sm-9 col-xs-12">
						<div class="input-group">
							<input type="text" name="takeout_serve_rate" value="<?php  echo $account['takeout_serve_rate'];?>" class="form-control"/>
							<span class="input-group-addon">%</span>
						</div>
						<div class="help-block">只能填写整数，不填写为不收取手续费.</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="require">*</span>店内单服务费</label>
					<div class="col-sm-9 col-xs-12">
						<div class="input-group">
							<input type="text" name="instore_serve_rate" value="<?php  echo $account['instore_serve_rate'];?>" class="form-control"/>
							<span class="input-group-addon">%</span>
						</div>
						<div class="help-block">只能填写整数，不填写为不收取手续费.</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="require">*</span>最低提现金额</label>
					<div class="col-sm-9 col-xs-12">
						<div class="input-group">
							<input type="text" name="get_cash_fee_limit" value="<?php  echo $account['fee_limit'];?>" class="form-control"/>
							<span class="input-group-addon">元</span>
						</div>
						<div class="help-block">只能填写整数，最低为1元(因为微信企业付款接口支持的最低付款金额为1元)</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="require">*</span>提现费率</label>
					<div class="col-sm-9 col-xs-12">
						<div class="input-group">
							<input type="text" name="get_cash_fee_rate" value="<?php  echo $account['fee_rate'];?>" class="form-control"/>
							<span class="input-group-addon">%</span>
						</div>
						<div class="help-block">
							商户申请提现时，每笔申请提现扣除的费用，默认为空，即提现不扣费，支持填写小数
							<br>
							<strong clas="text-danger">商户入驻时的默认提现费率</strong>
						</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="require">*</span>提现费用</label>
					<div class="col-sm-9 col-xs-12">
						<div class="input-group">
							<span class="input-group-addon">最低</span>
							<input type="text" name="get_cash_fee_min" value="<?php  echo $account['fee_min'];?>" class="form-control"/>
							<span class="input-group-addon">元</span>
						</div>
						<br>
						<div class="input-group">
							<span class="input-group-addon">最高</span>
							<input type="text" name="get_cash_fee_max" value="<?php  echo $account['fee_max'];?>" class="form-control"/>
							<span class="input-group-addon">元</span>
						</div>
						<div class="help-block">
							<strong class="text-danger">最高金额设置为0， 表示不限制最高提现费用。</strong>
							<br>
							商户提现时，提现费用的上下限，最高为空时，表示不限制扣除的提现费用
							<br>
							例如：提现100元，费率5%，最低1元，最高2元，商户最终提现金额=100-2=98
							<br>
							例如：提现100元，费率5%，最低1元，最高10元，商户最终提现金额=100-100*5%=95
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
	</form>
</div>
<script id="tpl-delivery-time" type="text/html">
	<{# for(var i = 0, len = d.length; i < len; i++){ }>
	<div class="col-sm-6">
		<div class="input-group">
			<span class="input-group-addon"><{d[i].start}> ~ <{d[i].end}></span>
			<span class="input-group-addon">增加附加费</span>
			<input type="text" class="form-control" name="times[fee][]" value="<{d[i].fee}>" placeholder="增加附加费">
			<input type="hidden" name="times[start][]" value="<{d[i].start}>"/>
			<input type="hidden" name="times[end][]" value="<{d[i].end}>"/>
			<input type="hidden" name="times[status][]" value="1">
			<span class="input-group-addon">元</span>
			<div class="input-group-btn">
				<a href="javascript:;" class="btn btn-success btn-delivery-time-item">使用中</a>
			</div>
		</div>
	</div>
	<{# } }>
</script>
<script>
$(function(){
	$(document).on('click', '.btn-build-delivery-time', function(){
		tiny.confirm($(this), '确定重新生成配送时间段吗?', function(){
			var pre_minute = parseInt($.trim($(':text[name="pre_delivery_time_minute"]').val()));
			if(isNaN(pre_minute)) {
				util.message('时间间隔只能是整数');
				return false;
			}
			if(!pre_minute) {
				util.message('时间间隔必须大于0');
				return false;
			}
			$.post("<?php  echo $this->createWebUrl('cmnutility', array('op' => 'build_time'));?>", {pre_minute: pre_minute}, function(data) {
				var result = $.parseJSON(data);
				if(result.message.errno == -1) {
					util.message(result.message.message);
					return false;
				}
				var gettpl = $('#tpl-delivery-time').html();
				laytpl(gettpl).render(result.message.message, function(html){
					$('#delivery-times .containter').html(html);
				});
			});
		});
	});

	$(document).on('click', '.btn-delivery-time-item', function(){
		if($(this).hasClass('btn-success')) {
			$(this).parent().prev().prev().val(0);
			$(this).removeClass('btn-success').addClass('btn-default');
			$(this).html('已弃用');
		} else {
			$(this).parent().prev().prev().val(1);
			$(this).removeClass('btn-default').addClass('btn-success');
			$(this).html('使用中');
		}
	});
});
</script>
<?php  } ?>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('public/footer', TEMPLATE_INCLUDEPATH)) : (include template('public/footer', TEMPLATE_INCLUDEPATH));?>