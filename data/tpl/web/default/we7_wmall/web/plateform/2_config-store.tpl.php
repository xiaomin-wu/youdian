<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('public/header', TEMPLATE_INCLUDEPATH)) : (include template('public/header', TEMPLATE_INCLUDEPATH));?>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('plateform/nav', TEMPLATE_INCLUDEPATH)) : (include template('plateform/nav', TEMPLATE_INCLUDEPATH));?>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('plateform/config-nav', TEMPLATE_INCLUDEPATH)) : (include template('plateform/config-nav', TEMPLATE_INCLUDEPATH));?>
<form class="form-horizontal form" id="form1" action="" method="post" enctype="multipart/form-data">
	<div class="main">
		<div class="panel panel-default">
			<div class="panel-heading">商家设置</div>
			<div class="panel-body">
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="require">* </span>是否允许商家自己设置商品已售份数</label>
					<div class="col-sm-9 col-xs-12">
						<label class="radio-inline">
							<input type="radio" value="1" name="custom_goods_sailed_status" <?php  if($config['store']['custom_goods_sailed_status'] == 1) { ?>checked<?php  } ?>> 允许
						</label>
						<label class="radio-inline">
							<input type="radio" value="0" name="custom_goods_sailed_status" <?php  if(!$config['store']['custom_goods_sailed_status']) { ?>checked<?php  } ?>> 不允许
						</label>
						<div class="help-block text-danger">当设置为不允许时, 商品的销量会按照销量就行递增</div>
					</div>
				</div>
			</div>
		</div>
		<div class="panel panel-default">
			<div class="panel-heading">配送设置</div>
			<div class="panel-body">
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="require">* </span>配送员模式</label>
					<div class="col-sm-9 col-xs-12">
						<label class="radio-inline">
							<input type="radio" value="1" name="delivery_type" <?php  if($config_deliveryer['delivery_type'] == 1 || !$config['delivery_type']) { ?>checked<?php  } ?>> 店内配送员
						</label>
						<label class="radio-inline">
							<input type="radio" value="2" name="delivery_type" <?php  if($config_deliveryer['delivery_type'] == 2) { ?>checked<?php  } ?>> 平台配送员
						</label>
						<div class="help-block"><strong class="text-danger">门店只能选择一个配送方式, 不能同时使用"平台配送员"和"店内配送员".</strong>如需单独设置某个门店可已使用自己门店的配送员, 请到"<a href="<?php  echo $this->createWebUrl('ptftrade', array('op' => 'account'));?>" target="_blank">财务中心-门店账户</a>"进行设置.</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="require">*</span>平台配送费</label>
					<div class="col-sm-9 col-xs-12">
						<div class="input-group">
							<div class="input-group-addon">每单</div>
							<input type="text" name="plateform_delivery_fee" value="<?php  echo $config_deliveryer['plateform_delivery_fee'];?>" class="form-control"/>
							<div class="input-group-addon">元</div>
						</div>
						<div class="help-block">
							<strong class="text-danger">
								此项设置: 商家使用平台配送模式后, 下单人需要支付的配送费.使用平台配送模式后, 商家将不能自己变更配送费, 只能由平台管理员设置配送费.
							</strong>
							如需单独设置某个门店的配送费, 请到"<a href="<?php  echo $this->createWebUrl('ptftrade', array('op' => 'account'));?>" target="_blank">财务中心-门店账户</a>"进行设置.
						</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="require">*</span>将配送模式和配送费同步到所有门店</label>
					<div class="col-sm-9 col-xs-12">
						<div class="input-group">
							<label class="checkbox-inline">
								<input type="checkbox" name="delivery_sync" value="1"/> 将配送模式和配送费同步到所有门店
							</label>
						</div>
						<div class="help-block">同步后,所有门店的配送员模式都会被设置为这个规则</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="require">* </span>平台给配送员每单支付金额(外卖单)</label>
					<div class="col-sm-9 col-xs-12">
						<div class="input-group">
							<label class="input-group-addon">
								<input type="radio" name="delivery_fee_type" value="1" <?php  if($config_deliveryer['delivery_fee_type'] == 1 || !$config_deliveryer['delivery_fee_type']) { ?>checked<?php  } ?>>
							</label>
							<span class="input-group-addon">每单固定</span>
							<input type="text" class="form-control" name="delivery_fee_1" <?php  if($config_deliveryer['delivery_fee_type'] == 1) { ?>value="<?php  echo $config_deliveryer['delivery_fee'];?>"<?php  } ?>>
							<span class="input-group-addon">元</span>
						</div>
						<br>
						<div class="input-group">
							<label class="input-group-addon">
								<input type="radio" name="delivery_fee_type" value="2" <?php  if($config_deliveryer['delivery_fee_type'] == 2) { ?>checked<?php  } ?>>
							</label>
							<span class="input-group-addon">每单按照订单价格提成</span>
							<input type="text" class="form-control" name="delivery_fee_2" <?php  if($config_deliveryer['delivery_fee_type'] == 2) { ?>value="<?php  echo $config_deliveryer['delivery_fee'];?>"<?php  } ?>>
							<span class="input-group-addon">%</span>
						</div>
						<div class="help-block text-danger"></div>
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
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('public/footer', TEMPLATE_INCLUDEPATH)) : (include template('public/footer', TEMPLATE_INCLUDEPATH));?>