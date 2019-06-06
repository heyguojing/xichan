 var noX = 0;  /* 没选中时点击加减计算数量  */
       var allThis = $(".commodity_box .select em"); /*底部全选*/
       var totalH;
        /* 减  */
        function reduceMod(e,totalH,mod,noX){
            var tn = e.siblings().find(".qu_su").text(); /* 当前选中商品  */
            var tn1 = e.siblings().find(".zi").text(); /* 商品数量  */
            if(mod != 2){
                var Total = parseFloat(totalH) - (tn*tn1);  /* 总价格减该商品总数价格  */
                $("#total_price b").text(Total.toFixed(2));
            }else{
                /* 合计加单价-1 */
                var Total = parseFloat(totalH) - parseFloat(tn);    /* 总价格减该商品总数价格  */
                $("#total_price b").text(Total.toFixed(2));
            }
            
        };
        /* 加  */
        function plusMod(e,totalH,mod){
            var tn = e.siblings().find(".qu_su").text(); /* 当前选中商品  */
            var tn1 = e.siblings().find(".zi").text(); /* 商品数量  */
            if(mod != 2){
                var Total = parseFloat(totalH)+(tn*tn1);    /* 总价格加上该商品总数价格  */
                $("#total_price b").text(Total.toFixed(2));
            }else{
                /* 合计加单价+1 */
                var Total = parseFloat(totalH)+(parseFloat(tn)+(noX-1));    /* 总价格加上该商品总数价格  */
                $("#total_price b").text(Total.toFixed(2));
            }
            
        };
        /*全选该店商品价格 加*/
        function commodityPlusMod(e,totalH){
            var qu = e.parents(".commodity_list").find(".pitch_on").parent().find(".qu_su");
            var quj = e.parents(".commodity_list").find(".pitch_on").parent().find(".zi");
            var Total = 0;
            var erTotal = true;
            /* 该商品全部金额  */
            for(var i=0; i<qu.length; i++)
                {
                    var n = qu.eq(i).text();
                    var n1 = quj.eq(i).text();
                    /*合计价格*/
                    if(erTotal){
                        Total = parseFloat(totalH) +(parseFloat(n)*parseFloat(n1));
                        if(Total < 0)
                            Total=0;
                        erTotal = false;
                    }else{
                        Total = parseFloat(Total) + (parseFloat(n)*parseFloat(n1));
                        
                    }
                }
            $("#total_price b").text(Total.toFixed(2)); /* 合计金额  */
        };
        var plus;
        /*全选该店商品价格 减*/
        function commodityReduceMod(e,totalH){
            var qu = e.parents(".commodity_list").find(".pitch_on").parent().find(".qu_su");
            var quj = e.parents(".commodity_list").find(".pitch_on").parent().find(".zi");
            var Total = 0;
            plus = totalH;
            
            var erTotal = true;
            /* 该商品全部金额  */
            for(var i=0; i<qu.length; i++)
                {
                    var n = qu.eq(i).text();
                    var n1 = quj.eq(i).text();
                    /*合计价格*/
                    if(erTotal){
                        Total = parseFloat(totalH) - (parseFloat(n)*parseFloat(n1));
                        plus = Total;
                        if(Total < 0)
                            Total=0;
                        erTotal = false;
                    }else{
                        Total = parseFloat(Total) - (parseFloat(n)*parseFloat(n1));
                        plus = Total;
                        
                    }
                    
                    $("#total_price b").text(Total.toFixed(2)); /* 合计金额  */
                    plus;
                }
        };
        /*全部商品价格*/
        function commodityWhole() {
            /* 合计金额  */
            var je = $(".commodity_box .select .qu_su"); /* 全部商品单价  */
            var je1 = $(".commodity_box .select .zi");  /* 全部商品数量  */
            var TotalJe = 0;
            for(var i=0; i<je.length; i++)
            {
                var n = je.eq(i).text();
                var n1 = je1.eq(i).text();
                TotalJe = TotalJe + (parseFloat(n)*parseFloat(n1));
                
            }
            $("#total_price b").text(TotalJe.toFixed(2)); /* 合计金额  */
        };

        //选择结算商品
        
        $(".select em").click(function(){
            var su = $(this).attr("aem");
            var carts_id=$(this).attr("cart_id");
            totalH = $("#total_price b").text(); /* 合计金额  */
            if(su == 0){
                /* 单选商品  */
                if($(this).hasClass("pitch_on")){
                    /*去该店全选*/
                    $(this).parents("ul").siblings(".select").find("em").removeClass("pitch_on");
                    /*去底部全选*/
                    $("#all_pitch_on").removeClass("pitch_on");
                    $(this).removeClass("pitch_on");
                    reduceMod($(this),totalH);
                    cart_id[carts_id]="";
                    delete cart_id[carts_id];
                }else{
                    $(this).addClass("pitch_on");
                    var n = $(this).parents("ul").children().find(".pitch_on");
                    var n1 = $(this).parents("ul").children();
                    plusMod($(this),totalH,0,noX);
                    cart_id[carts_id]="";
                    /*该店商品全选中时*/
                    if(n.length == n1.length){
                        $(this).parents("ul").siblings(".select").find("em").addClass("pitch_on");
                    }
                    /*商品全部选中时*/
                    var fot = $(".commodity_list_box .tite_tim .pitch_on");
                    var fot1 = $(".commodity_list_box .tite_tim em");
                    if(fot.length == fot1.length)
                    $("#all_pitch_on").addClass("pitch_on");
                }
            }else{
                /* 全选该店铺  */
                if($(this).hasClass("pitch_on")){
                    /*去底部全选*/
                    $("#all_pitch_on").removeClass("pitch_on");
                    $(this).removeClass("pitch_on");
                    
                    commodityReduceMod($(this),totalH);
                    $(this).parent().siblings("ul").find("em").removeClass("pitch_on");
                    delete cart_id[carts_id];
                }else{
                    commodityReduceMod($(this),totalH);

                    $(this).addClass("pitch_on");
                    
                    $(this).parent().siblings("ul").find("em").addClass("pitch_on");
                    
                    /*if(plus != NaN && plus != undefined && plus > 0){
                        totalH = parseFloat(totalH)-parseFloat(plus);
                        if(totalH < 0)
                            totalH = 0;
                    }*/
                    if(plus == undefined || plus == NaN)
                        {
                            plus=0
                        }

                    commodityPlusMod($(this),plus);
                    cart_id[carts_id]="";
                    /*商品全部选中时*/
                    var fot = $(".commodity_list_box .tite_tim .pitch_on");
                    var fot1 = $(".commodity_list_box .tite_tim em");
                    if(fot.length == fot1.length){
                        $("#all_pitch_on").addClass("pitch_on");
                    }
                    
                }
            }
            
            //计算选择数值
            //number();
            
        }); 
        /* 底部全选  */
        
        var bot = 0;
        $("#all_pitch_on").click(function(){
            if(bot == 0){
                $(this).addClass("pitch_on");
                allThis.removeClass("pitch_on");
                allThis.addClass("pitch_on");
                /*总价格*/
                commodityWhole();
                bot = 1;
                //重新加入属性对象
                for(var key in cart_id_copy){
                    cart_id[key]="";
                }
            }else{
                $(this).removeClass("pitch_on");
                allThis.removeClass("pitch_on");
                $("#total_price b").text("0");
                bot = 0;
                //移除全部对象
                for(var key in cart_id){
                    delete cart_id[key];
                }
            }
            //计算选择数值
            //number();
        });
        
        function number() {
            var num=0;
            for(var key in cart_id){
                    num++;
                }
            //将选择的放入到计算里面
            //$("#confirm_cart").html("结算("+num+")");
        }
        
        /* 编辑商品  */
        var topb = 0;
        $("#rem_s").click(function(){
            if(topb == 0){
                $(this).text("完成");
                $(".total_amount").hide(); /* 合计  */
                $("#confirm_cart").hide(); /* 结算  */
                $("#confirm_cart1").show(); /* 删除 */
                topb = 1;
            }else{
                topb = 0;
                $(this).text("编辑");
                $(".total_amount").show(); /* 合计  */
                $("#confirm_cart").show(); /* 结算  */
                $("#confirm_cart1").hide(); /* 删除 */
                allThis.removeClass("pitch_on"); /* 取消所有选择  */
                $("#all_pitch_on").removeClass("pitch_on"); /* 取消所有选择  */
                $("#total_price b").text("0"); /*合计价格清零*/

            }
            
        });
        /* 加减  */
        
        function reducew(obj){
            //减
            var $this = $(obj);
            var totalH = $("#total_price b").text(); /* 合计金额  */
            var ise = $this.siblings("span").text();
            var gc_id = $this.siblings("input").val();
            if(noX <= 0){
                noX = 0;
            }else{
                noX--;
            };
            
            if(parseInt(ise) <= 1){
                $this.siblings("span").text("1");
            }else{
                var n =parseInt(ise)-1;
                $this.siblings("span").text(n);
                $this.siblings('.num').val($this.siblings('.gid').val()+'_'+n);
                if($this.parent().parent().children("em").hasClass("pitch_on")){
                    var mo = $this.parent().parent().children("em");
                    reduceMod(mo,totalH,2,noX);
                    noX=0;
                }
                
            }
            
            //goods_count_adjust(gc_id,n,null);
        };
        
        function plusw(obj){
            //加
            var $this = $(obj);
            var totalH = $("#total_price b").text(); /* 合计金额  */
            var ise = $this.siblings("span").text();
            var gc_id = $this.siblings("input").val();
            var n =parseInt(ise)+1;
            noX++;
            
            $this.siblings("span").text(n);
            $this.siblings('.num').val($this.siblings('.gid').val()+'_'+n);
            if($this.parent().parent().children("em").hasClass("pitch_on")){
                var mo = $this.parent().parent().children("em");
                plusMod(mo,totalH,2,noX);
                noX=0;
            }
            
            //goods_count_adjust(gc_id,n,null);
        }
        
        
         //删除 
        function big_cart_remove(){
            $(".commodity_list_term .pitch_on").parent().remove();
            $(".commodity_list .tite_tim > em.pitch_on").parents(".commodity_box").remove();    
        } 