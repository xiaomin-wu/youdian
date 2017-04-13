<?php defined('IN_IA') or exit('Access Denied');?><ul class="nav nav-tabs">
	<li <?php  if($op == 'basic') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('ptfconfig', array('op' => 'basic'));?>"> 基础设置</a></li>
	<li <?php  if($op == 'pay') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('ptfconfig', array('op' => 'pay'));?>"> 支付方式设置</a></li>
	<li <?php  if($do == 'takeout') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('ptfconfig-takeout', array('op' => 'set'));?>"> 外卖订单&商家设置</a></li>
	<li <?php  if($do == 'settle') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('ptfconfig-settle', array('op' => 'set'));?>"> 商家入驻设置</a></li>
	<li <?php  if($do == 'gcategory') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('ptfgcategory');?>"> 标签分组设置</a></li>
	<li <?php  if($do == 'delivery') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('ptfconfig-delivery', array('op' => 'set'));?>"> 配送员设置</a></li>
	<li <?php  if($do == 'card') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('ptfconfig-card', array('op' => 'card_set'));?>"> 配送会员卡</a></li>
	<li <?php  if($op == 'report') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('ptfconfig', array('op' => 'report'));?>"> 商户举报类型</a></li>
	<li <?php  if($do == 'help') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('ptfconfig-help', array('op' => 'list'));?>"> 常见问题设置</a></li>
	<?php  if(MODULE_FAMILY != 'basic') { ?>
		<li <?php  if($do == 'errander') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('ptfconfig-errander', array('op' => 'set'));?>"> 跑腿设置</a></li>
		<!-- <li <?php  if($do == 'app') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('ptfconfig-app', array('op' => 'set'));?>"> APP设置</a></li> -->
	<?php  } ?>
	<?php  if(!empty($_W['isfounder'])) { ?>
		<!-- <li <?php  if($do == 'cloud') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('ptfcloud', array('op' => 'auth'));?>"> 云服务</a></li> -->
	<?php  } ?>
</ul>