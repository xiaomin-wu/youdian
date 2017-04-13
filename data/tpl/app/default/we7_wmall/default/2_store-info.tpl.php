<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('header', TEMPLATE_INCLUDEPATH)) : (include template('header', TEMPLATE_INCLUDEPATH));?>
<div class="page store-detail" id="page-app-store">
	<header class="bar bar-nav">
		<a class="pull-left back" href="javascript:;"><i class="fa fa-arrow-left"></i></a>
		<h1 class="title">商户详情</h1>
		<a class="pull-right" href="javascript:;" id="btn-favorite" data-id="<?php  echo $store['id'];?>" data-uid="<?php  echo $_W['member']['uid'];?>">
			<i class="fa <?php  if(!empty($is_favorite)) { ?>fa-favor-fill<?php  } else { ?>fa-favor<?php  } ?>"></i>
		</a>
	</header>
	<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('nav', TEMPLATE_INCLUDEPATH)) : (include template('nav', TEMPLATE_INCLUDEPATH));?>
	<div class="content">
		<?php  if(!empty($store['thumbs'])) { ?>
		<div class="swiper-container swiper-container-horizontal" data-space-between='30' data-pagination='.swiper-pagination' data-autoplay="2000">
			<div class="swiper-wrapper">
				<?php  if(is_array($store['thumbs'])) { foreach($store['thumbs'] as $thumb) { ?>
				<div class="swiper-slide" data-link="<?php  echo $thumb['url'];?>">
					<img src="<?php  echo tomedia($thumb['image']);?>"alt="">
				</div>
				<?php  } } ?>
			</div>
			<div class="swiper-pagination"></div>
		</div>
		<?php  } ?>
		<div class="row no-gutter banner">
			<div class="col-33 text-center">
				<img src="<?php  echo tomedia($store['logo']);?>" alt="" class="logo"/>
			</div>
			<div class="col-67">
				<div class="goods-title"><?php  echo $store['title'];?></div>
				<div class="star-rank">
					<span class="star-rank-outline">
						<span class="star-rank-active" style="width:<?php  echo $store['score_cn'];?>%"></span>
						<span class="star-rank-value"><?php  echo $store['score'];?></span>
					</span>
				</div>
				<div class="sell-info">已售:<?php  echo $store['sailed'];?>份</div>
			</div>
		</div>
		<div class="row no-gutter delivery-info">
			<div class="col-33">起送价￥<?php  echo $store['send_price'];?></div>
			<div class="col-33">配送￥<?php  echo $store['delivery_price'];?></div>
			<div class="col-33">送达时长<?php  echo $store['delivery_time'];?>分钟</div>
		</div>
		<div class="grid-nav grid-money">
			<div class="grid-money-title">
				商家服务
			</div>
			<div class="row no-gutter">
				<div class="col-25">
					<a href="<?php  echo $this->createMobileUrl('goods', array('sid' => $sid));?>" class="external">
						<i class="fa fa-takeout"></i>
						<span>点外卖</span>
					</a>
				</div>
				<?php  if($store['is_meal'] == 1) { ?>
					<div class="col-25">
						<a href="javascript:;" id="scanqrcode">
							<i class="fa fa-meal"></i>
							<span>堂食</span>
						</a>
					</div>
				<?php  } ?>
				<?php  if($store['is_reserve'] == 1) { ?>
					<div class="col-25">
						<a href="<?php  echo $this->createMobileUrl('reserve', array('sid' => $sid));?>" class="external">
							<i class="fa fa-reserve"></i>
							<span>预定</span>
						</a>
					</div>
				<?php  } ?>
				<?php  if($store['is_assign'] == 1) { ?>
					<div class="col-25">
						<a href="<?php  echo $this->createMobileUrl('assign', array('sid' => $sid));?>" class="external">
							<i class="fa fa-assign"></i>
							<span>排号</span>
						</a>
					</div>
				<?php  } ?>
			</div>
		</div>
		<?php  if(!empty($store['custom_url'])) { ?>
		<div class="list-block">
			<ul>
				<?php  if(is_array($store['custom_url'])) { foreach($store['custom_url'] as $row) { ?>
				<li>
					<a href="<?php  echo $row['url'];?>" class="item-content item-link external">
						<div class="item-inner">
							<div class="item-title">
								<img src="<?php echo MODULE_URL;?>resource/app/img/goods.png" alt="" />
								<?php  echo $row['title'];?>
							</div>
						</div>
					</a>
				</li>
				<?php  } } ?>
			</ul>
		</div>
		<?php  } ?>
		<div class="list-block">
			<ul>
				<li>
					<div class="item-content">
						<div class="item-inner">
							<div class="item-title">
								<img src="<?php echo MODULE_URL;?>resource/app/img/clock-grey.png" alt="" />
								<?php  echo $store['business_hours_cn'];?>
							</div>
						</div>
					</div>
				</li>
				<li>
					<a href="http://m.amap.com/?q=<?php  echo $store['location_x'];?>,<?php  echo $store['location_y'];?>&name=<?php  echo $store['address'];?>" class="item-content item-link external">
						<div class="item-inner">
							<div class="item-title">
								<img src="<?php echo MODULE_URL;?>resource/app/img/location-grey.png" alt="" />
								<?php  echo $store['address'];?>
							</div>
						</div>
					</a>
				</li>
				<li>
					<a href="tel:<?php  echo $store['telephone'];?>" class="item-content item-link external">
						<div class="item-inner">
							<div class="item-title">
								<img src="<?php echo MODULE_URL;?>resource/app/img/tel-grey.png" alt="" />
								<?php  echo $store['telephone'];?>
							</div>
						</div>
					</a>
				</li>
				<?php  if(!empty($store['sns']['qq'])) { ?>
				<li>
					<a href="http://wpa.qq.com/msgrd?v=3&uin=<?php  echo $store['sns']['qq'];?>&site=qq&menu=yes" class="item-content item-link external">
						<div class="item-inner">
							<div class="item-title">
								<span><i class="fa fa-qq"></i></span>
								<?php  echo $store['sns']['qq'];?>
							</div>
						</div>
					</a>
				</li>
				<?php  } ?>
				<?php  if(!empty($store['sns']['weixin'])) { ?>
				<li>
					<a href="javascript:;" class="item-content item-link external">
						<div class="item-inner">
							<div class="item-title">
								<span><i class="fa fa-weixin"></i></span>
								<?php  echo $store['sns']['weixin'];?>
							</div>
						</div>
					</a>
				</li>
				<?php  } ?>
				<?php  if(!empty($store['notice'])) { ?>
				<li>
					<div class="item-content">
						<div class="item-inner">
							<div class="item-title text">
								<img src="<?php echo MODULE_URL;?>resource/app/img/voice-grey.png" alt="" />
								<?php  echo $store['notice'];?>
							</div>
						</div>
					</div>
				</li>
				<?php  } ?>
				<?php  if(!empty($store['description'])) { ?>
				<li>
					<a href="javascript:;" class="item-content item-link external open-popup" data-popup=".popup-store-description">
						<div class="item-inner">
							<div class="item-title">
								<span><i class="fa fa-weixin"></i></span>
								门店特色
							</div>
						</div>
					</a>
				</li>
				<?php  } ?>
			</ul>
		</div>
		<div class="list-block">
			<ul>
				<?php  if($activity['first_order_status'] == 1) { ?>
					<li>
						<div class="item-content">
							<div class="item-inner">
								<div class="item-title">
									<img src="<?php echo MODULE_URL;?>resource/app/img/xin_b.png" alt="" />
									新用户在线支付
									<?php  if(is_array($activity['first_order_data'])) { foreach($activity['first_order_data'] as $first) { ?>
									满<?php  echo $first['condition'];?>元减<?php  echo $first['back'];?>
									<?php  } } ?>
								</div>
							</div>
						</div>
					</li>
				<?php  } ?>
				<?php  if($activity['discount_status'] == 1) { ?>
					<li>
						<div class="item-content">
							<div class="item-inner">
								<div class="item-title">
									<img src="<?php echo MODULE_URL;?>resource/app/img/jian_b.png" alt="" />
									在线支付
									<?php  if(is_array($activity['discount_data'])) { foreach($activity['discount_data'] as $discount) { ?>
									满<?php  echo $discount['condition'];?>元减<?php  echo $discount['back'];?>
									<?php  } } ?>
								</div>
							</div>
						</div>
					</li>
				<?php  } ?>
				<?php  if($activity['grant_status'] == 1) { ?>
				<li>
					<div class="item-content">
						<div class="item-inner">
							<div class="item-title">
								<img src="<?php echo MODULE_URL;?>resource/app/img/zeng_b.png" alt="" />
								在线支付
								<?php  if(is_array($activity['grant_data'])) { foreach($activity['grant_data'] as $grant) { ?>
								满<?php  echo $grant['condition'];?>元赠<?php  echo $grant['back'];?>
								<?php  } } ?>
							</div>
						</div>
					</div>
				</li>
				<?php  } ?>
				<?php  if($activity['collect_coupon_status'] == 1) { ?>
				<li>
					<div class="item-content">
						<div class="item-inner">
							<div class="item-title">
								<img src="<?php echo MODULE_URL;?>resource/app/img/coupon_b.png" alt="" />
								进店可领取代金券
							</div>
						</div>
					</div>
				</li>
				<?php  } ?>
				<?php  if($store['delivery_free_price'] > 0) { ?>
				<li>
					<div class="item-content">
						<div class="item-inner">
							<div class="item-title">
								<img src="<?php echo MODULE_URL;?>resource/app/img/mian_b.png" alt="" />
								下单满<?php  echo $store['delivery_free_price'];?>元免配送费
							</div>
						</div>
					</div>
				</li>
				<?php  } ?>
			</ul>
		</div>
		<div class="list-block">
			<ul>
				<li>
					<div class="item-content">
						<div class="item-inner">
							<div class="item-title">
								<img src="<?php echo MODULE_URL;?>resource/app/img/pay_b.png" alt="" />
								支持在线支付
							</div>
						</div>
					</div>
				</li>
				<?php  if($store['invoice_status'] == 1) { ?>
				<li>
					<div class="item-content">
						<div class="item-inner">
							<div class="item-title">
								<img src="<?php echo MODULE_URL;?>resource/app/img/invoice_b.png" alt="" />
								支持使用代金券抵付现金
							</div>
						</div>
					</div>
				</li>
				<?php  } ?>
			</ul>
		</div>
		<div class="report">
			<a href="<?php  echo $this->createMobileUrl('report', array('sid' => $sid));?>">举报商家</a>
		</div>
	</div>
</div>

<div class="popup popup-store-description">
	<div class="page">
		<header class="bar bar-nav common-bar-nav">
			<h1 class="title">门店特色</h1>
			<button class="button button-link button-nav pull-right close-popup">关闭</button>
		</header>
		<div class="content" style="background: #FFF">
			<div class="content-padded">
				<?php  echo $store['description'];?>
			</div>
		</div>
	</div>
</div>

<script>
$(function(){
	$(document).on('click', '.swiper-slide', function(){
		var url = $(this).data('link');
		if(url) {
			location.href = url;
		}
	});
	$(document).on('click', '#scanqrcode', function(){
		$.confirm("如果您已经到店,请点击'扫码下单'并扫描桌子上的二维码进行店内下单", function(){
			wx.ready(function(){
				wx.scanQRCode({
					needResult: 0, // 默认为0，扫描结果由微信处理，1则直接返回扫描结果，
					scanType: ["qrCode","barCode"], // 可以指定扫二维码还是一维码，默认二者都有
					success: function (res) {
						var result = res.resultStr; // 当needResult 为 1 时，扫码返回的结果
					}
				});
			});
		});
	});
});
</script>
<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('common', TEMPLATE_INCLUDEPATH)) : (include template('common', TEMPLATE_INCLUDEPATH));?>
<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('footer', TEMPLATE_INCLUDEPATH)) : (include template('footer', TEMPLATE_INCLUDEPATH));?>