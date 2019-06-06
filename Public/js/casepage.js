var cw= (parseFloat($(".infinite_scroll").width())*48.9)/100;
var gw=parseFloat($(".infinite_scroll").width())-cw*2;
function item_masonry(){ 
	$('.masonry_brick img').load(function(){ 
		$('.infinite_scroll').masonry({ 
			itemSelector: '.masonry_brick',
			columnWidth:cw,
			gutterWidth:gw								
		});		
	});
		
	$('.infinite_scroll').masonry({ 
		itemSelector: '.masonry_brick',
		columnWidth:cw,
		gutterWidth:gw								
	});	
}


$(function(){
	$(".masonry_brick").css({"width":cw+"px"});
	function item_callback(){
		item_masonry();	
	}
	item_callback();
	$('.masonry_brick').fadeIn();
	var sp = 1;
	var pcount=parseInt($("#infinite_count").val());//总分页数
	var pcount=parseInt($("#infinite_count").val());//总分页数
	$(".infinite_scroll").infinitescroll({
		navSelector  	: "#more",
		nextSelector 	: "#more a",
		itemSelector 	: ".masonry_brick",
		loading:{
			img: "images/load.png",
			msgText: ' ',
			//finishedMsg: '木有了',
			finishedMsg: '',
			finished: function(){
				sp++;
				if(sp>=pcount){ //到第10页结束事件				
					$("#more").remove();
					$("#infscr-loading").hide();
					$("#page").show();
					$(window).unbind('.infscr');
				}
			}	
		},errorCallback:function(){ 
			$("#page").show();
		}
		
	},function(newElements){
		var $newElems = $(newElements);
		$('.infinite_scroll').masonry('appended', $newElems, false);
		$(".masonry_brick").css({"width":cw+"px"});
		$newElems.fadeIn();
		item_callback();
		return;
	});
        UpdateMasonry();
	$(window).resize(function () {
        UpdateMasonry();
    })
});
function UpdateMasonry(){
	cw= (parseFloat($(".infinite_scroll").width())*48.9)/100;
	gw=parseFloat($(".infinite_scroll").width())-cw*2;
	$(".masonry_brick").css({"width":cw+"px"});
	item_masonry();
}

$("#CaseSelect").click(function(){
		var mh=parseFloat($(".mTopBar").height())+parseFloat($(".mtopBar_line").height());
		$(".caseclass").css({"top":mh+"px"});
		var op=$(this).attr("op");
		if(op=="close")
		{
			$(".caseclass").hide();	
			$(this).attr("op","open");
		}
		else
		{
			$(".caseclass").show();
			$(this).attr("op","close");
		}
	})