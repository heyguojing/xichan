$(function () {
	$(".mTopBar .back").remove();
	$(".swiper-slide").each(function () {
		var imgUrl=$(this).find("img").attr("src");
		var boady_width= parseFloat($(window).width());
		// 使用占位方式快速获取大小
		imgReady(imgUrl, function (width, height) {
			var img_height=(height/width)*boady_width;
			$(".bannerbox").css({"height":img_height+"px"});
			$(".swiper-container").css({"height":img_height+"px"});
			//statusReady.innerHTML = 'width = ' + width + '; height = ' + height;
		}, function () {
			//statusReady.innerHTML = 'Img Error!';
		});
	});
	
	var mySwiper = new Swiper('.swiper-container',{
		pagination: '.pagination',
	    paginationClickable: true,
	    moveStartThreshold: 5,
	    autoplay:4000//自动切换时间间隔
	})
    $(window).resize(function () {
        UpdateBanner();
    })
})

function UpdateBanner(){
	var boady_width= parseFloat($(window).width());
	var boady_height=parseFloat($(window).height());
	var img_height = 0;	
	if($(".swiper-slide").length>0){
		$(".swiper-slide").each(function () {
		    /*var dp = $(this).css("display");
		    if (dp != "none") {
		    img_height = parseFloat($(this).find("img").height()); 
		    }*/
		    if ($(this).hasClass("swiper-slide-active")) {
		        img_height = parseFloat($(this).find("img").height()); 
            }
		});
		if (img_height == 0) {
		    img_height=300;
        }
		$(".bannerbox").css({"height":img_height+"px"});
		$(".swiper-container").css({"height":img_height+"px"});
		//var span_w=$(".swiper-pagination-switch").height();
		//$(".swiper-pagination-switch").css({"width":span_w+"px"});
}
}