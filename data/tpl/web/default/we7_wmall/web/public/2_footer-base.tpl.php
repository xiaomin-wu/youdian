<?php defined('IN_IA') or exit('Access Denied');?>	<script src="<?php  echo $_W['siteroot'];?>addons/we7_wmall/resource/web/js/laytpl.js"></script>
	<script src="<?php  echo $_W['siteroot'];?>addons/we7_wmall/resource/web/js/common.js"></script>
	<script src="<?php  echo $_W['siteroot'];?>addons/we7_wmall/resource/web/js/tiny.js"></script>
	<script type="text/javascript">
		require(['bootstrap']);
		laytpl.config({open: '<{', 'close': '}>'});
		$('.js-clip').each(function(){
			util.clip(this, $(this).attr('data-url'));
		});
		<?php  if($_W['isfounder'] && !defined('IN_MESSAGE')) { ?>
			if(util.cookie.get('ignoreupgrade') != 1) {
				$.getJSON("<?php  echo $this->createWebUrl('ptfcloud', array('op' => 'check'));?>", function(result){
					if(result && result.message && result.message.upgrade == '1') {
						$('body').append('<div class="panel panel-default panel-checkupgrade"><div class="panel-body"><span class="tclose"><i class="fa fa-times-circle"></i></span><div class="title">检测到更新</div><div class="content">新版本 '+ result.message.version +',请尽快更新!</div><div class="buttons"><a href="<?php  echo $this->createWebUrl('ptfcloud', array('op' => 'upgrade'));?>" class="btn btn-warning btn-sm">立即去更新</a></div></div></div>');
					}
				});
			}
			$(document).on('click', '.panel-checkupgrade .fa-times-circle', function(){
				util.cookie.set('ignoreupgrade', 1, 3600);
				$('.panel-checkupgrade').fadeOut(150);
			});
		<?php  } ?>
	</script>
	<div class="container-fluid footer" role="footer">
		<div class="page-header"></div>
		<span class="pull-left">
			<p><?php  echo $_W['setting']['copyright']['footerleft'];?></p>
		</span>
		<span class="pull-right">
			<p><?php  echo $_W['setting']['copyright']['footerright'];?></p>
		</span>
	</div>
	<?php  if(!empty($_W['setting']['copyright']['statcode'])) { ?><?php  echo $_W['setting']['copyright']['statcode'];?><?php  } ?>
</body>
</html>
