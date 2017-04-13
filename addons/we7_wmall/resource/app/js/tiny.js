var tiny = {};
tiny.querystring = function(name){
	var result = location.search.match(new RegExp("[\?\&]" + name+ "=([^\&]+)","i"));
	if (result == null || result.length < 1){
		return "";
	}
	return result[1];
}

tiny.image = function(obj, callback, options) {
	var defaultOptions = {
		fileNum: 1
	};
	var options = $.extend({}, defaultOptions, options);
	var $button = $(obj);
	wx.ready(function(){
		wx.chooseImage({
			count: options.fileNum,
			sizeType: ['compressed'],
			sourceType: ['album', 'camera'],
			success: function (res) {
				var localIds = res.localIds;
				if(localIds.length > 0) {
					for(var i = 0; i < localIds.length; i++) {
						$.showIndicator();
						wx.uploadImage({
							localId: localIds[i],
							isShowProgressTips: 0,
							success: function (res) {
								var serverId = res.serverId;
								var i = tiny.querystring('i');
								$.post("./index.php?i="+i+"&c=entry&do=cmnfile&op=image&m=we7_wmall", {media_id: serverId}, function(data){
									$.hideIndicator();
									var result = $.parseJSON(data);
									if(result.message.errno == 0) {
										if($.isFunction(callback)) {
											callback($button, result.message);
										}
									} else {
										alert('上传文件失败, 具体原因:' + result.message.message);
									}
								});
							},
							fail: function() {}
						});
					}
				}
			}
		});
	});
};
