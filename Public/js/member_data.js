$(function(){
        // $('.photo').click(function(){
        //     $('.bg_img').css('display','block');
        //     $('.img').attr({    placeholder:'点击上传文件'});
        //     $('.other').click(function(){if($('.bg_img').css('display') == 'block'){$('.bg_img').css('display','none')}})
        // })
        $('.nickname').click(function(){
            $('.bg_z').css('display','block');
            $('.c').attr({name:'nickname',value:'',placeholder:'请输入新的昵称'});
            $('.other').click(function(){if($('.bg_z').css('display') == 'block'){$('.bg_z').css('display','none')}})
        })
        $('.email').click(function(){
            $('.bg_z').css('display','block');
            $('.c').attr({name:'email',value:'',placeholder:'请输入新的邮箱'});
            $('.other').click(function(){if($('.bg_z').css('display') == 'block'){$('.bg_z').css('display','none')}})
        })
        $('.tel').click(function(){
            $('.bg_z').css('display','block');
            $('.c').attr({name:'mobile',value:'',placeholder:'请输入新的电话号码'});
            $('.other').click(function(){if($('.bg_z').css('display') == 'block'){$('.bg_z').css('display','none')}})
        })
        $('.sbt').click(function(){
            if($('.c').val() ==''){
                layerAlert('内容不能为空');
                return false;
            }
            if($('.c').prop('name') == 'email'){
                if(!isEmail($("input[name='email']").val())){
                    layerAlert("邮箱格式错误");
                    return false;
                }
            }
            if($('.c').prop('name') == 'mobile'){
                if(!isMobile($("input[name='mobile']").val())){
                    layerAlert("手机号码格式错误");
                    return false;
                }
            }
            $.ajax({
                url:'/index.php/member/data',
                type:'post',
                dataType:'json',
                data:$('form').serialize(),
                success:function(dat){
                    if(dat.status==1){
                        layer.open({
                            title:'温馨提示',
                            icon:1,
                            offset:'170px',
                            content:dat.info,
                            time:2000,
                        });
                        setTimeout(function(){
                            top.window.location.reload();
                        },2000);
                    }else{
                        layer.open({
                            title:'温馨提示',
                            icon:2,
                            offset:'170px',
                            content:dat.info,
                            time:2000,
                        });
                        setTimeout(function(){
                            top.window.location.reload();
                        },2000);
                    }
                }
            })
        })
    })