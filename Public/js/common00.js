<!--移动端版本兼容 -->
$(function () {
    UpdateWidth();
    /*$(window).resize(function () {
        UpdateWidth();
    })*/
})
function orient() {
	if (window.orientation == 0 || window.orientation == 180) {//竖屏
		return 'portrait';
	}
	else if (window.orientation == 90 || window.orientation == -90) {//这是匹配横屏的状态
		return 'landscape';
	}
}


$(function(){
	orient();
});


$(window).bind( 'orientationchange', function(e){
	UpdateWidth();
});
function UpdateWidth(){
	var x = navigator;
var w=window.innerWidth|| document.documentElement.clientWidth|| document.body.clientWidth;
var h=window.innerHeight|| document.documentElement.clientHeight|| document.body.clientHeight;
alert("<b>屏幕分辨率:</b>"+screen.width + "*" + screen.height+"<b>内部窗口</b>:" + w + "*" + h);
	
	
	
	var phoneWidth=parseInt(window.screen.width);//分辨率宽度
	if(orient()=='landscape')
	{
		phoneWidth=parseInt(screen.height);//分辨率高度
		listener('remove');
		//phoneWidth =  parseInt(document.body.offsetWidth);// parseInt($(window).width());
	}
	var phoneScale = phoneWidth/1280;
	var ua = navigator.userAgent;
	if($("meta[name=viewport]").length>0){
		alert("这个是屏幕的宽度"+phoneWidth);
		if (/Android (\d+\.\d+)/.test(ua)){
			var version = parseFloat(RegExp.$1);
			if(version>2.3){
				$("meta[name=viewport]").attr('content','width=1280, initial-scale='+phoneScale+', minimum-scale = '+phoneScale+', maximum-scale = '+phoneScale+', target-densitydpi=device-dpi');
			}else{
				$("meta[name=viewport]").attr('content','width=1280, target-densitydpi=device-dpi');
			}
		} else {
			$("meta[name=viewport]").attr('content','width=1280, user-scalable=no, target-densitydpi=device-dpi');
		}
		alert($("meta[name=viewport]").attr('content'));
	}
	else
	{
		if (/Android (\d+\.\d+)/.test(ua)){
		var version = parseFloat(RegExp.$1);
		if(version>2.3){
			$("#BodyHead").append('<meta name="viewport" content="width=1280, initial-scale='+phoneScale+', minimum-scale = '+phoneScale+', maximum-scale = '+phoneScale+', target-densitydpi=device-dpi">');
		}else{
			$("#BodyHead").append('<meta name="viewport" content="width=1280, target-densitydpi=device-dpi">');
		}
		} else {
			$("#BodyHead").append('<meta name="viewport" content="width=1280, user-scalable=no, target-densitydpi=device-dpi">');
		}
	}
}