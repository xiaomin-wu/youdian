<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('header', TEMPLATE_INCLUDEPATH)) : (include template('header', TEMPLATE_INCLUDEPATH));?>
<div class="page errander-index">
	<header class="bar bar-nav common-bar-nav">
		<a class="pull-left back" href="javascript:;"><i class="fa fa-arrow-left"></i></a>
		<h1 class="title">随意购</h1>
		<a class="pull-right" href="<?php  echo $this->createMobileUrl('errander-order', array('op' => 'list'));?>">订单</a>
	</header>
	<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('nav', TEMPLATE_INCLUDEPATH)) : (include template('nav', TEMPLATE_INCLUDEPATH));?>
	<div class="content">
		<div class="border-1px-t">
			<div class="comindex-main">
				<div class="com-map" id="com-map"></div>
				<?php  if(!empty($orders)) { ?>
					<div class="com-status">
						<div class="swiper-container" data-direction="vertical" data-space-between="100" data-autoplay="2000">
							<div class="swiper-wrapper">
								<?php  if(is_array($orders)) { foreach($orders as $order) { ?>
									<div class="swiper-slide">
										<a href="<?php  echo $this->createMobileUrl('errander-index', array('op' => 'submit', 'id' => $order['order_cid']));?>">
											<img src="<?php  echo tomedia($order['thumb']);?>">
											<?php  echo $order['anonymous_username'];?>购买了<?php  echo $order['goods_name'];?>
											<i class="fa fa-arrow-right"></i>
										</a>
									</div>
								<?php  } } ?>
							</div>
						</div>
					</div>
				<?php  } ?>
				<div class="com-cate">
					<p class="com-cate-title">平台共有<span class="color-danger"><?php  echo $delivery_num;?></span>位骑士为您服务</p>
					<ul class="com-cate-list">
						<?php  if(is_array($categorys)) { foreach($categorys as $category) { ?>
							<li>
								<a href="<?php  echo $this->createMobileUrl('errander-index', array('op' => 'submit', 'id' => $category['id']));?>">
									<div class="com-pic"><img src="<?php  echo tomedia($category['thumb']);?>" alt=""></div>
									<p><?php  echo $category['title'];?></p>
								</a>
							</li>
						<?php  } } ?>
					</ul>
				</div>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript" src="http://webapi.amap.com/maps?v=1.3&key=550a3bf0cb6d96c3b43d330fb7d86950"></script>
<script>
$(function(){
	var map = new AMap.Map('com-map', {
		resizeEnable: true,
		center: ["<?php  echo $config['map']['location_y'];?>", "<?php  echo $config['map']['location_x'];?>"],
		zoom: 11
	});
	map.plugin('AMap.Geolocation', function() {
		geolocation = new AMap.Geolocation({
			enableHighAccuracy: true,//是否使用高精度定位，默认:true
			showMarker: true,      //定位成功后在定位到的位置显示点标记，默认：true
			showButton: false
		});
		geolocation.getCurrentPosition();
		map.addControl(geolocation);
		AMap.event.addListener(geolocation, 'complete', function(){});//返回定位信息
		map.setFitView();
	});

	$.post("<?php  echo $this->createMobileUrl('errander-index', array('op' => 'deliveryer'))?>", function(data){
		var result = $.parseJSON(data);
		if(result.message.errno != -1) {
			$.each(result.message.message, function(k, v){
				var deliveryer = v.deliveryer;
				if(deliveryer.location_x && deliveryer.location_y) {
					marker = new AMap.Marker({
						position: [deliveryer.location_y, deliveryer.location_x],
						offset: new AMap.Pixel(-26, -80),
						content: '<div class="marker-deliveyer-route"><img src="'+ v.deliveryer.avatar +'" alt=""/></div>'
					});
					marker.setMap(map);
				}
			});
		}
	});
});
</script>
<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('common', TEMPLATE_INCLUDEPATH)) : (include template('common', TEMPLATE_INCLUDEPATH));?>
<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('footer', TEMPLATE_INCLUDEPATH)) : (include template('footer', TEMPLATE_INCLUDEPATH));?>