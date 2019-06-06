$(function(){
	 $('.getcode').click(function(){
            if($(this).hasClass('no_read')){
                layerAlert('请输入验证码！');
                return false;
            }
            $.ajax({
                type:'POST',
                url:'/index.php/Member/messageVer',
                data:'mobile='+$('#mobile').val()+'&code='+$('#verify').val(),
                dataType:'json',
                success:function(dat){
                    if(dat.status == 1){
                        layerAlert(dat.info);
                    }else{
                        layerAlert(dat.info);
                        setTimeout(function(){
                            window.location.reload();
                        },2000);
                    }
                }
            })
        })

      
        $('#verify').keyup(function(){
            if($(this).val().length == 4){
                $.ajax({
                    type:'POST',
                    url:'/index.php/Member/verifiction',
                    data:'code='+$('#verify').val(),
                    dataType:'json',
                    success:function(dat){
                        if(dat.status == 0){
                            layer.open({
                                title:'温馨提示',
                                icon:1,
                                offset:'170px',
                                content:dat.info,
                                time:2000,
                            });
                        }
                        if(dat.status == 1){
                            $('.getcode').removeClass('no_read');
                        }
                    }
                })
            }
        })
        //当输入11位电话号码的时候，提交ajax检测电话号码是否注册
         $('#mobile').keyup(function(){
            if($(this).val().length == 11){
             if(!isMobile($('#mobile').val())){
                layerAlert("手机号码格式错误");
                return false;
             }
                $.ajax({
                    type:'POST',
                    url:'/index.php/Member/verifiction',
                    data:'code='+$('#mobile').val(),
                    dataType:'json',
                    success:function(dat){
                        if(dat.status == 0){
                            layerAlert(dat.info);
                            setTimeout(function(){
                                top.window.location.reload();
                            },2000)
                        }
                    }
                })
            }
        })


})