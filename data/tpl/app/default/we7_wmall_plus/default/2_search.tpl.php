<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('header', TEMPLATE_INCLUDEPATH)) : (include template('header', TEMPLATE_INCLUDEPATH));?>
<div class="page home search" id="page-app-store-search">
	<header class="bar bar-nav">
		<a class="pull-left back" href="javascript:;"><i class="fa fa-arrow-left"></i></a>
		<a class="pull-right search-block" href="<?php  echo $this->createMobileUrl('hunt');?>">
			<i class="fa fa-search"></i>
		</a>
		<h1 class="title">
			<?php  if(!empty($categorys[$_GPC['cid']]['title'])) { ?><?php  echo $categorys[$_GPC['cid']]['title'];?><?php  } else { ?>全部商家<?php  } ?>
		</h1>
	</header>
	<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('nav', TEMPLATE_INCLUDEPATH)) : (include template('nav', TEMPLATE_INCLUDEPATH));?>
	<div class="content lazyload-container">
		<div class="hide bind-data" data-lat="<?php  echo $lat;?>" data-lng="<?php  echo $lng;?>" data-cid="<?php  echo $_GPC['cid'];?>" data-dis="<?php  echo $_GPC['dis'];?>" data-order="<?php  echo $_GPC['order'];?>">dd</div>
		<div class="buttons-tab select-tab">
			<a href="javascript:;" class="button"><?php  if(!empty($categorys[$_GPC['cid']]['title'])) { ?><?php  echo $categorys[$_GPC['cid']]['title'];?><?php  } else { ?>商家分类<?php  } ?> <span class="fa"></span></a>
			<div class="drop-menu-list">
				<div class="list-block">
					<ul>
						<li><a class="list-button item-link border-1px-tb" href="<?php  echo $this->createMobileUrl('search', array('cid' => 0, 'order' => $_GPC['order'], 'dis' => $_GPC['dis']));?>">全部</a></li>
						<?php  if(is_array($categorys)) { foreach($categorys as $row) { ?>
							<li>
								<a class="list-button item-link border-1px-b" href="<?php  echo $row['link'];?>">
									<?php  echo $row['title'];?>
									<?php  if($_GPC['cid'] == $row['id']) { ?><i class="fa fa-selected"></i><?php  } ?>
								</a>
							</li>
						<?php  } } ?>
					</ul>
				</div>
			</div>
			<a href="javascript:;" class="button"><?php  if(!empty($orderbys[$_GPC['order']]['title'])) { ?><?php  echo $orderbys[$_GPC['order']]['title'];?><?php  } else { ?>智能排序<?php  } ?> <span class="fa"></span></a>
			<div class="drop-menu-list">
				<div class="list-block">
					<ul>
						<li><a class="list-button item-link border-1px-tb" href="<?php  echo $this->createMobileUrl('search', array('order' => '', 'cid' => $_GPC['cid'], 'dis' => $_GPC['dis']));?>"><span class="icon"></span>全部</a></li>
						<?php  if(is_array($orderbys)) { foreach($orderbys as $row) { ?>
						<li>
							<a class="list-button item-link border-1px-b"  href="<?php  echo $this->createMobileUrl('search', array('order' => $row['key'], 'cid' => $_GPC['cid'], 'dis' => $_GPC['dis']));?>">
								<span class="<?php  echo $row['css'];?>"></span>
								<?php  echo $row['title'];?>
								<?php  if($_GPC['order'] == $row['key']) { ?><i class="fa fa-selected"></i><?php  } ?>
							</a>
						</li>
						<?php  } } ?>
					</ul>
				</div>
			</div>
			<a href="javascript:;" class="button"><?php  if(!empty($discounts[$_GPC['dis']]['title'])) { ?><?php  echo $discounts[$_GPC['dis']]['title'];?><?php  } else { ?>优惠活动<?php  } ?> <span class="fa"></span></a>
			<div class="drop-menu-list">
				<div class="list-block">
					<ul>
						<li><a class="list-button item-link border-1px-tb" href="<?php  echo $this->createMobileUrl('search', array('dis' => '', 'cid' => $_GPC['cid'], 'order' => $_GPC['order']));?>"><span class="icon"></span>全部</a></li>
						<?php  if(is_array($discounts)) { foreach($discounts as $row) { ?>
						<li>
							<a class="list-button item-link border-1px-b" href="<?php  echo $this->createMobileUrl('search', array('dis' => $row['key'], 'cid' => $_GPC['cid'], 'order' => $_GPC['order']));?>">
								<span class="<?php  echo $row['css'];?>"></span>
								<?php  echo $row['title'];?>
								<?php  if($_GPC['dis'] == $row['key']) { ?><i class="fa fa-selected"></i><?php  } ?>
							</a>
						</li>
						<?php  } } ?>
					</ul>
				</div>
			</div>
		</div>
		<?php  if($carousel['slide_status'] == 1 && !empty($carousel['slide'])) { ?>
			<div class="swiper-container slide border-1px-t" data-space-between='20' data-pagination='.swiper-pagination' data-autoplay="2000">
				<div class="swiper-wrapper">
					<?php  if(is_array($carousel['slide'])) { foreach($carousel['slide'] as $slide) { ?>
						<div class="swiper-slide" data-link="<?php  echo $slide['link'];?>">
							<img src="<?php  echo tomedia($slide['thumb']);?>" alt="">
						</div>
					<?php  } } ?>
				</div>
				<div class="swiper-pagination"></div>
			</div>
		<?php  } ?>
		<?php  if($carousel['nav_status'] == 1 && !empty($carousel['nav'])) { ?>
			<div class="search-discount border-1px-tb">
				<?php  if(is_array($carousel['nav'])) { foreach($carousel['nav'] as $nav) { ?>
					<?php  $i++;?>
					<div class="discount-item pull-left <?php  if($i == 1) { ?>border-1px-r<?php  } ?>" data-link="<?php  echo $nav['link'];?>">
						<div class="discount-item-info pull-left">
							<p class="store-title"><?php  echo $nav['title'];?></p>
							<p class="store-subtitle"><?php  echo $nav['sub_title'];?></p>
						</div>
						<div class="discount-item-image pull-left">
							<img src="<?php  echo tomedia($nav['thumb']);?>" alt="">
						</div>
						<div class="clearfix"></div>
					</div>
				<?php  } } ?>
				<div class="clearfix"></div>
			</div>
		<?php  } ?>
		<div class="store-list store-empty" id="store-list">
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
	$(document).on('click', '.swiper-slide, .discount-item', function(){
		var url = $(this).data('link');
		location.href = url;
		return;
	});
	$(document).on("pageInit", "#page-app-store-search", function(e, id, page) {
		var $this = $(page).find('.bind-data');
		var params = {
			lat: $this.data('lat'),
			lng: $this.data('lng'),
			dis: $this.data('dis'),
			cid: $this.data('cid'),
			order: $this.data('order')
		}
		if(!params.lat || !params.lng) {
			var map, geolocation;
			map = new AMap.Map('allmap');
			map.plugin('AMap.Geolocation', function() {
				geolocation = new AMap.Geolocation({
					enableHighAccuracy: true //是否使用高精度定位，默认:true
				});
				geolocation.getCurrentPosition();
				AMap.event.addListener(geolocation, 'complete', getStoreList);//返回定位信息
				AMap.event.addListener(geolocation, 'error', function(){
					if(!tiny.cookie.get('__search_getPosition')) {
						location.reload();
						tiny.cookie.set('__search_getPosition', 1, 300);
					} else {
						getStoreList();
					}
				});//返回定位出错信息
			});
		} else {
			getStoreList();
		}
		function getStoreList() {
			$.post("<?php  echo $this->createMobileUrl('search', array('op' => 'list'));?>", params, function(data){
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
	});
});
</script>
<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('common', TEMPLATE_INCLUDEPATH)) : (include template('common', TEMPLATE_INCLUDEPATH));?>
<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('footer', TEMPLATE_INCLUDEPATH)) : (include template('footer', TEMPLATE_INCLUDEPATH));?>