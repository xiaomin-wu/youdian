<?php defined('IN_IA') or exit('Access Denied');?><ul class="nav nav-tabs">
	<li <?php  if($do == 'store' && $op == 'post') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('store', array('op' => 'post', 'id' => $sid));?>">门店信息</a></li>
	<li <?php  if($do == 'order') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('order');?>">订单列表</a></li>
	<li <?php  if($do == 'activity' || $do == 'token') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('activity');?>">营销活动</a></li>
	<li <?php  if($do == 'dispatch') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('dispatch');?>">配货中心</a></li>
	<li <?php  if($do == 'category') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('category');?>">商品分类</a></li>
	<li <?php  if($do == 'goods') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('goods');?>">商品列表</a></li>
	<li <?php  if($do == 'printer') { ?> class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('printer');?>">打印机管理</a></li>
	<li <?php  if($do == 'clerk') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('clerk');?>">店员管理</a></li>
	<?php  if($_W['we7_wmall']['store']['account']['delivery_type'] == 1) { ?>
		<li <?php  if($do == 'deliveryer') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('deliveryer');?>">配送员管理</a></li>
	<?php  } ?>
	<li <?php  if($do == 'comment') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('comment');?>">评价管理</a></li>
	<li <?php  if($do == 'member') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('member');?>">顾客管理</a></li>
	<li <?php  if($do == 'stat') { ?>class="active"<?php  } ?>>
		<a class="dropdown-toggle" data-toggle="dropdown" href="javascript:;">
			订单统计 <span class="caret"></span>
		</a>
		<ul class="dropdown-menu">
			<li><a href="<?php  echo $this->createWebUrl('stat');?>">订单统计</a></li>
			<li><a href="<?php  echo $this->createWebUrl('stat', array('op' => 'day', 'addtime[start]' => date('Y-m-d')));?>">某天详细统计</a></li>
		</ul>
	</li>
	<li <?php  if($do == 'trade') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('trade', array('op' => 'index'));?>">收入/提现</a></li>
	<li <?php  if($do == 'table_order') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('table_order');?>">店内点餐</a></li>
	<li <?php  if($do == 'place') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('place');?>">店员代客下单</a></li>
</ul>
<?php  if($store['status'] > 1) { ?>
<div class="alert alert-danger">
	<i class="fa fa-info-circle"></i> 当前门店尚未审核,请完善信息后联系管理员审核
</div>
<?php  } ?>
