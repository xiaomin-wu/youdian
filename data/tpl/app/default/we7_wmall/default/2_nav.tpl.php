<?php defined('IN_IA') or exit('Access Denied');?><nav class="bar bar-tab footer-bar sborder <?php  if($_GPC['do'] == 'goods') { ?>hide<?php  } ?>">
	<a class="tab-item external <?php  if(in_array($do, array('index'))) { ?>active<?php  } ?>" href="<?php  echo $this->createMobileUrl('index');?>">
		<span class="icon icon-index"></span>
		<span class="tab-label">首页</span>
	</a>
	<?php  if($_W['we7_wmall']['config']['version'] == 1) { ?>
		<a class="tab-item <?php  if(in_array($do, array('search'))) { ?>active<?php  } ?>" href="<?php  echo $this->createMobileUrl('search', array('force' => 1));?>">
			<span class="icon icon-found"></span>
			<span class="tab-label">附近</span>
		</a>
		<a class="tab-item <?php  if(in_array($do, array('hunt'))) { ?>active<?php  } ?>" href="<?php  echo $this->createMobileUrl('hunt');?>">
			<span class="icon icon-isearch"></span>
			<span class="tab-label">搜索</span>
		</a>
	<?php  } ?>
	<a class="tab-item <?php  if(in_array($do, array('order'))) { ?>active<?php  } ?>" href="<?php  echo $this->createMobileUrl('order');?>">
		<span class="icon icon-order"></span>
		<span class="tab-label">订单</span>
	</a>
	<a class="tab-item <?php  if(in_array($do, array('address', 'mine', 'favorite'))) { ?>active<?php  } ?>" href="<?php  echo $this->createMobileUrl('mine');?>">
		<span class="icon icon-mine"></span>
		<span class="tab-label">我的</span>
	</a>
</nav>
<?php  if(empty($_W['fans']['follow']) && $_W['we7_wmall']['config']['follow_guide_status'] == 1 && in_array($_GPC['do'], array('index', 'goods', 'mine'))) { ?>
<div class="follow-tips">
	<div class="info">
		<div class="logo"><img src="<?php  echo tomedia($_W['we7_wmall']['config']['thumb']);?>" alt=""></div>
		<div class="txt"><p>欢迎进入<?php  echo $_W['we7_wmall']['config']['title'];?><br>关注公众号，享专属服务</p></div>
	</div>
	<div class="text-btn"><a href="<?php  echo $_W['we7_wmall']['config']['followurl'];?>">立即关注</a></div>
</div>
<?php  } ?>
