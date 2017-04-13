<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('manage/header', TEMPLATE_INCLUDEPATH)) : (include template('manage/header', TEMPLATE_INCLUDEPATH));?>
<div class="page" id="page-manage-order">
	<header class="bar bar-nav common-bar-nav">
		<a class="icon pull-left fa fa-arrow-left back"></a>
		<h1 class="title">订单管理</h1>
	</header>
	<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('manage/nav', TEMPLATE_INCLUDEPATH)) : (include template('manage/nav', TEMPLATE_INCLUDEPATH));?>
	<div class="content infinite-scroll" data-distance="50" data-min="<?php  echo $min;?>">
		<div class="buttons-tab">
			<a href="<?php  echo $this->createMobileUrl('mgorder', array('status' => 1));?>" class="button <?php  if($status == 1) { ?>active<?php  } ?>">待确认</a>
			<a href="<?php  echo $this->createMobileUrl('mgorder', array('status' => 2));?>" class="button <?php  if($status == 2) { ?>active<?php  } ?>">处理中</a>
			<a href="<?php  echo $this->createMobileUrl('mgorder', array('status' => 3));?>" class="button <?php  if($status == 3) { ?>active<?php  } ?>">待配送</a>
			<a href="<?php  echo $this->createMobileUrl('mgorder', array('status' => 4));?>" class="button <?php  if($status == 4) { ?>active<?php  } ?>">配送中</a>
			<a href="javascript:;" class="button <?php  if(in_array($status, array(5, 6, 0))) { ?>active<?php  } ?> open-popover" data-popover=".popover-order-status"><?php  if(in_array($status, array(5, 6, 0))) { ?><?php  echo $order_status[$status]['text'];?><?php  } else { ?>更多<?php  } ?> <i class="fa fa-arrow-down"></i></a>
		</div>
		<?php  if(empty($orders)) { ?>
			<div class="no-data">
				<div class="bg"></div>
				<p>没有任何订单哦～</p>
			</div>
		<?php  } else { ?>
		<div class="order-list">
			<ul>
				<?php  if(is_array($orders)) { foreach($orders as $order) { ?>
					<li>
						<a class="order-ls-info" href="<?php  echo $this->createMobileUrl('mgorder', array('op' => 'detail', 'id' => $order['id']));?>">
							<div class="order-ls-tl">下单人:<?php  echo $order['username'];?><span class="<?php  echo $order_status[$order['status']]['color'];?>"><?php  echo $order_status[$order['status']]['text'];?></span></div>
							<div class="order-ls-date"><?php  echo date('Y-m-d H:i:s', $order['addtime']);?><span>编号: <?php  echo $order['id'];?></span></div>
							<div class="order-ls-dl">
								<?php  if(is_array($order['goods'])) { foreach($order['goods'] as $good) { ?>
								<div class="row">
									<div class="col-60"><?php  echo $good['goods_title'];?></div>
									<div class="col-20 align-right">X <?php  echo $good['goods_num'];?></div>
									<div class="col-20 align-right">¥<?php  echo $good['goods_price'];?></div>
								</div>
								<?php  } } ?>
								<div class="row">
									<div class="col-60">配送费¥<?php  echo $order['delivery_fee'];?> + 包装费¥<?php  echo $order['pack_fee'];?></div>
									<div class="col-40 align-right">¥<?php  echo $order['total_fee'];?>元</div>
								</div>
							</div>
							<div class="order-ls-sum">
								共<?php  echo $order['num'];?>件，合计：¥<?php  echo $order['final_fee'];?>
								<span class="color-danger">(已优惠¥<?php  echo $order['discount_fee'];?>)</span>
								<span class="pull-right color-primary order-ls-dist hide" data-lat="<?php  echo $order['location_x'];?>" data-lng="<?php  echo $order['location_y'];?>"></span>
							</div>
						</a>
						<div class="order-ls-btn">
							<?php  if($order['status'] < 5) { ?>
								<?php  if($order['status'] == 1) { ?>
									<a href="javascript:;" class="order-status" data-id="<?php  echo $order['id'];?>" data-status="2" data-type="handel"><i class="fa fa-selected"></i> 确认接单</a>
									<a href="javascript:;" class="order-cancel" data-id="<?php  echo $order['id'];?>" data-status="6" data-pay="<?php  echo $order['is_pay'];?>" data-pay-type="<?php  echo $order['pay_type'];?>"><i class="fa fa-selected"></i> 取消订单</a>
								<?php  } else if($order['status'] == 2 || $order['status'] == 3) { ?>
									<a href="javascript:;" class="order-status" data-id="<?php  echo $order['id'];?>" data-status="3" data-type="delivery_wait"><i class="fa fa-selected"></i> 通知配送员配送</a>
									<?php  if($account['delivery_type'] == 1) { ?>
										<a href="javascript:;" class="order-delivery" data-id="<?php  echo $order['id'];?>"><i class="fa fa-selected"></i> 指定配送员配送</a>
									<?php  } ?>
									<a href="javascript:;" class="order-status" data-id="<?php  echo $order['id'];?>" data-status="4" data-type="delivery_ing"><i class="fa fa-selected"></i> 设为配送中</a>
								<?php  } else if($order['status'] == 4) { ?>
									<a href="javascript:;" class="order-status" data-id="<?php  echo $order['id'];?>" data-status="5" data-type="end"><i class="fa fa-selected"></i> 订单完成</a>
								<?php  } ?>
							<?php  } ?>
						</div>
					</li>
				<?php  } } ?>
			</ul>
			<div class="infinite-scroll-preloader hide">
				<div class="preloader"></div>
			</div>
		</div>
		<?php  } ?>
	</div>
</div>
<div class="popover popover-manage popover-order-status">
	<div class="popover-angle"></div>
	<div class="popover-inner">
		<div class="list-block">
			<ul>
				<li><a href="<?php  echo $this->createMobileUrl('mgorder', array('status' => 5));?>" class="list-button item-link">已完成</a></li>
				<li><a href="<?php  echo $this->createMobileUrl('mgorder', array('status' => 6));?>" class="list-button item-link">已取消</a></li>
				<li><a href="<?php  echo $this->createMobileUrl('mgorder', array('status' => 0));?>" class="list-button item-link">所有</a></li>
				<li><a href="javascript:;" class="list-button item-link close-popover">关闭</a></li>
			</ul>
		</div>
	</div>
</div>
<!-- 选择配送员 -->
<div class="popup popup-delivery" id="popup-delivery">
	<div class="page">
		<header class="bar bar-nav common-bar-nav">
			<h1 class="title">分配配送员</h1>
			<a class="pull-right close-popup" href="javascript:;">关闭</a>
		</header>
		<div class="content">
			<?php  if(!empty($deliveryers)) { ?>
			<div class="list-block">
				<ul>
					<?php  if(is_array($deliveryers)) { foreach($deliveryers as $deliveryer) { ?>
					<li>
						<label class="label-checkbox item-content">
							<input type="radio" name="deliveryer_id" value="<?php  echo $deliveryer['deliveryer']['id'];?>" checked>
							<div class="item-media"><i class="icon icon-form-checkbox"></i></div>
							<div class="item-inner">
								<div class="item-title"><?php  echo $deliveryer['deliveryer']['title'];?></div>
								<div class="item-after"><?php  echo $deliveryer['deliveryer']['mobile'];?></div>
							</div>
						</label>
					</li>
					<?php  } } ?>
				</ul>
			</div>
			<div class="content-block">
				<a href="javascript:;" class="button button-big button-fill button-danger">确定</a>
			</div>
			<?php  } else { ?>
				<h3 class="align-center">您还没有添加配送员</h3>
			<?php  } ?>
		</div>
	</div>
</div>
<script type="text/javascript" src="http://webapi.amap.com/maps?v=1.3&key=550a3bf0cb6d96c3b43d330fb7d86950"></script>
<script>
$(function(){
	$(document).on("pageInit", "#page-manage-order", function(e, id, page) {
		var loading = false;
		$(page).on('infinite', '.infinite-scroll',function() {
			var $this = $(this);
			var id = $this.data('min');
			if(!id) return;
			if (loading) return;

			loading = true;
			$this.find('.infinite-scroll-preloader').removeClass('hide');
			$.post("<?php  echo $this->createMobileUrl('mgorder', array('op' => 'more', 'status' => $status))?>", {id: id, time: timeStamp}, function(data){
				var result = $.parseJSON(data);
				$this.attr('data-min', result.message.min);

				if(!result.message.min) {
					$.detachInfiniteScroll($('.infinite-scroll'));
					$('.infinite-scroll-preloader').remove();
					return;
				}

				$this.find('.infinite-scroll-preloader').removeClass('hide');
				var gettpl = $('#tpl-order').html();
				loading = false;
				laytpl(gettpl).render(result.message.message, function(html){
					setTimeout(function() {
						$this.find('.order-list ul').append(html);
						order_list_dist();
					}, 1000);
				});
			});
		});
		function order_list_dist() {
			<?php  if($store['location_x'] && $store['location_y']) { ?>
				var lnglat = new AMap.LngLat("<?php  echo $store['location_y'];?>", "<?php  echo $store['location_x'];?>");
				$.each($('.order-ls-dist'), function(){
					var lat = $(this).data('lat');
					var lng = $(this).data('lng');
					if(lat && lng) {
						$(this).removeClass('hide').html('大约 ' + ((lnglat.distance([lng, lat]))/1000).toFixed(2)+' 公里');
					}
				});
			<?php  } ?>
			return true;
		}
		order_list_dist();
	});
});
</script>
<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('manage/common', TEMPLATE_INCLUDEPATH)) : (include template('manage/common', TEMPLATE_INCLUDEPATH));?>
<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('manage/footer', TEMPLATE_INCLUDEPATH)) : (include template('manage/footer', TEMPLATE_INCLUDEPATH));?>