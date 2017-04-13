<?php defined('IN_IA') or exit('Access Denied');?><ul class="nav nav-tabs">
	<li <?php  if($do == 'trade' && ($op == 'account' || $op == 'set')) { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('ptftrade', array('op' => 'account'));?>">门店账户</a></li>
	<li <?php  if($do == 'order') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('ptforder-takeout');?>">订单列表</a></li>
	<li <?php  if($do == 'trade' && $op == 'currentlog') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('ptftrade', array('op' => 'currentlog'));?>">交易记录</a></li>
	<li <?php  if($do == 'trade' && $op == 'getcashlog') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('ptftrade', array('op' => 'getcashlog'));?>">商户提现</a></li>
	<li <?php  if($do == 'finance' && $op == 'list') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('ptffinance', array('op' => 'list'));?>">订单统计</a></li>
</ul>