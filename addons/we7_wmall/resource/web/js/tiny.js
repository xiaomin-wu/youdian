var tiny = {};

tiny.linkBrowser = function(callback){
	var footer = '<button type="button" class="btn btn-default" data-dismiss="modal">取消</button>';
	var modalobj = util.dialog('请选择链接',["./index.php?c=site&a=entry&&do=cmnlink&m=we7_wmall&callback=selectTinyLinkComplete"],footer,{containerName:'link-container'});
	modalobj.modal({'keyboard': false});
	modalobj.find('.modal-body').css({'height':'300px','overflow-y':'auto' });
	modalobj.modal('show');

	window.selectTinyLinkComplete = function(link){
		if($.isFunction(callback)){
			callback(link);
			modalobj.modal('hide');
		}
	};
};

tiny.selectfan = function(callback) {
	require(['bootstrap'], function($){
		$('#select-fans-modal').remove();
		$(document.body).append($('#select-fans-containter').html());
		var $Modal = $('#select-fans-modal');
		$Modal.modal('show');
		$Modal.find('#keyword').on('keydown', function(e){
			if(e.keyCode == 13) {
				$Modal.find('#search').trigger('click');
				e.preventDefault();
				return;
			}
		});

		$Modal.find('#search').on('click', function(){
			var key = $.trim($Modal.find('#keyword').val());
			if(!key) {
				return false;
			}
			$.post("./index.php?c=site&a=entry&&do=cmnfans&m=we7_wmall&op=list", {key: key}, function(data){
				var result = $.parseJSON(data);
				console.dir(result);
				if(result.message.message && result.message.message.length > 0) {
					$Modal.find('.content').data('attachment', result.message.message);
					var gettpl = $('#select-fans-data').html();
					laytpl(gettpl).render(result.message.message, function(html){
						$Modal.find('.content').html(html);
						$Modal.find('.content .btn-primary').off();
						$Modal.find('.content .btn-primary').on('click', function(){
							var fanid = $(this).data('fanid');
							var fan = result.message.data[fanid];
							if($.isFunction(callback)){
								callback(fan);
							}
							$Modal.modal('hide');
						});
					});
				} else {
					$Modal.find('.content #info').html('没有符合条件的粉丝');
				}
			});
		});
	});
}

tiny.confirm = function(obj, option, callback_confirm, callback_cancel) {
	if(typeof option == 'string'){
		option = {tips : option};
	}
	option = $.extend({tips:'确认删除?', placement:'left'}, option);
	obj.popover({
		'html':true,
		'placement': option.placement,
		'trigger': 'manual',
		'title': '',
		'content':'<span> '+ option.tips +' </span> <a class="btn btn-primary confirm">确定</a> <a class="btn btn-default cancel">取消</a>'
	});
	obj.popover('show');
	var confirm = obj.next().find('a.confirm');
	var cancel = obj.next().find('a.cancel');
	cancel.off('click').on('click', function(){
		obj.popover('hide');
		obj.next().remove();
		if(typeof callback_cancel == 'function') {
			callback_cancel();
		}
	});
	confirm.off('click').on('click', function(){
		obj.popover('hide');
		obj.next().remove();
		if(typeof callback_confirm == 'function') {
			callback_confirm();
		}
	});
	return false;
}

tiny.map = function(val, callback){
	$.getScript('http://webapi.amap.com/maps?v=1.3&key=550a3bf0cb6d96c3b43d330fb7d86950&plugin=AMap.Geocoder,AMap.Scale,AMap.OverView,AMap.ToolBar', function(){
		if(!val) {
			val = {};
		}
		if(!val.lng) {
			val.lng = 116.397428;
		}
		if(!val.lat) {
			val.lat = 39.90923;
		}
		var geo = new AMap.Geocoder();

		var modalobj = $('#map-dialog');
		if(modalobj.length == 0) {
			var content =
				'<div class="form-group">' +
					'<div class="input-group">' +
					'<input type="text" class="form-control" placeholder="请输入地址来直接查找相关位置">' +
					'<div class="input-group-btn">' +
					'<button class="btn btn-default"><i class="icon-search"></i> 搜索</button>' +
					'</div>' +
					'</div>' +
					'</div>' +
					'<div id="map-container" style="height:400px;"></div>';
			var footer =
				'<button type="button" class="btn btn-default" data-dismiss="modal">取消</button>' +
					'<button type="button" class="btn btn-primary">确认</button>';
			modalobj = util.dialog('请选择地点', content, footer, {containerName : 'map-dialog'});
			modalobj.find('.modal-dialog').css('width', '80%');
			modalobj.modal({'keyboard': false});

			map = tiny.map.instance = new AMap.Map('map-container');
			map.setZoomAndCenter(12, [val.lng, val.lat]);

			map.addControl(new AMap.Scale());
			map.addControl(new AMap.ToolBar());

			marker = tiny.map.marker = new AMap.Marker({
				position: [val.lng, val.lat],
				draggable: true
			});
			marker.setLabel({
				offset: new AMap.Pixel(-80, -25),
				content: "请您移动此标记，选择您的坐标！"
			});
			marker.setMap(map);
			AMap.event.addListener(marker, "dragend", function(e){
				var point = marker.getPosition();
				geo.getAddress([point.lng, point.lat], function(status, result) {
					if (status === 'complete' && result.info === 'OK') {
						modalobj.find('.input-group :text').val(result.regeocode.formattedAddress);
					}
				});
			});

			function searchAddress(address) {
				geo.getLocation(address, function(status, result) {
					if (status === 'complete' && result.info === 'OK') {
						var geocode = result.geocodes[0];
						if(geocode.location) {
							map.panTo([geocode.location.lng, geocode.location.lat]);
							marker.setPosition([geocode.location.lng, geocode.location.lat]);
							marker.setAnimation('AMAP_ANIMATION_BOUNCE');
							setTimeout(function(){marker.setAnimation(null)}, 3600);
						}
					}
				});
			}
			modalobj.find('.input-group :text').keydown(function(e){
				if(e.keyCode == 13) {
					var kw = $(this).val();
					searchAddress(kw);
				}
			});
			modalobj.find('.input-group button').click(function(){
				var kw = $(this).parent().prev().val();
				searchAddress(kw);
			});
		}
		modalobj.off('shown.bs.modal');
		modalobj.on('shown.bs.modal', function(){
			marker.setPosition([val.lng, val.lat]);
			map.panTo([val.lng, val.lat]);
		});

		modalobj.find('button.btn-primary').off('click');
		modalobj.find('button.btn-primary').on('click', function(){
			if($.isFunction(callback)) {
				var point = marker.getPosition();
				geo.getAddress([point.lng, point.lat], function(status, result) {
					if (status === 'complete' && result.info === 'OK') {
						var val = {lng: point.lng, lat: point.lat, label: result.regeocode.formattedAddress};
						callback(val);
					}
				});
			}
			modalobj.modal('hide');
		});
		modalobj.modal('show');
	});
};

tiny.prompt = function(obj, option, callback_confirm, callback_cancel) {
	if(typeof option == 'string'){
		option = {tips : option};
	}
	option = $.extend({title: '', placement:'top'}, option);
	obj.popover({
		'html':true,
		'placement': option.placement,
		'trigger': 'manual',
		'title': option.title,
		'content':'<input type="text" class="form-control prompt-input-text" value=""> <a class="btn btn-primary confirm">确定</a> <a class="btn btn-default cancel">取消</a>'
	});
	obj.popover('show');
	var confirm = obj.next().find('a.confirm');
	var cancel = obj.next().find('a.cancel');
	var input = obj.next().find('.prompt-input-text');
	input.focus();
	$(input).keydown(function(event){
		if(event.keyCode == 13){
			$(confirm).trigger('click');
			return false;
		}
	});
	cancel.off('click').on('click', function(){
		var value = obj.next().find('.prompt-input-text').val();
		obj.popover('hide');
		obj.next().remove();
		if(typeof callback_cancel == 'function') {
			callback_cancel(value);
		}
	});
	confirm.off('click').on('click', function(){
		var value = obj.next().find('.prompt-input-text').val();
		obj.popover('hide');
		obj.next().remove();
		if(typeof callback_confirm == 'function') {
			callback_confirm(value);
		}
	});
	return false;
}









