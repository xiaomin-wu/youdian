<?php defined('IN_IA') or exit('Access Denied');?>			</div>
		</div>
	</div>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('public/tiny', TEMPLATE_INCLUDEPATH)) : (include template('public/tiny', TEMPLATE_INCLUDEPATH));?>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('public/footer-base', TEMPLATE_INCLUDEPATH)) : (include template('public/footer-base', TEMPLATE_INCLUDEPATH));?>
