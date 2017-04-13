<?php defined('IN_IA') or exit('Access Denied');?><div class="panel panel-default">
	<div class="panel-body">
		<ul class="nav nav-pills">
			<li <?php  if($do == 'order-takeout') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('ptforder-takeout');?>"> 外卖订单</a></li>
			<?php  if(MODULE_FAMILY != 'basic') { ?>
				<li <?php  if($do == 'order-errander') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('ptforder-errander');?>"> 跑腿订单</a></li>
			<?php  } ?>
		</ul>
	</div>
</div>