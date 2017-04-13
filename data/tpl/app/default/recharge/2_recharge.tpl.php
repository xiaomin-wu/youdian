<?php defined('IN_IA') or exit('Access Denied');?><?php  define(MUI, true);?>
<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>
<div class="mui-content mc-recharge">
	<form  method="post" role="form" id="form1">
		<input type="hidden" name="c" value="entry" />
		<input type="hidden" name="m" value="recharge" />
		<input type="hidden" name="i" value="<?php  echo $_W['uniacid'];?>" />
		<input type="hidden" name="do" value="pay" />
	<div class="mui-row mui-mr5 mui-ml5">
		<?php  if(!empty($recharge_settings['params']['recharge_type']) && !empty($recharge_settings['params']['recharges'])) { ?>
		<?php  if(is_array($recharge_settings['params']['recharges'])) { foreach($recharge_settings['params']['recharges'] as $key => $row) { ?>
		<?php  if(!empty($row['back']) && !empty($row['condition'])) { ?>
		<div class="mui-col-xs-4 mui-pa5 mui-mt5">
			<div class="mui-thumbnail mui-text-center mui-text-info" data-recharge="<?php  echo $row['condition'];?>" data-backtype="<?php  echo $row['backtype'];?>" data-back="<?php  echo $row['back'];?>">
				<div class="mui-big mui-rmb"><?php  echo $row['condition'];?></div>
				<div class="mui-small">
					<?php  if($row['backtype'] == '0') { ?>
						送<?php  echo $row['back'];?>元
					<?php  } else { ?>
						送<?php  echo $row['back'];?>积分
					<?php  } ?>
				</div>
				<span class="selected-status"></span>
			</div>
		</div>
		<?php  } ?>
		<?php  } } ?>
		<?php  } ?>
		<div class="mui-col-xs-4 mui-pa5 mui-mt5 other-sum">
			<div class="mui-thumbnail mui-text-center mui-text-info js-inputpay">
				<input type="text" placeholder="输入金额" name="inputpay" style="display:none;text-align:center;">
				<span class="paytext">其他金额</span>
				<span class="selected-status"></span>
			</div>
		</div>
	</div>
	<div class="mui-section">
		支付金额 <span class="mui-text-success mui-big mui-pull-right add-pay mui-rmb">0元</span>
	</div>
	<div class="mui-content-padded">
		<input type="hidden" name="backtype">
		<input type="hidden" name="back">
		<input type="hidden" name="token" value="<?php  echo $_W['token'];?>">
		<input type="hidden" name="fee" />
		<button type="submit" name="submit" class="mui-btn mui-btn-success mui-btn-block dopay" value="确认充值">确认充值</button>
	</div>
	</form>
</div>
<script>
$(function(){
	$('.mui-thumbnail').click(function(){
		$('.add-pay').addClass('mui-rmb');
		$('.mui-thumbnail').removeClass('selected');
		$(this).addClass('selected');
		$('.paytext').show();
		$('input[name="inputpay"]').hide();
		var backtype = $(this).data('backtype');
		var back = $(this).data('back');
		var recharge = $(this).data('recharge');
		$('input[name="back"]').val(back);
		$('input[name="backtype"]').val(backtype);
		exists = $(this).hasClass('js-inputpay');
		if (exists) {
			$('.paytext').hide();
			$('input[name="inputpay"]').show();
			$('input[name="inputpay"]').focus();
			$('input[name="backtype"]').val('2');
			recharge = $('input[name="inputpay"]').val();
			if(!recharge) {
				recharge = 0;
			}
			if(isNaN(recharge)) {
				util.toast('请输入正确的金额', '', 'error');
				$('.dopay').attr('href', 'javascript:;');
				$('input[name="fee"]').val('0');
				$('.add-pay').text(0 + '元');
				return;
			}
		}
		$('input[name="fee"]').val(recharge);
		$('.add-pay').text(recharge + '元');
	})
	$('input[name="inputpay"]').bind('input propertchange', function() {
		$('.add-pay').addClass('mui-rmb');
		$('.mui-thumbnail').removeClass('selected');
		var recharge = $('input[name="inputpay"]').val();
		if(isNaN(recharge)) {
			util.toast('请输入正确的金额', '', 'error');
			$('.dopay').attr('href', 'javascript:;');
			return false;
		}
		$('input[name="fee"]').val(recharge);
		$('.add-pay').text(recharge + '元');
	})
	$(document).on('click', '.dopay', function() {
		var recharge = $('input[name="fee"]').val();
		recharge = parseFloat(recharge);
		if(isNaN(recharge)) {
			util.toast('请输入正确的金额', '', 'error');
			$('input[name="inputpay"]').val('');
			return false;
		}
		if(!recharge || recharge == '0') {
			$('input[name="inputpay"]').val('');
			util.toast('请输入或选择充值金额', '', 'error');
			return false;
		}
	})
})
</script>
