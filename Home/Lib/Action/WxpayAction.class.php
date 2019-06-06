<?php
class WxpayAction extends Action{
	public function _initialize(){
		  vendor('WxPay.WechatH5Pay');
	}
//微信支付异步回调地址
public function notifyurl(){
	$wxpay = M ( 'wxpay' )->where ( array ('pay_type' => 'wxpay' ) )->find ();
	$appid = $wxpay['appid'];
	$mch_id = $wxpay['partnerid'];//商户号
	$key = $wxpay['signkey'];//商户key
	$notify_url = C('WEB_ROOT')."index.php/Wxpay/notifyurl";//回调地址
	$wechatAppPay = new wechatAppPay($appid, $mch_id, $notify_url, $key);
	$resData = $wechatAppPay->getNotifyData();//获取支付结果
	$order = M('order');
	$pay   = M('Pay');
	//接收到的数组转为字符串，用于出错后存入日志中查看
	$data_str = var_export($resData,true);

	if($resData['result_code'] == 'SUCCESS'){


		/***************修改订单表记录信息*****************/
		$order_num = $order->where('order_num ="'.$resData['out_trade_no'].'"')->find();
		if($order_num){//查询订单是否存在
			//查询订单交易金额是否和订单的金额相符
			if($resData['cash_fee'] == $order_num['price']*100){
				//组合修改数据库订单的信息
				$arr['order_num']     = $order_num['order_num'];
				$arr['status']   	  =  3;
				$arr['finished_time'] =  $resData['time_end'];
				$arr['id']			  = $order_num['id'];
				if($order_num['status'] != 3){
					if(!$order->save($arr)){
						$this->logResult('order:订单为'.$resData["out_trade_no"].'的数据存入失败'.$data_str.'\r\n');
					}
				}
			}else{
				$this->logResult('order:订单为'.$resData["out_trade_no"].'的信息的交易金额不正确'.$data_str.'\r\n');
			}
		}else{
			$this->logResult('order:没有此订单信息'.$data_str.'\r\n');
		}


		/***************修改支付表的订单信息*****************/
		$pay_info = $pay->where('code ="'.$resData['out_trade_no'].'"')->find();
		if($pay_info){
			if($resData['cash_fee'] == $pay_info['rmb']*100){
				$pay_arr['id'] 			=  $pay_info['id'];
				$pay_arr['code']		=  $resData['out_trade_no'];
				$pay_arr['yes']			= 1;
				$pay_arr['type']		= '微信';
				$pay_arr['trade_no']    = $resData['transaction_id'];  
				if($pay_info['yes'] !=1){
					if($pay->save($pay_arr)){

						/***************短信发送通知*****************/
						$notice = M('message_notice');
						//短信发送内容拼装
						//短信接口用户名 $uid
						$uid = 'LKSDK00085';
						//短信接口密码 $passwd
						$passwd = '506122';
						//根据订单表的uid查询出用户的电话号码
						$mobile = M('member')->field('`uid`,`mobile`')->where("uid=".$order_num['uid'])->find();
						//发送到的目标手机号码 $telphone
						$telphone = $mobile['mobile'];
						//查询出之前是否获取过验证码且未过期，有则发送给用户，没有则重新获取
						
						//短信内容 $message
						$message = '您已成功购买'.$order_num['title'].'美容服务及产品，您的订单号是:'.$resData["out_trade_no"].'，欢迎到店使用!如有疑问请致电4000286666!';
						$message_tow=rawurlencode(mb_convert_encoding($message, "gb2312", "utf-8"));
						//验证码请求时间	
						$data['ctime'] = time();
						//验证码内容
						$data['content'] = $message;
						//验证码用户id
						$data['uid'] = $order_num['uid'];
						//用户的手机号码
						$data['orderid'] = $order_num['id'];
						$data['mobile'] = $telphone;
						$gateway = "http://mb345.com/ws/batchSend.aspx?CorpID=".$uid."&Pwd=".$passwd."&Mobile=".$telphone."&Content=".$message_tow."&Cell=&SendTime=";
						//发送短信	
						$result = file_get_contents($gateway);
						$res = $notice->add($data);//将短信内容存入数据库
						$this->logResult('订单为'.$resData["out_trade_no"].'支付成功\r\n');
					}else{
						$this->logResult('pay:订单为'.$resData["out_trade_no"].'的数据存入失败'.$data_str.'\r\n');
					}
				}
			}else{
				$this->logResult('pay：订单为'.$resData["out_trade_no"].'的信息的交易金额不正确'.$data_str.'\r\n');
			}
		}else{
			$this->logResult('pay：没有此订单信息'.$data_str.'\r\n');
		}

		echo 'success';
	}else{
		//将支付结果写入日志
		$this->logResult('支付失败，支付数据为'.$data_str);
	}
}

public function pay(){
    $wxpay = M ( 'wxpay' )->where ( array ('pay_type' => 'wxpay' ) )->find ();
    $appid = $wxpay['appid'];
    $mch_id = $wxpay['partnerid'];//商户号
    $key = $wxpay['signkey'];//商户key
    $notify_url = C('WEB_ROOT')."index.php/Wxpay/notifyurl";//回调地址
    $wechatAppPay = new wechatAppPay($appid, $mch_id, $notify_url, $key);

    /**********************处理session中存在订单号的情况下的数据*********************/
    $order = M('order');
    $goods = M('goods');
	if(!$_SESSION['goods_data'] && !$_SESSION['order_num']){
		$this->error('商品参数错误!',U('order/myorder'));exit;
	}	
	//如果session中存在订单号，则未完成订单重复提交支付的订单
    if($_SESSION['order_num'] && $_SESSION['order_num'] != ''){
    	$wheres = "order_num='".$_SESSION['order_num']."'  and status=2";
    	//查询出订单号未支付的订单
    	$order_info = $order->where($wheres)->find();
    	if(!$order_info){
    		unset($_SESSION['order_num']);
			unset($_SESSION['goods_data']);
    		$this->error('订单已失效，请重新购买商品!',U('order/myorder'));
    	}
    	//如果订单三天未支付，则订单失效，需重新购买
    	if(($order_info['ctime'] + 60*60*24*3) < $_SERVER[REQUEST_TIME]){
    		$arr['id'] 		= $order_info['id'];
    		$arr['status']  = 0;
    		if($order->save($arr)){
    			unset($_SESSION['order_num']);
    			unset($_SESSION['goods_data']);
        		$this->error('订单已失效，请重新购买商品!',U('order/myorder'));
    		} 
    	}
    	/*******如果session中存在订单号，根据查询出的信息组合支付数据******/
		$out_trade_no 	     = $order_info['order_num'];
    	$params['body']      = $order_info['title'];//组合商品主题
    	$params['total_fee'] = $order_info['price'] *100;
    	//如果支付类型不是状态2（微信）
    	if($order_info['type'] != 2){
	    	$arr['id']  	     = $order_info['id'];
	    	$arr['type']         = 2; //微信支付
	    	if(!$order->save($arr)){
	    		$this->redirect('订单异常，请稍候再试!',U('order/myorder'));exit;
	    	}
    	}
    }else{
    	 /**************根据session中goods_data的信息处理数据,并生成订单存入数据库************/
    	$out_trade_no="M".Date("Ymd-His",time()).'-'.substr(time(),'-4').rand(100,999);//商户订单号
		

	    $goods_data = $_SESSION['goods_data'];
	    if($goods_data && !empty($goods_data)){
	    	//循环组建订单信息
	    	foreach($goods_data as $key => $val){
	    		$result = $goods->where('id = '.$val['gid'])->find();
	    		$order_data['gids'] 	 .= $result['id'].',';
	    		$order_data['uid'] 		  = $_SESSION['uid'];
	    		$order_data['status']     = 2;
	    		$order_data['total_num'] += $val['num'];
	    		$order_data['title']	 .= $result['goods_name'].',';
	    		$goods_new[$key]['goods_price'] = $result['price']*$val['num'];
	    		$goods_new[$key]['gid_num'] = $result['id'].'_'.$val['num'];
	    	}
	    	foreach($goods_new as $k => $v){
	    		$order_data['gid_num']  .= $v['gid_num'].',';
	    		$order_data['price']	+= $v['goods_price'];
	    	}
	    	$order_data['title']   = rtrim($order_data['title'],',');
	    	$order_data['gid_num'] = rtrim($order_data['gid_num'],',');
	    	$order_data['gids']	   = rtrim($order_data['gids'],',');
	    	$order_data['ctime']   = $_SERVER[REQUEST_TIME];
	    	$order_data['type']    = 2;
	    	$order_data['order_num'] 	= $out_trade_no;
			$params['body'] 	 = $order_data['title'];
			$params['total_fee'] = $order_data['price'] *100;
	   }
	   if(!$order->where('order_num ="'.$order_data.'"')->find()){
	    	//如果订单添加失败，则不继续执行，并跳转的结算页面
	    	if(!$order->add($order_data)){
	    		$this->redirect('订单创建失败，请稍候再试!',U('shop/settilement'));exit;
	    	}
	    	M('shopcar')->where('gid in('.$order_data["gids"].')')->delete();//订单创建成功后根据订单的商品id删除购物车内容
	   }
    }
    /****************穿件支付*********************/
	$res = M('member')->field('`uid`,`mobile`')->where('uid = '.$_SESSION['uid'])->find();
 	$data['mobile'] 	=	$res['mobile'];
	$data['rmb']		=	 $order_data['price'] ? $order_data['price'] : $order_info['price'];
	$data['projectid']  =	$order_data['gids'] ? $order_data['gids'] : $order_info['gids'];
	$data['type']		=	"微信";
	$data['code']		=	$out_trade_no;
	$data['time']		=	$_SERVER[REQUEST_TIME];
	$data['yes']		=	0;
	$pay = M("Pay");
	//再次支付不再重复提交订单
    if(!$_SESSION['order_num'] || $_SESSION['order_num'] ==''){
	    $ispay=$pay->add($data);
	    if(!$ispay){
	        $this->error("订单写入失败");//提交过来入库,如果入库失败,则不往下执行支付宝
	    }
    }
    $params['out_trade_no'] = $out_trade_no;    //自定义的订单号
    $params['trade_type']   = 'MWEB';      //交易类型 JSAPI | NATIVE | APP | WAP 
    $params['scene_info']   = '{"h5_info": {"type":"Wap","wap_url": "https://m.xichan.cn","wap_name": "'.$params['body'].'"}}';
    $result = $wechatAppPay->unifiedOrder( $params );
    $redirect_url = urlencode(C("WEB_ROOT").'index.php/order/myorder');
    // pre($result);exit;
	if($result['return_code'] == 'SUCCESS'){
        $url = $result['mweb_url'].'&redirect_url='.$redirect_url;//redirect_url 是支付完成后返回的页面
        unset($_SESSION['order_num']);
  		unset($_SESSION['goods_data']);
        echo "<script type='text/javascript'>
			window.location.href='".$url."';
        </script>";
	}else{
		$this->error('参数错误，请稍候再试!','member/myorder');exit;
	}
}

    //将执行结果写入日志
    public function logResult($word='') {
        $fp = fopen("wx_log.txt","a");
        flock($fp, LOCK_EX) ;
        fwrite($fp,"执行日期：".strftime("%Y%m%d%H%M%S",time())."\n".$word."\n");
        flock($fp, LOCK_UN);
        fclose($fp);
    }
		
}
?>