 $(function(){
        //支付宝支付选中
        $('.zfbao').click(function(){
            if(!$(this).find('.bg').hasClass('bgp')){
                $(this).find('input').attr('checked',true);
                $(this).find('.bg').addClass('bgp');
                $('.wechat').find('input').attr('checked',false);
                $('.wechat').find('.bg').removeClass('bgp');
                $('#myform').attr('action','/index.php/pay/alipay');
            }else{
                $('#myform').attr('action','');
                $(this).find('input').attr('checked',false);
                $(this).find('.bg').removeClass('bgp');
            }
        })
        //微信支付选中
         $('.wechat').click(function(){
            $('#myform').attr('action','/index.php/wxpay/pay');
            if(!$(this).find('.bg').hasClass('bgp')){
                $(this).find('input').attr('checked',true);
                $(this).find('.bg').addClass('bgp');
                $('.zfbao').find('input').attr('checked',false);
                $('.zfbao').find('.bg').removeClass('bgp');
            }else{
                $('#myform').attr('action','');
                $(this).find('input').attr('checked',false);
                $(this).find('.bg').removeClass('bgp');
            }
        })
         //点击去支付，提交数据到对应页面
         $('.go').click(function(){
            if(!isMobile($('#mobile').val())){
                layerAlert('电话号码格式错误!');
                return false;
            }
            if($('#myform').attr('action')==''){
                layerAlert('请选择一个支付方式!');
                return false;
            }
            $('form').submit();
         });
    })