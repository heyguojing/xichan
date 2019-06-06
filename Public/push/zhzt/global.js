$(function(){
	//二级菜单下如果没有内容，则隐藏此二级菜单
	var men = $('.menu_list');
	for(var i=0 ; i < men.length ; i++){
		if($.trim(men.eq(i).find('.menu3').text()).length === 0){
			men.eq(i).children('li').hide();
		}
	}
	// 三级菜单伸展
	$('.menu_list>li').click(function(){
		$(this).find('span').toggleClass('glyphicon glyphicon-menu-up');
		$(this).siblings('.menu3').slideToggle('slow');
		// $(this).parent().siblings('.menu_list').find('.menu3').css('display','none');
		$(this).parent().siblings('.menu_list').find('.menu3').slideUp().siblings('li').find('span').removeClass('glyphicon-menu-up').addClass('glyphicon-menu-down');
	})

	//二级菜单切换
	$('.menu1 li').eq(0).addClass('current').children('span').addClass('spancurrent');
	$('.menu1 li').click(function(){
		var int = $(this).index();
		$(this).addClass('current').children('span').addClass('spancurrent').parent().siblings('li').removeClass('current');
		$('.menu2').eq(int).slideDown('slow').siblings('.menu2').hide();
	})
	$('.menu2').eq(0).show();

	//nav_icon切换及一级菜单toggle
	$('.nav_icon').click(function(){
		if($(this).children().attr('src') == "images/nav_ico.png"){
			$(this).children().attr('src','images/x.png');
			var menu2 = $(window).height() - 80;
			$('.menu2').height(menu2);
			$('.menu').animate({'left':'0'},"4000");
		}else{
			$(this).children().attr('src','images/nav_ico.png');
			$('.menu').animate({'left':'-200%'},"4000");
		}
	})
	
	//统计代码消除
	$('.wrapper').siblings('a').hide();
	$('.footer').siblings('a').hide();

	//footer wechat
	$('.footer_wechat a').click(function(){
		$(this).siblings('.weixin').toggle();
	})


	//返回顶部js
	$(function () {
		if($(document).height() > 800){
	        $(window).scroll(function(){
	            if ($(window).scrollTop()>200){
	                $(".backTop").fadeIn(1500);
	            }
	            else
	            {
	                $(".backTop").fadeOut(1500);
	            }
	        });
		}

        //当点击跳转链接后，回到页面顶部位置
        $(".backTop").click(function(){
            if ($('html').scrollTop()) {
                $('html').animate({ scrollTop: 0 }, 500);//动画效果
                return false;
            }
            $('body').animate({ scrollTop: 0 }, 500);
            return false;
        });
    });
})

function isHaveSpecialString(value){
   var pattern = new RegExp("[~'!@#$%^&*()-+_=:<>\/;:\'\"\.]"); 
    return pattern.test(value);
}