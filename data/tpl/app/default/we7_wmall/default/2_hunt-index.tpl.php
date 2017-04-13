<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('header', TEMPLATE_INCLUDEPATH)) : (include template('header', TEMPLATE_INCLUDEPATH));?>
<div class="page search-result search-hot">
	<div class="bar bar-header-secondary">
		<div class="searchbar">
			<a class="searchbar-arrow back"><i class="fa fa-arrow-left"></i></a>
			<a class="searchbar-cancel">搜索</a>
			<div class="search-input">
				<label class="icon fa fa-search" for="search"></label>
				<input type="search" id='search' name="search" placeholder='请输入商户或商品名称'/>
			</div>
		</div>
	</div>
	<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('nav', TEMPLATE_INCLUDEPATH)) : (include template('nav', TEMPLATE_INCLUDEPATH));?>
	<div class="content">
		<?php  if(!empty($stores)) { ?>
		<div class="search-tag">
			<div class="search-tag-title">热门搜索</div>
			<?php  if(is_array($stores)) { foreach($stores as $store) { ?>
				<span class="search-history" data-value="<?php  echo $store['title'];?>"><a href="javascript:;"><?php  echo $store['title'];?></a></span>
			<?php  } } ?>
		</div>
		<?php  } ?>
		<?php  if(!empty($member['search_data'])) { ?>
		<div class="search-text-list">
			<ul>
				<?php  if(is_array($member['search_data'])) { foreach($member['search_data'] as $data) { ?>
					<li class="search-history" data-value="<?php  echo $data;?>"><a href="javascript:;"><i class="fa fa-time"></i> <?php  echo $data;?></a></li>
				<?php  } ?>
				<li class="last-item"><a href="javascript:;" id="truncate-search-data">清空历史记录</a></li>
			</ul>
		</div>
		<?php  } } ?>
	</div>
</div>
<script>
$(function(){
	$(document).on('click', '#truncate-search-data', function(){
		$.post("<?php  echo $this->createMobileUrl('hunt', array('op' => 'truncate'));?>", {}, function(data){
			$('.search-text-list').remove();
		});
	});
	$(document).on('click', '.search-history', function(){
		var value = $(this).data('value');
		if(!value) {
			return false;
		}
		$('.search-input input').val(value);
		setTimeout(function(){
			$('.searchbar-cancel').trigger('click');
		}, 200)
	});

	$(document).on('click', '.searchbar-cancel', function(){
		var key = $('.search-input input').val();
		if(!key) {
			return false;
		}
		$.showIndicator();
		$.post("<?php  echo $this->createMobileUrl('hunt', array('op' => 'search_data'));?>", {key: key}, function(data){
			location.href = "<?php  echo $this->createMobileUrl('hunt', array('op' => 'search'));?>&key=" + key;
		});
		return false;
	});
});
</script>
<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('common', TEMPLATE_INCLUDEPATH)) : (include template('common', TEMPLATE_INCLUDEPATH));?>
<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('footer', TEMPLATE_INCLUDEPATH)) : (include template('footer', TEMPLATE_INCLUDEPATH));?>