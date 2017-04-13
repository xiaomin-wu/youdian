<?php defined('IN_IA') or exit('Access Denied');?><script type="text/javascript">
	function clicklink(href, title) {
		if(href=='tel:'){
			require(['util'],function(u){
				u.message('请添加一键拨号号码.');
			});
			return;
		}

		if($.isFunction(<?php  echo $callback;?>)){
			<?php  echo $callback;?>(href, title);
		}
	}
	function retrunLinkBrowser() {
		$(".link-browser").removeClass('hide');
		$(".link-modal > div").addClass('hide');
	}
</script>

<style type="text/css">
	.link-browser ul li{width: 120px; }
	.list-group .list-group-item a{color:#428bca;}
	.link-browser .page-header, .link-modal .page-header{margin:25px 0 10px;}
	.link-browser .page-header:first-child, .link-modal .page-header:first-of-type{margin-top:0;}
	.link-browser div.btn, .link-modal div.btn{min-width:100px; text-align:center; margin:5px 2px;}
</style>

<div class="link-browser">
	<?php  if(is_array($data)) { foreach($data as $da) { ?>
		<div class="page-header">
			<h4><i class="fa fa-folder-open-o"></i> <?php  echo $da['title'];?></h4>
		</div>
		<?php  if(is_array($da['items'])) { foreach($da['items'] as $item) { ?>
			<div class="btn btn-default" onclick="clicklink('<?php  echo $item['url'];?>', '<?php  echo $item['title'];?>');" title="<?php  echo $item['title'];?>"><?php  echo $item['title'];?></div>
		<?php  } } ?>
	<?php  } } ?>
</div>