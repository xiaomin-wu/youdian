<?php defined('IN_IA') or exit('Access Denied');?><div class="modal fade" id="select-deliveryer-containter" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog ">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">选择配送员</h4>
			</div>
			<div class="modal-body">
				<table class="table table-hover table-bordered">
					<thead>
					<tr>
						<th>姓名</th>
						<th>手机号</th>
						<th style="text-align: right">操作</th>
					</tr>
					</thead>
					<tbody class="content"></tbody>
				</table>
			</div>
			<div class="modal-footer text-center">
				<div class="modal-footer text-center">
					<a class="btn btn-default" data-dismiss="modal">取消</a>
					<a class="btn btn-primary js-select-deliveryer-submit">确定</a>
				</div>
			</div>
		</div>
	</div>
</div>
<script id="tpl-select-deliveryer" type="text/html">
	<{# for(var i = 0, len = d.length; i < len; i++){ }>
	<tr>
		<td><{d[i].deliveryer.title}></td>
		<td><{d[i].deliveryer.mobile}></td>
		<td style="text-align: right">
			<a href="javascript:;" data-id="<{d[i].deliveryer_id}>" class="btn btn-default js-select-deliveryer">选择</a>
		</td>
	</tr>
	<{# } }>
</script>
<script>
$(function(){
	$(document).on('click', '.select-deliveryer', function(){
		var $this = $(this);
		var order_ids = [];
		if($this.data('type') == 'single') {
			order_ids.push($this.data('id'));
		} else {
			var $select_ids = $('#form-order :checkbox[name="id[]"]:checked');
			if($select_ids.length == 0) {
				util.message('请选择要操作的订单', '', 'error');
				return false;
			}
			$.each($select_ids, function(){
				order_ids.push($(this).val());
			});
		}
		var $deliveryer_containter = $('#select-deliveryer-containter');
		$.get("<?php  echo $this->createWebUrl('utility', array('op' => 'deliveryer', 'sid' => $sid));?>", function(data){
			var result = $.parseJSON(data);
			if(result.message.errno != 0) {
				util.message('获取配送员信息失败', '', 'error');
				return false;
			} else {
				if(result.message.message.length == 0) {
					var url = "<?php  echo $this->createWebUrl('deliveryer');?>";
					util.message('还没有添加配送员, <a href="'+ url +'" target="_blank" class="text-danger">现在去添加?</a>');
					return false;
				}
				var gettpl = $('#tpl-select-deliveryer').html();
				laytpl(gettpl).render(result.message.message, function(html){
					$deliveryer_containter.find('.content').html(html);
					$deliveryer_containter.modal('show');
				});
			}
		});
		$(document).on('click', '#select-deliveryer-containter .js-select-deliveryer', function(){
			var $this = $(this);
			$('#select-deliveryer-containter .js-select-deliveryer').removeClass('btn-primary');
			$this.addClass('btn-primary');
		});
		$('.js-select-deliveryer-submit').unbind().click(function(){
			var deliveryer_id = $('.js-select-deliveryer.btn-primary').data('id');
			if(!deliveryer_id) {
				util.message('请选择配送员', '', 'error');
				return false;
			}
			$(this).html('处理中...').attr('disabled', true);
			$.post("<?php  echo $this->createWebUrl('order', array('op' => 'set_deliveryer'));?>", {deliveryer_id: deliveryer_id, order_ids: order_ids}, function(data){
				var result = $.parseJSON(data);
				if(result.message.errno != 0) {
					$(this).html('确定').attr('disabled', false);
					util.message(result.message.message, '', 'error');
					return false;
				} else {
					location.reload();
				}
			});
		});
	});
});

</script>