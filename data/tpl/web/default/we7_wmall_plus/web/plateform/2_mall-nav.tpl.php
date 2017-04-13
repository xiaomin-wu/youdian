<?php defined('IN_IA') or exit('Access Denied');?><ul class="nav nav-tabs">
	<li <?php  if($do == 'ad') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('ptfad');?>"> 全屏引导页</a></li>
	<li <?php  if($do == 'slide') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('ptfslide');?>"> 幻灯片</a></li>
	<li <?php  if($do == 'category') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('ptfcategory');?>"> 导航图标</a></li>
	<li <?php  if($do == 'nav') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('ptfnav');?>"> 图片魔方</a></li>
	<li <?php  if($do == 'notice') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('ptfnotice');?>"> 公告</a></li>
</ul>
