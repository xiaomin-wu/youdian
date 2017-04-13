<?php defined('IN_IA') or exit('Access Denied');?><nav class="bar bar-tab footer-bar">
	<a class="tab-item <?php  if($do == 'mghome' || $do == 'mgstore') { ?>active<?php  } ?>" href="<?php  echo $this->createMobileUrl('mghome');?>">
		<span class="icon icon-home"></span>
		<span class="tab-label">店铺</span>
	</a>
	<a class="tab-item external <?php  if($do == 'mgorder') { ?>active<?php  } ?>" href="<?php  echo $this->createMobileUrl('mgorder');?>">
		<span class="icon icon-more"></span>
		<span class="tab-label">订单</span>
	</a>
	<a class="tab-item <?php  if($do == 'mggoods') { ?>active<?php  } ?>" href="<?php  echo $this->createMobileUrl('mggoods');?>">
		<span class="icon icon-more"></span>
		<span class="tab-label">商品</span>
	</a>
	<a class="tab-item <?php  if($do == 'mgindex') { ?>active<?php  } ?>" href="<?php  echo $this->createMobileUrl('mgindex');?>">
		<span class="icon icon-customer"></span>
		<span class="tab-label">切换店铺</span>
	</a>
</nav>