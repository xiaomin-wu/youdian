<?php defined('IN_IA') or exit('Access Denied');?><ul class="nav nav-tabs">
	<li <?php  if($do == 'trade' && ($op == 'account' || $op == 'set')) { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('ptftrade', array('op' => 'account'));?>">门店账户</a></li>
	<li <?php  if($do == 'trade' && $op == 'getcashlog') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('ptftrade', array('op' => 'getcashlog'));?>">门店提现</a></li>
	<li <?php  if($do == 'analyse-takeout') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('ptfanalyse-takeout');?>">经营分析</a></li>
</ul>