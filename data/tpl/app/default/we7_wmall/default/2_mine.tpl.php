<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('header', TEMPLATE_INCLUDEPATH)) : (include template('header', TEMPLATE_INCLUDEPATH));?>
<div class="page my-page" id="page-app-mine">
	<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('nav', TEMPLATE_INCLUDEPATH)) : (include template('nav', TEMPLATE_INCLUDEPATH));?>
	<div class="content">
		<div class="banner">
			<div class="avatar">
				<?php  if(!empty($user['avatar'])) { ?>
					<img src="<?php  echo tomedia($user['avatar']);?>" alt="">
				<?php  } else { ?>
					<img src="<?php echo MODULE_URL;?>resource/app/img/head.png" alt="">
				<?php  } ?>
			</div>
			<div class="name">
				<a href="<?php  echo $this->createMobileUrl('card');?>">
					<?php  echo $user['nickname'];?>
					<?php  if($user['setmeal_id'] > 0 && $user['setmeal_endtime'] > TIMESTAMP) { ?>
					<img src="<?php echo MODULE_URL;?>resource/app/img/vip_effective.png" alt="">
					<?php  } else { ?>
					<img src="<?php echo MODULE_URL;?>resource/app/img/vip_deprecated.png" alt="">
					<?php  } ?>
				</a>
			</div>
			<div class="table activity-nav">
				<div class="table-cell">
					<a href="<?php  echo $this->createMobileUrl('coupon', array('op' => 'list'));?>">
						<div class="count"><?php  echo $coupon_nums;?></div>
						<div class="">代金券</div>
					</a>
				</div>
				<div class="table-cell">
					<a href="<?php  echo $this->createMobileUrl('favorite');?>">
						<div class="count"><?php  echo $favorite;?></div>
						<div class="">收藏店铺</div>
					</a>
				</div>
				<div class="table-cell">
					<a href="<?php  if($_W['member']['is_sys'] != 2) { ?><?php  echo murl('entry/recharge/pay', array('m' => 'recharge'));?><?php  } else { ?>javascript:;<?php  } ?>">
						<div class="count"><?php  echo floatval($user['credit2'])?></div>
						<div class="">余额</div>
					</a>
				</div>
				<div class="table-cell">
					<a href="<?php  echo murl('entry/recharge/pay', array('m' => 'recharge'));?>">
						<div class="count"><?php  echo floatval($user['credit1'])?></div>
						<div class="">积分</div>
					</a>
				</div>
			</div>
		</div>
		<div class="grid-nav grid-money border-1px-tb">
			<div class="grid-money-title border-1px-b">
				商家管理
				<?php  if($_W['we7_wmall']['config']['version'] == 1 && $settle_config['status'] == 1) { ?>
					<a href="<?php  echo $this->createMobileUrl('settle');?>">商家入驻,轻松提现</a> <i class="fa fa-arrow-right"></i>
				<?php  } ?>
			</div>
			<div class="row no-gutter">
				<div class="col-33">
					<a href="<?php  echo $this->createMobileUrl('mgindex');?>" class="external">
						<img src="<?php echo MODULE_URL;?>resource/app/img/mypage_messages.png" alt="" />
						<span>店员入口</span>
					</a>
				</div>
				<div class="col-33">
					<a href="<?php  echo $this->createMobileUrl('dyindex');?>">
						<img src="<?php echo MODULE_URL;?>resource/app/img/mypage_messages.png" alt="" />
						<span>配送员入口</span>
					</a>
				</div>
				<?php  if($_W['we7_wmall']['config']['version'] == 1 && $settle_config['status'] == 1) { ?>
					<div class="col-33">
						<a href="<?php  echo $this->createMobileUrl('settle');?>">
							<img src="<?php echo MODULE_URL;?>resource/app/img/mypage_settle.png" alt="" />
							<span>商户入驻</span>
						</a>
					</div>
				<?php  } ?>
			</div>
		</div>
		<div class="grid-nav">
			<div class="row no-gutter">
				<?php  if($delivery_config['card_apply_status'] == 1) { ?>
					<div class="col-25">
						<a href="<?php  echo $this->createMobileUrl('card');?>" class="border-1px-r">
							<img src="<?php echo MODULE_URL;?>resource/app/img/mypage_vip.png" alt="" />
							<span>配送会员卡</span>
						</a>
					</div>
				<?php  } ?>
				<div class="col-25 hide">
					<a href="<?php  echo $this->createMobileUrl('share');?>" class="border-1px-r">
						<img src="<?php echo MODULE_URL;?>resource/app/img/mypage_token.png" alt="" />
						<span>分享有礼</span>
					</a>
				</div>
				<?php  if($_W['member']['is_sys'] != 2) { ?>
					<div class="col-25">
						<a href="<?php  echo murl('entry/recharge/pay', array('m' => 'recharge'));?>" class="border-1px-r">
							<img src="<?php echo MODULE_URL;?>resource/app/img/mypage_credit.png" alt="" />
							<span>余额充值</span>
						</a>
					</div>
				<?php  } ?>
				<div class="col-25">
					<a href="<?php  echo $this->createMobileUrl('coupon');?>" class="border-1px-r">
						<img src="<?php echo MODULE_URL;?>resource/app/img/mypage_token.png" alt="" />
						<span>我的代金券</span>
					</a>
				</div>
				<div class="col-25">
					<a href="<?php  echo $this->createMobileUrl('address');?>" class="border-1px-r">
						<img src="<?php echo MODULE_URL;?>resource/app/img/mypage_location.png" alt="" />
						<span>我的地址</span>
					</a>
				</div>
				<div class="col-25 border-1px-t">
					<a href="<?php  echo $this->createMobileUrl('favorite');?>" class="border-1px-r">
						<img src="<?php echo MODULE_URL;?>resource/app/img/mypage_star.png" alt="" />
						<span>我的收藏</span>
					</a>
				</div>
				<div class="col-25 border-1px-t">
					<a href="<?php  echo $this->createMobileUrl('comment');?>" class="border-1px-r">
						<img src="<?php echo MODULE_URL;?>resource/app/img/mypage_comment.png" alt="" />
						<span>我的评价</span>
					</a>
				</div>
				<div class="col-25 border-1px-t">
					<a href="<?php  echo $this->createMobileUrl('help');?>" class="border-1px-r">
						<img src="<?php echo MODULE_URL;?>resource/app/img/mypage_problem.png" alt="" />
						<span>常见问题</span>
					</a>
				</div>
				<div class="col-25 border-1px-t">
					&nbsp;
				</div>
			</div>
		</div>
		<div class="service-tel">
			<a href="tel:<?php  echo $_W['we7_wmall']['config']['mobile'];?>" class="color-danger border-1px-tb">客服热线: <?php  echo $_W['we7_wmall']['config']['mobile'];?></a>
		</div>
	</div>
</div>
<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('common', TEMPLATE_INCLUDEPATH)) : (include template('common', TEMPLATE_INCLUDEPATH));?>
<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('footer', TEMPLATE_INCLUDEPATH)) : (include template('footer', TEMPLATE_INCLUDEPATH));?>