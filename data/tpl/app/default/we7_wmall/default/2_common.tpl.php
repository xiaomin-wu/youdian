<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('template', TEMPLATE_INCLUDEPATH)) : (include template('template', TEMPLATE_INCLUDEPATH));?>
<script>
(function($) {
	$.fn.sorted = function(customOptions) {
		var options = {
			reversed: false,
			by: function(a) { return a.text(); }
		};
		$.extend(options, customOptions);
		$data = $(this);
		arr = $data.get();
		arr.sort(function(a, b) {
			var valA = options.by($(a));
			var valB = options.by($(b));
			if (options.reversed) {
				return (valA < valB) ? 1 : (valA > valB) ? -1 : 0;
			} else {
				return (valA < valB) ? -1 : (valA > valB) ? 1 : 0;
			}
		});
		return $(arr);
	};
})(jQuery);
$(function(){
	wx.ready(function () {
		sharedata = {
			title: "<?php  echo $_share['title'];?>",
			desc: "<?php  echo $_share['desc'];?>",
			link: "<?php  echo $_share['link'];?>",
			imgUrl: "<?php  echo $_share['imgUrl'];?>",
			success: function(){},
			cancel: function(){}
		};
		wx.onMenuShareAppMessage(sharedata);
		wx.onMenuShareTimeline(sharedata);
	});

	$(document).on('click', '.activity-containter', function(){
		if($(this).hasClass('active')) {
			$(this).find('.activity-row').addClass('hide');
			$(this).find('.activity-num i').addClass('fa-arrow-down').removeClass('fa-arrow-up');
		} else {
			$(this).find('.activity-row').removeClass('hide');
			$(this).find('.activity-num i').addClass('fa-arrow-up').removeClass('fa-arrow-down');
		}
		$(this).toggleClass('active');
	});

	$(document).on('click', '.home .select-tab a.button', function(){
		var flag = false;
		if($(this).hasClass('button-active')) {
			flag = true;
		}
		$('.home .select-tab a.button').removeClass('button-active');
		$('.home .drop-menu-list').hide();
		if(!flag) {
			$(this).addClass('button-active');
			$(this).next('.drop-menu-list').show();
		}
	});

	$(document).off('click', '.comment-images-item');
	$(document).on('click', '.comment-images-item', function(){
		var $parents = $(this).parent();
		var thumbs = [];
		$($parents).find('img').each(function(){
			var thumb = $(this).attr('src');
			if(thumb) {
				thumbs.push(thumb);
			}
		});
		var myPhotoBrowserPopup = $.photoBrowser({
			photos: thumbs
		});
		myPhotoBrowserPopup.open();
	});

	$(document).on("pageInit", "#page-app-store, #page-app-goods, #page-app-store-comment", function(e, id, page) {
		$(document).on('click', '#btn-favorite', function(){
			var uid = $(this).data('uid');
			if(!uid) {
				$.confirm('登陆后才可以收藏,现在去登陆?', function() {
					location.href = "<?php  echo murl('auth/login');?>";
					return false;
				});
			}
			var $this = $(this);
			var id = $(this).data('id');
			if(!id) return false;
			var type = 'star';
			if($(this).find('i').hasClass('fa-favor-fill')) {
				type = 'cancel';
			}
			$.post("<?php  echo $this->createMobileUrl('mine', array('op' => 'favorite'));?>", {id: id, type: type}, function(data){
				var result = $.parseJSON(data);
				if(result.message.errno != 0) {
					$.toast(result.message.message);
				} else {
					if(type == 'cancel') {
						$this.find('i').removeClass('fa-favor-fill').addClass('fa-favor');
						$.toast('取消收藏成功');
					} else {
						$this.find('i').addClass('fa-favor-fill').removeClass('fa-favor');
						$.toast('添加收藏成功');
					}
				}
				return false;
			});
		});
	});

	$(document).on("pageInit", "#page-app-order", function(e, id, page) {
		var loading = false;
		$(page).on('infinite', '.infinite-scroll',function() {
			var $this = $(this);
			var id = $this.data('min');
			if(!id) return;
			if (loading) return;

			loading = true;
			$this.find('.infinite-scroll-preloader').removeClass('hide');
			$.post("<?php  echo $this->createMobileUrl('order', array('op' => 'more'))?>", {id: id, time: timeStamp}, function(data){
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
						$this.find('.order-list').append(html);
					}, 1000);
				});
			});
		});
	});

	/*取消订单*/
	$(document).on('click', '.order-cancel', function(){
		var id = $(this).data('id');
		$.confirm('确定取消该订单吗?', function () {
			$.post("<?php  echo $this->createMobileUrl('order', array('op' => 'cancel'))?>", {id: id}, function(data) {
				var result = $.parseJSON(data);
				if(result.message.errno != 0) {
					$.toast(result.message.message);
				} else {
					$.toast('订单取消成功', location.href, 1000);
				}
				return false;
			});
		});
	})

	/*确认送达*/
	$(document).on('click', '.order-end', function(){
		var id = $(this).data('id');
		var order_type = $(this).data('type');
		var tips = ['', '你确定收到该商家的外卖?', '确认已自提?']
		$.confirm(tips[order_type], function () {
			$.post("<?php  echo $this->createMobileUrl('order', array('op' => 'end'))?>", {id: id}, function(data) {
				var result = $.parseJSON(data);
				if(result.message.errno != 0) {
					$.toast(result.message.message);
				} else {
					$.toast('确认订单完成成功', "<?php  echo $this->createMobileUrl('order', array('op' => 'comment'))?>&id=" + id, 1000);
				}
				return false;
			});
		});
	});

	/*催单*/
	$(document).on('click', '.order-remind', function(){
		var id = $(this).data('id');
		$.post("<?php  echo $this->createMobileUrl('order', array('op' => 'remind'))?>", {id: id}, function(data) {
			var result = $.parseJSON(data);
			if(result.message.errno != 0) {
				$.toast(result.message.message);
			} else {
				$.toast('进行催单成功');
			}
			return false;
		});
	});

	/*商品评价*/
	$(document).on("pageInit", "#page-app-add-comment", function(e, id, page) {
		$(document).on('click', '.star-outline label', function(){
			$(this).parent().find('.radio').removeClass('checked').prop('checked', false);
			$(this).prevAll().find('.radio').prop('checked', true);
			$(this).find('.radio').addClass('checked').prop('checked', true);
		});

		$(document).on('click', '.submit-com', function(){
			var $this = $(this);
			var order_id = $this.data('id');
			if($this.hasClass('disabled')) {
				return false;
			}
			$this.addClass('disabled');

			var params = {
				id: order_id,
				goods: {},
				thumbs: []
			};
			$('.star-outline').each(function(){
				var name = $(this).data('name');
				var value = $(this).find('.radio.checked').val();
				params[name] = value;
			});
			if(!params.delivery_service) {
				$this.removeClass('disabled')
				$.toast('请评价配送服务');
				return false;
			}
			if(!params.goods_quality) {
				$this.removeClass('disabled')
				$.toast('请评价商品质量');
				return false;
			}
			var note = $.trim($('.note').val());
/*
			if(!note) {
				$this.removeClass('disabled')
				$.toast('点评不能为空');
				return false;
			}
*/
			params.note = note;
			$('.goods-list').each(function(){
				var id = $(this).data('id');
				params.goods[id] = $(this).find('.radio:checked').val();
			});
			$('.tpl-image .image-item input').each(function(){
				var value = $.trim($(this).val());
				if(value) {
					params.thumbs.push(value);
				}
			});
			$.post("<?php  echo $this->createMobileUrl('order', array('op' => 'comment'));?>", params, function(data){
				var result = $.parseJSON(data);
				if(result.message.errno != 0) {
					$this.removeClass('disabled')
					$.toast(result.message.message);
				} else {
					$.toast('评价成功', "<?php  echo $this->createMobileUrl('order');?>");
				}
				return false;
			});
		})
	});

	$(document).on("pageInit", "#page-app-my-comment", function(e, id, page) {
		var loading = false;
		$(page).on('infinite', '.infinite-scroll',function() {
			var $this = $(this);
			var id = $this.data('min');

			if(!id) return;
			if (loading) return;

			loading = true;
			$this.find('.infinite-scroll-preloader').removeClass('hide');
			$.post("<?php  echo $this->createMobileUrl('comment', array('op' => 'more'))?>", {id: id}, function(data){
				var result = $.parseJSON(data);
				$this.attr('data-min', result.message.min);

				if(!result.message.min) {
					$.detachInfiniteScroll($('.infinite-scroll'));
					$('.infinite-scroll-preloader').remove();
					return;
				}

				$this.find('.infinite-scroll-preloader').removeClass('hide');
				var gettpl = $('#tpl-my-comment').html();
				loading = false;
				laytpl(gettpl).render(result.message.message, function(html){
					setTimeout(function() {
						$this.find('.comment-list').append(html);
					}, 1000);
				});
			});
		});
	});

	$(document).on("pageInit", "#page-app-store-comment", function(e, id, page) {
		var loading = false;
		$(page).on('infinite', '.infinite-scroll',function() {
			var $this = $(this);
			var aid = $this.data('min');
			var sid = $this.data('sid');
			var type = $this.data('type');

			if(!id) return;
			if (loading) return;

			loading = true;
			$this.find('.infinite-scroll-preloader').removeClass('hide');
			$.post("<?php  echo $this->createMobileUrl('store', array('op' => 'comment'))?>", {aid: aid, sid: sid, type: type}, function(data){
				var result = $.parseJSON(data);
				$this.attr('data-min', result.message.min);

				if(!result.message.min) {
					$.detachInfiniteScroll($('.infinite-scroll'));
					$('.infinite-scroll-preloader').remove();
					return;
				}

				$this.find('.infinite-scroll-preloader').removeClass('hide');
				var gettpl = $('#tpl-store-comment').html();
				loading = false;
				laytpl(gettpl).render(result.message.message, function(html){
					setTimeout(function() {
						$this.find('.comment-list ul').append(html);
					}, 1000);
				});
			});
		});
	});

	$.init();
});
</script>