<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('public/header', TEMPLATE_INCLUDEPATH)) : (include template('public/header', TEMPLATE_INCLUDEPATH));?>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('plateform/config-nav', TEMPLATE_INCLUDEPATH)) : (include template('plateform/config-nav', TEMPLATE_INCLUDEPATH));?>
<form class="form-horizontal form" id="form1" action="" method="post" enctype="multipart/form-data">
	<div class="main">
		<div class="alert alert-info">
			<h3>在开启以下支付方式前，请到 <a href="<?php  echo url('profile/payment');?>" target="_blank">支付选项</a> 去设置好参数。</h3>
		</div>
		<div class="panel panel-default">
			<div class="panel-heading">支付方式设置</div>
			<div class="panel-body">
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">微信支付</label>
					<div class="col-sm-9 col-md-3">
						<label class="radio-inline">
							<input type="radio" value="1" name="wechat" <?php  if($config['payment']['wechat'] == 1) { ?>checked<?php  } ?>> 开启
						</label>
						<label class="radio-inline">
							<input type="radio" value="0" name="wechat" <?php  if($config['payment']['wechat'] == 0) { ?>checked<?php  } ?>> 关闭
						</label>
					</div>
				</div>
				<div id="wechat-pay">
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">CERT证书文件</label>
						<div class="col-sm-12 col-md-9">
							<div class="form-group">
								<textarea name="apiclient_cert" class="form-control" placeholder="为保证安全性, 不显示证书内容. 若要修改, 请直接输入" cols="30" rows="7"></textarea>
								<span class="help-block">
									<?php  if(!empty($config['payment']['wechat_cert']['apiclient_cert'])) { ?>
									<span class="label label-success">已上传</span>
									<?php  } else { ?>
									<span class="label label-danger">未上传</span>
									<?php  } ?>
									从商户平台上下载支付证书, 解压并取得其中的 <span class="bg-danger">apiclient_cert.pem</span> 用记事本打开并复制文件内容, 填至此处
								</span>
							</div>
						</div>
					</div>
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">KEy证书密钥</label>
						<div class="col-sm-12 col-md-9">
							<div class="form-group">
								<textarea name="apiclient_key" class="form-control" placeholder="为保证安全性, 不显示证书内容. 若要修改, 请直接输入" cols="30" rows="7"></textarea>
								<span class="help-block">
									<?php  if(!empty($config['payment']['wechat_cert']['apiclient_key'])) { ?>
									<span class="label label-success">已上传</span>
									<?php  } else { ?>
									<span class="label label-danger">未上传</span>
									<?php  } ?>
									从商户平台上下载支付证书, 解压并取得其中的 <span class="bg-danger">apiclient_key.pem</span> 用记事本打开并复制文件内容, 填至此处
								</span>
							</div>
						</div>
					</div>
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">ROOTCA证书</label>
						<div class="col-sm-12 col-md-9">
							<div class="form-group">
								<textarea name="rootca" class="form-control" placeholder="为保证安全性, 不显示证书内容. 若要修改, 请直接输入" cols="30" rows="7"></textarea>
								<span class="help-block">
									<?php  if(!empty($config['payment']['wechat_cert']['rootca'])) { ?>
									<span class="label label-success">已上传</span>
									<?php  } else { ?>
									<span class="label label-danger">未上传</span>
									<?php  } ?>
									从商户平台上下载支付证书, 解压并取得其中的 <span class="bg-danger"> rootca.pem</span> 用记事本打开并复制文件内容, 填至此处
								</span>
							</div>
						</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">支付宝支付</label>
					<div class="col-sm-9 col-xs-12">
						<label class="radio-inline">
							<input type="radio" value="1" name="alipay" <?php  if($config['payment']['alipay'] == 1) { ?>checked<?php  } ?>> 开启
						</label>
						<label class="radio-inline">
							<input type="radio" value="0" name="alipay" <?php  if($config['payment']['alipay'] == 0) { ?>checked<?php  } ?>> 关闭
						</label>
					</div>
				</div>
				<div id="alipay-pay">
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">支付宝应用id</label>
						<div class="col-sm-9 col-xs-12">
							<input type="text" class="form-control" name="app_id" value="<?php  echo $config['payment']['alipay_cert']['app_id'];?>"/>
						</div>
					</div>
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">应用公钥(SHA1withRsa)</label>
						<div class="col-sm-12 col-md-9">
							<div class="form-group">
								<textarea name="private_key" class="form-control" placeholder="为保证安全性, 不显示证书内容. 若要修改, 请直接输入" cols="30" rows="7"></textarea>
								<span class="help-block">
									<?php  if(!empty($config['payment']['alipay_cert']['private_key'])) { ?>
									<span class="label label-success">已上传</span>
									<?php  } else { ?>
									<span class="label label-danger">未上传</span>
									<?php  } ?>
									从商户平台上下载支付证书, 解压并取得其中的 <span class="bg-danger"> rootca.pem</span> 用记事本打开并复制文件内容, 填至此处
								</span>
							</div>
						</div>
					</div>

					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">支付宝公钥(SHA1withRsa)</label>
						<div class="col-sm-12 col-md-9">
							<div class="form-group">
								<textarea name="public_key" class="form-control" placeholder="为保证安全性, 不显示证书内容. 若要修改, 请直接输入" cols="30" rows="7"></textarea>
								<span class="help-block">
									<?php  if(!empty($config['payment']['alipay_cert']['public_key'])) { ?>
									<span class="label label-success">已上传</span>
									<?php  } else { ?>
									<span class="label label-danger">未上传</span>
									<?php  } ?>
									从商户平台上下载支付证书, 解压并取得其中的 <span class="bg-danger"> rootca.pem</span> 用记事本打开并复制文件内容, 填至此处
								</span>
							</div>
						</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">余额支付</label>
					<div class="col-sm-9 col-xs-12">
						<label class="radio-inline">
							<input type="radio" value="1" name="credit" <?php  if($config['payment']['credit'] == 1) { ?>checked<?php  } ?>> 开启
						</label>
						<label class="radio-inline">
							<input type="radio" value="0" name="credit" <?php  if($config['payment']['credit'] == 0) { ?>checked<?php  } ?>> 关闭
						</label>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">货到付款</label>
					<div class="col-sm-9 col-xs-12">
						<label class="radio-inline">
							<input type="radio" value="1" name="delivery" <?php  if($config['payment']['delivery'] == 1) { ?>checked<?php  } ?>> 开启
						</label>
						<label class="radio-inline">
							<input type="radio" value="0" name="delivery" <?php  if($config['payment']['delivery'] == 0) { ?>checked<?php  } ?>> 关闭
						</label>
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