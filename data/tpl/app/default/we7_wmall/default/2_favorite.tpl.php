<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('header', TEMPLATE_INCLUDEPATH)) : (include template('header', TEMPLATE_INCLUDEPATH));?>
<div class="page home" id="page-app-my-favotite">
	<header class="bar bar-nav common-bar-nav">
		<a class="pull-left icon fa fa-arrow-left back" href="javascript:;"></a>
		<h1 class="title">我的收藏</h1>
	</header>
	<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('nav', TEMPLATE_INCLUDEPATH)) : (include template('nav', TEMPLATE_INCLUDEPATH));?>
	<div class="content">
		<div class="store-list" <?php  if(empty($stores)) { ?>style="position:relative; background: #eee"<?php  } ?>>
		<?php  if(empty($stores)) { ?>
			<div class="common-no-con">
				<img src= "<?php echo MODULE_URL;?>resource/app/img/store_no_con.png" alt="" />
				<p>您还没有收藏过店铺呢！</p>
			</div>
		<?php  } else { ?>
			<?php  if(is_array($stores)) { foreach($stores as $store) { ?>
				<div class="list-item sborder">
					<a href="<?php  echo $store['url'];?>" class="external">
						<div class="store-info row no-gutter">
							<div class="store-img col-25">
								<img src="<?php  echo tomedia($store['logo']);?>" alt="<?php  echo $store['title'];?>">
							</div>
							<div class="col-75">
								<div class="row no-gutter">
									<div class="col-60 text-ellipsis"><?php  echo $store['title'];?></div>
									<div class="col-40 money-info text-right">
										<?php  if($store['token_status'] == 1) { ?>
											<span>券</span>
										<?php  } ?>
										<?php  if($store['invoice_status'] == 1) { ?>
											<span>票</span>
										<?php  } ?>
										<span>付</span>
									</div>
								</div>
								<div class="rel-info">
									<div class="row no-gutter">
										<div class="col-60">
											<?php  if($store['is_in_business_hours']) { ?>
												<div class="star-rank">
													<span class="star-rank-outline">
														<span class="star-rank-active" style="width:<?php  echo $store['score_cn'];?>%"></span>
														<span class="star-rank-value"><?php  echo $store['score'];?></span>
													</span>
													<span class="sailed">已售 <?php  echo $store['sailed'];?> 份</span>
												</div>
											<?php  } else { ?>
												<div class="order-status">
													<span class="badge bg-default">店铺休息中</span>
												</div>
											<?php  } ?>
										</div>
										<?php  if($store['delivery_mode'] == 2) { ?>
											<div class="plateform-delivery"><span>平台专送</span></div>
										<?php  } ?>
									</div>
									<div class="delivery-conditions">
										起送￥<?php  echo $store['send_price'];?><span class="pipe">|</span>配送￥<?php  echo $store['delivery_price'];?><span class="pipe">|</span>约<?php  echo $store['delivery_time'];?>分钟
									</div>
								</div>
							</div>
						</div>
					</a>
					<div class="activity-containter">
						<?php  if($store['activity']['activity_num'] > 0) { ?>
							<div class="dashed-line"></div>
						<?php  } ?>
						<?php  if($store['activity']['activity_num'] > 2) { ?>
							<div class="activity-num"><?php  echo $store['activity']['activity_num'];?>个活动 <i class="fa fa-arrow-down"></i></div>
						<?php  } ?>
						<?php  $num = 0;?>
						<?php  if($store['activity']['first_order_status'] == 1) { ?>
							<?php  $num++;?>
							<div class="xin">
								新用户在线支付
								<?php  if(is_array($store['activity']['first_order_data'])) { foreach($store['activity']['first_order_data'] as $first) { ?>
								满<?php  echo $first['condition'];?>元减<?php  echo $first['back'];?>,
								<?php  } } ?>
							</div>
						<?php  } ?>
						<?php  if($store['activity']['discount_status'] == 1) { ?>
							<?php  $num++;?>
							<div class="minus">
								在线支付
								<?php  if(is_array($store['activity']['discount_data'])) { foreach($store['activity']['discount_data'] as $discount) { ?>
								满<?php  echo $discount['condition'];?>元减<?php  echo $discount['back'];?>,
								<?php  } } ?>
							</div>
						<?php  } ?>
						<?php  if($store['activity']['grant_status'] == 1) { ?>
							<?php  $num++;?>
							<div class="activity-row zeng <?php  if($num > 2) { ?>hide<?php  } ?>">
								在线支付
								<?php  if(is_array($store['activity']['grant_data'])) { foreach($store['activity']['grant_data'] as $grant) { ?>
								满<?php  echo $grant['condition'];?>元赠<?php  echo $grant['back'];?>,
								<?php  } } ?>
							</div>
						<?php  } ?>
						<?php  if($store['activity']['collect_coupon_status'] == 1) { ?>
							<?php  $num++;?>
							<div class="activity-row coupon <?php  if($num > 2) { ?>hide<?php  } ?>">
								进店可领取代金券
							</div>
						<?php  } ?>
						<?php  if($store['delivery_free_price'] > 0) { ?>
							<?php  $num++;?>
							<div class="activity-row free <?php  if($num > 2) { ?>hide<?php  } ?>">
								满<?php  echo $store['delivery_free_price'];?>元免配送费
							</div>
						<?php  } ?>
						<?php  if(!empty($store['hot_goods'])) { ?>
							<div class="dashed-line"></div>
							<div class="hot">
								热销:
								<?php  if(is_array($store['hot_goods'])) { foreach($store['hot_goods'] as $good) { ?>
								<?php  echo $good['title'];?>
								<?php  } } ?>
							</div>
						<?php  } ?>
					</div>
				</div>
			<?php  } } ?>
		<?php  } ?>
		</div>
	</div>
</div>


<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('common', TEMPLATE_INCLUDEPATH)) : (include template('common', TEMPLATE_INCLUDEPATH));?>
<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('footer', TEMPLATE_INCLUDEPATH)) : (include template('footer', TEMPLATE_INCLUDEPATH));?>