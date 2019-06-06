
	$(".caseshow_tit a").click(function(){
		$(this).addClass("hover").siblings().removeClass("hover");
		$.ajax({
            async: false,
            type: "get",
            dataType: "html",
            data: "pc_id=" + pc_id,
            url: "ajax/case_show.php?time=" + (new Date().getTime()),
            success: function (data) {
                $(".caseshow_box").html(data);
            }
        });
	})