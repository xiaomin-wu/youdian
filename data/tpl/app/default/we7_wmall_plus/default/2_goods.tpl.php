<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('header', TEMPLATE_INCLUDEPATH)) : (include template('header', TEMPLATE_INCLUDEPATH));?>
<script type='text/javascript' src='../addons/we7_wmall_plus/resource/app/js/iscroll-probe.js' charset='utf-8'></script>
<div class="page store shopcategory" id="page-app-goods">
	<nav class="bar bar-tab no-gutter shop-cart-bar">
		<div class="" id="cartEmpty">
			<div class="left empty">
				<span class="fa fa-shopping-cart"></span>购物车是空的
			</div>
			<div class="right text-center bg-grey"><?php  echo $store['send_price'];?>元起送</div>
		</div>
		<div class="hide" id="cartNotEmpty">
			<div class="left">
			<span class="cart">
				<span class="fa fa-shopping-cart"></span>
				<span class="badge bg-danger" id="cartNum">0</span>
			</span>
				共<span class="sum">￥<span id="totalPrice">0</span>元</span>
			</div>
			<div class="right text-center bg-grey" id="categoryCondition">还差￥0元起送</div>
			<div class="right text-center bg-grey">还差￥<span id="sendCondition"><?php  echo $store['send_price'];?></span>元起送</div>
			<div class="right text-center bg-danger hide" id="btnSubmit">选好了</div>
		</div>
	</nav>
	<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('nav', TEMPLATE_INCLUDEPATH)) : (include template('nav', TEMPLATE_INCLUDEPATH));?>
	<header class="bar bar-nav common-bar-nav">
		<a class="pull-left back" href="javascript:;"><i class="fa fa-arrow-left"></i></a>
		<h1 class="title open-popup" data-popup=".popup-privilege"><?php  echo $store['title'];?></h1>
		<a class="pull-right" href="javascript:;" style="margin-left: 5px"><i class="fa fa-search"></i></a>
		<a class="pull-right" href="javascript:;" id="btn-favorite" data-id="<?php  echo $store['id'];?>" data-uid="<?php  echo $_W['member']['uid'];?>"><i class="fa <?php  if(!empty($is_favorite)) { ?>fa-favor-fill<?php  } else { ?>fa-favor<?php  } ?>"></i></a>
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
		<a href="<?php  echo $this->createMobileUrl('goods', array('sid' => $store['id']));?>" class="button active">商品</a>
		<a href="<?php  echo $this->createMobileUrl('store', array('sid' => $store['id'], 'op' => 'comment'));?>" class="button">评价</a>
		<a href="<?php  echo $this->createMobileUrl('store', array('sid' => $store['id']));?>" class="button">商家</a>
		<a href="http://wpa.qq.com/msgrd?v=3&uin=<?php  echo $store['sns']['qq'];?>&site=qq&menu=yes" class="button">客服</a>
	</div>
	<div class="parent-category-wrapper">
		<div class="parent-category">
			<div id="cateMenu">
				<ul class="category-list">
					<?php  if(is_array($categorys)) { foreach($categorys as $category) { ?>
						<?php  if(!empty($cate_goods[$category['id']])) { ?>
							<li>
								<a href="javascript:;">
									<?php  if($category['bargain_id'] > 0) { ?>
										<svg class="svg" aria-hidden="true">
											<use xlink:href="#svg-tag"></use>
										</svg>
									<?php  } ?>
									<?php  echo $category['title'];?>
								</a>
							</li>
						<?php  } ?>
					<?php  } } ?>
				</ul>
			</div>
		</div>
	</div>
	<div class="content lazyload-container">
		<div class="category-container row no-gutter" style="padding-left: 20%">
			<?php  if(!empty($tokens)) { ?>
				<div class="coupon-show-container">
					<div class="coupon-show">
						<div class="coupon-sum">
							<span>￥</span><?php  echo $token['discount'];?>
						</div>
						<div class="division">
							<img src="<?php echo MODULE_URL;?>resource/app/img/division.png" alt="" />
						</div>
						<div class="coupon-info">
							<div class="coupon-title"><?php  echo $token['title'];?></div>
							<?php  if($token_nums > 1) { ?>
								<div class="condition">内含<?php  echo $token_nums;?>张券</div>
							<?php  } else { ?>
								<div class="condition">满<?php  echo $token['condition'];?>元可用</div>
							<?php  } ?>
						</div>
						<div class="get">
							<span class="btn-get" id="get-coupon">领券</span>
						</div>
					</div>
				</div>
			<?php  } ?>
		</div>
		<div class="category-container row no-gutter" id="category-container">
			<div class="children-category col-100">
				<form action="<?php  echo $this->createMobileUrl('submit', array('sid' => $sid, 'op' => 'goods'), true);?>" method="post" id="goods-form">
					<input type="hidden" name="goods" value=""/>
					<?php  if(is_array($categorys)) { foreach($categorys as $cate_row) { ?>
						<?php  if(!empty($cate_goods[$cate_row['id']])) { ?>
							<div class="children-category-wrapper">
								<div class="heading"><?php  echo $cate_row['title'];?> <?php  if($cate_row['min_fee'] > 0) { ?><small>最低消费<?php  echo $cate_row['min_fee'];?>元</small><?php  } ?></div>
								<ul class="list-block media-list">
								<?php  if(is_array($cate_goods[$cate_row['id']])) { foreach($cate_goods[$cate_row['id']] as $ds) { ?>
									<li id="goods-<?php  echo $ds['id'];?>" <?php  if($_GPC['goods_id'] == $ds['id']) { ?>class="active"<?php  } ?>>
										<a class="item-content" data-id="<?php  echo $ds['id'];?>" href="javascript:;">
											<div class="item-media">
												<?php  if(!empty($ds['label'])) { ?>
													<span class="sale-badge bg-danger"><?php  echo $ds['label'];?></span>
												<?php  } ?>
												<img src="../addons/we7_wmall_plus/resource/app/img/hm.gif" class="goods-popup lazyload" data-id="<?php  echo $ds['id'];?>" data-original="<?php  echo tomedia($ds['thumb']);?>" alt=""/>
											</div>
											<div class="item-inner">
												<div class="item-title-row">
													<div class="item-title"><?php  echo $ds['title'];?></div>
												</div>
												<div class="item-text">
													<?php  if(!empty($ds['content'])) { ?>
														<div class="goods-info"><?php  echo $ds['content'];?></div>
													<?php  } ?>
													<div class="sell-info">已售<?php  echo $ds['sailed'];?><?php  echo $ds['unitname'];?>&nbsp;&nbsp; 好评<?php  echo $ds['comment_good'];?></div>
													<div class="price">
														<span class="fee">￥<?php  echo $ds['discount_price'];?></span>
														<?php  if(!empty($ds['bargain_id'])) { ?>
															<span class="original-fee">￥<?php  echo $ds['price'];?></span>
														<?php  } ?>
													</div>
													<?php  if(!empty($ds['discount'])) { ?>
														<span class="tag tag-danger"><?php  if($ds['poi_user_type'] == 'new') { ?>新用户专享<?php  } ?> <?php  echo $ds['discount'];?>折 每单仅限<?php  echo $ds['max_buy_limit'];?>份</span>
													<?php  } ?>
												</div>
											</div>
										</a>
										<?php  if($store['is_in_business_hours']) { ?>
											<?php  if(!$ds['is_options']) { ?>
												<?php  if(!$ds['discount_available_total'] && !$ds['total']) { ?>
													<div class="goods-tips">已售完</div>
												<?php  } else { ?>
													<div class="operate-num operate-goods">
														<span class="hide minus">
															<span class="fa fa-minus" data-goods-id="<?php  echo $ds['id'];?>" data-option-id="0"></span>
															<span class="num">0</span>
														</span>
														<span class="fa fa-plus" data-goods-id="<?php  echo $ds['id'];?>" data-option-id="0"></span>
													</div>
												<?php  } ?>
											<?php  } else if($ds['is_options'] == 1) { ?>
												<div class="operate-goods">
													<span class="select-spec goods-option" data-id="<?php  echo $ds['id'];?>">可选规格</span>
												</div>
											<?php  } ?>
										<?php  } else { ?>
											<div class="goods-tips">店铺休息中</div>
										<?php  } ?>
									</li>
								<?php  } } ?>
								</ul>
							</div>
						<?php  } ?>
					<?php  } } ?>
				</form>
			</div>
		</div>
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
				满<?php  echo $discount['condition'];?>元减<?php  echo $discount['back'];?>元,
				<?php  } } ?>
			</div>
			<?php  } ?>
			<?php  if($activity['grant_status'] == 1) { ?>
			<div class="zeng text-left">
				在线支付
				<?php  if(is_array($activity['grant_data'])) { foreach($activity['grant_data'] as $grant) { ?>
					满<?php  echo $grant['condition'];?>元赠<?php  echo $grant['back'];?>,
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

<div class="popup popup-search" id="popop-search-goods">
	<div class="page search-result search-goods">
		<div class="bar bar-header-secondary">
			<div class="searchbar">
				<a class="searchbar-arrow close-popup" data-popup=".popup-search" ><i class="fa fa-arrow-left"></i></a>
				<a class="searchbar-cancel">搜索</a>
				<div class="search-input">
					<label class="icon fa fa-search" for="search"></label>
					<input type="search" id='search' name="key" value="<?php  echo $_GPC['key'];?>" placeholder="搜索<?php  echo $store['title'];?>的商品"/>
				</div>
			</div>
		</div>
		<div class="content">
			<ul class="list-block media-list">
				<div class="common-no-con hide">
					<img src="<?php echo MODULE_URL;?>/resource/app/img/search_no_con.png" alt="">
					<p>没有符合条件的搜索结果!</p>
				</div>
				<div class="search-result-container"></div>
			</ul>
		</div>
	</div>
</div>

<div class="modal modal-no-buttons modal-notice modal-store-notice">
	<div class="modal-inner">
		<div class="modal-title">
			<div>温馨提示</div>
		</div>
		<div class="modal-text">
			<div class="notice">
				<?php  echo $store['tips'];?>
			</div>
			<a href="javascript:;" onclick="$.closeModal('.modal-notice');" class="button button-big button-fill button-danger close-modal">开始购物</a>
		</div>
	</div>
</div>

<div class="modal modal-no-buttons modal-notice modal-store-business">
	<div class="modal-inner">
		<div class="modal-title">
			<div>店铺休息中</div>
		</div>
		<div class="modal-text">
			<div class="notice">
				店铺休息中，给各位带来不便敬请谅解
			</div>
			<a href="javascript:;" onclick="$.closeModal('.modal-notice');" class="button button-big button-fill button-danger close-modal">我知道了</a>
		</div>
	</div>
</div>
<script id="goods-detail" type="text/html">
	<div class="popup popup-goods-detail">
		<div class="content-block">
			<div class="goods-img">
				<{# if(!d.slides.length){ }>
					<img src="<{d.thumb_}>" width= alt=""/>
				<{# } else { }>
					<div class="swiper-container" data-space-between='0' data-pagination='.swiper-pagination'>
						<div class="swiper-wrapper">
							<{# for(var j = 0, len = d.slides.length; j < len; j++){ }>
							<div class="swiper-slide"><img src="<{d.slides[j]}>" alt=""></div>
							<{# } }>
						</div>
						<div class="swiper-pagination"></div>
					</div>
				<{# } }>
				<a href="#" class="close-popup" data-popup=".popup-goods-detail"><span class="fa fa-close"></span></a>
			</div>
			<div class="goods-name">
				<{d.title}>
			</div>
			<div class="sell-info">已售<{d.sailed}>&nbsp;&nbsp;好评<{d.comment_good}></div>
			<{# if(d.is_options == 0){ }>
				<div class="row no-gutter goods-num">
					<div class="col-50 price">￥<span class="fee"><{d.price}></span></div>
					<div class="col-50 text-right operate-num <?php  if(!$store['is_in_business_hours']) { ?>hide<?php  } ?>">
						<span class="minus hide">
							<span class="fa fa-minus goods-detail-minus" data-goods-id="<{d.id}>" data-option-id="0"></span>
							<span class="num"><{d.hasNum}></span>
						</span>
						<span class="fa fa-plus goods-detail-plus" data-goods-id="<{d.id}>" data-option-id="0"></span>
					</div>
				</div>
			<{# } }>
			<div class="goods-evaluate">商品评价</div>
			<div class="praise text-center">好评率 <span class="rate"><{d.comment_good_percent}></span><span class="num">(共<{d.comment_total}>人评价)</span></div>
			<div class="progress">
				<div class="progress-bar">
					<div class="progress-active" style="width:<{d.comment_good_percent}>;"></div>
				</div>
			</div>
			<div class="goods-desc">商品描述</div>
			<div class="goods-desc-con">
				<{d.description}><br>
				温馨提示: 图片仅供参考,请以实物为准;<br>
				高峰时段及恶劣天气,请提前下单
			</div>
		</div>
	</div>
</script>
<script id="goods-option" type="text/html">
	<div class="popup popup-spec specs goods-option">
		<div class="content-block">
			<div class="goods-title">
				<{d.title}>
				<a href="#" class="close-popup" data-popup=".popup-spec.goods-option"><span class="fa fa-close"></span></a>
			</div>
			<div class="sell-info">已售<{d.sailed}>&nbsp;&nbsp;好评<{d.comment_good}></div>
			<dl class="standard-con">
				<dt>规格</dt>
				<{# for(var i = 0, len = d.options.length; i < len; i++){ }>
					<{# if(i == 0){ }>
						<{# var price = d.options[i].price; var num = d.options[i].num;}>
					<{# } }>
					<dd data-price="<{d.options[i].price}>" data-goods-id="<{d.options[i].goods_id}>" data-option-id="<{d.options[i].id}>" class="goods-option-dd <{# if(i == 0){ }> selected<{# } }>" ><{d.options[i].name}></dd>
				<{# } }>
			</dl>
			<div class="parting-line"></div>
			<div class="row no-gutter">
				<div class="col-50 price">￥<{price}></div>
				<div class="col-50 text-right operate-num">
					<span class="minus <{# if(num == 0){ }>hide<{# } }>">
						<span class="fa fa-minus from-goods-option" data-goods-id="<{d.id}>"></span>
						<span class="num"><{num}></span>
					</span>
					<span class="fa fa-plus from-goods-option" data-goods-id="<{d.id}>"></span>
				</div>
			</div>
		</div>
	</div>
</script>
<script id="goods-cart" type="text/html">
	<div class="popup popup-shop-cart">
		<div class="shop-cart-list">
			<div class="row no-gutter popup-shop-cart-header border-1px-b">
				<div class="col-50"><span><?php  echo $store['title'];?></span></div>
				<div class="col-50 text-right shop-cart-truncate"><img src="<?php echo MODULE_URL;?>resource/app/img/icon-trash.png" alt="" /><span class="color-gray">清空购物车</span></div>
			</div>
			<{# for(var i in d){ }>
				<{# for(var j in d[i]['options']){ }>
					<{# if(d[i]['options'][j]['num'] > 0){ }>
						<div class="row no-gutter list-item border-1px-b <{# if(d[i].goods_id == 0){ }>box-price-item<{# } }>" id="shop-cart-list-item-<{d[i].goods_id}>-<{d[i]['options'][j]['option_id']}>">
							<{# if(d[i]['options'][j]['discount_num'] > 0 && d[i]['options'][j]['price_num'] > 0){ }>
								<div class="col-42 goods-title active">
									<{d[i].title}><{d[i]['options'][j]['name']}>
									<span class="discount-info">含<{d[i]['options'][j]['price_num']}>份原价商品</span>
								</div>
							<{# } else { }>
								<div class="col-42 goods-title">
									<{d[i].title}><{# if(d[i]['options'][j]['name']){ }><{d[i]['options'][j]['name']}><{# } }>
								</div>
							<{# } }>
							<div class="col-25 color-orange text-right goods-price <{# if(d[i].goods_id == 0){ }>box-price<{# } }>">￥<{d[i]['options'][j]['price_total']}></div>
							<div class="col-33 text-right">
								<{# if(d[i].goods_id != 0){ }>
									<div class="operate-num">
										<span class="minus">
											<span class="fa fa-minus from-goods-cart" data-option-id="<{d[i]['options'][j]['option_id']}>" data-goods-id="<{d[i].goods_id}>"></span>
											<span class="num"><{d[i]['options'][j]['num']}></span>
										</span>
										<span class="fa fa-plus from-goods-cart" data-option-id="<{d[i]['options'][j]['option_id']}>" data-goods-id="<{d[i].goods_id}>"></span>
									</div>
								<{# } }>
							</div>
						</div>
					<{# } }>
				<{# } }>
			<{# } }>
		</div>
	</div>
</script>
<script id="goods-list" type="text/html">
	<{# for(var i = 0, len = d.length; i < len; i++){ }>
	<li>
		<a class="item-content" href="javascript:;">
			<div class="item-media">
				<{# if(d[i].label != ''){ }>
					<span class="sale-badge bg-danger"><{d[i].label}></span>
				<{# } }>
				<img class="goods-popup" data-id="<{d[i].id}>" src="<{d[i].thumb_cn}>" alt=""/>
			</div>
			<div class="item-inner">
				<div class="item-title-row">
					<div class="item-title"><{d[i].title}></div>
				</div>
				<div class="item-text">
					<div class="sell-info">已售<{d[i].sailed}><{d[i].unitname}>&nbsp;&nbsp; 好评<{d[i].comment_good}></div>
					<div class="price">￥<span class="fee"><{d[i].price}></span></div>
				</div>
			</div>
		</a>
		<{# if(d[i].is_in_business_hours){ }>
			<{# if(d[i].is_options != 1){ }>
				<div class="operate-num operate-goods">
					<span class="minus <{# if(!d[i].num) {}>hide<{# } }>">
						<span class="fa fa-minus" data-goods-id="<{d[i].id}>" data-option-id="0"></span>
						<span class="num"><{d[i].num}></span>
					</span>
					<span class="fa fa-plus" data-goods-id="<{d[i].id}>" data-option-id="0"></span>
				</div>
			<{# } else { }>
				<div class="operate-goods">
					<span class="select-spec goods-option" data-id="<{d[i].id}>">可选规格</span>
				</div>
			<{# } }>
		<{# } else { }>
			<div class="goods-tips">店铺休息中</div>
		<{# } }>
	</li>
	<{# } }>
</script>
<script type="text/javascript" src="../addons/we7_wmall_plus/resource/app/js/jquery.fly.min.js"></script>
<script>
$.modal.prototype.defaults.closePrevious = false;
$(function(){
	var categorys_limit_status = <?php  echo $categorys_limit_status;?>;
	var categorys_limit = <?php  echo json_encode($categorys_limit);?>;
	var categorys_limit_temp = <?php  echo json_encode($categorys_limit);?>;
	var categorys_index = <?php  echo json_encode($categorys_index)?>;
	var goods = <?php  echo json_encode($goods);?>;
	var bargains = <?php  echo json_encode($bargains);?>;
	var bargain_goods = {};
	var cart_new = {};
	var is_newmember = <?php  echo $_W['member']['is_newmember'];?>;
	var cart_data = <?php  echo json_encode($cart['original_data'])?>;

	$('.parent-category .category-list li:first').addClass('active');

	$(document).on('click', '.shop-cart-truncate', function(){
		$.post("<?php  echo $this->createMobileUrl('goods', array('op' => 'cart_truncate', 'sid' => $sid));?>", {}, function(data){
			var result = $.parseJSON(data);
			if(result.message.errno != 0) {
				$.toast(result.message.message);
				return false;
			} else {
				var send_price = "<?php  echo $store['send_price'];?>";
				cart_new = new Array();
				bargain_goods= {};
				categorys_limit = categorys_limit_temp;
				$('.children-category-wrapper span.minus .num').html(0);
				$('.children-category-wrapper span.minus').addClass('hide');
				$('#popop-search-goods .minus').addClass('hide');
				$('#popop-search-goods .minus .num').html(0);

				$('#cartNotEmpty').addClass('hide');
				$('#cartNotEmpty #totalPrice, #cartNotEmpty #cartNum').html(0);
				$('#cartNotEmpty #sendCondition').html(send_price);
				$('#cartEmpty').removeClass('hide');
				$.closeModal('.popup-shop-cart');
				return false;
			}
		});
	});
	$('#btnSubmit').click(function(){
		$('#goods-form :hidden[name="goods"]').val((encodeURI(JSON.stringify((cart_new)))));
		$('#goods-form').submit();
	});

	<?php  if(!empty($store['notice'])) { ?>
		var left = 0, notice = $('.js-scroll-notice');
		setInterval(function(){
			left--;
			0 > left + notice.width() && (left = notice.width());
			notice.css({
				'left': left
			});
		}, 25);
	<?php  } ?>

	<?php  if(!empty($store['tips'])) { ?>
		$.iopenModal('.modal-store-notice', function(){});
	<?php  } ?>

	<?php  if(!$store['is_in_business_hours']) { ?>
		$.iopenModal('.modal-store-business', function(){});
	<?php  } ?>

	$(document).on("pageInit", "#page-app-goods", function(e, id, page) {
		$(document).on('click', '.bar-nav .fa-search', function(){
			$('.search-input input').val('');
			$('.search-result-container').html('');
			$.popup('.popup-search');
		});

		$(document).on('click', '#popop-search-goods .searchbar-cancel', function(){
			var key = $('.search-input input').val();
			if(!key) {
				return false;
			}
			$('.search-result-container').html('');
			$.showIndicator();
			$.post("<?php  echo $this->createMobileUrl('goods', array('op' => 'search', 'sid' => $sid));?>", {key: key}, function(data){
				var result = $.parseJSON(data);
				if(result.message.errno == -1) {
					$.toast(result.message.message);
					return false;
				} else {
					if(result.message.message.length <= 0) {
						$.hideIndicator();
						$('#popop-search-goods .common-no-con').removeClass('hide');
						return false;
					}
					$('#popop-search-goods .common-no-con').addClass('hide');
					$.each(result.message.message, function(i, v){
						var goods_id = v.id;
						if(v.is_options == 0) {
							result.message.message[i].num = goodsNum(goods_id, 0);
						} else {
							$.each(v.options, function(j, d){
								result.message.message[i].options[j].num = goodsNum(goods_id, d.id);
							});
						}
					});
					var gettpl = $('#goods-list').html();
					laytpl(gettpl).render(result.message.message, function(html){
						$.hideIndicator();
						$('#popop-search-goods').find('.search-result-container').html(html);
					});
				}
			});
			return false;
		});

		$(document).on('click', '#cartNotEmpty .cart', function(){
			if(!$(this).hasClass('show')) {
				$(this).addClass('show');
				var gettpl = $('#goods-cart').html();
				laytpl(gettpl).render(cart_new, function(html){
					$.popup(html);
				});
			} else {
				$(this).removeClass('show')
				$.closeModal('.popup-shop-cart');
			}
		});

		$(document).on('click', '.goods-option-dd', function(){
			var $parent = $(this).parents('.popup-spec');
			var goods_id = $(this).data('goods-id');
			var option_id = $(this).data('option-id');
			var price = $(this).data('price');
			$(this).siblings().removeClass('selected');
			$(this).addClass('selected');
			$('.goods-option .no-gutter .price').html('¥ ' + price);
			var num = goodsNum(goods_id, option_id);
			$('.goods-option .no-gutter .num').html(num);
			if(num > 0) {
				$parent.find('.minus').removeClass('hide');
			}
		});

		$(document).on('click', '#category-container .goods-option, #popop-search-goods .goods-option', function(){
			var id = $(this).data('id');
			$.showIndicator();
			$.post("<?php  echo $this->createMobileUrl('goods', array('op' => 'detail', 'sid' => $sid));?>", {id: id}, function(data) {
				var result = $.parseJSON(data);
				if(result.message.errno != 0) {
					$.hideIndicator();
					$.toast(result.message.message);
				} else {
					var val = {};
					for(var i = 0; i < result.message.message['options'].length; i++){
						val = result.message.message.options[i];
						result.message.message.options[i]['num'] = goodsNum(id,  val.id);
					}
					var gettpl = $('#goods-option').html();
					laytpl(gettpl).render(result.message.message, function(html){
						$.hideIndicator();
						$.popup(html);
					});
				}
				return false;
			});
		});

		$(document).on('click', '.goods-popup', function(){
			var id = $(this).data('id');
			var num = goodsNum(id, 0);
			$.showIndicator();
			$.post("<?php  echo $this->createMobileUrl('goods', array('op' => 'detail', 'sid' => $sid));?>", {id: id}, function(data) {
				var result = $.parseJSON(data);
				if(result.message.errno != 0) {
					$.hideIndicator();
					$.toast(result.message.message);
				} else {
					result.message.message.hasNum = num;
					var gettpl = $('#goods-detail').html();
					laytpl(gettpl).render(result.message.message, function(html){
						$.hideIndicator();
						$.popup(html);
						if(num > 0) {
							$('.popup-goods-detail').find('.minus').removeClass('hide')
						}
						$(".swiper-container").swiper({autoplay: 1000});
					});
				}
				return false;
			});
		});

		$(document).on('click', '.fa-plus', function(){
			var goods_id = $(this).data('goods-id');
			var option_id = $(this).data('option-id');
			var from_goods_option = $(this).hasClass('from-goods-option');
			if(from_goods_option) {
				var $parent = $(this).parents('.popup-spec');
				option_id = $parent.find('.standard-con dd.selected').data('option-id');
				if(!option_id) {
					return false;
				}
			}

			var curNum = goodsCartNew(goods_id, option_id, '+');
			if(curNum === false) {
				return false;
			}
			var $num = $(this).prev().find('.num');
			$num.text(curNum);
			$('#goods-' + goods_id + ' .minus .num').html(curNum);
			if(curNum > 0){
				$(this).prev().removeClass('hide');
				$('#goods-' + goods_id + ' .minus').removeClass('hide');
			}
			var from_goods_cart = $(this).hasClass('from-goods-cart');
			if(from_goods_cart) {
				var price = cart_new[goods_id]['options'][option_id]['price_total'];
				$('#shop-cart-list-item-' + goods_id + '-' + option_id + ' .goods-price').html('￥' + price);
			}

			if(flag) {
				return false;
			}
			var flyer = $('<div class="u-flyer"></div>');
			flyer.fly({
				start: {
					left: event.pageX,
					top: event.pageY
				},
				end: {
					left: 25,
					top: $(window).height() - 20,
					width: 0,
					height: 0
				},
				onEnd: function(){
					$('.u-flyer:not(:last)').remove();
				}
			});
		});

		$(document).on('click', '.fa-minus', function(){
			var goods_id = $(this).data('goods-id');
			var option_id = $(this).data('option-id') ? $(this).data('option-id') : 0;
			var from_goods_option = $(this).hasClass('from-goods-option');
			if(from_goods_option) {
				var $parent = $(this).parents('.popup-spec');
				option_id = $parent.find('.standard-con dd.selected').data('option-id');
				if(!option_id) {
					return false;
				}
			}
			var curNum = goodsCartNew(goods_id, option_id, '-');
			if(curNum === false) {
				return false;
			}
			var $num = $(this).next();
			$num.text(curNum);
			$('#goods-' + goods_id + ' .minus .num').text(curNum);
			if(curNum < 1) {
				$(this).parent().addClass('hide');
				$('#goods-' + goods_id + ' .minus').addClass('hide');
				var from_goods_cart = $(this).hasClass('from-goods-cart');
				if(from_goods_cart) {
					$(this).parents('.popup-shop-cart').find('#shop-cart-list-item-' + goods_id + '-' + option_id).remove();
					if($('.popup-shop-cart .shop-cart-list .row.list-item:not(".box-price-item")').length == 0) {
						$.closeModal('.popup-shop-cart');
					}
				}
			}
		});

		var goodsCartNew = function(goods_id, option_id, sign) {
			var goods_item = goods[goods_id];
			if(!goods_item) {
				return false;
			}
			var cart_item = cart_new[goods_id];
			var curNum = 0;
			if(cart_item && cart_item['options'] && cart_item['options'][option_id]) {
				var curNum = cart_item['options'][option_id]['num'];
			}
			var price = goods_item['price'];
			var original_price = goods_item['price'];
			var discount_price = goods_item['discount_price'];
			if(option_id > 0) {
				price = goods_item['options'][option_id]['price'];
				original_price = goods_item['options'][option_id]['price'];
				discount_price = goods_item['options'][option_id]['discount_price'];
			}
			var price_change = 0;
			if(sign == '+') {
				if(goods_item['bargain_id'] > 0 && bargains[goods_item['bargain_id']]) {
					var bargain = bargains[goods_item['bargain_id']];
					if(bargain['avaliable_order_limit'] <= 0) {
						if(!curNum && !flag) {
							$.toast(bargain['title'] + '活动每天限购一单,超出后恢复原价');
						}
						price = original_price;
						price_change = 1;
					}
					if(goods_item['poi_user_type'] == 'new' && !is_newmember) {
						if(!curNum && !flag) {
							//只提示一次
							$.toast('仅限门店新用户优惠');
						}
						price = original_price;
						price_change = 1;
					}
					if(!price_change) {
						//天天特价限制商品种数
						var bargain_goods_item = bargain['hasgoods'] ? bargain['hasgoods'] : new Array();
						var marks = 0;
						for(var i in bargain_goods_item) {
							if(bargain_goods_item[i] == goods_id) {
								marks = 1;
								break;
							}
						}
						if(!marks && bargain_goods_item.length == bargain.goods_limit && !flag) {
							$.toast(bargain['title'] + '每单特价商品限购'+ bargain.goods_limit +'种,超出后恢复原价');
						}
						if(!marks) {
							bargain_goods_item.push(goods_id);
						}
						if(bargain_goods_item.length > bargain.goods_limit) {
							price = original_price;
							price_change = 1;
						}
						//天天特价限制单个商品购买数量
						if(!price_change) {
							if(curNum == goods_item['max_buy_limit'] && !flag) {
								$.toast('每单特价商品限购' + goods_item.max_buy_limit + '份,超出后恢复原价');
							}
							if(curNum >= goods_item['max_buy_limit']) {
								price = original_price;
								price_change = 1;
							} else {
								price = discount_price;
								price_change = 2;
								if(!flag && goods_item.discount_available_total != -1 && cart_item && cart_item['options'] && cart_item['options'][0] && cart_item['options'][0]['discount_num'] >= goods_item.discount_available_total) {
									$.toast('活动库存不足,恢复原价购买');
									price = original_price;
									price_change = 1;
								}
							}
						}
					}
					if(price_change <= 1) {
						var max = parseInt(goods_item.total);
						if(!max && !flag) {
							$.toast('库存不足,下次再来');
							return false;
						} else {
							if(max != -1 && cart_item && cart_item['options'] && cart_item['options'][0]['num'] >= max && !flag) {
								$.toast('库存不足,下次再来');
								return false;
							}
						}
					}
				} else {
					var max = goods_item.total;
					if(option_id > 0) {
						max = goods_item['options'][option_id]['total'];
					}
					if(null != max && max != "" && max != "-1" && curNum >= max && !flag) {
						$.toast('库存不足,下次再来');
						return false;
					}
				}

				if(!cart_item) {
					var item = {
						goods_id: goods_id,
						title: goods_item['title'],
						options: {}
					};
					goods_id = goods_id + '';
					cart_new[goods_id] = item;
				}
				var options = cart_new[goods_id]['options'];
				option_id = option_id + '';
				if(!options[option_id]) {
					options[option_id] = {
						option_id: option_id,
						name: goods_item['is_options'] == 1 ? goods_item['options'][option_id]['name'] : '',
						num: 1,
						price_num: price_change <= 1 ? 1: 0,
						discount_num: price_change == 2 ? 1: 0,
						bargain_id: price_change == 2 ? goods_item['bargain_id']: 0,
						price_total: price
					}
				} else {
					options[option_id]['num'] = parseInt(options[option_id]['num']) + 0 + 1;
					if(price_change <= 1) {
						options[option_id]['price_num'] = parseInt(options[option_id]['price_num']) + 1;
					} else {
						options[option_id]['bargain_id'] = goods_item['bargain_id'];
						options[option_id]['discount_num'] =  parseInt(options[option_id]['discount_num']) + 1;
					}
					options[option_id]['price_total'] = parseFloat(options[option_id]['price_total']) + parseFloat(price);
				}
				curNum = options[option_id]['num'];
			} else {
				if(!cart_item) {
					return false;
				}
				var options = cart_item['options']
				if(!options) {
					return false;
				}
				var option = options[option_id];
				if(option && option['num'] > 0) {
					option['num']--;
					var num_change = 0;
					if(option['price_num'] > 0) {
						price = original_price;
						if(option['price_num'] > 0) {
							option['price_num']--;
							num_change = 1;
						}
					}
					if(!num_change && option['discount_num'] > 0) {
						price = discount_price;
						if(option['discount_num'] > 0) {
							option['discount_num']--;
							num_change = 2;
							if(option['discount_num'] <= 0) {
								option['bargain_id'] = 0;
							}
						}
					}
					option['price_total'] = parseFloat(options[option_id]['price_total']) - parseFloat(price);
				}
				curNum--;
				if(goods_item['bargain_id'] > 0 && bargains[goods_item['bargain_id']]) {
					var bargain = bargains[goods_item['bargain_id']];
					var bargain_goods_item = bargain['hasgoods'];
					if(curNum <= 0 && bargain_goods_item) {
						for(var i in bargain_goods_item) {
							if(bargain_goods_item[i] == goods_id) {
								bargain_goods_item.splice(i, 1);
								break;
							}
						}
					}
				}
				curNum = option['num'];
			}
			var goods_box_price = parseFloat(goods_item['box_price']);
			if(goods_box_price > 0) {
				var box_price_item = cart_new['88888'];
				if(!box_price_item) {
					cart_new['88888'] = {
						title: '餐盒费',
						goods_id: 0,
						options: {
							'0': {
								num: 0,
								name: '',
								discount_num: 0,
								price_num: 0,
								price_total: 0
							}
						}
					}
					box_price_item = cart_new['88888'];
				}
				box_price_item = box_price_item['options']['0'];
				if(sign == '+') {
					box_price_item['num'] += 1;
				} else {
					box_price_item['num'] -= 1;
				}
				box_price_item['price_total'] = (parseFloat(box_price_item['price_total']) + parseFloat(sign + 1) * goods_box_price).toFixed(2);
			}
			var params = {
				price: price,
				box_price: goods_item['box_price'] ? goods_item['box_price']: 0,
				cid: goods_item['cid']
			};
			count(params, sign);

			return curNum;
		}

		//获取某个商品的数量
		var goodsNum = function(goods_id, option_id) {
			var cart_goods_item = cart_new[goods_id];
			if(!cart_goods_item) {
				return 0;
			}
			var option = cart_goods_item['options'][option_id];
			if(!option) {
				return 0;
			} else {
				return option['num'];
			}
		}

		var count = function(goods, sign) {
			var $condition = $('#sendCondition'),
				$total = $('#totalPrice'),
				$cartNum = $('#cartNum'),
				$cartEmpty = $('#cartEmpty'),
				$cartNotEmpty = $('#cartNotEmpty'),
				sendCondition = parseFloat($condition.text()).toFixed(3),
				totalPrice = parseFloat($total.text()) || 0,
				disPrice = parseFloat(sign + 1) * parseFloat(goods['price']),
				boxPrice = parseFloat(sign + 1) * parseFloat(goods['box_price']),
				price = totalPrice + disPrice + boxPrice,
				price = parseFloat(price.toFixed(3)),
				number = $cartNum.text() == '' ? 0 : parseInt($cartNum.text()),
				disNumber = number + parseInt(sign + 1);
			$total.text(price);
			$condition.text(parseFloat((sendCondition - disPrice - boxPrice).toFixed(3)));
			$cartNum.text(disNumber);

			var category_flag = 0;
			if(categorys_limit_status == 1) {
				var cid = parseInt(goods['cid']);
				var index = $.inArray(cid, categorys_index);
				if(index != -1) {
					categorys_limit[cid].fee = (parseFloat(categorys_limit[cid].fee) + parseFloat(disPrice)).toFixed(3);
				}
				for(var i in categorys_limit) {
					var category = categorys_limit[i];
					if(category.fee > 0 && parseFloat(category.fee) < parseFloat(category.min_fee)) {
						category_flag = 1;
						break;
					} else {
						category_flag = 0;
					}
				}
			}

			if(sendCondition - disPrice - boxPrice <= 0){
				if(category_flag == 1) {
					$condition.parent().hide();
					$condition.parent().next().hide();
					$condition.parent().hide().prev().show();
					$('#categoryCondition').off('click');
					$('#categoryCondition').on('click', function(){
						$.toast(categorys_limit[i].title + '分类最低消费' + categorys_limit[i].min_fee + '元才能下单');
						return;
					});
				} else {
					$condition.parent().hide();
					$condition.parent().prev().hide();
					$condition.parent().hide().next().show();
				}
			} else {
				$condition.parent().prev().hide();
				$condition.parent().next().hide();
				$condition.parent().show();
			}
			if(disNumber > 0){
				$cartEmpty.addClass('hide');
				$cartNotEmpty.removeClass('hide');
			} else {
				$cartEmpty.removeClass('hide');
				$cartNotEmpty.addClass('hide');
			}
			return false;
		}

		var flag = 0;
		function __init() {
			if(cart_data) {
				flag = 1;
				for(var n in cart_data) {
					if(cart_data[n]['options']) {
						for(var m in cart_data[n]['options']) {
							var option = cart_data[n]['options'][m];
							if(option['num'] > 0) {
								for(var i = 0; i < option['num']; i++) {
									var curNum = goodsCartNew(n, m, '+');
									if(curNum !== false) {
										$('#goods-' + n + ' .minus .num').html(curNum);
										if(curNum > 0){
											$('#goods-' + n + ' .minus').removeClass('hide');
										}
									}
								}
							}
						}
					}
				}
			}
			flag = 0;
		}
		__init();

		var menu = {
			offsetAry: [0],
			_is_left_menu_addclass: true,
			init: function(){
				var _this = this;
				var windowHeight = $(window).height();
				var maxLeftHeight = windowHeight - 200;
				$('#cateMenu').height(maxLeftHeight);
				if($('.parent-category ul').height() > maxLeftHeight) {
					if($.device.iphone) {
						new IScroll('#cateMenu', {probeType: 3, mouseWheel: true, click: false, tap: true})
					} else {
						new IScroll('#cateMenu', {probeType: 3, mouseWheel: true, click: true})
					}
				}

				$(document).on('click', '.parent-category li', function(){
					$('.parent-category li').removeClass('active');
					$(this).addClass('active');
					_this._is_left_menu_addclass = false;
					var t = $('.content').scrollTop();
					var t1 = _this.offsetAry[$('.parent-category li').index(this) + 1] - 125;
					var _t = Math.abs(t1-t);
					$('.content').scrollTop(t1);
					setTimeout(function(){
						_this._is_left_menu_addclass=true;
					}, 300);
				});
				$('.children-category-wrapper .heading').each(function(){
					_this.offsetAry.push($(this).offset().top);
				});
				$('.content').bind('scroll', function(){
					_this.scroll.call(_this);
				});
			},

			getIndex: function(ary, value){
				var i = 0;
				for(; i < ary.length; i++){
					if(value >= ary[i] && value < ary[i + 1]){
						return i;
					}
				}
				return ary.length -1;
			},

			scroll: function(){
				var st = $('.content').scrollTop() + 125;
				var index = this.getIndex(this.offsetAry, st);
				var i = index - 1;
				if(this.curIndex !== index){
					if(this._is_left_menu_addclass) {
						$('.parent-category li').removeClass('active');
					}
					if(i >= 0){
						$('#category-container .parent-category').addClass('fixed');
						if(this._is_left_menu_addclass) {
							$('.parent-category li').eq(i).addClass('active');
						}
					} else {
						$('.parent-category li').eq(0).addClass('active');
						$('#category-container .parent-category').removeClass('fixed');
					}
					this.curIndex = index;
				}
			}
		};
		menu.init();
		<?php  if(!empty($_GPC['goods_id'])) { ?>
			var goods_id = "<?php  echo $_GPC['goods_id'];?>";
			$('.content').scrollTop($('#goods-' + goods_id).offset().top - 125);
		<?php  } ?>
		<?php  if(!empty($_GPC['key'])) { ?>
			$('#popop-search-goods .searchbar-cancel').trigger('click');
			$.popup('.popup-search');
		<?php  } ?>
	});

	//领取优惠券
	$(document).on('click', '#get-coupon', function(){
		$.showIndicator();
		$.post("<?php  echo $this->createMobileUrl('coupon', array('op' => 'get', 'sid' => $sid))?>", {}, function(data){
			var result = $.parseJSON(data);
			$.hideIndicator();
			if(result.message.errno != 0) {
				$.toast(result.message.message);
				return false;
			} else {
				$('.coupon-show-container').hide();
				$.toast('领取优惠券成功');
				return false;
			}
		});
	});
});
</script>
<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('common', TEMPLATE_INCLUDEPATH)) : (include template('common', TEMPLATE_INCLUDEPATH));?>
<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('footer', TEMPLATE_INCLUDEPATH)) : (include template('footer', TEMPLATE_INCLUDEPATH));?>