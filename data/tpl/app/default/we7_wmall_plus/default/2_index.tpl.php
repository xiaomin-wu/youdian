<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('header', TEMPLATE_INCLUDEPATH)) : (include template('header', TEMPLATE_INCLUDEPATH));?>
<div class="page home" id="page-app-index">
	<span id="js-lat" class="hide"><?php  echo $_GPC['lat'];?></span>
	<span id="js-lng" class="hide"><?php  echo $_GPC['lng'];?></span>
	<header class="bar bar-nav">
		<a class="pull-right search-block" href="<?php  echo $this->createMobileUrl('hunt');?>">
			<i class="fa fa-search"></i>
		</a>
		<h1 class="title">
			<a id="position" class="external" href="<?php  echo $this->createMobileUrl('location');?>"><?php  echo $_GPC['address'];?></a>  <i class="fa fa-arrow-down-fill"></i>
		</h1>
	</header>
	<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('nav', TEMPLATE_INCLUDEPATH)) : (include template('nav', TEMPLATE_INCLUDEPATH));?>
	<div class="content lazyload-container">
		<?php  if(!empty($slides)) { ?>
			<div class="swiper-container slide" data-space-between='0' data-pagination='.swiper-slide-pagination' data-autoplay="10000">
				<div class="swiper-wrapper">
					<?php  if(is_array($slides)) { foreach($slides as $slide) { ?>
						<div class="swiper-slide" data-link="<?php  echo $slide['link'];?>">
							<img src="<?php  echo tomedia($slide['thumb']);?>"alt="">
						</div>
					<?php  } } ?>
				</div>
				<div class="swiper-pagination swiper-slide-pagination"></div>
			</div>
		<?php  } ?>
		<?php  if(!empty($categorys_chunk)) { ?>
			<div class="swiper-container category" data-space-between='0' data-pagination='.swiper-category-pagination' data-autoplay="0">
				<div class="swiper-wrapper">
					<?php  if(is_array($categorys_chunk)) { foreach($categorys_chunk as $row) { ?>
						<div class="swiper-slide">
							<div class="row no-gutter nav">
								<?php  if(is_array($row)) { foreach($row as $category) { ?>
									<div class="col-25">
										<a href="<?php  echo $category['link'];?>">
											<img src="<?php  echo tomedia($category['thumb']);?>" alt="<?php  echo $category['title'];?>" />
											<div class="text-center"><?php  echo $category['title'];?></div>
										</a>
									</div>
								<?php  } } ?>
							</div>
						</div>
					<?php  } } ?>
				</div>
				<?php  if(count($categorys_chunk) > 1) { ?>
					<div class="swiper-pagination swiper-category-pagination"></div>
				<?php  } ?>
			</div>
		<?php  } ?>
		<?php  if(!empty($notices)) { ?>
			<div class="headlines swiper-container border-1px-t" data-direction="vertical" data-pagination="" data-space-between="8" data-autoplay="2000">
				<div class="headline-logo pull-left"><img src="<?php echo MODULE_URL;?>resource/app/img/head_line.png" alt=""></div>
				<div class="headline-news pull-left swiper-wrapper">
					<?php  if(is_array($notices)) { foreach($notices as $notice) { ?>
					<div class="swiper-slide">
						<?php  if(!empty($notice['link'])) { ?>
							<a href="<?php  echo $notice['link'];?>"><?php  echo $notice['title'];?></a>
						<?php  } else { ?>
							<a href="<?php  echo $this->createMobileUrl('notice', array('id' => $notice['id']))?>"><?php  echo $notice['title'];?></a>
						<?php  } ?>
					</div>
					<?php  } } ?>
				</div>
				<i class="fa fa-arrow-right pull-left"></i>
			</div>
		<?php  } ?>

		<?php  if($_W['we7_wmall_plus']['config']['imgnav_status'] == 1 && !empty($_W['we7_wmall_plus']['config']['imgnav_data'])) { ?>
			<div class="row no-gutter sborder activity" style="z-index: 1000">
				<?php  if(is_array($_W['we7_wmall_plus']['config']['imgnav_data'])) { foreach($_W['we7_wmall_plus']['config']['imgnav_data'] as $i => $nav) { ?>
					<div class="col-50 sborder">
						<a href="<?php  echo $nav['link'];?>">
							<div class="row no-gutter">
								<?php  if($i % 2 == 0) { ?>
									<div class="col-60">
										<div class="heading"><?php  echo $nav['title'];?></div>
										<div class="sub-heading"><?php  echo $nav['tips'];?></div>
									</div>
									<div class="col-40 text-center">
										<img src="<?php  echo tomedia($nav['img']);?>" alt="" />
									</div>
								<?php  } else { ?>
									<div class="col-40 text-center">
										<img src="<?php  echo tomedia($nav['img']);?>" alt="" />
									</div>
									<div class="col-60">
										<div class="heading"><?php  echo $nav['title'];?></div>
										<div class="sub-heading"><?php  echo $nav['tips'];?></div>
									</div>
								<?php  } ?>
							</div>
						</a>
					</div>
					<?php  $i++?>
				<?php  } } ?>
			</div>
		<?php  } ?>
		<?php  if(!empty($recommends)) { ?>
			<div class="selective">
				<div class="selective-tab">
					为您优选
					<a class="more" href="<?php  echo $this->createMobileUrl('brand-channel')?>">更多<i class="fa fa-arrow-right"></i></a>
				</div>
				<div class="selective-info row">
					<?php  if(is_array($recommends)) { foreach($recommends as $recommend) { ?>
						<a href="<?php  echo $this->createMobileUrl('goods', array('sid' => $recommend['id']))?>" class="col-33 selective-item">
							<img src="../addons/we7_wmall_plus/resource/app/img/hm.gif" data-original="<?php  echo tomedia($recommend['logo']);?>" class="lazyload" alt="">
							<p class="selective-title"><?php  echo $recommend['title'];?></p>
						</a>
					<?php  } } ?>
					<div class="clearfix"></div>
				</div>
			</div>
		<?php  } ?>
		<div class="buttons-tab select-tab">
			<a href="javascript:;" class="button">商家分类 <span class="fa"></span></a>
			<div class="drop-menu-list">
				<div class="list-block">
					<ul>
						<li><a class="list-button item-link border-1px-tb" href="<?php  echo $this->createMobileUrl('search', array('cid' => 0));?>">全部</a></li>
						<?php  if(is_array($categorys)) { foreach($categorys as $row) { ?>
							<li><a class="list-button item-link border-1px-b" href="<?php  echo $row['link'];?>"><?php  echo $row['title'];?></a></li>
						<?php  } } ?>
					</ul>
				</div>
			</div>
			<a href="javascript:;" class="button">智能排序 <span class="fa"></span></a>
			<div class="drop-menu-list">
				<div class="list-block">
					<ul>
						<li><a class="list-button item-link border-1px-tb" href="<?php  echo $this->createMobileUrl('search', array('order' => ''));?>"><span class="icon"></span>全部</a></li>
						<?php  if(is_array($orderbys)) { foreach($orderbys as $row) { ?>
							<li><a class="list-button item-link border-1px-b"  href="<?php  echo $this->createMobileUrl('search', array('order' => $row['key']));?>"><span class="<?php  echo $row['css'];?>"></span><?php  echo $row['title'];?></a></li>
						<?php  } } ?>
					</ul>
				</div>
			</div>
			<a href="javascript:;" class="button">优惠活动 <span class="fa"></span></a>
			<div class="drop-menu-list">
				<div class="list-block">
					<ul>
						<li><a class="list-button item-link border-1px-tb" href="<?php  echo $this->createMobileUrl('search', array('dis' => ''));?>"><span class="icon"></span>全部</a></li>
						<?php  if(is_array($discounts)) { foreach($discounts as $row) { ?>
							<li><a class="list-button item-link border-1px-b" href="<?php  echo $this->createMobileUrl('search', array('dis' => $row['key']));?>"><span class="<?php  echo $row['css'];?>"></span><?php  echo $row['title'];?></a></li>
						<?php  } } ?>
					</ul>
				</div>
			</div>
		</div>
		<div class="store-list store-empty lazyload-container" id="store-list">
			<div class="common-no-con">
				<img src= "<?php echo MODULE_URL;?>resource/app/img/store_no_con.png" alt="" />
				<p>努力加载中...</p>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript" src="http://webapi.amap.com/maps?v=1.3&key=550a3bf0cb6d96c3b43d330fb7d86950"></script>
<script>
$(function(){
	$(document).on('click', '.slide .swiper-slide', function(){
		var url = $(this).data('link');
		location.href = url;
		return;
	});

	function getLocation() {
		var map, geolocation;
		map = new AMap.Map('allmap');
		map.plugin('AMap.Geolocation', function() {
			geolocation = new AMap.Geolocation({
				enableHighAccuracy: true //是否使用高精度定位，默认:true
			});
			geolocation.getCurrentPosition();
			AMap.event.addListener(geolocation, 'complete', getPositionInfo);//返回定位信息
			AMap.event.addListener(geolocation, 'error', function(){
				if(!tiny.cookie.get('__getPosition')) {
					location.reload();
					tiny.cookie.set('__getPosition', 1, 300);
				} else {
					getPositionInfo();
				}
			});
		});
	}

	function getPositionInfo(data) {
		if(typeof data != 'undefined') {
			tiny.cookie.set('__getPosition', 0, -1000);
			var point = data.position;
			$('#js-lat').html(point.lat);
			$('#js-lng').html(point.lng);
			var lnglatXY = [point.lng, point.lat]; //已知点坐标
			var map = new AMap.Map('allmap');
			map.plugin('AMap.Geocoder', function() {
				var geocoder = new AMap.Geocoder();
				geocoder.getAddress(lnglatXY, function(status, result) {
					if (status === 'complete' && result.info === 'OK') {
						var obj = result.regeocode.addressComponent;
						var position = result.regeocode.formattedAddress;
						position = position.replace(obj.province, '');
						position = position.replace(obj.district, '');
						position = position.replace(obj.city, '');
						$('#position').html(position);
					}
				});
			});
		}
		getStoreList();
		return ;
	}

	function getStoreList() {
		var params = {
			lat: $('#js-lat').html(),
			lng: $('#js-lng').html()
		}
		$.post("<?php  echo $this->createMobileUrl('index', array('op' => 'list'));?>", params, function(data){
			var result = $.parseJSON(data);
			if(result.message.error != 0) {
				$.toast(result.message.message);
				return false;
			}
			if(result.message.message.length == 0) {
				$('#store-list').addClass('store-empty');
				$('#store-list .common-no-con').find('p').html('没有符合条件的商户');
				$('#store-list .common-no-con').removeClass('hide');
			} else {
				var gettpl = $('#tpl-store-list').html();
				laytpl(gettpl).render(result.message.message, function(html){
					$('#store-list').removeClass('store-empty');
					$('#store-list .common-no-con').addClass('hide');
					$('#store-list').append(html);
					$('#store-list').find("img.lazyload").lazyload({
						container: $('.lazyload-container'),
						effect : 'fadeIn',
						threshold : 200
					});
					return;
				});
			}
		});
	}
	<?php  if(!$_GPC['d']) { ?>
		getLocation();
	<?php  } else { ?>
		getStoreList();
	<?php  } ?>
});
</script>
<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('common', TEMPLATE_INCLUDEPATH)) : (include template('common', TEMPLATE_INCLUDEPATH));?>
<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('footer', TEMPLATE_INCLUDEPATH)) : (include template('footer', TEMPLATE_INCLUDEPATH));?>
