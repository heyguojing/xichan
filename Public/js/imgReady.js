/*!
 * img ready v0.3
 * http://www.planeart.cn/?p=1121
 * TangBin - MIT Licensed
 */

// 图片头数据加载就绪事件
// @param	{String}	图片路径
// @param	{Function}	获取尺寸的回调函数 (参数1接收width；参数2接收height)
// @param	{Function}	加载错误的回调函数 (可选)
(function () {
	var list = [], intervalId = null,
	
	tick = function () {
		var i = 0;
		for (; i < list.length; i++) {
			list[i].end ? list.splice(i--, 1) : list[i]();
		};
		!list.length && stop();
	},
	
	stop = function () {
		clearInterval(intervalId);
		intervalId = null;
	};
	
	this.imgReady = function (url, callback, error) {
		var check, end, width, height, offsetWidth, offsetHeight, div,
			accuracy = 1024,
			doc = document,
			container = doc.body || doc.getElementsByTagName('head')[0],
			img = new Image();
				
		img.src = url;
		if (!callback) return img;
		
		// 如果图片被缓存，则直接返回缓存数据
		if (img.complete) return callback(img.width, img.height);
		
		// 向页面插入隐秘图像，用来监听图片是否占位
		div = doc.createElement('div');
		div.style.cssText = 'visibility:hidden;position:absolute;left:0;top:0;width:1px;height:1px;overflow:hidden';
		div.appendChild(img)
		container.appendChild(div);
		width = img.offsetWidth;
		height = img.offsetHeight;
		
		// 完全加载完毕的事件
		img.onload = function () {
			end();
			callback(img.width, img.height);
		};
		
		// 加载错误后的事件
		img.onerror = function () {
			end();
			error && error();
		};
		
		// 检测图片是否已经占位
		check = function () {
			offsetWidth = img.offsetWidth;
			offsetHeight = img.offsetHeight;
			if (offsetWidth !== width || offsetHeight !== height || offsetWidth * offsetHeight > accuracy) {
				end();
				callback(offsetWidth, offsetHeight);
			};
		};
		check.url = url;
		
		// 操作结束后进行清理
		// 删除元素与事件，避免IE内存泄漏
		end = function () {
			check.end = true;
			img.onload = img.onerror = null;
			div.innerHTML = '';
			div.parentNode.removeChild(div);
		};
		
		// 将检测图片是否占位的函数加入定时器列队定期执行
		// 同一图片只加入一个检测器
		// 无论何时只允许出现一个定时器，减少浏览器性能损耗
		!check.end && check();
		for (var i = 0; i < list.length; i ++) {
			if (list[i].url === url) return;
		};
		if (!check.end) {
			list.push(check);
			if (!intervalId) intervalId = setInterval(tick, 150);
		};
	};
})();