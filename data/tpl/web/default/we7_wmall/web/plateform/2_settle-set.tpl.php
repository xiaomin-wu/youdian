<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('public/header', TEMPLATE_INCLUDEPATH)) : (include template('public/header', TEMPLATE_INCLUDEPATH));?>
<ul class="nav nav-tabs">
	<li<?php  if($op == 'set') { ?> class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('ptfsettle', array('op' => 'set'));?>">商户入驻参数设置</a></li>
	<li<?php  if($op == 'list') { ?> class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('ptfsettle', array('op' => 'list'));?>">申请入驻列表</a></li>
</ul>
<div class="alert alert-info">
	<i class="fa fa-info-circle"></i> 商户登陆地址:<a href="<?php  echo $_W['siteroot'];?>addons/we7_wmall/admin" target="_blank"><?php  echo $_W['siteroot'];?>addons/we7_wmall/admin</a>
</div>
<?php  if($op == 'set') { ?>
<div class="clearfix">
	<form class="form-horizontal form" id="form1" action="" method="post" enctype="multipart/form-data">
		<div class="panel panel-default">
			<div class="panel-heading">商户入驻参数设置</div>
			<div class="panel-body">
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="require">*</span>商户入驻</label>
					<div class="col-sm-9 col-xs-12">
						<label class="radio-inline"><input type="radio" name="status" value="1" <?php  if($settle['status'] == 1 || !$settle['status']) { ?>checked<?php  } ?>> 开启</label>
						<label class="radio-inline"><input type="radio" name="status" value="2" <?php  if($settle['status'] == 2) { ?>checked<?php  } ?>> 关闭</label>
						<div class="help-block">开启商户入驻后，手机端个人中心页面将开启入口，否则不显示</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="require">*</span>提交入驻申请后审核状态</label>
					<div class="col-sm-9 col-xs-12">
						<label class="radio-inline"><input type="radio" name="audit_status" value="1" <?php  if($settle['audit_status'] == 1) { ?>checked<?php  } ?>> 直接审核通过</label>
						<label class="radio-inline"><input type="radio" name="audit_status" value="2" <?php  if($settle['audit_status'] == 2 || !$settle['audit_status']) { ?>checked<?php  } ?>> 审核中</label>
						<div class="help-block">商家提交入驻申请后,设置是否直接审核通过.</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="require">*</span>是否短信验证手机号</label>
					<div class="col-sm-9 col-xs-12">
						<label class="radio-inline"><input type="radio" name="mobile_verify_status" value="1" <?php  if($settle['mobile_verify_status'] == 1) { ?>checked<?php  } ?>> 验证手机号</label>
						<label class="radio-inline"><input type="radio" name="mobile_verify_status" value="2" <?php  if($settle['mobile_verify_status'] == 2 || !$settle['mobile_verify_status']) { ?>checked<?php  } ?>> 不验证</label>
						<div class="help-block">开启验证手机号,需要配置短信参数.<a href="<?php  echo $this->createWebUrl('ptfsms', array('op' => 'set'));?>" target="_blank">现在去配置</a></div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="require">*</span>入驻协议</label>
					<div class="col-sm-9 col-xs-12">
						<?php  echo tpl_ueditor('agreement', $settle['agreement']);?>
						<div class="help-block">不填写时，商户入驻申请页面将不显示：我已阅读并同意 《商户入驻协议》</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="require">*</span>最低提现金额</label>
					<div class="col-sm-9 col-xs-12">
						<div class="input-group">
							<input type="text" name="get_cash_fee_limit" value="<?php  echo $settle['get_cash_fee_limit'];?>" class="form-control"/>
							<span class="input-group-addon">元</span>
						</div>
						<div class="help-block">只能填写整数，不填写为不限制</div>
						<div class="help-block">
							<span class="text-danger">如需要给不同的门店设置不同的提现费率, 请到<a href="<?php  echo $this->createWebUrl('ptftrade', array('op' => 'account'));?>" target="_blank">"财务中心-门店账户-账户设置"</a>里进行设置</span>
						</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="require">*</span>提现费率</label>
					<div class="col-sm-9 col-xs-12">
						<div class="input-group">
							<input type="text" name="get_cash_fee_rate" value="<?php  echo $settle['get_cash_fee_rate'];?>" class="form-control"/>
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
							<input type="text" name="get_cash_fee_min" value="<?php  echo $settle['get_cash_fee_min'];?>" class="form-control"/>
							<span class="input-group-addon">元</span>
						</div>
						<br>
						<div class="input-group">
							<span class="input-group-addon">最高</span>
							<input type="text" name="get_cash_fee_max" value="<?php  echo $settle['get_cash_fee_max'];?>" class="form-control"/>
							<span class="input-group-addon">元</span>
						</div>
						<div class="help-block">
							商户提现时，提现费用的上下限，最高为空时，表示不限制扣除的提现费用
							<br>
							例如：提现100元，费率5%，最低1元，最高2元，商户最终提现金额=100-2=98
							<br>
							例如：提现100元，费率5%，最低1元，最高10元，商户最终提现金额=100-100*5%=95
						</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="require">*</span>将提现设置同步到所有门店</label>
					<div class="col-sm-9 col-xs-12">
						<div class="input-group">
							<label class="checkbox-inline">
								<input type="checkbox" name="sync" value="1"/> 将提现设置同步到所有门店
							</label>
						</div>
						<div class="help-block">同步后,所有门店的提现规则都会被设置为这个提现规则</div>
					</div>
				</div>
			</div>
		</div>
		<div class="form-group col-sm-12">
			<input name="submit" id="submit" type="submit" value="提交" class="btn btn-primary col-lg-1">
			<input type="hidden" name="token" value="<?php  echo $_W['token'];?>" />
		</div>
	</form>
</div>
<?php  } ?>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('public/footer', TEMPLATE_INCLUDEPATH)) : (include template('public/footer', TEMPLATE_INCLUDEPATH));?>