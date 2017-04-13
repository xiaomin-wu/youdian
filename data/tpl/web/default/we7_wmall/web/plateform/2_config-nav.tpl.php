<?php defined('IN_IA') or exit('Access Denied');?><ul class="nav nav-tabs">
	<li <?php  if($op == 'basic') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('ptfconfig', array('op' => 'basic'));?>"> 基础设置</a></li>
	<li <?php  if($op == 'pay') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('ptfconfig', array('op' => 'pay'));?>"> 支付方式设置</a></li>
	<li <?php  if($op == 'store') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('ptfconfig', array('op' => 'store'));?>"> 商家设置</a></li>
	<li <?php  if($op == 'order') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('ptfconfig', array('op' => 'order'));?>"> 订单相关设置</a></li>
	<li <?php  if($op == 'deliveryer') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('ptfconfig', array('op' => 'deliveryer'));?>"> 配送员设置</a></li>
	<li <?php  if($do == 'card') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('ptfconfig-card', array('op' => 'card_set'));?>"> 配送会员卡</a></li>
	<li <?php  if($op == 'report') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('ptfconfig', array('op' => 'report'));?>"> 商户举报类型</a></li>
	<li <?php  if($do == 'help') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('ptfconfig-help', array('op' => 'list'));?>"> 常见问题设置</a></li>
<!--
	<li <?php  if($do == 'errander') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('ptfconfig-errander', array('op' => 'set'));?>"> 跑腿设置</a></li>
-->
	<li <?php  if($do == 'cloud') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('ptfcloud', array('op' => 'auth'));?>"> 云服务</a></li>
</ul>