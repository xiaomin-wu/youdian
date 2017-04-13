<?php defined('IN_IA') or exit('Access Denied');?><script id="tpl-order" type="text/html">
	<{# for(var i = 0, len = d.length; i < len; i++){ }>
	<div class="order-container">
		<div class="order-inner">
			<div class="store-info">
				<a class="external" href="<?php  echo $this->createMobileUrl('goods');?>&sid=<{d[i].sid}>">
					<img src="<{d[i].logo_cn}>" alt="" />
					<span class="store-title"><{d[i].title}></span>
					<span class="fa fa-arrow-right"></span>
					<{# if(d[i].delivery_mode == 2){ }>
						<div class="plateform-delivery"><span>平台专送</span></div>
					<{# } }>
				</a>
			</div>
			<a class="goods-info row no-gutter external" href="<?php  echo $this->createMobileUrl('order', array('op' => 'detail'));?>&id=<{d[i].id}>">
				<div class="col-75">
					<div class="goods-title"><{d[i].goods['goods_title']}>等<span><{d[i].num}></span>件商品</div>
					<div class="date"><{d[i].addtime_cn}></div>
				</div>
				<div class="col-25 text-right">
					<div class="price">￥<{d[i].final_fee}></div>
					<div class="status no-pay"><{d[i].status_cn}></div>
				</div>
			</a>
			<{# if(!d[i].is_pay && d[i].status != 6){ }>
			<div class="order-status">
				<div class="pic">
					<img src="<?php echo MODULE_URL;?>resource/app/img/order_status_money.png" alt="" />
				</div>
				<div class="order-status-detail">
					<div class="arrow-left"></div>
					<div class="clearfix">待支付<span class="pull-right date"><{d[i].time_cn}></span></div>
					<div class="tips">请在提交订单后<{d[i].pay_time_limit}>分钟内完成支付</div>
				</div>
			</div>
			<{# } }>
		</div>
		<div class="order-btn table">
			<{# if(!d[i].is_pay && d[i].status != 5 && d[i].status != 6){ }>
				<a href="<?php  echo $this->createMobileUrl('pay', array('order_type' => 'order', 'type' => 1));?>&id=<{d[i].id}>" class="table-cell">立即支付</a>
			<{# } }>
			<{# if(d[i].status == 1){ }>
				<a href="javascript:;" class="order-cancel table-cell" data-id="<{d[i].id}>">取消订单</a>
				<{# if(d[i].is_pay == 1){ }>
					<a href="javascript:;" class="order-remind table-cell" data-id="<{d[i].id}>">催单</a>
				<{# } }>
			<{# } else if(d[i].status >= 2 && d[i].status <= 4) { }>
				<{# if(d[i].order_type == 1){ }>
					<a href="javascript:;" class="order-end table-cell" data-id="<{d[i].id}>" data-type="1">确认送达</a>
				<{# } else if(d[i].order_type == 2) { }>
					<a href="javascript:;" class="order-end table-cell" data-id="<{d[i].id}>" data-type="2">我已提货</a>
				<{# } }>
				<{# if(d[i].is_pay == 1){ }>
					<a href="javascript:;" class="order-remind table-cell" data-id="<{d[i].id}>">催单</a>
				<{# } }>
			<{# } else if(d[i].status == 5) { }>
				<a href="<?php  echo $this->createMobileUrl('goods', array('f' => '1'));?>&id=<{d[i].id}>&sid=<{d[i].sid}>" class="table-cell external" data-id="<?php  echo $order['id'];?>">再来一单</a>
				<{# if(d[i].is_comment == 1){ }>
					<a href="<?php  echo $this->createMobileUrl('order', array('op' => 'comment'));?>&id=<{d[i].id}>" class="table-cell">去评价</a>
				<{# } else { }>
					<a href="<?php  echo $this->createMobileUrl('comment');?>" class="table-cell">查看评价</a>
				<{# } }>
			<{# } else if(d[i].status == 6) { }>
				<a href="<?php  echo $this->createMobileUrl('goods', array('f' => '1'));?>&id=<{d[i].id}>&sid=<{d[i].sid}>" class="table-cell external" data-id="<?php  echo $order['id'];?>">再来一单</a>
			<{# } }>
			<{# if(d[i].is_refund == 1){ }>
				<a href="<?php  echo $this->createMobileUrl('order', array('op' => 'detail'));?>&id=<{d[i].id}>" class="table-cell">查看退款</a>
			<{# } }>
		</div>
	</div>
	<{# } }>
</script>

<script id="tpl-my-comment" type="text/html">
	<{# for(var i = 0, len = d.length; i < len; i++){ }>
	<div class="comment-inner">
		<div class="store-title">
			<{d[i].title}><span class="date color-muted"><{d[i].addtime_cn}></span>
		</div>
		<div>
			<div class="star-rank">
				<span class="star-rank-outline">
					<span class="star-rank-active" style="width:<{d[i].id}>%"></span>
				</span>
			</div>
			<span class="color-muted hide">送货速度:40分钟</span>
		</div>
		<div class="color-muted">送货：<{d[i].delivery_service}>分&nbsp;&nbsp;商品：<{d[i].goods_quality}>分</div>
		<{# if(d[i].note != ''){ }>
			<div class="comment-info"><{d[i].note}></div>
		<{# } }>
		<{# if(d[i].data && d[i].data.good && d[i].data.good.length > 0){ }>
			<div class="comment-favor-oppose">
				<i class="icon favor"></i>
				<{# for(var j = 0, lenj = d[i].data.good.length; j < lenj; j++){ }>
					<span><{d[i].data.good[j]}></span>
				<{# } }>
			</div>
		<{# } }>
		<{# if(d[i].data && d[i].data.bad && d[i].data.bad.length > 0){ }>
			<div class="comment-favor-oppose">
				<i class="icon oppose"></i>
				<{# for(var j = 0, lenj = d[i].data.bad.length; j < lenj; j++){ }>
					<span><{d[i].data.bad[j]}></span>
				<{# } }>
			</div>
		<{# } }>
		<{# if(d[i].thumbs && d[i].thumbs.length > 0){ }>
			<div class="comment-images-containter row">
				<{# for(var j = 0, lenj = d[i].thumbs.length; j < lenj; j++){ }>
					<div class="col-33 comment-images-item">
						<img src="<{d[i].thumbs[j]}>" alt=""/>
					</div>
				<{# } }>
			</div>
		<{# } }>
		<{# if(d[i].reply != ''){ }>
			<div class="store-comment">
				<div class="clearfix store-comment-top">
					店家回复：<span class="pull-right"><{d[i].replytime_cn}></span>
				</div>
				<div class="info"><{d[i].reply}></div>
			</div>
		<{# } }>
	</div>
	<{# } }>
</script>

<script id="tpl-store-comment" type="text/html">
	<{# for(var i = 0, len = d.length; i < len; i++){ }>
	<li>
		<a href="javascript:;" class="item-link item-content">
			<div class="item-media">
				<img src="<{d[i].avatar}>" alt="">
			</div>
			<div class="item-inner">
				<div class="item-title-row">
					<div class="item-title"><{d[i].mobile}></div>
					<div class="item-after"><{d[i].addtime}></div>
				</div>
				<div class="item-text">
					<div>
						<div class="star-rank">
							<span class="star-rank-outline">
								<span class="star-rank-active" style="width:<{d[i].score}>%"></span>
							</span>
						</div>
						<span class="color-muted hide">送货速度:40分钟</span>
					</div>
					<{# if(d[i].note != ''){ }>
						<div class="comment-info"><{d[i].note}></div>
					<{# } }>
					<{# if(d[i].data && d[i].data.good && d[i].data.good.length > 0){ }>
						<div class="comment-favor-oppose">
							<i class="icon favor"></i>
							<{# for(var j = 0, lenj = d[i].data.good.length; j < lenj; j++){ }>
							<span><{d[i].data.good[j]}></span>
							<{# } }>
						</div>
					<{# } }>
					<{# if(d[i].thumbs && d[i].thumbs.length > 0){ }>
						<div class="comment-images-containter row">
							<{# for(var j = 0, lenj = d[i].thumbs.length; j < lenj; j++){ }>
							<div class="col-33 comment-images-item">
								<img src="<{d[i].thumbs[j]}>" alt=""/>
							</div>
							<{# } }>
						</div>
					<{# } }>
					<{# if(d[i].reply != ''){ }>
						<div class="store-comment">
							<div class="clearfix store-comment-top">
								店家回复<span class="pull-right"><{d[i].replytime}></span>
							</div>
							<div class="info"><{d[i].reply}></div>
						</div>
					<{# } }>
				</div>
			</div>
		</a>
	</li>
	<{# } }>
</script>

<script id="tpl-store-list" type="text/html">
	<{# for(var i = 0, len = d.length; i < len; i++){ }>
	<div class="no-dist list-item sborder" data-lat="<{d[i].location_x}>" data-lng="<{d[i].location_y}>">
		<a href="<{d[i].url}>" class="external">
			<div class="store-info row no-gutter">
				<div class="store-img col-25">
					<img src="<{d[i].logo}>" alt="<{d[i].title}>">
				</div>
				<div class="col-75">
					<div class="row no-gutter">
						<div class="col-60 text-ellipsis"><{d[i].title}></div>
						<div class="money-info text-right">
							<{# if(d[i].token_status == '1'){ }>
								<span>券</span>
							<{# } }>
							<{# if(d[i].invoice_status == '1'){ }>
								<span>票</span>
							<{# } }>
							<span>付</span>
						</div>
					</div>
					<div class="rel-info">
						<div class="row no-gutter">
							<div class="col-60">
								<{# if(d[i].is_in_business_hours){ }>
									<div class="star-rank">
										<span class="star-rank-outline">
											<span class="star-rank-active" style="width:<{d[i].score_cn}>%"></span>
											<span class="star-rank-value"><{d[i].score}></span>
										</span>
										<span class="sailed">
											已售 <{d[i].sailed}> 份
										</span>
									</div>
								<{# }else{ }>
									<div class="order-status">
										<span class="badge bg-default">店铺休息中</span>
									</div>
								<{# } }>
							</div>
							<{# if(d[i].delivery_mode == 2){ }>
								<div class="plateform-delivery"><span>平台专送</span></div>
							<{# } }>
						</div>
						<div class="delivery-conditions">
							起送<span class="color-danger">￥<{d[i].send_price}></span><span class="pipe">|</span>配送<span class="color-danger">￥<{d[i].delivery_price}></span><span class="pipe">|</span>约<span class="color-danger"><{d[i].delivery_time}>分钟</span>
							<div class="distance" data-in-business-hours="<{# if(d[i].is_in_business_hours){ }>1<{# } else { }>100000000<{# } }>"><i class="fa fa-lbs"></i><{d[i].distance}>km</div>
						</div>
					</div>
				</div>
			</div>
		</a>
		<div class="activity-containter">
			<{# if(d[i].activity && d[i].activity.activity_num > 0){ }>
				<div class="dashed-line"></div>
			<{# } }>
			<{# if(d[i].activity && d[i].activity.activity_num > 2){ }>
				<div class="activity-num"><{d[i].activity.activity_num}>个活动 <i class="fa fa-arrow-down"></i></div>
			<{# } }>
			<{# var num = 0; }>
			<{# if(d[i].activity && d[i].activity.first_order_status == 1){ }>
				<{# num++ }>
				<div class="xin">
					新用户在线支付
					<{# for(var j in d[i].activity.first_order_data){ }>
					满<{d[i].activity.first_order_data[j].condition}>元减<{d[i].activity.first_order_data[j].back}>,
					<{# } }>
				</div>
			<{# } }>
			<{# if(d[i].activity && d[i].activity.discount_status == 1){ }>
				<{# num++ }>
				<div class="minus">
					在线支付
					<{# for(var j in d[i].activity.discount_data){ }>
					满<{d[i].activity.discount_data[j].condition}>元减<{d[i].activity.discount_data[j].back}>,
					<{# } }>
				</div>
			<{# } }>
			<{# if(d[i].activity && d[i].activity.grant_status == 1){ }>
				<{# num++ }>
				<div class="activity-row zeng <{# if(num > 2){ }>hide<{# } }>">
					在线支付
					<{# for(var j in d[i].activity.grant_data){ }>
					满<{d[i].activity.grant_data[j].condition}>元赠<{d[i].activity.grant_data[j].back}>,
					<{# } }>
				</div>
			<{# } }>
			<{# if(d[i].activity && d[i].activity.collect_coupon_status == 1){ }>
				<{# num++ }>
				<div class="activity-row coupon <{# if(num > 2){ }>hide<{# } }>">
					进店可领取代金券
				</div>
			<{# } }>
			<{# if(d[i].delivery_free_price > 0){ }>
				<{# num++ }>
				<div class="activity-row free <{# if(num > 2){ }>hide<{# } }>">
					满<{d[i].delivery_free_price}>元免配送费
				</div>
			<{# } }>

			<{# if(d[i].hot_goods && d[i].hot_goods.length != 0){ }>
			<div class="dashed-line"></div>
			<div class="hot">
				热销:
				<{# for(var j in d[i].hot_goods){ }>
					<{d[i].hot_goods[j].title}>
				<{# } }>
			</div>
			<{# } }>
		</div>
	</div>
	<{# } }>
</script>
