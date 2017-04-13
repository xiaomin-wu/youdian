<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('header', TEMPLATE_INCLUDEPATH)) : (include template('header', TEMPLATE_INCLUDEPATH));?>
<div class="page store shopcategory" id="page-app-store-comment">
	<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('nav', TEMPLATE_INCLUDEPATH)) : (include template('nav', TEMPLATE_INCLUDEPATH));?>
	<header class="bar bar-nav common-bar-nav">
		<a class="pull-left back" href="javascript:;"><i class="fa fa-arrow-left"></i></a>
		<h1 class="title open-popup" data-popup=".popup-privilege"><?php  echo $store['title'];?></h1>
		<a class="pull-right" href="javascript:;" id="btn-favorite" data-id="<?php  echo $store['id'];?>" data-uid="<?php  echo $_W['member']['uid'];?>">
			<i class="fa <?php  if(!empty($is_favorite)) { ?>fa-favor-fill<?php  } else { ?>fa-favor<?php  } ?>"></i>
		</a>
	</header>
	<div class="store-notice open-popup" data-popup=".popup-privilege">
		<span class="js-scroll-notice">
			<span class="fa fa-voice"></span>
			<?php  if(!empty($store['notice'])) { ?>
				<?php  echo $store['notice'];?>
			<?php  } else { ?>
				营业时间: <?php  echo $store['business_hours_cn'];?>
			<?php  } ?>
		</span>
	</div>
	<div class="buttons-tab">
		<a href="<?php  echo $this->createMobileUrl('goods', array('sid' => $store['id']));?>" class="button">商品</a>
		<a href="<?php  echo $this->createMobileUrl('store', array('sid' => $store['id'], 'op' => 'comment'));?>" class="button active">评价</a>
		<a href="<?php  echo $this->createMobileUrl('store', array('sid' => $store['id']));?>" class="button">商家</a>
		<a href="http://wpa.qq.com/msgrd?v=3&uin=<?php  echo $store['sns']['qq'];?>&site=qq&menu=yes" class="button">客服</a>
	</div>
	<div class="content infinite-scroll" data-distance="50" data-min="<?php  echo $min;?>" data-type="<?php  echo $type;?>" data-sid="<?php  echo $sid;?>">
		<?php  if(empty($comments)) { ?>
		<div class="common-no-con">
			<img src="<?php echo MODULE_URL;?>resource/app/img/comment_no_con.png" alt="" />
			<p>这个店铺还没有评价！</p>
		</div>
		<?php  } else { ?>
		<div id="comment">
			<div class="table comment-nav">
				<div class="table-cell">
					<a href="">
						<div class="count"><?php  echo $stat['score'];?></div>
						<div class="">整体评价</div>
					</a>
				</div>
				<div class="table-cell">
					<a href="">
						<div class="count"><?php  echo $stat['goods_quality'];?></div>
						<div class="">商品质量</div>
					</a>
				</div>
				<div class="table-cell">
					<a href="">
						<div class="count"><?php  echo $stat['delivery_service'];?></div>
						<div class="">配送服务</div>
					</a>
				</div>
			</div>
			<div class="comment-list">
				<div class="list-item-top">
					<div class="btn-tab row no-gutter">
						<div class="col-25"><span <?php  if(!$type) { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createMobileUrl('store', array('op' => 'comment', 'type' => 0, 'sid' => $store['id']));?>">全部(<?php  echo $stat['all'];?>)</a></span></div>
						<div class="col-25"><span <?php  if($type == 1) { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createMobileUrl('store', array('op' => 'comment', 'type' => 1, 'sid' => $store['id']));?>">好评(<?php  echo $stat['good'];?>)</a></span></div>
						<div class="col-25"><span <?php  if($type == 2) { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createMobileUrl('store', array('op' => 'comment', 'type' => 2, 'sid' => $store['id']));?>">中评(<?php  echo $stat['middle'];?>)</a></span></div>
						<div class="col-25"><span <?php  if($type == 3) { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createMobileUrl('store', array('op' => 'comment', 'type' => 3, 'sid' => $store['id']));?>">差评(<?php  echo $stat['bad'];?>)</a></span></div>
					</div>
					<div class="content-padded"></div>
				</div>
				<div class="list-block media-list">
					<ul>
						<?php  if(is_array($comments)) { foreach($comments as $comment) { ?>
						<li>
							<a href="javascript:;" class="item-link item-content">
								<div class="item-media">
									<img src="<?php  echo $comment['avatar'];?>" alt="">
								</div>
								<div class="item-inner">
									<div class="item-title-row">
										<div class="item-title"><?php  echo $comment['mobile'];?></div>
										<div class="item-after"><?php  echo $comment['addtime'];?></div>
									</div>
									<div class="item-text">
										<div>
											<div class="star-rank">
												<span class="star-rank-outline">
													<span class="star-rank-active" style="width:<?php  echo $comment['score'];?>%"></span>
												</span>
											</div>
											<span class="color-muted hide">送货速度:40分钟</span>
										</div>
										<?php  if(!empty($comment['note'])) { ?>
											<div class="comment-info"><?php  echo $comment['note'];?></div>
										<?php  } ?>
										<?php  if(!empty($comment['data']['good'])) { ?>
											<div class="comment-favor-oppose">
												<i class="icon favor"></i>
												<?php  if(is_array($comment['data']['good'])) { foreach($comment['data']['good'] as $good) { ?>
													<span><?php  echo $good;?></span>
												<?php  } } ?>
											</div>
										<?php  } ?>
										<?php  if(!empty($comment['thumbs'])) { ?>
											<div class="comment-images-containter row">
												<?php  if(is_array($comment['thumbs'])) { foreach($comment['thumbs'] as $thumb) { ?>
												<div class="col-33 comment-images-item">
													<img src="<?php  echo $thumb;?>" alt=""/>
												</div>
												<?php  } } ?>
											</div>
										<?php  } ?>
										<?php  if(!empty($comment['reply'])) { ?>
											<div class="store-comment">
												<div class="clearfix store-comment-top">
													店家回复<span class="pull-right"><?php  echo $comment['replytime'];?></span>
												</div>
												<div class="info"><?php  echo $comment['reply'];?></div>
											</div>
										<?php  } ?>
									</div>
								</div>
							</a>
						</li>
						<?php  } } ?>
					</ul>
				</div>
			</div>
			<div class="infinite-scroll-preloader hide">
				<div class="preloader"></div>
			</div>
		</div>
		<?php  } ?>
	</div>
</div>
<div class="popup popup-privilege">
	<div class="popup-opacity">
		<div class="content-block">
			<div class="store-name"><?php  echo $store['title'];?></div>
			<div class="star-rank">
				<span class="star-rank-outline">
					<span class="star-rank-active" style="width:<?php  echo $store['score_cn'];?>%"></span>
					<span class="star-rank-value"><?php  echo $store['score'];?></span>
				</span>
			</div>
			<div class="sell-info">已售<?php  echo $store['sailed'];?>份&nbsp;&nbsp;营业时间: <?php  echo $store['business_hours_cn'];?></div>
			<div class="evaluate">优惠活动</div>
			<?php  if($activity['first_order_status'] == 1) { ?>
			<div class="xin text-left">
				新用户在线支付
				<?php  if(is_array($activity['first_order_data'])) { foreach($activity['first_order_data'] as $first) { ?>
				满<?php  echo $first['condition'];?>元减<?php  echo $first['back'];?>元,
				<?php  } } ?>
			</div>
			<?php  } ?>
			<?php  if($activity['discount_status'] == 1) { ?>
			<div class="minus text-left">
				在线支付
				<?php  if(is_array($activity['discount_data'])) { foreach($activity['discount_data'] as $discount) { ?>
				满<?php  echo $first['condition'];?>元减<?php  echo $discount['back'];?>元,
				<?php  } } ?>
			</div>
			<?php  } ?>
			<?php  if($activity['grant_status'] == 1) { ?>
			<div class="zeng text-left">
				在线支付
				<?php  if(is_array($activity['grant_data'])) { foreach($activity['grant_data'] as $grant) { ?>
				满<?php  echo $grant['$discount'];?>元赠<?php  echo $grant['back'];?>,
				<?php  } } ?>
			</div>
			<?php  } ?>
			<?php  if($store['collect_coupon_status'] == 1) { ?>
			<div class="coupon text-left">
				进店可领取代金券
			</div>
			<?php  } ?>
			<?php  if($store['delivery_free_price'] > 0) { ?>
			<div class="free text-left">
				满<?php  echo $store['delivery_free_price'];?>元免配送费
			</div>
			<?php  } ?>
			<div class="announcement">商家公告</div>
			<div class="announcement-con">
				<?php  if(!empty($store['notice'])) { ?>
				<?php  echo $store['notice'];?><br>
				<?php  } ?>
				本店欢迎您下单，用餐高峰请提前下单，谢谢！
			</div>
			<p><a href="#" class="close-popup"><span class="fa fa-close"></span></a></p>
		</div>
	</div>
</div>
<script>
$(function(){
	var left = 0, notice = $(this).find('.js-scroll-notice');
	setInterval(function(){
		left--;
		0 > left + notice.width() && (left = notice.width());
		notice.css({
			'left': left
		});
	}, 25);
});
</script>
<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('common', TEMPLATE_INCLUDEPATH)) : (include template('common', TEMPLATE_INCLUDEPATH));?>
<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('footer', TEMPLATE_INCLUDEPATH)) : (include template('footer', TEMPLATE_INCLUDEPATH));?>