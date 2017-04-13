<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('header', TEMPLATE_INCLUDEPATH)) : (include template('header', TEMPLATE_INCLUDEPATH));?>
<div class="page order" id="page-app-order">
	<header class="bar bar-nav">
		<a class="pull-left back" href="javascript:;"><i class="fa fa-arrow-left"></i></a>
		<h1 class="title">我的订单</h1>
	</header>
	<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('nav', TEMPLATE_INCLUDEPATH)) : (include template('nav', TEMPLATE_INCLUDEPATH));?>
	<div class="content infinite-scroll" data-distance="50" data-min="<?php  echo $min;?>">
		<div class="order-list">
			<?php  if(empty($orders)) { ?>
				<div class="common-no-con">
					<img src= "<?php echo MODULE_URL;?>resource/app/img/order_no_con.png" alt="" />
					<p>您还没有订单，赶紧点一份！</p>
					<div class="btn">
						<a href="<?php  echo $this->createMobileUrl('index');?>">现在点一份</a>
					</div>
				</div>
			<?php  } else { ?>
				<?php  if(is_array($orders)) { foreach($orders as $order) { ?>
					<div class="order-container">
						<div class="order-inner">
							<div class="store-info" style="position: relative">
								<a class="external" href="<?php  echo $this->createMobileUrl('goods', array('sid' => $order['sid']));?>">
									<img src="<?php  echo tomedia($order['logo']);?>" alt="" />
									<span class="store-title"><?php  echo $order['title'];?></span>
									<span class="fa fa-arrow-right"></span>
								</a>
								<?php  if($order['delivery_mode'] == 2) { ?>
									<div class="plateform-delivery"><span>平台专送</span></div>
								<?php  } ?>
							</div>
							<a class="goods-info row no-gutter external" href="<?php  echo $this->createMobileUrl('order', array('op' => 'detail', 'id' => $order['id']));?>">
								<div class="col-75">
									<div class="goods-title"><?php  echo $order['goods']['goods_title'];?>等<span><?php  echo $order['num'];?></span>件商品</div>
									<div class="date"><?php  echo date('Y-m-d H:i', $order['addtime']);?></div>
								</div>
								<div class="col-25 text-right">
									<div class="price">￥<?php  echo $order['final_fee'];?></div>
									<div class="status no-pay"><?php  echo $order_status[$order['status']]['text'];?></div>
								</div>
							</a>
							<?php  if(!$order['is_pay'] && $order['status'] != 6) { ?>
								<div class="order-status">
									<div class="pic">
										<img src="<?php echo MODULE_URL;?>resource/app/img/order_status_money.png" alt="" />
									</div>
									<div class="order-status-detail">
										<div class="arrow-left"></div>
										<div class="clearfix">待支付<span class="pull-right date"><?php  echo date('H:i', $order['addtime']);?></span></div>
										<div class="tips">请在提交订单后<?php  echo $order['pay_time_limit'];?>分钟内完成支付</div>
									</div>
								</div>
							<?php  } ?>
						</div>
						<div class="order-btn table">
							<?php  if(!$order['is_pay'] && !in_array($order['status'], array(5, 6))) { ?>
								<a href="<?php  echo $this->createMobileUrl('pay', array('id' => $order['id'], 'order_type' => 'order', 'type' => 1));?>" class="table-cell external">立即支付</a>
							<?php  } ?>
							<?php  if($order['status'] == 1) { ?>
								<a href="javascript:;" class="order-cancel table-cell" data-id="<?php  echo $order['id'];?>">取消订单</a>
								<?php  if($order['is_pay'] == 1) { ?>
									<a href="javascript:;" class="order-remind table-cell" data-id="<?php  echo $order['id'];?>">催单</a>
								<?php  } ?>
							<?php  } else if(in_array($order['status'], array(2, 3, 4))) { ?>
								<?php  if($order['order_type'] == 1) { ?>
									<a href="javascript:;" class="order-end table-cell" data-id="<?php  echo $order['id'];?>" data-type="1">确认送达</a>
								<?php  } else if($order['order_type'] == 2) { ?>
									<a href="javascript:;" class="order-end table-cell" data-id="<?php  echo $order['id'];?>" data-type="2">我已提货</a>
								<?php  } ?>
								<?php  if($order['is_pay'] == 1) { ?>
									<a href="javascript:;" class="order-remind table-cell" data-id="<?php  echo $order['id'];?>">催单</a>
								<?php  } ?>
							<?php  } else if(in_array($order['status'], array(5))) { ?>
								<a href="<?php  echo $this->createMobileUrl('goods', array('f' => '1', 'id' => $order['id'], 'sid' => $order['sid']));?>" class="table-cell external" data-id="<?php  echo $order['id'];?>">再来一单</a>
								<?php  if(!$order['is_comment']) { ?>
									<a href="<?php  echo $this->createMobileUrl('order', array('op' => 'comment', 'id' => $order['id']));?>" class="table-cell">去评价</a>
								<?php  } else { ?>
									<a href="<?php  echo $this->createMobileUrl('comment');?>" class="table-cell">查看评价</a>
								<?php  } ?>
							<?php  } else if(in_array($order['status'], array(6))) { ?>
								<a href="<?php  echo $this->createMobileUrl('goods', array('f' => '1', 'id' => $order['id'], 'sid' => $order['sid']));?>" class="table-cell external" data-id="<?php  echo $order['id'];?>">再来一单</a>
							<?php  } ?>
							<?php  if($order['is_refund'] == 1) { ?>
								<a href="<?php  echo $this->createMobileUrl('order', array('op' => 'detail', 'id' => $order['id']));?>" class="table-cell">查看退款</a>
							<?php  } ?>
						</div>
					</div>
				<?php  } } ?>
			<?php  } ?>
		</div>
		<div class="infinite-scroll-preloader hide">
			<div class="preloader"></div>
		</div>
	</div>
</div>
<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('common', TEMPLATE_INCLUDEPATH)) : (include template('common', TEMPLATE_INCLUDEPATH));?>
<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('footer', TEMPLATE_INCLUDEPATH)) : (include template('footer', TEMPLATE_INCLUDEPATH));?>