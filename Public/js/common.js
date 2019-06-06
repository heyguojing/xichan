$(function () {
	
	$(".header").addClass("hfixed");
	SetHeader();
	$('a').bind('focus', function () { if (this.blur) { this.blur() }; }); // 去除a标签点击时的外部虚线
	goToTop();
    $(window).scroll(function () {
		SetHeader();
		// if(window.location.href=='http://m.xichan.cn/index.php/index.html' || window.location.href=='http://m.xichan.cn/'){
		// 	$("#overLayBody").css({"top":$(this).scrollTop()+25+"px"});	
		// }else{
		// 	$("#overLayBody").css({"top":$(this).scrollTop()+"px"});	
		// }
	});
	if($(".fixedbottom").length>0)
	{
		$(".fixedfloat>a.fus").hide();	
		$(".fixedfloat>a.ftel").hide();
	}
	$("#sea_key").focus(function() {
		$(".searchtxt").addClass("bg_write");
	})
	$("#sea_key").blur(function() {
		$(".searchtxt").removeClass("bg_write");
	})
	 $("#sea_btn").click(function () {
        var keys = $("#sea_key").val()
        if (keys == "搜索" || keys == "") {
            //alert("查询关键词不能为空");
            //$("#sea_key").focus();
			$("#sea_key").val("搜索");
            return;
        }
        //keys = escape(keys);
        //keys = unescape(keys);//解码
        window.location.href = "/index.php/Search.html?key=" + keys;
    })
	$(".medilase_panelBtn").click(function (event){
		event.stopPropagation();  //取消事件冒泡  
		var st=$(this).attr("state");
		if(st=="open")
		{
			$("#overLayBody").show();	
			$(this).attr("state","close");
		}
		else
		{
			$("#overLayBody").hide();	
			$(this).attr("state","open");	
		}
	})
	$("#btnshare").click(function(event){
		event.stopPropagation();  //取消事件冒泡
		$(".sharediv").show();
	})
	$("#fixeopenmore").click(function(event){
		event.stopPropagation();  //取消事件冒泡
		$(".divmoreitem").show();
	})

	$(document).click(function (event) { 
		//$('#overLayBody').hide();
		$(".sharediv").hide();
		$(".divmoreitem").slideUp('slow');
	});  
})


function goToTop() { //返回顶部
    $('#backTop').click(function () {
        $('html,body').animate({ scrollTop: 0 }, 'slow'); //慢慢回到页面顶部
        return false;
    });
    $(window).scroll(function () {
		if($("#backTop").length>0)
		{
			if ($(this).scrollTop() < 500) {//当window的垂直滚动条距顶部距离小于300时
				$('#backTop').fadeOut('slow'); //goToTop按钮淡出
			} else {
				$('#backTop').fadeIn('slow'); //反之按钮淡入
			}
		}
		
    });
}

function SetHeader(){
	var hh=$(".header").height();
	$("body").css({"padding-top":hh+"px"});	
}
