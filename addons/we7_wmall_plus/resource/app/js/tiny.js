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
								$.post("./index.php?i="+i+"&c=entry&do=cmnfile&op=image&m=we7_wmall_plus", {media_id: serverId}, function(data){
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

tiny.cookie = {
	'prefix' : we7_wmall_plus.prefix,
	// 保存 Cookie
	'set' : function(name, value, seconds) {
		expires = new Date();
		expires.setTime(expires.getTime() + (1000 * seconds));
		document.cookie = this.name(name) + "=" + escape(value) + "; expires=" + expires.toGMTString() + "; path=/";
	},
	// 获取 Cookie
	'get' : function(name) {
		cookie_name = this.name(name) + "=";
		cookie_length = document.cookie.length;
		cookie_begin = 0;
		while (cookie_begin < cookie_length)
		{
			value_begin = cookie_begin + cookie_name.length;
			if (document.cookie.substring(cookie_begin, value_begin) == cookie_name)
			{
				var value_end = document.cookie.indexOf ( ";", value_begin);
				if (value_end == -1)
				{
					value_end = cookie_length;
				}
				return unescape(document.cookie.substring(value_begin, value_end));
			}
			cookie_begin = document.cookie.indexOf ( " ", cookie_begin) + 1;
			if (cookie_begin == 0)
			{
				break;
			}
		}
		return null;
	},
	// 清除 Cookie
	'del' : function(name) {
		var expireNow = new Date();
		document.cookie = this.name(name) + "=" + "; expires=Thu, 01-Jan-70 00:00:01 GMT" + "; path=/";
	},
	'name' : function(name) {
		return this.prefix + name;
	}
};//end cookie

