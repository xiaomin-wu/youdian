<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('manage/header', TEMPLATE_INCLUDEPATH)) : (include template('manage/header', TEMPLATE_INCLUDEPATH));?>
<div class="page" id="page-manage-store">
	<header class="bar bar-nav common-bar-nav">
		<h1 class="title">选择店铺</h1>
	</header>
	<div class="content">
		<ul class="store-list">
			<?php  if(is_array($stores)) { foreach($stores as $store) { ?>
			<li>
				<a href="<?php  echo $this->createMobileUrl('mgswitch', array('sid' => $store['id']));?>">
					<div class="store-pic"><img src="<?php  echo tomedia($store['logo']);?>" alt=""></div>
					<div class="store-info">
						<?php  echo $store['title'];?>
						<i class="fa fa-arrow-right"></i>
					</div>
				</a>
			</li>
			<?php  } } ?>
		</ul>
	</div>
</div>
<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('manage/common', TEMPLATE_INCLUDEPATH)) : (include template('manage/common', TEMPLATE_INCLUDEPATH));?>
<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('manage/footer', TEMPLATE_INCLUDEPATH)) : (include template('manage/footer', TEMPLATE_INCLUDEPATH));?>