<?php defined('IN_IA') or exit('Access Denied');?><div class="panel panel-default">
	<div class="panel-body">
		<ul class="nav nav-pills">
			<li <?php  if($op == 'TY_store_label') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('ptfgcategory', array('op' => 'TY_store_label'));?>"> 商户标签</a></li>
		</ul>
	</div>
</div>