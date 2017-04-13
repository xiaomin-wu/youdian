<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('manage/header', TEMPLATE_INCLUDEPATH)) : (include template('manage/header', TEMPLATE_INCLUDEPATH));?>
<div class="page comment" id="page-manage-comment">
	<header class="bar bar-nav common-bar-nav">
		<a class="icon pull-left fa fa-arrow-left back"></a>
		<h1 class="title">评论管理</h1>
	</header>
	<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('manage/nav', TEMPLATE_INCLUDEPATH)) : (include template('manage/nav', TEMPLATE_INCLUDEPATH));?>
	<div class="content infinite-scroll" data-distance="50" data-min="<?php  echo $min;?>">
		<div class="buttons-tab">
			<a href="<?php  echo $this->createMobileUrl('mgcomment', array('type' => 0));?>" class="button <?php  if($type == 0) { ?>active<?php  } ?>">全部评价</a>
			<a href="<?php  echo $this->createMobileUrl('mgcomment', array('type' => 1));?>" class="button <?php  if($type == 1) { ?>active<?php  } ?>">好评</a>
			<a href="<?php  echo $this->createMobileUrl('mgcomment', array('type' => 2));?>" class="button <?php  if($type == 2) { ?>active<?php  } ?>">中评</a>
			<a href="<?php  echo $this->createMobileUrl('mgcomment', array('type' => 3));?>" class="button <?php  if($type == 3) { ?>active<?php  } ?>">差评</a>
		</div>
		<?php  if(empty($comments)) { ?>
			<div class="no-data">
				<div class="bg"></div>
				<p>没有任何评论哦～</p>
			</div>
		<?php  } else { ?>
			<div class="list-block media-list comment-list">
				<ul>
					<?php  if(is_array($comments)) { foreach($comments as $comment) { ?>
					<li class="border-1px-tb">
						<div class="item-content">
							<div class="item-media">
								<img src="<?php  echo $comment['avatar'];?>" alt="">
							</div>
							<div class="item-inner">
								<div class="item-title-row">
									<div class="item-title"><?php  echo $comment['mobile'];?></div>
									<div class="item-after"><?php  echo $comment['addtime'];?></div>
								</div>
								<div class="item-text">
									<div>
										<div class="star-rank">
											<span class="star-rank-outline">
												<span class="star-rank-active" style="width:<?php  echo $comment['score'];?>%"></span>
											</span>
										</div>
										<span class="audit-info pull-right <?php  echo $comment['status_css'];?>"><?php  echo $comment['status_cn'];?></span>
									</div>
									<?php  if(!empty($comment['note'])) { ?>
										<div class="comment-info"><?php  echo $comment['note'];?></div>
									<?php  } ?>
									<?php  if(!empty($comment['data']['good'])) { ?>
										<div class="comment-favor-oppose">
											<i class="icon favor"></i>
											<?php  if(is_array($comment['data']['good'])) { foreach($comment['data']['good'] as $good) { ?>
											<span><?php  echo $good;?></span>
											<?php  } } ?>
										</div>
									<?php  } ?>
									<?php  if(!empty($comment['thumbs'])) { ?>
										<div class="comment-images-containter row">
											<?php  if(is_array($comment['thumbs'])) { foreach($comment['thumbs'] as $thumb) { ?>
											<div class="col-25 comment-images-item">
												<img src="<?php  echo $thumb;?>" alt=""/>
											</div>
											<?php  } } ?>
										</div>
									<?php  } ?>
									<?php  if(!empty($comment['reply'])) { ?>
										<div class="store-comment">
											<div class="clearfix store-comment-top">
												店家回复<span class="pull-right"><?php  echo $comment['replytime'];?></span>
											</div>
											<div class="info"><?php  echo $comment['reply'];?></div>
										</div>
									<?php  } ?>
								</div>
							</div>
						</div>
						<div class="row border-1px-tb">
							<a href="<?php  echo $this->createMobileUrl('mgorder', array('op' => 'detail', 'id' => $comment['oid']));?>" class="col-25 border-1px-r">查看订单</a>
							<a href="javascript:;" class="comment-status col-25 border-1px-r" data-id="<?php  echo $comment['id'];?>" data-status="2">审核不通过</a>
							<a href="javascript:;" class="comment-status col-25 border-1px-r" data-id="<?php  echo $comment['id'];?>" data-status="1">审核通过</a>
							<a href="javascript:;" class="comment-reply col-25" data-id="<?php  echo $comment['id'];?>" data-status="2" data-type="handel">回复</a>
						</div>
					</li>
					<?php  } } ?>
				</ul>
			</div>
			<div class="infinite-scroll-preloader hide">
				<div class="preloader"></div>
			</div>
		<?php  } ?>
	</div>
</div>

<!-- 回复评论 -->
<div class="popup popup-comment-reply" id="popup-comment-reply">
	<div class="page">
		<header class="bar bar-nav common-bar-nav">
			<h1 class="title">回复客户评论</h1>
			<a class="pull-right close-popup" href="javascript:;">关闭</a>
		</header>
		<div class="content">
			<div class="content-block-title">选择回复</div>
			<div class="list-block media-list">
				<ul>
					<?php  if(is_array($store['comment_reply'])) { foreach($store['comment_reply'] as $reply) { ?>
					<li>
						<label class="label-checkbox item-content">
							<input type="radio" name="" value="">
							<div class="item-media"><i class="icon icon-form-checkbox"></i></div>
							<div class="item-inner">
								<div class="item-text"><?php  echo $reply;?></div>
							</div>
						</label>
					</li>
					<?php  } } ?>
				</ul>
			</div>
			<div class="content-block-title">自定义回复</div>
			<div class="list-block">
				<ul>
					<li class="align-top">
						<div class="item-content">
							<div class="item-inner">
								<div class="item-input">
									<textarea name="reply" id="reply" placeholder="输入回复内容"></textarea>
								</div>
							</div>
						</div>
					</li>
				</ul>
			</div>
			<div class="content-padded">
				<a href="javascript:;" class="button button-big button-fill button-danger">确定</a>
			</div>
		</div>
	</div>
</div>

<script id="tpl-manage-comment" type="text/html">
	<{# for(var i = 0, len = d.length; i < len; i++){ }>
	<li>
		<div class="item-content">
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
						<span class="audit-info pull-right <{d[i].status_css}>"><{d[i].status_cn}></span>
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
		</div>
		<div class="row border-1px-tb">
			<a href="<?php  echo $this->createMobileUrl('mgorder', array('op' => 'detail'));?>&id=<{d[i].oid}>" class="col-25 border-1px-r">查看订单</a>
			<a href="javascript:;" class="comment-status col-25 border-1px-r" data-id="<{d[i].id}>" data-status="2">审核不通过</a>
			<a href="javascript:;" class="comment-status col-25 border-1px-r" data-id="<{d[i].id}>" data-status="1">审核通过</a>
			<a href="javascript:;" class="comment-reply col-25" data-id="<{d[i].id}>" data-status="2" data-type="handel">回复</a>
		</div>
	</li>
	<{# } }>
</script>

<script>
$(function(){
	$(document).on('click', '.comment-status', function(){
		var id = $(this).data('id');
		var status = $(this).data('status');
		$.confirm('确定变更评论状态吗', function(){
			$.post("<?php  echo $this->createMobileUrl('mgcomment', array('op' => 'status'));?>", {id: id, status: status}, function(data){
				var result = $.parseJSON(data);
				if(result.message.errno != 0) {
					$.toast(result.message.message);
				} else {
					$.toast('变更状态成功', location.href);
				}
			});
		});
	});

	$(document).on("click", ".comment-reply", function() {
		var id = $(this).data('id');
		if(!id) {
			return false;
		}
		$('#popup-comment-reply .label-checkbox').unbind().click(function(){
			var reply = $(this).find('.item-text').html();
			$('#popup-comment-reply :radio').prop('checked', false);
			$(this).find(':radio').prop('checked', true);
			$('#reply').val(reply);
		});
		$.popup('.popup-comment-reply');

		$('#popup-comment-reply .button-danger').unbind().click(function(){
			var $this = $(this);
			if($this.hasClass('disabled')) {
				return false;
			}
			var reply = $('#reply').val();
			if(!reply) {
				$.toast('没有设置回复内容');
				return false;
			}
			$this.addClass('disabled');
			$.post("<?php  echo $this->createMobileUrl('mgcomment', array('op' => 'reply'))?>", {id: id, reply: reply}, function(data){
				var result = $.parseJSON(data);
				if(result.message.errno != 0) {
					$this.removeClass('disabled');
					$.toast(result.message.message);
					return false;
				}
				$.toast('回复客户评论成功', location.href);
			});
		});
	});

	$(document).on("pageInit", "#page-manage-comment", function(e, id, page) {
		var loading = false;
		$(page).on('infinite', '.infinite-scroll',function() {
			var $this = $(this);
			var id = $this.data('min');
			if(!id) return;
			if (loading) return;

			loading = true;
			$this.find('.infinite-scroll-preloader').removeClass('hide');
			$.post("<?php  echo $this->createMobileUrl('mgcomment', array('op' => 'list'))?>", {id: id, time: timeStamp}, function(data){
				var result = $.parseJSON(data);
				$this.attr('data-min', result.message.min);

				if(!result.message.min) {
					$.detachInfiniteScroll($('.infinite-scroll'));
					$('.infinite-scroll-preloader').remove();
					return;
				}

				$this.find('.infinite-scroll-preloader').removeClass('hide');
				var gettpl = $('#tpl-manage-comment').html();
				loading = false;
				laytpl(gettpl).render(result.message.message, function(html){
					setTimeout(function() {
						$this.find('.comment-list ul').append(html);
					}, 1000);
				});
			});
		});
	});
});
</script>
<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('manage/common', TEMPLATE_INCLUDEPATH)) : (include template('manage/common', TEMPLATE_INCLUDEPATH));?>
<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('manage/footer', TEMPLATE_INCLUDEPATH)) : (include template('manage/footer', TEMPLATE_INCLUDEPATH));?>