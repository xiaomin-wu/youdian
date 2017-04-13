<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('header', TEMPLATE_INCLUDEPATH)) : (include template('header', TEMPLATE_INCLUDEPATH));?>
<div class="page search-result search-corr">
	<div class="bar bar-header-secondary">
		<div class="searchbar">
			<a class="searchbar-arrow back"><i class="fa fa-arrow-left"></i></a>
			<a class="searchbar-cancel">搜索</a>
			<div class="search-input">
				<label class="icon fa fa-search" for="search"></label>
				<input type="search" id='search' name="search" value="<?php  echo $_GPC['key'];?>" placeholder='请输入商户或商品名称'/>
			</div>
		</div>
	</div>
	<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('nav', TEMPLATE_INCLUDEPATH)) : (include template('nav', TEMPLATE_INCLUDEPATH));?>
	<div class="content">
		<?php  if(empty($stores)) { ?>
			<div class="search-noany">
				<span></span>
				<p>抱歉，没有找到合适的商户</p>
			</div>
		<?php  } else { ?>
			<?php  if(is_array($stores)) { foreach($stores as $store) { ?>
				<div class="store-list">
					<div class="list-item">
						<a href="<?php  echo $store['url'];?>&from=search">
							<div class="store-info row no-gutter">
								<div class="store-img col-25">
									<img src="<?php  echo tomedia($store['logo']);?>" alt="<?php  echo $store['title'];?>"/>
								</div>
								<div class="col-75">
									<div class="row no-gutter">
										<div class="col-60 lineheight"><?php  echo $store['title'];?></div>
										<?php  if($store['delivery_mode'] == 2) { ?>
											<div class="plateform-delivery"><span>平台专送</span></div>
										<?php  } ?>
									</div>
									<div class="rel-info">
										<div class="row delivery-conditions">
											<div class="col-60">起送<span>￥<?php  echo $store['send_price'];?></span><span class="pipe">|</span>配送<span>￥<?php  echo $store['delivery_price'];?></span></div>
											<div class="col-40 textright color-danger"><?php  echo $store['delivery_time'];?>分钟</div>
										</div>
										<?php  if(!empty($store['activity']['discount_status'])) { ?>
											<div class="jian">
												在线支付
												<?php  if(is_array($store['activity']['discount_data'])) { foreach($store['activity']['discount_data'] as $discount) { ?>
													满<?php  echo $discount['condition'];?>元减<?php  echo $discount['back'];?>元
												<?php  } } ?>
											</div>
										<?php  } ?>
									</div>
								</div>
							</div>
						</a>
					</div>
					<?php  if(!empty($store['goods'])) { ?>
						<div class="search-text-list">
							<ul>
								<?php  if(is_array($store['goods'])) { foreach($store['goods'] as $goods) { ?>
									<li><a href="<?php  echo $this->createMobileUrl('goods', array('sid' => $store['id'], 'from' => 'search', 'key' => $goods['title']));?>"><?php  echo $goods['title'];?><span>¥<?php  echo $goods['price'];?></span></a></li>
								<?php  } } ?>
							</ul>
						</div>
					<?php  } ?>
				</div>
			<?php  } } ?>
		<?php  } ?>
		<?php  if(!empty($recommend_stores)) { ?>
		<div class="store-list search-mar-top">
			<div class="search-r-like">
				<span><i class="fa fa-dianzan"></i>为您推荐</span>
			</div>
			<?php  if(is_array($recommend_stores)) { foreach($recommend_stores as $store) { ?>
				<div class="list-item">
					<a href="<?php  echo $store['url'];?>">
						<div class="store-info row no-gutter">
							<div class="store-img col-25">
								<img src="<?php  echo tomedia($store['logo']);?>" alt="<?php  echo $store['title'];?>"/>
							</div>
							<div class="col-75">
								<div class="row no-gutter">
									<div class="col-60 lineheight"><?php  echo $store['title'];?></div>
									<div class="col-40 money-info text-right"></div>
								</div>
								<div class="rel-info">
									<div class="row delivery-conditions">
										<div class="col-60">起送<span>￥<?php  echo $store['send_price'];?></span><span class="pipe">|</span>配送<span>￥<?php  echo $store['delivery_price'];?></span></div>
										<div class="col-40 textright color-danger"><?php  echo $store['delivery_time'];?>分钟</div>
									</div>
									<?php  if(!empty($store['activity']['discount_status'])) { ?>
										<div class="jian">
											在线支付
											<?php  if(is_array($store['activity']['discount_data'])) { foreach($store['activity']['discount_data'] as $discount) { ?>
											满<?php  echo $discount['condition'];?>元减<?php  echo $discount['back'];?>元
											<?php  } } ?>
										</div>
									<?php  } ?>
								</div>
							</div>
						</div>
					</a>
				</div>
			<?php  } } ?>
		</div>
		<?php  } ?>
	</div>
</div>
<script>
$(function(){
	$(document).on('click', '.searchbar-cancel', function(){
		var key = $('.search-input input').val();
		if(!key) {
			return false;
		}
		$.showIndicator();
		$.post("<?php  echo $this->createMobileUrl('hunt', array('op' => 'search_data'));?>", {key: key}, function(data){
			location.href = "<?php  echo $this->createMobileUrl('hunt', array('op' => 'search'));?>&key=" + key;
		});
		return false;
	});
});
</script>
<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('common', TEMPLATE_INCLUDEPATH)) : (include template('common', TEMPLATE_INCLUDEPATH));?>
<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('footer', TEMPLATE_INCLUDEPATH)) : (include template('footer', TEMPLATE_INCLUDEPATH));?>