<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('manage/header', TEMPLATE_INCLUDEPATH)) : (include template('manage/header', TEMPLATE_INCLUDEPATH));?>
<div class="page store-index" id="page-manage-store-index">
	<header class="bar bar-nav common-bar-nav">
		<a class="icon pull-left fa fa-arrow-left back"></a>
		<h1 class="title">店铺管理</h1>
	</header>
	<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('manage/nav', TEMPLATE_INCLUDEPATH)) : (include template('manage/nav', TEMPLATE_INCLUDEPATH));?>
	<div class="content">
		<div class="list-block media-list">
			<ul>
				<li class="store-title">
					<a href="javascript:;" class="item-content">
						<div class="item-media"><img src="<?php  echo tomedia($store['logo']);?>" style='width: 3rem;'></div>
						<div class="item-inner">
							<div class="item-title-row">
								<div class="item-title"><?php  echo $store['title'];?></div>
							</div>
							<div class="item-subtitle">营业时间:<?php  echo $store['business_hours_cn'];?></div>
						</div>
					</a>
				</li>
			</ul>
			<ul style="border-top: 0">
				<li class="business-hours">
					<div class="item-content">
						<div class="item-inner">
							<div class="item-title">店铺状态</div>
							<div class="item-after">
								<label class="label-switch invoice-status">
									<input type="checkbox" name="is_in_business" value="1" <?php  if($store['is_in_business'] == 1) { ?>checked<?php  } ?>>
									<div class="checkbox"></div>
								</label>
							</div>
						</div>
					</div>
				</li>
			</ul>
		</div>
	</div>
</div>
<script>
$(function(){
	$(document).on('click', ':checkbox[name="is_in_business"]', function(){
		var is_in_business = $(this).prop('checked') ? 1 : 0;
		$.post("<?php  echo $this->createMobileUrl('mgstore', array('op' => 'is_in_business'))?>", {is_in_business: is_in_business}, function(data) {
			var result = $.parseJSON(data);
			$.toast(result.message.message);
		});
	});
});
</script>
<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('manage/common', TEMPLATE_INCLUDEPATH)) : (include template('manage/common', TEMPLATE_INCLUDEPATH));?>
<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('manage/footer', TEMPLATE_INCLUDEPATH)) : (include template('manage/footer', TEMPLATE_INCLUDEPATH));?>