<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('public/header', TEMPLATE_INCLUDEPATH)) : (include template('public/header', TEMPLATE_INCLUDEPATH));?>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('plateform/config-nav', TEMPLATE_INCLUDEPATH)) : (include template('plateform/config-nav', TEMPLATE_INCLUDEPATH));?>
<div class="panel panel-default">
	<div class="panel-body">
		<ul class="nav nav-pills">
			<li <?php  if($op == 'auth') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('ptfcloud', array('op' => 'auth'));?>"> 系统授权</a></li>
			<li <?php  if($op == 'upgrade') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('ptfcloud', array('op' => 'upgrade'));?>"> 系统更新</a></li>
			<?php  if($op == 'process') { ?><li class="active"><a href="<?php  echo $this->createWebUrl('ptfcloud', array('op' => 'process'));?>"> 自动更新</a></li><?php  } ?>
		</ul>
	</div>
</div>
<div class="alert alert-info" style="font-size: 18px">
	<i class="fa fa-info-circle"></i> 模块更新日志：<a href="https://denghuo.kf5.com/hc/kb/category/28875/" target="_blank">https://denghuo.kf5.com/hc/kb/category/28875/</a>
</div>
<?php  if($op == 'auth') { ?>
<div class="clearfix">
	<form class="form-horizontal form" id="form1" action="" method="post" enctype="multipart/form-data">
		<div class="panel panel-default">
			<div class="panel-heading">系统授权</div>
			<div class="panel-body">
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">网站URL</label>
					<div class="col-md-6">
						<input type="text" name="url" value="<?php  echo $params['url'];?>" class="form-control" readonly/>
						<div class="help-block">网站URL</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">IP地址</label>
					<div class="col-md-6">
						<input type="text" name="ip" value="<?php  echo $params['ip'];?>" class="form-control" readonly/>
						<div class="help-block">IP地址</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">站点ID</label>
					<div class="col-md-6">
						<input type="text" name="site_id" value="<?php  echo $params['site_id'];?>" class="form-control" readonly/>
						<div class="help-block">站点ID,如果为空，请到 <a href="<?php  echo url('cloud/profile');?>" target="_blank">站点注册</a> 绑定您的服务器</div>
					</div>
				</div>
				<?php  if(!empty($cache['code'])) { ?>
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">授权码</label>
						<div class="col-md-6">
							<input type="text" name="code" value="<?php  echo $cache['code'];?>" class="form-control"/>
							<div class="help-block">请联系客服将IP及站点ID提交给客服, 索取授权码，保护好您的授权码，避免泄漏</div>
						</div>
					</div>
				<?php  } ?>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">授权状态</label>
					<div class="col-md-6">
						<p class="form-control-static">
							<?php  if(is_array($cache) && !empty($cache['code'])) { ?>
								<span class="label label-success">已授权<?php  if(MODULE_FAMILY == 'basic') { ?>-外送基础版<?php  } ?></span>
								<?php  if(MODULE_FAMILY == 'basic') { ?>
									<br>
									<span class="label label-info label-br" style="line-height: 3">
										当前版本为“外送基础版”， 可联系作者QQ:2471240272升级到“外送+跑腿+配送app版”
									</span>
								<?php  } ?>
							<?php  } else { ?>
								<span class="label label-danger">您的系统尚未授权，请点击 “验证授权” 按钮就行授权验证。</span>
							<?php  } ?>
						</p>
					</div>
				</div>
			</div>
		</div>
		<div class="form-group">
			<div class="col-sm-9 col-xs-9 col-md-9">
				<input type="hidden" name="token" value="<?php  echo $_W['token'];?>">
				<input name="submit" id="submit" type="submit" value="验证授权" class="btn btn-primary col-lg-1">
			</div>
		</div>
	</form>
</div>
<script>
$(function(){
	$('#form1').submit(function(){
		var url = $.trim($(':text[name="url"]').val());
		if(!url) {
			util.message('站点url不能为空', '', 'error');
			return false;
		}
		var ip = $.trim($(':text[name="ip"]').val());
		if(!ip) {
			util.message('服务器ip不能为空', '', 'error');
			return false;
		}
		var code = $.trim($(':text[name="code"]').val());
		if(!code && 0) {
			util.message('授权码不能为空', '', 'error');
			return false;
		}
	});
});
</script>
<?php  } ?>

<?php  if($op == 'upgrade') { ?>
<div class="clearfix">
	<div class="alert alert-warning">
		<i class="fa fa-exclamation-triangle"></i> 平台所有打印机均属于定制打印机， 客户自行购买的打印机会造成打印不兼容的问题， 如需购买打印机， 请联系模块官方作者。<strong class="text-danger">QQ: 2471240272</strong>
	</div>
	<div class="alert alert-danger">
		<i class="fa fa-exclamation-triangle"></i> 更新时请注意备份网站数据和相关数据库文件！官方不强制要求用户跟随官方意愿进行更新尝试！
	</div>
	<div class="panel panel-default">
		<div class="panel-body">
			<form action="" method="post" class="form-horizontal" role="form">
				<?php  if(!empty($upgrade) && !empty($upgrade['upgrade'])) { ?>
					<div class="form-group">
						<label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">版本</label>
						<div class="col-sm-10">
							<p class="form-control-static"><span class="fa fa-square-o"></span> &nbsp; 系统当前版本: <?php  echo $now_family?> <?php  echo MODULE_VERSION;?>-<?php  echo MODULE_RELEASE_DATE?></p>
							<?php  if($upgrade['release'] != MODULE_RELEASE_DATE) { ?>
							 <p class="form-control-static"><span class="fa fa-square-o"></span> &nbsp; 存在的新版本: <?php  echo $now_family?> <?php  echo $upgrade['version'];?>-<?php  echo $upgrade['release'];?></p>
							<?php  } ?>
							<div class="help-block">在一个发布版中可能存在多次补丁, 因此版本可能未更新</div>
						</div>
					</div>
					<?php  if(!empty($upgrade['logs'])) { ?>
						<div class="form-group">
							<label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">更新内容</label>
							<div class="col-sm-10 col-md-6">
								<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
									<?php  if(is_array($upgrade['logs'])) { foreach($upgrade['logs'] as $log) { ?>
										<?php  $i++;?>
										<div class="panel panel-default panel-update-content">
											<div class="panel-heading" role="tab" id="heading-<?php  echo $log['addtime'];?>" data-toggle="collapse" data-parent="#accordion" href="#<?php  echo $log['addtime'];?>" aria-expanded="true" aria-controls="collapseOne">
												<h4 class="panel-title">
													<a>
														<?php  echo $log['version'];?>-<?php  echo $log['release'];?>
														<span class="pull-right"><?php  echo date('Y-m-d H:i', $log['addtime'])?></span>
													</a>
												</h4>
											</div>
											<div id="<?php  echo $log['addtime'];?>" class="panel-collapse collapse <?php  if($i == 1) { ?>in<?php  } ?>" role="tabpanel" aria-labelledby="heading-<?php  echo $log['addtime'];?>">
												<div class="panel-body">
													<?php  echo $log['message'];?>
												</div>
											</div>
										</div>
									<?php  } } ?>
								</div>
							</div>
						</div>
					<?php  } ?>
					<?php  if(!empty($upgrade['database']) && 0) { ?>
						<div class="form-group">
							<label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">数据库同步情况</label>
							<div class="col-sm-10">
								<div class="help-block"><strong>注意: 重要: 本次更新涉及到数据库变动, 请做好备份.</strong></div>
								<div class="alert alert-success" style="line-height:20px;margin-top:20px;">
									<?php  if(is_array($upgrade['database'])) { foreach($upgrade['database'] as $line) { ?>
									<div class="row">
										<div class="col-xs-2 col-lg-1">表名:</div>
										<div class="col-xs-2 col-lg-2"><?php  echo $line['tablename'];?></div>
										<?php  if(!empty($line['new'])) { ?>
										<div class="col-xs-8 col-lg-9">New</div>
										<?php  } else { ?>
										<div class="col-xs-8 col-lg-9"><?php  if(!empty($line['fields'])) { ?>fields: <?php  echo $line['fields'];?>; <?php  } ?><?php  if(!empty($line['indexes'])) { ?>indexes: <?php  echo $line['indexes'];?><?php  } ?></div>
										<?php  } ?>
									</div>
									<?php  } } ?>
								</div>
							</div>
						</div>
					<?php  } ?>
					<?php  if(!empty($upgrade['scripts'])) { ?>
						<div class="form-group">
							<label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">更新通告</label>
							<div class="col-sm-10 col-md-6">
								<?php  if(is_array($upgrade['scripts'])) { foreach($upgrade['scripts'] as $ver) { ?>
									<p class="form-control-static"><?php  echo $upgrade['family'];?><?php  echo $ver['version'];?><?php  echo $ver['release'];?> Build</p>
								<?php  } } ?>
							</div>
						</div>
					<?php  } ?>
					<?php  if(!empty($upgrade['files'])) { ?>
						<div class="form-group">
							<label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">文件同步情况</label>
							<div class="col-sm-10 col-md-6">
								<div class="help-block"><strong>注意: 重要: 本次更新涉及到程序变动, 请做好备份.</strong></div>
								<div class="alert alert-info" style="line-height:20px;margin-top:20px;">
									<?php  if(is_array($upgrade['files'])) { foreach($upgrade['files'] as $line) { ?>
									<div><span style="display:inline-block; width:30px;"><?php  if(is_file(MODULE_ROOT . $line)) { ?>M<?php  } else { ?>A<?php  } ?></span><?php  echo $line;?></div>
									<?php  } } ?>
								</div>
							</div>
						</div>
					<?php  } ?>
					<div class="form-group">
						<label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">更新协议</label>
						<div class="col-sm-10 col-md-6">
							<div class="checkbox">
								<label>
									<input type="checkbox" id="agreement_0"> 我已经做好了相关文件的备份工作
								</label>
							</div>
							<div class="checkbox">
								<label>
									<input type="checkbox" id="agreement_1"> 认同官方的更新行为并自愿承担更新所存在的风险
								</label>
							</div>
							<div class="checkbox">
								<label>
									<input type="checkbox" id="agreement_2"> 理解官方的辛勤劳动并报以感恩的心态点击更新按钮
								</label>
							</div>
						</div>
					</div>
					<div class="form-group">
						<div class="col-sm-offset-2 col-md-offset-2 col-lg-offset-1 col-xs-12 col-sm-10 col-md-10 col-lg-11">
							<input type="button" id="forward" value="立即更新" class="btn btn-primary" />
						</div>
					</div>
				<?php  } else { ?>
					<div class="form-group">
						<div class="col-sm-offset-2 col-md-offset-2 col-lg-offset-1 col-xs-12 col-sm-10 col-md-10 col-lg-11">
							<input name="submit" type="submit" value="立即检查新版本" class="btn btn-primary" />
							<input type="hidden" name="token" value="<?php  echo $_W['token'];?>" />
							<div class="help-block">当前系统未检测到有新版本, 你可以点击此按钮, 来立即检查一次.</div>
						</div>
					</div>
				<?php  } ?>
			</form>
		</div>
	</div>
</div>
<?php  if(!empty($upgrade) && !empty($upgrade['upgrade'])) { ?>
<script type="text/javascript">
	$('#forward').click(function(){
		var a = $("#agreement_0").is(':checked');
		var b = $("#agreement_1").is(':checked');
		var c = $("#agreement_2").is(':checked');
		if(a && b && c) {
			if(confirm('更新将直接覆盖本地文件, 请注意备份文件和数据. \n\n**另注意** 更新过程中不要关闭此浏览器窗口.')) {
				location.href = "<?php  echo $this->createWebUrl('ptfcloud', array('op' => 'process'))?>";
			}
		} else {
			util.message("抱歉，更新前请仔细阅读更新协议！", '', 'error');
			return false;
		}
	});
</script>
<?php  } ?>
<?php  } ?>

<?php  if($op == 'process') { ?>
<div class="clearfix">
	<?php  if($step == 'files') { ?>
		<?php  if(empty($packet['files'])) { ?>
			<script type="text/javascript">
				location.href = "<?php  echo $this->createWebUrl('ptfcloud', array('op' => 'process', 'step' => 'schemas'));?>";
			</script>
		<?php  } ?>
		<div class="alert alert-warning">
			正在更新系统文件, 请不要关闭窗口.
		</div>
		<div class="alert alert-warning">
			如果下载文件失败，可能造成的原因：写入失败，请仔细检查写入权限是否正确。<strong>如果出现更新失败的文件， 请刷新页面重新更新。如果出现空白页面 ，请等待1分钟左右在更新， 是服务器缓存的原因</strong>
		</div>
		<div class="alert alert-info form-horizontal ng-cloak" ng-controller="processor">
			<dl class="dl-horizontal">
				<dt>整体进度</dt>
				<dd>{{pragress}}</dd>
				<dt>正在下载文件</dt>
				<dd>{{file}}</dd>
			</dl>
			<dl class="dl-horizontal" ng-show="fails.length > 0">
				<dt>下载失败的文件</dt>
				<dd>
					<p class="text-danger" ng-repeat="file in fails" style="margin:0;">{{file}}</p>
				</dd>
			</dl>
		</div>
		<script>
			require(['angular'], function(angular){
				angular.module('app', []).controller('processor', function($scope, $http){
					$scope.files = <?php  echo json_encode($packet['files']);?>;
					$scope.fails = [];
					var total = $scope.files.length;
					var i = 1;
					var proc = function() {
						var path = $scope.files.pop();
						if(!path) {
							if($scope.fails.length == 0) {
								setTimeout(function(){
									location.href = "<?php  echo $this->createWebUrl('ptfcloud', array('op' => 'process', 'step' => 'schemas'));?>";
								}, 2000);
							} else {
								setTimeout(function(){
									location.href = "<?php  echo $this->createWebUrl('ptfcloud', array('op' => 'process'));?>";
								}, 2000);
							}
							return;
						}
						$scope.file = path;
						$scope.pragress = i + '/' + total;
						var params = {path: path};
						$http.post(location.href, params).success(function(dat){
							i++;
							if(dat != 'success') {
								$scope.fails.push(path);
							}
							proc();
						}).error(function(){
							i++;
							$scope.fails.push(path);
							proc();
						});
					}
					proc();
				});
				angular.bootstrap(document, ['app']);
			});
		</script>
	<?php  } ?>
	<?php  if($step == 'schemas') { ?>
		<?php  if(empty($packet['schemas'])) { ?>
			<script>
				location.href = "<?php  echo $this->createWebUrl('ptfcloud', array('op' => 'process', 'step' => 'scripts'));?>";
			</script>
		<?php  } ?>
		<div class="alert alert-warning">
			正在更新数据库, 请不要关闭窗口.
		</div>
		<div class="alert alert-info form-horizontal ng-cloak" ng-controller="processor">
			<dl class="dl-horizontal">
				<dt>整体进度</dt>
				<dd>{{pragress}}</dd>
			</dl>
			<dl class="dl-horizontal" ng-show="fails.length > 0">
				<dt>处理失败的数据表</dt>
				<dd>
					<p class="text-danger" ng-repeat="schema in fails" style="margin:0;" class="hide">{{schema}}</p>
				</dd>
			</dl>
		</div>
		<script>
			require(['angular', 'util'], function(angular, u){
				angular.module('app', []).controller('processor', function($scope, $http){
					$scope.schemas = <?php  echo json_encode($schemas);?>;
					$scope.fails = [];
					var total = $scope.schemas.length;
					var i = 1;
					var error = function() {
						require(['util'], function(u){
							util.message('未能成功执行处理数据库, 请联系开发商解决. ');
						});
					}
					var proc = function() {
						var schema = $scope.schemas.pop();
						if(!schema) {
							if($scope.fails.length > 0) {
								error();
								return;
							} else {
								setTimeout(function(){
									location.href = "<?php  echo $this->createWebUrl('ptfcloud', array('op' => 'process', 'step' => 'scripts'));?>";
								}, 2000);
								return;
							}
						}
						$scope.schema = schema;
						$scope.pragress = i + '/' + total;
						var params = {table: schema};
						$http.post(location.href, params).success(function(dat){
							i++;
							if(dat != 'success') {
								$scope.fails.push(schema)
							}
							if (dat['message']) {
								util.message(dat['message']);
								return;
							}
							proc();
						}).error(function(){
							i++;
							$scope.fails.push(schema);
							proc();
						});
					}
					proc();
				});
				angular.bootstrap(document, ['app']);
			});
		</script>
	<?php  } ?>
	<?php  if($step == 'scripts') { ?>
		<?php  if(empty($packet['scripts'])) { ?>
			<script>
				util.message('已经成功执行升级操作!', "<?php  echo $this->createWebUrl('ptfcloud', array('op' => 'upgrade'));?>");
			</script>
		<?php  } ?>
		<div class="alert alert-warning">
			正在数据迁移及清理操作, 请不要关闭窗口.
		</div>
		<div class="alert alert-info form-horizontal ng-cloak" ng-controller="processor">
			<dl class="dl-horizontal">
				<dt>整体进度</dt>
				<dd>{{pragress}}</dd>
				<dt>正在处理</dt>
				<dd>{{script}}</dd>
			</dl>
			<dl class="dl-horizontal" ng-show="fails.length > 0">
				<dt>处理失败的操作</dt>
				<dd>
					<p class="text-danger" ng-repeat="script in fails" style="margin:0;">{{script}}</p>
				</dd>
			</dl>
		</div>
		<script>
			require(['angular'], function(angular){
				angular.module('app', []).controller('processor', function($scope, $http){
					$scope.scripts = <?php  echo json_encode($scripts);?>;
					$scope.fails = [];
					var total = $scope.scripts.length;
					var i = 1;
					var error = function() {
						require(['util'], function(u){
							util.message('未能成功执行清理升级操作, 请联系开发商. ');
						});
					}
					var proc = function() {
						var script = $scope.scripts.shift();
						if(!script) {
							if($scope.fails.length > 0) {
								error();
							} else {
								util.message('已经成功执行升级操作!', "<?php  echo $this->createWebUrl('ptfcloud', array('op' => 'upgrade'));?>");
								return;
							}
						}
						$scope.script = script.fname;
						$scope.message = script.message;
						$scope.pragress = i + '/' + total;
						var params = {fname: script.fname};
						$http.post(location.href, params).success(function(dat){
							i++;
							if(dat != 'success') {
								console.dir(dat)
								$scope.fails.push(script.fname)
								error();
								return;
							}
							proc();
						}).error(function(){
							i++;
							$scope.fails.push(script.fname);
							error();
						});
					}
					proc();
				});
				angular.bootstrap(document, ['app']);
			});
		</script>
	<?php  } ?>
</div>
<?php  } ?>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('public/footer', TEMPLATE_INCLUDEPATH)) : (include template('public/footer', TEMPLATE_INCLUDEPATH));?>