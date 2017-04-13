<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('header', TEMPLATE_INCLUDEPATH)) : (include template('header', TEMPLATE_INCLUDEPATH));?>
<div class="page message">
	<div class="content">
		<div class="container <?php  echo $type;?>">
			<div class="icon-area"><i class="fa fa-icon"></i></div>
			<div class="text-area">
				<h2 class="msg-title"><?php  echo $msg;?></h2>
				<div class="desc"><?php  echo $tip;?></div>
			</div>
			<div class="btn-area">
				<p>
					<a href="<?php  echo $redirect;?>" class="button"><?php  echo $btn_text;?></a>
				</p>
			</div>
			<div class="extra-area">
				<a href="<?php  echo $this->createMobileUrl('index');?>">返回首页</a>
			</div>
		</div>
	</div>
</div>
<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('common', TEMPLATE_INCLUDEPATH)) : (include template('common', TEMPLATE_INCLUDEPATH));?>
<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('footer', TEMPLATE_INCLUDEPATH)) : (include template('footer', TEMPLATE_INCLUDEPATH));?>